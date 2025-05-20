<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChatbotController extends Controller
{
    /**
     * Nhận câu hỏi từ khách hàng, tìm câu trả lời phù hợp trong bảng admin_question và trả về.
     */
    public function answer(Request $request)
    {
        $question = $request->input('question');
        // Tìm kiếm pattern phù hợp nhất với câu hỏi khách hàng
        $matched = DB::table('admin_question')
            ->whereRaw('LOWER(?) LIKE LOWER(CONCAT("%", pattern, "%"))', [$question])
            ->orderByDesc('id')
            ->first();

        if ($matched) {
            // Ưu tiên trả về handler nếu có, nếu không thì trả về desc
            $answer = $matched->handler ?: $matched->desc;
            return response()->json([
                'answer' => $answer
            ]);
        } else {
            return response()->json([
                'answer' => 'Xin lỗi, tôi chưa có câu trả lời cho câu hỏi này. Vui lòng liên hệ admin để được hỗ trợ.'
            ]);
        }
    }

    /**
     * Hiển thị danh sách các mẫu câu hỏi và câu trả lời
     */
    public function index()
    {
        $questions = DB::table('admin_question')->orderBy('id', 'desc')->get();
        return view('_system.chatboxai.index', compact('questions'));
    }

    /**
     * Hiển thị form tạo mẫu câu hỏi mới
     */
    public function create()
    {
        return view('_system.chatboxai.create');
    }

    /**
     * Lưu mẫu câu hỏi mới vào database
     */
    public function store(Request $request)
    {
        $request->validate([
            'question_text' => 'required|string|max:255',
            'pattern' => 'required|string|max:255',
            'handler' => 'nullable|string',
            'desc' => 'nullable|string',
        ]);

        DB::table('admin_question')->insert([
            'question_text' => $request->question_text,
            'pattern' => $request->pattern,
            'handler' => $request->handler,
            'desc' => $request->desc,
            'created_at' => now(),
        ]);

        return redirect()->route('admin.chatbot.questions.index')
            ->with('success', 'Mẫu câu hỏi được thêm thành công.');
    }

    /**
     * Hiển thị form chỉnh sửa mẫu câu hỏi
     */
    public function edit($id)
    {
        $question = DB::table('admin_question')->where('id', $id)->first();

        if (!$question) {
            return redirect()->route('admin.chatbot.questions.index')
                ->with('error', 'Không tìm thấy mẫu câu hỏi.');
        }

        return view('_system.chatboxai.edit', compact('question'));
    }

    /**
     * Cập nhật mẫu câu hỏi trong database
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'question_text' => 'required|string|max:255',
            'pattern' => 'required|string|max:255',
            'handler' => 'nullable|string',
            'desc' => 'nullable|string',
        ]);

        DB::table('admin_question')->where('id', $id)->update([
            'question_text' => $request->question_text,
            'pattern' => $request->pattern,
            'handler' => $request->handler,
            'desc' => $request->desc,
        ]);

        return redirect()->route('admin.chatbot.questions.index')
            ->with('success', 'Mẫu câu hỏi được cập nhật thành công.');
    }

    /**
     * Xóa mẫu câu hỏi khỏi database
     */
    public function destroy($id)
    {
        DB::table('admin_question')->where('id', $id)->delete();

        return redirect()->route('admin.chatbot.questions.index')
            ->with('success', 'Mẫu câu hỏi được xóa thành công.');
    }
}
