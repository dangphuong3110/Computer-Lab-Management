<?php

namespace App\Http\Controllers;

use App\Imports\LecturersImport;
use App\Imports\StudentsImport;
use App\Models\Lecturer;
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
    /**
     * Display a listing of the resource.
     */
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

    public function storeLecturerAPI(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'full-name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
        ], [
            'full-name.required' => 'Vui lòng nhập họ và tên',
            'full-name.string' => 'Họ và tên phải là chuỗi',
            'full-name.max' => 'Họ và tên không được vượt quá 255 ký tự',
            'email.required' => 'Vui lòng nhập địa chỉ email',
            'email.email' => 'Địa chỉ email không hợp lệ',
            'email.unique' => 'Địa chỉ email đã được sử dụng',
            'email.max' => 'Địa chỉ email không được vượt quá 255 ký tự',
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

        $lecturers = Lecturer::orderBy('updated_at', 'desc')->paginate(7);
        $table_lecturer = view('technician.table-lecturer', compact('lecturers'))->render();

        return response()->json(['success' => 'Thêm giảng viên thành công!', 'table_lecturer' => $table_lecturer, 'links' => $lecturers->render('pagination::bootstrap-5')->toHtml()]);
    }

    public function storeStudentAPI(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'full-name' => 'required|string|max:255',
            'student-code' => 'required|string|max:255|unique:students,student_code',
        ], [
            'full-name.required' => 'Vui lòng nhập họ và tên',
            'full-name.string' => 'Họ và tên phải là chuỗi',
            'full-name.max' => 'Họ và tên không được vượt quá 255 ký tự',
            'student-code.required' => 'Vui lòng nhập mã sinh viên',
            'student-code.string' => 'Mã sinh viên không phải là chuỗi',
            'student-code.unique' => 'Mã sinh viên đã tồn tại',
            'student-code.max' => 'Mã sinh viên không được vượt quá 255 ký tự',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        $user = new User();

        $user->email = $request->input('student-code');
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

    public function updateLecturerAPI(Request $request, string $id)
    {
        $lecturer = Lecturer::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'full-name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $lecturer->user_id,
        ], [
            'full-name.required' => 'Vui lòng nhập họ và tên',
            'full-name.string' => 'Họ và tên phải là chuỗi',
            'full-name.max' => 'Họ và tên không được vượt quá 255 ký tự',
            'email.required' => 'Vui lòng nhập địa chỉ email',
            'email.email' => 'Địa chỉ email không hợp lệ',
            'email.unique' => 'Địa chỉ email đã được sử dụng',
            'email.max' => 'Địa chỉ email không được vượt quá 255 ký tự',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
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
            'full-name' => 'required|string|max:255',
            'student-code' => 'required|string|max:255|unique:students,student_code,' . $student->id,
        ], [
            'full-name.required' => 'Vui lòng nhập họ và tên',
            'full-name.string' => 'Họ và tên phải là chuỗi',
            'full-name.max' => 'Họ và tên không được vượt quá 255 ký tự',
            'student-code.required' => 'Vui lòng nhập mã sinh viên',
            'student-code.string' => 'Mã sinh viên không phải là chuỗi',
            'student-code.unique' => 'Mã sinh viên đã tồn tại',
            'student-code.max' => 'Mã sinh viên không được vượt quá 255 ký tự',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        $user = User::findOrFail($student->user_id);
        $user->email = $request->input('student-code');
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

    public function importLecturerAPI(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lecturer-file' => 'required|mimes:xlsx,xls',
        ], [
            'lecturer-file.required' => 'Vui lòng nhập file',
            'lecturer-file.mimes' => 'Vui lòng nhập đúng định dạng file excel (.xlsx, .xls)',
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
            'student-file.required' => 'Vui lòng nhập file',
            'student-file.mimes' => 'Vui lòng nhập đúng định dạng file excel (.xlsx, .xls)',
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
