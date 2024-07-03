@extends('technician.layout')
@section('content')
    <div class="row m-5 mb-4 d-flex align-items-center">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="d-flex align-items-center">
                <div class="text">Sinh viên</div>
                <ol class="breadcrumb my-auto ms-4">
                    <li class="breadcrumb-item"><a href="{{ route('technician.index') }}">Trang chủ</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><a href="{{ route('technician.get-list-student') }}">Sinh viên</a></li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="row p-4 ms-5 me-5 mt-5 mb-0 main-content">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="text fs-4">Danh sách sinh viên</div>
                <div class="d-flex justify-content-end">
                    <a href="#" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#add-student-modal">Thêm</a>
                    <!----- Modal thêm sinh viên ----->
                    <div class="modal fade" id="add-student-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="addStudentModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="exampleModalLabel">Thêm sinh viên</h1>
                                </div>
                                <div class="modal-body">
                                    <form method="post" action="{{ route('technician.store-student-api') }}" id="add-student-form">
                                        @csrf
                                        <div class="row mb-3 mt-4">
                                            <label class="col-md-4 col-label-form fs-6 fw-bold text-md-end">Họ và tên<span class="required">*</span></label>
                                            <div class="col-md-7">
                                                <input type="text" name="full-name" class="form-control fs-6"/>
                                            </div>
                                        </div>
                                        <div class="row mb-3 mt-4">
                                            <label class="col-md-4 col-label-form fs-6 fw-bold text-md-end">Mã sinh viên<span class="required">*</span></label>
                                            <div class="col-md-7">
                                                <input type="text" name="student-code" class="form-control fs-6"/>
                                            </div>
                                        </div>
                                        <div class="row mb-3 mt-4">
                                            <label class="col-md-4 col-label-form fs-6 fw-bold text-md-end">Số điện thoại</label>
                                            <div class="col-md-7">
                                                <input type="text" name="phone" class="form-control fs-6"/>
                                            </div>
                                        </div>
                                        <div class="row mb-3 mt-4">
                                            <label class="col-md-4 col-label-form fs-6 fw-bold text-md-end">Lớp</label>
                                            <div class="col-md-7">
                                                <input type="text" name="class" class="form-control fs-6"/>
                                            </div>
                                        </div>
                                        <div class="row mb-3 mt-4">
                                            <label class="col-md-4 col-label-form fs-6 fw-bold text-md-end">Giới tính</label>
                                            <div class="col-md-7">
                                                <select name="gender" class="form-select form-control fs-6">
                                                    <option value="Nam" selected>Nam</option>
                                                    <option value="Nữ">Nữ</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row mb-3 mt-4">
                                            <label class="col-md-4 col-label-form fs-6 fw-bold text-md-end">Ngày sinh</label>
                                            <div class="col-md-7">
                                                <input type="date" name="date-of-birth" class="form-control fs-6"/>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="text-center">
                                    <p class="ps-3 pe-3 note">*Chú ý: Tài khoản sẽ được tạo tự động với tên đăng nhập là <span>Ma_sinh_vien@e.tlu.edu.vn</span> và mật khẩu là <span>123456</span></p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary close-btn" data-bs-dismiss="modal">Đóng</button>
                                    <button type="button" id="btn-add-student" class="btn btn-primary">Thêm</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-outline-success ms-2" data-bs-toggle="modal" data-bs-target="#import-student-modal">Nhập file</button>
                    <!----- Modal nhập file sinh viên ----->
                    <div class="modal fade" id="import-student-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="addStudentModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="exampleModalLabel">Nhập file sinh viên</h1>
                                </div>
                                <div class="modal-body">
                                    <form method="post" action="{{ route('technician.import-student-api') }}" enctype="multipart/form-data" id="import-student-form">
                                        @csrf
                                        <div class="row mb-3 mt-4 d-flex justify-content-center align-items-center">
                                            <label class="col-md-3 col-label-form fs-6 fw-bold text-md-end">Chọn file</label>
                                            <div class="col-md-8">
                                                <input type="file" name="student-file" class="form-control fs-6" accept=".xlsx,.xls"/>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="text-center">
                                    <p class="ps-3 pe-3">File mẫu: <a href="{{ asset('file/import-student.xlsx') }}" download>mau-nhap.xlsx</a></p>
                                    <p class="ps-3 pe-3 note">*Chú ý: Tài khoản sẽ được tạo tự động với tên đăng nhập là <span>Ma_sinh_vien@e.tlu.edu.vn</span> và mật khẩu là <span>123456</span>.<br> Thông tin tối thiểu cần có: <span>Họ và tên</span> + <span>Mã sinh viên</span></p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary close-btn" data-bs-dismiss="modal">Đóng</button>
                                    <button type="button" id="btn-import-student" class="btn btn-primary">Nhập</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="table-responsive" id="table-student">
                <table class="table table-bordered border-black">
                    <thead>
                    <tr>
                        <th scope="col" class="text-center" width="5%">STT</th>
                        <th scope="col" class="text-center" data-sort="full_name" width="15%">Họ và tên <i class="bx bx-sort-alt-2"></i></th>
                        <th scope="col" class="text-center" data-sort="student_code" width="15%">Mã sinh viên <i class="bx bx-sort-alt-2"></i></th>
                        <th scope="col" class="text-center" data-sort="class" width="15%">Lớp <i class="bx bx-sort-alt-2"></i></th>
                        <th scope="col" class="text-center" width="20%">Email</th>
                        <th scope="col" class="text-center" width="20%">Số điện thoại</th>
                        <th scope="col" class="text-center action-column">Hành động</th>
                    </tr>
                    </thead>
                    <tbody>
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
                                    <div class="d-flex justify-content-center">
                                        <div class="wrap-button m-1" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Đổi mật khẩu tài khoản">
                                            <a href="#" class="btn btn-sm btn-success my-auto" data-bs-toggle="modal" data-bs-target="#update-password-student-modal-{{ $student->id }}"><i class='bx bxs-key'></i></a>
                                        </div>
                                        <!----- Modal đổi mật khẩu sinh viên ----->
                                        <div class="modal fade" id="update-password-student-modal-{{ $student->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="updatePasswordStudentModalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h1 class="modal-title fs-5" id="exampleModalLabel">Đổi mật khẩu tài khoản sinh viên</h1>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form method="post" action="{{ route('technician.update-password-student-api', $student->id) }}" id="update-password-student-form-{{ $student->id }}">
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
                                                        <button type="submit" class="btn btn-primary btn-update-password-student" data-student-id="{{ $student->id }}">Xác nhận</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="wrap-button m-1" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Sửa thông tin sinh viên">
                                            <a href="#" class="btn btn-sm btn-primary my-auto" data-bs-toggle="modal" data-bs-target="#update-student-modal-{{ $student->id }}"><i class='bx bx-pencil'></i></a>
                                        </div>
                                        <!----- Modal sửa sinh viên ----->
                                        <div class="modal fade modal-update" id="update-student-modal-{{ $student->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="addStudentModalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h1 class="modal-title fs-5" id="exampleModalLabel">Chỉnh sửa thông tin sinh viên</h1>
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
                                        <div class="wrap-button m-1" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Xóa sinh viên">
                                            <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#destroy-student-modal-{{ $student->id }}"><i class='bx bx-trash'></i></button>
                                        </div>
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
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="7" class="text-center">Không có dữ liệu sinh viên</td>
                        </tr>
                    @endif
                    </tbody>
                </table>
                <div class="fw-bold skill-pagination" id="paginate-student">
                    {!! $students->render('pagination::bootstrap-5') !!}
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

            function submitFormCreateStudent (form, overlay) {
                const formDataObj = {};
                form.find('input, select, textarea').each(function() {
                    formDataObj[$(this).attr('name')] = $(this).val();
                });

                $.ajax({
                    type: 'POST',
                    data: JSON.stringify(formDataObj),
                    contentType: 'application/json',
                    url: '{{ route("technician.store-student-api") }}',
                    success: function (response) {
                        if (response.success) {
                            showToastSuccess(response.success);
                            form[0].reset();
                            $('#table-student tbody').html(response.table_student);
                            $('#paginate-student').html(response.links);

                            const currentUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
                            window.history.pushState({path: currentUrl}, '', currentUrl);
                            updatePagination();
                            addEventForModalUpdate();
                            addEventForButtons();
                            $('#add-student-modal').modal('hide');
                            $('body').css('overflow', 'auto');
                        } else {
                            if (response.errors['full-name']) {
                                showToastError(response.errors['full-name']);
                            }
                            if (response.errors['student-code']) {
                                showToastError(response.errors['student-code'])
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

            function submitFormUpdateStudent (form, studentId, overlay) {
                const formDataObj = Object.fromEntries(new FormData(form).entries());

                let url = `{{ route("technician.update-student-api", ":studentId") }}`;
                url = url.replace(':studentId', studentId);
                $.ajax({
                    type: 'PUT',
                    data: JSON.stringify(formDataObj),
                    contentType: 'application/json',
                    url: url,
                    success: function (response) {
                        if (response.success) {
                            showToastSuccess(response.success);
                            $('#table-student tbody').html(response.table_student);
                            $('#paginate-student').html(response.links);

                            const currentUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
                            window.history.pushState({path: currentUrl}, '', currentUrl);
                            updatePagination();
                            addEventForModalUpdate();
                            addEventForButtons();
                            $('#update-student-modal-' + studentId).modal('hide');
                            $('body').css('overflow', 'auto');
                        } else {
                            if (response.errors['full-name']) {
                                showToastError(response.errors['full-name']);
                            }
                            if (response.errors['student-code']) {
                                showToastError(response.errors['student-code'])
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

            function submitFormUpdatePasswordStudent(form, studentId, overlay) {
                const formDataObj = {};
                form.find('input, select, textarea').each(function() {
                    formDataObj[$(this).attr('name')] = $(this).val();
                });

                let url = `{{ route("technician.update-password-student-api", ":studentId") }}`;
                url = url.replace(':studentId', studentId);
                $.ajax({
                    type: 'PUT',
                    data: JSON.stringify(formDataObj),
                    contentType: 'application/json',
                    url: url,
                    success: function (response) {
                        if (response.success) {
                            showToastSuccess(response.success);
                            form[0].reset();
                            $('#update-password-student-modal-' + studentId).modal('hide');
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

            function submitFormDestroyStudent (studentId, overlay) {
                let url = `{{ route("technician.destroy-student-api", ":studentId") }}`;
                url = url.replace(':studentId', studentId);
                $.ajax({
                    type: 'DELETE',
                    contentType: 'application/json',
                    url: url,
                    success: function (response) {
                        if (response.success) {
                            showToastSuccess(response.success);
                            $('#table-student tbody').html(response.table_student);
                            $('#paginate-student').html(response.links);

                            const currentUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
                            window.history.pushState({path: currentUrl}, '', currentUrl);
                            updatePagination();
                            addEventForModalUpdate();
                            addEventForButtons();
                        } else {
                            if (response.errors['student']) {
                                showToastError(response.errors['student']);
                            }
                        }

                        $('#destroy-student-modal-' + studentId).modal('hide');
                        $('body').css('overflow', 'auto');
                        overlay.classList.remove('show');
                    },
                    error: function (error) {
                        console.error(error);
                    }
                });
            }

            function submitFormImportStudent (form, overlay) {
                const formData = new FormData(form[0]);
                $.ajax({
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    url: '{{ route("technician.import-student-api") }}',
                    success: function (response) {
                        if (response.success) {
                            showToastSuccess(response.success);
                            form[0].reset();
                            $('#table-student tbody').html(response.table_student);
                            $('#paginate-student').html(response.links);

                            const currentUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
                            window.history.pushState({path: currentUrl}, '', currentUrl);
                            updatePagination();
                            addEventForModalUpdate();
                            addEventForButtons();
                            $('#import-student-modal').modal('hide');
                            $('body').css('overflow', 'auto');
                        } else {
                            showToastError(response.errors['student-file']);
                            $('body').append('<div class="modal-backdrop fade show"></div>');
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
                        const newUrl = new URL(link.attr('href'), window.location.origin);
                        searchParams.set('page', newUrl.searchParams.get('page'));

                        const updatedUrl = currentPath + '?' + searchParams.toString();
                        link.attr('href', updatedUrl);
                    }
                });
            }

            $('#btn-add-student').click(function(e) {
                e.preventDefault();
                const overlay = document.getElementById('overlay');
                overlay.classList.add('show');
                $('.modal-backdrop').remove();

                const form = $('#add-student-form');
                submitFormCreateStudent(form, overlay);
            });

            $('#btn-import-student').click(function(e) {
                e.preventDefault();
                const overlay = document.getElementById('overlay');
                overlay.classList.add('show');
                $('.modal-backdrop').remove();

                const form = $('#import-student-form');
                submitFormImportStudent(form, overlay);
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

                $('.btn-update-student').off('click').click(function(e) {
                    e.preventDefault();
                    const overlay = document.getElementById('overlay');
                    overlay.classList.add('show');
                    $('.modal-backdrop').remove();

                    const studentId = $(this).data('student-id');
                    const form = $('#update-student-form-' + studentId);

                    submitFormUpdateStudent(form[0], studentId, overlay);
                });

                $('.btn-update-password-student').off('click').click(function(e) {
                    e.preventDefault();
                    const overlay = document.getElementById('overlay');
                    overlay.classList.add('show');
                    $('.modal-backdrop').remove();

                    const studentId = $(this).data('student-id');
                    const form = $('#update-password-student-form-' + studentId);

                    submitFormUpdatePasswordStudent(form, studentId, overlay);
                });

                $('.btn-destroy-student').off('click').click(function(e) {
                    e.preventDefault();
                    const overlay = document.getElementById('overlay');
                    overlay.classList.add('show');
                    $('.modal-backdrop').remove();

                    const studentId = $(this).data('student-id');

                    submitFormDestroyStudent(studentId, overlay);
                });

                $('th[data-sort]').off('click').click(function() {
                    const field = $(this).data('sort');
                    const order = $(this).hasClass('ascending') ? 'desc' : 'asc';

                    const currentUrl = new URL(window.location.href);
                    currentUrl.searchParams.set('sort-field', field);
                    currentUrl.searchParams.set('sort-order', order);
                    history.pushState(null, '', currentUrl.toString());

                    $.ajax({
                        url: `{{ route('technician.sort-student-api') }}`,
                        type: 'GET',
                        data: {sortField: field, sortOrder: order},
                        success: function(response) {
                            $('#table-student tbody').html(response.table_student);
                            $('#paginate-student').html(response.links);
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
            updatePagination();
        });
    </script>
@endsection
