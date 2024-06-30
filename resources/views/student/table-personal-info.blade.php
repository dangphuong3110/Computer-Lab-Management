<div class="col-md-6 mb-3">
    <label class="col-label-form fs-6 fw-bold">Họ và tên</label>
    <div>
        <input type="text" name="full-name" class="form-control fs-6" value="{{ $user->student->full_name ?? '' }}" disabled/>
    </div>
</div>
<div class="col-md-6 mb-3">
    <label class="col-label-form fs-6 fw-bold">Mã sinh viên</label>
    <div>
        <input type="text" name="student-code" class="form-control fs-6" value="{{ $user->student->student_code ?? '' }}" disabled/>
    </div>
</div>
<div class="col-md-6 mb-3">
    <label class="col-label-form fs-6 fw-bold">Lớp</label>
    <div>
        <input type="text" name="class" class="form-control fs-6" value="{{ $user->student->class ?? '' }}" disabled/>
    </div>
</div>
<div class="col-md-6 mb-3">
    <label class="col-label-form fs-6 fw-bold">Ngày sinh</label>
    <div>
        <input type="date" name="date-of-birth" class="form-control fs-6" value="{{ $user->student->date_of_birth ?? '' }}" disabled/>
    </div>
</div>
<div class="col-md-6 mb-3">
    <label class="col-label-form fs-6 fw-bold">Email</label>
    <div>
        <input type="text" name="email" class="form-control fs-6" value="{{ $user->email ?? '' }}" disabled/>
    </div>
</div>
<div class="col-md-6 mb-3">
    <label class="col-label-form fs-6 fw-bold">Số điện thoại</label>
    <div>
        <input type="text" name="phone" class="form-control fs-6" value="{{ $user->phone ?? '' }}" disabled/>
    </div>
</div>
<!----- Modal thông tin cá nhân sinh viên ----->
<div class="modal fade modal-update" id="update-personal-info-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="updatePersonalInfoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Chỉnh sửa thông tin cá nhân</h1>
            </div>
            <div class="modal-body">
                <form method="post" action="{{ route('student.update-personal-info-api', $user->student->id) }}" id="update-personal-info-form">
                    @csrf
                    @method('PUT')
                    <div class="row mb-3 mt-4">
                        <label class="col-md-4 col-label-form fs-6 fw-bold text-md-end">Họ và tên<span class="required">*</span></label>
                        <div class="col-md-7">
                            <input type="text" name="full-name" class="form-control fs-6" value="{{ $user->student->full_name }}" data-initial-value="{{ $user->student->full_name }}"/>
                        </div>
                    </div>
                    <div class="row mb-3 mt-4">
                        <label class="col-md-4 col-label-form fs-6 fw-bold text-md-end">Mã sinh viên<span class="required">*</span></label>
                        <div class="col-md-7">
                            <input type="text" name="student-code" class="form-control fs-6" value="{{ $user->student->student_code }}" data-initial-value="{{ $user->student->student_code }}" disabled/>
                        </div>
                    </div>
                    <div class="row mb-3 mt-4">
                        <label class="col-md-4 col-label-form fs-6 fw-bold text-md-end">Lớp</label>
                        <div class="col-md-7">
                            <input type="text" name="class" class="form-control fs-6" value="{{ $user->student->class }}" data-initial-value="{{ $user->student->class }}"/>
                        </div>
                    </div>
                    <div class="row mb-3 mt-4">
                        <label class="col-md-4 col-label-form fs-6 fw-bold text-md-end">Ngày sinh</label>
                        <div class="col-md-7">
                            <input type="date" name="date-of-birth" class="form-control fs-6" value="{{ $user->student->date_of_birth }}" data-initial-value="{{ $user->student->date_of_birth }}"/>
                        </div>
                    </div>
                    <div class="row mb-3 mt-4">
                        <label class="col-md-4 col-label-form fs-6 fw-bold text-md-end">Email</label>
                        <div class="col-md-7">
                            <input type="text" name="email" class="form-control fs-6" value="{{ $user->email}}" data-initial-value="{{ $user->email }}" disabled/>
                        </div>
                    </div>
                    <div class="row mb-3 mt-4">
                        <label class="col-md-4 col-label-form fs-6 fw-bold text-md-end">Số điện thoại</label>
                        <div class="col-md-7">
                            <input type="text" name="phone" class="form-control fs-6" value="{{ $user->phone }}" data-initial-value="{{ $user->phone }}"/>
                        </div>
                    </div>
                    <div class="row mb-3 mt-4">
                        <label class="col-md-4 col-label-form fs-6 fw-bold text-md-end">Giới tính</label>
                        <div class="col-md-7">
                            <select name="gender" class="form-select form-control fs-6" data-initial-value="{{ $user->student->gender }}">
                                <option value="Nam" {{ $user->student->gender == 'Nam' ? 'selected' : '' }}>Nam</option>
                                <option value="Nữ" {{ $user->student->gender == 'Nữ' ? 'selected' : '' }}>Nữ</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary close-update-btn" data-student-id="{{ $user->student->id }}" data-bs-dismiss="modal">Đóng</button>
                <button type="button" id="btn-update-personal-info" class="btn btn-primary" data-student-id="{{ $user->student->id }}">Lưu</button>
            </div>
        </div>
    </div>
</div>
