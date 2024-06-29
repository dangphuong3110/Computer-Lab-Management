@extends('technician.layout')
@section('content')
    <div class="row m-5 mb-4 d-flex align-items-center">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="d-flex align-items-center">
                <div class="text" style="min-width: 310px;">Sinh viên lớp học phần</div>
                <ol class="breadcrumb my-auto ms-4" style="min-width: 375px;">
                    <li class="breadcrumb-item"><a href="{{ route('technician.index') }}">Trang chủ</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('technician.get-list-class') }}">Lớp học phần</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><a href="{{ route('technician.get-list-student') }}">Sinh viên lớp học phần</a></li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="row p-4 ms-5 me-5 mt-5 mb-0 main-content">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="text fs-4">Danh sách sinh viên lớp:<br> {{ $class->name }}</div>
                <div class="d-flex justify-content-end">
                    <a href="#" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#add-student-modal">Thêm</a>
                    <!----- Modal thêm sinh viên ----->
                    <div class="modal fade" id="add-student-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="addStudentModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="exampleModalLabel">Thêm sinh viên vào lớp học phần</h1>
                                </div>
                                <div class="modal-body">
                                    <form method="post" action="{{ route('technician.store-student-api') }}" id="add-student-form">
                                        @csrf
                                        <div class="row mb-3 mt-4">
                                            <label class="col-md-4 col-label-form fs-6 fw-bold text-md-end">Mã sinh viên<span class="required">*</span></label>
                                            <div class="col-md-6">
                                                <input type="text" name="student-code" class="form-control fs-6"/>
                                            </div>
                                            <div class="col-md-2 mt-md-0 mt-2">
                                                <a href="#" class="btn btn-outline-success" id="btn-search-student"><i class='bx bx-search-alt-2' ></i></a>
                                            </div>
                                            <div class="info-student mt-3 text-center">

                                            </div>
                                        </div>
                                    </form>
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
            <div class="table-responsive" id="table-student-class">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th scope="col" class="text-center" width="5%">STT</th>
                        <th scope="col" class="text-center" width="15%">Họ và tên</th>
                        <th scope="col" class="text-center" width="15%">Mã sinh viên</th>
                        <th scope="col" class="text-center" width="15%">Lớp</th>
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
                                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#destroy-student-class-modal-{{ $student->id }}"><i class='bx bx-trash'></i></button>
                                    <form method="post" action="{{ route('technician.destroy-student-class-api', $student->id) }}" id="destroy-student-class-form-{{ $student->id }}">
                                        @csrf
                                        @method('DELETE')
                                        <!----- Modal xóa sinh viên ----->
                                        <div class="modal fade" id="destroy-student-class-modal-{{ $student->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="destroyStudentClassLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h1 class="modal-title fs-5" id="exampleModalLabel">Xóa sinh viên khỏi lớp học phần</h1>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p class="text-wrap m-0">Bạn có chắc chắn muốn xóa sinh viên ra khỏi lớp học phần?</p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Trở về</button>
                                                        <button type="submit" class="btn btn-danger btn-destroy-student-class" data-student-id="{{ $student->id }}">Xóa</button>
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
                            <td colspan="7" class="text-center">Không có dữ liệu sinh viên trong lớp học phần</td>
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

            function submitFormCreateStudentClass (form, overlay) {
                const formDataObj = {};
                form.find('input, select, textarea').each(function() {
                    formDataObj[$(this).attr('name')] = $(this).val();
                });

                formDataObj['class_id'] = '{{ $class->id }}';

                $.ajax({
                    type: 'POST',
                    data: JSON.stringify(formDataObj),
                    contentType: 'application/json',
                    url: '{{ route("technician.store-student-class-api") }}',
                    success: function (response) {
                        if (response.success) {
                            showToastSuccess(response.success);
                            form[0].reset();
                            $('#table-student-class tbody').html(response.table_student_class);
                            $('#paginate-student').html(response.links);
                            updatePagination();
                            addEventForModalUpdate();
                            addEventForButtons();
                            $('.info-student').html('');
                            $('#add-student-modal').modal('hide');
                            $('body').css('overflow', 'auto');
                        } else {
                            if (response.errors['student-code']) {
                                showToastError(response.errors['student-code'])
                            }
                            if (response.errors['student-class']) {
                                showToastError(response.errors['student-class']);
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

            function submitFormSearchStudent (form, overlay) {
                const formDataObj = {};
                form.find('input, select, textarea').each(function() {
                    formDataObj[$(this).attr('name')] = $(this).val();
                });

                $.ajax({
                    type: 'POST',
                    data: JSON.stringify(formDataObj),
                    contentType: 'application/json',
                    url: '{{ route("technician.search-student-api") }}',
                    success: function (response) {
                        if (response.success) {
                            showToastSuccess(response.success);
                            const infoStudent = `
                                    <p class="m-0">Thông tin sinh viên:</p>
                                    <p class="m-0">Họ và tên: ${response.student.full_name}</p>
                                    <p class="m-0">Lớp: ${response.student.class}</p>
                                `;
                            $('.info-student').html(infoStudent);
                        } else {
                            if (response.errors['student-code']) {
                                showToastError(response.errors['student-code'])
                            }
                            $('.info-student').html('');
                        }

                        $('body').append('<div class="modal-backdrop fade show"></div>');
                        overlay.classList.remove('show');
                    },
                    error: function (error) {
                        console.error(error);
                    }
                });
            }

            function submitFormDestroyStudentClass (studentId, overlay) {
                const formDataObj = {};

                formDataObj['class_id'] = '{{ $class->id }}';

                let url = `{{ route("technician.destroy-student-class-api", ":studentId") }}`;
                url = url.replace(':studentId', studentId);
                $.ajax({
                    type: 'DELETE',
                    data: JSON.stringify(formDataObj),
                    contentType: 'application/json',
                    url: url,
                    success: function (response) {
                        if (response.success) {
                            showToastSuccess(response.success);
                            $('#table-student-class tbody').html(response.table_student_class);
                            $('#paginate-student').html(response.links);
                            updatePagination();
                            addEventForModalUpdate();
                            addEventForButtons();
                            $('#destroy-student-modal-' + studentId).modal('hide');
                            $('body').css('overflow', 'auto');
                        }

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
                        const newUrl = new URL(link.attr('href'));
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
                submitFormCreateStudentClass(form, overlay);
            });

            $('#btn-search-student').click(function(e) {
                e.preventDefault();
                const overlay = document.getElementById('overlay');
                overlay.classList.add('show');
                $('.modal-backdrop').remove();

                const form = $('#add-student-form');
                submitFormSearchStudent(form, overlay);
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
                $('.btn-destroy-student').off('click');
                $('.close-btn').off('click');

                $('.btn-destroy-student-class').click('click', function(e) {
                    e.preventDefault();
                    const overlay = document.getElementById('overlay');
                    overlay.classList.add('show');
                    $('.modal-backdrop').remove();

                    const studentId = $(this).data('student-id');

                    submitFormDestroyStudentClass(studentId, overlay);
                });

                $('.close-btn').click(function() {
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
