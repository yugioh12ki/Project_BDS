<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Models\User;

class TestEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:email {email}';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'Test email sending functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $otp = '123456';

        try {
            $this->info("Đang gửi email test tới: {$email}");

            // Tạo user giả để test
            $user = new User();
            $user->Name = 'Test User';
            $user->Email = $email;

            Mail::send('emails.otp', [
                'user' => $user,
                'otp' => $otp
            ], function ($message) use ($email) {
                $message->to($email)
                        ->subject('Test OTP - BDS Platform');
            });

            $this->info("✅ Email đã được gửi thành công!");
            $this->info("📧 Kiểm tra hộp thư (có thể ở mục Spam): {$email}");
            $this->info("🔑 OTP test: {$otp}");

        } catch (\Exception $e) {
            $this->error("❌ Lỗi gửi email: " . $e->getMessage());
            $this->info("🔧 Kiểm tra lại cấu hình Gmail trong file .env");
        }
    }
}
