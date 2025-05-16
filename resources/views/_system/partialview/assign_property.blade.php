<div class="table-container">
<table>
    <thead>
        <tr>
            @foreach($columns as $column)
                @if($column === 'AgentID')
                    <th>Người phụ trách</th>
                @elseif($column === 'OwnerID')
                    <th>Chủ sở hữu</th>
                @elseif($column === 'PropertyID')
                    <th>ID Bất Động Sản</th>
                @else
                    @continue
                @endif
            @endforeach
            <th>Chức năng</th>
        </tr>
    </thead>
    <tbody>
        @foreach($properties as $property)
            <tr>
                @foreach($columns as $column)
                @if($column === 'AgentID')
                    <td>
                            <select class="form-control broker-select" data-property-id="{{ $property->PropertyID }}">
                                <option value="">-- Chọn môi giới --</option>
                                @foreach($agents ?? [] as $user)
                                    <option value="{{ $user->UserID }}" {{ $property->AgentID == $user->UserID ? 'selected' : '' }}>
                                        {{ $user->Name }}
                                    </option>
                                @endforeach
                            </select>
                        </td>
                @elseif($column === 'OwnerID')
                    <td>{{ optional($property->chusohuu)->Name ?? 'No data'}}</td>
                @elseif($column === 'PropertyID')
                    <td>{{ $property->PropertyID }}</td>
                @else
                    @continue
                @endif
                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>
</div>
