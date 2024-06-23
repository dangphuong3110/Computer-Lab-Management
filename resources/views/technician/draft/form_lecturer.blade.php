@extends('technician.layout')
@section('content')
    <div class="row p-5 mb-0 d-flex align-items-center">
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

    <div class="row pt-4 pb-4 ms-5 me-5 mb-0 main-content">
        <div class="col-12">
            <div class="d-flex justify-content-center align-items-center mb-1">
                <div class="text fs-4">{{ isset($lecturer) ? 'Chỉnh sửa thông tin ' : 'Thêm ' }} giảng viên</div>
            </div>
            <form method="post" action="{{ route(isset($lecturer) ? 'technician.update-lecturer' : 'technician.store-lecturer', $lecturer->id ?? '') }}">
                @csrf
                @if(isset($lecturer))
                    @method('PUT')
                @endif
                <div class="row mb-3 mt-4">
                    <label class="col-md-4 col-label-form fs-6 fw-bold text-md-end">Họ và tên<span class="required">*</span></label>
                    <div class="col-md-7">
                        <input type="text" name="full-name" class="form-control fs-6" value="{{ old('full-name', $lecturer->full_name ?? '') }}"/>
                        @error('full-name')
                        <span role="alert" class="text-danger fs-6 d-flex align-items-center justify-content-center">
                            <br>
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                </div>
                <div class="row mb-3 mt-4">
                    <label class="col-md-4 col-label-form fs-6 fw-bold text-md-end">Email<span class="required">*</span></label>
                    <div class="col-md-7">
                        <input type="text" name="email" class="form-control fs-6" value="{{ old('email', $lecturer->user->email ?? '') }}"/>
                        @error('email')
                        <span role="alert" class="text-danger fs-6 d-flex align-items-center justify-content-center">
                            <br>
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                </div>
                <div class="row mb-3 mt-4">
                    <label class="col-md-4 col-label-form fs-6 fw-bold text-md-end">Học vị</label>
                    <div class="col-md-7">
                        <input type="text" name="academic-rank" class="form-control fs-6" value="{{ old('academic-rank', $lecturer->academic_rank ?? '') }}"/>
                    </div>
                </div>
                <div class="row mb-3 mt-4">
                    <label class="col-md-4 col-label-form fs-6 fw-bold text-md-end">Bộ môn</label>
                    <div class="col-md-7">
                        <input type="text" name="department" class="form-control fs-6" value="{{ old('department', $lecturer->department ?? '') }}"/>
                    </div>
                </div>
                <div class="row mb-3 mt-4">
                    <label class="col-md-4 col-label-form fs-6 fw-bold text-md-end">Khoa</label>
                    <div class="col-md-7">
                        <input type="text" name="faculty" class="form-control fs-6" value="{{ old('faculty', $lecturer->faculty ?? '') }}"/>
                    </div>
                </div>
                <div class="row mb-3 mt-4">
                    <label class="col-md-4 col-label-form fs-6 fw-bold text-md-end">Chức vị</label>
                    <div class="col-md-7">
                        <input type="text" name="position" class="form-control fs-6" value="{{ old('position', $lecturer->position ?? '') }}"/>
                    </div>
                </div>
                <div class="d-flex align-items-center justify-content-center">
                    <a href="{{ route('technician.get-list-lecturer') }}" class="btn btn-secondary close-btn">Trở về</a>
                    <button type="submit" class="btn btn-primary ms-2">{{ isset($lecturer) ? 'Lưu' : 'Thêm' }}</button>
                </div>
            </form>
            @if (!isset($lecturer))
                <div class="text-center mt-2">
                    <p class="note">*Chú ý: Tài khoản sẽ được tạo tự động với tên đăng nhập là <span>Email giảng viên</span> và mật khẩu là <span>123456</span></p>
                </div>
            @endif
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
{{--    <script>--}}
{{--        $(document).ready(function() {--}}
{{--            $('#success-alert').delay(3000).fadeOut();--}}
{{--        });--}}
{{--    </script>--}}
@endsection
