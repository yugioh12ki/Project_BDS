@if ($users->isEmpty())
    <div class="alert alert-danger">
        Không tìm thấy user nào.
    </div>
@else
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    @foreach($columns as $column)
                        <th>{{ $column }}</th>

                    @endforeach
                    <th>Chức năng</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr>
                        @foreach ($columns as $column)
                            <td>{{ $user->$column }}</td>

                        @endforeach
                        <td>
                            <a href="#" class="action-icon" style="font-size: 1rem;"><i class="fas fa-edit"></i></a>
                            <a href="#" class="action-icon" style="font-size: 1rem;"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
