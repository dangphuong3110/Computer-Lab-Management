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
                    <th scope="col" class="text-center" width="30%">Nội dung</th>
                    <th scope="col" class="text-center" width="30%">Trạng thái xử lý</th>
                    <th scope="col" class="text-center" width="20%">Thời gian gửi</th>
                </tr>
                </thead>
                <tbody>
                @if(count($reports) > 0)
                    @foreach($reports as $index => $report)
                        <tr>
                            <th scope="row" class="text-center">{{ $index + 1 }}</th>
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
                                        @case('cancel')
                                            bg-danger
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
                                        @case('cancel')
                                            Không xử lý
                                            @break
                                        @default
                                            Không xác định
                                    @endswitch
                                </span>
                            </td>
                            <td class="text-center align-middle">{{ $report->created_at->format('H:m:i d-m-Y') }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="3" class="text-center">Không có dữ liệu báo cáo sự cố</td>
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
