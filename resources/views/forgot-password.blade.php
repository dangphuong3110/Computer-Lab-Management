<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Education | Đặt lại mật khẩu</title>
    <link rel="icon" href="{{ asset('images/logo-dhtl.png') }}" type="image/x-icon">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <link rel="stylesheet" href="{{ asset('css/login/style.css') }}">
</head>
<body>
<div class="modal fade show" id="reset-password" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="addLecturerModalLabel" aria-hidden="true" role="dialog" style="display: block;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Đặt lại mật khẩu</h1>
            </div>
            <div class="modal-body">
                <form method="post" action="{{ route('reset-password-api') }}" id="reset-password-form">
                    @csrf
                    <div class="row mb-3 mt-4">
                        <label class="col-md-5 col-label-form fs-6 fw-bold text-md-end">Mật khẩu mới<span class="required">*</span></label>
                        <div class="col-md-7">
                            <input type="password" name="new-password" class="form-control fs-6"/>
                        </div>
                    </div>
                    <div class="row mb-3 mt-4">
                        <label class="col-md-5 col-label-form fs-6 fw-bold text-md-end">Nhập lại mật khẩu mới<span class="required">*</span></label>
                        <div class="col-md-7">
                            <input type="password" name="re-enter-new-password" class="form-control fs-6"/>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="submit" id="btn-reset-password" class="btn btn-primary">Xác nhận</button>
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
<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        window.addEventListener("beforeunload", function (event) {
            const overlay = document.getElementById('overlay');
            overlay.classList.add('show');
            event.returnValue = '';
        });

        window.addEventListener("unload", function (event) {
            const overlay = document.getElementById('overlay');
            overlay.classList.remove('show');
        });

        function submitFormResetPassword(form, overlay) {
            const formDataObj = {};
            form.find('input, select, textarea').each(function() {
                formDataObj[$(this).attr('name')] = $(this).val();
            });
            formDataObj['token'] = '{{ $token }}';

            $.ajax({
                type: 'POST',
                data: JSON.stringify(formDataObj),
                contentType: 'application/json',
                url: '{{ route("reset-password-api") }}',
                success: function (response) {
                    if (response.success) {
                        showToastSuccess(response.success);
                        window.location.href = response.redirect;
                    } else {
                        if (response.errors['new-password']) {
                            showToastError(response.errors['new-password']);
                        }
                        if (response.errors['re-enter-new-password']) {
                            showToastError(response.errors['re-enter-new-password']);
                        }
                        if (response.errors['is_verified']) {
                            showToastError(response.errors['is_verified']);
                            $('#verification-email').modal('show');
                        }
                        overlay.classList.remove('show');
                    }
                },
                error: function (error) {
                    console.error(error);
                }
            });
        }

        $('#btn-reset-password').click(function(e) {
            e.preventDefault();
            const overlay = document.getElementById('overlay');
            overlay.classList.add('show');

            const form = $('#reset-password-form');
            submitFormResetPassword(form, overlay);
        });

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

