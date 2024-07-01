@if(count($reports) > 0)
    @foreach($reports as $index => $report)
        <tr>
            <th scope="row" class="text-center">{{ $reports->firstItem() + $index }}</th>
            <td class="text-center">
                Tên: {{ $report->student->full_name }}<br>
                Mã sinh viên: {{ $report->student->student_code }}
            </td>
            <td class="text-center">{{ $report->content }}</td>
            <td class="text-center align-middle">
                <span class="p-1 rounded bg-opacity-75 {{ $report->is_approved ? 'bg-success' : 'bg-warning' }}">
                    {{ $report->is_approved ? 'Đã duyệt' : 'Chưa duyệt' }}
                </span>
            </td>
            <td class="text-center align-middle">{{ \Carbon\Carbon::parse($report->submitted_at)->format('H:i:s d-m-Y') }}</td>
            <td class="text-center align-middle">
                <div class="d-flex justify-content-center">
                    <div class="wrap-button m-1" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Duyệt báo cáo">
                        <button class="btn btn-sm btn-success my-auto approve-report" data-report-id="{{ $report->id }}" {{ $report->is_approved ? 'disabled' : '' }}><i class='bx bx-check-square'></i></button>
                    </div>
                    <div class="wrap-button m-1" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Hủy duyệt báo cáo">
                        <button class="btn btn-sm btn-warning my-auto disapprove-report" data-report-id="{{ $report->id }}" {{ $report->is_approved ? '' : 'disabled' }}><i class='bx bx-no-entry'></i></button>
                    </div>
                    <div class="wrap-button m-1" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Xóa báo cáo">
                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#destroy-student-report-modal-{{ $report->id }}"><i class='bx bx-trash'></i></button>
                    </div>
                    <form method="post" action="{{ route('lecturer.destroy-report-api', $report->id) }}" id="destroy-student-report-form-{{ $report->id }}">
                        @csrf
                        @method('DELETE')
                        <!----- Modal xóa giảng viên ----->
                        <div class="modal fade" id="destroy-student-report-modal-{{ $report->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="destroyStudentReportLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title fs-5" id="exampleModalLabel">Xóa báo cáo</h1>
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
