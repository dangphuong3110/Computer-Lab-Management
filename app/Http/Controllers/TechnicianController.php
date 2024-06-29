<?php

namespace App\Http\Controllers;

use App\Imports\LecturersImport;
use App\Imports\StudentClassImport;
use App\Imports\StudentsImport;
use App\Models\Building;
use App\Models\ClassSession;
use App\Models\Computer;
use App\Models\CreditClass;
use App\Models\Lecturer;
use App\Models\Lesson;
use App\Models\Room;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
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

    public function getListLecturer()
    {
        $title = 'Giảng viên';
        $user = Auth::user();

        $lecturers = Lecturer::orderBy('updated_at', 'desc')->paginate(7);

        return view('technician.list-lecturer', compact('title','user', 'lecturers'));
    }

    public function getListStudent()
    {
        $title = 'Sinh viên';
        $user = Auth::user();

        $students = Student::orderBy('updated_at', 'desc')->paginate(7);

        return view('technician.list-student', compact('title','user', 'students'));
    }

    public function getListClass() {
        $title = 'Lớp học phần';
        $user = Auth::user();

        $classes = CreditClass::withCount('classSessions')->orderBy('updated_at', 'desc')->paginate(7);

        $classes->transform(function ($class) {
            $class->start_date = Carbon::parse($class->start_date)->format('d-m-Y');
            $class->end_date = Carbon::parse($class->end_date)->format('d-m-Y');

            return $class;
        });

        $lecturers = Lecturer::all();
        $buildings = Building::all();
        $rooms = Room::all();
        $classSessions = $classes->map(function ($class) {
            return $class->classSessions;
        });
        $lessons = $classSessions->map(function ($sessions) {
            return $sessions->map(function ($session) {
                return $session->lessons;
            });
        });

        return view('technician.list-class', compact('title','user', 'classes', 'lecturers', 'buildings', 'rooms', 'classSessions', 'lessons'));
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

        $buildings = Building::orderBy('updated_at', 'desc')->get();

        return view('technician.list-building', compact('title','user', 'buildings'));
    }

    public function getListRoom(string $building_id) {
        $title = 'Phòng máy';
        $user = Auth::user();

        $rooms = Room::where('building_id', $building_id)->orderBy('updated_at', 'desc')->get();

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

    public function getListStudentClass(string $class_id)
    {
        $title = 'Sinh viên lớp học phần';
        $user = Auth::user();

        $class = CreditClass::where('id', $class_id)->first();
        $students = $class->students()->orderBy('class_student.created_at', 'desc')->paginate(7);

        return view('technician.list-student-class', compact('title','user', 'students', 'class'));
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

        if (!Str::endsWith($request->input('email'), '@tlu.edu.vn')) {
            return response()->json(['errors' => ['email' => 'Email phải là email giảng viên của nhà trường!']]);
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

        $lecturers = Lecturer::orderBy('updated_at', 'desc')->paginate(7);
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

        $students = Student::orderBy('updated_at', 'desc')->paginate(7);
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

        $classes = CreditClass::withCount('classSessions')->orderBy('updated_at', 'desc')->paginate(7);

        $classes->transform(function ($class) {
            $class->start_date = Carbon::parse($class->start_date)->format('d-m-Y');
            $class->end_date = Carbon::parse($class->end_date)->format('d-m-Y');

            return $class;
        });

        $lecturers = Lecturer::all();
        $buildings = Building::all();
        $rooms = Room::all();
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
            'success' => 'Thêm lớp học phần thành công!',
            'table_class' => $table_class,
            'links' => $classes->render('pagination::bootstrap-5')->toHtml(),
            'class_sessions' => $classSessions,
            'lessons' => $lessons
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
            $students = $class->students()->orderBy('class_student.created_at', 'desc')->paginate(7);
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

        $buildings = Building::orderBy('updated_at', 'desc')->get();
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

        $rooms = Room::where('building_id', $building_id)->orderBy('updated_at', 'desc')->get();
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

        if (!Str::endsWith($request->input('email'), '@tlu.edu.vn')) {
            return response()->json(['errors' => ['email' => 'Email phải là email giảng viên của nhà trường!']]);
        }

        $user = User::findOrFail($lecturer->user_id);
        $user->email = $request->input('email');
        $user->phone = $request->input('phone');

        $user->save();

        $lecturer->full_name = $request->input('full-name');
        $lecturer->academic_rank = $request->input('academic-rank');
        $lecturer->department = $request->input('department');
        $lecturer->faculty = $request->input('faculty');
        $lecturer->position = $request->input('position');

        $lecturer->save();

        $lecturers = Lecturer::orderBy('updated_at', 'desc')->paginate(7);
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
        $user->email = trim($request->input('student-code')) . '@e.tlu.edu.vn';
        $user->phone = $request->input('phone');

        $user->save();

        $student->full_name = $request->input('full-name');
        $student->student_code = $request->input('student-code');
        $student->class = $request->input('class');
        $student->gender = $request->input('gender');
        $student->date_of_birth = $request->input('date-of-birth');

        $student->save();

        $students = Student::orderBy('updated_at', 'desc')->paginate(7);
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

        foreach ($classSessions as $classSession) {
            $classSession->lessons()->detach();
            $classSession->delete();
        }

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

        $classes = CreditClass::withCount('classSessions')->orderBy('updated_at', 'desc')->paginate(7);

        $classes->transform(function ($class) {
            $class->start_date = Carbon::parse($class->start_date)->format('d-m-Y');
            $class->end_date = Carbon::parse($class->end_date)->format('d-m-Y');

            return $class;
        });

        $lecturers = Lecturer::all();
        $buildings = Building::all();
        $rooms = Room::all();
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
            'success' => 'Chỉnh sửa thông tin lớp học phần thành công!',
            'table_class' => $table_class,
            'links' => $classes->render('pagination::bootstrap-5')->toHtml(),
            'class_sessions' => $classSessions,
            'lessons' => $lessons
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

        return response()->json(['success' => $message]);
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

        $buildings = Building::orderBy('updated_at', 'desc')->get();
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

        $rooms = Room::where('building_id', $building_id)->orderBy('updated_at', 'desc')->get();
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

    public function destroyLecturerAPI(string $id)
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

        $lecturers = Lecturer::orderBy('updated_at', 'desc')->paginate(7);
        $table_lecturer = view('technician.table-lecturer', compact('lecturers'))->render();

        return response()->json(['success' => 'Xóa giảng viên thành công!', 'table_lecturer' => $table_lecturer, 'links' => $lecturers->render('pagination::bootstrap-5')->toHtml()]);
    }

    public function destroyStudentAPI(string $id)
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

        $students = Student::orderBy('updated_at', 'desc')->paginate(7);
        $table_student = view('technician.table-student', compact('students'))->render();

        return response()->json(['success' => 'Xóa sinh viên thành công!', 'table_student' => $table_student, 'links' => $students->render('pagination::bootstrap-5')->toHtml()]);
    }

    public function destroyClassAPI(string $id)
    {

        $creditClass = CreditClass::findOrFail($id);

        if (Carbon::now() >= Carbon::parse($creditClass->start_date) && Carbon::now() <= Carbon::parse($creditClass->end_date)) {
            return response()->json(['errors' => ['class' => 'Không thể xóa lớp học phần đang diễn ra!']]);
        }

        $classSessions = ClassSession::where('class_id', $id)->get();
        foreach ($classSessions as $classSession) {
            $classSession->lessons()->detach();
            $classSession->delete();
        }

        $creditClass->delete();

        $classes = CreditClass::withCount('classSessions')->orderBy('updated_at', 'desc')->paginate(7);

        $classes->transform(function ($class) {
            $class->start_date = Carbon::parse($class->start_date)->format('d-m-Y');
            $class->end_date = Carbon::parse($class->end_date)->format('d-m-Y');

            return $class;
        });

        $lecturers = Lecturer::all();
        $buildings = Building::all();
        $rooms = Room::all();
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
            'success' => 'Xóa lớp học phần thành công!',
            'table_class' => $table_class,
            'links' => $classes->render('pagination::bootstrap-5')->toHtml(),
            'class_sessions' => $classSessions,
            'lessons' => $lessons
        ]);
    }

    public function destroyStudentClassAPI(Request $request, string $student_id)
    {
        $student = Student::findOrFail($student_id);

        $student->creditClasses()->detach();

        $class = CreditClass::where('id', $request->input('class_id'))->first();
        $students = $class->students()->orderBy('class_student.created_at', 'desc')->paginate(7);
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

        $buildings = Building::orderBy('updated_at', 'desc')->get();
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

        $rooms = Room::where('building_id', $building_id)->orderBy('updated_at', 'desc')->get();
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

        $lecturers = Lecturer::orderBy('updated_at', 'desc')->paginate(7);
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

        $students = Student::orderBy('updated_at', 'desc')->paginate(7);
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

        $class = CreditClass::where('id', $class_id)->first();
        $students = $class->students()->orderBy('class_student.created_at', 'desc')->paginate(7);
        $table_student_class = view('technician.table-student-class', compact('students', 'class'))->render();

        return response()->json(['success' => 'Nhập file sinh viên vào lớp học phần thành công!', 'table_student_class' => $table_student_class, 'links' => $students->render('pagination::bootstrap-5')->toHtml()]);
    }
}
