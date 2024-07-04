@extends('technician.layout')
@section('content')
    <div class="row m-5 mb-4 d-flex align-items-center">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="d-flex align-items-center">
                <div class="text">Thông tin cá nhân</div>
                <ol class="breadcrumb my-auto ms-4">
                    <li class="breadcrumb-item"><a href="{{ route('technician.index') }}">Trang chủ</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><a href="{{ route('technician.get-personal-info') }}">Thông tin cá nhân</a></li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="row p-4 ms-5 me-5 mt-5 mb-0 main-content">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="text fs-4">Thông tin chung</div>
                <div class="d-flex justify-content-end">
                    <a href="#" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#update-personal-info-modal">Sửa thông tin cá nhân</a>
                    <a href="#" class="btn btn-outline-success ms-2" data-bs-toggle="modal" data-bs-target="#update-password-modal">Đổi mật khẩu</a>
                    <!----- Modal đổi mật khẩu tài khoản ----->
                    <div class="modal fade" id="update-password-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="updatePasswordModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="exampleModalLabel">Đổi mật khẩu tài khoản</h1>
                                </div>
                                <div class="modal-body">
                                    <form method="post" action="{{ route('technician.update-password-api', $user->id) }}" id="update-password-form">
                                        @csrf
                                        <div class="row mb-3 mt-4">
                                            <label class="col-md-5 col-label-form fs-6 fw-bold text-md-end">Mật khẩu mới<span class="required">*</span></label>
                                            <div class="col-md-7">
                                                <input type="password" name="new-password" class="form-control fs-6"/>
                                            </div>
                                        </div>
                                        <div class="row mb-3 mt-4">
                                            <label class="col-md-5 col-label-form fs-6 fw-bold text-md-end">Nhập lại mật khẩu mới<span class="required">*</span></label>
                                            <div class="col-md-7">
                                                <input type="password" name="re-enter-new-password" class="form-control fs-6"/>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary close-update-btn" data-bs-dismiss="modal">Đóng</button>
                                    <button type="submit" id="btn-update-password" class="btn btn-primary" data-user-id="{{ $user->id }}">Xác nhận</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row" id="table-personal-info">
                <div class="col-md-6 mb-3">
                    <label class="col-label-form fs-6 fw-bold">Họ và tên</label>
                    <div>
                        <input type="text" name="full-name" class="form-control fs-6" value="{{ $user->technician->full_name ?? '' }}" disabled/>
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
                <!----- Modal sửa thông tin cá nhân kỹ thuật viên ----->
                <div class="modal fade modal-update" id="update-personal-info-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="updatePersonalInfoModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="exampleModalLabel">Chỉnh sửa thông tin cá nhân</h1>
                            </div>
                            <div class="modal-body">
                                <form method="post" action="{{ route('technician.update-personal-info-api', $user->technician->id) }}" id="update-personal-info-form">
                                    @csrf
                                    @method('PUT')
                                    <div class="row mb-3 mt-4">
                                        <label class="col-md-4 col-label-form fs-6 fw-bold text-md-end">Họ và tên<span class="required">*</span></label>
                                        <div class="col-md-7">
                                            <input type="text" name="full-name" class="form-control fs-6" value="{{ $user->technician->full_name }}" data-initial-value="{{ $user->technician->full_name }}"/>
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
                                <button type="button" class="btn btn-secondary close-update-btn" data-technician-id="{{ $user->technician->id }}" data-bs-dismiss="modal">Đóng</button>
                                <button type="button" id="btn-update-personal-info" class="btn btn-primary" data-technician-id="{{ $user->technician->id }}">Lưu</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            function submitFormUpdatePersonalInfo (form, technicianId, overlay) {
                const formDataObj = {};
                form.find('input, select, textarea').each(function() {
                    formDataObj[$(this).attr('name')] = $(this).val();
                });

                let url = `{{ route("technician.update-personal-info-api", ":technician") }}`;
                url = url.replace(':technician', technicianId);
                $.ajax({
                    type: 'PUT',
                    data: JSON.stringify(formDataObj),
                    contentType: 'application/json',
                    url: url,
                    success: function (response) {
                        if (response.success) {
                            showToastSuccess(response.success);
                            $('#table-personal-info').html(response.table_personal_info);
                            addEventForModalUpdate();
                            addEventForButtons();
                            $('#update-personal-info-modal').modal('hide');
                            $('body').css('overflow', 'auto');
                        } else {
                            if (response.errors['full-name']) {
                                showToastError(response.errors['full-name']);
                            }
                            $('body').append('<div class="modal-backdrop fade show"></div>');
                        }
                        overlay.classList.remove('show');
                    },
                    error: function (error) {
                        console.error(error);
                    }
                });
            }

            function submitFormUpdatePassword(form, userId, overlay) {
                const formDataObj = {};
                form.find('input, select, textarea').each(function() {
                    formDataObj[$(this).attr('name')] = $(this).val();
                });

                let url = `{{ route("technician.update-password-api", ":userId") }}`;
                url = url.replace(':userId', userId);
                $.ajax({
                    type: 'PUT',
                    data: JSON.stringify(formDataObj),
                    contentType: 'application/json',
                    url: url,
                    success: function (response) {
                        if (response.success) {
                            showToastSuccess(response.success);
                            form[0].reset();
                            $('#update-password-modal').modal('hide');
                            $('body').css('overflow', 'auto');
                        } else {
                            if (response.errors['new-password']) {
                                showToastError(response.errors['new-password']);
                            }
                            if (response.errors['re-enter-new-password']) {
                                showToastError(response.errors['re-enter-new-password'])
                            }
                            $('body').append('<div class="modal-backdrop fade show"></div>');
                        }
                        overlay.classList.remove('show');
                    },
                    error: function (error) {
                        console.error(error);
                    }
                });
            }

            $('#btn-update-password').click(function(e) {
                e.preventDefault();
                const overlay = document.getElementById('overlay');
                overlay.classList.add('show');
                $('.modal-backdrop').remove();

                const userId = $(this).data('user-id');
                const form = $('#update-password-form');

                submitFormUpdatePassword(form, userId, overlay);
            });

            function addEventForButtons() {
                $('#btn-update-personal-info').off('click').click(function(e) {
                    e.preventDefault();
                    const overlay = document.getElementById('overlay');
                    overlay.classList.add('show');
                    $('.modal-backdrop').remove();

                    const technicianId = $(this).data('technician-id');
                    const form = $('#update-personal-info-form');

                    submitFormUpdatePersonalInfo(form, technicianId, overlay);
                });

                $('.close-update-btn').off('click').click(function() {
                    $('.modal-backdrop.fade.show').remove();
                });
            }

            function addEventForModalUpdate() {
                $('.modal-update').on('shown.bs.modal', function() {
                    const modal = $(this);
                    modal.find('input, select, textarea').each(function() {
                        const input = $(this);
                        input.data('initial-value', input.val());
                    });
                });

                $('.modal-update').on('hidden.bs.modal', function() {
                    const modal = $(this);
                    modal.find('input, select, textarea').each(function() {
                        const input = $(this);
                        input.val(input.data('initial-value'));
                    });
                });
            }

            function resetInitialValue() {
                $('.modal-update').find('input, select, textarea').each(function() {
                    const input = $(this);
                    if (input.is('select')) {
                        const initialValue = input.data('initial-value');
                        input.val(initialValue).trigger('change');
                    } else {
                        input.val(input.data('initial-value'));
                    }
                });
            }

            function showToastSuccess(text) {
                Toastify({
                    text: text,
                    duration: 4000,
                    close: true,
                    gravity: "top",
                    position: "right",
                    stopOnFocus: true,
                    style: {
                        background: "linear-gradient(to right, #1930B0, #0D6EFD)",
                    },
                    offset: {
                        x: 50,
                        y: 60,
                    },
                    onClick: function(){}
                }).showToast();
            }

            function showToastError(text) {
                Toastify({
                    text: text,
                    duration: 4000,
                    close: true,
                    gravity: "top",
                    position: "right",
                    stopOnFocus: true,
                    style: {
                        background: "linear-gradient(to right, #9D2626, #F95968)",
                    },
                    offset: {
                        x: 50,
                        y: 60,
                    },
                    onClick: function(){}
                }).showToast();
            }

            resetInitialValue();
            addEventForModalUpdate();
            addEventForButtons();
        });
    </script>
@endsection
