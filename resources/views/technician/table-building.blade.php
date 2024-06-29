@if (count($buildings) > 0)
    @foreach($buildings as $building)
        <div class="col-xl-3 col-lg-4 pt-3">
            <div class="card text-center text-bg-light mb-3">
                <div class="card-header">
                    <h5 class="card-title m-0">{{ $building->name }}</h5>
                </div>
                <div class="card-body d-flex justify-content-center">
                    <div class="wrap-button" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Danh sách phòng máy">
                        <a href="{{ route('technician.get-list-room', $building->id) }}" class="btn btn-success m-1 btn-sm"><i class='bx bx-show'></i></a>
                    </div>
                    <div class="wrap-button" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Sửa thông tin nhà thực hành">
                        <a href="#" class="btn btn-primary m-1 btn-sm" data-bs-toggle="modal" data-bs-target="#update-building-modal-{{ $building->id }}"><i class='bx bx-pencil'></i></a>
                    </div>
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
                    <div class="wrap-button" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Xóa nhà thực hành">
                        <a href="#" class="btn btn-danger m-1 btn-sm" data-bs-toggle="modal" data-bs-target="#destroy-building-modal-{{ $building->id }}"><i class='bx bx-trash'></i></a>
                    </div>
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
