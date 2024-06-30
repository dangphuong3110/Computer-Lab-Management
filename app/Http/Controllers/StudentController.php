<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Building;
use App\Models\ClassSession;
use App\Models\Computer;
use App\Models\CreditClass;
use App\Models\Report;
use App\Models\Room;
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

        return view('student.class-session', compact('title', 'user', 'student', 'classSession', 'room', 'building', 'computers', 'attendances'));
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

        return response()->json(['success' => 'Gửi báo cáo thành công!']);
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
}
