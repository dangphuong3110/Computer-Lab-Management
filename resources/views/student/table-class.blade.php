@if(count($classes) > 0)
    @foreach($classes as $index => $class)
        <tr>
            <th scope="row" class="text-center">{{ $classes->firstItem() + $index }}</th>
            <td class="text-center">{{ $class->name }}</td>
            <td class="text-center">{{ $class->lecturer->full_name }}</td>
            <td class="text-center">
                {{ \Carbon\Carbon::parse($class->start_date)->format('d-m-Y') }} <i class='bx bx-right-arrow-alt'></i> {{ \Carbon\Carbon::parse($class->end_date)->format('d-m-Y') }}
            </td>
            <td class="text-center">
                <div class="d-flex justify-content-center">
                    <div class="wrap-button m-1" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Lịch sử điểm danh">
                        <a href="#" class="btn btn-sm btn-secondary my-auto" data-bs-toggle="modal" data-bs-target="#view-attendance-history-modal-{{ $class->id }}"><i class='bx bx-notepad'></i></a>
                    </div>
                    <!----- Modal xem lịch sử báo cáo sự cố ----->
                    <div class="modal fade" id="view-attendance-history-modal-{{ $class->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="viewAttendanceHistoryModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-xl">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="exampleModalLabel">Lịch sử gửi báo cáo sự cố</h1>
                                </div>
                                <div class="modal-body table-responsive">
                                    <table class="table table-bordered border-black">
                                        <thead>
                                        <tr>
                                            <th scope="col" class="text-center" width="5%">STT</th>
                                            <th scope="col" class="text-center" width="30%">Thời gian điểm danh</th>
                                            <th scope="col" class="text-center" width="30%">Vị trí ngồi</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @if(count($attendances[$class->id]) > 0)
                                            @foreach($attendances[$class->id] as $i => $attendance)
                                                <tr>
                                                    <th scope="row" class="text-center">{{ $i + 1 }}</th>
                                                    <td class="text-center">{{ \Carbon\Carbon::parse($attendance->created_at)->format('H:i:s d-m-Y') }}</td>
                                                    <td class="text-center">Vị trí {{ $attendance->computer->position }} - Phòng {{ $attendance->computer->room->name }} - Tòa nhà {{ $attendance->computer->room->building->name }}</td>
                                                <tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="3" class="text-center">Không có dữ liệu điểm danh</td>
                                            </tr>
                                        @endif
                                        </tbody>
                                    </table>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary close-btn" data-bs-dismiss="modal">Đóng</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </td>
        </tr>
    @endforeach
@else
    <tr>
        <td colspan="4" class="text-center">Không có dữ liệu lớp học phần</td>
    </tr>
@endif
