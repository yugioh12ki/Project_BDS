<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckFAQFile
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $faqPath = storage_path('app/faq.json');

        if (!file_exists($faqPath)) {
            // Tạo file FAQ mặc định nếu không tồn tại
            $defaultFAQ = [
                "key_open_time" => [
                    "questions" => [
                        "Khi nào mở cửa",
                        "Giờ mở cửa là mấy giờ",
                        "Khi nào đóng cửa",
                        "Thời gian làm việc",
                        "Giờ làm việc công ty"
                    ],
                    "answer" => "Công ty mở cửa từ 8h đến 17h, thứ 2 đến thứ 6."
                ],
                "key_contact_support" => [
                    "questions" => [
                        "Làm sao liên hệ hỗ trợ",
                        "Tôi cần hỗ trợ, làm sao liên hệ",
                        "Cách liên hệ bộ phận hỗ trợ"
                    ],
                    "answer" => "Bạn có thể gửi email đến support@company.com hoặc gọi điện thoại đến số 0123456789 trong giờ làm việc."
                ]
            ];

            file_put_contents($faqPath, json_encode($defaultFAQ, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        }

        return $next($request);
    }
}
