@extends('technician.layout')
@section('content')
    <div class="row m-5 mb-4 d-flex align-items-center">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="d-flex align-items-center">
                <div class="text">Lớp học phần</div>
                <ol class="breadcrumb my-auto ms-4">
                    <li class="breadcrumb-item"><a href="{{ route('technician.index') }}">Trang chủ</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><a href="{{ route('technician.get-list-class') }}">Lớp học phần</a></li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="row p-4 ms-5 me-5 mt-5 mb-3 main-content">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="text fs-4">Danh sách lớp học</div>
                <div class="d-flex justify-content-end">
                    <a href="#" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#add-class-modal">Thêm</a>
                    <!----- Modal thêm lớp học ----->
                    <div class="modal fade" id="add-class-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="addClassModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-xl modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="exampleModalLabel">Thêm lớp học phần</h1>
                                </div>
                                <div class="modal-body">
                                    <form method="post" action="{{ route('technician.store-class-api') }}" id="add-class-form">
                                        @csrf
                                        <div class="container-fluid">
                                            <div class="row">
                                                <div class="col-lg-6 border-bottom">
                                                    <div class="row mb-3 mt-4">
                                                        <label class="col-md-4 col-label-form fs-6 fw-bold text-md-end">Tên lớp học phần<span class="required">*</span></label>
                                                        <div class="col-md-7">
                                                            <input type="text" name="class-name" class="form-control fs-6"/>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-3 mt-4">
                                                        <label class="col-md-4 col-label-form fs-6 fw-bold text-md-end">Ngày bắt đầu<span class="required">*</span></label>
                                                        <div class="col-md-7">
                                                            <input type="date" name="start-date" class="form-control fs-6"/>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-3 mt-4">
                                                        <label class="col-md-4 col-label-form fs-6 fw-bold text-md-end">Số buổi học/tuần</label>
                                                        <div class="col-md-7">
                                                            <select id="number-of-session-create" name="number-of-session" class="form-select form-control fs-6">
                                                                <option value="1" selected>1</option>
                                                                <option value="2">2</option>
                                                                <option value="3">3</option>
                                                                <option value="4">4</option>
                                                                <option value="5">5</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 border-bottom">
                                                    <div class="row mb-3 mt-4">
                                                        <label class="col-md-4 col-label-form fs-6 fw-bold text-md-end">Giảng viên<span class="required">*</span></label>
                                                        <div class="col-md-7">
                                                            <select name="lecturer" class="form-select form-control fs-6">
                                                                @foreach($lecturers as $lecturer)
                                                                    <option value="{{ $lecturer->id }}">{{ $lecturer->full_name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-3 mt-4">
                                                        <label class="col-md-4 col-label-form fs-6 fw-bold text-md-end">Ngày kết thúc<span class="required">*</span></label>
                                                        <div class="col-md-7">
                                                            <input type="date" name="end-date" class="form-control fs-6"/>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-3 mt-4">
                                                        <label class="col-md-4 col-label-form fs-6 fw-bold text-md-end">Mã mời vào lớp</label>
                                                        <div class="col-md-7">
                                                            <input type="text" name="class-code" id="class-code-create" class="form-control fs-6" disabled/>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row m-0 p-0 mt-2" id="session-container-create">
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
                                                                <select id="building-create" name="building[]" class="form-select form-control fs-6">
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
                                                                <div id="label-lesson-of-class-session-create" class="fs-6 text-center"></div>
                                                                <div id="lesson-of-class-session-create"></div>
                                                                <input type="hidden" id="start-lesson-input-create" name="start-lesson[]">
                                                                <input type="hidden" id="end-lesson-input-create" name="end-lesson[]">
                                                            </div>
                                                        </div>
                                                        <div class="row mb-3 mt-4">
                                                            <label class="col-md-4 col-label-form fs-6 fw-bold text-md-end">Phòng học</label>
                                                            <div class="col-md-7">
                                                                <select id="room-create" name="room[]" class="form-select form-control fs-6">
                                                                    @foreach($rooms as $room)
                                                                        @if ($room->building_id == 1)
                                                                            <option value="{{ $room->id }}">{{ $room->name }} (Sức chứa: {{ $room->capacity }})</option>
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
                                <div class="text-center">
                                    <p class="ps-3 pe-3 attention">
                                        *Chú ý: <a href="{{ route('technician.get-list-lecturer') }}" class="text-danger">Thêm giảng viên</a>,
                                        <a href="{{ route('technician.get-list-building') }}" class="text-danger">nhà thực hành và phòng máy</a> trước khi tạo lớp
                                    </p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary close-btn" data-bs-dismiss="modal">Đóng</button>
                                    <button type="button" id="btn-add-class" class="btn btn-primary">Thêm</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <a href="#" class="btn btn-outline-success ms-2" data-bs-toggle="modal" data-bs-target="#update-lesson-modal">Sửa tiết học</a>
                    <!----- Modal sửa tiết học ----->
                    <div class="modal fade" id="update-lesson-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="updateLessonModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-xl modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="exampleModalLabel">Chỉnh sửa thông tin tiết học</h1>
                                </div>
                                <div class="modal-body">
                                    <form method="post" action="{{ route('technician.update-lesson-api') }}" id="update-lesson-form">
                                        @csrf
                                        <div class="container-fluid">
                                            <div class="row">
                                                @foreach($fullLessons->chunk(5) as $lessonsGroup)
                                                    <div class="col-lg-4">
                                                        @foreach($lessonsGroup as $index => $lesson)
                                                            <div class="row mb-3 mt-4">
                                                                <label class="col-md-4 col-label-form fs-6 fw-bold text-md-end">Tiết học {{ $index + 1 }}:</label>
                                                                <div class="col-md-7 text-center">
                                                                    <input type="time" name="start-lesson[]" value="{{ \Carbon\Carbon::parse($lesson->start_time)->format('H:i') }}" class="form-control fs-6 text-center"/>
                                                                    <i class='bx bx-down-arrow-alt'></i>
                                                                    <input type="time" name="end-lesson[]"  value="{{ \Carbon\Carbon::parse($lesson->end_time)->format('H:i') }}" class="form-control fs-6 text-center"/>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary close-btn" data-bs-dismiss="modal">Đóng</button>
                                    <button type="button" id="btn-update-lesson" class="btn btn-primary">Lưu</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-between mb-3">
                <div class="col-2 d-flex align-items-center">
                    <select class="form-select me-3 border-black" id="records-per-page" name="records-per-page" style="min-width: 80px;">
                        <option value="5" selected>5</option>
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="100">100</option>
                    </select>
                    <span class="small text-muted fw-bold" style="min-width: 130px;">kết quả mỗi trang</span>
                </div>
                <div class="col-5 d-flex align-items-center justify-content-end">
                    <input class="form-control border-black me-2" type="search" id="search-input" placeholder="Tìm kiếm" style="min-width: 130px; max-width: 160px;">
                    <button class="btn btn-outline-dark" type="submit" id="search-button"><i class='bx bx-search-alt'></i></button>
                </div>
            </div>
            <div class="table-responsive" id="table-class">
                <table class="table table-bordered border-black">
                    <thead>
                    <tr>
                        <th scope="col" class="text-center" width="5%">STT</th>
                        <th scope="col" class="text-center" data-sort="name" width="30%">Lớp học phần <i class="bx bx-sort-alt-2"></i></th>
                        <th scope="col" class="text-center" data-sort="lecturer" width="20%">Giảng viên <i class="bx bx-sort-alt-2"></i></th>
                        <th scope="col" class="text-center" data-sort="start_date" width="20%">Bắt đầu - kết thúc <i class="bx bx-sort-alt-2"></i></th>
                        <th scope="col" class="text-center" data-sort="status" width="15%">Đóng/mở lớp <i class="bx bx-sort-alt-2"></i></th>
                        <th scope="col" class="text-center">Hành động</th>
                    </tr>
                    </thead>
                    <tbody>
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
                                                                                                <option value="{{ $room->id }}">{{ $room->name }} (Sức chứa: {{ $room->capacity }})</option>
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
                    </tbody>
                </table>
                <div class="fw-bold skill-pagination" id="paginate-class">
                    {!! $classes->render('pagination::bootstrap-5') !!}
                </div>
            </div>
        </div>
    </div>
    <div class="row p-4 ms-5 me-5 mb-5 main-content">
        <div class="fs-6 fw-bold mb-3">Thời khóa biểu các lớp học thực hành</div>
        <div class="table-responsive">
            <table class="table table-bordered border-black" id="table-schedule">
                <thead>
                <tr>
                    <td rowspan="2" class="text-center align-middle fw-bold">Tòa nhà</td>
                    <td rowspan="2" class="text-center align-middle fw-bold">Phòng</td>
                    <td colspan="15" class="text-center fw-bold">Thứ Hai</td>
                    <td colspan="15" class="text-center fw-bold">Thứ Ba</td>
                    <td colspan="15" class="text-center fw-bold">Thứ Tư</td>
                    <td colspan="15" class="text-center fw-bold">Thứ Năm</td>
                    <td colspan="15" class="text-center fw-bold">Thứ Sáu</td>
                    <td colspan="15" class="text-center fw-bold">Thứ Bảy</td>
                    <td colspan="15" class="text-center fw-bold">Chủ Nhật</td>
                </tr>
                <tr>
                    @for($i=1; $i<=7; $i++)
                        @for($j=1; $j<=15; $j++)
                            <td class="text-center">{!! $j < 10 ? '&nbsp;' . $j . '&nbsp;' : $j !!} </td>
                        @endfor
                    @endfor
                </tr>
                </thead>
                <tbody>
                @foreach($buildings as $building)
                    @php $firstRoom = true; @endphp
                    @foreach($building->rooms as $index => $room)
{{--                        @if ($room->classSessions()->count() == 0)--}}
{{--                            @php continue; @endphp--}}
{{--                        @endif--}}
                        <tr>
                            @if ($firstRoom)
                                <td rowspan="{{ $building->rooms()->count() }}" class="text-center align-middle">{{ $building->name }}</td>
                                @php $firstRoom = false; @endphp
                            @endif
                            <td class="text-center align-middle">{{ $room->name }}</td>
                            @foreach($daysOfWeek as $dayIndex => $dayName)
                                @for ($lesson = 1; $lesson <= 15; $lesson++)
                                    @php
                                        $foundSession = false;
                                        $colspan = 1;
                                        $backgroundColor = '';
                                    @endphp
                                    @foreach ($schedule as $session)
                                        @if ($session['day_of_week'] == $dayIndex && $lesson >= $session['start_lesson'] && $lesson <= $session['end_lesson'] && $session['room_id'] == $room->id)
                                            @if (!$foundSession)
                                                @php
                                                    $colspan = $session['end_lesson'] - $session['start_lesson'] + 1;
                                                    $backgroundColor = 'background-color: ' . '#' . substr(md5($session['class_name'] . $session['class_id']), 0, 6) . ';';
                                                @endphp
                                                <td class="text-center schedule" colspan="{{ $colspan }}" style="{{ $backgroundColor }}" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{ $session['class_name'] }} ({{ $room->name }}-{{ $building->name }})">
{{--                                                    {{ $session['class_name'] }}--}}
                                                </td>
                                                @php
                                                    $foundSession = true;
                                                    $lesson += $colspan - 1;
                                                @endphp
                                            @else
                                                @php $colspan--; @endphp
                                            @endif
                                        @endif
                                    @endforeach
                                    @if (!$foundSession)
                                        <td class="text-center"></td>
                                    @endif
                                @endfor
                            @endforeach
                        </tr>
                    @endforeach
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $(document).ready(function() {
            const currentUrl = new URL(window.location.href);
            $('#records-per-page').val(currentUrl.searchParams.get('records-per-page') ?? 5);

            const buildings = @json($buildings);
            const rooms = @json($rooms);
            const roomDict = {};
            rooms.forEach(room => {
                roomDict[room.id] = room;
            });
            let classSessions = @json($classSessions);
            let lessons = @json($lessons);

            randomClassCode();
            resetInitialValue();
            addEventForModalUpdate();
            addEventForButtons();
            addEventForSelectOption();

            const urlParams = new URLSearchParams(window.location.search);
            const classId = urlParams.get('conflict-session');
            if (classId) {
                const btnEditClass = document.getElementById(`btn-edit-class-${classId}`);
                btnEditClass.click();
            }

            $(function() {
                $("#lesson-of-class-session-create").slider({
                    range: true,
                    min: 1,
                    max: 15,
                    values: [1, 3],
                    slide: function(event, ui) {
                        $("#label-lesson-of-class-session-create").html("Tiết " + ui.values[0] + "<i class='bx bx-right-arrow-alt'></i> Tiết " + ui.values[1]);
                        $("#start-lesson-input-create").val(ui.values[0]);
                        $("#end-lesson-input-create").val(ui.values[1]);
                    }
                });

                $("#label-lesson-of-class-session-create").html("Tiết " + $("#lesson-of-class-session-create").slider("values", 0) + "<i class='bx bx-right-arrow-alt'></i> Tiết " + $("#lesson-of-class-session-create").slider("values", 1));
                $("#start-lesson-input-create").val($("#lesson-of-class-session-create").slider("values", 0));
                $("#end-lesson-input-create").val($("#lesson-of-class-session-create").slider("values", 1));
            });

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            function submitFormCreateClass (form, overlay) {
                const formDataObj = {};
                form.find('input, select, textarea').each(function() {
                    const name = $(this).attr('name');
                    const value = $(this).val();
                    if (name.includes('[]')) {
                        if (!formDataObj[name]) {
                            formDataObj[name] = [];
                        }
                        formDataObj[name].push(value);
                    } else {
                        formDataObj[name] = value;
                    }
                });

                formDataObj['records-per-page'] = $('#records-per-page').val();

                $.ajax({
                    type: 'POST',
                    data: JSON.stringify(formDataObj),
                    contentType: 'application/json',
                    url: '{{ route("technician.store-class-api") }}',
                    success: function (response) {
                        if (response.success) {
                            showToastSuccess(response.success);
                            form[0].reset();
                            resetSessionsContainer();
                            randomClassCode();
                            $('#table-class tbody').html(response.table_class);
                            $('#paginate-class').html(response.links);
                            $('#table-schedule tbody').html(response.table_schedule);
                            classSessions = response.class_sessions;
                            lessons = response.lessons;

                            const currentUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
                            window.history.pushState({path: currentUrl}, '', currentUrl);
                            const newUrl = new URL(window.location.href);
                            newUrl.searchParams.set('records-per-page', $('#records-per-page').val());
                            history.pushState(null, '', newUrl.toString());
                            updatePagination();
                            addEventForModalUpdate();
                            addEventForButtons();
                            addEventForSelectOption();
                            $('#add-class-modal').modal('hide');
                            $('body').css('overflow', 'auto');
                            $('body').css('padding', '0');
                        } else {
                            if (response.errors['class-name']) {
                                showToastError(response.errors['class-session']);
                            } else if (response.errors['start-date']) {
                                showToastError(response.errors['start-date']);
                            } else if (response.errors['end-date']) {
                                showToastError(response.errors['end-date']);
                            } else if (response.errors['class-session']) {
                                showToastError(response.errors['class-session'], response.errors['class-id'] ?? '');
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

            function submitFormUpdateClass (form, classId, overlay) {
                const formDataObj = {};
                form.find('input, select, textarea').each(function() {
                    const name = $(this).attr('name');
                    const value = $(this).val();
                    if (name.includes('[]')) {
                        if (!formDataObj[name]) {
                            formDataObj[name] = [];
                        }
                        formDataObj[name].push(value);
                    } else {
                        formDataObj[name] = value;
                    }
                });

                formDataObj['records-per-page'] = $('#records-per-page').val();

                $.ajax({
                    type: 'PUT',
                    data: JSON.stringify(formDataObj),
                    contentType: 'application/json',
                    url: `{{ route("technician.update-class-api", ":classId") }}`.replace(':classId', classId),
                    success: function (response) {
                        if (response.success) {
                            showToastSuccess(response.success);
                            $('#table-class tbody').html(response.table_class);
                            $('#paginate-class').html(response.links);
                            $('#table-schedule tbody').html(response.table_schedule);
                            classSessions = response.class_sessions;
                            lessons = response.lessons;

                            const currentUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
                            window.history.pushState({path: currentUrl}, '', currentUrl);
                            const newUrl = new URL(window.location.href);
                            newUrl.searchParams.set('records-per-page', $('#records-per-page').val());
                            history.pushState(null, '', newUrl.toString());
                            updatePagination();
                            addEventForModalUpdate();
                            addEventForButtons();
                            $('#update-class-modal-' + classId).modal('hide');
                            $('body').css('overflow', 'auto');
                            $('body').css('padding', '0');
                        } else {
                            if (response.errors['class-name']) {
                                showToastError(response.errors['class-session']);
                            } else if (response.errors['start-date']) {
                                showToastError(response.errors['start-date']);
                            } else if (response.errors['end-date']) {
                                showToastError(response.errors['end-date']);
                            } else if (response.errors['class-session']) {
                                showToastError(response.errors['class-session'], response.errors['class-id'] ?? '');
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

            function submitFormDestroyClass (classId, overlay) {
                let url = `{{ route("technician.destroy-class-api", ":classId") }}`;
                url = url.replace(':classId', classId);
                $.ajax({
                    type: 'DELETE',
                    contentType: 'application/json',
                    url: url,
                    data: JSON.stringify({'records-per-page': $('#records-per-page').val()}),
                    success: function (response) {
                        if (response.success) {
                            showToastSuccess(response.success);
                            $('#table-class tbody').html(response.table_class);
                            $('#paginate-class').html(response.links);
                            $('#table-schedule tbody').html(response.table_schedule);
                            classSessions = response.class_sessions;
                            lessons = response.lessons;

                            const currentUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
                            window.history.pushState({path: currentUrl}, '', currentUrl);
                            const newUrl = new URL(window.location.href);
                            newUrl.searchParams.set('records-per-page', $('#records-per-page').val());
                            history.pushState(null, '', newUrl.toString());
                            updatePagination();
                            addEventForModalUpdate();
                            addEventForButtons();
                        } else {
                            if (response.errors['class']) {
                                showToastError(response.errors['class']);
                            }
                        }

                        $('#destroy-class-modal-' + classId).modal('hide');
                        $('body').css('overflow', 'auto');
                        $('body').css('padding', '0');
                        overlay.classList.remove('show');
                    },
                    error: function (error) {
                        console.error(error);
                    }
                });
            }

            function submitFormUpdateLesson (form, overlay) {
                const formDataObj = {};
                form.find('input, select, textarea').each(function() {
                    const name = $(this).attr('name');
                    const value = $(this).val();
                    if (name.includes('[]')) {
                        if (!formDataObj[name]) {
                            formDataObj[name] = [];
                        }
                        formDataObj[name].push(value);
                    } else {
                        formDataObj[name] = value;
                    }
                });

                $.ajax({
                    type: 'PUT',
                    data: JSON.stringify(formDataObj),
                    contentType: 'application/json',
                    url: `{{ route("technician.update-lesson-api") }}`,
                    success: function (response) {
                        console.log(response);
                        if (response.success) {
                            showToastSuccess(response.success);

                            $('#update-lesson-modal').modal('hide');
                            $('body').css('overflow', 'auto');
                            $('body').css('padding', '0');
                        } else {
                            if (response.errors['lesson']) {
                                showToastError(response.errors['lesson']);
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
                        const newUrl = new URL(link.attr('href'), window.location.origin);
                        searchParams.set('page', newUrl.searchParams.get('page'));

                        const updatedUrl = currentPath + '?' + searchParams.toString();
                        link.attr('href', updatedUrl);
                    }
                });
            }

            $('#btn-add-class').click(function(e) {
                e.preventDefault();
                const overlay = document.getElementById('overlay');
                overlay.classList.add('show');
                $('.modal-backdrop').remove();

                const form = $('#add-class-form');
                submitFormCreateClass(form, overlay);
            });

            $('#btn-update-lesson').click(function(e) {
                e.preventDefault();
                const overlay = document.getElementById('overlay');
                overlay.classList.add('show');
                $('.modal-backdrop').remove();

                const form = $('#update-lesson-form');
                submitFormUpdateLesson(form, overlay);
            });

            $('#records-per-page').change(function() {
                const overlay = document.getElementById('overlay');
                overlay.classList.add('show');
                const recordsPerPage = $(this).val();
                const currentUrl = new URL(window.location.href);
                currentUrl.searchParams.set('records-per-page', recordsPerPage);
                history.pushState(null, '', currentUrl.toString());

                const sortField = currentUrl.searchParams.get('sort-field');
                const sortOrder = currentUrl.searchParams.get('sort-order');
                const data = {};
                if (sortField && sortOrder) {
                    data['sortField'] = sortField;
                    data['sortOrder'] = sortOrder;
                }
                data['recordsPerPage'] = recordsPerPage;

                $.ajax({
                    url: `{{ route('technician.change-records-per-page-class-api') }}`,
                    type: 'GET',
                    data: data,
                    success: function(response) {
                        $('#table-class tbody').html(response.table_class);
                        $('#paginate-class').html(response.links);
                        classSessions = response.class_sessions;
                        lessons = response.lessons;

                        updatePagination();
                        addEventForModalUpdate();
                        addEventForButtons();
                        overlay.classList.remove('show');
                    }
                });
            });

            $('#search-button').click(function() {
                const query = $('#search-input').val();
                const recordsPerPage = $('#records-per-page').val();
                const currentUrl = new URL(window.location.href);
                const sortField = currentUrl.searchParams.get('sort-field');
                const sortOrder = currentUrl.searchParams.get('sort-order');
                const data = {};
                if (sortField && sortOrder) {
                    data['sortField'] = sortField;
                    data['sortOrder'] = sortOrder;
                }
                data['recordsPerPage'] = recordsPerPage;
                data['query'] = query;

                $.ajax({
                    url: `{{ route('technician.search-class-api') }}`,
                    type: 'GET',
                    data: data,
                    success: function(response) {
                        $('#table-class tbody').html(response.table_class);
                        $('#paginate-class').html(response.links);
                        classSessions = response.class_sessions;
                        lessons = response.lessons;

                        updatePagination();
                        addEventForModalUpdate();
                        addEventForButtons();
                    }
                });
            });

            function addEventForButtons () {
                const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
                const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));

                $('th[data-sort]').each(function() {
                    const currentUrl = new URL(window.location.href);
                    const currentField = currentUrl.searchParams.get('sort-field');
                    const currentOrder = currentUrl.searchParams.get('sort-order');
                    const field = $(this).data('sort');

                    if (currentField === field) {
                        if (currentOrder === 'asc') {
                            $(this).find('i').attr('class', 'bx bx-sort-up');
                        } else if (currentOrder === 'desc') {
                            $(this).find('i').attr('class', 'bx bx-sort-down');
                        }
                    } else {
                        $(this).find('i').attr('class', 'bx bx-sort-alt-2');
                    }
                });

                $('.btn-update-class').off('click').click(function(e) {
                    e.preventDefault();
                    const overlay = document.getElementById('overlay');
                    overlay.classList.add('show');
                    $('.modal-backdrop').remove();

                    const classId = $(this).data('class-id');
                    const form = $('#update-class-form-' + classId);

                    submitFormUpdateClass(form, classId, overlay);
                });

                $('.btn-destroy-class').off('click').click(function(e) {
                    e.preventDefault();
                    const overlay = document.getElementById('overlay');
                    overlay.classList.add('show');
                    $('.modal-backdrop').remove();

                    const classId = $(this).data('class-id');

                    submitFormDestroyClass(classId, overlay);
                });

                $('.status-class').click('click', function() {
                    const overlay = document.getElementById('overlay');
                    overlay.classList.add('show');

                    const classId = $(this).data('class-id');
                    $.ajax({
                        type: 'PUT',
                        contentType: 'application/json',
                        url: `{{ route("technician.update-status-class-api", ":classId") }}`.replace(':classId', classId),
                        success: function (response) {
                            if (response.success) {
                                showToastSuccess(response.success);
                                $('#table-schedule tbody').html(response.table_schedule);
                            }

                            overlay.classList.remove('show');
                        },
                        error: function (error) {
                            console.error(error);
                        }
                    });
                })

                $('.btn-edit-class').on('click', function() {
                    const classId = $(this).data('class-id');
                    const index = $(this).data('index');
                    const numberOfSession = $(`#number-of-session-update-${classId}`).val();
                    const sessionContainer = $('#session-container-update-' + classId);
                    sessionContainer.html('');
                    for (let i = 1; i <= numberOfSession; i++) {
                        const sessionData = classSessions[index][i-1];
                        const lessonData = lessons[index][i-1];
                        const session = `
                            <div class="row mb-3 mt-4 pb-3">
                                <label class="col-12 col-label-form fs-6 fw-bold fst-italic text-center">Buổi học ${i}:</label>
                            </div>
                            <div class="col-lg-6 ${i != numberOfSession ? 'border-bottom' : ''}">
                                <div class="row mb-3 mt-4">
                                    <label class="col-md-4 col-label-form fs-6 fw-bold text-md-end">Thứ trong tuần</label>
                                    <div class="col-md-7">
                                        <select name="day-of-week[]" class="form-select form-control fs-6">
                                            <option value="2" ${sessionData.day_of_week == 2 ? 'selected' : ''}>Thứ 2</option>
                                            <option value="3" ${sessionData.day_of_week == 3 ? 'selected' : ''}>Thứ 3</option>
                                            <option value="4" ${sessionData.day_of_week == 4 ? 'selected' : ''}>Thứ 4</option>
                                            <option value="5" ${sessionData.day_of_week == 5 ? 'selected' : ''}>Thứ 5</option>
                                            <option value="6" ${sessionData.day_of_week == 6 ? 'selected' : ''}>Thứ 6</option>
                                            <option value="7" ${sessionData.day_of_week == 7 ? 'selected' : ''}>Thứ 7</option>
                                            <option value="8" ${sessionData.day_of_week == 8 ? 'selected' : ''}>Chủ nhật</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row mb-3 mt-4">
                                    <label class="col-md-4 col-label-form fs-6 fw-bold text-md-end">Tòa nhà</label>
                                    <div class="col-md-7">
                                        <select id="building-update-${classId}-${i}" name="building[]" class="form-select form-control fs-6">
                                            ${buildings.map(building => `<option value="${building.id}" ${building.id == roomDict[sessionData.room_id].building_id ? 'selected' : ''}>${building.name}</option>`).join('')}
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 ${i != numberOfSession ? 'border-bottom' : ''}">
                                <div class="row mb-3 mt-4">
                                    <label class="col-md-4 col-label-form fs-6 fw-bold text-md-end">Tiết học</label>
                                    <div class="col-md-7">
                                        <div id="label-lesson-of-class-session-update-${classId}-${i}" class="fs-6 text-center"></div>
                                        <div id="lesson-of-class-session-update-${classId}-${i}"></div>
                                        <input type="hidden" id="start-lesson-input-update-${classId}-${i}" name="start-lesson[]">
                                        <input type="hidden" id="end-lesson-input-update-${classId}-${i}" name="end-lesson[]">
                                    </div>
                                </div>
                                <div class="row mb-3 mt-4">
                                    <label class="col-md-4 col-label-form fs-6 fw-bold text-md-end">Phòng học</label>
                                    <div class="col-md-7">
                                        <select id="room-update-${classId}-${i}" name="room[]" class="form-select form-control fs-6">
                                            ${rooms.filter(room => room.building_id == roomDict[sessionData.room_id].building_id).map(room => `<option value="${room.id}" ${room.id == sessionData.room_id ? 'selected' : ''}>${room.name} (Sức chứa: ${room.capacity})</option>`).join('')}
                                        </select>
                                    </div>
                                </div>
                            </div>
                        `;
                        sessionContainer.append(session);

                        $(`#building-update-${classId}-${i}`).off('change').change(function () {
                            const buildingId = $(this).val();
                            const roomSelect = $(`#room-update-${classId}-${i}`);
                            roomSelect.html('');
                            rooms.forEach(room => {
                                if (room.building_id == buildingId) {
                                    const option = `<option value="${room.id}">${room.name} (Sức chứa: ${room.capacity})</option>`;
                                    roomSelect.append(option);
                                }
                            });
                        });

                        const lessonIds = lessonData.map(lesson => lesson.id);
                        const minLessonId = Math.min(...lessonIds);
                        const maxLessonId = Math.max(...lessonIds);

                        $(function () {
                            $(`#lesson-of-class-session-update-${classId}-${i}`).slider({
                                range: true,
                                min: 1,
                                max: 15,
                                values: [minLessonId, maxLessonId],
                                slide: function (event, ui) {
                                    $(`#label-lesson-of-class-session-update-${classId}-${i}`).html("Tiết " + ui.values[0] + "<i class='bx bx-right-arrow-alt'></i> Tiết " + ui.values[1]);
                                    $(`#start-lesson-input-update-${classId}-${i}`).val(ui.values[0]);
                                    $(`#end-lesson-input-update-${classId}-${i}`).val(ui.values[1]);
                                }
                            });

                            $(`#label-lesson-of-class-session-update-${classId}-${i}`).html("Tiết " + $(`#lesson-of-class-session-update-${classId}-${i}`).slider("values", 0) + "<i class='bx bx-right-arrow-alt'></i> Tiết " + $(`#lesson-of-class-session-update-${classId}-${i}`).slider("values", 1));
                            $(`#start-lesson-input-update-${classId}-${i}`).val($(`#lesson-of-class-session-update-${classId}-${i}`).slider("values", 0));
                            $(`#end-lesson-input-update-${classId}-${i}`).val($(`#lesson-of-class-session-update-${classId}-${i}`).slider("values", 1));
                        });
                    }
                });

                $('th[data-sort]').off('click').click(function() {
                    const field = $(this).data('sort');
                    const order = $(this).hasClass('ascending') ? 'desc' : 'asc';

                    const currentUrl = new URL(window.location.href);
                    currentUrl.searchParams.set('sort-field', field);
                    currentUrl.searchParams.set('sort-order', order);
                    history.pushState(null, '', currentUrl.toString());

                    $.ajax({
                        url: `{{ route('technician.sort-class-api') }}`,
                        type: 'GET',
                        data: {sortField: field, sortOrder: order, recordsPerPage: $('#records-per-page').val()},
                        success: function(response) {
                            $('#table-class tbody').html(response.table_class);
                            $('#paginate-class').html(response.links);
                            classSessions = response.class_sessions;
                            lessons = response.lessons;
                            updatePagination();
                            addEventForModalUpdate();
                            addEventForButtons();

                            $('th[data-sort] i').attr('class', 'bx bx-sort-alt-2');
                            const iconClass = order === 'asc' ? 'bx-sort-up' : 'bx-sort-down';
                            $(`th[data-sort="${field}"] i`).attr('class', `bx ${iconClass}`);

                            $('th[data-sort]').removeClass('ascending descending');
                            $(`th[data-sort="${field}"]`).addClass(order === 'asc' ? 'ascending' : 'descending');
                        }
                    });
                });

                $('.close-btn').off('click').click(function() {
                    $('#error-message-full-name-create').text('');
                    $('#error-message-class-code-create').text('');
                    $('.modal-backdrop.fade.show').remove();
                });

                $('.close-update-btn').off('click').click(function() {
                    const classId = $(this).data('class-id');
                    $('#error-message-full-name-update-' + classId).text('');
                    $('#error-message-class-code-update-' + classId).text('');
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

            function addEventForSelectOption() {
                const buildings = @json($buildings);
                const rooms = @json($rooms);

                $('#building-create').off('change').change(function() {
                    const buildingId = $(this).val();
                    const roomSelect = $('#room-create');
                    roomSelect.html('');
                    rooms.forEach(room => {
                        if (room.building_id == buildingId) {
                            const option = `<option value="${room.id}">${room.name} (Sức chứa: ${room.capacity})</option>`;
                            roomSelect.append(option);
                        }
                    });
                });

                $('#number-of-session-create').off('change').change(function() {
                    const numberOfSession = $(this).val();
                    const sessionContainer = $('#session-container-create');

                    sessionContainer.html('');
                    for (let i = 1; i <= numberOfSession; i++) {
                        const session = `
                            <div class="row mb-3 mt-4 pb-3">
                                <label class="col-12 col-label-form fs-6 fw-bold fst-italic text-center">Buổi học ${i}:</label>
                            </div>
                            <div class="col-lg-6 ${i != numberOfSession ? 'border-bottom' : ''}">
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
                                        <select id="building-create-${i}" name="building[]" class="form-select form-control fs-6">
                                            ${buildings.map(building => `<option value="${building.id}">${building.name}</option>`).join('')}
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 ${i != numberOfSession ? 'border-bottom' : ''}">
                                <div class="row mb-3 mt-4">
                                    <label class="col-md-4 col-label-form fs-6 fw-bold text-md-end">Tiết học</label>
                                    <div class="col-md-7">
                                        <div id="label-lesson-of-class-session-create-${i}" class="fs-6 text-center"></div>
                                        <div id="lesson-of-class-session-create-${i}"></div>
                                        <input type="hidden" id="start-lesson-input-create-${i}" name="start-lesson[]">
                                        <input type="hidden" id="end-lesson-input-create-${i}" name="end-lesson[]">
                                    </div>
                                </div>
                                <div class="row mb-3 mt-4">
                                    <label class="col-md-4 col-label-form fs-6 fw-bold text-md-end">Phòng học</label>
                                    <div class="col-md-7">
                                        <select id="room-create-${i}" name="room[]" class="form-select form-control fs-6">
                                            ${rooms.filter(room => room.building_id == 1).map(room => `<option value="${room.id}">${room.name} (Sức chứa: ${room.capacity})</option>`).join('')}
                                        </select>
                                    </div>
                                </div>
                            </div>
                        `;
                        sessionContainer.append(session);

                        $(`#building-create-${i}`).off('change').change(function() {
                            const buildingId = $(this).val();
                            const roomSelect = $(`#room-create-${i}`);
                            roomSelect.html('');
                            rooms.forEach(room => {
                                if (room.building_id == buildingId) {
                                    const option = `<option value="${room.id}">${room.name} (Sức chứa: ${room.capacity})</option>`;
                                    roomSelect.append(option);
                                }
                            });
                        });

                        $(function() {
                            $(`#lesson-of-class-session-create-${i}`).slider({
                                range: true,
                                min: 1,
                                max: 15,
                                values: [1, 3],
                                slide: function(event, ui) {
                                    $(`#label-lesson-of-class-session-create-${i}`).html("Tiết " + ui.values[0] + "<i class='bx bx-right-arrow-alt'></i> Tiết " + ui.values[1]);
                                    $(`#start-lesson-input-create-${i}`).val(ui.values[0]);
                                    $(`#end-lesson-input-create-${i}`).val(ui.values[1]);
                                }
                            });

                            $(`#label-lesson-of-class-session-create-${i}`).html("Tiết " + $(`#lesson-of-class-session-create-${i}`).slider("values", 0) + "<i class='bx bx-right-arrow-alt'></i> Tiết " + $(`#lesson-of-class-session-create-${i}`).slider("values", 1));
                            $(`#start-lesson-input-create-${i}`).val($(`#lesson-of-class-session-create-${i}`).slider("values", 0));
                            $(`#end-lesson-input-create-${i}`).val($(`#lesson-of-class-session-create-${i}`).slider("values", 1));
                        });
                    }
                });

                $(document).on('change', '[id^="number-of-session-update-"]', function() {
                    const classId = $(this).data('class-id');
                    const numberOfSession = $(this).val();
                    const sessionContainer = $('#session-container-update-' + classId);

                    $.ajax({
                        type: 'GET',
                        url: `{{ route('technician.get-class-sessions-api', ":classId") }}` . replace(':classId', classId),
                        success: function(response) {
                            console.log(response.classSessions);
                            console.log(response.lessons);
                            sessionContainer.html('');
                            for (let i = 1; i <= numberOfSession; i++) {
                                const sessionData = response.classSessions[i-1];
                                const lessonData = response.lessons[i-1];
                                if (sessionData && lessonData) {
                                    const session = `
                                    <div class="row mb-3 mt-4 pb-3">
                                        <label class="col-12 col-label-form fs-6 fw-bold fst-italic text-center">Buổi học ${i}:</label>
                                    </div>
                                    <div class="col-lg-6 ${i != numberOfSession ? 'border-bottom' : ''}">
                                        <div class="row mb-3 mt-4">
                                            <label class="col-md-4 col-label-form fs-6 fw-bold text-md-end">Thứ trong tuần</label>
                                            <div class="col-md-7">
                                                <select name="day-of-week[]" class="form-select form-control fs-6">
                                                    <option value="2" ${sessionData.day_of_week == 2 ? 'selected' : ''}>Thứ 2</option>
                                                    <option value="3" ${sessionData.day_of_week == 3 ? 'selected' : ''}>Thứ 3</option>
                                                    <option value="4" ${sessionData.day_of_week == 4 ? 'selected' : ''}>Thứ 4</option>
                                                    <option value="5" ${sessionData.day_of_week == 5 ? 'selected' : ''}>Thứ 5</option>
                                                    <option value="6" ${sessionData.day_of_week == 6 ? 'selected' : ''}>Thứ 6</option>
                                                    <option value="7" ${sessionData.day_of_week == 7 ? 'selected' : ''}>Thứ 7</option>
                                                    <option value="8" ${sessionData.day_of_week == 8 ? 'selected' : ''}>Chủ nhật</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row mb-3 mt-4">
                                            <label class="col-md-4 col-label-form fs-6 fw-bold text-md-end">Tòa nhà</label>
                                            <div class="col-md-7">
                                                <select id="building-update-${classId}-${i}" name="building[]" class="form-select form-control fs-6">
                                                    ${buildings.map(building => `<option value="${building.id}" ${building.id == roomDict[sessionData.room_id].building_id ? 'selected' : ''}>${building.name}</option>`).join('')}
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 ${i != numberOfSession ? 'border-bottom' : ''}">
                                        <div class="row mb-3 mt-4">
                                            <label class="col-md-4 col-label-form fs-6 fw-bold text-md-end">Tiết học</label>
                                            <div class="col-md-7">
                                                <div id="label-lesson-of-class-session-update-${classId}-${i}" class="fs-6 text-center"></div>
                                                <div id="lesson-of-class-session-update-${classId}-${i}"></div>
                                                <input type="hidden" id="start-lesson-input-update-${classId}-${i}" name="start-lesson[]">
                                                <input type="hidden" id="end-lesson-input-update-${classId}-${i}" name="end-lesson[]">
                                            </div>
                                        </div>
                                        <div class="row mb-3 mt-4">
                                            <label class="col-md-4 col-label-form fs-6 fw-bold text-md-end">Phòng học</label>
                                            <div class="col-md-7">
                                                <select id="room-update-${classId}-${i}" name="room[]" class="form-select form-control fs-6">
                                                    ${rooms.filter(room => room.building_id == roomDict[sessionData.room_id].building_id).map(room => `<option value="${room.id}" ${room.id == sessionData.room_id ? 'selected' : ''}>${room.name} (Sức chứa: ${room.capacity})</option>`).join('')}
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                `;
                                    sessionContainer.append(session);

                                    $(`#building-update-${classId}-${i}`).off('change').change(function () {
                                        const buildingId = $(this).val();
                                        const roomSelect = $(`#room-update-${classId}-${i}`);
                                        roomSelect.html('');
                                        rooms.forEach(room => {
                                            if (room.building_id == buildingId) {
                                                const option = `<option value="${room.id}">${room.name} (Sức chứa: ${room.capacity})</option>`;
                                                roomSelect.append(option);
                                            }
                                        });
                                    });

                                    const lessonIds = lessonData.map(lesson => lesson.id);
                                    const minLessonId = Math.min(...lessonIds);
                                    const maxLessonId = Math.max(...lessonIds);

                                    $(function () {
                                        $(`#lesson-of-class-session-update-${classId}-${i}`).slider({
                                            range: true,
                                            min: 1,
                                            max: 15,
                                            values: [minLessonId, maxLessonId],
                                            slide: function (event, ui) {
                                                $(`#label-lesson-of-class-session-update-${classId}-${i}`).html("Tiết " + ui.values[0] + "<i class='bx bx-right-arrow-alt'></i> Tiết " + ui.values[1]);
                                                $(`#start-lesson-input-update-${classId}-${i}`).val(ui.values[0]);
                                                $(`#end-lesson-input-update-${classId}-${i}`).val(ui.values[1]);
                                            }
                                        });

                                        $(`#label-lesson-of-class-session-update-${classId}-${i}`).html("Tiết " + $(`#lesson-of-class-session-update-${classId}-${i}`).slider("values", 0) + "<i class='bx bx-right-arrow-alt'></i> Tiết " + $(`#lesson-of-class-session-update-${classId}-${i}`).slider("values", 1));
                                        $(`#start-lesson-input-update-${classId}-${i}`).val($(`#lesson-of-class-session-update-${classId}-${i}`).slider("values", 0));
                                        $(`#end-lesson-input-update-${classId}-${i}`).val($(`#lesson-of-class-session-update-${classId}-${i}`).slider("values", 1));
                                    });
                                } else {
                                    const session = `
                            <div class="row mb-3 mt-4 pb-3">
                                <label class="col-12 col-label-form fs-6 fw-bold fst-italic text-center">Buổi học ${i}:</label>
                            </div>
                            <div class="col-lg-6 ${i != numberOfSession ? 'border-bottom' : ''}">
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
                                        <select id="building-create-${i}" name="building[]" class="form-select form-control fs-6">
                                            ${buildings.map(building => `<option value="${building.id}">${building.name}</option>`).join('')}
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 ${i != numberOfSession ? 'border-bottom' : ''}">
                                <div class="row mb-3 mt-4">
                                    <label class="col-md-4 col-label-form fs-6 fw-bold text-md-end">Tiết học</label>
                                    <div class="col-md-7">
                                        <div id="label-lesson-of-class-session-create-${i}" class="fs-6 text-center"></div>
                                        <div id="lesson-of-class-session-create-${i}"></div>
                                        <input type="hidden" id="start-lesson-input-create-${i}" name="start-lesson[]">
                                        <input type="hidden" id="end-lesson-input-create-${i}" name="end-lesson[]">
                                    </div>
                                </div>
                                <div class="row mb-3 mt-4">
                                    <label class="col-md-4 col-label-form fs-6 fw-bold text-md-end">Phòng học</label>
                                    <div class="col-md-7">
                                        <select id="room-create-${i}" name="room[]" class="form-select form-control fs-6">
                                            ${rooms.filter(room => room.building_id == 1).map(room => `<option value="${room.id}">${room.name} (Sức chứa: ${room.capacity})</option>`).join('')}
                                        </select>
                                    </div>
                                </div>
                            </div>
                        `;
                                    sessionContainer.append(session);

                                    $(`#building-create-${i}`).off('change').change(function() {
                                        const buildingId = $(this).val();
                                        const roomSelect = $(`#room-create-${i}`);
                                        roomSelect.html('');
                                        rooms.forEach(room => {
                                            if (room.building_id == buildingId) {
                                                const option = `<option value="${room.id}">${room.name} (Sức chứa: ${room.capacity})</option>`;
                                                roomSelect.append(option);
                                            }
                                        });
                                    });

                                    $(function() {
                                        $(`#lesson-of-class-session-create-${i}`).slider({
                                            range: true,
                                            min: 1,
                                            max: 15,
                                            values: [1, 3],
                                            slide: function(event, ui) {
                                                $(`#label-lesson-of-class-session-create-${i}`).html("Tiết " + ui.values[0] + "<i class='bx bx-right-arrow-alt'></i> Tiết " + ui.values[1]);
                                                $(`#start-lesson-input-create-${i}`).val(ui.values[0]);
                                                $(`#end-lesson-input-create-${i}`).val(ui.values[1]);
                                            }
                                        });

                                        $(`#label-lesson-of-class-session-create-${i}`).html("Tiết " + $(`#lesson-of-class-session-create-${i}`).slider("values", 0) + "<i class='bx bx-right-arrow-alt'></i> Tiết " + $(`#lesson-of-class-session-create-${i}`).slider("values", 1));
                                        $(`#start-lesson-input-create-${i}`).val($(`#lesson-of-class-session-create-${i}`).slider("values", 0));
                                        $(`#end-lesson-input-create-${i}`).val($(`#lesson-of-class-session-create-${i}`).slider("values", 1));
                                    });
                                }
                            }
                        }
                    });
                });
            }

            function randomClassCode() {
                const classCodeInput = document.getElementById("class-code-create");

                function generateRandomCode(length) {
                    const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
                    let result = '';
                    const charactersLength = characters.length;
                    for (let i = 0; i < length; i++) {
                        result += characters.charAt(Math.floor(Math.random() * charactersLength));
                    }
                    return result;
                }

                classCodeInput.value = generateRandomCode(6);

                $(function() {
                    $("#lesson-of-class-session-create").slider({
                        range: true,
                        min: 1,
                        max: 15,
                        values: [1, 3],
                        slide: function(event, ui) {
                            $("#label-lesson-of-class-session-create").html("Tiết " + ui.values[0] + "<i class='bx bx-right-arrow-alt'></i> Tiết " + ui.values[1]);
                            $("#start-lesson-input-create").val(ui.values[0]);
                            $("#end-lesson-input-create").val(ui.values[1]);
                        }
                    });

                    $("#label-lesson-of-class-session-create").html("Tiết " + $("#lesson-of-class-session-create").slider("values", 0) + "<i class='bx bx-right-arrow-alt'></i> Tiết " + $("#lesson-of-class-session-create").slider("values", 1));
                    $("#start-lesson-input-create").val($("#lesson-of-class-session-create").slider("values", 0));
                    $("#end-lesson-input-create").val($("#lesson-of-class-session-create").slider("values", 1));
                });
            }

            function resetInitialValue() {
                const selectElement = document.getElementById("number-of-session-create");
                selectElement.value = "1";

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

            function resetSessionsContainer() {
                const session = `
                                <div class="row mb-3 mt-4 pb-3">
                                    <label class="col-12 col-label-form fs-6 fw-bold fst-italic text-center">Buổi học 1:</label>
                                </div>
                                <div class="col-lg-6">
                                    <div class="row mb-3 mt-4">
                                        <label class="col-md-4 col-label-form fs-6 fw-bold text-md-end">Thứ trong tuần</label>
                                        <div class="col-md-7">
                                            <select name="day-of-week[]" class="form-select form-control fs-6">
                                                <option value="2">Thứ 2</option>
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
                                            <select id="building-create" name="building[]" class="form-select form-control fs-6">
                                                ${buildings.map(building => `<option value="${building.id}">${building.name}</option>`).join('')}
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="row mb-3 mt-4">
                                        <label class="col-md-4 col-label-form fs-6 fw-bold text-md-end">Tiết học</label>
                                        <div class="col-md-7">
                                            <div id="label-lesson-of-class-session-create" class="fs-6 text-center"></div>
                                            <div id="lesson-of-class-session-create"></div>
                                            <input type="hidden" id="start-lesson-input-create" name="start-lesson[]">
                                            <input type="hidden" id="end-lesson-input-create" name="end-lesson[]">
                                        </div>
                                    </div>
                                    <div class="row mb-3 mt-4">
                                        <label class="col-md-4 col-label-form fs-6 fw-bold text-md-end">Phòng học</label>
                                        <div class="col-md-7">
                                            <select id="room-create" name="room[]" class="form-select form-control fs-6">
                                                ${rooms.filter(room => room.building_id == 1).map(room => `<option value="${room.id}">${room.name} (Sức chứa: ${room.capacity})</option>`).join('')}
                                            </select>
                                        </div>
                                    </div>
                                </div>`;
                $('#session-container-create').html(session);
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

            function showToastError(text, classId='') {
                Toastify({
                    text: text,
                    duration: 4000,
                    newWindow: true,
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
                        if (classId !== '') {
                            const url = `{{ route('technician.get-list-class') }}?conflict-session=${classId}`;
                            window.open(url, '_blank');
                        }
                    }
                }).showToast();
            }
        });
    </script>
@endsection
