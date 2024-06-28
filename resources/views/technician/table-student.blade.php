@if(count($students) > 0)
    @foreach($students as $index => $student)
        <tr>
            <th scope="row" class="text-center">{{ $students->firstItem() + $index }}</th>
            <td class="text-center">{{ $student->full_name }}</td>
            <td class="text-center">{{ $student->student_code }}</td>
            <td class="text-center">{{ $student->class }}</td>
            <td class="text-center">{{ $student->user->email }}</td>
            <td class="text-center">{{ $student->user->phone }}</td>
            <td class="text-center">
                <a href="#" class="btn btn-sm btn-primary my-auto" data-bs-toggle="modal" data-bs-target="#update-student-modal-{{ $student->id }}"><i class='bx bx-pencil'></i></a>
                <!----- Modal sửa sinh viên ----->
                <div class="modal fade modal-update" id="update-student-modal-{{ $student->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="addStudentModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="exampleModalLabel">Chỉnh sửa thông tin giảng viên</h1>
                            </div>
                            <div class="modal-body">
                                <form method="post" action="{{ route('technician.update-student-api', $student->id) }}" id="update-student-form-{{ $student->id }}">
                                    @csrf
                                    @method('PUT')
                                    <div class="row mb-3 mt-4">
                                        <label class="col-md-4 col-label-form fs-6 fw-bold text-md-end">Họ và tên<span class="required">*</span></label>
                                        <div class="col-md-7">
                                            <input type="text" name="full-name" class="form-control fs-6" value="{{ $student->full_name }}" data-initial-value="{{ $student->full_name }}"/>
                                        </div>
                                    </div>
{{--                                    <div class="row mb-3 mt-4">--}}
{{--                                        <label class="col-md-4 col-label-form fs-6 fw-bold text-md-end">Email<span class="required">*</span></label>--}}
{{--                                        <div class="col-md-7">--}}
{{--                                            <input type="text" name="email" class="form-control fs-6" value="{{ $student->user->email }}" data-initial-value="{{ $student->user->email }}"/>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
                                    <div class="row mb-3 mt-4">
                                        <label class="col-md-4 col-label-form fs-6 fw-bold text-md-end">Mã sinh viên<span class="required">*</span></label>
                                        <div class="col-md-7">
                                            <input type="text" name="student-code" class="form-control fs-6" value="{{ $student->student_code }}" data-initial-value="{{ $student->student_code }}"/>
                                        </div>
                                    </div>
                                    <div class="row mb-3 mt-4">
                                        <label class="col-md-4 col-label-form fs-6 fw-bold text-md-end">Số điện thoại</label>
                                        <div class="col-md-7">
                                            <input type="text" name="phone" class="form-control fs-6" value="{{ $student->user->phone }}" data-initial-value="{{ $student->user->phone }}"/>
                                        </div>
                                    </div>
                                    <div class="row mb-3 mt-4">
                                        <label class="col-md-4 col-label-form fs-6 fw-bold text-md-end">Lớp</label>
                                        <div class="col-md-7">
                                            <input type="text" name="class" class="form-control fs-6" value="{{ $student->class }}" data-initial-value="{{ $student->class }}"/>
                                        </div>
                                    </div>
                                    <div class="row mb-3 mt-4">
                                        <label class="col-md-4 col-label-form fs-6 fw-bold text-md-end">Giới tính</label>
                                        <div class="col-md-7">
                                            <select name="gender" class="form-select form-control fs-6" data-initial-value="{{ $student->gender }}">
                                                <option value="Nam" {{ $student->gender == 'Nam' ? 'selected' : '' }}>Nam</option>
                                                <option value="Nữ" {{ $student->gender == 'Nữ' ? 'selected' : '' }}>Nữ</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row mb-3 mt-4">
                                        <label class="col-md-4 col-label-form fs-6 fw-bold text-md-end">Ngày sinh</label>
                                        <div class="col-md-7">
                                            <input type="date" name="date-of-birth" class="form-control fs-6" value="{{ $student->date_of_birth }}" data-initial-value="{{ $student->date_of_birth }}"/>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary close-update-btn" data-student-id="{{ $student->id }}" data-bs-dismiss="modal">Đóng</button>
                                <button type="button" class="btn btn-primary btn-update-student" data-student-id="{{ $student->id }}">Lưu</button>
                            </div>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#destroy-student-modal-{{ $student->id }}"><i class='bx bx-trash'></i></button>
                <form method="post" action="{{ route('technician.destroy-student-api', $student->id) }}" id="destroy-student-form-{{ $student->id }}">
                    @csrf
                    @method('DELETE')
                    <!----- Modal xóa sinh viên ----->
                    <div class="modal fade" id="destroy-student-modal-{{ $student->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="destroyStudentLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="exampleModalLabel">Xóa sinh viên</h1>
                                </div>
                                <div class="modal-body">
                                    <p class="text-wrap m-0">Bạn có chắc chắn muốn xóa sinh viên?<br> Điều này sẽ dẫn đến tài khoản của sinh viên cũng sẽ không còn tồn tại trong hệ thống.</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Trở về</button>
                                    <button type="submit" class="btn btn-danger btn-destroy-student" data-student-id="{{ $student->id }}">Xóa</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </td>
        </tr>
    @endforeach
@else
    <tr>
        <td colspan="7" class="text-center">No Data Found</td>
    </tr>
@endif
