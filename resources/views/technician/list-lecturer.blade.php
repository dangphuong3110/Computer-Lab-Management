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

    <div class="ms-5 me-5 alert alert-success" id="success-message" style="display: none;">

    </div>

    <div class="row p-4 ms-5 me-5 mb-0 main-content">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="text fs-4">Danh sách giảng viên</div>
                <div class="d-flex justify-content-end">
                    <a href="{{ route('technician.create-lecturer') }}" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#add-lecturer-modal">Thêm</a>
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
                                                <span role="alert" class="text-danger fs-6 d-flex align-items-center justify-content-center">
                                                    <br>
                                                    <strong id="error-message-full-name"></strong>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="row mb-3 mt-4">
                                            <label class="col-md-4 col-label-form fs-6 fw-bold text-md-end">Email<span class="required">*</span></label>
                                            <div class="col-md-7">
                                                <input type="text" name="email" class="form-control fs-6"/>
                                                <span role="alert" class="text-danger fs-6 d-flex align-items-center justify-content-center">
                                                    <br>
                                                    <strong id="error-message-email"></strong>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="row mb-3 mt-4">
                                            <label class="col-md-4 col-label-form fs-6 fw-bold text-md-end">Học vị</label>
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
                    <button type="button" class="btn btn-secondary ms-2" data-bs-toggle="modal" data-bs-target="#import-lecturer-modal">Nhập file</button>
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
            <div class="table-responsive" id="table-lecturer">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th scope="col" class="text-center" width="5%">STT</th>
                        <th scope="col" class="text-center" width="20%">Họ và tên</th>
                        <th scope="col" class="text-center" width="15%">Học vị</th>
                        <th scope="col" class="text-center" width="20%">Bộ môn</th>
                        <th scope="col" class="text-center" width="20%">Khoa</th>
                        <th scope="col" class="text-center" width="10%">Chức vụ</th>
                        <th scope="col" class="text-center" width="10%">Hành động</th>
                    </tr>
                    </thead>
                    <tbody>
                        @if(count($lecturers) > 0)
                            @foreach($lecturers as $index => $lecturer)
                                <tr>
                                    <th scope="row" class="text-center">{{ $lecturers->firstItem() + $index }}</th>
                                    <td class="text-center">{{ $lecturer->full_name }}</td>
                                    <td class="text-center">{{ $lecturer->academic_rank }}</td>
                                    <td class="text-center">{{ $lecturer->department }}</td>
                                    <td class="text-center">{{ $lecturer->faculty }}</td>
                                    <td class="text-center">{{ $lecturer->position }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('technician.edit-lecturer', $lecturer->id) }}" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#update-lecturer-modal-{{ $lecturer->id }}"><i class='bx bx-pencil'></i></a>
                                        <!----- Modal sửa giảng viên ----->
                                        <div class="modal fade modal-update" id="update-lecturer-modal-{{ $lecturer->id }}" data-bs-keyboard="false" tabindex="-1" aria-labelledby="addLecturerModalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h1 class="modal-title fs-5" id="exampleModalLabel">Chỉnh sửa thông tin giảng viên</h1>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form method="post" action="{{ route('technician.update-lecturer', $lecturer->id) }}" id="update-lecturer-form-{{ $lecturer->id }}">
                                                            @csrf
                                                            @method('PUT')
                                                            <div class="row mb-3 mt-4">
                                                                <label class="col-md-4 col-label-form fs-6 fw-bold text-md-end">Họ và tên<span class="required">*</span></label>
                                                                <div class="col-md-7">
                                                                    <input type="text" name="full-name" class="form-control fs-6" value="{{ $lecturer->full_name }}"/>
                                                                    <span role="alert" class="text-danger fs-6 d-flex align-items-center justify-content-center">
                                                                <br>
                                                                <strong id="error-message-full-name"></strong>
                                                            </span>
                                                                </div>
                                                            </div>
                                                            <div class="row mb-3 mt-4">
                                                                <label class="col-md-4 col-label-form fs-6 fw-bold text-md-end">Email<span class="required">*</span></label>
                                                                <div class="col-md-7">
                                                                    <input type="text" name="email" class="form-control fs-6" value="{{ $lecturer->user->email }}"/>
                                                                    <span role="alert" class="text-danger fs-6 d-flex align-items-center justify-content-center">
                                                                <br>
                                                                <strong id="error-message-email"></strong>
                                                            </span>
                                                                </div>
                                                            </div>
                                                            <div class="row mb-3 mt-4">
                                                                <label class="col-md-4 col-label-form fs-6 fw-bold text-md-end">Học vị</label>
                                                                <div class="col-md-7">
                                                                    <input type="text" name="academic-rank" class="form-control fs-6" value="{{ $lecturer->academic_rank }}"/>
                                                                </div>
                                                            </div>
                                                            <div class="row mb-3 mt-4">
                                                                <label class="col-md-4 col-label-form fs-6 fw-bold text-md-end">Bộ môn</label>
                                                                <div class="col-md-7">
                                                                    <input type="text" name="department" class="form-control fs-6" value="{{ $lecturer->department }}"/>
                                                                </div>
                                                            </div>
                                                            <div class="row mb-3 mt-4">
                                                                <label class="col-md-4 col-label-form fs-6 fw-bold text-md-end">Khoa</label>
                                                                <div class="col-md-7">
                                                                    <input type="text" name="faculty" class="form-control fs-6" value="{{ $lecturer->faculty }}"/>
                                                                </div>
                                                            </div>
                                                            <div class="row mb-3 mt-4">
                                                                <label class="col-md-4 col-label-form fs-6 fw-bold text-md-end">Chức vị</label>
                                                                <div class="col-md-7">
                                                                    <input type="text" name="position" class="form-control fs-6" value="{{ $lecturer->position }}"/>
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
                                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#destroy-lecturer-modal-{{ $lecturer->id }}"><i class='bx bx-trash'></i></button>
                                        <form method="post" action="{{ route('technician.destroy-lecturer', $lecturer->id) }}" id="destroy-lecturer-form-{{ $lecturer->id }}">
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
                                                            Bạn có chắc chắn muốn xóa giảng viên?<br> Điều này sẽ dẫn đến tài khoản của giảng viên cũng sẽ không còn tồn tại trong hệ thống.
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Trở về</button>
                                                            <button type="submit" class="btn btn-danger btn-destroy-lecturer" data-lecturer-id="{{ $lecturer->id }}">Xóa</button>
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
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            function submitFormCreateLecturer (form, overlay) {
                const formDataObj = {};
                form.find('input').each(function() {
                    formDataObj[$(this).attr('name')] = $(this).val();
                });

                $.ajax({
                    type: 'POST',
                    data: JSON.stringify(formDataObj),
                    contentType: 'application/json',
                    url: '{{ route("technician.store-lecturer-api") }}',
                    success: function (response) {
                        if (response.success) {
                            $('#success-message').text(response.success).fadeIn();
                            setTimeout(function() {
                                $('#success-message').fadeOut();
                            }, 4000);
                            form[0].reset();
                            $('#table-lecturer tbody').html(response.table_lecturer);
                            $('#paginate-lecturer').html(response.links);
                            updatePagination();
                            addEventForModalUpdate();
                            addEventForButtons();
                            $('#add-lecturer-modal').modal('hide');
                        } else {
                            if (response.errors['full-name']) {
                                $('#error-message-full-name').text(response.errors['full-name']);
                            } else {
                                $('#error-message-full-name').text('');
                            }
                            if (response.errors['email']) {
                                $('#error-message-email').text(response.errors['email']);
                            } else {
                                $('#error-message-email').text('');
                            }
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

                let url = `{{ route("technician.update-lecturer-api", ":lecturerId") }}`;
                url = url.replace(':lecturerId', lecturerId);
                $.ajax({
                    type: 'PUT',
                    data: JSON.stringify(formDataObj),
                    contentType: 'application/json',
                    url: url,
                    success: function (response) {
                        if (response.success) {
                            $('#success-message').text(response.success).fadeIn();
                            setTimeout(function() {
                                $('#success-message').fadeOut();
                            }, 4000);
                            // updateInitialValue($('#update-lecturer-modal-' + lecturerId));
                            $('#table-lecturer tbody').html(response.table_lecturer);
                            $('#paginate-lecturer').html(response.links);
                            updatePagination();
                            addEventForModalUpdate();
                            addEventForButtons();
                            $('#update-lecturer-modal-' + lecturerId).modal('hide');
                        } else {
                            if (response.errors['full-name']) {
                                $('#error-message-full-name').text(response.errors['full-name']);
                            } else {
                                $('#error-message-full-name').text('');
                            }
                            if (response.errors['email']) {
                                $('#error-message-email').text(response.errors['email']);
                            } else {
                                $('#error-message-email').text('');
                            }
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
                    success: function (response) {
                        if (response.success) {
                            $('#success-message').text(response.success).fadeIn();
                            setTimeout(function() {
                                $('#success-message').fadeOut();
                            }, 4000);
                            // resetInitialValue();
                            $('#table-lecturer tbody').html(response.table_lecturer);
                            $('#paginate-lecturer').html(response.links);
                            updatePagination();
                            addEventForModalUpdate();
                            addEventForButtons();
                            $('#destroy-lecturer-modal-' + lecturerId).modal('hide');
                        }

                        overlay.classList.remove('show');
                    },
                    error: function (error) {
                        console.error(error);
                    }
                });
            }

            function submitFormImportLecturer (form, overlay) {
                const formData = new FormData(form[0]);
                $.ajax({
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    url: '{{ route("technician.import-lecturer-api") }}',
                    success: function (response) {
                        if (response.success) {
                            $('#success-message').text(response.success).fadeIn();
                            setTimeout(function() {
                                $('#success-message').fadeOut();
                            }, 4000);

                            $('#import-lecturer-modal-').modal('hide');
                        }

                        overlay.classList.remove('show');
                    },
                    error: function (error) {
                        console.error(error);
                    }
                });
            }

            function updatePagination () {
                const currentUrl = new URL(window.location.href);
                const currentPath = currentUrl.pathname;
                const searchParams = currentUrl.searchParams;

                $('.pagination .page-link').each(function() {
                    const link = $(this);
                    if (link.attr('href')) {
                        const newUrl = new URL(link.attr('href'));
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

            function addEventForButtons () {
                $('.btn-update-lecturer').off('click');
                $('.btn-destroy-lecturer').off('click');
                $('.close-btn').off('click');
                $('.close-update-btn').off('click');

                $('.btn-update-lecturer').click(function(e) {
                    e.preventDefault();
                    const overlay = document.getElementById('overlay');
                    overlay.classList.add('show');
                    $('.modal-backdrop').remove();

                    const lecturerId = $(this).data('lecturer-id');
                    const form = $('#update-lecturer-form-' + lecturerId);
                    console.log(form[0]);

                    submitFormUpdateLecturer(form[0], lecturerId, overlay);
                });

                $('.btn-destroy-lecturer').click('click', function(e) {
                    e.preventDefault();
                    const overlay = document.getElementById('overlay');
                    overlay.classList.add('show');
                    $('.modal-backdrop').remove();

                    const lecturerId = $(this).data('lecturer-id');

                    submitFormDestroyLecturer(lecturerId, overlay);
                });

                $('.close-btn').click(function() {
                    $('#error-message-full-name').text('');
                    $('#error-message-email').text('');
                });

                $('.close-update-btn').click(function() {
                    $('#error-message-full-name').text('');
                    $('#error-message-email').text('');
                });
            }

            function addEventForModalUpdate() {
                $('.modal-update').on('shown.bs.modal', function() {
                    const modal = $(this);
                    // Lưu trữ giá trị ban đầu của mỗi trường input
                    modal.find('input, select, textarea').each(function() {
                        const input = $(this);
                        input.data('initial-value', input.val());
                    });
                });

                // Khi modal bị ẩn
                $('.modal-update').on('hidden.bs.modal', function() {
                    const modal = $(this);
                    // Khôi phục lại giá trị ban đầu của mỗi trường input
                    modal.find('input, select, textarea').each(function() {
                        const input = $(this);
                        input.val(input.data('initial-value'));
                    });
                });
            }

            addEventForModalUpdate();
            addEventForButtons();
        });
    </script>
@endsection
