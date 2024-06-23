<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Education | {{ $title }}</title>
    <link rel="icon" href="{{ asset('images/logo-dhtl.png') }}" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700,200" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/homepage/style.css') }}">
    @yield('css')
</head>
<body>
<nav class="sidebar close">
    <header>
        <div class="image-text">
            <span class="image">
                <img src="{{ asset('images/logo-dhtl.png') }}" alt="logo">
            </span>
            <div class="text header-text">
                <span class="name-university">Đại học Thủy lợi</span>
                <span class="name">{{ $user->technician->full_name }}</span>
            </div>
        </div>
        <i class='bx bx-chevron-right toggle'></i>
    </header>

    <div class="menu-bar">
        <div class="menu">
            <li class="search-box">
                <i class='bx bx-search icon'></i>
                <input type="search" placeholder="Tìm kiếm...">
            </li>
            <ul class="menu-links">
                <li class="nav-link {{ request()->routeIs('technician.index') ? 'active' : '' }}">
                    <a href="{{ route('technician.index') }}">
                        <i class='bx bx-home-alt icon'></i>
                        <span class="text nav-text">Trang chủ</span>
                    </a>
                </li>
                <li class="nav-link {{ request()->routeIs('technician.get-list-lecturer-api', 'technician.get-list-lecturer', 'technician.create-lecturer', 'technician.edit-lecturer') ? 'active' : '' }}">
                    <a href="{{ route('technician.get-list-lecturer') }}">
                        <i class='bx bx-user icon'></i>
                        <span class="text nav-text">Giảng viên</span>
                    </a>
                </li>
                <li class="nav-link">
                    <a href="#">
                        <i class='bx bx-group icon'></i>
                        <span class="text nav-text">Sinh viên</span>
                    </a>
                </li>
                <li class="nav-link">
                    <a href="#">
                        <i class='bx bxs-grid icon'></i>
                        <span class="text nav-text">Lớp tín chỉ</span>
                    </a>
                </li>
                <li class="nav-link">
                    <a href="#">
                        <i class='bx bx-buildings icon'></i>
                        <span class="text nav-text">Phòng máy</span>
                    </a>
                </li>
                <li class="nav-link">
                    <a href="#">
                        <i class='bx bx-comment-error bx-tada icon' ></i>
                        <span class="text nav-text">Báo cáo sự cố</span>
                    </a>
                </li>
            </ul>
        </div>

        <div class="bottom-content">
            <li class="logout">
                <form id="logout-form" action="{{ route('logout') }}" method="POST">
                    @csrf
                    <a href="#" onclick="confirmLogout()">
                        <i class='bx bx-log-out icon'></i>
                        <span class="text nav-text">Đăng xuất</span>
                    </a>
                </form>
            </li>

            <li class="mode">
                <div class="moon-sun">
                    <i class='bx bx-moon icon moon'></i>
                    <i class='bx bx-sun icon sun'></i>
                </div>
                <span class="mode-text text">Dark Mode</span>
                <div class="toggle-switch">
                    <span class="switch"></span>
                </div>
            </li>
        </div>
    </div>
</nav>


<section class="home">
    @yield('content')
</section>

<div id="overlay" class="overlay">
    <div class="overlay-content">
        <div class="spinner-border spinner-border-sm" role="status">
            <span class="visually-hidden"></span>
        </div>
        Vui lòng chờ...
    </div>
</div>
<!-- Libraries Js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
<script src="{{ asset('js/homepage/script.js') }}"></script>
@yield('scripts')
<script>
    AOS.init();
    function confirmLogout() {
        document.getElementById('logout-form').submit();
    }
</script>
</body>
</html>