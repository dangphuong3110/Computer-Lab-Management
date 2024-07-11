@extends('manager.layout')
@section('content')
    <div class="row m-5 mb-4 d-flex align-items-center">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="d-flex align-items-center">
                <div class="text">Quản lý tài khoản</div>
                <ol class="breadcrumb my-auto ms-4">
                    <li class="breadcrumb-item"><a href="{{ route('manager.index') }}">Trang chủ</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><a href="{{ route('manager.get-list-technician') }}">Quản lý tài khoản</a></li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="row p-4 ms-5 me-5 mt-5 mb-5 main-content">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="text fs-4">Danh sách kỹ thuật viên</div>
                <div class="d-flex justify-content-end">
                    <a href="#" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#add-technician-modal">Thêm</a>
                    <!----- Modal thêm kỹ thuật viên ----->
                    <div class="modal fade" id="add-technician-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="addTechnicianModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="exampleModalLabel">Thêm kỹ thuật viên</h1>
                                </div>
                                <div class="modal-body">
                                    <form method="post" action="{{ route('manager.store-technician-api') }}" id="add-technician-form">
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
                                    </form>
                                </div>
                                <div class="text-center">
                                    <p class="ps-3 pe-3 attention">*Chú ý: Tài khoản sẽ được tạo tự động với tên đăng nhập là <span>Email</span> và mật khẩu là <span>123456</span></p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary close-btn" data-bs-dismiss="modal">Đóng</button>
                                    <button type="button" id="btn-add-technician" class="btn btn-primary">Thêm</button>
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
                <div class="col-5 d-flex align-items-center justify-content-end">
                    <input class="form-control border-black me-2" type="search" id="search-input" placeholder="Tìm kiếm" style="min-width: 130px; max-width: 160px;">
                    <button class="btn btn-outline-dark" type="submit" id="search-button"><i class='bx bx-search-alt'></i></button>
                </div>
            </div>
            <div class="table-responsive" id="table-technician">
                <table class="table table-bordered border-black">
                    <thead>
                    <tr>
                        <th scope="col" class="text-center" width="5%">STT</th>
                        <th scope="col" class="text-center" data-sort="full_name" width="15%">Họ và tên <i class="bx bx-sort-alt-2"></i></th>
                        <th scope="col" class="text-center" width="15%">Email</th>
                        <th scope="col" class="text-center" width="15%">Số điện thoại</th>
                        <th scope="col" class="text-center" data-sort="report_count" width="17%">Báo cáo đã xử lý <i class="bx bx-sort-alt-2"></i></th>
                        <th scope="col" class="text-center" data-sort="avg_processing_time" width="18%">TG xử lý trung bình <i class="bx bx-sort-alt-2"></i></th>
                        <th scope="col" class="text-center action-column">Hành động</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(count($technicians) > 0)
                        @foreach($technicians as $index => $technician)
                            <tr>
                                <th scope="row" class="text-center">{{ $technicians->firstItem() + $index }}</th>
                                <td class="text-center">{{ $technician->full_name }}</td>
                                <td class="text-center">{{ $technician->user->email }}</td>
                                <td class="text-center">{{ $technician->user->phone }}</td>
                                <td class="text-center">{{ $technician->report_count ?? 0 }}</td>
                                <td class="text-center">{{ $technician->avg_processing_time ?? 0 }}</td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center">
                                        <div class="wrap-button m-1" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Đổi mật khẩu tài khoản">
                                            <a href="#" class="btn btn-sm btn-success my-auto" data-bs-toggle="modal" data-bs-target="#update-password-technician-modal-{{ $technician->id }}"><i class='bx bxs-key'></i></a>
                                        </div>
                                        <!----- Modal đổi mật khẩu kỹ thuật viên ----->
                                        <div class="modal fade" id="update-password-technician-modal-{{ $technician->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="updatePasswordTechnicianModalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h1 class="modal-title fs-5" id="exampleModalLabel">Đổi mật khẩu tài khoản kỹ thuật viên</h1>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form method="post" action="{{ route('manager.update-password-technician-api', $technician->id) }}" id="update-password-technician-form-{{ $technician->id }}">
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
                                                        <button type="submit" class="btn btn-primary btn-update-password-technician" data-technician-id="{{ $technician->id }}">Xác nhận</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="wrap-button m-1" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Sửa thông tin kỹ thuật viên">
                                            <a href="#" class="btn btn-sm btn-primary my-auto" data-bs-toggle="modal" data-bs-target="#update-technician-modal-{{ $technician->id }}"><i class='bx bx-pencil'></i></a>
                                        </div>
                                        <!----- Modal sửa kỹ thuật viên ----->
                                        <div class="modal fade modal-update" id="update-technician-modal-{{ $technician->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="addTechnicianModalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h1 class="modal-title fs-5" id="exampleModalLabel">Chỉnh sửa thông tin kỹ thuật viên</h1>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form method="post" action="{{ route('manager.update-technician-api', $technician->id) }}" id="update-technician-form-{{ $technician->id }}">
                                                            @csrf
                                                            @method('PUT')
                                                            <div class="row mb-3 mt-4">
                                                                <label class="col-md-4 col-label-form fs-6 fw-bold text-md-end">Họ và tên<span class="required">*</span></label>
                                                                <div class="col-md-7">
                                                                    <input type="text" name="full-name" class="form-control fs-6" value="{{ $technician->full_name }}" data-initial-value="{{ $technician->full_name }}"/>
                                                                </div>
                                                            </div>
                                                            <div class="row mb-3 mt-4">
                                                                <label class="col-md-4 col-label-form fs-6 fw-bold text-md-end">Email<span class="required">*</span></label>
                                                                <div class="col-md-7">
                                                                    <input type="text" name="email" class="form-control fs-6" value="{{ $technician->user->email }}" data-initial-value="{{ $technician->user->email }}"/>
                                                                </div>
                                                            </div>
                                                            <div class="row mb-3 mt-4">
                                                                <label class="col-md-4 col-label-form fs-6 fw-bold text-md-end">Số điện thoại</label>
                                                                <div class="col-md-7">
                                                                    <input type="text" name="phone" class="form-control fs-6" value="{{ $technician->user->phone }}" data-initial-value="{{ $technician->user->phone }}"/>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary close-update-btn" data-technician-id="{{ $technician->id }}" data-bs-dismiss="modal">Đóng</button>
                                                        <button type="button" class="btn btn-primary btn-update-technician" data-technician-id="{{ $technician->id }}">Lưu</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="wrap-button m-1" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Xóa kỹ thuật viên">
                                            <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#destroy-technician-modal-{{ $technician->id }}"><i class='bx bx-trash'></i></button>
                                        </div>
                                        <form method="post" action="{{ route('manager.destroy-technician-api', $technician->id) }}" id="destroy-technician-form-{{ $technician->id }}">
                                            @csrf
                                            @method('DELETE')
                                            <!----- Modal xóa kỹ thuật viên ----->
                                            <div class="modal fade" id="destroy-technician-modal-{{ $technician->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="destroyTechnicianLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h1 class="modal-title fs-5" id="exampleModalLabel">Xóa kỹ thuật viên</h1>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p class="text-wrap m-0">Bạn có chắc chắn muốn xóa kỹ thuật viên?<br> Điều này sẽ dẫn đến tài khoản và những báo cáo sự cố đã xử lý của kỹ thuật viên này cũng sẽ không còn tồn tại trong hệ thống.</p>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Trở về</button>
                                                            <button type="submit" class="btn btn-danger btn-destroy-technician" data-technician-id="{{ $technician->id }}">Xóa</button>
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
                            <td colspan="7" class="text-center">Không có dữ liệu kỹ thuật viên</td>
                        </tr>
                    @endif
                    </tbody>
                </table>
                <div class="fw-bold skill-pagination" id="paginate-technician">
                    {!! $technicians->render('pagination::bootstrap-5') !!}
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

            function submitFormCreateTechnician (form, overlay) {
                const formDataObj = {};
                form.find('input, select, textarea').each(function() {
                    formDataObj[$(this).attr('name')] = $(this).val();
                });

                formDataObj['records-per-page'] = $('#records-per-page').val();

                $.ajax({
                    type: 'POST',
                    data: JSON.stringify(formDataObj),
                    contentType: 'application/json',
                    url: '{{ route("manager.store-technician-api") }}',
                    success: function (response) {
                        if (response.success) {
                            showToastSuccess(response.success);
                            form[0].reset();
                            $('#table-technician tbody').html(response.table_technician);
                            $('#paginate-technician').html(response.links);

                            const currentUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
                            window.history.pushState({path: currentUrl}, '', currentUrl);
                            const newUrl = new URL(window.location.href);
                            newUrl.searchParams.set('records-per-page', $('#records-per-page').val());
                            history.pushState(null, '', newUrl.toString());
                            updatePagination();
                            addEventForModalUpdate();
                            addEventForButtons();
                            $('#add-technician-modal').modal('hide');
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

            function submitFormUpdateTechnician (form, technicianId, overlay) {
                const formDataObj = Object.fromEntries(new FormData(form).entries());
                formDataObj['records-per-page'] = $('#records-per-page').val();

                let url = `{{ route("manager.update-technician-api", ":technicianId") }}`;
                url = url.replace(':technicianId', technicianId);
                $.ajax({
                    type: 'PUT',
                    data: JSON.stringify(formDataObj),
                    contentType: 'application/json',
                    url: url,
                    success: function (response) {
                        if (response.success) {
                            showToastSuccess(response.success);
                            $('#table-technician tbody').html(response.table_technician);
                            $('#paginate-technician').html(response.links);

                            const currentUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
                            window.history.pushState({path: currentUrl}, '', currentUrl);
                            const newUrl = new URL(window.location.href);
                            newUrl.searchParams.set('records-per-page', $('#records-per-page').val());
                            history.pushState(null, '', newUrl.toString());
                            updatePagination();
                            addEventForModalUpdate();
                            addEventForButtons();
                            $('#update-technician-modal-' + technicianId).modal('hide');
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

            function submitFormUpdatePasswordTechnician(form, technicianId, overlay) {
                const formDataObj = {};
                form.find('input, select, textarea').each(function() {
                    formDataObj[$(this).attr('name')] = $(this).val();
                });

                let url = `{{ route("manager.update-password-technician-api", ":technicianId") }}`;
                url = url.replace(':technicianId', technicianId);
                $.ajax({
                    type: 'PUT',
                    data: JSON.stringify(formDataObj),
                    contentType: 'application/json',
                    url: url,
                    success: function (response) {
                        if (response.success) {
                            showToastSuccess(response.success);
                            form[0].reset();
                            $('#update-password-technician-modal-' + technicianId).modal('hide');
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

            function submitFormDestroyTechnician (technicianId, overlay) {
                let url = `{{ route("manager.destroy-technician-api", ":technicianId") }}`;
                url = url.replace(':technicianId', technicianId);
                $.ajax({
                    type: 'DELETE',
                    contentType: 'application/json',
                    url: url,
                    data: JSON.stringify({'records-per-page': $('#records-per-page').val()}),
                    success: function (response) {
                        if (response.success) {
                            showToastSuccess(response.success);
                            $('#table-technician tbody').html(response.table_technician);
                            $('#paginate-technician').html(response.links);

                            const currentUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
                            window.history.pushState({path: currentUrl}, '', currentUrl);
                            updatePagination();
                            addEventForModalUpdate();
                            addEventForButtons();
                        } else {
                            if (response.errors['technician']) {
                                showToastError(response.errors['technician']);
                            }
                        }

                        $('#destroy-technician-modal-' + technicianId).modal('hide');
                        $('body').css('overflow', 'auto');
                        $('body').css('padding', '0');
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

            $('#btn-add-technician').click(function(e) {
                e.preventDefault();
                const overlay = document.getElementById('overlay');
                overlay.classList.add('show');
                $('.modal-backdrop').remove();

                const form = $('#add-technician-form');
                submitFormCreateTechnician(form, overlay);
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
                    url: `{{ route('manager.change-records-per-page-technician-api') }}`,
                    type: 'GET',
                    data: data,
                    success: function(response) {
                        $('#table-technician tbody').html(response.table_technician);
                        $('#paginate-technician').html(response.links);
                        updatePagination();
                        addEventForModalUpdate();
                        addEventForButtons();
                        overlay.classList.remove('show');
                    }
                });
            });

            $('#search-button').click(function() {
                const query = $('#search-input').val();
                const recordsPerPage = $('#records-per-page').val();
                const currentUrl = new URL(window.location.href);
                const sortField = currentUrl.searchParams.get('sort-field');
                const sortOrder = currentUrl.searchParams.get('sort-order');
                const data = {};
                if (sortField && sortOrder) {
                    data['sortField'] = sortField;
                    data['sortOrder'] = sortOrder;
                }
                data['recordsPerPage'] = recordsPerPage;
                data['query'] = query;

                $.ajax({
                    url: `{{ route('manager.search-technician-api') }}`,
                    type: 'GET',
                    data: data,
                    success: function(response) {
                        $('#table-technician tbody').html(response.table_technician);
                        $('#paginate-technician').html(response.links);
                        updatePagination();
                        addEventForModalUpdate();
                        addEventForButtons();
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

                $('.btn-update-technician').off('click').click(function(e) {
                    e.preventDefault();
                    const overlay = document.getElementById('overlay');
                    overlay.classList.add('show');
                    $('.modal-backdrop').remove();

                    const technicianId = $(this).data('technician-id');
                    const form = $('#update-technician-form-' + technicianId);

                    submitFormUpdateTechnician(form[0], technicianId, overlay);
                });

                $('.btn-update-password-technician').off('click').click(function(e) {
                    e.preventDefault();
                    const overlay = document.getElementById('overlay');
                    overlay.classList.add('show');
                    $('.modal-backdrop').remove();

                    const technicianId = $(this).data('technician-id');
                    const form = $('#update-password-technician-form-' + technicianId);

                    submitFormUpdatePasswordTechnician(form, technicianId, overlay);
                });

                $('.btn-destroy-technician').off('click').click(function(e) {
                    e.preventDefault();
                    const overlay = document.getElementById('overlay');
                    overlay.classList.add('show');
                    $('.modal-backdrop').remove();

                    const technicianId = $(this).data('technician-id');

                    submitFormDestroyTechnician(technicianId, overlay);
                });

                $('th[data-sort]').off('click').click(function() {
                    const field = $(this).data('sort');
                    const order = $(this).hasClass('ascending') ? 'desc' : 'asc';

                    const currentUrl = new URL(window.location.href);
                    currentUrl.searchParams.set('sort-field', field);
                    currentUrl.searchParams.set('sort-order', order);
                    history.pushState(null, '', currentUrl.toString());

                    $.ajax({
                        url: `{{ route('manager.sort-technician-api') }}`,
                        type: 'GET',
                        data: {sortField: field, sortOrder: order, recordsPerPage: $('#records-per-page').val()},
                        success: function(response) {
                            console.log(response);
                            $('#table-technician tbody').html(response.table_technician);
                            $('#paginate-technician').html(response.links);
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
