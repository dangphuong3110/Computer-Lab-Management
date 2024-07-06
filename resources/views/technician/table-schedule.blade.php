@foreach($buildings as $building)
    @php $firstRoom = true; @endphp
    @foreach($building->rooms as $index => $room)
{{--        @if ($room->classSessions()->count() == 0)--}}
{{--            @php continue; @endphp--}}
{{--        @endif--}}
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
                                    {{--                                                    {{ $session['class_name'] }}--}}
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
