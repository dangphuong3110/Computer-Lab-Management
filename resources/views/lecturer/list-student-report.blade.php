@extends('lecturer.layout')
@section('content')
    <div class="row m-5 mb-4 d-flex align-items-center">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="d-flex align-items-center">
                <div class="text">Xét duyệt báo cáo</div>
                <ol class="breadcrumb my-auto ms-4">
                    <li class="breadcrumb-item"><a href="{{ route('lecturer.index') }}">Trang chủ</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><a href="{{ route('lecturer.get-list-student-report') }}">Xét duyệt báo cáo</a></li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="row p-4 ms-5 me-5 mt-5 mb-5 main-content">
        <div class="col-12">
            <div class="d-flex mb-3">
                <div class="text fs-4">Danh sách báo cáo sự cố của sinh viên lớp học tiếp quản</div>
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
            </div>
            <div class="table-responsive" id="table-report">
                <table class="table table-bordered border-black">
                    <thead>
                    <tr>
                        <th scope="col" class="text-center" width="5%">STT</th>
                        <th scope="col" class="text-center" width="20%">Người gửi</th>
                        <th scope="col" class="text-center" width="30%">Nội dung báo cáo</th>
                        <th scope="col" class="text-center" data-sort="is_approved" width="20%">Trạng thái <i class="bx bx-sort-alt-2"></i></th>
                        <th scope="col" class="text-center" data-sort="submitted_at" width="15%">Ngày gửi <i class="bx bx-sort-alt-2"></i></th>
                        <th scope="col" class="text-center action-column">Hành động</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(count($reports) > 0)
                        @foreach($reports as $index => $report)
                            <tr>
                                <th scope="row" class="text-center">{{ $reports->firstItem() + $index }}</th>
                                <td class="text-center">
                                    Tên: {{ $report->student->full_name }}<br>
                                    Mã sinh viên: {{ $report->student->student_code }}
                                </td>
                                <td class="text-center">{{ $report->content }}</td>
                                <td class="text-center align-middle">
                                    <span class="p-1 rounded bg-opacity-75 {{ $report->is_approved ? 'bg-success' : 'bg-warning' }}">
                                        {{ $report->is_approved ? 'Đã duyệt' : 'Chưa duyệt' }}
                                    </span>
                                </td>
                                <td class="text-center align-middle">{{ \Carbon\Carbon::parse($report->submitted_at)->format('H:i:s d-m-Y') }}</td>
                                <td class="text-center align-middle">
                                    <div class="d-flex justify-content-center">
                                        <div class="wrap-button m-1" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Duyệt báo cáo">
                                            <button class="btn btn-sm btn-success my-auto approve-report" data-report-id="{{ $report->id }}" {{ $report->is_approved ? 'disabled' : '' }}><i class='bx bx-check-square'></i></button>
                                        </div>
                                        <div class="wrap-button m-1" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Hủy duyệt báo cáo">
                                            <button class="btn btn-sm btn-warning my-auto disapprove-report" data-report-id="{{ $report->id }}" {{ $report->is_approved ? '' : 'disabled' }}><i class='bx bx-no-entry'></i></button>
                                        </div>
                                        <div class="wrap-button m-1" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Xóa báo cáo">
                                            <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#destroy-student-report-modal-{{ $report->id }}"><i class='bx bx-trash'></i></button>
                                        </div>
                                        <form method="post" action="{{ route('lecturer.destroy-report-api', $report->id) }}" id="destroy-student-report-form-{{ $report->id }}">
                                            @csrf
                                            @method('DELETE')
                                            <!----- Modal xóa báo cáo sự cố ----->
                                            <div class="modal fade" id="destroy-student-report-modal-{{ $report->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="destroyStudentReportLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h1 class="modal-title fs-5" id="exampleModalLabel">Xóa báo cáo sự cố</h1>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p class="text-wrap m-0">Bạn có chắc chắn muốn xóa báo cáo sự cố?</p>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Trở về</button>
                                                            <button type="submit" class="btn btn-danger btn-destroy-report" data-report-id="{{ $report->id }}">Xóa</button>
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
                            <td colspan="6" class="text-center">Không có dữ liệu báo cáo sự cố</td>
                        </tr>
                    @endif
                    </tbody>
                </table>
                <div class="fw-bold skill-pagination" id="paginate-report">
                    {!! $reports->render('pagination::bootstrap-5') !!}
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

            function submitFormDestroyReport (reportId, overlay) {
                let url = `{{ route("lecturer.destroy-report-api", ":reportId") }}`;
                url = url.replace(':reportId', reportId);
                $.ajax({
                    type: 'DELETE',
                    contentType: 'application/json',
                    url: url,
                    data: JSON.stringify({'records-per-page': $('#records-per-page').val()}),
                    success: function (response) {
                        if (response.success) {
                            showToastSuccess(response.success);
                            $('#table-report tbody').html(response.table_report);
                            $('#paginate-report').html(response.links);

                            const currentUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
                            window.history.pushState({path: currentUrl}, '', currentUrl);
                            const newUrl = new URL(window.location.href);
                            newUrl.searchParams.set('records-per-page', $('#records-per-page').val());
                            history.pushState(null, '', newUrl.toString());
                            updatePagination();
                            addEventForButtons();
                            $('#destroy-student-report-modal-' + reportId).modal('hide');
                            $('body').css('overflow', 'auto');
                            $('body').css('padding', '0');
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
                    url: `{{ route('lecturer.change-records-per-page-report-api') }}`,
                    type: 'GET',
                    data: data,
                    success: function(response) {
                        $('#table-report tbody').html(response.table_report);
                        $('#paginate-report').html(response.links);
                        updatePagination();
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

                $('.btn-destroy-report').off('click').click(function(e) {
                    e.preventDefault();
                    const overlay = document.getElementById('overlay');
                    overlay.classList.add('show');
                    $('.modal-backdrop').remove();

                    const reportId = $(this).data('report-id');

                    submitFormDestroyReport(reportId, overlay);
                });

                $('.approve-report').off('click').click(function(e) {
                    e.preventDefault();
                    $('[data-bs-toggle="tooltip"]').tooltip('hide');
                    const overlay = document.getElementById('overlay');
                    overlay.classList.add('show');
                    $('.modal-backdrop').remove();

                    const reportId = $(this).data('report-id');

                    const recordsPerPage = $('#records-per-page').val();
                    const currentUrl = new URL(window.location.href);
                    const sortField = currentUrl.searchParams.get('sort-field');
                    const sortOrder = currentUrl.searchParams.get('sort-order');
                    const data = {};
                    if (sortField && sortOrder) {
                        data['sort-field'] = sortField;
                        data['sort-order'] = sortOrder;
                    }
                    data['records-per-page'] = recordsPerPage;

                    $.ajax({
                        type: 'PUT',
                        contentType: 'application/json',
                        url: `{{ route("lecturer.approve-report-api", ":reportId") }}`.replace(':reportId', reportId),
                        data: JSON.stringify(data),
                        success: function (response) {
                            if (response.success) {
                                showToastSuccess(response.success);
                                $('#table-report tbody').html(response.table_report);
                                $('#paginate-report').html(response.links);
                                updatePagination();
                                addEventForButtons();
                            }

                            overlay.classList.remove('show');
                        },
                        error: function (error) {
                            console.error(error);
                        }
                    });
                });

                $('.disapprove-report').off('click').click(function(e) {
                    e.preventDefault();
                    $('[data-bs-toggle="tooltip"]').tooltip('hide');
                    const overlay = document.getElementById('overlay');
                    overlay.classList.add('show');
                    $('.modal-backdrop').remove();

                    const reportId = $(this).data('report-id');

                    const recordsPerPage = $('#records-per-page').val();
                    const currentUrl = new URL(window.location.href);
                    const sortField = currentUrl.searchParams.get('sort-field');
                    const sortOrder = currentUrl.searchParams.get('sort-order');
                    const data = {};
                    if (sortField && sortOrder) {
                        data['sort-field'] = sortField;
                        data['sort-order'] = sortOrder;
                    }
                    data['records-per-page'] = recordsPerPage;

                    $.ajax({
                        type: 'PUT',
                        contentType: 'application/json',
                        url: `{{ route("lecturer.disapprove-report-api", ":reportId") }}`.replace(':reportId', reportId),
                        data: JSON.stringify(data),
                        success: function (response) {
                            if (response.success) {
                                showToastSuccess(response.success);
                                $('#table-report tbody').html(response.table_report);
                                $('#paginate-report').html(response.links);
                                updatePagination();
                                addEventForButtons();
                            }

                            overlay.classList.remove('show');
                        },
                        error: function (error) {
                            console.error(error);
                        }
                    });
                });

                $('th[data-sort]').off('click').click(function() {
                    const field = $(this).data('sort');
                    const order = $(this).hasClass('ascending') ? 'desc' : 'asc';

                    const currentUrl = new URL(window.location.href);
                    currentUrl.searchParams.set('sort-field', field);
                    currentUrl.searchParams.set('sort-order', order);
                    history.pushState(null, '', currentUrl.toString());

                    $.ajax({
                        url: `{{ route('lecturer.sort-student-report-api') }}`,
                        type: 'GET',
                        data: {sortField: field, sortOrder: order, recordsPerPage: $('#records-per-page').val()},
                        success: function(response) {
                            $('#table-report tbody').html(response.table_report);
                            $('#paginate-report').html(response.links);
                            updatePagination();
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

            addEventForButtons();
            updatePagination();
        });
    </script>
@endsection
