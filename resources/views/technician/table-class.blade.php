@if(count($classes) > 0)
    @foreach($classes as $index => $class)
        <tr>
            <th scope="row" class="text-center">{{ $classes->firstItem() + $index }}</th>
            <td class="text-center">{{ $class->name }}</td>
            <td class="text-center">{{ $class->lecturer->full_name }}</td>
            <td class="text-center">{{ $class->start_date }} <i class='bx bx-right-arrow-alt'></i> {{ $class->end_date }}</td>
            <td class="text-center align-middle">
                <div class="form-check form-switch d-flex justify-content-center">
                    <input class="form-check-input status-class" type="checkbox" role="switch" data-class-id="{{ $class->id }}" {{ $class->status == 'active' ? 'checked' : '' }}>
                </div>
            </td>
            <td class="text-center">
                <div class="d-flex justify-content-center">
                    <div class="wrap-button m-1" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Danh sách sinh viên">
                        <a href="{{ route('technician.get-list-student-class', $class->id) }}" class="btn btn-sm btn-success my-auto btn-student-class"><i class='bx bx-group'></i></a>
                    </div>
                    <div class="wrap-button m-1" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Sửa thông tin lớp học phần">
                        <a href="#" class="btn btn-sm btn-primary my-auto btn-edit-class" id="btn-edit-class-{{ $class->id }}" data-index="{{ $index }}" data-class-id="{{ $class->id }}" data-bs-toggle="modal" data-bs-target="#update-class-modal-{{ $class->id }}"><i class='bx bx-pencil'></i></a>
                    </div>
                    <!----- Modal sửa lớp học ----->
                    <div class="modal fade modal-update" id="update-class-modal-{{ $class->id }}" data-index="{{ $index }}" data-class-id="{{ $class->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="addClassModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-xl modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="exampleModalLabel">Chỉnh sửa thông tin lớp học phần</h1>
                                </div>
                                <div class="modal-body">
                                    <form method="post" action="{{ route('technician.update-class-api', $class->id) }}" id="update-class-form-{{ $class->id }}">
                                        @csrf
                                        @method('PUT')
                                        <div class="container-fluid">
                                            <div class="row">
                                                <div class="col-lg-6 border-bottom">
                                                    <div class="row mb-3 mt-4">
                                                        <label class="col-md-4 col-label-form fs-6 fw-bold text-md-end">Tên lớp học phần<span class="required">*</span></label>
                                                        <div class="col-md-7">
                                                            <input type="text" name="class-name" class="form-control fs-6" value="{{ $class->name }}" data-initial-value="{{ $class->name }}"/>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-3 mt-4">
                                                        <label class="col-md-4 col-label-form fs-6 fw-bold text-md-end">Ngày bắt đầu<span class="required">*</span></label>
                                                        <div class="col-md-7">
                                                            <input type="date" name="start-date" class="form-control fs-6" value="{{ date('Y-m-d', strtotime($class->start_date)) }}" data-initial-value="{{ date('Y-m-d', strtotime($class->start_date)) }}"/>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-3 mt-4">
                                                        <label class="col-md-4 col-label-form fs-6 fw-bold text-md-end">Số buổi học/tuần</label>
                                                        <div class="col-md-7">
                                                            <select id="number-of-session-update-{{ $class->id }}" name="number-of-session" class="form-select form-control fs-6" data-class-id="{{ $class->id }}" data-initial-value="{{ $class->class_sessions_count }}">
                                                                @for ($i = 1; $i <= 5; $i++)
                                                                    <option value="{{ $i }}" {{ $i == $class->class_sessions_count ? 'selected' : '' }}>{{ $i }}</option>
                                                                @endfor
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 border-bottom">
                                                    <div class="row mb-3 mt-4">
                                                        <label class="col-md-4 col-label-form fs-6 fw-bold text-md-end">Giảng viên<span class="required">*</span></label>
                                                        <div class="col-md-7">
                                                            <select name="lecturer" class="form-select form-control fs-6" data-initial-value="{{ $class->lecturer_id }}">
                                                                @foreach($lecturers as $lecturer)
                                                                    <option value="{{ $lecturer->id }}" {{ $lecturer->id == $class->lecturer_id ? 'selected' : '' }}>{{ $lecturer->full_name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-3 mt-4">
                                                        <label class="col-md-4 col-label-form fs-6 fw-bold text-md-end">Ngày kết thúc<span class="required">*</span></label>
                                                        <div class="col-md-7">
                                                            <input type="date" name="end-date" class="form-control fs-6" value="{{ date('Y-m-d', strtotime($class->end_date)) }}" data-initial-value="{{ date('Y-m-d', strtotime($class->end_date)) }}"/>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-3 mt-4">
                                                        <label class="col-md-4 col-label-form fs-6 fw-bold text-md-end">Mã mời vào lớp</label>
                                                        <div class="col-md-7">
                                                            <input type="text" name="class-code" class="form-control fs-6" disabled value="{{ $class->class_code }}" data-initial-value="{{ $class->class_code }}"/>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row m-0 p-0 mt-2" id="session-container-update-{{ $class->id }}" data-class-id="{{ $class->id }}">
                                                    <div class="row mb-3 mt-4 pb-3">
                                                        <label class="col-12 col-label-form fs-6 fw-bold fst-italic text-center">Buổi học 1:</label>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <div class="row mb-3 mt-4">
                                                            <label class="col-md-4 col-label-form fs-6 fw-bold text-md-end">Thứ trong tuần</label>
                                                            <div class="col-md-7">
                                                                <select name="day-of-week[]" class="form-select form-control fs-6">
                                                                    <option value="2" selected>Thứ 2</option>
                                                                    <option value="3">Thứ 3</option>
                                                                    <option value="4">Thứ 4</option>
                                                                    <option value="5">Thứ 5</option>
                                                                    <option value="6">Thứ 6</option>
                                                                    <option value="7">Thứ 7</option>
                                                                    <option value="8">Chủ nhật</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="row mb-3 mt-4">
                                                            <label class="col-md-4 col-label-form fs-6 fw-bold text-md-end">Tòa nhà</label>
                                                            <div class="col-md-7">
                                                                <select id="building-update-{{ $class->id }}" name="building[]" class="form-select form-control fs-6">
                                                                    @foreach($buildings as $building)
                                                                        <option value="{{ $building->id }}">{{ $building->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <div class="row mb-3 mt-4">
                                                            <label class="col-md-4 col-label-form fs-6 fw-bold text-md-end">Tiết học</label>
                                                            <div class="col-md-7">
                                                                <div id="label-lesson-of-class-session-update-{{ $class->id }}" class="fs-6 text-center"></div>
                                                                <div id="lesson-of-class-session-update-{{ $class->id }}"></div>
                                                                <input type="hidden" id="start-lesson-input-update-{{ $class->id }}" name="start-lesson[]">
                                                                <input type="hidden" id="end-lesson-input-update-{{ $class->id }}" name="end-lesson[]">
                                                            </div>
                                                        </div>
                                                        <div class="row mb-3 mt-4">
                                                            <label class="col-md-4 col-label-form fs-6 fw-bold text-md-end">Phòng học</label>
                                                            <div class="col-md-7">
                                                                <select id="room-update-{{ $class->id }}" name="room[]" class="form-select form-control fs-6">
                                                                    @foreach($rooms as $room)
                                                                        @if ($room->building_id == 1)
                                                                            <option value="{{ $room->id }}">{{ $room->name }}</option>
                                                                        @endif
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary close-update-btn" data-class-id="{{ $class->id }}" data-bs-dismiss="modal">Đóng</button>
                                    <button type="button" class="btn btn-primary btn-update-class" data-class-id="{{ $class->id }}">Lưu</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="wrap-button m-1" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Xóa lớp học phần">
                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#destroy-class-modal-{{ $class->id }}"><i class='bx bx-trash'></i></button>
                    </div>
                    <form method="post" action="{{ route('technician.destroy-class-api', $class->id) }}" id="destroy-class-form-{{ $class->id }}">
                        @csrf
                        @method('DELETE')
                        <!----- Modal xóa lớp học ----->
                        <div class="modal fade" id="destroy-class-modal-{{ $class->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="destroyClassLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title fs-5" id="exampleModalLabel">Xóa lớp học phần</h1>
                                    </div>
                                    <div class="modal-body">
                                        <p class="text-wrap m-0">Bạn có chắc chắn muốn xóa lớp học phần?</p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Trở về</button>
                                        <button type="submit" class="btn btn-danger btn-destroy-class" data-class-id="{{ $class->id }}">Xóa</button>
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
        <td colspan="6" class="text-center">Không có dữ liệu lớp học phần</td>
    </tr>
@endif
