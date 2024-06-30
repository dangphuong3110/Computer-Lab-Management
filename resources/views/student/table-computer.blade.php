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
                    <div class="text-center d-flex align-items-center overflow-hidden h-100"><span style="font-size: 12px;">{{ $hasAttendance ? $attendance->student->full_name : '' }}</span></div>
                    <a href="#" class="position-absolute top-0 dropdown-toggle" data-bs-toggle="dropdown">{{ $computerNumber }}</a>
                    <!----- Modal gửi báo cáo sự cố thiết bị ----->
                    <div class="modal fade modal-update" id="send-report-modal-{{ $computerAtPosition->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="sendReportModalLabel" aria-hidden="true">
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
