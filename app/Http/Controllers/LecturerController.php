<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Building;
use App\Models\ClassSession;
use App\Models\Computer;
use App\Models\CreditClass;
use App\Models\Lecturer;
use App\Models\Report;
use App\Models\Room;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

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

        return view('lecturer.list-class-session', compact('title', 'user', 'daysOfWeek', 'schedule', 'maxCount', 'dayOfWeekCounts', 'startOfWeek', 'endOfWeek'));
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
            ->whereBetween('attendance_time', [$startLesson, $endLesson])
            ->get();
        $reports = $lecturer->reports;

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
        $report->submitted_at = Carbon::now();
        $report->lecturer_id = Auth::user()->lecturer->id;
        $report->save();

        $lecturer = Auth::user()->lecturer;
        $reports = $lecturer->reports()->orderBy('submitted_at', 'desc')->get();
        $table_report = view('lecturer.table-report', compact('reports'))->render();

        return response()->json(['success' => 'Gửi báo cáo thành công!', 'table_report' => $table_report]);
    }

    public function getListStudentReport()
    {
        $title = 'Xét duyệt báo cáo';
        $user = Auth::user();

        $renderedReportTable = $this->renderReportTable($user);
        return view('lecturer.list-student-report', compact('title', 'user'))->with('reports', $renderedReportTable['reports']);
    }

    public function approveReportAPI(string $report_id)
    {
        $report = Report::findOrFail($report_id);
        $report->is_approved = true;
        $report->save();

        $user = Auth::user();

        $renderedReportTable = $this->renderReportTable($user);
        return response()->json(['success' => 'Duyệt báo cáo thành công!', 'table_report' => $renderedReportTable['table_report'], 'links' => $renderedReportTable['reports']->render('pagination::bootstrap-5')->toHtml()]);
    }

    public function disapproveReportAPI(string $report_id)
    {
        $report = Report::findOrFail($report_id);
        $report->is_approved = false;
        $report->save();

        $user = Auth::user();

        $renderedReportTable = $this->renderReportTable($user);
        return response()->json(['success' => 'Hủy duyệt báo cáo thành công!', 'table_report' => $renderedReportTable['table_report'], 'links' => $renderedReportTable['reports']->render('pagination::bootstrap-5')->toHtml()]);
    }

    public function destroyReportAPI(string $report_id)
    {
        $report = Report::findOrFail($report_id);

        $report->delete();

        $user = Auth::user();

        $renderedReportTable = $this->renderReportTable($user);
        return response()->json(['success' => 'Xóa báo cáo thành công!', 'table_report' => $renderedReportTable['table_report'], 'links' => $renderedReportTable['reports']->render('pagination::bootstrap-5')->toHtml()]);
    }

    public function renderReportTable(User $user)
    {
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
                $student_reports = $student->reports()->orderBy('submitted_at', 'desc')->get();
                foreach ($student_reports as $student_report) {
                    if (!$reports->contains('id', $student_report->id)) {
                        $reports->push($student_report);
                    }
                }
            }
        }

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 7;
        $currentItems = $reports->slice(($currentPage * $perPage) - $perPage, $perPage)->all();
        $reports = new LengthAwarePaginator($currentItems, count($reports), $perPage, $currentPage, ['path' => LengthAwarePaginator::resolveCurrentPath()]);
        $table_report = view('lecturer.table-student-report', compact('reports'))->render();

        return ['table_report' => $table_report, 'reports' => $reports];
    }
}
