@extends('lecturer.layout')
@section('content')
    <div class="row m-5 mb-4 d-flex align-items-center">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="d-flex align-items-center">
                <div class="text">Buổi học</div>
                <ol class="breadcrumb my-auto ms-4">
                    <li class="breadcrumb-item"><a href="{{ route('lecturer.index') }}">Trang chủ</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('lecturer.get-list-class-session') }}">Thời khóa biểu</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><a href="{{ route('lecturer.get-class-session', $classSession->id) }}">Buổi học</a></li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="row p-4 ms-5 me-5 mt-5 mb-5 main-content">
        <div class="col-12">
            <div class="mb-3 d-flex justify-content-between align-items-center">
                <div>
                    <div class="text fs-4">Sơ đồ phòng máy: {{ $room->name . ' - ' . $building->name }}</div>
                    <span class="text fs-5">Lớp học phần: {{ $classSession->creditClass->name }}</span>
                </div>
                <div class="row" style="min-width: 295px;">
                    <div class="col-12 text-end">
                        <a href="#" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#send-report-modal" style="width: 163px;">Báo cáo sự cố</a>
                        <!----- Modal gửi báo cáo sự cố thiết bị ----->
                        <div class="modal fade" id="send-report-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="sendReportModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title fs-5" id="exampleModalLabel">Gửi báo cáo sự cố thiết bị</h1>
                                    </div>
                                    <div class="modal-body">
                                        <form method="post" action="{{ route('lecturer.send-report-api', $classSession->room_id) }}" id="send-report-form">
                                            @csrf
                                            <div class="row mb-3 mt-4">
                                                <label class="col-12 col-label-form fs-6 fw-bold text-start">Nội dung báo cáo<span class="required">*</span></label>
                                                <div class="col-12">
                                                    <textarea class="w-100" name="content" rows="10"></textarea>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary close-btn" data-bs-dismiss="modal">Đóng</button>
                                        <button type="button" id="btn-send-report" class="btn btn-primary" data-room-id="{{ $classSession->room_id }}">Gửi</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <a href="#" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#view-report-history-modal" style="width: 163px;">Báo cáo đã gửi</a>
                        <!----- Modal xem lịch sử báo cáo sự cố ----->
                        <div class="modal fade" id="view-report-history-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="viewReportHistoryModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-xl">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title fs-5" id="exampleModalLabel">Lịch sử gửi báo cáo sự cố</h1>
                                    </div>
                                    <div class="modal-body table-responsive">
                                        <table class="table table-bordered border-black">
                                            <thead>
                                            <tr>
                                                <th scope="col" class="text-center" width="5%">STT</th>
                                                <th scope="col" class="text-center" width="30%">Nội dung</th>
                                                <th scope="col" class="text-center" data-sort="status" width="30%">Trạng thái xử lý <i class="bx bx-sort-alt-2"></i></th>
                                                <th scope="col" class="text-center" data-sort="submitted_at" width="20%">Thời gian gửi <i class="bx bx-sort-alt-2"></i></th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @if(count($reports) > 0)
                                                @foreach($reports as $index => $report)
                                                    <tr>
                                                        <th scope="row" class="text-center">{{ $index + 1 }}</th>
                                                        <td class="text-center">{{ $report->content }}</td>
                                                        <td class="text-center align-middle">
                                                            <span class="p-1 rounded bg-opacity-75
                                                                @switch($report->status)
                                                                    @case('pending')
                                                                        bg-warning
                                                                        @break
                                                                    @case('processing')
                                                                        bg-primary
                                                                        @break
                                                                    @case('processed')
                                                                        bg-success
                                                                        @break
                                                                    @default
                                                                        bg-secondary
                                                                @endswitch">
                                                                @switch($report->status)
                                                                    @case('pending')
                                                                        Chờ xử lý
                                                                        @break
                                                                    @case('processing')
                                                                        Đang xử lý
                                                                        @break
                                                                    @case('processed')
                                                                        Đã xử lý
                                                                        @break
                                                                    @default
                                                                        Không xác định
                                                                @endswitch
                                                            </span>
                                                        </td>
                                                        <td class="text-center align-middle">{{ $report->created_at->format('H:m:i d-m-Y') }}</td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="4" class="text-center">Không có dữ liệu báo cáo sự cố</td>
                                                </tr>
                                            @endif
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary close-btn" data-bs-dismiss="modal">Đóng</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 text-end mt-1">
                        <a href="{{ route('lecturer.export-attendances', $classSession->class_id) }}" class="btn btn-outline-success btn-export-attendances">Xuất file điểm danh</a>
                    </div>
                </div>
            </div>
            <div class="row border border-black ms-0 me-0" id="table-computer">
                @php
                    $computerNumber = 1;
                @endphp
                @for ($i = 1; $i <= $room->number_of_computer_rows * $room->max_computers_per_row; $i++)
                    @if ($i % $room->max_computers_per_row == 1)
                        <div class="col-12 d-flex justify-content-start p-0">
                            @endif
                            @php
                                $computerAtPosition = $computers->firstWhere('position', $i);
                                if ($computerAtPosition) {
                                    $attendance = $attendances->firstWhere('computer_id', $computerAtPosition->id);
                                    $hasAttendance = $attendance != null;
                                }
                            @endphp
                            @if ($computerAtPosition)
                                <div class="position-relative border border-black {{ $hasAttendance ? 'bg-warning' : 'bg-info' }} bg-opacity-50" style="width: {{ 100 / $room->max_computers_per_row }}%; height: 100px;">
                                    <div class="text-center d-flex justify-content-center align-items-center overflow-hidden h-100">
                                        <span style="font-size: 12px;">{!! $hasAttendance ? $attendance->student->full_name . '<br>' . \Carbon\Carbon::parse($attendance->updated_at)->format('H:i:s') : '' !!}</span>
                                    </div>
                                    <div class="position-absolute top-0">{{ $computerNumber }}</div>
                                </div>
                                @php
                                    $computerNumber++;
                                @endphp
                            @else
                                <div class="border border-black bg-secondary" style="width: {{ 100 / $room->max_computers_per_row }}%; height: 100px;">

                                </div>
                            @endif
                            @if ($i % $room->max_computers_per_row == 0 || $i == $room->number_of_computer_rows * $room->max_computers_per_row)
                        </div>
                    @endif
                @endfor
            </div>
            <div class="row note mt-3">
                <div class="col-12 d-flex align-items-center">
                    <div class="border border-black bg-info bg-opacity-50" style="width: 18px; height: 18px"></div>
                    <span class="ms-2">Máy chưa sử dụng</span>
                </div>
                <div class="col-12 d-flex align-items-center">
                    <div class="border border-black bg-warning bg-opacity-50" style="width: 18px; height: 18px"></div>
                    <span class="ms-2">Máy đã sử dụng</span>
                </div>
                <div class="col-12 d-flex align-items-center">
                    <div class="border border-black bg-secondary d-flex justify-content-center align-items-center" style="width: 18px; height: 18px"></div>
                    <span class="ms-2">Vị trí trống</span>
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

            function submitFormSendReport(form, roomId, overlay) {
                const formDataObj = {};
                form.find('input, select, textarea').each(function() {
                    formDataObj[$(this).attr('name')] = $(this).val();
                });

                let url = `{{ route("lecturer.send-report-api", ":roomId") }}`;
                url = url.replace(':roomId', roomId);
                $.ajax({
                    type: 'POST',
                    data: JSON.stringify(formDataObj),
                    contentType: 'application/json',
                    url: url,
                    success: function (response) {
                        if (response.success) {
                            showToastSuccess(response.success);
                            form[0].reset();
                            $('#view-report-history-modal').html(response.table_report);
                            $('#send-report-modal').modal('hide');
                            $('body').css('overflow', 'auto');
                            $('body').css('padding', '0');
                        } else {
                            if (response.errors['content']) {
                                showToastError(response.errors['content'])
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

            $('#btn-send-report').click(function(e) {
                e.preventDefault();
                const overlay = document.getElementById('overlay');
                overlay.classList.add('show');
                $('.modal-backdrop').remove();

                const roomId = $(this).data('room-id');
                const form = $('#send-report-form');

                submitFormSendReport(form, roomId, overlay);
            });

            $('.btn-export-attendances').click(function(e) {
                e.preventDefault();
                setTimeout(() => {
                    const overlay = document.getElementById('overlay');
                    overlay.classList.remove('show');
                }, 1500);
                window.location.href = $(this).attr('href');
            });

            function addEventForButtons() {
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

                $('th[data-sort]').off('click').click(function() {
                    const field = $(this).data('sort');
                    const order = $(this).hasClass('ascending') ? 'desc' : 'asc';

                    $.ajax({
                        url: `{{ route('lecturer.sort-report-api') }}`,
                        type: 'GET',
                        data: {sortField: field, sortOrder: order},
                        success: function(response) {
                            $('#view-report-history-modal').html(response.table_report);
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
        });
    </script>
@endsection
