@extends('technician.layout')
@section('css')
    <style>
        .wrapper{
            width: 100%;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.12);
        }

        .wrapper header{
            display: flex;
            align-items: center;
            padding: 25px 30px 10px;
            justify-content: space-between;
        }

        header .icons{
            display: flex;
        }

        header .icons span{
            height: 38px;
            width: 38px;
            margin: 0 1px;
            cursor: pointer;
            color: #878787;
            text-align: center;
            line-height: 38px;
            font-size: 1.9rem;
            user-select: none;
            border-radius: 50%;
        }

        .icons span:last-child{
            margin-right: -10px;
        }

        header .icons span:hover{
            background: #f2f2f2;
        }

        header .current-date{
            font-size: 1.45rem;
            font-weight: 500;
        }

        .calendar{
            padding: 20px;
        }

        .calendar ul{
            display: flex;
            flex-wrap: wrap;
            list-style: none;
            text-align: center;
        }

        .calendar .days{
            margin-bottom: 20px;
        }

        .calendar li{
            color: #333;
            width: calc(100% / 7);
            font-size: 1.07rem;
        }

        .calendar .weeks li{
            font-weight: 500;
            cursor: default;
        }

        .calendar .days li{
            z-index: 1;
            cursor: pointer;
            position: relative;
            margin-top: 30px;
        }

        .days li.inactive{
            color: #aaa;
        }

        .days li.active{
            color: #fff;
        }

        .days li::before{
            position: absolute;
            content: "";
            left: 50%;
            top: 50%;
            height: 40px;
            width: 40px;
            z-index: -1;
            border-radius: 50%;
            transform: translate(-50%, -50%);
        }

        .days li.active::before{
            background: #9B59B6;
        }

        .days li:not(.active):hover::before{
            background: #f2f2f2;
        }
    </style>
@endsection
@section('content')
    <div class="row m-5 mb-4 d-flex align-items-center">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <div class="text">Trang chủ</div>
            </nav>
        </div>
    </div>
    <div class="row p-4 ms-5 me-5 mt-4 mb-0 d-flex justify-content-center">
        <div class="wrapper">
            <header>
                <p class="current-date"></p>
                <div class="icons">
                    <span id="prev" class="material-symbols-rounded"><i class='bx bx-chevron-left'></i></span>
                    <span id="next" class="material-symbols-rounded"><i class='bx bx-chevron-right'></i></span>
                </div>
            </header>
            <div class="calendar">
                <ul class="weeks">
                    <li>Chủ nhật</li>
                    <li>Thứ hai</li>
                    <li>Thứ ba</li>
                    <li>Thứ tư</li>
                    <li>Thứ năm</li>
                    <li>Thứ sáu</li>
                    <li>Thứ bảy</li>
                </ul>
                <ul class="days"></ul>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        const daysTag = document.querySelector(".days"),
            currentDate = document.querySelector(".current-date"),
            prevNextIcon = document.querySelectorAll(".icons span");

        let date = new Date(),
            currYear = date.getFullYear(),
            currMonth = date.getMonth();

        const months = ["Tháng một - ", "Tháng hai - ", "Tháng ba - ", "Tháng tư - ", "Tháng năm - ", "Tháng sáu - ", "Tháng bảy - ",
            "Tháng tám - ", "Tháng chín - ", "Tháng mười - ", "Tháng mười một - ", "Tháng mười hai - "];

        const renderCalendar = () => {
            let firstDayofMonth = new Date(currYear, currMonth, 1).getDay(),
                lastDateofMonth = new Date(currYear, currMonth + 1, 0).getDate(),
                lastDayofMonth = new Date(currYear, currMonth, lastDateofMonth).getDay(),
                lastDateofLastMonth = new Date(currYear, currMonth, 0).getDate();
            let liTag = "";

            for (let i = firstDayofMonth; i > 0; i--) {
                liTag += `<li class="inactive">${lastDateofLastMonth - i + 1}</li>`;
            }

            for (let i = 1; i <= lastDateofMonth; i++) {
                let isToday = i === date.getDate() && currMonth === new Date().getMonth()
                && currYear === new Date().getFullYear() ? "active" : "";
                liTag += `<li class="${isToday}">${i}</li>`;
            }

            for (let i = lastDayofMonth; i < 6; i++) {
                liTag += `<li class="inactive">${i - lastDayofMonth + 1}</li>`
            }
            currentDate.innerText = `${months[currMonth]} ${currYear}`;
            daysTag.innerHTML = liTag;
        }
        renderCalendar();

        prevNextIcon.forEach(icon => {
            icon.addEventListener("click", () => {
                currMonth = icon.id === "prev" ? currMonth - 1 : currMonth + 1;

                if(currMonth < 0 || currMonth > 11) {
                    date = new Date(currYear, currMonth, new Date().getDate());
                    currYear = date.getFullYear();
                    currMonth = date.getMonth();
                } else {
                    date = new Date();
                }
                renderCalendar();
            });
        });
    </script>
@endsection
