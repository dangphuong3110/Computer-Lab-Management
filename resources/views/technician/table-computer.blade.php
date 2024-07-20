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
