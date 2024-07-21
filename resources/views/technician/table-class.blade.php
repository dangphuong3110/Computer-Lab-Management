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
                    <div class="wrap-button m-1" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Tra cứu thông tin">
                        <a href="#" class="btn btn-sm btn-secondary my-auto" id="btn-search-info-class-{{ $class->id }}" data-class-id="{{ $class->id }}" data-bs-toggle="modal" data-bs-target="#search-info-class-modal-{{ $class->id }}"><i class='bx bx-notepad'></i></a>
                    </div>
                    <!----- Modal tra cứu thông tin lớp học ----->
                    <div class="modal fade" id="search-info-class-modal-{{ $class->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="searchInfoClassModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-xl modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="exampleModalLabel">Tra cứu thông tin sử dụng phòng máy của lớp học phần</h1>
                                </div>
                                <div class="modal-body">
                                    <div class="row mb-3 mt-1">
                                        <label class="col-12 col-label-form fs-6 fw-bold text-start">Ngày học</label>
                                        <div class="col-12">
                                            <select data-class-id="{{ $class->id }}" class="form-select form-control fs-6 class-date-info" style="max-width: 200px;">
                                                @foreach ($class->classInfo as $sessionInfo)
                                                    <option value="{{ $sessionInfo['session_id'] }}">{{ $sessionInfo['date'] }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12" id="table-class-session-info-{{ $class->id }}">
                                            <div class="mb-3 d-flex justify-content-between align-items-center">
                                                <div class="text fs-4">Sơ đồ phòng máy: {{ $class->classInfo[0]['room']->name . ' - ' . $class->classInfo[0]['building']->name }}</div>
                                            </div>
                                            <div class="row border border-black ms-0 me-0">
                                                @php
                                                    $computerNumber = 1;
                                                @endphp
                                                @for ($i = 1; $i <= $class->classInfo[0]['room']->number_of_computer_rows * $class->classInfo[0]['room']->max_computers_per_row; $i++)
                                                    @if ($i % $class->classInfo[0]['room']->max_computers_per_row == 1)
                                                        <div class="col-12 d-flex justify-content-start p-0">
                                                            @endif
                                                            @php
                                                                $computerAtPosition = $class->classInfo[0]['computers']->firstWhere('position', $i);
                                                                if ($computerAtPosition) {
                                                                    $attendance = $class->classInfo[0]['attendances']->firstWhere('computer_id', $computerAtPosition->id);
                                                                    $hasAttendance = $attendance != null;
                                                                }
                                                            @endphp
                                                            @if ($computerAtPosition)
                                                                <div class="position-relative border border-black {{ $hasAttendance ? 'bg-warning' : 'bg-info' }} bg-opacity-50" style="width: {{ 100 / $class->classInfo[0]['room']->max_computers_per_row }}%; height: 100px;">
                                                                    <div class="text-center d-flex justify-content-center align-items-center overflow-hidden h-100">
                                                                        <span style="font-size: 12px;">{{ $hasAttendance ? $attendance->student->full_name : '' }}</span>
                                                                    </div>
                                                                    <div class="position-absolute top-0">{{ $computerNumber }}</div>
                                                                </div>
                                                                @php
                                                                    $computerNumber++;
                                                                @endphp
                                                            @else
                                                                <div class="border border-black bg-secondary" style="width: {{ 100 / $class->classInfo[0]['room']->max_computers_per_row }}%; height: 100px;">

                                                                </div>
                                                            @endif
                                                            @if ($i % $class->classInfo[0]['room']->max_computers_per_row == 0 || $i == $class->classInfo[0]['room']->number_of_computer_rows * $class->classInfo[0]['room']->max_computers_per_row)
                                                        </div>
                                                    @endif
                                                @endfor
                                            </div>
                                            <div class="row note mt-3">
                                                <div class="col-12 d-flex align-items-center">
                                                    <div class="border border-black bg-info bg-opacity-50" style="width: 18px; height: 18px"></div>
                                                    <span class="ms-2">Máy chưa sử dụng</span>
                                                </div>
                                                <div class="col-12 d-flex align-items-center">
                                                    <div class="border border-black bg-warning bg-opacity-50" style="width: 18px; height: 18px"></div>
                                                    <span class="ms-2">Máy đã sử dụng</span>
                                                </div>
                                                <div class="col-12 d-flex align-items-center">
                                                    <div class="border border-black bg-secondary d-flex justify-content-center align-items-center" style="width: 18px; height: 18px"></div>
                                                    <span class="ms-2">Vị trí trống</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary close-update-btn" data-class-id="{{ $class->id }}" data-bs-dismiss="modal">Đóng</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="wrap-button m-1" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Sửa thông tin lớp học phần">
                        <a href="#" class="btn btn-sm btn-primary my-auto btn-edit-class" id="btn-edit-class-{{ $class->id }}" data-index="{{ $index }}" data-class-id="{{ $class->id }}" data-bs-toggle="modal" data-bs-target="#update-class-modal-{{ $class->id }}"><i class='bx bx-pencil'></i></a>
                    </div>
                    <!----- Modal sửa lớp học ----->
                    <div class="modal fade modal-update" id="update-class-modal-{{ $class->id }}" data-index="{{ $index }}" data-class-id="{{ $class->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="updateClassModalLabel" aria-hidden="true">
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
