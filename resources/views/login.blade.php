<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Education | Đăng nhập</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('css/login/style.css') }}">
</head>
<body>
    <div class="form-container">
        <div class="col col-1">
            <div class="image-layer">
                <img src="{{ asset('images/logo-dhtl.png') }}" class="form-image-main logo">
                <img src="{{ asset('images/cloud.png') }}" class="form-image cloud">
                <img src="{{ asset('images/stars.png') }}" class="form-image stars">
            </div>
            <p class="featured-words">Hệ thống Quản lý phòng máy tính thực hành<br><span>Đại học Thủy lợi</span></p>
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
                            <input type="text" name="email" class="input-field" placeholder="Email" required>
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
    <div id="overlay" class="overlay">
        <div class="overlay-content">
            <div class="spinner-border spinner-border-sm" role="status">
                <span class="visually-hidden"></span>
            </div>
            Vui lòng chờ...
        </div>
    </div>
</body>
</html>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script>
    window.addEventListener("beforeunload", function (event) {
        const overlay = document.getElementById('overlay');
        overlay.classList.add('show');
        event.returnValue = '';
    });

    window.addEventListener("unload", function (event) {
        const overlay = document.getElementById('overlay');
        overlay.classList.remove('show');
    });

    const loginBtn = document.querySelector('.input-submit');
    loginBtn.addEventListener('click', function() {
        console.log(1);
        const overlay = document.getElementById('overlay');
        overlay.classList.add('show');
    });
</script>
