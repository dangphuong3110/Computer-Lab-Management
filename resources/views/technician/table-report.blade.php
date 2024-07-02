@if(count($reports) > 0)
    @foreach($reports as $index => $report)
        <tr>
            <th scope="row" class="text-center">{{ $reports->firstItem() + $index }}</th>
            <td class="text-center">
                @if ($report->student_id)
                    Sinh viên: {{ $report->student->full_name }}<br>
                    Mã sinh viên: {{ $report->student->student_code }}
                @else
                    Giảng viên: {{ $report->lecturer->full_name }}<br>
                @endif
            </td>
            <td class="text-center">{{ $report->content }}</td>
            <td class="text-center align-middle">
                <span class="p-1 rounded bg-opacity-75
                    @switch($report->status)
                        @case('pending')
                            bg-warning
                            @break
                        @case('processing')
                            bg-primary
                            @break
                        @case('processed')
                            bg-success
                            @break
                        @default
                            bg-secondary
                    @endswitch">
                    @switch($report->status)
                        @case('pending')
                            Chờ xử lý
                            @break
                        @case('processing')
                            Đang xử lý
                            @break
                        @case('processed')
                            Đã xử lý
                            @break
                        @default
                            Không xác định
                    @endswitch
                </span>
            </td>
            <td class="text-center align-middle">{{ \Carbon\Carbon::parse($report->submitted_at)->format('H:i:s d-m-Y') }}</td>
            <td class="text-center align-middle">
                <div class="d-flex justify-content-center">
                    <div class="wrap-button m-1" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Nhận xử lý">
                        <button class="btn btn-sm btn-primary my-auto processing-report" data-report-id="{{ $report->id }}" {{ $report->status == 'processing' ? 'disabled' : '' }}><i class='bx bx-loader-circle'></i></button>
                    </div>
                    <div class="wrap-button m-1" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Hủy nhận xử lý">
                        <button class="btn btn-sm btn-warning my-auto pending-report" data-report-id="{{ $report->id }}" {{ $report->status == 'pending' ? 'disabled' : '' }}><i class='bx bx-loader-circle'></i></button>
                    </div>
                    <div class="wrap-button m-1" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Đã xử lý">
                        <button class="btn btn-sm btn-success my-auto processed-report" data-report-id="{{ $report->id }}" {{ $report->status == 'processed' ? 'disabled' : '' }}><i class='bx bx-check-square'></i></button>
                    </div>
                    <div class="wrap-button m-1" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Xóa báo cáo">
                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#destroy-report-modal-{{ $report->id }}"><i class='bx bx-trash'></i></button>
                    </div>
                    <form method="post" action="{{ route('technician.destroy-report-api', $report->id) }}" id="destroy-report-form-{{ $report->id }}">
                        @csrf
                        @method('DELETE')
                        <!----- Modal xóa báo cáo sự cố ----->
                        <div class="modal fade" id="destroy-report-modal-{{ $report->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="destroyReportLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title fs-5" id="exampleModalLabel">Xóa báo cáo sự cố</h1>
                                    </div>
                                    <div class="modal-body">
                                        <p class="text-wrap m-0">Bạn có chắc chắn muốn xóa báo cáo sự cố?</p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Trở về</button>
                                        <button type="submit" class="btn btn-danger btn-destroy-report" data-report-id="{{ $report->id }}">Xóa</button>
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
        <td colspan="6" class="text-center">Không có dữ liệu báo cáo sự cố</td>
    </tr>
@endif