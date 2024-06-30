@extends('student.layout')
@section('content')
    <div class="row m-5 mb-4 d-flex align-items-center">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="d-flex align-items-center">
                <div class="text">Lớp học của tôi</div>
                <ol class="breadcrumb my-auto ms-4">
                    <li class="breadcrumb-item"><a href="{{ route('student.index') }}">Trang chủ</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><a href="{{ route('student.get-list-class') }}">Lớp học của tôi</a></li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="row p-4 ms-5 me-5 mt-5 mb-0 main-content">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="text fs-4">Danh sách lớp học phần</div>
                <div class="d-flex justify-content-end">
                    <a href="#" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#join-class-modal">Vào lớp mới</a>
                    <!----- Modal thêm tham gia lớp học phần ----->
                    <div class="modal fade" id="join-class-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="joinClassModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="exampleModalLabel">Vào lớp học phần mới bằng mã mời</h1>
                                </div>
                                <div class="modal-body">
                                    <form method="post" action="{{ route('student.join-class-api') }}" id="join-class-form">
                                        @csrf
                                        <div class="row mb-3 mt-4">
                                            <label class="col-md-5 col-label-form fs-6 fw-bold text-md-end">Mã mời vào lớp<span class="required">*</span></label>
                                            <div class="col-md-7">
                                                <input type="text" name="class-code" class="form-control fs-6"/>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary close-btn" data-bs-dismiss="modal">Đóng</button>
                                    <button type="button" id="btn-join-class" class="btn btn-primary">Thêm</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="table-responsive" id="table-class">
                <table class="table table-bordered border-black">
                    <thead>
                        <tr>
                            <th scope="col" class="text-center" width="5%">STT</th>
                            <th scope="col" class="text-center" width="30%">Lớp học phần</th>
                            <th scope="col" class="text-center" width="20%">Giảng viên</th>
                            <th scope="col" class="text-center" width="20%">Bắt đầu - kết thúc</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(count($classes) > 0)
                            @foreach($classes as $index => $class)
                                <tr>
                                    <th scope="row" class="text-center">{{ $classes->firstItem() + $index }}</th>
                                    <td class="text-center">{{ $class->name }}</td>
                                    <td class="text-center">{{ $class->lecturer->full_name }}</td>
                                    <td class="text-center">
                                        {{ \Carbon\Carbon::parse($class->start_date)->format('d-m-Y') }} <i class='bx bx-right-arrow-alt'></i> {{ \Carbon\Carbon::parse($class->end_date)->format('d-m-Y') }}
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

            function submitFormJoinClass(form, overlay) {
                const formDataObj = {};
                form.find('input, select, textarea').each(function() {
                    formDataObj[$(this).attr('name')] = $(this).val();
                });

                $.ajax({
                    type: 'POST',
                    data: JSON.stringify(formDataObj),
                    contentType: 'application/json',
                    url: '{{ route("student.join-class-api") }}',
                    success: function (response) {
                        if (response.success) {
                            showToastSuccess(response.success);
                            form[0].reset();
                            $('#table-class tbody').html(response.table_class);
                            $('#paginate-class').html(response.links);
                            updatePagination();
                            $('#join-class-modal').modal('hide');
                            $('body').css('overflow', 'auto');
                        } else {
                            if (response.errors['class-code']) {
                                showToastError(response.errors['class-code']);
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

            function updatePagination () {
                const currentUrl = new URL(window.location.href);
                const currentPath = currentUrl.pathname;
                const searchParams = currentUrl.searchParams;

                $('.pagination .page-link').each(function() {
                    const link = $(this);
                    if (link.attr('href')) {
                        const newUrl = new URL(link.attr('href'));
                        searchParams.set('page', newUrl.searchParams.get('page'));

                        const updatedUrl = currentPath + '?' + searchParams.toString();
                        link.attr('href', updatedUrl);
                    }
                });
            }

            $('#btn-join-class').click(function(e) {
                e.preventDefault();
                const overlay = document.getElementById('overlay');
                overlay.classList.add('show');
                $('.modal-backdrop').remove();

                const form = $('#join-class-form');
                submitFormJoinClass(form, overlay);
            });

            function addEventForButtons() {
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
