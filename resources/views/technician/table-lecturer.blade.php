@if(count($lecturers) > 0)
    @foreach($lecturers as $index => $lecturer)
        <tr>
            <th scope="row" class="text-center">{{ $lecturers->firstItem() + $index }}</th>
            <td class="text-center">{{ $lecturer->full_name }}</td>
            <td class="text-center">{{ $lecturer->faculty }}</td>
            <td class="text-center">{{ $lecturer->user->email }}</td>
            <td class="text-center">{{ $lecturer->user->phone }}</td>
            <td class="text-center">
                <div class="d-flex justify-content-center">
                    <div class="wrap-button m-1" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Đổi mật khẩu tài khoản">
                        <a href="#" class="btn btn-sm btn-success my-auto" data-bs-toggle="modal" data-bs-target="#update-password-lecturer-modal-{{ $lecturer->id }}"><i class='bx bxs-key'></i></a>
                    </div>
                    <!----- Modal đổi mật khẩu giảng viên ----->
                    <div class="modal fade" id="update-password-lecturer-modal-{{ $lecturer->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="updatePasswordLecturerModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="exampleModalLabel">Đổi mật khẩu tài khoản giảng viên</h1>
                                </div>
                                <div class="modal-body">
                                    <form method="post" action="{{ route('technician.update-password-lecturer-api', $lecturer->id) }}" id="update-password-lecturer-form-{{ $lecturer->id }}">
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
                                    <button type="submit" class="btn btn-primary btn-update-password-lecturer" data-lecturer-id="{{ $lecturer->id }}">Xác nhận</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="wrap-button m-1" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Sửa thông tin giảng viên">
                        <a href="#" class="btn btn-sm btn-primary my-auto" data-bs-toggle="modal" data-bs-target="#update-lecturer-modal-{{ $lecturer->id }}"><i class='bx bx-pencil'></i></a>
                    </div>
                    <!----- Modal sửa giảng viên ----->
                    <div class="modal fade modal-update" id="update-lecturer-modal-{{ $lecturer->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="addLecturerModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="exampleModalLabel">Chỉnh sửa thông tin giảng viên</h1>
                                </div>
                                <div class="modal-body">
                                    <form method="post" action="{{ route('technician.update-lecturer-api', $lecturer->id) }}" id="update-lecturer-form-{{ $lecturer->id }}">
                                        @csrf
                                        @method('PUT')
                                        <div class="row mb-3 mt-4">
                                            <label class="col-md-4 col-label-form fs-6 fw-bold text-md-end">Họ và tên<span class="required">*</span></label>
                                            <div class="col-md-7">
                                                <input type="text" name="full-name" class="form-control fs-6" value="{{ $lecturer->full_name }}" data-initial-value="{{ $lecturer->full_name }}"/>
                                            </div>
                                        </div>
                                        <div class="row mb-3 mt-4">
                                            <label class="col-md-4 col-label-form fs-6 fw-bold text-md-end">Email<span class="required">*</span></label>
                                            <div class="col-md-7">
                                                <input type="text" name="email" class="form-control fs-6" value="{{ $lecturer->user->email }}" data-initial-value="{{ $lecturer->user->email }}"/>
                                            </div>
                                        </div>
                                        <div class="row mb-3 mt-4">
                                            <label class="col-md-4 col-label-form fs-6 fw-bold text-md-end">Số điện thoại</label>
                                            <div class="col-md-7">
                                                <input type="text" name="phone" class="form-control fs-6" value="{{ $lecturer->user->phone }}" data-initial-value="{{ $lecturer->user->phone }}"/>
                                            </div>
                                        </div>
                                        <div class="row mb-3 mt-4">
                                            <label class="col-md-4 col-label-form fs-6 fw-bold text-md-end">Học hàm/Học vị</label>
                                            <div class="col-md-7">
                                                <input type="text" name="academic-rank" class="form-control fs-6" value="{{ $lecturer->academic_rank }}" data-initial-value="{{ $lecturer->academic_rank }}"/>
                                            </div>
                                        </div>
                                        <div class="row mb-3 mt-4">
                                            <label class="col-md-4 col-label-form fs-6 fw-bold text-md-end">Bộ môn</label>
                                            <div class="col-md-7">
                                                <input type="text" name="department" class="form-control fs-6" value="{{ $lecturer->department }}" data-initial-value="{{ $lecturer->department }}"/>
                                            </div>
                                        </div>
                                        <div class="row mb-3 mt-4">
                                            <label class="col-md-4 col-label-form fs-6 fw-bold text-md-end">Khoa</label>
                                            <div class="col-md-7">
                                                <input type="text" name="faculty" class="form-control fs-6" value="{{ $lecturer->faculty }}" data-initial-value="{{ $lecturer->faculty }}"/>
                                            </div>
                                        </div>
                                        <div class="row mb-3 mt-4">
                                            <label class="col-md-4 col-label-form fs-6 fw-bold text-md-end">Chức vị</label>
                                            <div class="col-md-7">
                                                <input type="text" name="position" class="form-control fs-6" value="{{ $lecturer->position }}" data-initial-value="{{ $lecturer->position }}"/>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary close-update-btn" data-lecturer-id="{{ $lecturer->id }}" data-bs-dismiss="modal">Đóng</button>
                                    <button type="button" class="btn btn-primary btn-update-lecturer" data-lecturer-id="{{ $lecturer->id }}">Lưu</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="wrap-button m-1" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Xóa giảng viên">
                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#destroy-lecturer-modal-{{ $lecturer->id }}"><i class='bx bx-trash'></i></button>
                    </div>
                    <form method="post" action="{{ route('technician.destroy-lecturer-api', $lecturer->id) }}" id="destroy-lecturer-form-{{ $lecturer->id }}">
                        @csrf
                        @method('DELETE')
                        <!----- Modal xóa giảng viên ----->
                        <div class="modal fade" id="destroy-lecturer-modal-{{ $lecturer->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="destroyLecturerLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title fs-5" id="exampleModalLabel">Xóa giảng viên</h1>
                                    </div>
                                    <div class="modal-body">
                                        <p class="text-wrap m-0">Bạn có chắc chắn muốn xóa giảng viên?<br> Điều này sẽ dẫn đến tài khoản của giảng viên cũng sẽ không còn tồn tại trong hệ thống.</p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Trở về</button>
                                        <button type="submit" class="btn btn-danger btn-destroy-lecturer" data-lecturer-id="{{ $lecturer->id }}">Xóa</button>
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
        <td colspan="6" class="text-center">Không có dữ liệu giảng viên</td>
    </tr>
@endif
