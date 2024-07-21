<div class="mb-3 d-flex justify-content-between align-items-center">
    <div class="text fs-4">Sơ đồ phòng máy: {{ $sessionInfo['room']->name . ' - ' . $sessionInfo['building']->name }}</div>
</div>
<div class="row border border-black ms-0 me-0">
    @php
        $computerNumber = 1;
    @endphp
    @for ($i = 1; $i <= $sessionInfo['room']->number_of_computer_rows * $sessionInfo['room']->max_computers_per_row; $i++)
        @if ($i % $sessionInfo['room']->max_computers_per_row == 1)
            <div class="col-12 d-flex justify-content-start p-0">
                @endif
                @php
                    $computerAtPosition = $sessionInfo['computers']->firstWhere('position', $i);
                    if ($computerAtPosition) {
                        $attendance = $sessionInfo['attendances']->firstWhere('computer_id', $computerAtPosition->id);
                        $hasAttendance = $attendance != null;
                    }
                @endphp
                @if ($computerAtPosition)
                    <div class="position-relative border border-black {{ $hasAttendance ? 'bg-warning' : 'bg-info' }} bg-opacity-50" style="width: {{ 100 / $sessionInfo['room']->max_computers_per_row }}%; height: 100px;">
                        <div class="text-center d-flex justify-content-center align-items-center overflow-hidden h-100">
                            <span style="font-size: 12px;">{{ $hasAttendance ? $attendance->student->full_name : '' }}</span>
                        </div>
                        <div class="position-absolute top-0">{{ $computerNumber }}</div>
                    </div>
                    @php
                        $computerNumber++;
                    @endphp
                @else
                    <div class="border border-black bg-secondary" style="width: 6.67%; height: 100px;">

                    </div>
                @endif
                @if ($i % $sessionInfo['room']->max_computers_per_row == 0 || $i == $sessionInfo['room']->number_of_computer_rows * $sessionInfo['room']->max_computers_per_row)
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
