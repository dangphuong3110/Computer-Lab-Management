@extends('student.layout')
@section('content')
    <div class="row m-5 mb-4 d-flex align-items-center">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="d-flex align-items-center">
                <div class="text">Thời khóa biểu</div>
                <ol class="breadcrumb my-auto ms-4">
                    <li class="breadcrumb-item"><a href="{{ route('student.index') }}">Trang chủ</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><a href="{{ route('student.get-list-class-session') }}">Thời khóa biểu</a></li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="row p-4 ms-5 me-5 mt-5 mb-0 main-content">
        <div class="col-12">
            <div class="mb-3">
                <div class="text fs-4">Thời khóa biểu trong tuần</div>
                <span class="text fs-5">({{ $startOfWeek }}<i class='bx bx-right-arrow-alt'></i>{{ $endOfWeek }})</span>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered border-black schedule-table">
                    <thead>
                        <tr>
                            <td rowspan="3" width="10%" class="text-center align-middle"><span class="fw-bold">Ngày</span></td>
                            <td colspan="15" class="text-center"><span class="fw-bold">Tiết</span></td>
                        </tr>
                        <tr>
                            <td class="text-center" width="6%">1</td>
                            <td class="text-center" width="6%">2</td>
                            <td class="text-center" width="6%">3</td>
                            <td class="text-center" width="6%">4</td>
                            <td class="text-center" width="6%">5</td>
                            <td class="text-center" width="6%">6</td>
                            <td class="text-center" width="6%">7</td>
                            <td class="text-center" width="6%">8</td>
                            <td class="text-center" width="6%">9</td>
                            <td class="text-center" width="6%">10</td>
                            <td class="text-center" width="6%">11</td>
                            <td class="text-center" width="6%">12</td>
                            <td class="text-center" width="6%">13</td>
                            <td class="text-center" width="6%">14</td>
                            <td class="text-center" width="6%">15</td>
                        </tr>
                        <tr>
                            @foreach($fullLessons as $lesson)
                                <td class="text-center">
                                    ({{ \Carbon\Carbon::parse($lesson->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($lesson->end_time)->format('H:i') }})
                                </td>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($daysOfWeek as $dayIndex => $dayName)
                            <tr>
                                <td class="text-center align-middle"><span class="fw-bold">{{ $dayName }}</span></td>
                                @for ($lesson = 1; $lesson <= 15; $lesson++)
                                    @php
                                        $foundSession = false;
                                        $colspan = 1;
                                        $backgroundColor = '';
                                    @endphp
                                    @foreach ($schedule as $session)
                                        @if ($session['day_of_week'] == $dayIndex && $lesson >= $session['start_lesson'] && $lesson <= $session['end_lesson'])
                                            @if (!$foundSession)
                                                @php
                                                    $colspan = $session['end_lesson'] - $session['start_lesson'] + 1;
                                                    $backgroundColor = 'background-color: ' . '#' . substr(md5($session['class_name']), 0, 6) . ';';
                                                @endphp
                                                <td class="text-center schedule is-class-session" colspan="{{ $colspan }}" style="{{ $backgroundColor }}" data-class-session-id="{{ $session['session_id'] }}" data-day-of-week="{{ $dayIndex }}" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{ $session['class_name'] }} ({{ $session['room'] }}-{{ $session['building'] }})">
                                                    {{ $session['class_name'] }}
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
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $(document).ready(function() {
            const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
            const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('.is-class-session').click(function() {
                const overlay = document.getElementById('overlay');
                overlay.classList.add('show');

                const classSessionId = $(this).data('class-session-id');
                const dayOfWeek = $(this).data('day-of-week');
                const formDataObj = {};
                formDataObj['day_of_week'] = dayOfWeek;

                $.ajax({
                    type: 'POST',
                    data: JSON.stringify(formDataObj),
                    contentType: 'application/json',
                    url: `{{ route("student.get-class-session-api", ":classSessionId") }}`.replace(':classSessionId', classSessionId),
                    success: function(response) {
                        if (response.success) {
                            window.location.href = response.success['get-class-session-route'];
                        } else {
                            if (response.errors['class-session']) {
                                showToastError(response.errors['class-session']);
                            }
                        }
                        overlay.classList.remove('show');
                    },
                    error: function(error) {
                        console.error(error);
                    }

                })
            });

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
        });
    </script>
@endsection
