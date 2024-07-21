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
                <div class="position-relative dropdown-center border border-black {{ $hasAttendance ? 'bg-warning' : 'bg-info' }} bg-opacity-50" style="width: {{ 100 / $room->max_computers_per_row }}%; height: 100px;">
                    <div class="text-center d-flex justify-content-center align-items-center overflow-hidden h-100"><span style="font-size: 12px;">{{ $hasAttendance ? $attendance->student->full_name : '' }}</span></div>
                    <a href="#" class="position-absolute top-0 dropdown-toggle" data-bs-toggle="dropdown">{{ $computerNumber }}</a>
                    <!----- Modal xác nhận thiết bị sử dụng (điểm danh) ----->
                    <div class="modal fade" id="attendance-modal-{{ $computerAtPosition->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="attendanceModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="exampleModalLabel">Xác nhận thiết bị sử dụng</h1>
                                </div>
                                <div class="modal-body">
                                    <p class="fs-6 m-0 p-3 text-center">Bạn sử dụng loại máy tính nào cho tiết học thực hành này?<br>(Quá trình điểm danh hoàn tất sau khi trả lời câu hỏi)</p>
                                </div>
                                <div class="modal-footer d-flex justify-content-center">
                                    <button type="button" class="btn btn-secondary btn-personal-computer" data-is-lab="false" data-computer-id="{{ $computerAtPosition->id }}" data-class-session-id="{{ $classSession->id }}">Máy tính cá nhân</button>
                                    <button type="button" class="btn btn-primary btn-lab-computer" data-is-lab="true" data-computer-id="{{ $computerAtPosition->id }}" data-class-session-id="{{ $classSession->id }}">Máy tính phòng thực hành</button>
                                </div>
                            </div>
                        </div>
                    </div>
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
                        <li><a class="dropdown-item btn-attendance" href="#" data-bs-toggle="modal" data-bs-target="#attendance-modal-{{ $computerAtPosition->id }}">Điểm danh</a></li>
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
            @if ($i % $room->max_computers_per_row == 0 || $i == $room->number_of_computer_rows * $room->max_computers_per_row)
        </div>
    @endif
@endfor
