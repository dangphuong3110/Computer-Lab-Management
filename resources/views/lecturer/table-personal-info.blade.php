<div class="col-md-6 mb-3">
    <label class="col-label-form fs-6 fw-bold">Họ và tên</label>
    <div>
        <input type="text" name="full-name" class="form-control fs-6" value="{{ $user->lecturer->full_name ?? '' }}" disabled/>
    </div>
</div>
<div class="col-md-6 mb-3">
    <label class="col-label-form fs-6 fw-bold">Học hàm/Học vị</label>
    <div>
        <input type="text" name="academic-rank" class="form-control fs-6" value="{{ $user->lecturer->academic_rank ?? '' }}" disabled/>
    </div>
</div>
<div class="col-md-6 mb-3">
    <label class="col-label-form fs-6 fw-bold">Bộ môn</label>
    <div>
        <input type="text" name="department" class="form-control fs-6" value="{{ $user->lecturer->department ?? '' }}" disabled/>
    </div>
</div>
<div class="col-md-6 mb-3">
    <label class="col-label-form fs-6 fw-bold">Khoa</label>
    <div>
        <input type="text" name="faculty" class="form-control fs-6" value="{{ $user->lecturer->faculty ?? '' }}" disabled/>
    </div>
</div>
<div class="col-md-6 mb-3">
    <label class="col-label-form fs-6 fw-bold">Chức vị</label>
    <div>
        <input type="text" name="position" class="form-control fs-6" value="{{ $user->lecturer->position ?? '' }}" disabled/>
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
<!----- Modal sửa thông tin cá nhân giảng viên ----->
<div class="modal fade modal-update" id="update-personal-info-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="updatePersonalInfoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Chỉnh sửa thông tin cá nhân</h1>
            </div>
            <div class="modal-body">
                <form method="post" action="{{ route('lecturer.update-personal-info-api', $user->lecturer->id) }}" id="update-personal-info-form">
                    @csrf
                    @method('PUT')
                    <div class="row mb-3 mt-4">
                        <label class="col-md-4 col-label-form fs-6 fw-bold text-md-end">Họ và tên<span class="required">*</span></label>
                        <div class="col-md-7">
                            <input type="text" name="full-name" class="form-control fs-6" value="{{ $user->lecturer->full_name }}" data-initial-value="{{ $user->lecturer->full_name }}"/>
                        </div>
                    </div>
                    <div class="row mb-3 mt-4">
                        <label class="col-md-4 col-label-form fs-6 fw-bold text-md-end">Học hàm/Học vị</label>
                        <div class="col-md-7">
                            <input type="text" name="academic-rank" class="form-control fs-6" value="{{ $user->lecturer->academic_rank }}" data-initial-value="{{ $user->lecturer->academic_rank }}"/>
                        </div>
                    </div>
                    <div class="row mb-3 mt-4">
                        <label class="col-md-4 col-label-form fs-6 fw-bold text-md-end">Bộ môn</label>
                        <div class="col-md-7">
                            <input type="text" name="department" class="form-control fs-6" value="{{ $user->lecturer->department }}" data-initial-value="{{ $user->lecturer->department }}"/>
                        </div>
                    </div>
                    <div class="row mb-3 mt-4">
                        <label class="col-md-4 col-label-form fs-6 fw-bold text-md-end">Khoa</label>
                        <div class="col-md-7">
                            <input type="text" name="faculty" class="form-control fs-6" value="{{ $user->lecturer->faculty }}" data-initial-value="{{ $user->lecturer->faculty }}"/>
                        </div>
                    </div>
                    <div class="row mb-3 mt-4">
                        <label class="col-md-4 col-label-form fs-6 fw-bold text-md-end">Chức vị</label>
                        <div class="col-md-7">
                            <input type="text" name="position" class="form-control fs-6" value="{{ $user->lecturer->position }}" data-initial-value="{{ $user->lecturer->position }}"/>
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
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary close-update-btn" data-lecturer-id="{{ $user->lecturer->id }}" data-bs-dismiss="modal">Đóng</button>
                <button type="button" id="btn-update-personal-info" class="btn btn-primary" data-lecturer-id="{{ $user->lecturer->id }}">Lưu</button>
            </div>
        </div>
    </div>
</div>
