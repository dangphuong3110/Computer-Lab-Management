@if(count($classes) > 0)
    @foreach($classes as $index => $class)
        <tr>
            <th scope="row" class="text-center">{{ $classes->firstItem() + $index }}</th>
            <td class="text-center">{{ $class->name }}</td>
            <td class="text-center">{{ $class->lecturer->full_name }}</td>
            <td class="text-center">{{ $class->start_date }} <i class='bx bx-right-arrow-alt'></i> {{ $class->end_date }}</td>
        </tr>
    @endforeach
@else
    <tr>
        <td colspan="4" class="text-center">Không có dữ liệu lớp học phần</td>
    </tr>
@endif
