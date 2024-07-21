@if (count($rooms) > 0)
    @foreach($rooms as $room)
        <div class="col-xl-3 col-lg-4 pt-3">
            <div class="card text-center text-bg-light mb-3">
                <div class="card-header">
                    <h5 class="card-title m-0">{{ $room->name }}</h5>
                </div>
                <div class="card-body">
                    <h6 class="card-title mb-3">Sức chứa: {{ $room->number_of_computer_rows * $room->max_computers_per_row }} máy</h6>
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
                                                <label class="col-md-5 col-label-form fs-6 fw-bold text-md-end">Số hàng máy tính<span class="required">*</span></label>
                                                <div class="col-md-7">
                                                    <input type="text" name="number-of-computer-rows" class="form-control fs-6" value="{{ $room->number_of_computer_rows }}" data-initial-value="{{ $room->number_of_computer_rows }}"/>
                                                </div>
                                            </div>
                                            <div class="row mb-3 mt-4">
                                                <label class="col-md-5 col-label-form fs-6 fw-bold text-md-end">Số lượng máy tính tối đa mỗi hàng<span class="required">*</span></label>
                                                <div class="col-md-7">
                                                    <input type="text" name="max-computers-per-row" class="form-control fs-6" value="{{ $room->max_computers_per_row }}" data-initial-value="{{ $room->max_computers_per_row }}"/>
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
