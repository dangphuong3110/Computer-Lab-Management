<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Education | Đăng nhập</title>
    <link rel="icon" href="{{ asset('images/logo-dhtl.png') }}" type="image/x-icon">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <link rel="stylesheet" href="{{ asset('css/login/style.css') }}">
</head>
<body>
    <div class="form-container">
        <div class="col col-1">
            <div class="image-layer">
                <img src="{{ asset('images/logo-dhtl.png') }}" class="form-image-main logo">
            </div>
            <p class="featured-words">Hệ thống Quản lý phòng thực hành máy tính<br><span>Đại học Thủy lợi</span></p>
        </div>
        <div class="col col-2">
            <div class="btn-box">
                <button class="button btn-1" id="login">Đăng nhập</button>
                <button class="button btn-2" id="register">Đăng ký</button>
            </div>
            <div class="login-form">
                <div class="form-title">
                    <span>Đăng nhập</span>
                </div>
                <div class="form-inputs">
                    <form action="{{ route('login-api') }}" method="POST" id="login-form">
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
                            <a href="#" data-bs-toggle="modal" data-bs-target="#forgot-password">Quên mật khẩu?</a>
                        </div>
                        <div class="input-box">
                            <button type="submit" class="input-submit" id="btn-login">
                                <span>Đăng nhập</span>
                                <i class="bx bx-right-arrow-alt"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="register-form">
                <div class="form-title">
                    <span>Tạo tài khoản</span>
                </div>
                <div class="form-inputs">
                    <form action="{{ route('register-api') }}" method="POST" id="register-form">
                        @csrf
                        <div class="input-box">
                            <input type="text" name="email" class="input-field" placeholder="Email" required>
                            <i class="bx bx-user icon"></i>
                        </div>
                        <div class="input-box">
                            <input type="password" name="password" class="input-field" placeholder="Mật khẩu" required>
                            <i class="bx bx-lock-alt icon"></i>
                        </div>
                        <div class="input-box">
                            <input type="password" name="re-enter-password" class="input-field" placeholder="Nhập lại mật khẩu" required>
                            <i class="bx bx-lock-alt icon"></i>
                        </div>
                        <div class="input-box">
                            <button type="submit" class="input-submit" id="btn-register">
                                <span>Đăng ký</span>
                                <i class="bx bx-right-arrow-alt"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!----- Modal xác thực email ----->
    <div class="modal fade" id="verification-email" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="addLecturerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Xác thực Email</h1>
                </div>
                <div class="modal-body">
                    <form method="post" action="{{ route('verification-email-api') }}" id="verification-email-form">
                        @csrf
                        <div class="row mb-3 mt-4">
                            <label class="col-md-5 col-label-form fs-6 fw-bold text-md-end">Mã xác thực<span class="required">*</span></label>
                            <div class="col-md-7">
                                <input type="text" name="verification-code" class="form-control fs-6"/>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary close-btn" data-bs-dismiss="modal">Đóng</button>
                    <button type="button" id="btn-verification-email" class="btn btn-primary">Xác nhận</button>
                </div>
            </div>
        </div>
    </div>
    <!----- Modal quên mật khẩu ----->
    <div class="modal fade" id="forgot-password" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="addLecturerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Quên mật khẩu</h1>
                </div>
                <div class="modal-body">
                    <form method="post" action="{{ route('forgot-password-api') }}" id="forgot-password-form">
                        @csrf
                        <div class="row mb-3 mt-4">
                            <label class="col-md-5 col-label-form fs-6 fw-bold text-md-end">Nhập email của bạn<span class="required">*</span></label>
                            <div class="col-md-7">
                                <input type="text" name="email" class="form-control fs-6"/>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary close-btn" data-bs-dismiss="modal">Đóng</button>
                    <button type="button" id="btn-forgot-password" class="btn btn-primary">Xác nhận</button>
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
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
<script src="{{ asset('js/login/script.js') }}"></script>
<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        function submitFormLogin(form, overlay) {
            const formDataObj = {};
            form.find('input, select, textarea').each(function() {
                formDataObj[$(this).attr('name')] = $(this).val();
            });

            $.ajax({
                type: 'POST',
                data: JSON.stringify(formDataObj),
                contentType: 'application/json',
                url: '{{ route("login-api") }}',
                success: function (response) {
                    if (response.errors) {
                        if (response.errors['email']) {
                            showToastError(response.errors['email']);
                        }
                        if (response.errors['password']) {
                            showToastError(response.errors['password']);
                        }
                        if (response.errors['is_verified']) {
                            showToastError(response.errors['is_verified']);
                            $('#verification-email').modal('show');
                        }
                        overlay.classList.remove('show');
                    } else if (response.redirect) {
                        window.location.href = response.redirect;
                    }
                },
                error: function (error) {
                    console.error(error);
                }
            });
        }

        function submitFormRegister(form, overlay) {
            const formDataObj = {};
            form.find('input, select, textarea').each(function() {
                formDataObj[$(this).attr('name')] = $(this).val();
            });

            $.ajax({
                type: 'POST',
                data: JSON.stringify(formDataObj),
                contentType: 'application/json',
                url: '{{ route("register-api") }}',
                success: function (response) {
                    if (response.success) {
                        showToastSuccess(response.success);
                        form[0].reset();
                        $('#verification-email').modal('show');
                        $('body').css('overflow', 'auto');
                        $('body').css('padding', '0');
                    } else {
                        if (response.errors['email']) {
                            showToastError(response.errors['email']);
                        } else if (response.errors['password']) {
                            showToastError(response.errors['password']);
                        } else if (response.errors['re-enter-password']) {
                            showToastError(response.errors['re-enter-password']);
                        }
                    }
                    overlay.classList.remove('show');
                },
                error: function (error) {
                    console.error(error);
                }
            });
        }

        function submitFormForgotPassword(form, overlay) {
            const formDataObj = {};
            form.find('input, select, textarea').each(function() {
                formDataObj[$(this).attr('name')] = $(this).val();
            });

            $.ajax({
                type: 'POST',
                data: JSON.stringify(formDataObj),
                contentType: 'application/json',
                url: '{{ route("forgot-password-api") }}',
                success: function (response) {
                    if (response.success) {
                        showToastSuccess(response.success);
                        form[0].reset();
                        $('#forgot-password').modal('hide');
                        $('body').css('overflow', 'auto');
                        $('body').css('padding', '0');
                        $('div.modal-backdrop.fade.show').remove();
                    } else {
                        if (response.errors['email']) {
                            showToastError(response.errors['email']);
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

        function submitFormVerificationEmail(form, overlay) {
            const formDataObj = {};
            form.find('input, select, textarea').each(function() {
                formDataObj[$(this).attr('name')] = $(this).val();
            });

            $.ajax({
                type: 'POST',
                data: JSON.stringify(formDataObj),
                contentType: 'application/json',
                url: '{{ route("verification-email-api") }}',
                success: function (response) {
                    if (response.success) {
                        showToastSuccess(response.success);
                        form[0].reset();
                        $('#verification-email').modal('hide');
                        $('#login').click();
                        $('body').css('overflow', 'auto');
                        $('body').css('padding', '0');
                    } else {
                        if (response.errors['verification-code']) {
                            showToastError(response.errors['verification-code']);
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

        $('#btn-login').click(function(e) {
            e.preventDefault();
            const overlay = document.getElementById('overlay');
            overlay.classList.add('show');

            const form = $('#login-form');
            submitFormLogin(form, overlay);
        });

        $('#btn-register').click(function(e) {
            e.preventDefault();
            const overlay = document.getElementById('overlay');
            overlay.classList.add('show');

            const form = $('#register-form');
            submitFormRegister(form, overlay);
        });

        $('#btn-forgot-password').click(function(e) {
            e.preventDefault();
            const overlay = document.getElementById('overlay');
            overlay.classList.add('show');

            const form = $('#forgot-password-form');
            submitFormForgotPassword(form, overlay);
        });

        $('#btn-verification-email').click(function(e) {
            e.preventDefault();
            const overlay = document.getElementById('overlay');
            overlay.classList.add('show');

            const form = $('#verification-email-form');
            submitFormVerificationEmail(form, overlay);
        });

        $('.close-btn').click(function() {
            $('#forgot-password-form')[0].reset();
            $('#verification-email-form')[0].reset();
            $('.modal-backdrop.fade.show').remove();
        })

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

        function showToastError(text) {
            Toastify({
                text: text,
                duration: 4000,
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
                onClick: function(){}
            }).showToast();
        }
    });
</script>

