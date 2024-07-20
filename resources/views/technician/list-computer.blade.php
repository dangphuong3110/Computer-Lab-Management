@extends('technician.layout')
@section('content')
    <div class="row m-5 mb-4 d-flex align-items-center">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="d-flex align-items-center">
                <div class="text" style="min-width: 240px;">Sơ đồ phòng máy</div>
                <ol class="breadcrumb my-auto ms-4" style="min-width: 450px;">
                    <li class="breadcrumb-item"><a href="{{ route('technician.index') }}">Trang chủ</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('technician.get-list-building') }}">Nhà thực hành</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('technician.get-list-room', $room->building_id) }}">Phòng máy</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><a href="{{ route('technician.get-list-computer', $room->id) }}">Sơ đồ phòng máy</a></li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="row p-4 ms-5 me-5 mt-5 mb-5 main-content">
        <div class="col-12">
            <div class="row d-flex justify-content-between align-items-center mb-3">
                <div class="text fs-4 col-12">Sơ đồ phòng máy: {{ $room->name . ' - ' . $building->name }}</div>
            </div>
            <div class="row border border-black ms-0 me-0" id="table-computer">
                @for ($i = 1; $i <= $room->capacity; $i++)
                    @if ($i % 15 == 1)
                        <div class="col-12 d-flex justify-content-start p-0">
                            @endif
                    @php
                        $computerAtPosition = $computers->firstWhere('position', $i);
                    @endphp
                    @if ($computerAtPosition)
                        <div class="dropdown-center border border-black {{ $computerAtPosition->status == 'available' ? 'bg-info' : 'bg-danger' }} bg-opacity-50" style="width: 6.67%; height: 100px;">
                            <a href="#" class="dropdown-toggle position-relative" data-bs-toggle="dropdown">{{ $i }}</a>
                            <!----- Modal sửa máy tính ----->
                            <div class="modal fade modal-update" id="update-computer-modal-{{ $computerAtPosition->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="updateComputerModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h1 class="modal-title fs-5" id="exampleModalLabel">Chỉnh sửa thông tin phòng máy</h1>
                                        </div>
                                        <div class="modal-body">
                                            <form method="post" action="{{ route('technician.update-computer-api', $computerAtPosition->id) }}" id="update-computer-form-{{ $computerAtPosition->id }}">
                                                @csrf
                                                @method('PUT')
                                                <div class="row mb-3 mt-4">
                                                    <label class="col-md-4 col-label-form fs-6 fw-bold text-md-end">Vị trí<span class="required">*</span></label>
                                                    <div class="col-md-7">
                                                        <input type="text" name="position" class="form-control fs-6" value="{{ $computerAtPosition->position }}" data-initial-value="{{ $computerAtPosition->position }}" disabled/>
                                                    </div>
                                                </div>
                                                <div class="row mb-3 mt-4">
                                                    <label class="col-md-4 col-label-form fs-6 fw-bold text-md-end">Cấu hình</label>
                                                    <div class="col-md-7">
                                                        <input type="text" name="configuration" class="form-control fs-6" value="{{ $computerAtPosition->configuration }}" data-initial-value="{{ $computerAtPosition->configuration }}"/>
                                                    </div>
                                                </div>
                                                <div class="row mb-3 mt-4">
                                                    <label class="col-md-4 col-label-form fs-6 fw-bold text-md-end">Ngày mua</label>
                                                    <div class="col-md-7">
                                                        <input type="date" name="purchase-date" class="form-control fs-6" value="{{ $computerAtPosition->purchase_date }}" data-initial-value="{{ $computerAtPosition->purchase_date }}"/>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary close-update-btn" data-bs-dismiss="modal">Đóng</button>
                                            <button type="button" class="btn btn-primary btn-update-computer" data-computer-id="{{ $computerAtPosition->id }}">Lưu</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <form method="post" action="{{ route('technician.destroy-computer-api', $computerAtPosition->id) }}" id="destroy-computer-form-{{ $computerAtPosition->id }}">
                                @csrf
                                @method('DELETE')
                                <!----- Modal xóa máy tính ----->
                                <div class="modal fade" id="destroy-computer-modal-{{ $computerAtPosition->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="destroyComputerLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="exampleModalLabel">Xóa máy tính</h1>
                                            </div>
                                            <div class="modal-body">
                                                <p class="text-wrap m-0">Bạn có chắc chắn muốn xóa máy tính?</p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Trở về</button>
                                                <button type="submit" class="btn btn-danger btn-destroy-computer" data-computer-id="{{ $computerAtPosition->id }}">Xóa</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <!----- Modal xem lịch sử sử dụng máy tính ----->
                            <div class="modal fade modal-update" id="search-usage-info-modal-{{ $computerAtPosition->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="searchUsageInfoModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-xl">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h1 class="modal-title fs-5" id="exampleModalLabel">Lịch sử sử dụng máy tính</h1>
                                        </div>
                                        <div class="modal-body">
                                            <div class="table-responsive">
                                                <table class="table table-bordered">
                                                    <thead>
                                                    <tr>
                                                        <th scope="col" class="text-center">STT</th>
                                                        <th scope="col" class="text-center">Họ và tên</th>
                                                        <th scope="col" class="text-center">Mã sinh viên</th>
                                                        <th scope="col" class="text-center">Ngày sử dụng</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @if(count($computerAtPosition->attendances) > 0)
                                                        @foreach ($computerAtPosition->attendances as $key => $attendance)
                                                            <tr>
                                                                <td class="text-center">{{ $key + 1 }}</td>
                                                                <td class="text-center">{{ $attendance->student->full_name }}</td>
                                                                <td class="text-center">{{ $attendance->student->student_code }}</td>
                                                                <td class="text-center">{{ \Carbon\Carbon::parse($attendance->attendance_date)->format('d-m-Y') }}</td>
                                                            </tr>
                                                        @endforeach
                                                    @else
                                                        <tr>
                                                            <td colspan="4" class="text-center">Không có dữ liệu sử dụng.</td>
                                                        </tr>
                                                    @endif
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary close-update-btn" data-bs-dismiss="modal">Đóng</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#update-computer-modal-{{ $computerAtPosition->id }}">Sửa</a></li>
                                <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#destroy-computer-modal-{{ $computerAtPosition->id }}">Xóa</a></li>
                                <li><a class="dropdown-item btn-start-maintenance {{ $computerAtPosition->status == 'available' ? '' : 'd-none' }}" data-computer-id="{{ $computerAtPosition->id }}" href="#">Bảo trì</a></li>
                                <li><a class="dropdown-item btn-end-maintenance {{ $computerAtPosition->status == 'available' ? 'd-none' : '' }}" data-computer-id="{{ $computerAtPosition->id }}" href="#">Kết thúc bảo trì</a></li>
                                <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#search-usage-info-modal-{{ $computerAtPosition->id }}">Lịch sử sử dụng</a></li>
                            </ul>
                        </div>
                    @else
                        <div class="border border-black" style="width: 6.67%; height: 100px;">
                            <div class="wrap-button" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Thêm máy tính" style="width: 100%; height: 100%;">
                                <a href="#" class="d-flex align-items-center justify-content-center position-relative" data-bs-toggle="modal" data-bs-target="#add-computer-modal-{{ $i }}">
                                    <span class="position-absolute top-0 start-0">{{ $i }}</span>
                                    <i class="bx bx-plus"></i>
                                </a>
                            </div>
                            <!----- Modal thêm máy tính ----->
                            <div class="modal fade" id="add-computer-modal-{{ $i }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="addComputerModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h1 class="modal-title fs-5" id="exampleModalLabel">Thêm máy tính</h1>
                                        </div>
                                        <div class="modal-body">
                                            <form method="post" action="{{ route('technician.store-computer-api') }}" id="add-computer-form-{{ $i }}">
                                                @csrf
                                                <div class="row mb-3 mt-4">
                                                    <label class="col-md-4 col-label-form fs-6 fw-bold text-md-end">Vị trí<span class="required">*</span></label>
                                                    <div class="col-md-7">
                                                        <input type="text" name="position" class="form-control fs-6" value="{{ $i }}" disabled/>
                                                    </div>
                                                </div>
                                                <div class="row mb-3 mt-4">
                                                    <label class="col-md-4 col-label-form fs-6 fw-bold text-md-end">Cấu hình</label>
                                                    <div class="col-md-7">
                                                        <input type="text" name="configuration" class="form-control fs-6"/>
                                                    </div>
                                                </div>
                                                <div class="row mb-3 mt-4">
                                                    <label class="col-md-4 col-label-form fs-6 fw-bold text-md-end">Ngày mua</label>
                                                    <div class="col-md-7">
                                                        <input type="date" name="purchase-date" class="form-control fs-6"/>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary close-btn" data-bs-dismiss="modal">Đóng</button>
                                            <button type="button" class="btn btn-primary btn-add-computer" data-position="{{ $i }}">Thêm</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    @if ($i % 15 == 0 || $i == $room->capacity)
                        </div>
                    @endif
                @endfor
            </div>
            <div class="row note mt-3">
                <div class="col-12 d-flex align-items-center">
                    <div class="border border-black bg-info bg-opacity-50" style="width: 18px; height: 18px"></div>
                    <span class="ms-2">Máy bình thường</span>
                </div>
                <div class="col-12 d-flex align-items-center">
                    <div class="border border-black bg-danger bg-opacity-50" style="width: 18px; height: 18px"></div>
                    <span class="ms-2">Máy đang bảo trì</span>
                </div>
                <div class="col-12 d-flex align-items-center">
                    <div class="border border-black d-flex justify-content-center align-items-center" style="width: 18px; height: 18px">+</div>
                    <span class="ms-2">Vị trí trống</span>
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

            function submitFormCreateComputer (form, position, overlay) {
                const formDataObj = {};
                form.find('input, select, textarea').each(function() {
                    formDataObj[$(this).attr('name')] = $(this).val();
                });

                formDataObj['room_id'] = '{{ $room->id }}';

                $.ajax({
                    type: 'POST',
                    data: JSON.stringify(formDataObj),
                    contentType: 'application/json',
                    url: '{{ route("technician.store-computer-api") }}',
                    success: function (response) {
                        if (response.success) {
                            showToastSuccess(response.success);
                            form[0].reset();
                            $('#table-computer').html(response.table_computer);
                            addEventForModalUpdate();
                            addEventForButtons();
                            $('#add-computer-modal-' + position).modal('hide');
                            $('body').css('overflow', 'auto');
                            $('body').css('padding', '0');
                        } else {
                            if (response.errors['position']) {
                                showToastError(response.errors['position']);
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

            function submitFormUpdateComputer (form, computerId, overlay) {
                const formDataObj = {};
                form.find('input, select, textarea').each(function() {
                    formDataObj[$(this).attr('name')] = $(this).val();
                });

                formDataObj['room_id'] = '{{ $room->id }}';

                let url = `{{ route("technician.update-computer-api", ":computerId") }}`;
                url = url.replace(':computerId', computerId);
                $.ajax({
                    type: 'PUT',
                    data: JSON.stringify(formDataObj),
                    contentType: 'application/json',
                    url: url,
                    success: function (response) {
                        if (response.success) {
                            showToastSuccess(response.success);
                            $('#table-computer').html(response.table_computer);
                            addEventForModalUpdate();
                            addEventForButtons();
                            $('#update-computer-modal-' + computerId).modal('hide');
                            $('body').css('overflow', 'auto');
                            $('body').css('padding', '0');
                        } else {
                            if (response.errors['position']) {
                                showToastError(response.errors['position'])
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

            function submitFormDestroyComputer (computerId, overlay) {
                const formDataObj = {};

                formDataObj['room_id'] = '{{ $room->id }}';

                let url = `{{ route("technician.destroy-computer-api", ":computerId") }}`;
                url = url.replace(':computerId', computerId);
                $.ajax({
                    type: 'DELETE',
                    data: JSON.stringify(formDataObj),
                    contentType: 'application/json',
                    url: url,
                    success: function (response) {
                        if (response.success) {
                            showToastSuccess(response.success);
                            $('#table-computer').html(response.table_computer);
                            addEventForModalUpdate();
                            addEventForButtons();
                            $('#destroy-computer-modal-' + computerId).modal('hide');
                            $('body').css('overflow', 'auto');
                            $('body').css('padding', '0');
                        }

                        overlay.classList.remove('show');
                    },
                    error: function (error) {
                        console.error(error);
                    }
                });
            }

            function addEventForButtons() {
                const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
                const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));

                $('.btn-add-computer').click(function(e) {
                    e.preventDefault();
                    const overlay = document.getElementById('overlay');
                    overlay.classList.add('show');
                    $('.modal-backdrop').remove();

                    const position = $(this).data('position');
                    const form = $('#add-computer-form-' + position);

                    submitFormCreateComputer(form, position, overlay);
                });

                $('.btn-update-computer').off('click').click(function(e) {
                    e.preventDefault();
                    const overlay = document.getElementById('overlay');
                    overlay.classList.add('show');
                    $('.modal-backdrop').remove();

                    const computerId = $(this).data('computer-id');
                    const form = $('#update-computer-form-' + computerId);

                    submitFormUpdateComputer(form, computerId, overlay);
                });

                $('.btn-destroy-computer').off('click').click(function(e) {
                    e.preventDefault();
                    const overlay = document.getElementById('overlay');
                    overlay.classList.add('show');
                    $('.modal-backdrop').remove();

                    const computerId = $(this).data('computer-id');

                    submitFormDestroyComputer(computerId, overlay);
                });

                $('.btn-start-maintenance').off('click').click(function() {
                    const overlay = document.getElementById('overlay');
                    overlay.classList.add('show');

                    const computerId = $(this).data('computer-id');
                    const formDataObj = {};

                    formDataObj['room_id'] = '{{ $room->id }}';

                    $.ajax({
                        type: 'PUT',
                        contentType: 'application/json',
                        data: JSON.stringify(formDataObj),
                        url: `{{ route("technician.start-maintenance-computer-api", ":computerId") }}`.replace(':computerId', computerId),
                        success: function (response) {
                            if (response.success) {
                                showToastSuccess(response.success);
                                $('#table-computer').html(response.table_computer);
                                addEventForModalUpdate();
                                addEventForButtons();
                            }

                            overlay.classList.remove('show');
                        },
                        error: function (error) {
                            console.error(error);
                        }
                    });
                })

                $('.btn-end-maintenance').off('click').click(function() {
                    const overlay = document.getElementById('overlay');
                    overlay.classList.add('show');

                    const computerId = $(this).data('computer-id');
                    const formDataObj = {};

                    formDataObj['room_id'] = '{{ $room->id }}';
                    $.ajax({
                        type: 'PUT',
                        contentType: 'application/json',
                        data: JSON.stringify(formDataObj),
                        url: `{{ route("technician.end-maintenance-computer-api", ":computerId") }}`.replace(':computerId', computerId),
                        success: function (response) {
                            if (response.success) {
                                showToastSuccess(response.success);
                                $('#table-computer').html(response.table_computer);
                                addEventForModalUpdate();
                                addEventForButtons();
                            }

                            overlay.classList.remove('show');
                        },
                        error: function (error) {
                            console.error(error);
                        }
                    });
                })

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
