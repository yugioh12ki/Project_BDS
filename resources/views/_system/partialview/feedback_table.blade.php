    <table>
        <thead>
            <tr>
                @foreach ($columns as $column )
                @if($column === 'CusID')
                    <th>Khách hàng</th>
                @elseif($column === 'AgentID')
                    <th>Người phụ trách</th>
                @else
                    <th>{{ $column }}</th>
                @endif
                @endforeach
                <th>Chức năng</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($feedbacks as $feedback)
                <tr>
                    @foreach ($columns as $column)
                        @if ($column === 'Status')
                            <td>
                                <form action="{{ route('admin.feedback.update', $feedback->FeedbackID)}}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status" onchange="this.form.submit()" class="form-select form-select-sm">
                                        <option value="Chờ duyệt" {{ $feedback->Status === 'Chờ duyệt' ? 'selected' : '' }}>Chờ Duyệt</option>
                                        <option value="Đã duyệt" {{ $feedback->Status === 'Đã duyệt' ? 'selected' : '' }}>Đã Duyệt</option>
                                        <option value="Hủy bỏ" {{ $feedback->Status === 'Hủy bỏ' ? 'selected' : '' }}>Hủy Bỏ</option>
                                    </select>
                                </form>
                            </td>
                        @elseif($column === 'CusID')
                            <td>
                                {{ $feedback->user_Cus ? $feedback->user_Cus->Name : $feedback->CusID }}
                            </td>
                        @elseif($column === 'AgentID')
                            <td>{{ $feedback->user_Agent ? $feedback->user_Agent->Name : $feedback->AgentID }}</td>
                        @else
                            <td>{{ $feedback->$column }}</td>
                        @endif
                    @endforeach
                    <td>
                        <!-- Edit Button: Opens Edit Modal -->
                        <!-- Delete Button: Opens Delete Confirmation Modal -->
                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{$feedback->FeedbackID  }}">
                            <i class="fas fa-trash"></i> Xóa
                        </button>
                        <!-- Delete Confirmation Modal -->
                        <div class="modal fade" id="deleteModal{{$feedback->FeedbackID}}" tabindex="-1" aria-labelledby="deleteModalLabel{{$feedback->FeedbackID }}" aria-hidden="true">
                          <div class="modal-dialog">
                            <form action="{{route('admin.feedback.delete', $feedback->FeedbackID)}}" method="POST">
                              @csrf
                              @method('DELETE')
                              <div class="modal-content">
                                <div class="modal-header">
                                  <h5 class="modal-title" id="deleteModalLabel{{$feedback->FeedbackID}}">Xác nhận xóa</h5>
                                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                                </div>
                                <div class="modal-body">
                                  Bạn có chắc chắn muốn xóa feedback này?
                                </div>
                                <div class="modal-footer">
                                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                  <button type="submit" class="btn btn-danger">Xóa</button>
                                </div>
                              </div>
                            </form>
                          </div>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
