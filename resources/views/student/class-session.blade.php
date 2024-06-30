@extends('student.layout')
@section('content')
    <div class="row m-5 mb-4 d-flex align-items-center">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="d-flex align-items-center">
                <div class="text">Buổi học</div>
                <ol class="breadcrumb my-auto ms-4">
                    <li class="breadcrumb-item"><a href="{{ route('student.index') }}">Trang chủ</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('student.get-list-class-session') }}">Thời khóa biểu</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><a href="{{ route('student.get-class-session', $classSession->id) }}">Buổi học</a></li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="row p-4 ms-5 me-5 mt-5 mb-0 main-content">
        <div class="col-12">
            <div class="mb-3 d-flex justify-content-between align-items-center">
                <div>
                    <div class="text fs-4">Sơ đồ phòng máy: {{ $room->name . ' - ' . $building->name }}</div>
                    <span class="text fs-5">Lớp học phần: {{ $classSession->creditClass->name }}</span>
                </div>
                <div>
                    <a href="#" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#view-report-history-modal">Báo cáo đã gửi</a>
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
                                                <th scope="col" class="text-center" width="30%">Trạng thái</th>
                                                <th scope="col" class="text-center" width="20%">Thời gian gửi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(count($reports) > 0)
                                                @foreach($reports as $index => $report)
                                                    <tr>
                                                        <th scope="row" class="text-center">{{ $index + 1 }}</th>
                                                        <td class="text-center">{{ $report->content }}</td>
                                                        <td class="text-center">
                                                            <span class="p-1 rounded bg-opacity-75 {{ $report->is_approved ? 'bg-success' : 'bg-danger' }}">
                                                                {{ $report->is_approved ? 'Đã duyệt' : 'Chưa duyệt' }}
                                                            </span>
                                                        </td>
                                                        <td class="text-center">{{ $report->created_at->format('H:m:i d-m-Y') }}</td>
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
            </div>
            <div class="row border border-black ms-0 me-0" id="table-computer">
                @php
                    $computerNumber = 1;
                @endphp
                @for ($i = 1; $i <= $room->capacity; $i++)
                    @if ($i % 15 == 1)
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
                                <div class="position-relative dropdown-center border border-black {{ $hasAttendance ? 'bg-warning' : 'bg-info' }} bg-opacity-50" style="width: 6.67%; height: 100px;">
                                    <div class="text-center d-flex justify-content-center align-items-center overflow-hidden h-100"><span style="font-size: 12px;">{{ $hasAttendance ? $attendance->student->full_name : '' }}</span></div>
                                    <a href="#" class="position-absolute top-0 dropdown-toggle" data-bs-toggle="dropdown">{{ $computerNumber }}</a>
                                    <!----- Modal gửi báo cáo sự cố thiết bị ----->
                                    <div class="modal fade" id="send-report-modal-{{ $computerAtPosition->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="sendReportModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h1 class="modal-title fs-5" id="exampleModalLabel">Gửi báo cáo sự cố thiết bị</h1>
                                                </div>
                                                <div class="modal-body">
                                                    <form method="post" action="{{ route('student.send-report-api', $computerAtPosition->id) }}" id="send-report-form-{{ $computerAtPosition->id }}">
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
                                                    <button type="button" class="btn btn-primary btn-send-report" data-computer-id="{{ $computerAtPosition->id }}">Gửi</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item btn-attendance" data-computer-id="{{ $computerAtPosition->id }}" data-class-session-id="{{ $classSession->id }}" href="#">Điểm danh</a></li>
                                        <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#send-report-modal-{{ $computerAtPosition->id }}">Báo cáo sự cố</a></li>
                                    </ul>
                                </div>
                                @php
                                    $computerNumber++;
                                @endphp
                            @else
                                <div class="border border-black bg-secondary" style="width: 6.67%; height: 100px;">

                                </div>
                            @endif
                            @if ($i % 15 == 0 || $i == $room->capacity)
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

            function submitFormSendReport(form, computerId, overlay) {
                const formDataObj = {};
                form.find('input, select, textarea').each(function() {
                    formDataObj[$(this).attr('name')] = $(this).val();
                });

                let url = `{{ route("student.send-report-api", ":computerId") }}`;
                url = url.replace(':computerId', computerId);
                $.ajax({
                    type: 'POST',
                    data: JSON.stringify(formDataObj),
                    contentType: 'application/json',
                    url: url,
                    success: function (response) {
                        if (response.success) {
                            showToastSuccess(response.success);
                            $('#send-report-modal-' + computerId).modal('hide');
                            $('body').css('overflow', 'auto');
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

            function addEventForButtons() {
                $('.btn-attendance').off('click');
                $('.btn-send-report').off('click');
                $('.close-btn').off('click');

                $('.btn-attendance').click('click', function() {
                    const overlay = document.getElementById('overlay');
                    overlay.classList.add('show');

                    const classSessionId = $(this).data('class-session-id');
                    const computerId = $(this).data('computer-id');

                    const formDataObj = {};
                    formDataObj['computer_id'] = computerId;

                    $.ajax({
                        type: 'POST',
                        contentType: 'application/json',
                        data: JSON.stringify(formDataObj),
                        url: `{{ route("student.attendance-api", ":classSessionId") }}`.replace(':classSessionId', classSessionId),
                        success: function (response) {
                            if (response.success) {
                                showToastSuccess(response.success);
                                $('#table-computer').html(response.table_computer);
                            } else {
                                if (response.errors['attendance']) {
                                    showToastError(response.errors['attendance']);
                                    $('#table-computer').html(response.table_computer);
                                }
                            }

                            addEventForButtons();
                            overlay.classList.remove('show');
                        },
                        error: function (error) {
                            console.error(error);
                        }
                    });
                })

                $('.btn-send-report').click('click', function(e) {
                    e.preventDefault();
                    const overlay = document.getElementById('overlay');
                    overlay.classList.add('show');
                    $('.modal-backdrop').remove();

                    const computerId = $(this).data('computer-id');
                    const form = $('#send-report-form-' + computerId);

                    submitFormSendReport(form, computerId, overlay);
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
