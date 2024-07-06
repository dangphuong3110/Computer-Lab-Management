<?php

namespace App\Http\Controllers;

use App\Models\Building;
use App\Models\Computer;
use App\Models\CreditClass;
use App\Models\Report;
use App\Models\Room;
use App\Models\Statistic;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ManagerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
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

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
