<?php

namespace App\Http\Controllers;

use App\Models\Building;
use App\Models\Computer;
use App\Models\CreditClass;
use App\Models\Lecturer;
use App\Models\Report;
use App\Models\Room;
use App\Models\Statistic;
use App\Models\Technician;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Ramsey\Collection\Collection;

class ManagerController extends Controller
{
    public function index()
    {
        $title = 'Trang chủ';
        $user = Auth::user();

        $creditClasses = CreditClass::where('status', 'active')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->get();

        $roomsInUse = 0;
        $now = Carbon::now();
        $dayOfWeek = $now->dayOfWeekIso + 1;
        foreach ($creditClasses as $creditClass) {
            $classSessions = $creditClass->classSessions;
            foreach ($classSessions as $classSession) {
                $startLesson = Carbon::parse($classSession->start_lesson);
                $endLesson = Carbon::parse($classSession->end_lesson);
                if ($dayOfWeek == $classSession->day_of_week && $now->between($startLesson, $endLesson)) {
                    $roomsInUse += 1;
                }
            }
        }

        $totalRooms = Room::all()->count();

        $computersInUse = Computer::where('is_active', true)->count();
        $totalComputers = Computer::all()->count();

        $pendingReports = Report::where('is_approved', true)->where('status', 'pending')->get()->count();
        $processingReports = Report::where('is_approved', true)->where('status', 'processing')->get()->count();
        $processedReports = Report::where('is_approved', true)->where('status', 'processed')->get()->count();

        $averageProcessingTime = Report::where('status', 'processed')
            ->selectRaw('AVG(TIMESTAMPDIFF(SECOND, created_at, processed_at)) as average_time')
            ->value('average_time');

        $hours = floor($averageProcessingTime / 3600);
        $minutes = floor(($averageProcessingTime / 60) % 60);
        $seconds = $averageProcessingTime % 60;

        if ($hours > 0) {
            $avgTime = $hours . 'h' . $minutes . 'm';
        } elseif ($minutes > 0) {
            $avgTime = $minutes . 'm' . $seconds . 's';
        } else {
            $avgTime = $seconds . 's';
        }

        $availableComputerUsageTimeToday = Computer::all()->count()*15;
        $totalComputerUsageTimeToday = Statistic::whereDate('usage_date', Carbon::today())->sum('lesson_count');
        $usedComputerTimeRatioToday = ($totalComputerUsageTimeToday/$availableComputerUsageTimeToday)*100;

        $availableComputerUsageTimeLast7Days = Computer::all()->count()*15*7;
        $totalComputerUsageTimeLast7Days = Statistic::whereBetween('usage_date', [Carbon::today()->subDays(7), Carbon::today()])->sum('lesson_count');
        $usedComputerTimeRatioLast7Days = ($totalComputerUsageTimeLast7Days/$availableComputerUsageTimeLast7Days)*100;

        $availableRoomUsageTimeToday = Room::all()->count()*15;
        $totalRoomUsageTimeToday = 0;
        $availableRoomUsageTimeLast7Days = Room::all()->count()*15*7;
        $totalRoomUsageTimeLast7Days = 0;

        foreach ($creditClasses as $creditClass) {
            $classSessions = $creditClass->classSessions;
            foreach ($classSessions as $classSession) {
                if ($dayOfWeek == $classSession->day_of_week) {
                    $totalRoomUsageTimeToday += $classSession->lessons()->count();
                }
                $totalRoomUsageTimeLast7Days += $classSession->lessons()->count();
            }
        }
        $usedRoomTimeRatioToday = ($totalRoomUsageTimeToday/$availableRoomUsageTimeToday)*100;
        $usedRoomTimeRatioLast7Days = ($totalRoomUsageTimeLast7Days/$availableRoomUsageTimeLast7Days)*100;

        $buildings = Building::all();

        $daysOfWeek = ['2' => 'Thứ hai', '3' => 'Thứ ba', '4' => 'Thứ tư', '5' => 'Thứ năm', '6' => 'Thứ sáu', '7' => 'Thứ bảy', '8' => 'Chủ nhật'];

        $schedule = [];

        foreach ($creditClasses as $creditClass) {
            $classSessions = $creditClass->classSessions()->orderBy('start_lesson', 'asc')->get();

            foreach ($classSessions as $classSession) {
                $room = $classSession->room;
                $startLesson = $classSession->lessons()->min('lesson_id');
                $endLesson = $classSession->lessons()->max('lesson_id');
                $schedule[] = [
                    'session_id' => $classSession->id,
                    'class_id' => $creditClass->id,
                    'class_name' => $creditClass->name,
                    'day_of_week' => $classSession->day_of_week,
                    'start_lesson' => $startLesson,
                    'end_lesson' => $endLesson,
                    'room_id' => $room->id,
                    'building_id' => $room->building->id,
                ];
            }
        }
        usort($schedule, function ($a, $b) {
            return $a['day_of_week'] <=> $b['day_of_week'];
        });

        return view('manager.index', compact( 'title','user', 'roomsInUse', 'totalRooms', 'computersInUse', 'totalComputers', 'pendingReports',
            'processingReports', 'processedReports', 'avgTime', 'usedComputerTimeRatioToday', 'usedRoomTimeRatioToday', 'usedComputerTimeRatioLast7Days', 'usedRoomTimeRatioLast7Days',
            'daysOfWeek', 'schedule', 'buildings'));
    }

    public function getListTechnician(Request $request) {
        $title = 'Quản lý tài khoản';
        $user = Auth::user();

        $sortField = $request->input('sort-field', 'technicians.updated_at');
        $sortOrder = $request->input('sort-order', 'desc');
        $recordsPerPage = $request->input('records-per-page', 5);

        if ($sortField == 'full_name') {
            $technicians = Technician::leftJoinSub(
                DB::table('reports')
                    ->selectRaw('technician_id, COUNT(*) as report_count, AVG(TIMESTAMPDIFF(SECOND, created_at, processed_at)) as avg_processing_time')
                    ->where('status', 'processed')
                    ->groupBy('technician_id'),
                'report_summary',
                'technicians.id',
                'report_summary.technician_id'
            )
            ->select('technicians.*', 'report_summary.report_count', 'report_summary.avg_processing_time')
            ->orderByRaw("SUBSTRING_INDEX(full_name, ' ', -1) $sortOrder")
            ->paginate($recordsPerPage);
        } else {
            $technicians = Technician::leftJoinSub(
                DB::table('reports')
                    ->selectRaw('technician_id, COUNT(*) as report_count, AVG(TIMESTAMPDIFF(SECOND, created_at, processed_at)) as avg_processing_time')
                    ->where('status', 'processed')
                    ->groupBy('technician_id'),
                'report_summary',
                'technicians.id',
                'report_summary.technician_id'
            )
            ->select('technicians.*', 'report_summary.report_count', 'report_summary.avg_processing_time')
            ->orderBy($sortField, $sortOrder)
            ->paginate($recordsPerPage);
        }

        $technicians = $this->processingTimeFormat($technicians);

        return view('manager.list-technician', compact('title', 'user', 'technicians'));
    }

    public function storeTechnicianAPI(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'full-name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users,email',
        ], [
            'full-name.required' => 'Vui lòng nhập họ và tên!',
            'full-name.max' => 'Họ và tên không được vượt quá 255 ký tự!',
            'email.required' => 'Vui lòng nhập địa chỉ email!',
            'email.email' => 'Địa chỉ email không hợp lệ!',
            'email.unique' => 'Địa chỉ email đã được sử dụng!',
            'email.max' => 'Địa chỉ email không được vượt quá 255 ký tự!',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        $user = new User();

        $user->email = $request->input('email');
        $user->password = Hash::make('123456');
        $user->phone = $request->input('phone');
        $user->role_id = '2';

        $user->save();

        $technician = new Technician();

        $technician->full_name = $request->input('full-name');
        $technician->user_id = $user->id;

        $technician->save();

        $renderTechnicians = $this->renderTechnicians($request);

        return response()->json(['success' => 'Thêm kỹ thuật viên thành công!', 'table_technician' => $renderTechnicians['table_technician'], 'links' => $renderTechnicians['technicians']->render('pagination::bootstrap-5')->toHtml()]);
    }

    public function updatePasswordTechnicianAPI(Request $request, string $id)
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

        $technician = Technician::findOrFail($id);

        $user = User::findOrFail($technician->user_id);
        $user->password = $request->input('new-password');

        $user->save();

        return response()->json(['success' => 'Đổi mật khẩu tài khoản của kỹ thuật viên thành công!']);
    }

    public function updateTechnicianAPI(Request $request, string $id)
    {
        $technician = Technician::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'full-name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $technician->user_id,
        ], [
            'full-name.required' => 'Vui lòng nhập họ và tên!',
            'full-name.max' => 'Họ và tên không được vượt quá 255 ký tự!',
            'email.required' => 'Vui lòng nhập địa chỉ email!',
            'email.email' => 'Địa chỉ email không hợp lệ!',
            'email.unique' => 'Địa chỉ email đã được sử dụng!',
            'email.max' => 'Địa chỉ email không được vượt quá 255 ký tự!',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        $user = User::findOrFail($technician->user_id);
        if ($user->email != $request->input('email')) {
            $user->email = $request->input('email');
            $user->is_verified = false;
        }
        $user->phone = $request->input('phone');

        $user->save();

        $technician->full_name = $request->input('full-name');

        $technician->save();

        $renderTechnicians = $this->renderTechnicians($request);

        return response()->json(['success' => 'Chỉnh sửa thông tin kỹ thuật viên thành công!', 'table_technician' => $renderTechnicians['table_technician'], 'links' => $renderTechnicians['technicians']->render('pagination::bootstrap-5')->toHtml()]);
    }

    public function destroyTechnicianAPI(Request $request, string $id)
    {
        $technician = Technician::findOrFail($id);

        $reports = $technician->reports;
        $reports->each(function ($report) {
            $report->delete();
        });

        $technician->delete();

        $user = User::findOrFail($technician->user_id);
        $user->delete();

        $renderTechnicians = $this->renderTechnicians($request);

        return response()->json(['success' => 'Xóa kỹ thuật viên thành công!', 'table_technician' => $renderTechnicians['table_technician'], 'links' => $renderTechnicians['technicians']->render('pagination::bootstrap-5')->toHtml()]);
    }

    public function renderTechnicians(Request $request)
    {
        $recordsPerPage = $request->input('records-per-page', 5);

        $technicians = Technician::with(['reports' => function($query) {
            $query->selectRaw('technician_id, COUNT(*) as report_count, AVG(TIMESTAMPDIFF(SECOND, created_at, processed_at)) as avg_processing_time')
                ->where('status', 'processed')
                ->groupBy('technician_id');
        }])->orderBy('technicians.updated_at', 'desc')->paginate($recordsPerPage);

        $technicians = $this->processingTimeFormat($technicians);

        $table_technician = view('manager.table-technician', compact('technicians'))->render();

        return ['table_technician' => $table_technician, 'technicians' => $technicians];
    }

    public function sortTechnicianAPI(Request $request)
    {
        $sortField = $request->input('sortField', 'updated_at');
        $sortOrder = $request->input('sortOrder', 'desc');
        $recordsPerPage = $request->input('recordsPerPage', 5);

        if ($sortField == 'full_name') {
            $technicians = Technician::leftJoinSub(
                DB::table('reports')
                    ->selectRaw('technician_id, COUNT(*) as report_count, AVG(TIMESTAMPDIFF(SECOND, created_at, processed_at)) as avg_processing_time')
                    ->where('status', 'processed')
                    ->groupBy('technician_id'),
                'report_summary',
                'technicians.id',
                'report_summary.technician_id'
            )
            ->select('technicians.*', 'report_summary.report_count', 'report_summary.avg_processing_time')
            ->orderByRaw("SUBSTRING_INDEX(full_name, ' ', -1) $sortOrder")
            ->paginate($recordsPerPage);
        } else {
            $technicians = Technician::leftJoinSub(
                DB::table('reports')
                    ->selectRaw('technician_id, COUNT(*) as report_count, AVG(TIMESTAMPDIFF(SECOND, created_at, processed_at)) as avg_processing_time')
                    ->where('status', 'processed')
                    ->groupBy('technician_id'),
                'report_summary',
                'technicians.id',
                'report_summary.technician_id'
            )
            ->select('technicians.*', 'report_summary.report_count', 'report_summary.avg_processing_time')
            ->orderBy($sortField, $sortOrder)
            ->paginate($recordsPerPage);
        }

        $technicians = $this->processingTimeFormat($technicians);

        $table_technician = view('manager.table-technician', compact('technicians'))->render();

        return response()->json(['table_technician' => $table_technician, 'links' => $technicians->render('pagination::bootstrap-5')->toHtml()]);
    }

    public function changeRecordsPerPageTechnicianAPI(Request $request)
    {
        $sortField = $request->input('sortField', 'updated_at');
        $sortOrder = $request->input('sortOrder', 'desc');
        $recordsPerPage = $request->input('recordsPerPage', 5);

        if ($sortField == 'full_name') {
            $technicians = Technician::leftJoinSub(
                DB::table('reports')
                    ->selectRaw('technician_id, COUNT(*) as report_count, AVG(TIMESTAMPDIFF(SECOND, created_at, processed_at)) as avg_processing_time')
                    ->where('status', 'processed')
                    ->groupBy('technician_id'),
                'report_summary',
                'technicians.id',
                'report_summary.technician_id'
            )
            ->select('technicians.*', 'report_summary.report_count', 'report_summary.avg_processing_time')
            ->orderByRaw("SUBSTRING_INDEX(full_name, ' ', -1) $sortOrder")
            ->paginate($recordsPerPage);
        } else {
            $technicians = Technician::leftJoinSub(
                DB::table('reports')
                    ->selectRaw('technician_id, COUNT(*) as report_count, AVG(TIMESTAMPDIFF(SECOND, created_at, processed_at)) as avg_processing_time')
                    ->where('status', 'processed')
                    ->groupBy('technician_id'),
                'report_summary',
                'technicians.id',
                'report_summary.technician_id'
            )
            ->select('technicians.*', 'report_summary.report_count', 'report_summary.avg_processing_time')
            ->orderBy($sortField, $sortOrder)
            ->paginate($recordsPerPage);
        }

        $technicians = $this->processingTimeFormat($technicians);

        $table_technician = view('manager.table-technician', compact('technicians'))->render();

        return response()->json(['table_technician' => $table_technician, 'links' => $technicians->render('pagination::bootstrap-5')->toHtml()]);
    }

    public function searchTechnicianAPI(Request $request)
    {
        $sortField = $request->input('sortField', 'technicians.updated_at');
        $sortOrder = $request->input('sortOrder', 'desc');
        $recordsPerPage = $request->input('recordsPerPage', 5);
        $query = $request->input('query');

        if ($sortField == 'full_name') {
            $technicians = Technician::leftJoinSub(
                DB::table('reports')
                    ->selectRaw('technician_id, COUNT(*) as report_count, AVG(TIMESTAMPDIFF(SECOND, created_at, processed_at)) as avg_processing_time')
                    ->where('status', 'processed')
                    ->groupBy('technician_id'),
                'report_summary',
                'technicians.id',
                'report_summary.technician_id'
            )
            ->join('users', 'technicians.user_id', '=', 'users.id')
            ->where('technicians.full_name', 'LIKE', "%{$query}%")
            ->orWhere('email', 'LIKE', "%{$query}%")
            ->orWhere('phone', 'LIKE', "%{$query}%")
            ->select('technicians.*', 'report_summary.report_count', 'report_summary.avg_processing_time')
            ->orderByRaw("SUBSTRING_INDEX(full_name, ' ', -1) $sortOrder")
            ->paginate($recordsPerPage);
        } else {
            $technicians = Technician::leftJoinSub(
                DB::table('reports')
                    ->selectRaw('technician_id, COUNT(*) as report_count, AVG(TIMESTAMPDIFF(SECOND, created_at, processed_at)) as avg_processing_time')
                    ->where('status', 'processed')
                    ->groupBy('technician_id'),
                'report_summary',
                'technicians.id',
                'report_summary.technician_id'
            )
            ->join('users', 'technicians.user_id', '=', 'users.id')
            ->where('technicians.full_name', 'LIKE', "%{$query}%")
            ->orWhere('email', 'LIKE', "%{$query}%")
            ->orWhere('phone', 'LIKE', "%{$query}%")
            ->select('technicians.*', 'report_summary.report_count', 'report_summary.avg_processing_time')
            ->orderBy($sortField, $sortOrder)
            ->paginate($recordsPerPage);
        }

        $technicians = $this->processingTimeFormat($technicians);

        $table_technician = view('manager.table-technician', compact('technicians'))->render();

        return response()->json(['table_technician' => $table_technician, 'links' => $technicians->render('pagination::bootstrap-5')->toHtml()]);
    }

    public function processingTimeFormat(LengthAwarePaginator $technicians)
    {
        foreach ($technicians as $technician) {
            $reportAvgProcessingTime = $technician->avg_processing_time;
            $hours = floor($reportAvgProcessingTime / 3600);
            $minutes = floor(($reportAvgProcessingTime % 3600) / 60);
            $seconds = $reportAvgProcessingTime % 60;

            if ($hours > 0) {
                $avgTime = $hours . 'h' . $minutes . 'm';
            } elseif ($minutes > 0) {
                $avgTime = $minutes . 'm' . $seconds . 's';
            } else {
                $avgTime = $seconds . 's';
            }
            $technician->avg_processing_time = $avgTime;
        }
        return $technicians;
    }
}
