@foreach($buildings as $building)
    @php $firstRoom = true; @endphp
    @foreach($building->rooms as $index => $room)
        {{--                        @if ($room->classSessions()->count() == 0)--}}
        {{--                            @php continue; @endphp--}}
        {{--                        @endif--}}
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
                                <td class="text-center schedule" data-class-id="{{ $session['class_id'] }}" colspan="{{ $colspan }}" style="{{ $backgroundColor }}" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{ $session['class_name'] }} ({{ $room->name }}-{{ $building->name }})">
                                    {{--                                                    {{ $session['class_name'] }}--}}
                                </td>
                                <!----- Modal tra cứu thông tin lớp học ----->
                                <div class="modal fade" id="search-info-class-in-schedule-modal-{{ $session['class_id'] }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="searchInfoClassInScheduleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-xl modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="exampleModalLabel">Tra cứu thông tin sử dụng phòng máy của lớp học phần</h1>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row mb-3 mt-1">
                                                    <label class="col-12 col-label-form fs-6 fw-bold text-start">Ngày học</label>
                                                    <div class="col-12">
                                                        @php
                                                            $selectedClass = $classes_schedule->firstWhere('id', $session['class_id']);
                                                        @endphp
                                                        <select data-class-id="{{ $session['class_id'] }}" class="form-select form-control fs-6 class-date-info-in-schedule" style="max-width: 200px;">
                                                            @foreach ($selectedClass->classInfo as $sessionInfo)
                                                                <option value="{{ $sessionInfo['session_id'] }}">{{ $sessionInfo['date'] }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-12" id="table-class-session-info-in-schedule">
                                                        <div class="mb-3 d-flex justify-content-between align-items-center">
                                                            <div class="text fs-4">Sơ đồ phòng máy: {{ $selectedClass->classInfo[0]['room']->name . ' - ' . $selectedClass->classInfo[0]['building']->name }}</div>
                                                        </div>
                                                        <div class="row border border-black ms-0 me-0">
                                                            @php
                                                                $computerNumber = 1;
                                                            @endphp
                                                            @for ($i = 1; $i <= $selectedClass->classInfo[0]['room']->number_of_computer_rows * $selectedClass->classInfo[0]['room']->max_computers_per_row; $i++)
                                                                @if ($i % $selectedClass->classInfo[0]['room']->max_computers_per_row == 1)
                                                                    <div class="col-12 d-flex justify-content-start p-0">
                                                                        @endif
                                                                        @php
                                                                            $computerAtPosition = $selectedClass->classInfo[0]['computers']->firstWhere('position', $i);
                                                                            if ($computerAtPosition) {
                                                                                $attendance = $selectedClass->classInfo[0]['attendances']->firstWhere('computer_id', $computerAtPosition->id);
                                                                                $hasAttendance = $attendance != null;
                                                                            }
                                                                        @endphp
                                                                        @if ($computerAtPosition)
                                                                            <div class="position-relative border border-black {{ $hasAttendance ? 'bg-warning' : 'bg-info' }} bg-opacity-50" style="width: {{ 100 / $selectedClass->classInfo[0]['room']->max_computers_per_row }}%; height: 100px;">
                                                                                <div class="text-center d-flex justify-content-center align-items-center overflow-hidden h-100">
                                                                                    <span style="font-size: 12px;">{{ $hasAttendance ? $attendance->student->full_name : '' }}</span>
                                                                                </div>
                                                                                <div class="position-absolute top-0">{{ $computerNumber }}</div>
                                                                            </div>
                                                                            @php
                                                                                $computerNumber++;
                                                                            @endphp
                                                                        @else
                                                                            <div class="border border-black bg-secondary" style="width: {{ 100 / $selectedClass->classInfo[0]['room']->max_computers_per_row }}%; height: 100px;">

                                                                            </div>
                                                                        @endif
                                                                        @if ($i % $selectedClass->classInfo[0]['room']->max_computers_per_row == 0 || $i == $selectedClass->classInfo[0]['room']->number_of_computer_rows * $selectedClass->classInfo[0]['room']->max_computers_per_row)
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
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary close-update-btn" data-bs-dismiss="modal">Đóng</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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
