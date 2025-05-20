@extends('_layout._layadmin.app')

@section('title', 'Thêm câu hỏi Chatbot')

@section('chatbotai')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Thêm câu hỏi Chatbot</h1>
            <a href="{{ route('admin.chatbot.questions.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Quay lại
            </a>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Thông tin câu hỏi</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.chatbot.questions.store') }}">
                    @csrf

                    <div class="form-group">
                        <label for="question_text">Câu hỏi <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('question_text') is-invalid @enderror"
                               id="question_text" name="question_text" value="{{ old('question_text') }}" required>
                        @error('question_text')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="pattern">Pattern (từ khóa để nhận dạng) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('pattern') is-invalid @enderror"
                               id="pattern" name="pattern" value="{{ old('pattern') }}" required>
                        <small class="text-muted">Nhập từ khóa hoặc cụm từ để hệ thống nhận dạng câu hỏi tương tự</small>
                        @error('pattern')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="handler">Handler (Xử lý đặc biệt)</label>
                        <input type="text" class="form-control @error('handler') is-invalid @enderror"
                               id="handler" name="handler" value="{{ old('handler') }}">
                        <small class="text-muted">Để trống nếu không có xử lý đặc biệt</small>
                        @error('handler')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="desc">Câu trả lời</label>
                        <textarea class="form-control @error('desc') is-invalid @enderror"
                                  id="desc" name="desc" rows="5">{{ old('desc') }}</textarea>
                        @error('desc')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary">Lưu</button>
                    <a href="{{ route('admin.chatbot.questions.index') }}" class="btn btn-secondary">Hủy</a>
                </form>
            </div>
        </div>
    </div>
@endsection
