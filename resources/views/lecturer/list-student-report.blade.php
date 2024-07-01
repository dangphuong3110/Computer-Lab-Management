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
    <div class="row p-4 ms-5 me-5 mt-5 mb-0 main-content">
        <div class="col-12">
            <div class="d-flex mb-3">
                <div class="text fs-4">Danh sách báo cáo sự cố của sinh viên lớp học tiếp quản</div>
            </div>
            <div class="table-responsive" id="table-report">
                <table class="table table-bordered border-black">
                    <thead>
                    <tr>
                        <th scope="col" class="text-center" width="5%">STT</th>
                        <th scope="col" class="text-center" width="20%">Người gửi</th>
                        <th scope="col" class="text-center" width="30%">Nội dung báo cáo</th>
                        <th scope="col" class="text-center" width="20%">Trạng thái</th>
                        <th scope="col" class="text-center" width="15%">Ngày gửi</th>
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
                                            <!----- Modal xóa giảng viên ----->
                                            <div class="modal fade" id="destroy-student-report-modal-{{ $report->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="destroyStudentReportLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h1 class="modal-title fs-5" id="exampleModalLabel">Xóa báo cáo</h1>
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
                    success: function (response) {
                        if (response.success) {
                            showToastSuccess(response.success);
                            $('#table-report tbody').html(response.table_report);
                            $('#paginate-report').html(response.links);
                            updatePagination();
                            addEventForButtons();
                            $('#destroy-student-report-modal-' + reportId).modal('hide');
                            $('body').css('overflow', 'auto');
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

            function addEventForButtons () {
                const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
                const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));

                $('.btn-destroy-lecturer').off('click');
                $('.approve-report').off('click');
                $('.disapprove-report').off('click');
                $('.close-btn').off('click');

                $('.btn-destroy-report').click('click', function(e) {
                    e.preventDefault();
                    const overlay = document.getElementById('overlay');
                    overlay.classList.add('show');
                    $('.modal-backdrop').remove();

                    const reportId = $(this).data('report-id');

                    submitFormDestroyReport(reportId, overlay);
                });

                $('.approve-report').click(function(e) {
                    e.preventDefault();
                    $('[data-bs-toggle="tooltip"]').tooltip('hide');
                    const overlay = document.getElementById('overlay');
                    overlay.classList.add('show');
                    $('.modal-backdrop').remove();

                    const reportId = $(this).data('report-id');

                    $.ajax({
                        type: 'PUT',
                        contentType: 'application/json',
                        url: `{{ route("lecturer.approve-report-api", ":reportId") }}`.replace(':reportId', reportId),
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

                $('.disapprove-report').click(function(e) {
                    e.preventDefault();
                    $('[data-bs-toggle="tooltip"]').tooltip('hide');
                    const overlay = document.getElementById('overlay');
                    overlay.classList.add('show');
                    $('.modal-backdrop').remove();

                    const reportId = $(this).data('report-id');

                    $.ajax({
                        type: 'PUT',
                        contentType: 'application/json',
                        url: `{{ route("lecturer.disapprove-report-api", ":reportId") }}`.replace(':reportId', reportId),
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

                $('.close-btn').click(function() {
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
        });
    </script>
@endsection
