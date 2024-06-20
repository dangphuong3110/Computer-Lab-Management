<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Education | Đăng nhập</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
    <div class="form-container">
        <div class="col col-1">
            <div class="image-layer">
                <img src="{{ asset('images/logo-dhtl.png') }}" class="form-image-main logo">
                <img src="{{ asset('images/cloud.png') }}" class="form-image cloud">
                <img src="{{ asset('images/stars.png') }}" class="form-image stars">
            </div>
            <p class="featured-words">Hệ thống Quản lý phòng máy tính thực hành <span>Đại học Thủy lợi</span></p>
        </div>
        <div class="col col-2">
            <div class="login-form">
                <div class="form-title">
                    <span>Đăng nhập</span>
                </div>
                <div class="form-inputs">
                    <form action="{{ route('login') }}" method="POST">
                        @csrf
                        <div class="input-box">
                            <input type="text" name="username" class="input-field" placeholder="Tài khoản" required>
                            <i class="bx bx-user icon"></i>
                        </div>
                        <div class="input-box">
                            <input type="password" name="password" class="input-field" placeholder="Mật khẩu" required>
                            <i class="bx bx-lock-alt icon"></i>
                        </div>
                        <div class="forgot-pass">
                            <a href="#">Quên mật khẩu?</a>
                        </div>
                        <div class="input-box">
                            <button type="submit" class="input-submit">
                                <span>Đăng nhập</span>
                                <i class="bx bx-right-arrow-alt"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
