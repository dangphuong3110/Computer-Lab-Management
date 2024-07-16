<div class="col-md-6 mb-3">
    <label class="col-label-form fs-6 fw-bold">Họ và tên</label>
    <div>
        <input type="text" name="full-name" class="form-control fs-6" value="{{ $user->manager->full_name ?? '' }}" disabled/>
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
<!----- Modal sửa thông tin cá nhân cán bộ quản lý ----->
<div class="modal fade modal-update" id="update-personal-info-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="updatePersonalInfoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Chỉnh sửa thông tin cá nhân</h1>
            </div>
            <div class="modal-body">
                <form method="post" action="{{ route('manager.update-personal-info-api', $user->manager->id) }}" id="update-personal-info-form">
                    @csrf
                    @method('PUT')
                    <div class="row mb-3 mt-4">
                        <label class="col-md-4 col-label-form fs-6 fw-bold text-md-end">Họ và tên<span class="required">*</span></label>
                        <div class="col-md-7">
                            <input type="text" name="full-name" class="form-control fs-6" value="{{ $user->manager->full_name }}" data-initial-value="{{ $user->manager->full_name }}"/>
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
                <button type="button" class="btn btn-secondary close-update-btn" data-manager-id="{{ $user->manager->id }}" data-bs-dismiss="modal">Đóng</button>
                <button type="button" id="btn-update-personal-info" class="btn btn-primary" data-manager-id="{{ $user->manager->id }}">Lưu</button>
            </div>
        </div>
    </div>
</div>
