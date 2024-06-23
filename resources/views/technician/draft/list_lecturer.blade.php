@extends('technician.layout')
@section('content')
    <div class="row m-5 mb-4 d-flex align-items-center">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="d-flex align-items-center">
                <div class="text">Giảng viên</div>
                <ol class="breadcrumb my-auto ms-4">
                    <li class="breadcrumb-item"><a href="{{ route('technician.index') }}">Trang chủ</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><a href="{{ route('technician.get-list-lecturer') }}">Giảng viên</a></li>
                </ol>
            </nav>
        </div>
    </div>

    @if($message = Session::get('success'))
        <div class="ms-5 me-5 alert alert-success" id="success-alert">
            {{ $message }}
        </div>
    @endif

    <div class="row p-4 ms-5 me-5 mb-0 main-content">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <div class="text fs-4">Danh sách giảng viên</div>
                <div class="d-flex justify-content-end">
                    <a href="{{ route('technician.create-lecturer') }}" class="btn btn-primary">Thêm</a>
                    <button type="button" class="btn btn-secondary ms-2">Nhập file</button>
                </div>
            </div>
            <div class="table-responsive" id="table-lecturer">
                <table class="table table-bordered table-hover">
                    <thead>
                    <tr>
                        <th scope="col" class="text-center">STT</th>
                        <th scope="col" class="text-center">Họ và tên</th>
                        <th scope="col" class="text-center">Học vị</th>
                        <th scope="col" class="text-center">Bộ môn</th>
                        <th scope="col" class="text-center">Khoa</th>
                        <th scope="col" class="text-center">Chức vụ</th>
                        <th scope="col" class="text-center">Hành động</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(count($lecturers) > 0)
                        @foreach($lecturers as $index => $lecturer)
                            <tr>
                                <th scope="row" class="text-center">{{ $lecturers->firstItem() + $index }}</th>
                                <td class="text-center">{{ $lecturer->full_name }}</td>
                                <td class="text-center">{{ $lecturer->academic_rank }}</td>
                                <td class="text-center">{{ $lecturer->department }}</td>
                                <td class="text-center">{{ $lecturer->faculty }}</td>
                                <td class="text-center">{{ $lecturer->position }}</td>
                                <td class="text-center">
                                    <a href="{{ route('technician.edit-lecturer', $lecturer->id) }}" class="btn btn-sm btn-primary"><i class='bx bx-pencil'></i></a>
                                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#destroy-lecturer-modal-{{ $lecturer->id }}"><i class='bx bx-trash'></i></button>
                                </td>
                            </tr>
                            <form id="destroy-lecturer-form-{{ $lecturer->id }}" method="post" action="{{ route('technician.destroy-lecturer', $lecturer->id) }}">
                                @csrf
                                @method('DELETE')
                                <!----- Modal xóa giảng viên ----->
                                <div class="modal fade" id="destroy-lecturer-modal-{{ $lecturer->id }}" tabindex="-1" aria-labelledby="destroyLecturerLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="exampleModalLabel">Xóa giảng viên</h1>
                                            </div>
                                            <div class="modal-body">
                                                Bạn có chắc chắn muốn xóa giảng viên?<br> Điều này sẽ dẫn đến tài khoản của giảng viên cũng sẽ không còn tồn tại trong hệ thống.
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary close-btn" data-bs-dismiss="modal">Trở về</button>
                                                <button type="submit" class="btn btn-danger">Xóa</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        @endforeach
                    </tbody>
                    @else
                        <tr>
                            <td colspan="7" class="text-center">No Data Found</td>
                        </tr>
                    @endif
                </table>
                <div class="fw-bold skill-pagination" id="paginate-lecturer">
                    {!! $lecturers->render('pagination::bootstrap-5') !!}
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#success-alert').delay(3000).fadeOut();
        });
    </script>
@endsection
