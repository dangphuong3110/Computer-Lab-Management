<?php

namespace App\Http\Controllers;

use App\Imports\LecturersImport;
use App\Imports\StudentsImport;
use App\Models\Building;
use App\Models\ClassSession;
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

        if (!str_contains($request->input('email'), '@tlu.edu.vn')) {
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
            'student-code' => 'unique:students,student_code',
            'email' => 'required|email|max:255|unique:users,email',
        ], [
            'full-name.required' => 'Vui lòng nhập họ và tên!',
            'full-name.max' => 'Họ và tên không được vượt quá 255 ký tự!',
            'student-code.unique' => 'Mã sinh viên đã tồn tại!',
            'email.required' => 'Vui lòng nhập địa chỉ email!',
            'email.email' => 'Địa chỉ email không hợp lệ!',
            'email.unique' => 'Địa chỉ email đã được sử dụng!',
            'email.max' => 'Địa chỉ email không được vượt quá 255 ký tự!',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        if (!str_contains($request->input('email'), '@e.tlu.edu.vn')) {
            return response()->json(['errors' => ['email' => 'Email phải là email sinh viên của nhà trường!']]);
        }

        $user = new User();

        $user->email = $request->input('email');
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
                                    ($endLessonId >= $range[0] && $endLessonId <= $range[1]) ||
                                    ($endLessonId <= $range[0] && $startLessonId >= $range[1]))) {
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

        if (!str_contains($request->input('email'), '@tlu.edu.vn')) {
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

    public function updateStudentAPI(Request $request, string $id)
    {
        $student = Student::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'full-name' => 'required|max:255',
            'student-code' => 'unique:students,student_code,' . $student->id,
            'email' => 'required|email|max:255|unique:users,email' . $student->user_id,
        ], [
            'full-name.required' => 'Vui lòng nhập họ và tên!',
            'full-name.max' => 'Họ và tên không được vượt quá 255 ký tự!',
            'student-code.unique' => 'Mã sinh viên đã tồn tại!',
            'email.required' => 'Vui lòng nhập địa chỉ email!',
            'email.email' => 'Địa chỉ email không hợp lệ!',
            'email.unique' => 'Địa chỉ email đã được sử dụng!',
            'email.max' => 'Địa chỉ email không được vượt quá 255 ký tự!',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        if (!str_contains($request->input('email'), '@e.tlu.edu.vn')) {
            return response()->json(['errors' => ['email' => 'Email phải là email sinh viên của nhà trường!']]);
        }

        $user = User::findOrFail($student->user_id);
        $user->email = $request->input('email');
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

    public function destroyLecturerAPI(string $id)
    {
        $lecturer = Lecturer::findOrFail($id);
        $userId = $lecturer->user_id;

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
        $classSessions = ClassSession::where('class_id', $id)->get();
        foreach ($classSessions as $classSession) {
            $classSession->lessons()->detach();
            $classSession->delete();
        }

        $creditClass = CreditClass::findOrFail($id);

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
}
