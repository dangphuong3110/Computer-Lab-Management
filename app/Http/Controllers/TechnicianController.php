<?php

namespace App\Http\Controllers;

use App\Imports\LecturersImport;
use App\Imports\StudentClassImport;
use App\Imports\StudentsImport;
use App\Models\Attendance;
use App\Models\Building;
use App\Models\ClassSession;
use App\Models\Computer;
use App\Models\CreditClass;
use App\Models\Lecturer;
use App\Models\Lesson;
use App\Models\Report;
use App\Models\Room;
use App\Models\Student;
use App\Models\Technician;
use App\Models\User;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class TechnicianController extends Controller
{
    public function index()
    {
        $title = 'Trang chủ';
        $user = Auth::user();

        return view('technician.index', compact( 'title','user'));
    }

    public function getListLecturer(Request $request)
    {
        $title = 'Giảng viên';
        $user = Auth::user();

        $sortField = $request->input('sort-field', 'updated_at');
        $sortOrder = $request->input('sort-order', 'desc');
        $recordsPerPage = $request->input('records-per-page', 5);

        if ($sortField == 'full_name') {
            $lecturers = Lecturer::orderByRaw("SUBSTRING_INDEX(full_name, ' ', -1) $sortOrder")->paginate($recordsPerPage);
        } else {
            $lecturers = Lecturer::orderBy($sortField, $sortOrder)->paginate($recordsPerPage);
        }

        return view('technician.list-lecturer', compact('title','user', 'lecturers'));
    }

    public function getListStudent(Request $request)
    {
        $title = 'Sinh viên';
        $user = Auth::user();

        $sortField = $request->input('sort-field', 'updated_at');
        $sortOrder = $request->input('sort-order', 'desc');
        $recordsPerPage = $request->input('records-per-page', 5);

        if ($sortField == 'full_name') {
            $students = Student::orderByRaw("SUBSTRING_INDEX(full_name, ' ', -1) $sortOrder")->paginate($recordsPerPage);
        } else {
            $students = Student::orderBy($sortField, $sortOrder)->paginate($recordsPerPage);
        }

        return view('technician.list-student', compact('title','user', 'students'));
    }

    public function getListClass(Request $request) {
        $title = 'Lớp học phần';
        $user = Auth::user();

        $sortField = $request->input('sortField', 'updated_at');
        $sortOrder = $request->input('sortOrder', 'desc');
        $recordsPerPage = $request->input('records-per-page', 5);

        if ($sortField == 'lecturer') {
            $classes = CreditClass::withCount('classSessions')
                ->join('lecturers', 'classes.lecturer_id', '=', 'lecturers.id')
                ->orderByRaw("SUBSTRING_INDEX(lecturers.full_name, ' ', -1) $sortOrder")
                ->paginate($recordsPerPage);
        } else {
            $classes = CreditClass::withCount('classSessions')->orderBy($sortField, $sortOrder)->paginate($recordsPerPage);
        }

        $classes->transform(function ($class) {
            $class->start_date = Carbon::parse($class->start_date)->format('d-m-Y');
            $class->end_date = Carbon::parse($class->end_date)->format('d-m-Y');
            $class->classInfo = $this->searchSessionsInfo($class);

            return $class;
        });

        $lecturers = Lecturer::all();
        $buildings = Building::orderBy('name', 'asc')->get();
        $rooms = Room::orderBy('name', 'asc')->get();
        $classSessions = $classes->map(function ($class) {
            return $class->classSessions;
        });
        $lessons = $classSessions->map(function ($sessions) {
            return $sessions->map(function ($session) {
                return $session->lessons;
            });
        });

        $fullLessons = Lesson::all();

        // Schedule
        $schedule = $this->getSchedule();
        $classes_schedule = CreditClass::all();
        $classes_schedule->transform(function ($class) {
            $class->start_date = Carbon::parse($class->start_date)->format('d-m-Y');
            $class->end_date = Carbon::parse($class->end_date)->format('d-m-Y');
            $class->classInfo = $this->searchSessionsInfo($class);

            return $class;
        });

        return view('technician.list-class', compact('title','user', 'classes', 'lecturers', 'buildings', 'rooms', 'classSessions', 'lessons',
            'fullLessons', 'classes_schedule'))->with('schedule', $schedule['schedule'])->with('daysOfWeek', $schedule['daysOfWeek']);
    }

    public function getClassSessionInfoAPI(Request $request)
    {
        $session = ClassSession::findOrFail($request->input('sessionId'));
        $selectedDate = Carbon::createFromFormat('d-m-Y', $request->input('selectedDate'))->format('Y-m-d');

        $room = $session->room;
        $building = $room->building;
        $computers = $room->computers;
        $attendances = Attendance::where('session_id', $session->id)
            ->whereDate('attendance_date', $selectedDate)
            ->get();
        $sessionInfo = [
            'session_id' => $session->id,
            'building' => $building,
            'room' => $room,
            'computers' => $computers,
            'attendances' => $attendances
        ];

        $table_class_session_info = view('technician.table-class-session-info', compact('sessionInfo'))->render();

        return response()->json(['table_class_session_info' => $table_class_session_info]);
    }

    private function searchSessionsInfo($class) {
        $sessionsInfo = [];
        $startDate = Carbon::createFromFormat('d-m-Y', $class->start_date);
        $endDate = Carbon::now();

        foreach ($class->classSessions as $session) {
            $currentDate = $startDate->copy();
            while ($currentDate->lte($endDate)) {
                if ($currentDate->dayOfWeekIso + 1 == $session->day_of_week) {
                    $room = $session->room;
                    $building = $room->building;
                    $computers = $room->computers;
                    $attendances = Attendance::where('session_id', $session->id)
                        ->whereDate('attendance_date', $currentDate->format('Y-m-d'))
                        ->get();
                    $sessionsInfo[] = [
                        'session_id' => $session->id,
                        'date' => $currentDate->format('d-m-Y'),
                        'building' => $building,
                        'room' => $room,
                        'computers' => $computers,
                        'attendances' => $attendances
                    ];
                }
                $currentDate->addDay();
            }
        }
        return $sessionsInfo;
    }

    public function getSchedule() {
        $creditClassesSchedule = CreditClass::where('status', 'active')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->get();

        $daysOfWeek = ['2' => 'Thứ hai', '3' => 'Thứ ba', '4' => 'Thứ tư', '5' => 'Thứ năm', '6' => 'Thứ sáu', '7' => 'Thứ bảy', '8' => 'Chủ nhật'];

        $schedule = [];

        foreach ($creditClassesSchedule as $creditClass) {
            $classSessionsSchedule = $creditClass->classSessions()->orderBy('start_lesson', 'asc')->get();

            foreach ($classSessionsSchedule as $classSession) {
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

        return ['schedule' => $schedule, 'daysOfWeek' => $daysOfWeek];
    }

    public function getLessonOfClassSessionAPI(string $classId)
    {
        $class = CreditClass::with(['classSessions.lessons' => function ($query) {
            $query->select('id', 'session_id');
        }])->findOrFail($classId);

        $classSessionsLessons = $class->classSessions->map(function ($session) {
            $minLessonId = $session->lessons->min('id');
            $maxLessonId = $session->lessons->max('id');

            return [
                'min_lesson_id' => $minLessonId,
                'max_lesson_id' => $maxLessonId,
            ];
        });

        return response()->json($classSessionsLessons);
    }

    public function getListBuilding() {
        $title = 'Nhà thực hành';
        $user = Auth::user();

        $buildings = Building::orderBy('name', 'asc')->get();

        return view('technician.list-building', compact('title','user', 'buildings'));
    }

    public function getListRoom(string $building_id) {
        $title = 'Phòng máy';
        $user = Auth::user();

        $rooms = Room::where('building_id', $building_id)->orderBy('name', 'asc')->get();

        return view('technician.list-room', compact('title','user', 'rooms', 'building_id'));
    }

    public function getListComputer(string $room_id)
    {
        $title = 'Sơ đồ phòng máy';
        $user = Auth::user();

        $room = Room::where('id', $room_id)->first();
        $building = Building::where('id', $room->building_id)->first();
        $computers = Computer::where('room_id', $room_id)->get();

        return view('technician.list-computer', compact('title','user', 'building', 'room', 'computers'));
    }

    public function getListStudentClass(Request $request, string $class_id)
    {
        $title = 'Sinh viên lớp học phần';
        $user = Auth::user();

        $sortField = $request->input('sort-field', 'created_at');
        $sortOrder = $request->input('sort-order', 'desc');
        $recordsPerPage = $request->input('records-per-page', 5);

        $class = CreditClass::where('id', $class_id)->first();
        if ($sortField == 'full_name') {
            $students = $class->students()->orderByRaw("SUBSTRING_INDEX(full_name, ' ', -1) $sortOrder")->paginate($recordsPerPage);
        } else {
            $students = $class->students()->orderBy($sortField, $sortOrder)->paginate($recordsPerPage);
        }

        return view('technician.list-student-class', compact('title','user', 'students', 'class'));
    }

    public function getListReport(Request $request)
    {
        $title = 'Báo cáo sự cố';
        $user = Auth::user();

        $sortField = $request->input('sort-field', 'created_at');
        $sortOrder = $request->input('sort-order', 'desc');
        $recordsPerPage = $request->input('records-per-page', 5);

        $reports = Report::where('is_approved', 1)->orderBy($sortField, $sortOrder)->paginate($recordsPerPage);

        return view('technician.list-report', compact('title','user', 'reports'));
    }

    public function getPersonalInfo()
    {
        $title = 'Thông tin cá nhân';

        $user = Auth::user();

        return view('technician.personal-info', compact('title', 'user'));
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

    public function getClassSessionsAPI(string $class_id)
    {
        $class = CreditClass::with('classSessions')->findOrFail($class_id);
        $classSessions = $class->classSessions;

        return response()->json(['classSessions' => $classSessions, 'lessons' => $classSessions->map(function ($session) {
            return $session->lessons;
        })]);
    }

    public function storeLecturerAPI(Request $request)
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
        $user->role_id = '4';

        $user->save();

        $lecturer = new Lecturer();

        $lecturer->full_name = $request->input('full-name');
        $lecturer->academic_rank = $request->input('academic-rank');
        $lecturer->department = $request->input('department');
        $lecturer->faculty = $request->input('faculty');
        $lecturer->position = $request->input('position');

        $lecturer->user_id = $user->id;

        $lecturer->save();

        $recordsPerPage = $request->input('records-per-page', 5);

        $lecturers = Lecturer::orderBy('updated_at', 'desc')->paginate($recordsPerPage);
        $table_lecturer = view('technician.table-lecturer', compact('lecturers'))->render();

        return response()->json(['success' => 'Thêm giảng viên thành công!', 'table_lecturer' => $table_lecturer, 'links' => $lecturers->render('pagination::bootstrap-5')->toHtml()]);
    }

    public function storeStudentAPI(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'full-name' => 'required|max:255',
            'student-code' => 'required|max:255|unique:students,student_code',
        ], [
            'full-name.required' => 'Vui lòng nhập họ và tên!',
            'full-name.max' => 'Họ và tên không được vượt quá 255 ký tự!',
            'student-code.required' => 'Vui lòng nhập địa mã sinh viên!',
            'student-code.unique' => 'Mã sinh viên đã được sử dụng!',
            'student-code.max' => 'Mã sinh viên không được vượt quá 255 ký tự!',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        $user = new User();

        $user->email = trim($request->input('student-code')) . '@e.tlu.edu.vn';
        $user->password = Hash::make('123456');
        $user->phone = $request->input('phone');
        $user->role_id = '3';

        $user->save();

        $student = new Student();

        $student->full_name = $request->input('full-name');
        $student->student_code = $request->input('student-code');
        $student->class = $request->input('class');
        $student->gender = $request->input('gender');
        $student->date_of_birth = $request->input('date-of-birth');

        $student->user_id = $user->id;

        $student->save();

        $recordsPerPage = $request->input('records-per-page', 5);

        $students = Student::orderBy('updated_at', 'desc')->paginate($recordsPerPage);
        $table_student = view('technician.table-student', compact('students'))->render();

        return response()->json(['success' => 'Thêm sinh viên thành công!', 'table_student' => $table_student, 'links' => $students->render('pagination::bootstrap-5')->toHtml()]);
    }

    public function storeClassAPI(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'class-name' => 'required|string|max:255',
            'start-date' => 'required|date|before:end-date',
            'end-date'   => 'required|date|after:start-date',
        ], [
            'class-name.required' => 'Vui lòng nhập tên lớp học phần!',
            'class-name.string' => 'Tên lớp học phần phải là chuỗi!',
            'class-name.max' => 'Tên lớp học phần không được vượt quá 255 ký tự!',
            'start-data.date' => 'Ngày bắt đầu không hợp lệ!',
            'start-date.required' => 'Vui lòng chọn ngày bắt đầu!',
            'start-date.before' => 'Ngày bắt đầu phải trước ngày kết thúc!',
            'end-date.date' => 'Ngày kết thúc không hợp lệ!',
            'end-date.required' => 'Vui lòng chọn ngày kết thúc!',
            'end-date.after' => 'Ngày kết thúc phải sau ngày bắt đầu!',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        $lessonRanges = [];

        $daysOfWeeks = $request->input('day-of-week[]');
        $startLessons = $request->input('start-lesson[]');
        $endLessons = $request->input('end-lesson[]');
        $roomIds = $request->input('room[]');
        $startDate = $request->input('start-date');
        $endDate = $request->input('end-date');

        foreach ($daysOfWeeks as $key => $daysOfWeek) {
            if ($endLessons[$key] - $startLessons[$key] >= 3) {
                return response()->json(['errors' => ['class-session' => "Số tiết học trong một buổi học không được vượt quá 3 tiết!"]]);
            }

            if (!isset($lessonRanges[$daysOfWeek])) {
                $lessonRanges[$daysOfWeek] = [];
            }

            foreach ($lessonRanges[$daysOfWeek] as $existingRange) {
                if (($startLessons[$key] >= $existingRange[0] && $startLessons[$key] <= $existingRange[1]) || ($endLessons[$key] >= $existingRange[0] && $endLessons[$key] <= $existingRange[1])) {
                    return response()->json(['errors' => ['class-session' => "Buổi học vào thứ {$daysOfWeek} bị trùng tiết học với nhau!"]]);
                }
            }

            $lessonRanges[$daysOfWeek][] = [$startLessons[$key], $endLessons[$key], $roomIds[$key]];

            foreach ($lessonRanges[$daysOfWeek] as $range) {
                $room_id = $range[2];
                $conflictingClass = ClassSession::whereHas('lessons', function ($query) use ($range, $daysOfWeek, $room_id) {
                    $query->where('lessons.id', '>=', $range[0])
                        ->where('lessons.id', '<=', $range[1])
                        ->where('day_of_week', $daysOfWeek)
                        ->where('room_id', $room_id);
                })->whereHas('creditClass', function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('start_date', [$startDate, $endDate])
                        ->orWhereBetween('end_date', [$startDate, $endDate]);
                })->first();

                if ($conflictingClass) {
                    return response()->json(['errors' => ['class-session' => "Buổi học vào thứ {$daysOfWeek} bị trùng tiết học với lớp học khác! (bấm để xem)", 'class-id' => $conflictingClass->class_id]]);
                }
            }
        }

        $lecturerId = $request->input('lecturer');
        $lecturerClasses = CreditClass::where('lecturer_id', $lecturerId)->get();

        foreach ($lecturerClasses as $lecturerClass) {
            if (Carbon::parse($lecturerClass->start_date)->between($startDate, $endDate) ||
                Carbon::parse($lecturerClass->end_date)->between($startDate, $endDate)) {
                foreach ($lecturerClass->classSessions as $classSession) {
                    $startLessonId = $classSession->lessons()->orderBy('start_time', 'asc')->first()->id;
                    $endLessonId = $classSession->lessons()->orderBy('end_time', 'desc')->first()->id;
                    foreach ($lessonRanges as $dayOfWeek => $ranges) {
                        foreach ($ranges as $range) {
                            if ($classSession->day_of_week == $dayOfWeek &&
                                (($startLessonId >= $range[0] && $startLessonId <= $range[1]) ||
                                    ($endLessonId >= $range[0] && $endLessonId <= $range[1]))) {
                                return response()->json(['errors' => ['class-session' => "Giảng viên đã có buổi học trùng với buổi học bạn đang cố gắng thêm! (bấm để xem)", 'class-id' => $classSession->class_id]]);
                            }
                        }
                    }
                }
            }
        }

        $creditClass = new CreditClass();

        $creditClass->name = $request->input('class-name');
        $creditClass->start_date = $startDate;
        $creditClass->end_date = $endDate;
        $creditClass->class_code = $request->input('class-code');
        $creditClass->lecturer_id = $request->input('lecturer');

        $creditClass->save();

        for ($i = 0; $i < $request->input('number-of-session'); $i++) {
            $classSession = new ClassSession();
            $lessonStart = Lesson::where('id', $request->input('start-lesson[]')[$i])->first();
            $lessonEnd = Lesson::where('id', $request->input('end-lesson[]')[$i])->first();

            $classSession->start_lesson = $lessonStart->start_time;
            $classSession->end_lesson = $lessonEnd->end_time;
            $classSession->day_of_week = $request->input('day-of-week[]')[$i];
            $classSession->room_id = $request->input('room[]')[$i];
            $classSession->class_id = $creditClass->id;

            $classSession->save();

            $classSession->lessons()->attach(range($lessonStart->id, $lessonEnd->id));
        }

        $recordsPerPage = $request->input('records-per-page', 5);

        $classes = CreditClass::withCount('classSessions')->orderBy('updated_at', 'desc')->paginate($recordsPerPage);

        $classes->transform(function ($class) {
            $class->start_date = Carbon::parse($class->start_date)->format('d-m-Y');
            $class->end_date = Carbon::parse($class->end_date)->format('d-m-Y');
            $class->classInfo = $this->searchSessionsInfo($class);

            return $class;
        });

        $lecturers = Lecturer::all();
        $buildings = Building::orderBy('name', 'asc')->get();
        $rooms = Room::orderBy('name', 'asc')->get();
        $classSessions = $classes->map(function ($class) {
            return $class->classSessions;
        });
        $lessons = $classSessions->map(function ($sessions) {
            return $sessions->map(function ($session) {
                return $session->lessons;
            });
        });

        $table_class = view('technician.table-class', compact('classes', 'lecturers', 'buildings', 'rooms'))->render();

        $schedule = $this->getSchedule();
        $classes_schedule = CreditClass::all();
        $classes_schedule->transform(function ($class) {
            $class->start_date = Carbon::parse($class->start_date)->format('d-m-Y');
            $class->end_date = Carbon::parse($class->end_date)->format('d-m-Y');
            $class->classInfo = $this->searchSessionsInfo($class);

            return $class;
        });

        $table_schedule = view('technician.table-schedule', compact('buildings', 'rooms', 'classes_schedule'))->with('schedule', $schedule['schedule'])->with('daysOfWeek', $schedule['daysOfWeek'])->render();

        return response()->json([
            'success' => 'Thêm lớp học phần thành công!',
            'table_class' => $table_class,
            'links' => $classes->render('pagination::bootstrap-5')->toHtml(),
            'class_sessions' => $classSessions,
            'lessons' => $lessons,
            'table_schedule' => $table_schedule,
        ]);
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
            $studentsCount = $newClass->students()->count();
            $minRoomCapacity = $newClassSessions->pluck('room.capacity')->min();

            if ($studentsCount == $minRoomCapacity) {
                return response()->json(['errors' => ['student-class' => 'Lớp học đã hết chỗ ngồi!']]);
            }

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

            $recordsPerPage = $request->input('records-per-page', 5);

            $class = CreditClass::where('id', $class_id)->first();
            $students = $class->students()->orderBy('class_student.created_at', 'desc')->paginate($recordsPerPage);
            $table_student_class = view('technician.table-student-class', compact('students', 'class'))->render();

            return response()->json(['success' => 'Đã thêm sinh viên vào lớp học!', 'table_student_class' => $table_student_class, 'links' => $students->render('pagination::bootstrap-5')->toHtml()]);
        }
    }

    public function storeBuildingAPI(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'building-name' => 'required|max:255',
        ], [
            'building-name.required' => 'Vui lòng nhập tên nhà thực hành!',
            'building-name.max' => 'Tên nhà thực hành không được vượt quá 255 ký tự!',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        $building = new Building();
        $building->name = $request->input('building-name');

        $building->save();

        $buildings = Building::orderBy('name', 'asc')->get();
        $table_building = view('technician.table-building', compact('buildings'))->render();

        return response()->json(['success' => 'Thêm nhà thực hành thành công!', 'table_building' => $table_building]);
    }

    public function storeRoomAPI(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'room-name' => 'required|max:255',
            'capacity' => 'required|numeric|min:1',
        ], [
            'room-name.required' => 'Vui lòng nhập tên phòng máy!',
            'room-name.max' => 'Tên phòng máy không được vượt quá 255 ký tự!',
            'capacity.required' => 'Vui lòng nhập sức chứa của phòng máy!',
            'capacity.numeric' => 'Sức chứa của phòng máy phải là một số!',
            'capacity.min' => 'Sức chứa của phòng máy phải lớn hơn 0!',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        $room = new Room();
        $room->name = $request->input('room-name');
        $room->capacity = $request->input('capacity');
        $room->building_id = $request->input('building_id');

        $room->save();

        $building_id = $request->input('building_id');

        $rooms = Room::where('building_id', $building_id)->orderBy('name', 'asc')->get();
        $table_room = view('technician.table-room', compact('rooms', 'building_id'))->render();

        return response()->json(['success' => 'Thêm phòng máy thành công!', 'table_room' => $table_room]);
    }

    public function storeComputerAPI(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'position' => 'required|int|min:1',
        ], [
            'position.required' => 'Vui lòng nhập vị trí máy tính!',
            'position.int' => 'Vị trí máy tính phải là một số nguyên!',
            'position.min' => 'Vị trí máy tính phải lớn hơn 0!',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        $room = Room::findOrFail($request->input('room_id'));

        $computer = new Computer();
        $computer->position = $request->input('position');
        $computer->configuration = $request->input('configuration');
        $computer->purchase_date = $request->input('purchase-date');
        $computer->room_id = $room->id;

        $computer->save();

        $computers = Computer::where('room_id', $room->id)->get();
        $table_computer = view('technician.table-computer', compact('computers', 'room'))->render();

        return response()->json(['success' => 'Thêm máy tính thành công!', 'table_computer' => $table_computer]);
    }

    public function updateLecturerAPI(Request $request, string $id)
    {
        $lecturer = Lecturer::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'full-name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $lecturer->user_id,
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

        $user = User::findOrFail($lecturer->user_id);
        if ($user->email != $request->input('email')) {
            $user->email = $request->input('email');
            $user->is_verified = false;
        }
        $user->phone = $request->input('phone');

        $user->save();

        $lecturer->full_name = $request->input('full-name');
        $lecturer->academic_rank = $request->input('academic-rank');
        $lecturer->department = $request->input('department');
        $lecturer->faculty = $request->input('faculty');
        $lecturer->position = $request->input('position');

        $lecturer->save();

        $recordsPerPage = $request->input('records-per-page', 5);

        $lecturers = Lecturer::orderBy('updated_at', 'desc')->paginate($recordsPerPage);
        $table_lecturer = view('technician.table-lecturer', compact('lecturers'))->render();

        return response()->json(['success' => 'Chỉnh sửa thông tin giảng viên thành công!', 'table_lecturer' => $table_lecturer, 'links' => $lecturers->render('pagination::bootstrap-5')->toHtml()]);
    }

    public function updatePasswordLecturerAPI(Request $request, string $id)
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

        $lecturer = Lecturer::findOrFail($id);

        $user = User::findOrFail($lecturer->user_id);
        $user->password = $request->input('new-password');

        $user->save();

        return response()->json(['success' => 'Đổi mật khẩu tài khoản của giảng viên thành công!']);
    }

    public function updateStudentAPI(Request $request, string $id)
    {
        $student = Student::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'full-name' => 'required|max:255',
            'student-code' => 'required|max:255|unique:students,student_code,' . $student->id,
        ], [
            'full-name.required' => 'Vui lòng nhập họ và tên!',
            'full-name.max' => 'Họ và tên không được vượt quá 255 ký tự!',
            'student-code.required' => 'Vui lòng nhập địa mã sinh viên!',
            'student-code.unique' => 'Mã sinh viên đã được sử dụng!',
            'student-code.max' => 'Mã sinh viên không được vượt quá 255 ký tự!',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        $user = User::findOrFail($student->user_id);
        if ($student->student_code != $request->input('student-code')) {
            $user->email = trim($request->input('student-code')) . '@e.tlu.edu.vn';
            $user->is_verified = false;
        }
        $user->phone = $request->input('phone');

        $user->save();

        $student->full_name = $request->input('full-name');
        $student->student_code = $request->input('student-code');
        $student->class = $request->input('class');
        $student->gender = $request->input('gender');
        $student->date_of_birth = $request->input('date-of-birth');

        $student->save();

        $recordsPerPage = $request->input('records-per-page', 5);

        $students = Student::orderBy('updated_at', 'desc')->paginate($recordsPerPage);
        $table_student = view('technician.table-student', compact('students'))->render();

        return response()->json(['success' => 'Chỉnh sửa thông tin sinh viên thành công!', 'table_student' => $table_student, 'links' => $students->render('pagination::bootstrap-5')->toHtml()]);
    }

    public function updatePasswordStudentAPI(Request $request, string $id)
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

        $student = Student::findOrFail($id);

        $user = User::findOrFail($student->user_id);
        $user->password = $request->input('new-password');

        $user->save();

        return response()->json(['success' => 'Đổi mật khẩu tài khoản của sinh viên thành công!']);
    }

    public function updateClassAPI(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'class-name' => 'required|string|max:255',
            'start-date' => 'required|date|before:end-date',
            'end-date'   => 'required|date|after:start-date',
        ], [
            'class-name.required' => 'Vui lòng nhập tên lớp học phần!',
            'class-name.string' => 'Tên lớp học phần phải là chuỗi!',
            'class-name.max' => 'Tên lớp học phần không được vượt quá 255 ký tự!',
            'start-data.date' => 'Ngày bắt đầu không hợp lệ!',
            'start-date.required' => 'Vui lòng chọn ngày bắt đầu!',
            'start-date.before' => 'Ngày bắt đầu phải trước ngày kết thúc!',
            'end-date.date' => 'Ngày kết thúc không hợp lệ!',
            'end-date.required' => 'Vui lòng chọn ngày kết thúc!',
            'end-date.after' => 'Ngày kết thúc phải sau ngày bắt đầu!',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        $lessonRanges = [];

        $daysOfWeeks = $request->input('day-of-week[]');
        $startLessons = $request->input('start-lesson[]');
        $endLessons = $request->input('end-lesson[]');
        $roomIds = $request->input('room[]');
        $startDate = $request->input('start-date');
        $endDate = $request->input('end-date');

        foreach ($daysOfWeeks as $key => $daysOfWeek) {
            if ($endLessons[$key] - $startLessons[$key] >= 3) {
                return response()->json(['errors' => ['class-session' => "Số tiết học trong một buổi học không được vượt quá 3 tiết!"]]);
            }

            if (!isset($lessonRanges[$daysOfWeek])) {
                $lessonRanges[$daysOfWeek] = [];
            }

            foreach ($lessonRanges[$daysOfWeek] as $existingRange) {
                if (($startLessons[$key] >= $existingRange[0] && $startLessons[$key] <= $existingRange[1]) || ($endLessons[$key] >= $existingRange[0] && $endLessons[$key] <= $existingRange[1])) {
                    return response()->json(['errors' => ['class-session' => "Buổi học vào thứ {$daysOfWeek} bị trùng tiết học với nhau!"]]);
                }
            }

            $lessonRanges[$daysOfWeek][] = [$startLessons[$key], $endLessons[$key], $roomIds[$key]];

            foreach ($lessonRanges[$daysOfWeek] as $range) {
                $room_id = $range[2];
                $conflictingClass = ClassSession::whereHas('lessons', function ($query) use ($range, $daysOfWeek, $room_id, $id) {
                    $query->where('lessons.id', '>=', $range[0])
                        ->where('lessons.id', '<=', $range[1])
                        ->where('day_of_week', $daysOfWeek)
                        ->where('room_id', $room_id)
                        ->where('class_sessions.class_id', '!=', $id);
                })->whereHas('creditClass', function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('start_date', [$startDate, $endDate])
                        ->orWhereBetween('end_date', [$startDate, $endDate]);
                })->first();

                if ($conflictingClass) {
                    return response()->json(['errors' => ['class-session' => "Buổi học vào thứ {$daysOfWeek} bị trùng tiết học với lớp học khác! (bấm để xem)", 'class-id' => $conflictingClass->class_id]]);
                }
            }
        }

        $lecturerId = $request->input('lecturer');
        $lecturerClasses = CreditClass::where('lecturer_id', $lecturerId)->where('id', '!=', $id)->get();

        foreach ($lecturerClasses as $lecturerClass) {
            if (Carbon::parse($lecturerClass->start_date)->between($startDate, $endDate) ||
                Carbon::parse($lecturerClass->end_date)->between($startDate, $endDate)) {
                foreach ($lecturerClass->classSessions as $classSession) {
                    $startLessonId = $classSession->lessons()->orderBy('start_time', 'asc')->first()->id;
                    $endLessonId = $classSession->lessons()->orderBy('end_time', 'desc')->first()->id;
                    foreach ($lessonRanges as $dayOfWeek => $ranges) {
                        foreach ($ranges as $range) {
                            if ($classSession->day_of_week == $dayOfWeek &&
                                (($startLessonId >= $range[0] && $startLessonId <= $range[1]) ||
                                    ($endLessonId >= $range[0] && $endLessonId <= $range[1]))) {
                                return response()->json(['errors' => ['class-session' => "Giảng viên đã có buổi học trùng với buổi học bạn đang cố gắng thêm! (bấm để xem)", 'class-id' => $classSession->class_id]]);
                            }
                        }
                    }
                }
            }
        }

        $creditClass = CreditClass::findOrFail($id);

        $creditClass->name = $request->input('class-name');
        $creditClass->start_date = $startDate;
        $creditClass->end_date = $endDate;
        $creditClass->class_code = $request->input('class-code');
        $creditClass->lecturer_id = $request->input('lecturer');

        $creditClass->save();
        $creditClass->touch();

        $classSessions = ClassSession::where('class_id', $id)->get();

        $oldSessions = $classSessions->count();
        $newSessions = $request->input('number-of-session');

        foreach ($classSessions as $index => $classSession) {
            if ($index < $newSessions) {
                $lessonStart = Lesson::where('id', $request->input('start-lesson[]')[$index])->first();
                $lessonEnd = Lesson::where('id', $request->input('end-lesson[]')[$index])->first();

                $classSession->start_lesson = $lessonStart->start_time;
                $classSession->end_lesson = $lessonEnd->end_time;
                $classSession->day_of_week = $request->input('day-of-week[]')[$index];
                $classSession->room_id = $request->input('room[]')[$index];

                $classSession->save();

                $classSession->lessons()->sync(range($lessonStart->id, $lessonEnd->id));
            } else {
                $classSession->lessons()->detach();
                $classSession->delete();
            }
        }

        for ($i = $oldSessions; $i < $newSessions; $i++) {
            $classSession = new ClassSession();
            $lessonStart = Lesson::where('id', $request->input('start-lesson[]')[$i])->first();
            $lessonEnd = Lesson::where('id', $request->input('end-lesson[]')[$i])->first();

            $classSession->start_lesson = $lessonStart->start_time;
            $classSession->end_lesson = $lessonEnd->end_time;
            $classSession->day_of_week = $request->input('day-of-week[]')[$i];
            $classSession->room_id = $request->input('room[]')[$i];
            $classSession->class_id = $creditClass->id;

            $classSession->save();

            // Gán lessons cho session mới
            $classSession->lessons()->attach(range($lessonStart->id, $lessonEnd->id));
        }

        $recordsPerPage = $request->input('records-per-page', 5);

        $classes = CreditClass::withCount('classSessions')->orderBy('updated_at', 'desc')->paginate($recordsPerPage);

        $classes->transform(function ($class) {
            $class->start_date = Carbon::parse($class->start_date)->format('d-m-Y');
            $class->end_date = Carbon::parse($class->end_date)->format('d-m-Y');
            $class->classInfo = $this->searchSessionsInfo($class);

            return $class;
        });

        $lecturers = Lecturer::all();
        $buildings = Building::orderBy('name', 'asc')->get();
        $rooms = Room::orderBy('name', 'asc')->get();
        $classSessions = $classes->map(function ($class) {
            return $class->classSessions;
        });
        $lessons = $classSessions->map(function ($sessions) {
            return $sessions->map(function ($session) {
                return $session->lessons;
            });
        });

        $table_class = view('technician.table-class', compact('classes', 'lecturers', 'buildings', 'rooms'))->render();

        $schedule = $this->getSchedule();
        $classes_schedule = CreditClass::all();
        $classes_schedule->transform(function ($class) {
            $class->start_date = Carbon::parse($class->start_date)->format('d-m-Y');
            $class->end_date = Carbon::parse($class->end_date)->format('d-m-Y');
            $class->classInfo = $this->searchSessionsInfo($class);

            return $class;
        });

        $table_schedule = view('technician.table-schedule', compact('buildings', 'rooms', 'classes_schedule'))->with('schedule', $schedule['schedule'])->with('daysOfWeek', $schedule['daysOfWeek'])->render();

        return response()->json([
            'success' => 'Chỉnh sửa thông tin lớp học phần thành công!',
            'table_class' => $table_class,
            'links' => $classes->render('pagination::bootstrap-5')->toHtml(),
            'class_sessions' => $classSessions,
            'lessons' => $lessons,
            'table_schedule' => $table_schedule,
        ]);
    }

    public function updateStatusClassAPI(string $id) {
        $class = CreditClass::findOrFail($id);

        if ($class->status == 'active') {
            $class->status = 'inactive';
            $message = 'Đóng lớp thành công!';
        } else {
            $class->status = 'active';
            $message = 'Mở lớp thành công!';
        }

        $class->save();

        $buildings = Building::orderBy('name', 'asc')->get();
        $rooms = Room::orderBy('name', 'asc')->get();
        $schedule = $this->getSchedule();
        $classes_schedule = CreditClass::all();
        $classes_schedule->transform(function ($class) {
            $class->start_date = Carbon::parse($class->start_date)->format('d-m-Y');
            $class->end_date = Carbon::parse($class->end_date)->format('d-m-Y');
            $class->classInfo = $this->searchSessionsInfo($class);

            return $class;
        });

        $table_schedule = view('technician.table-schedule', compact('buildings', 'rooms', 'classes_schedule'))->with('schedule', $schedule['schedule'])->with('daysOfWeek', $schedule['daysOfWeek'])->render();

        return response()->json(['success' => $message, 'table_schedule' => $table_schedule]);
    }

    public function updateBuildingAPI(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'building-name' => 'required|max:255',
        ], [
            'building-name.required' => 'Vui lòng nhập tên nhà thực hành!',
            'building-name.max' => 'Tên nhà thực hành không được vượt quá 255 ký tự!',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        $building = Building::findOrFail($id);
        $building->name = $request->input('building-name');

        $building->save();

        $buildings = Building::orderBy('name', 'asc')->get();
        $table_building = view('technician.table-building', compact('buildings'))->render();

        return response()->json(['success' => 'Chỉnh sửa thông tin nhà thực hành thành công!', 'table_building' => $table_building]);
    }

    public function updateRoomAPI(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'room-name' => 'required|max:255',
            'capacity' => 'required|numeric|min:1',
        ], [
            'room-name.required' => 'Vui lòng nhập tên phòng máy!',
            'room-name.max' => 'Tên phòng máy không được vượt quá 255 ký tự!',
            'capacity.required' => 'Vui lòng nhập sức chứa của phòng máy!',
            'capacity.numeric' => 'Sức chứa của phòng máy phải là một số!',
            'capacity.min' => 'Sức chứa của phòng máy phải lớn hơn 0!',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        $maxPositionComputer = Room::findOrFail($id)->computers()->max('position');

        if ($request->input('capacity') < $maxPositionComputer) {
            return response()->json(['errors' => ['capacity' => 'Sức chứa của phòng máy không thể nhỏ hơn vị trí máy tính lớn nhất đã đặt!']]);
        }

        $room = Room::findOrFail($id);
        $room->name = $request->input('room-name');
        $room->capacity = $request->input('capacity');

        $room->save();

        $building_id = $request->input('building_id');

        $rooms = Room::where('building_id', $building_id)->orderBy('name', 'asc')->get();
        $table_room = view('technician.table-room', compact('rooms', 'building_id'))->render();

        return response()->json(['success' => 'Chỉnh sửa thông tin phòng máy thành công!', 'table_room' => $table_room]);
    }

    public function updateComputerAPI(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'position' => 'required|int|min:1',
        ], [
            'position.required' => 'Vui lòng nhập vị trí máy tính!',
            'position.int' => 'Vị trí máy tính phải là một số nguyên!',
            'position.min' => 'Vị trí máy tính phải lớn hơn 0!',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        $room = Room::findOrFail($request->input('room_id'));

        $computer = Computer::findOrFail($id);
        $computer->position = $request->input('position');
        $computer->configuration = $request->input('configuration');
        $computer->purchase_date = $request->input('purchase-date');

        $computer->save();

        $computers = Computer::where('room_id', $room->id)->get();
        $table_computer = view('technician.table-computer', compact('computers', 'room'))->render();

        return response()->json(['success' => 'Chỉnh sửa thông tin máy tính thành công!', 'table_computer' => $table_computer]);
    }

    public function updateLessonAPI(Request $request)
    {
        $startLessons = $request->input('start-lesson[]');
        $endLessons = $request->input('end-lesson[]');

        foreach ($startLessons as $index => $startLesson) {
            if (empty($startLesson) || empty($endLessons[$index])) {
                return response()->json(['errors' => ['lesson' => 'Vui lòng nhập đầy thời gian bắt đầu và kết thúc cho tiết học ' . ($index + 1) . '!']]);
            }

            $startLessonTime = new DateTime($startLesson);
            $endLessonTime = new DateTime($endLessons[$index]);

            if ($startLessonTime >= $endLessonTime) {
                return response()->json(['errors' => ['lesson' => 'Thời gian bắt đầu tiết học ' . ($index + 1) . ' phải trước thời gian kết thúc tiết học ' . ($index + 1) . '!']]);
            }
            if ($index > 0) {
                $previousEndLessonTime = new DateTime($endLessons[$index - 1]);
                if ($startLessonTime <= $previousEndLessonTime) {
                    return response()->json(['errors' => ['lesson' => 'Thời gian bắt đầu tiết học ' . ($index + 1) . ' phải sau thời gian kết thúc tiết học ' . $index . '!']]);
                }
            }
        }

        foreach($startLessons as $index => $startLesson) {
            $lesson = Lesson::findOrFail($index + 1);
            $lesson->start_time = $startLesson;
            $lesson->end_time = $endLessons[$index];

            $lesson->save();
        }

        $classSessions = ClassSession::all();
        foreach ($classSessions as $classSession) {
            $minLessonId = $classSession->lessons()->min('lesson_id');
            $maxLessonId = $classSession->lessons()->max('lesson_id');

            $classSession->start_lesson = Lesson::where('id', $minLessonId)->first()->start_time;
            $classSession->end_lesson = Lesson::where('id', $maxLessonId)->first()->end_time;
            $classSession->save();
        }

        return response()->json(['success' => 'Chỉnh sửa thông tin tiết học thành công!']);
    }

    public function startMaintenanceClassAPI(Request $request, string $id) {
        $computer = Computer::findOrFail($id);

        $computer->status = 'unavailable';

        $computer->save();

        $room = Room::findOrFail($request->input('room_id'));

        $computers = Computer::where('room_id', $room->id)->get();
        $table_computer = view('technician.table-computer', compact('computers', 'room'))->render();

        return response()->json(['success' => 'Bắt đầu quá trình bảo trì máy tính thành công!', 'table_computer' => $table_computer]);
    }

    public function endMaintenanceClassAPI(Request $request, string $id) {
        $computer = Computer::findOrFail($id);

        $computer->status = 'available';

        $computer->save();

        $room = Room::findOrFail($request->input('room_id'));

        $computers = Computer::where('room_id', $room->id)->get();
        $table_computer = view('technician.table-computer', compact('computers', 'room'))->render();

        return response()->json(['success' => 'Kết thúc quá trình bảo trì máy tính thành công!', 'table_computer' => $table_computer]);
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

        $technician = Technician::findOrFail($id);

        $user = $technician->user;
        $user->phone = $request->input('phone');

        $user->save();

        $technician->full_name = $request->input('full-name');

        $technician->save();

        $table_personal_info = view('technician.table-personal-info', compact('user'))->render();

        return response()->json(['success' => 'Cập nhật thông tin cá nhân thành công!', 'table_personal_info' => $table_personal_info]);
    }

    public function destroyLecturerAPI(Request $request, string $id)
    {
        $lecturer = Lecturer::findOrFail($id);
        $userId = $lecturer->user_id;

        $activeClasses = $lecturer->creditClasses()
            ->where(function ($query) {
                $query->where('start_date', '<=', Carbon::now())
                    ->where('end_date', '>=', Carbon::now());
            })->get();

        if ($activeClasses->count() > 0) {
            $activeClassNames = $activeClasses->pluck('name')->implode(', ');
            return response()->json([
                'errors' => ['lecturer' => "Không thể xóa vì giảng viên này đang tiếp quản các lớp học: $activeClassNames. Vui lòng chuyển lớp học sang giảng viên khác trước khi xóa!"]
            ]);
        } else if ($lecturer->creditClasses->count() > 0) {
            return response()->json(['errors' => ['lecturer' => 'Không thể xóa vì giảng viên này đã tiếp quản các lớp học! Dữ liệu lưu trữ về giảng viên này sẽ được giữ lại để phục vụ báo cáo!']]);
        }

        $lecturer->delete();

        $user = User::findOrFail($userId);
        $user->delete();

        $recordsPerPage = $request->input('records-per-page', 5);

        $lecturers = Lecturer::orderBy('updated_at', 'desc')->paginate($recordsPerPage);
        $table_lecturer = view('technician.table-lecturer', compact('lecturers'))->render();

        return response()->json(['success' => 'Xóa giảng viên thành công!', 'table_lecturer' => $table_lecturer, 'links' => $lecturers->render('pagination::bootstrap-5')->toHtml()]);
    }

    public function destroyStudentAPI(Request $request, string $id)
    {
        $student = Student::findOrFail($id);
        $activeClasses = $student->creditClasses()
            ->where(function ($query) {
                $query->where('start_date', '<=', Carbon::now())
                    ->where('end_date', '>=', Carbon::now());
            })->get();

        if ($activeClasses->count() > 0) {
            $activeClassNames = $activeClasses->pluck('name')->implode(', ');
            return response()->json([
                'errors' => ['student' => "Không thể xóa vì sinh viên này đang tham gia các lớp học: $activeClassNames. Vui lòng xóa sinh viên khỏi lớp học phần trước khi xóa!"]
            ]);
        } else if ($student->creditClasses->count() > 0) {
            return response()->json(['errors' => ['student' => 'Không thể xóa vì sinh viên này đã tham gia các lớp học!']]);
        }

        $userId = $student->user_id;

        $student->delete();

        $user = User::findOrFail($userId);
        $user->delete();

        $recordsPerPage = $request->input('records-per-page', 5);

        $students = Student::orderBy('updated_at', 'desc')->paginate($recordsPerPage);
        $table_student = view('technician.table-student', compact('students'))->render();

        return response()->json(['success' => 'Xóa sinh viên thành công!', 'table_student' => $table_student, 'links' => $students->render('pagination::bootstrap-5')->toHtml()]);
    }

    public function destroyClassAPI(Request $request, string $id)
    {

        $creditClass = CreditClass::findOrFail($id);

        if (Carbon::now() >= Carbon::parse($creditClass->start_date) && Carbon::now() <= Carbon::parse($creditClass->end_date) && $creditClass->status == 'active') {
            return response()->json(['errors' => ['class' => 'Không thể xóa lớp học phần đang diễn ra!']]);
        }

        $classSessions = ClassSession::where('class_id', $id)->get();
        foreach ($classSessions as $classSession) {
            $classSession->lessons()->detach();
            $classSession->delete();
        }

        $creditClass->delete();

        $recordsPerPage = $request->input('records-per-page', 5);

        $classes = CreditClass::withCount('classSessions')->orderBy('updated_at', 'desc')->paginate($recordsPerPage);

        $classes->transform(function ($class) {
            $class->start_date = Carbon::parse($class->start_date)->format('d-m-Y');
            $class->end_date = Carbon::parse($class->end_date)->format('d-m-Y');
            $class->classInfo = $this->searchSessionsInfo($class);

            return $class;
        });

        $lecturers = Lecturer::all();
        $buildings = Building::orderBy('name', 'asc')->get();
        $rooms = Room::orderBy('name', 'asc')->get();
        $classSessions = $classes->map(function ($class) {
            return $class->classSessions;
        });
        $lessons = $classSessions->map(function ($sessions) {
            return $sessions->map(function ($session) {
                return $session->lessons;
            });
        });

        $table_class = view('technician.table-class', compact('classes', 'lecturers', 'buildings', 'rooms'))->render();

        $schedule = $this->getSchedule();
        $classes_schedule = CreditClass::all();
        $classes_schedule->transform(function ($class) {
            $class->start_date = Carbon::parse($class->start_date)->format('d-m-Y');
            $class->end_date = Carbon::parse($class->end_date)->format('d-m-Y');
            $class->classInfo = $this->searchSessionsInfo($class);

            return $class;
        });

        $table_schedule = view('technician.table-schedule', compact('buildings', 'rooms', 'classes_schedule'))->with('schedule', $schedule['schedule'])->with('daysOfWeek', $schedule['daysOfWeek'])->render();

        return response()->json([
            'success' => 'Xóa lớp học phần thành công!',
            'table_class' => $table_class,
            'links' => $classes->render('pagination::bootstrap-5')->toHtml(),
            'class_sessions' => $classSessions,
            'lessons' => $lessons,
            'table_schedule' => $table_schedule,
        ]);
    }

    public function destroyStudentClassAPI(Request $request, string $student_id)
    {
        $student = Student::findOrFail($student_id);

        $student->creditClasses()->detach($request->input('class_id'));

        $recordsPerPage = $request->input('records-per-page', 5);
        $class = CreditClass::where('id', $request->input('class_id'))->first();
        $students = $class->students()->orderBy('class_student.created_at', 'desc')->paginate($recordsPerPage);
        $table_student_class = view('technician.table-student-class', compact('students', 'class'))->render();

        return response()->json(['success' => 'Xóa sinh viên khỏi lớp học phần thành công!', 'table_student_class' => $table_student_class, 'links' => $students->render('pagination::bootstrap-5')->toHtml()]);
    }

    public function destroyBuildingAPI(string $id)
    {
        $building = Building::findOrFail($id);

        if ($building->rooms->count() > 0) {
            return response()->json(['errors' => ['building' => 'Không thể xóa vì tòa nhà này đang chứa phòng máy! Vui lòng xóa tất cả phòng máy trong tòa nhà trước khi xóa!']]);
        }

        $building->delete();

        $buildings = Building::orderBy('name', 'asc')->get();
        $table_building = view('technician.table-building', compact('buildings'))->render();

        return response()->json(['success' => 'Xóa nhà thực hành thành công!', 'table_building' => $table_building]);
    }

    public function destroyRoomAPI(Request $request, string $id)
    {
        $room = Room::findOrFail($id);

        if ($room->computers->count() > 0) {
            return response()->json(['errors' => ['room' => 'Không thể xóa vì phòng máy này đang chứa máy tính! Vui lòng xóa tất cả máy tính trong phòng máy trước khi xóa!']]);
        }

        $room->delete();

        $building_id = $request->input('building_id');

        $rooms = Room::where('building_id', $building_id)->orderBy('name', 'asc')->get();
        $table_room = view('technician.table-room', compact('rooms', 'building_id'))->render();

        return response()->json(['success' => 'Xóa phòng máy thành công!', 'table_room' => $table_room]);
    }

    public function destroyComputerAPI(Request $request, string $id)
    {
        $computer = Computer::findOrFail($id);

        $computer->delete();

        $room = Room::findOrFail($request->input('room_id'));
        $computers = Computer::where('room_id', $room->id)->get();
        $table_computer = view('technician.table-computer', compact('computers', 'room'))->render();

        return response()->json(['success' => 'Xóa máy tính thành công!', 'table_computer' => $table_computer]);
    }

    public function destroyReportAPI(Request $request, string $report_id)
    {
        $user = Auth::user();
        $report = Report::findOrFail($report_id);

        $report->delete();

        $recordsPerPage = $request->input('records-per-page', 5);

        $reports = Report::where('is_approved', 1)->orderBy('created_at', 'desc')->paginate($recordsPerPage);
        $table_report = view('technician.table-report', compact('reports', 'user'))->render();

        return response()->json(['success' => 'Xóa báo cáo thành công!', 'table_report' => $table_report, 'links' => $reports->render('pagination::bootstrap-5')->toHtml()]);
    }

    public function importLecturerAPI(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lecturer-file' => 'required|mimes:xlsx,xls',
        ], [
            'lecturer-file.required' => 'Vui lòng nhập file!',
            'lecturer-file.mimes' => 'Vui lòng nhập đúng định dạng file excel (.xlsx, .xls)!',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        $file = $request->file('lecturer-file');
        $import = new LecturersImport();
        Excel::import($import, $file);

        $recordsPerPage = $request->input('records-per-page', 5);

        $lecturers = Lecturer::orderBy('updated_at', 'desc')->paginate($recordsPerPage);
        $table_lecturer = view('technician.table-lecturer', compact('lecturers'))->render();

        return response()->json(['success' => 'Nhập file giảng viên thành công!', 'table_lecturer' => $table_lecturer, 'links' => $lecturers->render('pagination::bootstrap-5')->toHtml()]);
    }

    public function importStudentAPI(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'student-file' => 'required|mimes:xlsx,xls',
        ], [
            'student-file.required' => 'Vui lòng nhập file!',
            'student-file.mimes' => 'Vui lòng nhập đúng định dạng file excel (.xlsx, .xls)!',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        $file = $request->file('student-file');
        $import = new StudentsImport();
        Excel::import($import, $file);

        $recordsPerPage = $request->input('records-per-page', 5);

        $students = Student::orderBy('updated_at', 'desc')->paginate($recordsPerPage);
        $table_student = view('technician.table-student', compact('students'))->render();

        return response()->json(['success' => 'Nhập file sinh viên thành công!', 'table_student' => $table_student, 'links' => $students->render('pagination::bootstrap-5')->toHtml()]);
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

        $recordsPerPage = $request->input('records-per-page', 5);

        $class = CreditClass::where('id', $class_id)->first();
        $students = $class->students()->orderBy('class_student.created_at', 'desc')->paginate($recordsPerPage);
        $table_student_class = view('technician.table-student-class', compact('students', 'class'))->render();

        return response()->json(['success' => 'Nhập file sinh viên vào lớp học phần thành công!', 'table_student_class' => $table_student_class, 'links' => $students->render('pagination::bootstrap-5')->toHtml()]);
    }

    public function processingReportAPI(Request $request, string $report_id)
    {
        $user = Auth::user();
        $report = Report::findOrFail($report_id);
        $report->status = 'processing';
        $report->processed_at = null;
        $report->technician_id = Auth::user()->technician->id;
        $report->save();

        $sortField = $request->input('sortField', 'created_at');
        $sortOrder = $request->input('sortOrder', 'desc');
        $recordsPerPage = $request->input('recordsPerPage', 5);

        $reports = Report::where('is_approved', 1)->orderBy($sortField, $sortOrder)->paginate($recordsPerPage);
        $table_report = view('technician.table-report', compact('reports', 'user'))->render();

        return response()->json(['success' => 'Cập nhật trạng thái báo cáo thành công!', 'table_report' => $table_report, 'links' => $reports->render('pagination::bootstrap-5')->toHtml()]);
    }

    public function pendingReportAPI(Request $request, string $report_id)
    {
        $user = Auth::user();
        $report = Report::findOrFail($report_id);
        $report->status = 'pending';
        $report->processed_at = null;
        $report->technician_id = null;
        $report->save();

        $sortField = $request->input('sortField', 'created_at');
        $sortOrder = $request->input('sortOrder', 'desc');
        $recordsPerPage = $request->input('recordsPerPage', 5);

        $reports = Report::where('is_approved', 1)->orderBy($sortField, $sortOrder)->paginate($recordsPerPage);
        $table_report = view('technician.table-report', compact('reports', 'user'))->render();

        return response()->json(['success' => 'Cập nhật trạng thái báo cáo thành công!', 'table_report' => $table_report, 'links' => $reports->render('pagination::bootstrap-5')->toHtml()]);
    }

    public function processedReportAPI(Request $request, string $report_id)
    {
        $user = Auth::user();
        $report = Report::findOrFail($report_id);
        $report->status = 'processed';
        $report->technician_id = Auth::user()->technician->id;
        $report->processed_at = Carbon::now();
        $report->save();

        $sortField = $request->input('sortField', 'created_at');
        $sortOrder = $request->input('sortOrder', 'desc');
        $recordsPerPage = $request->input('recordsPerPage', 5);

        $reports = Report::where('is_approved', 1)->orderBy($sortField, $sortOrder)->paginate($recordsPerPage);
        $table_report = view('technician.table-report', compact('reports', 'user'))->render();

        return response()->json(['success' => 'Cập nhật trạng thái báo cáo thành công!', 'table_report' => $table_report, 'links' => $reports->render('pagination::bootstrap-5')->toHtml()]);
    }

    public function sortLecturerAPI(Request $request)
    {
        $sortField = $request->input('sortField', 'updated_at');
        $sortOrder = $request->input('sortOrder', 'desc');
        $recordsPerPage = $request->input('recordsPerPage', 5);

        if ($sortField == 'full_name') {
            $lecturers = Lecturer::orderByRaw("SUBSTRING_INDEX(full_name, ' ', -1) $sortOrder")->paginate($recordsPerPage);
        } else {
            $lecturers = Lecturer::orderBy($sortField, $sortOrder)->paginate($recordsPerPage);
        }

        $table_lecturer = view('technician.table-lecturer', compact('lecturers'))->render();

        return response()->json(['table_lecturer' => $table_lecturer, 'links' => $lecturers->render('pagination::bootstrap-5')->toHtml()]);
    }

    public function sortStudentAPI(Request $request)
    {
        $sortField = $request->input('sortField', 'updated_at');
        $sortOrder = $request->input('sortOrder', 'desc');
        $recordsPerPage = $request->input('recordsPerPage', 5);

        if ($sortField == 'full_name') {
            $students = Student::orderByRaw("SUBSTRING_INDEX(full_name, ' ', -1) $sortOrder")->paginate($recordsPerPage);
        } else {
            $students = Student::orderBy($sortField, $sortOrder)->paginate($recordsPerPage);
        }

        $table_student = view('technician.table-student', compact('students'))->render();

        return response()->json(['table_student' => $table_student, 'links' => $students->render('pagination::bootstrap-5')->toHtml()]);
    }

    public function sortClassAPI(Request $request)
    {
        $sortField = $request->input('sortField', 'updated_at');
        $sortOrder = $request->input('sortOrder', 'desc');
        $recordsPerPage = $request->input('recordsPerPage', 5);

        if ($sortField == 'lecturer') {
            $classes = CreditClass::withCount('classSessions')
                ->join('lecturers', 'classes.lecturer_id', '=', 'lecturers.id')
                ->orderByRaw("SUBSTRING_INDEX(lecturers.full_name, ' ', -1) $sortOrder")
                ->paginate($recordsPerPage);
        } else {
            $classes = CreditClass::withCount('classSessions')->orderBy($sortField, $sortOrder)->paginate($recordsPerPage);
        }

        $classes->transform(function ($class) {
            $class->start_date = Carbon::parse($class->start_date)->format('d-m-Y');
            $class->end_date = Carbon::parse($class->end_date)->format('d-m-Y');
            $class->classInfo = $this->searchSessionsInfo($class);

            return $class;
        });

        $classSessions = $classes->map(function ($class) {
            return $class->classSessions;
        });

        $lessons = $classSessions->map(function ($sessions) {
            return $sessions->map(function ($session) {
                return $session->lessons;
            });
        });

        $lecturers = Lecturer::all();
        $buildings = Building::orderBy('name', 'asc')->get();
        $rooms = Room::orderBy('name', 'asc')->get();

        $table_class = view('technician.table-class', compact('classes', 'lecturers', 'buildings', 'rooms'))->render();

        return response()->json([
            'table_class' => $table_class,
            'links' => $classes->render('pagination::bootstrap-5')->toHtml(),
            'class_sessions' => $classSessions,
            'lessons' => $lessons
        ]);
    }

    public function sortStudentClassAPI(Request $request)
    {
        $sortField = $request->input('sortField', 'created_at');
        $sortOrder = $request->input('sortOrder', 'desc');
        $recordsPerPage = $request->input('recordsPerPage', 5);

        $class = CreditClass::where('id', $request->input('classId'))->first();
        if ($sortField == 'full_name') {
            $students = $class->students()->orderByRaw("SUBSTRING_INDEX(full_name, ' ', -1) $sortOrder")->paginate($recordsPerPage);
        } else {
            $students = $class->students()->orderBy($sortField, $sortOrder)->paginate($recordsPerPage);
        }
        $table_student_class = view('technician.table-student-class', compact('students', 'class'))->render();

        return response()->json(['table_student_class' => $table_student_class, 'links' => $students->render('pagination::bootstrap-5')->toHtml()]);
    }

    public function sortReportAPI(Request $request)
    {
        $user = Auth::user();
        $sortField = $request->input('sortField', 'submitted_at');
        $sortOrder = $request->input('sortOrder', 'desc');
        $recordsPerPage = $request->input('recordsPerPage', 5);

        if ($sortField == 'student_lecturer') {
            $reports = Report::where('is_approved', 1)->orderBy('lecturer_id', $sortOrder)->paginate($recordsPerPage);
        } else {
            $reports = Report::where('is_approved', 1)->orderBy($sortField, $sortOrder)->paginate($recordsPerPage);
        }
        $table_report = view('technician.table-report', compact('reports', 'user'))->render();

        return response()->json(['table_report' => $table_report, 'links' => $reports->render('pagination::bootstrap-5')->toHtml()]);
    }

    public function changeRecordsPerPageLecturerAPI(Request $request)
    {
        $sortField = $request->input('sortField', 'updated_at');
        $sortOrder = $request->input('sortOrder', 'desc');
        $recordsPerPage = $request->input('recordsPerPage', 5);

        if ($sortField == 'full_name') {
            $lecturers = Lecturer::orderByRaw("SUBSTRING_INDEX(full_name, ' ', -1) $sortOrder")->paginate($recordsPerPage);
        } else {
            $lecturers = Lecturer::orderBy($sortField, $sortOrder)->paginate($recordsPerPage);
        }

        $table_lecturer = view('technician.table-lecturer', compact('lecturers'))->render();

        return response()->json(['table_lecturer' => $table_lecturer, 'links' => $lecturers->render('pagination::bootstrap-5')->toHtml()]);
    }

    public function changeRecordsPerPageStudentAPI(Request $request)
    {
        $sortField = $request->input('sortField', 'updated_at');
        $sortOrder = $request->input('sortOrder', 'desc');
        $recordsPerPage = $request->input('recordsPerPage', 5);

        if ($sortField == 'full_name') {
            $students = Student::orderByRaw("SUBSTRING_INDEX(full_name, ' ', -1) $sortOrder")->paginate($recordsPerPage);
        } else {
            $students = Student::orderBy($sortField, $sortOrder)->paginate($recordsPerPage);
        }

        $table_students = view('technician.table-student', compact('students'))->render();

        return response()->json(['table_student' => $table_students, 'links' => $students->render('pagination::bootstrap-5')->toHtml()]);
    }

    public function changeRecordsPerPageClassAPI(Request $request)
    {
        $sortField = $request->input('sortField', 'updated_at');
        $sortOrder = $request->input('sortOrder', 'desc');
        $recordsPerPage = $request->input('recordsPerPage', 5);

        if ($sortField == 'lecturer') {
            $classes = CreditClass::withCount('classSessions')
                ->join('lecturers', 'classes.lecturer_id', '=', 'lecturers.id')
                ->orderByRaw("SUBSTRING_INDEX(lecturers.full_name, ' ', -1) $sortOrder")
                ->paginate($recordsPerPage);
        } else {
            $classes = CreditClass::withCount('classSessions')->orderBy($sortField, $sortOrder)->paginate($recordsPerPage);
        }

        $classes->transform(function ($class) {
            $class->start_date = Carbon::parse($class->start_date)->format('d-m-Y');
            $class->end_date = Carbon::parse($class->end_date)->format('d-m-Y');
            $class->classInfo = $this->searchSessionsInfo($class);

            return $class;
        });

        $lecturers = Lecturer::all();
        $buildings = Building::orderBy('name', 'asc')->get();
        $rooms = Room::orderBy('name', 'asc')->get();
        $classSessions = $classes->map(function ($class) {
            return $class->classSessions;
        });
        $lessons = $classSessions->map(function ($sessions) {
            return $sessions->map(function ($session) {
                return $session->lessons;
            });
        });

        $table_class = view('technician.table-class', compact('classes', 'lecturers', 'buildings', 'rooms'))->render();

        return response()->json([
            'table_class' => $table_class,
            'links' => $classes->render('pagination::bootstrap-5')->toHtml(),
            'class_sessions' => $classSessions,
            'lessons' => $lessons,
        ]);
    }

    public function changeRecordsPerPageStudentClassAPI(Request $request)
    {
        $sortField = $request->input('sortField', 'updated_at');
        $sortOrder = $request->input('sortOrder', 'desc');
        $recordsPerPage = $request->input('recordsPerPage', 5);

        $class = CreditClass::where('id', $request->input('classId'))->first();
        if ($sortField == 'full_name') {
            $students = $class->students()->orderByRaw("SUBSTRING_INDEX(full_name, ' ', -1) $sortOrder")->paginate($recordsPerPage);
        } else {
            $students = $class->students()->orderBy($sortField, $sortOrder)->paginate($recordsPerPage);
        }
        $table_student_class = view('technician.table-student-class', compact('students', 'class'))->render();

        return response()->json(['table_student_class' => $table_student_class, 'links' => $students->render('pagination::bootstrap-5')->toHtml()]);
    }

    public function changeRecordsPerPageReportAPI(Request $request)
    {
        $sortField = $request->input('sortField', 'created_at');
        $sortOrder = $request->input('sortOrder', 'desc');
        $recordsPerPage = $request->input('recordsPerPage', 5);

        $user = Auth::user();

        $reports = Report::where('is_approved', 1)->orderBy($sortField, $sortOrder)->paginate($recordsPerPage);
        $table_report = view('technician.table-report', compact('reports', 'user'))->render();

        return response()->json(['table_report' => $table_report, 'links' => $reports->render('pagination::bootstrap-5')->toHtml()]);
    }

    public function searchLecturerAPI(Request $request)
    {
        $sortField = $request->input('sortField', 'lecturers.updated_at');
        $sortOrder = $request->input('sortOrder', 'desc');
        $recordsPerPage = $request->input('recordsPerPage', 5);
        $query = $request->input('query');

        if ($sortField == 'full_name') {
            $lecturers = Lecturer::join('users', 'lecturers.user_id', '=', 'users.id')
                ->where('full_name', 'LIKE', "%{$query}%")
                ->orWhere('faculty', 'LIKE', "%{$query}%")
                ->orWhere('email', 'LIKE', "%{$query}%")
                ->orWhere('phone', 'LIKE', "%{$query}%")
                ->orderByRaw("SUBSTRING_INDEX(full_name, ' ', -1) $sortOrder")
                ->paginate($recordsPerPage);
        } else {
            $lecturers = Lecturer::join('users', 'lecturers.user_id', '=', 'users.id')
                ->where('full_name', 'LIKE', "%{$query}%")
                ->orWhere('faculty', 'LIKE', "%{$query}%")
                ->orWhere('email', 'LIKE', "%{$query}%")
                ->orWhere('phone', 'LIKE', "%{$query}%")
                ->orderBy($sortField, $sortOrder)
                ->paginate($recordsPerPage);
        }

        $table_lecturer = view('technician.table-lecturer', compact('lecturers'))->render();

        return response()->json(['table_lecturer' => $table_lecturer, 'links' => $lecturers->render('pagination::bootstrap-5')->toHtml()]);
    }

    public function searchStudentAPI(Request $request)
    {
        $sortField = $request->input('sortField', 'students.updated_at');
        $sortOrder = $request->input('sortOrder', 'desc');
        $recordsPerPage = $request->input('recordsPerPage', 5);
        $query = $request->input('query');

        if ($sortField == 'full_name') {
            $students = Student::join('users', 'students.user_id', '=', 'users.id')
                ->where('full_name', 'LIKE', "%{$query}%")
                ->orWhere('student_code', 'LIKE', "%{$query}%")
                ->orWhere('class', 'LIKE', "%{$query}%")
                ->orWhere('email', 'LIKE', "%{$query}%")
                ->orWhere('phone', 'LIKE', "%{$query}%")
                ->orderByRaw("SUBSTRING_INDEX(full_name, ' ', -1) $sortOrder")
                ->paginate($recordsPerPage);
        } else {
            $students = Student::join('users', 'students.user_id', '=', 'users.id')
                ->where('full_name', 'LIKE', "%{$query}%")
                ->orWhere('student_code', 'LIKE', "%{$query}%")
                ->orWhere('class', 'LIKE', "%{$query}%")
                ->orWhere('email', 'LIKE', "%{$query}%")
                ->orWhere('phone', 'LIKE', "%{$query}%")
                ->orderBy($sortField, $sortOrder)
                ->paginate($recordsPerPage);
        }

        $table_student = view('technician.table-student', compact('students'))->render();

        return response()->json(['table_student' => $table_student, 'links' => $students->render('pagination::bootstrap-5')->toHtml()]);
    }

    public function searchClassAPI(Request $request)
    {
        $sortField = $request->input('sortField', 'classes.updated_at');
        $sortOrder = $request->input('sortOrder', 'desc');
        $recordsPerPage = $request->input('recordsPerPage', 5);
        $query = $request->input('query');

        if ($sortField == 'lecturer') {
            $classes = CreditClass::withCount('classSessions')
                ->join('lecturers', 'classes.lecturer_id', '=', 'lecturers.id')
                ->where('classes.name', 'LIKE', "%{$query}%")
                ->orWhere('lecturers.full_name', 'LIKE', "%{$query}%")
                ->orderByRaw("SUBSTRING_INDEX(lecturers.full_name, ' ', -1) $sortOrder")
                ->paginate($recordsPerPage);
        } else {
            $classes = CreditClass::withCount('classSessions')
                ->join('lecturers', 'classes.lecturer_id', '=', 'lecturers.id')
                ->where('classes.name', 'LIKE', "%{$query}%")
                ->orWhere('lecturers.full_name', 'LIKE', "%{$query}%")
                ->orderBy($sortField, $sortOrder)
                ->paginate($recordsPerPage);
        }

        $classes->transform(function ($class) {
            $class->start_date = Carbon::parse($class->start_date)->format('d-m-Y');
            $class->end_date = Carbon::parse($class->end_date)->format('d-m-Y');
            $class->classInfo = $this->searchSessionsInfo($class);

            return $class;
        });

        $lecturers = Lecturer::all();
        $buildings = Building::orderBy('name', 'asc')->get();
        $rooms = Room::orderBy('name', 'asc')->get();
        $classSessions = $classes->map(function ($class) {
            return $class->classSessions;
        });
        $lessons = $classSessions->map(function ($sessions) {
            return $sessions->map(function ($session) {
                return $session->lessons;
            });
        });

        $table_class = view('technician.table-class', compact('classes', 'lecturers', 'buildings', 'rooms'))->render();

        return response()->json([
            'table_class' => $table_class,
            'links' => $classes->render('pagination::bootstrap-5')->toHtml(),
            'class_sessions' => $classSessions,
            'lessons' => $lessons,
        ]);
    }

    public function searchStudentClassAPI(Request $request)
    {
        $sortField = $request->input('sortField', 'students.updated_at');
        $sortOrder = $request->input('sortOrder', 'desc');
        $recordsPerPage = $request->input('recordsPerPage', 5);
        $query = $request->input('query');

        $class = CreditClass::where('id', $request->input('classId'))->first();
        if ($sortField == 'full_name') {
            $students = Student::select('students.*')
                ->join('users', 'students.user_id', '=', 'users.id')
                ->join('class_student', 'students.id', '=', 'class_student.student_id')
                ->where('class_id', $class->id)
                ->where('full_name', 'LIKE', "%{$query}%")
                ->orWhere('student_code', 'LIKE', "%{$query}%")
                ->orWhere('class', 'LIKE', "%{$query}%")
                ->orWhere('email', 'LIKE', "%{$query}%")
                ->orWhere('phone', 'LIKE', "%{$query}%")
                ->groupBy('students.id')
                ->orderByRaw("SUBSTRING_INDEX(full_name, ' ', -1) $sortOrder")
                ->paginate($recordsPerPage);
        } else {
            $students = Student::select('students.*')
                ->join('users', 'students.user_id', '=', 'users.id')
                ->join('class_student', 'students.id', '=', 'class_student.student_id')
                ->where('class_id', $class->id)
                ->where('full_name', 'LIKE', "%{$query}%")
                ->orWhere('student_code', 'LIKE', "%{$query}%")
                ->orWhere('class', 'LIKE', "%{$query}%")
                ->orWhere('email', 'LIKE', "%{$query}%")
                ->orWhere('phone', 'LIKE', "%{$query}%")
                ->groupBy('students.id')
                ->orderBy($sortField, $sortOrder)
                ->paginate($recordsPerPage);
        }

        $table_student_class = view('technician.table-student-class', compact('students', 'class'))->render();

        return response()->json(['table_student_class' => $table_student_class, 'links' => $students->render('pagination::bootstrap-5')->toHtml()]);
    }
}
