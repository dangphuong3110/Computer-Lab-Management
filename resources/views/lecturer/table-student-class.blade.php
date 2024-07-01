@if(count($students) > 0)
    @foreach($students as $index => $student)
        <tr>
            <th scope="row" class="text-center">{{ $students->firstItem() + $index }}</th>
            <td class="text-center">{{ $student->full_name }}</td>
            <td class="text-center">{{ $student->student_code }}</td>
            <td class="text-center">{{ $student->class }}</td>
            <td class="text-center">{{ $student->user->email }}</td>
            <td class="text-center">{{ $student->user->phone }}</td>
            <td class="text-center">
                <div class="wrap-button m-1" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Xóa sinh viên khỏi lớp học phần">
                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#destroy-student-class-modal-{{ $student->id }}"><i class='bx bx-trash'></i></button>
                </div>
                <form method="post" action="{{ route('lecturer.destroy-student-class-api', $student->id) }}" id="destroy-student-class-form-{{ $student->id }}">
                    @csrf
                    @method('DELETE')
                    <!----- Modal xóa sinh viên ----->
                    <div class="modal fade" id="destroy-student-class-modal-{{ $student->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="destroyStudentClassLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="exampleModalLabel">Xóa sinh viên khỏi lớp học phần</h1>
                                </div>
                                <div class="modal-body">
                                    <p class="text-wrap m-0">Bạn có chắc chắn muốn xóa sinh viên ra khỏi lớp học phần?</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Trở về</button>
                                    <button type="submit" class="btn btn-danger btn-destroy-student-class" data-student-id="{{ $student->id }}">Xóa</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </td>
        </tr>
    @endforeach
@else
    <tr>
        <td colspan="7" class="text-center">Không có dữ liệu sinh viên trong lớp học phần</td>
    </tr>
@endif
