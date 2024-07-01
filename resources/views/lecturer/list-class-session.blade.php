@extends('lecturer.layout')
@section('content')
    <div class="row m-5 mb-4 d-flex align-items-center">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="d-flex align-items-center">
                <div class="text">Lịch dạy học</div>
                <ol class="breadcrumb my-auto ms-4">
                    <li class="breadcrumb-item"><a href="{{ route('lecturer.index') }}">Trang chủ</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><a href="{{ route('lecturer.get-list-class-session') }}">Lịch dạy học</a></li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="row p-4 ms-5 me-5 mt-5 mb-0 main-content">
        <div class="col-12">
            <div class="mb-3">
                <div class="text fs-4">Lịch dạy học trong tuần</div>
                <span class="text fs-5">({{ $startOfWeek }}<i class='bx bx-right-arrow-alt'></i>{{ $endOfWeek }})</span>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered border-black schedule-table">
                    <tbody>
                    @foreach ($daysOfWeek as $key => $day)
                        <tr>
                            <td class="text-center align-middle"><span class="fw-bold">{{ $day }}</span></td>
                            @php
                                $count = 0;
                                $colspan = 1;
                            @endphp
                            @foreach ($schedule as $session)
                                @if ($session['day_of_week'] == $key)
                                    @php
                                        $count++;
                                    @endphp
                                    @if ($count == $dayOfWeekCounts[$key])
                                        @php
                                            $colspan = $maxCount - $count + 1;
                                        @endphp
                                    @endif
                                    <td class="p-0 text-center" colspan="{{ $colspan }}">
                                        <div class="is-class-session" data-class-id="{{ $session['class_id'] }}" data-day-of-week="{{ $key }}">
                                            Thời gian: {{ $session['start_time'] }} <i class='bx bx-right-arrow-alt'></i> {{ $session['end_time'] }} <br>
                                            Tên lớp học phần: {{ $session['class_name'] }} <br>
                                            Địa điểm: {{ $session['room'] . '-' . $session['building'] }}
                                        </div>
                                    </td>
                                    @php
                                        $colspan = 1;
                                    @endphp
                                @endif
                            @endforeach
                            @if ($count == 0)
                                <td class="text-center" colspan="{{ $maxCount }}">Không có lịch</td>
                            @endif
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
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('.is-class-session').click(function() {
                const classId = $(this).data('class-id');
                const dayOfWeek = $(this).data('day-of-week');
                const formDataObj = {};
                formDataObj['day_of_week'] = dayOfWeek;

                $.ajax({
                    type: 'POST',
                    data: JSON.stringify(formDataObj),
                    contentType: 'application/json',
                    url: `{{ route("lecturer.get-class-session-api", ":classId") }}`.replace(':classId', classId),
                    success: function(response) {
                        if (response.success) {
                            window.location.href = response.success['get-class-session-route'];
                        } else {
                            if (response.errors['class-session']) {
                                showToastError(response.errors['class-session']);
                            }
                        }
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
