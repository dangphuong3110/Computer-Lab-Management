@extends('technician.layout')
@section('content')
    <div class="row m-5 mb-4 d-flex align-items-center">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="d-flex align-items-center">
                <div class="text">Phòng máy</div>
                <ol class="breadcrumb my-auto ms-4">
                    <li class="breadcrumb-item"><a href="{{ route('technician.index') }}">Trang chủ</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('technician.get-list-building') }}">Nhà thực hành</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><a href="{{ route('technician.get-list-room', $building_id) }}">Phòng máy</a></li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="row p-4 ms-5 me-5 mt-5 mb-0 main-content">
        <div class="col-12">
            <div class="row d-flex justify-content-between align-items-center mb-3">
                <div class="text fs-4 col-6">Danh sách phòng máy</div>
                <div class="d-flex justify-content-end col-6">
                    <a href="#" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#add-room-modal">Thêm</a>
                    <!----- Modal thêm phòng máy ----->
                    <div class="modal fade" id="add-room-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="addRoomModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="exampleModalLabel">Thêm phòng máy</h1>
                                </div>
                                <div class="modal-body">
                                    <form method="post" action="{{ route('technician.store-room-api') }}" id="add-room-form">
                                        @csrf
                                        <div class="row mb-3 mt-4">
                                            <label class="col-md-5 col-label-form fs-6 fw-bold text-md-end">Tên phòng máy<span class="required">*</span></label>
                                            <div class="col-md-7">
                                                <input type="text" name="room-name" class="form-control fs-6"/>
                                            </div>
                                        </div>
                                        <div class="row mb-3 mt-4">
                                            <label class="col-md-5 col-label-form fs-6 fw-bold text-md-end">Sức chứa<span class="required">*</span></label>
                                            <div class="col-md-7">
                                                <input type="text" name="capacity" class="form-control fs-6"/>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary close-btn" data-bs-dismiss="modal">Đóng</button>
                                    <button type="button" id="btn-add-room" class="btn btn-primary">Thêm</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row border border-black rounded ms-0 me-0" id="table-room">
                @if (count($rooms) > 0)
                    @foreach($rooms as $room)
                        <div class="col-xl-3 col-lg-4 pt-3">
                            <div class="card text-center text-bg-light mb-3">
                                <div class="card-header">
                                    <h5 class="card-title m-0">{{ $room->name }}</h5>
                                </div>
                                <div class="card-body">
                                    <h6 class="card-title mb-3">Sức chứa: {{ $room->capacity }} máy</h6>
                                    <h6 class="card-title mb-3">Hiện có: {{ $room->computers->count() }} máy</h6>
                                    <div class="btn-group">
                                        <div class="wrap-button" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Sơ đồ phòng máy">
                                            <a href="{{ route('technician.get-list-computer', $room->id) }}" class="btn btn-success m-1 btn-sm"><i class='bx bx-show'></i></a>
                                        </div>
                                        <div class="wrap-button" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Sửa thông tin phòng máy">
                                            <a href="#" class="btn btn-primary m-1 btn-sm" data-bs-toggle="modal" data-bs-target="#update-room-modal-{{ $room->id }}"><i class='bx bx-pencil'></i></a>
                                        </div>
                                        <!----- Modal sửa phòng máy----->
                                        <div class="modal fade modal-update" id="update-room-modal-{{ $room->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="addRoomModalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h1 class="modal-title fs-5" id="exampleModalLabel">Chỉnh sửa thông tin phòng máy</h1>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form method="post" action="{{ route('technician.update-room-api', $room->id) }}" id="update-room-form-{{ $room->id }}">
                                                            @csrf
                                                            @method('PUT')
                                                            <div class="row mb-3 mt-4">
                                                                <label class="col-md-5 col-label-form fs-6 fw-bold text-md-end">Tên phòng máy<span class="required">*</span></label>
                                                                <div class="col-md-7">
                                                                    <input type="text" name="room-name" class="form-control fs-6" value="{{ $room->name }}" data-initial-value="{{ $room->name }}"/>
                                                                </div>
                                                            </div>
                                                            <div class="row mb-3 mt-4">
                                                                <label class="col-md-5 col-label-form fs-6 fw-bold text-md-end">Sức chứa<span class="required">*</span></label>
                                                                <div class="col-md-7">
                                                                    <input type="text" name="capacity" class="form-control fs-6" value="{{ $room->capacity }}" data-initial-value="{{ $room->capacity }}"/>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary close-update-btn" data-room-id="{{ $room->id }}" data-bs-dismiss="modal">Đóng</button>
                                                        <button type="button" class="btn btn-primary btn-update-room" data-room-id="{{ $room->id }}">Lưu</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="wrap-button" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Xóa phòng máy">
                                            <a href="#" class="btn btn-danger m-1 btn-sm" data-bs-toggle="modal" data-bs-target="#destroy-room-modal-{{ $room->id }}"><i class='bx bx-trash'></i></a>
                                        </div>
                                        <form method="post" action="{{ route('technician.destroy-room-api', $room->id) }}" id="destroy-room-form-{{ $room->id }}">
                                            @csrf
                                            @method('DELETE')
                                            <!----- Modal xóa phòng máy ----->
                                            <div class="modal fade" id="destroy-room-modal-{{ $room->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="destroyRoomLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h1 class="modal-title fs-5" id="exampleModalLabel">Xóa phòng máy</h1>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p class="text-wrap m-0">Bạn có chắc chắn muốn xóa phòng máy?</p>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Trở về</button>
                                                            <button type="submit" class="btn btn-danger btn-destroy-room" data-room-id="{{ $room->id }}">Xóa</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="col-12 text-center p-5">
                        <span class="fs-5">Không có dữ liệu phòng máy</span>
                    </div>
                @endif
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

            function submitFormCreateRoom (form, overlay) {
                const formDataObj = {};
                form.find('input, select, textarea').each(function() {
                    formDataObj[$(this).attr('name')] = $(this).val();
                });

                formDataObj['building_id'] = '{{ $building_id }}';

                $.ajax({
                    type: 'POST',
                    data: JSON.stringify(formDataObj),
                    contentType: 'application/json',
                    url: '{{ route("technician.store-room-api") }}',
                    success: function (response) {
                        if (response.success) {
                            showToastSuccess(response.success);
                            form[0].reset();
                            $('#table-room').html(response.table_room);
                            addEventForModalUpdate();
                            addEventForButtons();
                            $('#add-room-modal').modal('hide');
                            $('body').css('overflow', 'auto');
                        } else {
                            if (response.errors['room-name']) {
                                showToastError(response.errors['room-name']);
                            }
                            if (response.errors['capacity']) {
                                showToastError(response.errors['capacity']);
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

            function submitFormUpdateRoom (form, roomId, overlay) {
                const formDataObj = Object.fromEntries(new FormData(form).entries());

                formDataObj['building_id'] = '{{ $building_id }}';

                let url = `{{ route("technician.update-room-api", ":roomId") }}`;
                url = url.replace(':roomId', roomId);
                $.ajax({
                    type: 'PUT',
                    data: JSON.stringify(formDataObj),
                    contentType: 'application/json',
                    url: url,
                    success: function (response) {
                        if (response.success) {
                            showToastSuccess(response.success);
                            $('#table-room').html(response.table_room);
                            addEventForModalUpdate();
                            addEventForButtons();
                            $('#update-room-modal-' + roomId).modal('hide');
                            $('body').css('overflow', 'auto');
                        } else {
                            if (response.errors['room-name']) {
                                showToastError(response.errors['room-name'])
                            }
                            if (response.errors['capacity']) {
                                showToastError(response.errors['capacity']);
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

            function submitFormDestroyRoom (roomId, overlay) {
                const formDataObj = {};

                formDataObj['building_id'] = '{{ $building_id }}';

                let url = `{{ route("technician.destroy-room-api", ":roomId") }}`;
                url = url.replace(':roomId', roomId);
                $.ajax({
                    type: 'DELETE',
                    data: JSON.stringify(formDataObj),
                    contentType: 'application/json',
                    url: url,
                    success: function (response) {
                        if (response.success) {
                            showToastSuccess(response.success);
                            $('#table-room').html(response.table_room);
                            addEventForModalUpdate();
                            addEventForButtons();
                        } else {
                            if (response.errors['room']) {
                                showToastError(response.errors['room'])
                            }
                        }

                        $('#destroy-room-modal-' + roomId).modal('hide');
                        $('body').css('overflow', 'auto');
                        overlay.classList.remove('show');
                    },
                    error: function (error) {
                        console.error(error);
                    }
                });
            }

            $('#btn-add-room').click(function(e) {
                e.preventDefault();
                const overlay = document.getElementById('overlay');
                overlay.classList.add('show');
                $('.modal-backdrop').remove();

                const form = $('#add-room-form');
                submitFormCreateRoom(form, overlay);
            });

            function addEventForButtons () {
                const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
                const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));

                $('.btn-update-room').off('click').click(function(e) {
                    e.preventDefault();
                    const overlay = document.getElementById('overlay');
                    overlay.classList.add('show');
                    $('.modal-backdrop').remove();

                    const roomId = $(this).data('room-id');
                    const form = $('#update-room-form-' + roomId);

                    submitFormUpdateRoom(form[0], roomId, overlay);
                });

                $('.btn-destroy-room').off('click').click(function(e) {
                    e.preventDefault();
                    const overlay = document.getElementById('overlay');
                    overlay.classList.add('show');
                    $('.modal-backdrop').remove();

                    const roomId = $(this).data('room-id');

                    submitFormDestroyRoom(roomId, overlay);
                });

                $('.close-btn').off('click').click(function() {
                    $('.modal-backdrop.fade.show').remove();
                });

                $('.close-update-btn').off('click').click(function() {
                    $('.modal-backdrop.fade.show').remove();
                });
            }

            function addEventForModalUpdate() {
                $('.modal-update').on('shown.bs.modal', function() {
                    const modal = $(this);
                    modal.find('input, select, textarea').each(function() {
                        const input = $(this);
                        input.data('initial-value', input.val());
                    });
                });

                $('.modal-update').on('hidden.bs.modal', function() {
                    const modal = $(this);
                    modal.find('input, select, textarea').each(function() {
                        const input = $(this);
                        input.val(input.data('initial-value'));
                    });
                });
            }

            function resetInitialValue() {
                $('.modal-update').find('input, select, textarea').each(function() {
                    const input = $(this);
                    if (input.is('select')) {
                        const initialValue = input.data('initial-value');
                        input.val(initialValue).trigger('change');
                    } else {
                        input.val(input.data('initial-value'));
                    }
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
                    onClick: function(){}
                }).showToast();
            }

            resetInitialValue();
            addEventForModalUpdate();
            addEventForButtons();
        });
    </script>
@endsection
