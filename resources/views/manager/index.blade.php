@extends('manager.layout')
@section('content')
    <div class="row m-5 mb-4 d-flex align-items-center">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <div class="text">Trang chủ</div>
            </nav>
        </div>
    </div>
    <div class="row ms-5 me-5 mt-5 mb-0">
        <div class="col-12">
            <div class="row mb-3">
                <div class="col-xl-3 col-md-6 ps-0">
                    <div class="card-statistics p-4">
                        <div class="fs-6 fw-bold">Số phòng máy đang sử dụng</div>
                        <div class="number fs-3 fw-bold">
                            <span class="text-success text-opacity-75">{{ $roomsInUse }}</span>
                            <span class="text-secondary text-opacity-50">/</span>
                            {{ $totalRooms }}
                        </div>
                        <div class="note fs-6 fst-italic text-secondary text-opacity-50">(Hoạt động/tổng số)</div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 ps-0 mt-md-0 mt-3">
                    <div class="card-statistics p-4">
                        <div class="fs-6 fw-bold">Số máy tính đang sử dụng</div>
                        <div class="number fs-3 fw-bold">
                            <span class="text-success text-opacity-75">{{ $computersInUse }}</span>
                            <span class="text-secondary text-opacity-50">/</span>
                            {{ $totalComputers }}
                        </div>
                        <div class="note fs-6 fst-italic text-secondary text-opacity-50">(Hoạt động/tổng số)</div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 ps-0 mt-xl-0 mt-3">
                    <div class="card-statistics p-4">
                        <div class="fs-6 fw-bold">Số báo cáo sự cố thiết bị</div>
                        <div class="number fs-3 fw-bold">
                            <span class="text-warning text-opacity-75">{{ $pendingReports }}</span>
                            <span class="text-secondary text-opacity-50">/</span>
                            <span class="text-primary text-opacity-75">{{ $processingReports }}</span>
                            <span class="text-secondary text-opacity-50">/</span>
                            <span class="text-success text-opacity-75">{{ $processedReports }}</span>
                        </div>
                        <div class="note fs-6 fst-italic text-secondary text-opacity-50">(Chờ/Đang/Đã xử lý)</div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 ps-0 mt-xl-0 mt-3">
                    <div class="card-statistics p-4">
                        <div class="fs-6 fw-bold">Thời gian xử lý sự cố</div>
                        <div class="number fs-3 fw-bold">
                            <span class="text-danger text-opacity-75">{{ $avgTime }}</span>
                        </div>
                        <div class="note fs-6 fst-italic text-secondary text-opacity-50">(Trung bình)</div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 ps-0">
                    <div class="card-statistics p-4 mb-3 p-0">
                        <div class="chart-container">
                            <div class="fs-6 fw-bold">Tỉ lệ sử dụng phòng máy</div>
                            <div class="chart mt-3 mx-auto" style="max-width: 175px; max-height: 175px;">
                                <canvas id="myChart"></canvas>
                            </div>
                        </div>
                        <div class="statistical-method-container mt-1">
                            <label class="col-12 p-0 col-label-form fs-6">Chọn thời gian thống kê:</label>
                            <div class="col-md-6 p-0">
                                <select id="statistical-method-room" name="statistical-method" class="form-select form-control fs-6">
                                    <option value="1" selected>Hôm nay</option>
                                    <option value="2">7 ngày qua</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 ps-0">
                    <div class="card-statistics p-4">
                        <div class="chart-container">
                            <div class="fs-6 fw-bold">Tỉ lệ sử dụng máy tính</div>
                            <div class="chart mt-3 mx-auto" style="max-width: 175px; max-height: 175px;">
                                <canvas id="computer-usage-statistics-chart"></canvas>
                            </div>
                        </div>
                        <div class="statistical-method-container mt-1">
                            <label class="col-12 p-0 col-label-form fs-6">Chọn thời gian thống kê:</label>
                            <div class="col-md-6 p-0">
                                <select id="statistical-method-computer" name="statistical-method" class="form-select form-control fs-6">
                                    <option value="1" selected>Hôm nay</option>
                                    <option value="2">7 ngày qua</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row ms-5 me-5 mt-0 mb-0">
        <div class="col-md-12 mt-md-0 mt-3 ps-0">
            <div class="card-statistics p-4">
                <div class="fs-6 fw-bold mb-3">Thời khóa biểu các lớp học thực hành</div>
                <div class="table-responsive">
                    <table class="table table-bordered border-black schedule-table">
                        <thead>
                        <tr>
                            <td rowspan="2" class="text-center align-middle fw-bold" width="1%">Tòa nhà</td>
                            <td rowspan="2" class="text-center align-middle fw-bold" width="1%">Phòng</td>
                            <td colspan="15" class="text-center fw-bold">Thứ Hai</td>
                            <td colspan="15" class="text-center fw-bold">Thứ Ba</td>
                            <td colspan="15" class="text-center fw-bold">Thứ Tư</td>
                            <td colspan="15" class="text-center fw-bold">Thứ Năm</td>
                            <td colspan="15" class="text-center fw-bold">Thứ Sáu</td>
                            <td colspan="15" class="text-center fw-bold">Thứ Bảy</td>
                            <td colspan="15" class="text-center fw-bold">Chủ Nhật</td>
                        </tr>
                        <tr>
                            @for($i=1; $i<=7; $i++)
                                @for($j=1; $j<=15; $j++)
                                    <td class="text-center">{!! $j < 10 ? '&nbsp;' . $j . '&nbsp;' : $j !!} </td>
                                @endfor
                            @endfor
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($buildings as $building)
                            @php $firstRoom = true; @endphp
                            @foreach($building->rooms as $index => $room)
{{--                                @if ($room->classSessions()->count() == 0)--}}
{{--                                    @php continue; @endphp--}}
{{--                                @endif--}}
                                <tr>
                                    @if ($firstRoom)
                                        <td rowspan="{{ $building->rooms()->count() }}" class="text-center align-middle">{{ $building->name }}</td>
                                        @php $firstRoom = false; @endphp
                                    @endif
                                    <td class="text-center align-middle">{{ $room->name }}</td>
                                    @foreach($daysOfWeek as $dayIndex => $dayName)
                                        @for ($lesson = 1; $lesson <= 15; $lesson++)
                                            @php
                                                $foundSession = false;
                                                $colspan = 1;
                                                $backgroundColor = '';
                                            @endphp
                                            @foreach ($schedule as $session)
                                                @if ($session['day_of_week'] == $dayIndex && $lesson >= $session['start_lesson'] && $lesson <= $session['end_lesson'] && $session['room_id'] == $room->id)
                                                    @if (!$foundSession)
                                                        @php
                                                            $colspan = $session['end_lesson'] - $session['start_lesson'] + 1;
                                                            $backgroundColor = 'background-color: ' . '#' . substr(md5($session['class_name'] . $session['class_id']), 0, 6) . ';';
                                                        @endphp
                                                        <td class="text-center schedule" colspan="{{ $colspan }}" style="{{ $backgroundColor }}" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{ $session['class_name'] }} ({{ $room->name }}-{{ $building->name }})">
{{--                                                            {{ $session['class_name'] }}--}}
                                                        </td>
                                                        @php
                                                            $foundSession = true;
                                                            $lesson += $colspan - 1;
                                                        @endphp
                                                    @else
                                                        @php $colspan--; @endphp
                                                    @endif
                                                @endif
                                            @endforeach
                                            @if (!$foundSession)
                                                <td class="text-center"></td>
                                            @endif
                                        @endfor
                                    @endforeach
                                </tr>
                            @endforeach
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $(document).ready(function() {
            const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
            const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));

            const dataRooms = {
                labels: [
                    'Thời gian sử dụng (%)',
                    'Thời gian không sử dụng (%)',
                ],
                datasets: [{
                    data: [{{ $usedRoomTimeRatioToday }}, 100-{{ $usedRoomTimeRatioToday }}],
                    backgroundColor: [
                        'rgba(83, 165, 127)',
                        'rgb(203,208,210)',
                    ],
                }],
            };

            const dataComputers = {
                labels: [
                    'Thời gian sử dụng (%)',
                    'Thời gian không sử dụng (%)',
                ],
                datasets: [{
                    data: [{{ $usedComputerTimeRatioToday }}, 100-{{ $usedComputerTimeRatioToday }}],
                    backgroundColor: [
                        'rgba(83, 165, 127)',
                        'rgb(203,208,210)',
                    ],
                }],
            };

            const config1 = {
                type: 'pie',
                data: dataRooms,
                options : {
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            };

            const config2 = {
                type: 'pie',
                data: dataComputers,
                options : {
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            };

            const ctx = document.getElementById('myChart').getContext('2d');
            const ctx2 = document.getElementById('computer-usage-statistics-chart').getContext('2d');
            const chartRoom = new Chart(ctx, config1);
            const chartComputer = new Chart(ctx2, config2);


            $('#statistical-method-computer').change(function() {
                const method = $(this).val();
                let newData = [];
                if (method == 1) {
                    newData = [{{ $usedComputerTimeRatioToday }}, 100 - {{ $usedComputerTimeRatioToday }}];
                } else if (method == 2) {
                    newData = [{{ $usedComputerTimeRatioLast7Days }}, 100 - {{ $usedComputerTimeRatioLast7Days }}];
                }

                chartComputer.config._config.data.datasets[0].data = newData;
                chartComputer.update();
            });

            $('#statistical-method-room').change(function() {
                const method = $(this).val();
                let newData = [];
                if (method == 1) {
                    newData = [{{ $usedRoomTimeRatioToday }}, 100 - {{ $usedRoomTimeRatioToday }}];
                } else if (method == 2) {
                    newData = [{{ $usedRoomTimeRatioLast7Days }}, 100 - {{ $usedRoomTimeRatioLast7Days }}];
                }

                chartRoom.config._config.data.datasets[0].data = newData;
                chartRoom.update();
            });
        });
    </script>
@endsection
