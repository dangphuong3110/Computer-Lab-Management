@extends('technician.layout')
@section('content')
    <div class="row m-5 mb-4 d-flex align-items-center">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="d-flex align-items-center">
                <div class="text">Nhà thực hành</div>
                <ol class="breadcrumb my-auto ms-4">
                    <li class="breadcrumb-item"><a href="{{ route('technician.index') }}">Trang chủ</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><a href="{{ route('technician.get-list-building') }}">Nhà thực hành</a></li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="row p-4 ms-5 me-5 mt-5 mb-0 main-content">
        <div class="col-12">
            <div class="row d-flex justify-content-between align-items-center mb-3">
                <div class="text fs-4 col-6">Danh sách nhà thực hành</div>
                <div class="d-flex justify-content-end col-6">
                    <a href="#" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#add-building-modal">Thêm</a>
                    <!----- Modal thêm nhà thực hành ----->
                    <div class="modal fade" id="add-building-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="addBuildingModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="exampleModalLabel">Thêm nhà thực hành</h1>
                                </div>
                                <div class="modal-body">
                                    <form method="post" action="{{ route('technician.store-building-api') }}" id="add-building-form">
                                        @csrf
                                        <div class="row mb-3 mt-4">
                                            <label class="col-md-5 col-label-form fs-6 fw-bold text-md-end">Tên nhà thực hành<span class="required">*</span></label>
                                            <div class="col-md-7">
                                                <input type="text" name="building-name" class="form-control fs-6"/>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary close-btn" data-bs-dismiss="modal">Đóng</button>
                                    <button type="button" id="btn-add-building" class="btn btn-primary">Thêm</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row border rounded ms-0 me-0" id="table-building">
                @if (count($buildings) > 0)
                    @foreach($buildings as $building)
                        <div class="col-xl-3 col-lg-4 pt-3">
                            <div class="card text-center text-bg-light mb-3">
                                <div class="card-header">
                                    <h5 class="card-title m-0">{{ $building->name }}</h5>
                                </div>
                                <div class="card-body">
                                    <a href="{{ route('technician.get-list-room', $building->id) }}" class="btn btn-success m-1 btn-sm"><i class='bx bx-show'></i></a>
                                    <a href="#" class="btn btn-primary m-1 btn-sm" data-bs-toggle="modal" data-bs-target="#update-building-modal-{{ $building->id }}"><i class='bx bx-pencil'></i></a>
                                    <!----- Modal sửa nhà thực hành ----->
                                    <div class="modal fade modal-update" id="update-building-modal-{{ $building->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="addBuildingModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h1 class="modal-title fs-5" id="exampleModalLabel">Chỉnh sửa thông tin nhà thực hành</h1>
                                                </div>
                                                <div class="modal-body">
                                                    <form method="post" action="{{ route('technician.update-building-api', $building->id) }}" id="update-building-form-{{ $building->id }}">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="row mb-3 mt-4">
                                                            <label class="col-md-5 col-label-form fs-6 fw-bold text-md-end">Tên nhà thực hành<span class="required">*</span></label>
                                                            <div class="col-md-7">
                                                                <input type="text" name="building-name" class="form-control fs-6" value="{{ $building->name }}" data-initial-value="{{ $building->name }}"/>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary close-update-btn" data-building-id="{{ $building->id }}" data-bs-dismiss="modal">Đóng</button>
                                                    <button type="button" class="btn btn-primary btn-update-building" data-building-id="{{ $building->id }}">Lưu</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <a href="#" class="btn btn-danger m-1 btn-sm" data-bs-toggle="modal" data-bs-target="#destroy-building-modal-{{ $building->id }}"><i class='bx bx-trash'></i></a>
                                    <form method="post" action="{{ route('technician.destroy-building-api', $building->id) }}" id="destroy-building-form-{{ $building->id }}">
                                        @csrf
                                        @method('DELETE')
                                        <!----- Modal xóa nhà thực hành ----->
                                        <div class="modal fade" id="destroy-building-modal-{{ $building->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="destroyBuildingLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h1 class="modal-title fs-5" id="exampleModalLabel">Xóa nhà thực hành</h1>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p class="text-wrap m-0">Bạn có chắc chắn muốn xóa nhà thực hành?</p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Trở về</button>
                                                        <button type="submit" class="btn btn-danger btn-destroy-building" data-building-id="{{ $building->id }}">Xóa</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="col-12 text-center p-5">
                        <span class="fs-5">Không có dữ liệu nhà thực hành</span>
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

            function submitFormCreateBuilding (form, overlay) {
                const formDataObj = {};
                form.find('input, select, textarea').each(function() {
                    formDataObj[$(this).attr('name')] = $(this).val();
                });

                $.ajax({
                    type: 'POST',
                    data: JSON.stringify(formDataObj),
                    contentType: 'application/json',
                    url: '{{ route("technician.store-building-api") }}',
                    success: function (response) {
                        if (response.success) {
                            showToastSuccess(response.success);
                            form[0].reset();
                            $('#table-building').html(response.table_building);
                            addEventForModalUpdate();
                            addEventForButtons();
                            $('#add-building-modal').modal('hide');
                            $('body').css('overflow', 'auto');
                        } else {
                            if (response.errors['building-name']) {
                                showToastError(response.errors['full-name']);
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

            function submitFormUpdateBuilding (form, buildingId, overlay) {
                const formDataObj = Object.fromEntries(new FormData(form).entries());

                let url = `{{ route("technician.update-building-api", ":buildingId") }}`;
                url = url.replace(':buildingId', buildingId);
                $.ajax({
                    type: 'PUT',
                    data: JSON.stringify(formDataObj),
                    contentType: 'application/json',
                    url: url,
                    success: function (response) {
                        if (response.success) {
                            showToastSuccess(response.success);
                            $('#table-building').html(response.table_building);
                            addEventForModalUpdate();
                            addEventForButtons();
                            $('#update-building-modal-' + buildingId).modal('hide');
                            $('body').css('overflow', 'auto');
                        } else {
                            if (response.errors['building-name']) {
                                showToastError(response.errors['building-name'])
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

            function submitFormDestroyBuilding (buildingId, overlay) {
                let url = `{{ route("technician.destroy-building-api", ":buildingId") }}`;
                url = url.replace(':buildingId', buildingId);
                $.ajax({
                    type: 'DELETE',
                    contentType: 'application/json',
                    url: url,
                    success: function (response) {
                        if (response.success) {
                            showToastSuccess(response.success);
                            $('#table-building').html(response.table_building);
                            addEventForModalUpdate();
                            addEventForButtons();
                        } else {
                            if (response.errors['building']) {
                                showToastError(response.errors['building'])
                            }
                        }

                        $('#destroy-building-modal-' + buildingId).modal('hide');
                        $('body').css('overflow', 'auto');
                        overlay.classList.remove('show');
                    },
                    error: function (error) {
                        console.error(error);
                    }
                });
            }

            $('#btn-add-building').click(function(e) {
                e.preventDefault();
                const overlay = document.getElementById('overlay');
                overlay.classList.add('show');
                $('.modal-backdrop').remove();

                const form = $('#add-building-form');
                submitFormCreateBuilding(form, overlay);
            });

            function addEventForButtons () {
                $('.btn-update-building').off('click');
                $('.btn-destroy-building').off('click');
                $('.close-btn').off('click');
                $('.close-update-btn').off('click');

                $('.btn-update-building').click(function(e) {
                    e.preventDefault();
                    const overlay = document.getElementById('overlay');
                    overlay.classList.add('show');
                    $('.modal-backdrop').remove();

                    const buildingId = $(this).data('building-id');
                    const form = $('#update-building-form-' + buildingId);

                    submitFormUpdateBuilding(form[0], buildingId, overlay);
                });

                $('.btn-destroy-building').click('click', function(e) {
                    e.preventDefault();
                    const overlay = document.getElementById('overlay');
                    overlay.classList.add('show');
                    $('.modal-backdrop').remove();

                    const buildingId = $(this).data('building-id');

                    submitFormDestroyBuilding(buildingId, overlay);
                });

                $('.close-btn').click(function() {
                    $('.modal-backdrop.fade.show').remove();
                });

                $('.close-update-btn').click(function() {
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
