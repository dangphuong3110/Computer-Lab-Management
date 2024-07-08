@if(count($technicians) > 0)
    @foreach($technicians as $index => $technician)
        <tr>
            <th scope="row" class="text-center">{{ $technicians->firstItem() + $index }}</th>
            <td class="text-center">{{ $technician->full_name }}</td>
            <td class="text-center">{{ $technician->user->email }}</td>
            <td class="text-center">{{ $technician->user->phone }}</td>
            <td class="text-center">
                @if (optional($technician->reports->first())->report_count)
                    {{ optional($technician->reports->first())->report_count }}
                @elseif ($technician->report_count)
                    {{ $technician->report_count }}
                @else
                    0
                @endif
            </td>
            <td class="text-center">
                @if (optional($technician->reports->first())->avg_processing_time)
                    {{ optional($technician->reports->first())->avg_processing_time }}
                @elseif ($technician->avg_processing_time)
                    {{ $technician->avg_processing_time }}
                @else
                    0
                @endif
            </td>
            <td class="text-center">
                <div class="d-flex justify-content-center">
                    <div class="wrap-button m-1" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Đổi mật khẩu tài khoản">
                        <a href="#" class="btn btn-sm btn-success my-auto" data-bs-toggle="modal" data-bs-target="#update-password-technician-modal-{{ $technician->id }}"><i class='bx bxs-key'></i></a>
                    </div>
                    <!----- Modal đổi mật khẩu kỹ thuật viên ----->
                    <div class="modal fade" id="update-password-technician-modal-{{ $technician->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="updatePasswordTechnicianModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="exampleModalLabel">Đổi mật khẩu tài khoản kỹ thuật viên</h1>
                                </div>
                                <div class="modal-body">
                                    <form method="post" action="{{ route('manager.update-password-technician-api', $technician->id) }}" id="update-password-technician-form-{{ $technician->id }}">
                                        @csrf
                                        <div class="row mb-3 mt-4">
                                            <label class="col-md-5 col-label-form fs-6 fw-bold text-md-end">Mật khẩu mới<span class="required">*</span></label>
                                            <div class="col-md-7">
                                                <input type="password" name="new-password" class="form-control fs-6"/>
                                            </div>
                                        </div>
                                        <div class="row mb-3 mt-4">
                                            <label class="col-md-5 col-label-form fs-6 fw-bold text-md-end">Nhập lại mật khẩu mới<span class="required">*</span></label>
                                            <div class="col-md-7">
                                                <input type="password" name="re-enter-new-password" class="form-control fs-6"/>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary close-update-btn" data-bs-dismiss="modal">Đóng</button>
                                    <button type="submit" class="btn btn-primary btn-update-password-technician" data-technician-id="{{ $technician->id }}">Xác nhận</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="wrap-button m-1" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Sửa thông tin kỹ thuật viên">
                        <a href="#" class="btn btn-sm btn-primary my-auto" data-bs-toggle="modal" data-bs-target="#update-technician-modal-{{ $technician->id }}"><i class='bx bx-pencil'></i></a>
                    </div>
                    <!----- Modal sửa kỹ thuật viên ----->
                    <div class="modal fade modal-update" id="update-technician-modal-{{ $technician->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="addTechnicianModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="exampleModalLabel">Chỉnh sửa thông tin kỹ thuật viên</h1>
                                </div>
                                <div class="modal-body">
                                    <form method="post" action="{{ route('manager.update-technician-api', $technician->id) }}" id="update-technician-form-{{ $technician->id }}">
                                        @csrf
                                        @method('PUT')
                                        <div class="row mb-3 mt-4">
                                            <label class="col-md-4 col-label-form fs-6 fw-bold text-md-end">Họ và tên<span class="required">*</span></label>
                                            <div class="col-md-7">
                                                <input type="text" name="full-name" class="form-control fs-6" value="{{ $technician->full_name }}" data-initial-value="{{ $technician->full_name }}"/>
                                            </div>
                                        </div>
                                        <div class="row mb-3 mt-4">
                                            <label class="col-md-4 col-label-form fs-6 fw-bold text-md-end">Email<span class="required">*</span></label>
                                            <div class="col-md-7">
                                                <input type="text" name="email" class="form-control fs-6" value="{{ $technician->user->email }}" data-initial-value="{{ $technician->user->email }}"/>
                                            </div>
                                        </div>
                                        <div class="row mb-3 mt-4">
                                            <label class="col-md-4 col-label-form fs-6 fw-bold text-md-end">Số điện thoại</label>
                                            <div class="col-md-7">
                                                <input type="text" name="phone" class="form-control fs-6" value="{{ $technician->user->phone }}" data-initial-value="{{ $technician->user->phone }}"/>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary close-update-btn" data-technician-id="{{ $technician->id }}" data-bs-dismiss="modal">Đóng</button>
                                    <button type="button" class="btn btn-primary btn-update-technician" data-technician-id="{{ $technician->id }}">Lưu</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="wrap-button m-1" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Xóa kỹ thuật viên">
                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#destroy-technician-modal-{{ $technician->id }}"><i class='bx bx-trash'></i></button>
                    </div>
                    <form method="post" action="{{ route('manager.destroy-technician-api', $technician->id) }}" id="destroy-technician-form-{{ $technician->id }}">
                        @csrf
                        @method('DELETE')
                        <!----- Modal xóa kỹ thuật viên ----->
                        <div class="modal fade" id="destroy-technician-modal-{{ $technician->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="destroyTechnicianLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title fs-5" id="exampleModalLabel">Xóa kỹ thuật viên</h1>
                                    </div>
                                    <div class="modal-body">
                                        <p class="text-wrap m-0">Bạn có chắc chắn muốn xóa kỹ thuật viên?<br> Điều này sẽ dẫn đến tài khoản và những báo cáo sự cố đã xử lý của kỹ thuật viên này cũng sẽ không còn tồn tại trong hệ thống.</p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Trở về</button>
                                        <button type="submit" class="btn btn-danger btn-destroy-technician" data-technician-id="{{ $technician->id }}">Xóa</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </td>
        </tr>
    @endforeach
@else
    <tr>
        <td colspan="6" class="text-center">Không có dữ liệu kỹ thuật viên</td>
    </tr>
@endif
