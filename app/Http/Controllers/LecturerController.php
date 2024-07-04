<?php

namespace App\Http\Controllers;

use App\Exports\AttendancesExport;
use App\Imports\StudentClassImport;
use App\Models\Attendance;
use App\Models\Building;
use App\Models\ClassSession;
use App\Models\Computer;
use App\Models\CreditClass;
use App\Models\Lecturer;
use App\Models\Report;
use App\Models\Room;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class LecturerController extends Controller
{
    public function index()
    {
        $title = 'Trang chủ';
        $user = Auth::user();

        return view('lecturer.index', compact( 'title','user'));
    }

    public function getListClassSession()
    {
        $title = 'Thời khóa biểu';
        $user = Auth::user();

        $lecturer = $user->lecturer;

        $creditClasses = $lecturer->creditClasses()
            ->where('status', 'active')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->get();

        $daysOfWeek = ['2' => 'Thứ hai', '3' => 'Thứ ba', '4' => 'Thứ tư', '5' => 'Thứ năm', '6' => 'Thứ sáu', '7' => 'Thứ bảy', '8' => 'Chủ nhật'];

        $schedule = [];

        foreach ($creditClasses as $creditClass) {
            $classSessions = $creditClass->classSessions()->orderBy('start_lesson', 'asc')->get();

            foreach ($classSessions as $classSession) {
                $room = $classSession->room;
                $schedule[] = [
                    'session_id' => $classSession->id,
                    'class_name' => $creditClass->name,
                    'day_of_week' => $classSession->day_of_week,
                    'start_time' => Carbon::parse($classSession->start_lesson)->format('H:i'),
                    'end_time' => Carbon::parse($classSession->end_lesson)->format('H:i'),
                    'room' => $room->name,
                    'building' => $room->building->name,
                ];
            }
        }
        usort($schedule, function ($a, $b) {
            $dayOfWeekComparison = $a['day_of_week'] <=> $b['day_of_week'];
            if ($dayOfWeekComparison !== 0) {
                return $dayOfWeekComparison;
            }

            return $a['start_time'] <=> $b['start_time'];
        });

        $maxCount = 0;
        $dayOfWeekCounts = array_count_values(array_column($schedule, 'day_of_week'));
        if ($dayOfWeekCounts) {
            $maxCount = max($dayOfWeekCounts);
        }
        $now = Carbon::now();

        $startOfWeek = $now->startOfWeek()->format('d-m-Y');
        $endOfWeek = $now->endOfWeek()->format('d-m-Y');

        return view('lecturer.list-class-session', compact('title', 'user', 'daysOfWeek', 'schedule', 'maxCount', 'dayOfWeekCounts', 'startOfWeek', 'endOfWeek'));
    }

    public function getClassSessionAPI(Request $request, string $id) {
        $now = Carbon::now();
        $dayOfWeek = $now->dayOfWeekIso + 1;


        $classSession = ClassSession::findOrFail($id);

        if ($classSession) {
            $startLesson = Carbon::parse($classSession->start_lesson);
            $endLesson = Carbon::parse($classSession->end_lesson);

            if ($dayOfWeek != $classSession->day_of_week || !$now->between($startLesson, $endLesson)) {
                return response()->json(['errors' => ['class-session' => 'Không thể truy cập khi chưa bắt đầu buổi học!']]);
            } else {
                return response()->json(['success' => ['get-class-session-route' => route('lecturer.get-class-session', $classSession->id)]]);
            }
        } else {
            return response()->json(['errors' => ['class-session' => 'Không tìm thấy buổi học!']]);
        }
    }

    public function getClassSession(string $class_session_id)
    {
        $title = 'Buổi học';

        $user = Auth::user();
        $lecturer = $user->lecturer;

        $classSession = ClassSession::findOrFail($class_session_id);

        $room_id = $classSession->room_id;
        $room = Room::where('id', $room_id)->first();
        $building = Building::where('id', $room->building_id)->first();
        $computers = Computer::where('room_id', $room_id)->get();

        $today = Carbon::today();
        $startLesson = Carbon::createFromFormat('H:i:s', $classSession->start_lesson)->setDateFrom($today);
        $endLesson = Carbon::createFromFormat('H:i:s', $classSession->end_lesson)->setDateFrom($today);
        $attendances = Attendance::where('session_id', $class_session_id)
            ->whereBetween('created_at', [$startLesson, $endLesson])
            ->get();
        $reports = $lecturer->reports()->orderBy('submitted_at', 'desc')->get();

        return view('lecturer.class-session', compact('title', 'user', 'lecturer', 'classSession', 'room', 'building', 'computers', 'attendances', 'reports'));
    }

    public function sendReportAPI(Request $request, string $room_id)
    {
        $validator = Validator::make($request->all(), [
            'content' => 'required',
        ], [
            'content.required' => 'Nội dung báo cáo không được để trống!',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        $room = Room::findOrFail($room_id);
        $building = Building::findOrFail($room->building_id);

        $report = new Report();

        $report->content = $request->input('content') . ' (Phòng: ' . $room->name . ' - Tòa nhà: ' . $building->name . ')';
        $report->is_approved = true;
        $report->submitted_at = Carbon::now();
        $report->lecturer_id = Auth::user()->lecturer->id;
        $report->save();

        $lecturer = Auth::user()->lecturer;
        $reports = $lecturer->reports()->orderBy('submitted_at', 'desc')->get();
        $table_report = view('lecturer.table-report', compact('reports'))->render();

        return response()->json(['success' => 'Gửi báo cáo thành công!', 'table_report' => $table_report]);
    }

    public function getListStudentReport(Request $request)
    {
        $title = 'Xét duyệt báo cáo';
        $user = Auth::user();

        $renderedReportTable = $this->renderReportTable($request, $user);
        return view('lecturer.list-student-report', compact('title', 'user'))->with('reports', $renderedReportTable['reports']);
    }

    public function approveReportAPI(Request $request, string $report_id)
    {
        $report = Report::findOrFail($report_id);
        $report->is_approved = true;
        $report->save();

        $user = Auth::user();

        $renderedReportTable = $this->renderReportTable($request, $user);
        return response()->json(['success' => 'Duyệt báo cáo thành công!', 'table_report' => $renderedReportTable['table_report'], 'links' => $renderedReportTable['reports']->render('pagination::bootstrap-5')->toHtml()]);
    }

    public function disapproveReportAPI(Request $request, string $report_id)
    {
        $report = Report::findOrFail($report_id);
        $report->is_approved = false;
        $report->save();

        $user = Auth::user();

        $renderedReportTable = $this->renderReportTable($request, $user);
        return response()->json(['success' => 'Hủy duyệt báo cáo thành công!', 'table_report' => $renderedReportTable['table_report'], 'links' => $renderedReportTable['reports']->render('pagination::bootstrap-5')->toHtml()]);
    }

    public function destroyReportAPI(Request $request, string $report_id)
    {
        $report = Report::findOrFail($report_id);

        $report->delete();

        $user = Auth::user();

        $renderedReportTable = $this->renderReportTable($request, $user);
        return response()->json(['success' => 'Xóa báo cáo thành công!', 'table_report' => $renderedReportTable['table_report'], 'links' => $renderedReportTable['reports']->render('pagination::bootstrap-5')->toHtml()]);
    }

    public function renderReportTable(Request $request, User $user)
    {
        $sortField = $request->input('sort-field', 'submitted_at');
        $sortOrder = $request->input('sort-order', 'desc');

        $lecturer = $user->lecturer;

        $creditClasses = $lecturer->creditClasses()
            ->where('status', 'active')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->get();

        $reports = collect();

        foreach ($creditClasses as $creditClass) {
            $students = $creditClass->students;
            foreach ($students as $student) {
                $student_reports = $student->reports()->orderBy($sortField, $sortOrder)->get();
                foreach ($student_reports as $student_report) {
                    if (!$reports->contains('id', $student_report->id)) {
                        $reports->push($student_report);
                    }
                }
            }
        }

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 5;
        $currentItems = $reports->slice(($currentPage * $perPage) - $perPage, $perPage)->all();
        $reports = new LengthAwarePaginator($currentItems, count($reports), $perPage, $currentPage, ['path' => LengthAwarePaginator::resolveCurrentPath()]);
        $table_report = view('lecturer.table-student-report', compact('reports'))->render();

        return ['table_report' => $table_report, 'reports' => $reports];
    }

    public function getListClass()
    {
        $title = 'Lớp học tiếp quản';
        $user = Auth::user();

        $lecturer = $user->lecturer;

        $classes = $lecturer->creditClasses()
            ->where('status', 'active')
            ->orderBy('classes.created_at', 'desc')
            ->paginate(5);

        return view('lecturer.list-class', compact('title', 'user', 'classes'));
    }

    public function getListStudentClass(Request $request, string $class_id)
    {
        $title = 'Sinh viên lớp học phần';
        $user = Auth::user();

        $sortField = $request->input('sort-field', 'created_at');
        $sortOrder = $request->input('sort-order', 'desc');

        $class = CreditClass::where('id', $class_id)->first();
        if ($sortField == 'full_name') {
            $students = $class->students()->orderByRaw("SUBSTRING_INDEX(full_name, ' ', -1) $sortOrder")->paginate(5);
        } else {
            $students = $class->students()->orderBy($sortField, $sortOrder)->paginate(5);
        }

        return view('lecturer.list-student-class', compact('title','user', 'students', 'class'));
    }

    public function getStudentByStudentCodeAPI(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'student-code' => 'required',
        ], [
            'student-code.required' => 'Vui lòng nhập mã sinh viên!',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        $student = Student::where('student_code', $request->input('student-code'))->first();

        if (!$student) {
            return response()->json(['errors' => ['student-code' => 'Không tìm thấy sinh viên!']]);
        } else {
            return response()->json(['success' => 'Đã tìm thấy sinh viên!','student' => $student]);
        }
    }

    public function storeStudentClassAPI(Request $request) {
        $validator = Validator::make($request->all(), [
            'student-code' => 'required',
        ], [
            'student-code.required' => 'Vui lòng nhập mã sinh viên!',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        $student = Student::where('student_code', $request->input('student-code'))->first();

        if (!$student) {
            return response()->json(['errors' => ['student-code' => 'Không tìm thấy sinh viên!']]);
        } else {
            $class_id = $request->input('class_id');
            $student = Student::where('student_code', $request->input('student-code'))->first();

            if ($student->creditClasses()->where('id', $class_id)->exists()) {
                return response()->json(['errors' => ['student-class' => 'Sinh viên đã có trong lớp học!']]);
            }

            $newClass = CreditClass::findOrFail($class_id);
            $newClassSessions = $newClass->classSessions;

            $currentClasses = $student->creditClasses;

            foreach ($currentClasses as $currentClass) {
                if (Carbon::parse($newClass->start_date)->between($currentClass->start_date, $currentClass->end_date) ||
                    Carbon::parse($newClass->end_date)->between($currentClass->start_date, $currentClass->end_date)) {
                    $currentClassSessions = $currentClass->classSessions;

                    foreach ($newClassSessions as $newClassSession) {
                        foreach ($currentClassSessions as $currentClassSession) {
                            if ($newClassSession->day_of_week == $currentClassSession->day_of_week &&
                                $newClassSession->start_lesson <= $currentClassSession->end_lesson &&
                                $newClassSession->end_lesson >= $currentClassSession->start_lesson) {
                                return response()->json(['errors' => ['student-class' => 'Sinh viên đã có lớp trùng tiết học với lớp mới! (' . $currentClass->name . ')']]);
                            }
                        }
                    }
                }
            }

            $student->creditClasses()->attach($class_id, ['created_at' => now(), 'updated_at' => now()]);

            $class = CreditClass::where('id', $class_id)->first();
            $students = $class->students()->orderBy('class_student.created_at', 'desc')->paginate(5);
            $table_student_class = view('lecturer.table-student-class', compact('students', 'class'))->render();

            return response()->json(['success' => 'Đã thêm sinh viên vào lớp học!', 'table_student_class' => $table_student_class, 'links' => $students->render('pagination::bootstrap-5')->toHtml()]);
        }
    }

    public function destroyStudentClassAPI(Request $request, string $student_id)
    {
        $student = Student::findOrFail($student_id);

        $student->creditClasses()->detach($request->input('class_id'));

        $class = CreditClass::where('id', $request->input('class_id'))->first();
        $students = $class->students()->orderBy('class_student.created_at', 'desc')->paginate(5);
        $table_student_class = view('lecturer.table-student-class', compact('students', 'class'))->render();

        return response()->json(['success' => 'Xóa sinh viên khỏi lớp học phần thành công!', 'table_student_class' => $table_student_class, 'links' => $students->render('pagination::bootstrap-5')->toHtml()]);
    }

    public function importStudentClassAPI(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'student-class-file' => 'required|mimes:xlsx,xls',
        ], [
            'student-class-file.required' => 'Vui lòng nhập file!',
            'student-class-file.mimes' => 'Vui lòng nhập đúng định dạng file excel (.xlsx, .xls)!',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        $file = $request->file('student-class-file');

        $class_id = $request->input('class_id');
        $import = new StudentClassImport($class_id);
        Excel::import($import, $file);

        $class = CreditClass::where('id', $class_id)->first();
        $students = $class->students()->orderBy('class_student.created_at', 'desc')->paginate(5);
        $table_student_class = view('lecturer.table-student-class', compact('students', 'class'))->render();

        return response()->json(['success' => 'Nhập file sinh viên vào lớp học phần thành công!', 'table_student_class' => $table_student_class, 'links' => $students->render('pagination::bootstrap-5')->toHtml()]);
    }

    public function getExportAttendances(string $class_id)
    {
        $class = CreditClass::find($class_id);
        $export = new AttendancesExport($class);
        return $export->download('Danh sách điểm danh lớp ' . $class->name . '.xlsx');
    }

    public function getPersonalInfo()
    {
        $title = 'Thông tin cá nhân';

        $user = Auth::user();

        return view('lecturer.personal-info', compact('title', 'user'));
    }

    public function updatePersonalInfoAPI(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'full-name' => 'required|max:255',
        ], [
            'full-name.required' => 'Vui lòng nhập họ và tên!',
            'full-name.max' => 'Họ và tên không được vượt quá 255 ký tự!',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        $lecturer = Lecturer::findOrFail($id);

        $user = $lecturer->user;
        $user->phone = $request->input('phone');

        $user->save();

        $lecturer->full_name = $request->input('full-name');
        $lecturer->academic_rank = $request->input('academic-rank');
        $lecturer->department = $request->input('department');
        $lecturer->faculty = $request->input('faculty');
        $lecturer->position = $request->input('position');

        $lecturer->save();

        $table_personal_info = view('lecturer.table-personal-info', compact('user'))->render();

        return response()->json(['success' => 'Cập nhật thông tin cá nhân thành công!', 'table_personal_info' => $table_personal_info]);
    }

    public function sortReportAPI(Request $request)
    {
        $sortField = $request->input('sortField', 'submitted_at');
        $sortOrder = $request->input('sortOrder', 'desc');

        $user = Auth::user();
        $lecturer = $user->lecturer;

        $reports = Report::where('lecturer_id', $lecturer->id)->orderBy($sortField, $sortOrder)->get();
        $table_report = view('lecturer.table-report', compact('reports'))->render();

        return response()->json(['table_report' => $table_report]);
    }

    public function sortStudentClassAPI(Request $request)
    {
        $sortField = $request->input('sortField', 'created_at');
        $sortOrder = $request->input('sortOrder', 'desc');

        $class = CreditClass::where('id', $request->input('classId'))->first();
        if ($sortField == 'full_name') {
            $students = $class->students()->orderByRaw("SUBSTRING_INDEX(full_name, ' ', -1) $sortOrder")->paginate(5);
        } else {
            $students = $class->students()->orderBy($sortField, $sortOrder)->paginate(5);
        }
        $table_student_class = view('lecturer.table-student-class', compact('students', 'class'))->render();

        return response()->json(['table_student_class' => $table_student_class, 'links' => $students->render('pagination::bootstrap-5')->toHtml()]);
    }

    public function sortStudentReportAPI(Request $request)
    {
        $sortField = $request->input('sortField', 'submitted_at');
        $sortOrder = $request->input('sortOrder', 'desc');

        $user = Auth::user();
        $lecturer = $user->lecturer;

        $creditClasses = $lecturer->creditClasses()
            ->where('status', 'active')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->get();

        $reports = collect();

        foreach ($creditClasses as $creditClass) {
            $students = $creditClass->students;
            foreach ($students as $student) {
                $student_reports = $student->reports()->orderBy($sortField, $sortOrder)->get();
                foreach ($student_reports as $student_report) {
                    if (!$reports->contains('id', $student_report->id)) {
                        $reports->push($student_report);
                    }
                }
            }
        }

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 5;
        $currentItems = $reports->slice(($currentPage * $perPage) - $perPage, $perPage)->all();
        $reports = new LengthAwarePaginator($currentItems, count($reports), $perPage, $currentPage, ['path' => LengthAwarePaginator::resolveCurrentPath()]);
        $table_report = view('lecturer.table-student-report', compact('reports'))->render();

        return response()->json(['table_report' => $table_report, 'links' => $reports->render('pagination::bootstrap-5')->toHtml()]);
    }
}
