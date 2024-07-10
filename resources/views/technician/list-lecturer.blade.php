@extends('technician.layout')
@section('content')
    <div class="row m-5 mb-4 d-flex align-items-center">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="d-flex align-items-center">
                <div class="text">Giảng viên</div>
                <ol class="breadcrumb my-auto ms-4">
                    <li class="breadcrumb-item"><a href="{{ route('technician.index') }}">Trang chủ</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><a href="{{ route('technician.get-list-lecturer') }}">Giảng viên</a></li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="row p-4 ms-5 me-5 mt-5 mb-5 main-content">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="text fs-4">Danh sách giảng viên</div>
                <div class="d-flex justify-content-end">
                    <a href="#" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#add-lecturer-modal">Thêm</a>
                    <!----- Modal thêm giảng viên ----->
                    <div class="modal fade" id="add-lecturer-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="addLecturerModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="exampleModalLabel">Thêm giảng viên</h1>
                                </div>
                                <div class="modal-body">
                                    <form method="post" action="{{ route('technician.store-lecturer-api') }}" id="add-lecturer-form">
                                        @csrf
                                        <div class="row mb-3 mt-4">
                                            <label class="col-md-4 col-label-form fs-6 fw-bold text-md-end">Họ và tên<span class="required">*</span></label>
                                            <div class="col-md-7">
                                                <input type="text" name="full-name" class="form-control fs-6"/>
                                            </div>
                                        </div>
                                        <div class="row mb-3 mt-4">
                                            <label class="col-md-4 col-label-form fs-6 fw-bold text-md-end">Email<span class="required">*</span></label>
                                            <div class="col-md-7">
                                                <input type="text" name="email" class="form-control fs-6"/>
                                            </div>
                                        </div>
                                        <div class="row mb-3 mt-4">
                                            <label class="col-md-4 col-label-form fs-6 fw-bold text-md-end">Số điện thoại</label>
                                            <div class="col-md-7">
                                                <input type="text" name="phone" class="form-control fs-6"/>
                                            </div>
                                        </div>
                                        <div class="row mb-3 mt-4">
                                            <label class="col-md-4 col-label-form fs-6 fw-bold text-md-end">Học hàm/Học vị</label>
                                            <div class="col-md-7">
                                                <input type="text" name="academic-rank" class="form-control fs-6"/>
                                            </div>
                                        </div>
                                        <div class="row mb-3 mt-4">
                                            <label class="col-md-4 col-label-form fs-6 fw-bold text-md-end">Bộ môn</label>
                                            <div class="col-md-7">
                                                <input type="text" name="department" class="form-control fs-6"/>
                                            </div>
                                        </div>
                                        <div class="row mb-3 mt-4">
                                            <label class="col-md-4 col-label-form fs-6 fw-bold text-md-end">Khoa</label>
                                            <div class="col-md-7">
                                                <input type="text" name="faculty" class="form-control fs-6"/>
                                            </div>
                                        </div>
                                        <div class="row mb-3 mt-4">
                                            <label class="col-md-4 col-label-form fs-6 fw-bold text-md-end">Chức vị</label>
                                            <div class="col-md-7">
                                                <input type="text" name="position" class="form-control fs-6"/>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="text-center">
                                    <p class="ps-3 pe-3 note">*Chú ý: Tài khoản sẽ được tạo tự động với tên đăng nhập là <span>Email</span> và mật khẩu là <span>123456</span></p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary close-btn" data-bs-dismiss="modal">Đóng</button>
                                    <button type="button" id="btn-add-lecturer" class="btn btn-primary">Thêm</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-outline-success ms-2" data-bs-toggle="modal" data-bs-target="#import-lecturer-modal">Nhập file</button>
                    <!----- Modal nhập file giảng viên ----->
                    <div class="modal fade" id="import-lecturer-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="addLecturerModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="exampleModalLabel">Nhập file giảng viên</h1>
                                </div>
                                <div class="modal-body">
                                    <form method="post" action="{{ route('technician.import-lecturer-api') }}" enctype="multipart/form-data" id="import-lecturer-form">
                                        @csrf
                                        <div class="row mb-3 mt-4 d-flex justify-content-center align-items-center">
                                            <label class="col-md-3 col-label-form fs-6 fw-bold text-md-end">Chọn file</label>
                                            <div class="col-md-8">
                                                <input type="file" name="lecturer-file" class="form-control fs-6" accept=".xlsx,.xls"/>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="text-center">
                                    <p class="ps-3 pe-3">File mẫu: <a href="{{ asset('file/import-lecturer.xlsx') }}" download>mau-nhap.xlsx</a></p>
                                    <p class="ps-3 pe-3 note">*Chú ý: Tài khoản sẽ được tạo tự động với tên đăng nhập là <span>Email</span> và mật khẩu là <span>123456</span>.<br> Thông tin tối thiểu cần có: <span>Họ và tên</span> + <span>Email</span></p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary close-btn" data-bs-dismiss="modal">Đóng</button>
                                    <button type="button" id="btn-import-lecturer" class="btn btn-primary">Nhập</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-between mb-3">
                <div class="col-2 d-flex align-items-center">
                    <select class="form-select me-3 border-black" id="records-per-page" name="records-per-page" style="min-width: 70px;">
                        <option value="5" selected>5</option>
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="100">100</option>
                    </select>
                    <span class="small text-muted fw-bold" style="min-width: 130px;">kết quả mỗi trang</span>
                </div>
                <div class="col-2">
                    <input class="form-control border-black" type="search" placeholder="Tìm kiếm">
                </div>
            </div>
            <div class="table-responsive" id="table-lecturer">
                <table class="table table-bordered border-black">
                    <thead>
                    <tr>
                        <th scope="col" class="text-center" width="5%">STT</th>
                        <th scope="col" class="text-center" data-sort="full_name" width="20%">Họ và tên <i class="bx bx-sort-alt-2"></i></th>
                        <th scope="col" class="text-center" data-sort="faculty" width="25%">Khoa <i class="bx bx-sort-alt-2"></i></th>
                        <th scope="col" class="text-center" width="20%">Email</th>
                        <th scope="col" class="text-center" width="20%">Số điện thoại</th>
                        <th scope="col" class="text-center action-column">Hành động</th>
                    </tr>
                    </thead>
                    <tbody>
                        @if(count($lecturers) > 0)
                            @foreach($lecturers as $index => $lecturer)
                                <tr>
                                    <th scope="row" class="text-center">{{ $lecturers->firstItem() + $index }}</th>
                                    <td class="text-center">{{ $lecturer->full_name }}</td>
                                    <td class="text-center">{{ $lecturer->faculty }}</td>
                                    <td class="text-center">{{ $lecturer->user->email }}</td>
                                    <td class="text-center">{{ $lecturer->user->phone }}</td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center">
                                            <div class="wrap-button m-1" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Đổi mật khẩu tài khoản">
                                                <a href="#" class="btn btn-sm btn-success my-auto" data-bs-toggle="modal" data-bs-target="#update-password-lecturer-modal-{{ $lecturer->id }}"><i class='bx bxs-key'></i></a>
                                            </div>
                                            <!----- Modal đổi mật khẩu giảng viên ----->
                                            <div class="modal fade" id="update-password-lecturer-modal-{{ $lecturer->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="updatePasswordLecturerModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h1 class="modal-title fs-5" id="exampleModalLabel">Đổi mật khẩu tài khoản giảng viên</h1>
                                                        </div>
                                                        <div class="modal-body">
                                                            <form method="post" action="{{ route('technician.update-password-lecturer-api', $lecturer->id) }}" id="update-password-lecturer-form-{{ $lecturer->id }}">
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
                                                            <button type="submit" class="btn btn-primary btn-update-password-lecturer" data-lecturer-id="{{ $lecturer->id }}">Xác nhận</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="wrap-button m-1" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Sửa thông tin giảng viên">
                                                <a href="#" class="btn btn-sm btn-primary my-auto" data-bs-toggle="modal" data-bs-target="#update-lecturer-modal-{{ $lecturer->id }}"><i class='bx bx-pencil'></i></a>
                                            </div>
                                            <!----- Modal sửa giảng viên ----->
                                            <div class="modal fade modal-update" id="update-lecturer-modal-{{ $lecturer->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="addLecturerModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h1 class="modal-title fs-5" id="exampleModalLabel">Chỉnh sửa thông tin giảng viên</h1>
                                                        </div>
                                                        <div class="modal-body">
                                                            <form method="post" action="{{ route('technician.update-lecturer-api', $lecturer->id) }}" id="update-lecturer-form-{{ $lecturer->id }}">
                                                                @csrf
                                                                @method('PUT')
                                                                <div class="row mb-3 mt-4">
                                                                    <label class="col-md-4 col-label-form fs-6 fw-bold text-md-end">Họ và tên<span class="required">*</span></label>
                                                                    <div class="col-md-7">
                                                                        <input type="text" name="full-name" class="form-control fs-6" value="{{ $lecturer->full_name }}" data-initial-value="{{ $lecturer->full_name }}"/>
                                                                    </div>
                                                                </div>
                                                                <div class="row mb-3 mt-4">
                                                                    <label class="col-md-4 col-label-form fs-6 fw-bold text-md-end">Email<span class="required">*</span></label>
                                                                    <div class="col-md-7">
                                                                        <input type="text" name="email" class="form-control fs-6" value="{{ $lecturer->user->email }}" data-initial-value="{{ $lecturer->user->email }}"/>
                                                                    </div>
                                                                </div>
                                                                <div class="row mb-3 mt-4">
                                                                    <label class="col-md-4 col-label-form fs-6 fw-bold text-md-end">Số điện thoại</label>
                                                                    <div class="col-md-7">
                                                                        <input type="text" name="phone" class="form-control fs-6" value="{{ $lecturer->user->phone }}" data-initial-value="{{ $lecturer->user->phone }}"/>
                                                                    </div>
                                                                </div>
                                                                <div class="row mb-3 mt-4">
                                                                    <label class="col-md-4 col-label-form fs-6 fw-bold text-md-end">Học hàm/Học vị</label>
                                                                    <div class="col-md-7">
                                                                        <input type="text" name="academic-rank" class="form-control fs-6" value="{{ $lecturer->academic_rank }}" data-initial-value="{{ $lecturer->academic_rank }}"/>
                                                                    </div>
                                                                </div>
                                                                <div class="row mb-3 mt-4">
                                                                    <label class="col-md-4 col-label-form fs-6 fw-bold text-md-end">Bộ môn</label>
                                                                    <div class="col-md-7">
                                                                        <input type="text" name="department" class="form-control fs-6" value="{{ $lecturer->department }}" data-initial-value="{{ $lecturer->department }}"/>
                                                                    </div>
                                                                </div>
                                                                <div class="row mb-3 mt-4">
                                                                    <label class="col-md-4 col-label-form fs-6 fw-bold text-md-end">Khoa</label>
                                                                    <div class="col-md-7">
                                                                        <input type="text" name="faculty" class="form-control fs-6" value="{{ $lecturer->faculty }}" data-initial-value="{{ $lecturer->faculty }}"/>
                                                                    </div>
                                                                </div>
                                                                <div class="row mb-3 mt-4">
                                                                    <label class="col-md-4 col-label-form fs-6 fw-bold text-md-end">Chức vị</label>
                                                                    <div class="col-md-7">
                                                                        <input type="text" name="position" class="form-control fs-6" value="{{ $lecturer->position }}" data-initial-value="{{ $lecturer->position }}"/>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary close-update-btn" data-lecturer-id="{{ $lecturer->id }}" data-bs-dismiss="modal">Đóng</button>
                                                            <button type="button" class="btn btn-primary btn-update-lecturer" data-lecturer-id="{{ $lecturer->id }}">Lưu</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="wrap-button m-1" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Xóa giảng viên">
                                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#destroy-lecturer-modal-{{ $lecturer->id }}"><i class='bx bx-trash'></i></button>
                                            </div>
                                            <form method="post" action="{{ route('technician.destroy-lecturer-api', $lecturer->id) }}" id="destroy-lecturer-form-{{ $lecturer->id }}">
                                                @csrf
                                                @method('DELETE')
                                                <!----- Modal xóa giảng viên ----->
                                                <div class="modal fade" id="destroy-lecturer-modal-{{ $lecturer->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="destroyLecturerLabel" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h1 class="modal-title fs-5" id="exampleModalLabel">Xóa giảng viên</h1>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p class="text-wrap m-0">Bạn có chắc chắn muốn xóa giảng viên?<br> Điều này sẽ dẫn đến tài khoản của giảng viên cũng sẽ không còn tồn tại trong hệ thống.</p>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Trở về</button>
                                                                <button type="submit" class="btn btn-danger btn-destroy-lecturer" data-lecturer-id="{{ $lecturer->id }}">Xóa</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="6" class="text-center">Không có dữ liệu giảng viên</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
                <div class="fw-bold skill-pagination" id="paginate-lecturer">
                    {!! $lecturers->render('pagination::bootstrap-5') !!}
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $(document).ready(function() {
            const currentUrl = new URL(window.location.href);
            $('#records-per-page').val(currentUrl.searchParams.get('records-per-page') ?? 5);

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            function submitFormCreateLecturer (form, overlay) {
                const formDataObj = {};
                form.find('input, select, textarea').each(function() {
                    formDataObj[$(this).attr('name')] = $(this).val();
                });

                formDataObj['records-per-page'] = $('#records-per-page').val();

                $.ajax({
                    type: 'POST',
                    data: JSON.stringify(formDataObj),
                    contentType: 'application/json',
                    url: '{{ route("technician.store-lecturer-api") }}',
                    success: function (response) {
                        if (response.success) {
                            showToastSuccess(response.success);
                            form[0].reset();
                            $('#table-lecturer tbody').html(response.table_lecturer);
                            $('#paginate-lecturer').html(response.links);

                            const currentUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
                            window.history.pushState({path: currentUrl}, '', currentUrl);
                            const newUrl = new URL(window.location.href);
                            newUrl.searchParams.set('records-per-page', $('#records-per-page').val());
                            history.pushState(null, '', newUrl.toString());
                            updatePagination();
                            addEventForModalUpdate();
                            addEventForButtons();
                            $('#add-lecturer-modal').modal('hide');
                            $('body').css('overflow', 'auto');
                            $('body').css('padding', '0');
                        } else {
                            if (response.errors['full-name']) {
                                showToastError(response.errors['full-name']);
                            }
                            if (response.errors['email']) {
                                showToastError(response.errors['email']);
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

            function submitFormUpdateLecturer (form, lecturerId, overlay) {
                const formDataObj = Object.fromEntries(new FormData(form).entries());
                formDataObj['records-per-page'] = $('#records-per-page').val();

                let url = `{{ route("technician.update-lecturer-api", ":lecturerId") }}`;
                url = url.replace(':lecturerId', lecturerId);
                $.ajax({
                    type: 'PUT',
                    data: JSON.stringify(formDataObj),
                    contentType: 'application/json',
                    url: url,
                    success: function (response) {
                        if (response.success) {
                            showToastSuccess(response.success);
                            $('#table-lecturer tbody').html(response.table_lecturer);
                            $('#paginate-lecturer').html(response.links);

                            const currentUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
                            window.history.pushState({path: currentUrl}, '', currentUrl);
                            const newUrl = new URL(window.location.href);
                            newUrl.searchParams.set('records-per-page', $('#records-per-page').val());
                            history.pushState(null, '', newUrl.toString());
                            updatePagination();
                            addEventForModalUpdate();
                            addEventForButtons();
                            $('#update-lecturer-modal-' + lecturerId).modal('hide');
                            $('body').css('overflow', 'auto');
                            $('body').css('padding', '0');

                            overlay.classList.remove('show');
                        } else {
                            if (response.errors['full-name']) {
                                showToastError(response.errors['full-name']);
                            }
                            if (response.errors['email']) {
                                showToastError(response.errors['email']);
                            }
                            $('body').append('<div class="modal-backdrop fade show"></div>');
                        }
                    },
                    error: function (error) {
                        console.error(error);
                    }
                });
            }

            function submitFormUpdatePasswordLecturer(form, lecturerId, overlay) {
                const formDataObj = {};
                form.find('input, select, textarea').each(function() {
                    formDataObj[$(this).attr('name')] = $(this).val();
                });

                let url = `{{ route("technician.update-password-lecturer-api", ":lecturerId") }}`;
                url = url.replace(':lecturerId', lecturerId);
                $.ajax({
                    type: 'PUT',
                    data: JSON.stringify(formDataObj),
                    contentType: 'application/json',
                    url: url,
                    success: function (response) {
                        if (response.success) {
                            showToastSuccess(response.success);
                            form[0].reset();
                            $('#update-password-lecturer-modal-' + lecturerId).modal('hide');
                            $('body').css('overflow', 'auto');
                            $('body').css('padding', '0');
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

            function submitFormDestroyLecturer (lecturerId, overlay) {
                let url = `{{ route("technician.destroy-lecturer-api", ":lecturerId") }}`;
                url = url.replace(':lecturerId', lecturerId);
                $.ajax({
                    type: 'DELETE',
                    contentType: 'application/json',
                    url: url,
                    data: JSON.stringify({'records-per-page': $('#records-per-page').val()}),
                    success: function (response) {
                        if (response.success) {
                            showToastSuccess(response.success);
                            $('#table-lecturer tbody').html(response.table_lecturer);
                            $('#paginate-lecturer').html(response.links);

                            const currentUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
                            window.history.pushState({path: currentUrl}, '', currentUrl);
                            const newUrl = new URL(window.location.href);
                            newUrl.searchParams.set('records-per-page', $('#records-per-page').val());
                            history.pushState(null, '', newUrl.toString());
                            updatePagination();
                            addEventForModalUpdate();
                            addEventForButtons();
                        } else {
                            if (response.errors['lecturer']) {
                                showToastError(response.errors['lecturer']);
                            }
                        }

                        $('#destroy-lecturer-modal-' + lecturerId).modal('hide');
                        $('body').css('overflow', 'auto');
                        $('body').css('padding', '0');
                        overlay.classList.remove('show');
                    },
                    error: function (error) {
                        console.error(error);
                    }
                });
            }

            function submitFormImportLecturer (form, overlay) {
                const formData = new FormData(form[0]);
                formData.append('records-per-page', $('#records-per-page').val());
                $.ajax({
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    url: '{{ route("technician.import-lecturer-api") }}',
                    success: function (response) {
                        if (response.success) {
                            showToastSuccess(response.success);
                            form[0].reset();
                            $('#table-lecturer tbody').html(response.table_lecturer);
                            $('#paginate-lecturer').html(response.links);

                            const currentUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
                            window.history.pushState({path: currentUrl}, '', currentUrl);
                            const newUrl = new URL(window.location.href);
                            newUrl.searchParams.set('records-per-page', $('#records-per-page').val());
                            history.pushState(null, '', newUrl.toString());
                            updatePagination();
                            addEventForModalUpdate();
                            addEventForButtons();
                            $('#import-lecturer-modal').modal('hide');
                            $('body').css('overflow', 'auto');
                            $('body').css('padding', '0');
                        } else {
                            showToastError(response.errors['lecturer-file']);
                            $('body').append('<div class="modal-backdrop fade show"></div>');
                        }

                        overlay.classList.remove('show');
                    },
                    error: function (error) {
                        console.error(error);
                    }
                });
            }

            function updatePagination() {
                const currentUrl = new URL(window.location.href);
                const currentPath = currentUrl.pathname;
                const searchParams = currentUrl.searchParams;

                $('.pagination .page-link').each(function() {
                    const link = $(this);
                    if (link.attr('href')) {
                        const newUrl = new URL(link.attr('href'), window.location.origin);
                        searchParams.set('page', newUrl.searchParams.get('page'));

                        const updatedUrl = currentPath + '?' + searchParams.toString();
                        link.attr('href', updatedUrl);
                    }
                });
            }

            $('#btn-add-lecturer').click(function(e) {
                e.preventDefault();
                const overlay = document.getElementById('overlay');
                overlay.classList.add('show');
                $('.modal-backdrop').remove();

                const form = $('#add-lecturer-form');
                submitFormCreateLecturer(form, overlay);
            });

            $('#btn-import-lecturer').click(function(e) {
                e.preventDefault();
                const overlay = document.getElementById('overlay');
                overlay.classList.add('show');
                $('.modal-backdrop').remove();

                const form = $('#import-lecturer-form');
                submitFormImportLecturer(form, overlay);
            });

            $('#records-per-page').change(function() {
                const overlay = document.getElementById('overlay');
                overlay.classList.add('show');
                const recordsPerPage = $(this).val();
                const currentUrl = new URL(window.location.href);
                currentUrl.searchParams.set('records-per-page', recordsPerPage);
                history.pushState(null, '', currentUrl.toString());

                const sortField = currentUrl.searchParams.get('sort-field');
                const sortOrder = currentUrl.searchParams.get('sort-order');
                const data = {};
                if (sortField && sortOrder) {
                    data['sortField'] = sortField;
                    data['sortOrder'] = sortOrder;
                }
                data['recordsPerPage'] = recordsPerPage;

                $.ajax({
                    url: `{{ route('technician.change-records-per-page-lecturer-api') }}`,
                    type: 'GET',
                    data: data,
                    success: function(response) {
                        $('#table-lecturer tbody').html(response.table_lecturer);
                        $('#paginate-lecturer').html(response.links);
                        updatePagination();
                        addEventForModalUpdate();
                        addEventForButtons();
                        overlay.classList.remove('show');
                    }
                });
            });

            function addEventForButtons () {
                const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
                const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));

                $('th[data-sort]').each(function() {
                    const currentUrl = new URL(window.location.href);
                    const currentField = currentUrl.searchParams.get('sort-field');
                    const currentOrder = currentUrl.searchParams.get('sort-order');
                    const field = $(this).data('sort');

                    if (currentField === field) {
                        if (currentOrder === 'asc') {
                            $(this).find('i').attr('class', 'bx bx-sort-up');
                        } else if (currentOrder === 'desc') {
                            $(this).find('i').attr('class', 'bx bx-sort-down');
                        }
                    } else {
                        $(this).find('i').attr('class', 'bx bx-sort-alt-2');
                    }
                });

                $('.btn-update-lecturer').off('click').click(function(e) {
                    e.preventDefault();
                    const overlay = document.getElementById('overlay');
                    overlay.classList.add('show');
                    $('.modal-backdrop').remove();

                    const lecturerId = $(this).data('lecturer-id');
                    const form = $('#update-lecturer-form-' + lecturerId);

                    submitFormUpdateLecturer(form[0], lecturerId, overlay);
                });

                $('.btn-update-password-lecturer').off('click').click(function(e) {
                    e.preventDefault();
                    const overlay = document.getElementById('overlay');
                    overlay.classList.add('show');
                    $('.modal-backdrop').remove();

                    const lecturerId = $(this).data('lecturer-id');
                    const form = $('#update-password-lecturer-form-' + lecturerId);

                    submitFormUpdatePasswordLecturer(form, lecturerId, overlay);
                });

                $('.btn-destroy-lecturer').off('click').click(function(e) {
                    e.preventDefault();
                    const overlay = document.getElementById('overlay');
                    overlay.classList.add('show');
                    $('.modal-backdrop').remove();

                    const lecturerId = $(this).data('lecturer-id');

                    submitFormDestroyLecturer(lecturerId, overlay);
                });

                $('th[data-sort]').off('click').click(function() {
                    const field = $(this).data('sort');
                    const order = $(this).hasClass('ascending') ? 'desc' : 'asc';

                    const currentUrl = new URL(window.location.href);
                    currentUrl.searchParams.set('sort-field', field);
                    currentUrl.searchParams.set('sort-order', order);
                    history.pushState(null, '', currentUrl.toString());

                    $.ajax({
                        url: `{{ route('technician.sort-lecturer-api') }}`,
                        type: 'GET',
                        data: {sortField: field, sortOrder: order, recordsPerPage: $('#records-per-page').val()},
                        success: function(response) {
                            $('#table-lecturer tbody').html(response.table_lecturer);
                            $('#paginate-lecturer').html(response.links);
                            updatePagination();
                            addEventForModalUpdate();
                            addEventForButtons();

                            $('th[data-sort] i').attr('class', 'bx bx-sort-alt-2');
                            const iconClass = order === 'asc' ? 'bx-sort-up' : 'bx-sort-down';
                            $(`th[data-sort="${field}"] i`).attr('class', `bx ${iconClass}`);

                            $('th[data-sort]').removeClass('ascending descending');
                            $(`th[data-sort="${field}"]`).addClass(order === 'asc' ? 'ascending' : 'descending');
                        }
                    });
                });

                $('.close-btn').off('click').click(function() {
                    $('.modal-backdrop.fade.show').remove();
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
                    input.val(input.data('initial-value'));
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
            updatePagination();
        });
    </script>
@endsection
