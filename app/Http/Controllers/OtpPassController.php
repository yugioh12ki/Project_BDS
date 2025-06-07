<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class OtpPassController extends Controller
{
    /**
     * Hiển thị form yêu cầu reset mật khẩu
     */
    public function showResetRequestForm()
    {
        return view('auth.reset-request');
    }

    /**
     * Gửi OTP qua email
     */
    public function sendOtpEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:user,Email'
        ], [
            'email.required' => 'Vui lòng nhập địa chỉ email.',
            'email.email' => 'Địa chỉ email không hợp lệ.',
            'email.exists' => 'Email này không tồn tại trong hệ thống.'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user = User::where('Email', $request->email)->first();

        // Tạo OTP 6 chữ số
        $otp = rand(100000, 999999);

        // Cập nhật OTP và thời gian hết hạn (15 phút)
        $user->otp = $otp;
        $user->otp_expires_at = Carbon::now()->addMinutes(15);
        $user->save();

        // Gửi email OTP (chỉ log trong trường hợp không config mail)
        try {
            // Kiểm tra có config mail không
            if (config('mail.default') === 'log' || !config('mail.mailers.smtp.host')) {
                // Log OTP thay vì gửi email thật
                Log::info("OTP Reset Password", [
                    'email' => $user->Email,
                    'otp' => $otp,
                    'expires_at' => $user->otp_expires_at
                ]);

                return redirect()->route('password.verify-otp-form', ['email' => $request->email, 'method' => 'email'])
                               ->with('success', 'Mã OTP đã được tạo. Kiểm tra log để lấy mã OTP (Demo mode).');
            }

            Mail::send('emails.otp', ['otp' => $otp, 'user' => $user], function ($message) use ($user) {
                $message->to($user->Email, $user->Name)
                        ->subject('Mã OTP đặt lại mật khẩu - BDS Platform');
            });

            return redirect()->route('password.verify-otp-form', ['email' => $request->email, 'method' => 'email'])
                           ->with('success', 'Mã OTP đã được gửi tới email của bạn. Vui lòng kiểm tra hộp thư.');
        } catch (\Exception $e) {
            Log::error("Send OTP Email Error: " . $e->getMessage());
            return back()->with('error', 'Có lỗi xảy ra khi gửi email: ' . $e->getMessage());
        }
    }

    /**
     * Gửi OTP qua SMS
     */
    public function sendOtpSms(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|regex:/^[0-9]{10,11}$/|exists:user,Phone'
        ], [
            'phone.required' => 'Vui lòng nhập số điện thoại.',
            'phone.regex' => 'Số điện thoại không hợp lệ.',
            'phone.exists' => 'Số điện thoại này không tồn tại trong hệ thống.'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user = User::where('Phone', $request->phone)->first();

        // Tạo OTP 6 chữ số
        $otp = rand(100000, 999999);

        // Cập nhật OTP và thời gian hết hạn (15 phút)
        $user->otp = $otp;
        $user->otp_expires_at = Carbon::now()->addMinutes(15);
        $user->save();

        // Gửi SMS OTP (Demo - chỉ log)
        try {
            Log::info("OTP Reset Password SMS", [
                'phone' => $user->Phone,
                'email' => $user->Email,
                'otp' => $otp,
                'expires_at' => $user->otp_expires_at
            ]);

            // TODO: Tích hợp SMS gateway thật (Twilio, AWS SNS, etc.)
            // $this->sendSmsMessage($user->Phone, "Mã OTP đặt lại mật khẩu BDS: {$otp}. Có hiệu lực 15 phút.");

            return redirect()->route('password.verify-otp-form', ['email' => $user->Email, 'method' => 'sms'])
                           ->with('success', 'Mã OTP đã được gửi tới số điện thoại của bạn. Kiểm tra log để lấy mã (Demo mode).');
        } catch (\Exception $e) {
            Log::error("Send OTP SMS Error: " . $e->getMessage());
            return back()->with('error', 'Có lỗi xảy ra khi gửi SMS: ' . $e->getMessage());
        }
    }

    /**
     * Hiển thị form nhập OTP
     */
    public function showVerifyOtpForm(Request $request)
    {
        $email = $request->get('email');
        $method = $request->get('method', 'email'); // email hoặc sms

        if (!$email) {
            return redirect()->route('password.request')->with('error', 'Vui lòng nhập email trước.');
        }

        $user = User::where('Email', $email)->first();
        if (!$user) {
            return redirect()->route('password.request')->with('error', 'Email không tồn tại.');
        }

        return view('auth.verify-otp', compact('email', 'method', 'user'));
    }

    /**
     * Xác thực OTP
     */
    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:user,Email',
            'otp' => 'required|digits:6'
        ], [
            'email.required' => 'Email không được để trống.',
            'email.exists' => 'Email không tồn tại.',
            'otp.required' => 'Vui lòng nhập mã OTP.',
            'otp.digits' => 'Mã OTP phải có 6 chữ số.'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user = User::where('Email', $request->email)->first();

        // Kiểm tra OTP và thời gian hết hạn
        if (!$user->otp || $user->otp != $request->otp) {
            return back()->with('error', 'Mã OTP không chính xác.')->withInput();
        }

        if (Carbon::now()->gt($user->otp_expires_at)) {
            return back()->with('error', 'Mã OTP đã hết hạn. Vui lòng yêu cầu mã mới.')->withInput();
        }

        // OTP hợp lệ, chuyển đến form đặt lại mật khẩu
        return redirect()->route('password.reset-form', ['email' => $request->email, 'token' => $user->otp])
                       ->with('success', 'Mã OTP chính xác. Vui lòng nhập mật khẩu mới.');
    }

    /**
     * Hiển thị form đặt lại mật khẩu
     */
    public function showResetForm(Request $request)
    {
        $email = $request->get('email');
        $token = $request->get('token');

        if (!$email || !$token) {
            return redirect()->route('password.request')->with('error', 'Liên kết không hợp lệ.');
        }

        $user = User::where('Email', $email)->where('otp', $token)->first();
        if (!$user || Carbon::now()->gt($user->otp_expires_at)) {
            return redirect()->route('password.request')->with('error', 'Liên kết đã hết hạn hoặc không hợp lệ.');
        }

        return view('auth.reset-password', compact('email', 'token'));
    }

    /**
     * Đặt lại mật khẩu
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:user,Email',
            'token' => 'required',
            'password' => 'required|min:6|confirmed',
            'password_confirmation' => 'required'
        ], [
            'email.required' => 'Email không được để trống.',
            'email.exists' => 'Email không tồn tại.',
            'token.required' => 'Token không hợp lệ.',
            'password.required' => 'Vui lòng nhập mật khẩu mới.',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
            'password.confirmed' => 'Mật khẩu xác nhận không khớp.',
            'password_confirmation.required' => 'Vui lòng xác nhận mật khẩu.'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user = User::where('Email', $request->email)->where('otp', $request->token)->first();

        if (!$user || Carbon::now()->gt($user->otp_expires_at)) {
            return redirect()->route('password.request')->with('error', 'Liên kết đã hết hạn hoặc không hợp lệ.');
        }

        // Cập nhật mật khẩu mới
        $user->PasswordHash = md5($request->password);
        $user->otp = null;
        $user->otp_expires_at = null;
        $user->save();

        return redirect()->route('login')->with('success', 'Mật khẩu đã được đặt lại thành công. Vui lòng đăng nhập.');
    }

    /**
     * Gửi lại OTP
     */
    public function resendOtp(Request $request)
    {
        $email = $request->get('email');
        $method = $request->get('method', 'email');

        if (!$email) {
            return response()->json(['error' => 'Email không hợp lệ.'], 400);
        }

        $user = User::where('Email', $email)->first();
        if (!$user) {
            return response()->json(['error' => 'Email không tồn tại.'], 404);
        }

        // Tạo OTP mới
        $otp = rand(100000, 999999);
        $user->otp = $otp;
        $user->otp_expires_at = Carbon::now()->addMinutes(15);
        $user->save();

        try {
            if ($method === 'sms') {
                // Gửi SMS
                Log::info("Resend OTP SMS", [
                    'phone' => $user->Phone,
                    'email' => $user->Email,
                    'otp' => $otp,
                    'expires_at' => $user->otp_expires_at
                ]);

                return response()->json(['success' => 'Mã OTP mới đã được gửi tới số điện thoại của bạn. Kiểm tra log (Demo mode).']);
            } else {
                // Gửi Email
                if (config('mail.default') === 'log' || !config('mail.mailers.smtp.host')) {
                    Log::info("Resend OTP Email", [
                        'email' => $user->Email,
                        'otp' => $otp,
                        'expires_at' => $user->otp_expires_at
                    ]);

                    return response()->json(['success' => 'Mã OTP mới đã được tạo. Kiểm tra log để lấy mã (Demo mode).']);
                }

                Mail::send('emails.otp', ['otp' => $otp, 'user' => $user], function ($message) use ($user) {
                    $message->to($user->Email, $user->Name)
                            ->subject('Mã OTP đặt lại mật khẩu - BDS Platform');
                });

                return response()->json(['success' => 'Mã OTP mới đã được gửi tới email của bạn.']);
            }
        } catch (\Exception $e) {
            Log::error("Resend OTP Error: " . $e->getMessage());
            return response()->json(['error' => 'Có lỗi xảy ra khi gửi lại mã OTP: ' . $e->getMessage()], 500);
        }
    }
}
