@extends('lecturer.layout')
@section('content')
    <div class="row m-5 mb-4 d-flex align-items-center">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="d-flex align-items-center">
                <div class="text">Lớp học tiếp quản</div>
                <ol class="breadcrumb my-auto ms-4">
                    <li class="breadcrumb-item"><a href="{{ route('lecturer.index') }}">Trang chủ</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><a href="{{ route('lecturer.get-list-class') }}">Lớp học tiếp quản</a></li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="row p-4 ms-5 me-5 mt-5 mb-5 main-content">
        <div class="col-12">
            <div class="mb-3">
                <div class="text fs-4">Danh sách lớp học phần tiếp quản</div>
            </div>
            <div class="table-responsive" id="table-class">
                <table class="table table-bordered border-black">
                    <thead>
                    <tr>
                        <th scope="col" class="text-center" width="5%">STT</th>
                        <th scope="col" class="text-center" width="50%">Lớp học phần</th>
                        <th scope="col" class="text-center" width="35%">Bắt đầu - kết thúc</th>
                        <th scope="col" class="text-center action-column">Hành động</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(count($classes) > 0)
                        @foreach($classes as $index => $class)
                            <tr>
                                <th scope="row" class="text-center">{{ $classes->firstItem() + $index }}</th>
                                <td class="text-center">{{ $class->name }}</td>
                                <td class="text-center">
                                    {{ \Carbon\Carbon::parse($class->start_date)->format('d-m-Y') }} <i class='bx bx-right-arrow-alt'></i> {{ \Carbon\Carbon::parse($class->end_date)->format('d-m-Y') }}
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center">
                                        <div class="wrap-button m-1" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Danh sách sinh viên">
                                            <a href="{{ route('lecturer.get-list-student-class', $class->id) }}" class="btn btn-sm btn-success my-auto btn-student-class"><i class='bx bx-group'></i></a>
                                        </div>
                                        <div class="wrap-button m-1" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Xuất file điểm danh">
                                            <a href="{{ route('lecturer.export-attendances', $class->id) }}" class="btn btn-sm btn-primary my-auto btn-export-attendances"><i class='bx bxs-file-export'></i></a>
                                        </div>
                                        <div class="wrap-button m-1" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Sao chép mã vào lớp">
                                            <a href="#" class="btn btn-sm btn-secondary my-auto btn-student-class btn-copy-class-code" data-class-code="{{ $class->class_code }}" data-class-id="{{ $class->id }}"><i class='bx bx-log-in-circle'></i></a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="4" class="text-center">Không có dữ liệu lớp học phần</td>
                        </tr>
                    @endif
                    </tbody>
                </table>
                <div class="fw-bold skill-pagination" id="paginate-class">
                    {!! $classes->render('pagination::bootstrap-5') !!}
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

            function addEventForButtons() {
                const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
                const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));

                $('.btn-export-attendances').off('click').click(function(e) {
                    e.preventDefault();
                    setTimeout(() => {
                        const overlay = document.getElementById('overlay');
                        overlay.classList.remove('show');
                    }, 1000);
                    window.location.href = $(this).attr('href');
                });

                $('.btn-copy-class-code').off('click').click(function() {
                    const classId = $(this).data('class-id');
                    const classCode = $(this).data('class-code');
                    navigator.clipboard.writeText(classCode).then(function() {
                        showToastSuccess('Sao chép mã vào lớp thành công! Mã có hiệu lực trong vòng 10 phút.');

                        let url = `{{ route("lecturer.update-class-code-api", ":classId") }}`;
                        url = url.replace(':classId', classId);
                        $.ajax({
                            type: 'POST',
                            url: url,
                        });
                    }, function() {
                        showToastError('Sao chép mã vào lớp thất bại!');
                    });
                    $('[data-bs-toggle="tooltip"]').tooltip('hide');
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
                    onClick: function(){

                    }
                }).showToast();
            }

            addEventForButtons();
        });
    </script>
@endsection
