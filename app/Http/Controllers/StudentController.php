<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Building;
use App\Models\ClassSession;
use App\Models\Computer;
use App\Models\CreditClass;
use App\Models\Report;
use App\Models\Room;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class StudentController extends Controller
{
    public function index()
    {
        $title = 'Trang chủ';
        $user = Auth::user();

        return view('student.index', compact( 'title','user'));
    }

    public function getListClassSession()
    {
        $title = 'Thời khóa biểu';
        $user = Auth::user();

        $student = $user->student;

        $creditClasses = $student->creditClasses()
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
                    'class_id' => $creditClass->id,
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

        $dayOfWeekCounts = array_count_values(array_column($schedule, 'day_of_week'));
        $maxCount = max($dayOfWeekCounts);
        $now = Carbon::now();

        $startOfWeek = $now->startOfWeek()->format('d-m-Y');
        $endOfWeek = $now->endOfWeek()->format('d-m-Y');

        return view('student.list-class-session', compact('title', 'user', 'daysOfWeek', 'schedule', 'maxCount', 'dayOfWeekCounts', 'startOfWeek', 'endOfWeek'));
    }

    public function getListClass()
    {
        $title = 'Danh sách lớp học';
        $user = Auth::user();

        $student = $user->student;

        $classes = $student->creditClasses()
            ->where('status', 'active')
            ->orderBy('class_student.created_at', 'desc')
            ->paginate(7);

        return view('student.list-class', compact('title', 'user', 'classes'));
    }

    public function getClassSessionAPI(Request $request, string $id) {
        $creditClass = CreditClass::findOrFail($id);

        $now = Carbon::now();
        $dayOfWeek = $now->dayOfWeekIso + 1;

        $classSession = $creditClass->classSessions()->where('day_of_week', $request->input('day_of_week'))->first();

        if ($classSession) {
            $startLesson = Carbon::parse($classSession->start_lesson);
            $endLesson = Carbon::parse($classSession->end_lesson);

            if ($dayOfWeek != $classSession->day_of_week || !$now->between($startLesson, $endLesson)) {
                return response()->json(['errors' => ['class-session' => 'Không thể truy cập khi chưa bắt đầu buổi học!']]);
            } else {
                return response()->json(['success' => ['get-class-session-route' => route('student.get-class-session', $classSession->id)]]);
            }
        } else {
            return response()->json(['errors' => ['class-session' => 'Không tìm thấy buổi học!']]);
        }
    }

    public function getClassSession(string $class_session_id)
    {
        $title = 'Buổi học';

        $user = Auth::user();
        $student = $user->student;

        $classSession = ClassSession::findOrFail($class_session_id);

        $room_id = $classSession->room_id;
        $room = Room::where('id', $room_id)->first();
        $building = Building::where('id', $room->building_id)->first();
        $computers = Computer::where('room_id', $room_id)->get();

        $today = Carbon::today();
        $startLesson = Carbon::createFromFormat('H:i:s', $classSession->start_lesson)->setDateFrom($today);
        $endLesson = Carbon::createFromFormat('H:i:s', $classSession->end_lesson)->setDateFrom($today);
        $attendances = Attendance::where('session_id', $class_session_id)
            ->whereBetween('attendance_time', [$startLesson, $endLesson])
            ->get();
        $reports = $student->reports()->orderBy('submitted_at', 'desc')->get();

        return view('student.class-session', compact('title', 'user', 'student', 'classSession', 'room', 'building', 'computers', 'attendances', 'reports'));
    }

    public function sendReportAPI(Request $request, string $computer_id)
    {
        $validator = Validator::make($request->all(), [
            'content' => 'required',
        ], [
            'content.required' => 'Nội dung báo cáo không được để trống!',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        $computer = Computer::findOrFail($computer_id);
        $room = Room::findOrFail($computer->room_id);
        $building = Building::findOrFail($room->building_id);

        $report = new Report();

        $report->content = $request->input('content') . ' (Vị trí máy: ' . $computer->position . ' - Phòng: ' . $room->name . ' - Tòa nhà: ' . $building->name . ')';
        $report->submitted_at = Carbon::now();
        $report->student_id = Auth::user()->student->id;
        $report->save();

        $student = Auth::user()->student;
        $reports = $student->reports()->orderBy('submitted_at', 'desc')->get();
        $table_report = view('student.table-report', compact('reports'))->render();

        return response()->json(['success' => 'Gửi báo cáo thành công!', 'table_report' => $table_report]);
    }

    public function attendanceAPI(Request $request, string $class_session_id)
    {
        $computer = Computer::findOrFail($request->input('computer_id'));
        $student = Auth::user()->student;
        $classSession = ClassSession::findOrFail($class_session_id);

        $now = Carbon::now();
        $today = Carbon::today();
        $startLesson = Carbon::createFromFormat('H:i:s', $classSession->start_lesson)->setDateFrom($today);
        $endLesson = Carbon::createFromFormat('H:i:s', $classSession->end_lesson)->setDateFrom($today);
        if (!$now->between($startLesson, $endLesson)) {
            return response()->json(['errors' => ['attendance' => 'Chỉ có thể điểm danh trong thời gian diễn ra buổi học!']]);
        }

        $existingAttendance = Attendance::where('session_id', $class_session_id)
            ->where('computer_id', $computer->id)
            ->whereBetween('attendance_time', [$startLesson, $endLesson])
            ->first();

        if ($existingAttendance) {
            $room = Room::where('id', $computer->room_id)->first();
            $computers = Computer::where('room_id', $room->id)->get();

            $attendances = Attendance::where('session_id', $class_session_id)
                ->whereBetween('attendance_time', [$startLesson, $endLesson])
                ->get();

            $table_computer = view('student.table-computer', compact('room', 'computers', 'attendances', 'classSession'))->render();

            return response()->json(['errors' => ['attendance' => 'Vị trí này đã có người điểm danh!'], 'table_computer' => $table_computer]);
        }

        $studentAttendance = Attendance::where('session_id', $class_session_id)
            ->whereBetween('attendance_time', [$startLesson, $endLesson])
            ->where('student_id', $student->id)
            ->first();

        if ($studentAttendance) {
            Attendance::where('session_id', $class_session_id)
                ->where('student_id', $student->id)
                ->whereBetween('attendance_time', [$startLesson, $endLesson])
                ->update(['computer_id' => $computer->id, 'updated_at' => $now]);
        } else {
            $attendance = new Attendance();
            $attendance->attendance_time = $now;
            $attendance->student_id = $student->id;
            $attendance->session_id = $class_session_id;
            $attendance->computer_id = $computer->id;
            $attendance->save();
        }

        $room = Room::where('id', $computer->room_id)->first();
        $computers = Computer::where('room_id', $room->id)->get();

        $attendances = Attendance::where('session_id', $class_session_id)
            ->whereBetween('attendance_time', [$startLesson, $endLesson])
            ->get();

        $table_computer = view('student.table-computer', compact('room', 'computers', 'attendances', 'classSession'))->render();

        return response()->json([
            'success' => 'Điểm danh thành công!',
            'table_computer' => $table_computer,
        ]);
    }

    public function joinClassAPI(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'class-code' => 'required|max:255',
        ], [
            'class-code.required' => 'Vui lòng nhập mã mời vào lớp!',
            'class-code.max' => 'Mã mời vào lớp không được vượt quá 255 ký tự!',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        $newClass = CreditClass::where('class_code', $request->input('class-code'))->first();

        if (!$newClass) {
            return response()->json(['errors' => ['class-code' => 'Không tìm thấy lớp học!']]);
        } else {
            $student = Auth::user()->student;

            if ($student->creditClasses()->where('class_id', $newClass->id)->exists()) {
                return response()->json(['errors' => ['class-code' => 'Bạn đã tham gia lớp học này rồi!']]);
            } else {
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
                                    return response()->json(['errors' => ['student-class' => 'Không thể tham gia lớp do trùng lịch học! (' . $currentClass->name . ')']]);
                                }
                            }
                        }
                    }
                }

                $student->creditClasses()->attach($newClass->id, ['created_at' => now(), 'updated_at' => now()]);
                $classes = $student->creditClasses()
                    ->where('status', 'active')
                    ->orderBy('class_student.created_at', 'desc')
                    ->paginate(7);
                $table_class = view('student.table-class', compact('classes'))->render();

                return response()->json(['success' => 'Tham gia lớp học thành công!', 'table_class' => $table_class, 'links' => $classes->render('pagination::bootstrap-5')->toHtml()]);
            }
        }
    }

    public function getPersonalInfo()
    {
        $title = 'Thông tin cá nhân';

        $user = Auth::user();

        return view('student.personal-info', compact('title', 'user'));
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

        $student = Student::findOrFail($id);

        $user = $student->user;
        $user->phone = $request->input('phone');

        $user->save();

        $student->full_name = $request->input('full-name');
        $student->class = $request->input('class');
        $student->gender = $request->input('gender');
        $student->date_of_birth = $request->input('date-of-birth');

        $student->save();

        $table_personal_info = view('student.table-personal-info', compact('user'))->render();

        return response()->json(['success' => 'Cập nhật thông tin cá nhân thành công!', 'table_personal_info' => $table_personal_info]);
    }

    public function updatePasswordAPI(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'new-password' => 'required|min:6',
            're-enter-new-password' => 'same:new-password',
        ], [
            'new-password.required' => 'Vui lòng nhập mật khẩu mới!',
            'new-password.min' => 'Mật khẩu phải chứa ít nhất 6 ký tự!',
            're-enter-new-password.same' => 'Mật khẩu nhập lại không khớp!',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        $user = User::findOrFail($id);
        $user->password = $request->input('new-password');

        $user->save();

        return response()->json(['success' => 'Đổi mật khẩu tài khoản thành công!']);
    }
}
