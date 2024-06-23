<?php

namespace App\Http\Controllers;

use App\Imports\LecturersImport;
use App\Models\Lecturer;
use App\Models\User;
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

        $lecturers = Lecturer::orderBy('updated_at', 'desc')->paginate(10);

        return view('technician.list-lecturer', compact('title','user', 'lecturers'));
    }

    public function getListLecturerAPI()
    {
        $lecturers = Lecturer::orderBy('updated_at', 'desc')->paginate(10);

//        return view('technician.test', compact('lecturers'));
        response()->json(['lecturers' => $lecturers]);
    }

    public function createLecturer() {
        $title = 'Thêm giảng viên';
        $user = Auth::user();

        return view('technician.form_lecturer', compact('title','user'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

    }

    public function storeLecturer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'full-name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
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
            return redirect()->route('technician.create-lecturer')->withErrors($validator)->withInput();
        }

        $user = new User();

        $user->email = $request->input('email');
        $user->password = Hash::make('123456');
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

        return redirect()->route('technician.list-lecturer')->with('success', 'Thêm giảng viên thành công!');
    }

    public function storeLecturerAPI(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'full-name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
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

        $lecturers = Lecturer::orderBy('updated_at', 'desc')->paginate(10);
        $table_lecturer = view('technician.table-lecturer', compact('lecturers'))->render();

        return response()->json(['success' => 'Thêm giảng viên thành công!', 'table_lecturer' => $table_lecturer, 'links' => $lecturers->render('pagination::bootstrap-5')->toHtml()]);
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

    public function editLecturer(string $id)
    {
        $title = 'Chỉnh sửa giảng viên';
        $user = Auth::user();

        $lecturer = Lecturer::findOrFail($id);

        return view('technician.form_lecturer', compact('title','user', 'lecturer'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    public function updateLecturer(Request $request, string $id)
    {
        $lecturer = Lecturer::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'full-name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $lecturer->user_id,
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
            return redirect()->route('technician.edit-lecturer', $lecturer->id)->withErrors($validator)->withInput();
        }

        $lecturer->full_name = $request->input('full-name');
        $lecturer->academic_rank = $request->input('academic-rank');
        $lecturer->department = $request->input('department');
        $lecturer->faculty = $request->input('faculty');
        $lecturer->position = $request->input('position');

        $lecturer->save();

        $user = User::findOrFail($lecturer->user_id);
        $user->email = $request->input('email');

        $user->save();

        return redirect()->route('technician.edit-lecturer', $lecturer->id)->with('success', 'Chỉnh sửa thông tin giảng viên thành công!');
    }

    public function updateLecturerAPI(Request $request, string $id)
    {
        $lecturer = Lecturer::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'full-name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $lecturer->user_id,
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

        $user->save();

        $lecturer->full_name = $request->input('full-name');
        $lecturer->academic_rank = $request->input('academic-rank');
        $lecturer->department = $request->input('department');
        $lecturer->faculty = $request->input('faculty');
        $lecturer->position = $request->input('position');

        $lecturer->save();

        $lecturers = Lecturer::orderBy('updated_at', 'desc')->paginate(10);
        $table_lecturer = view('technician.table-lecturer', compact('lecturers'))->render();

        return response()->json(['success' => 'Chỉnh sửa thông tin giảng viên thành công!', 'table_lecturer' => $table_lecturer, 'links' => $lecturers->render('pagination::bootstrap-5')->toHtml()]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    public function destroyLecturer(string $id)
    {
        $lecturer = Lecturer::findOrFail($id);
        $userId = $lecturer->user_id;

        $lecturer->delete();

        $user = User::findOrFail($userId);
        $user->delete();

        return redirect()->route('technician.list-lecturer')->with('success', 'Xóa giảng viên thành công!');
    }

    public function destroyLecturerAPI(string $id)
    {
        $lecturer = Lecturer::findOrFail($id);
        $userId = $lecturer->user_id;

        $lecturer->delete();

        $user = User::findOrFail($userId);
        $user->delete();

        $lecturers = Lecturer::orderBy('updated_at', 'desc')->paginate(10);
        $table_lecturer = view('technician.table-lecturer', compact('lecturers'))->render();

        return response()->json(['success' => 'Xóa giảng viên thành công!', 'table_lecturer' => $table_lecturer, 'links' => $lecturers->render('pagination::bootstrap-5')->toHtml()]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function importLecturerAPI(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lecturer-file' => 'required|mimes:xlsx,xls',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        $file = $request->file('lecturer-file');
        $import = new LecturersImport();
        Excel::import($import, $file);

        if ($import->failures()->isNotEmpty()) {
            return response()->json(['errors' => $import->failures()]);
        }

        return response()->json(['success' => 'Nhập file thành công!']);
    }
}
