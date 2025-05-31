<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * User registration
     */
    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:user,Email',
                'password' => 'required|string|min:6|confirmed',
                'phone' => 'required|string|max:20|unique:user,Phone',
                'role' => 'in:Customer,Owner,Agent,Admin'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dữ liệu không hợp lệ',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = User::create([
                'UserID' => 'USER_' . time() . '_' . rand(1000, 9999),
                'Name' => $request->name,
                'Email' => $request->email,
                'PasswordHash' => md5($request->password), // Use MD5 for compatibility
                'Phone' => $request->phone,
                'Role' => $request->role ?? 'Customer',
                'StatusUser' => 'active',
                'Birth' => now()->subYears(25)->format('Y-m-d'), // Default birth
                'Sex' => 'Khác',
                'IdentityCard' => str_pad(rand(100000000, 999999999), 9, '0', STR_PAD_LEFT), // 9 digits
            ]);

            // Generate simple token
            $token = base64_encode($user->UserID . '|' . time() . '|' . rand(1000, 9999));

            return response()->json([
                'success' => true,
                'message' => 'Đăng ký thành công',
                'data' => [
                    'user' => $this->formatUser($user),
                    'token' => $token
                ]
            ], 201);

        } catch (\Exception $e) {
            Log::error('Registration error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi đăng ký',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * User login
     */
    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|string|min:6',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dữ liệu không hợp lệ',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = User::where('Email', $request->email)->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email hoặc mật khẩu không đúng'
                ], 401);
            }

            // Check password (support both MD5 and bcrypt)
            $passwordMatch = false;
            
            // First try MD5 (for existing data)
            if (strlen($user->PasswordHash) === 32 && ctype_xdigit($user->PasswordHash)) {
                $passwordMatch = (md5($request->password) === $user->PasswordHash);
            } 
            // Then try bcrypt (for new data)
            else if (strlen($user->PasswordHash) === 60 && str_starts_with($user->PasswordHash, '$2y$')) {
                $passwordMatch = Hash::check($request->password, $user->PasswordHash);
            }
            // Plain text password (temporary support)
            else {
                $passwordMatch = ($request->password === $user->PasswordHash);
            }

            if (!$passwordMatch) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email hoặc mật khẩu không đúng'
                ], 401);
            }

            if ($user->StatusUser !== 'active') {
                return response()->json([
                    'success' => false,
                    'message' => 'Tài khoản đã bị khóa hoặc chưa được kích hoạt'
                ], 403);
            }

            // Delete all existing tokens for this user (skip for simple auth)
            
            // Create simple token
            $token = base64_encode($user->UserID . '|' . time() . '|' . rand(1000, 9999));

            return response()->json([
                'success' => true,
                'message' => 'Đăng nhập thành công',
                'data' => [
                    'user' => $this->formatUser($user),
                    'token' => $token
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Login error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi đăng nhập',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * User logout
     */
    public function logout(Request $request)
    {
        try {
            return response()->json([
                'success' => true,
                'message' => 'Đăng xuất thành công'
            ]);

        } catch (\Exception $e) {
            Log::error('Logout error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi đăng xuất',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get current user profile
     */
    public function profile(Request $request)
    {
        try {
            return response()->json([
                'success' => true,
                'data' => $this->formatUser($request->user())
            ]);

        } catch (\Exception $e) {
            Log::error('Profile error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Không thể tải thông tin người dùng',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request)
    {
        try {
            $user = $request->user();

            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|required|string|max:255',
                'phone' => 'sometimes|required|string|max:20|unique:user,Phone,' . $user->UserID . ',UserID',
                'current_password' => 'sometimes|required|string',
                'password' => 'sometimes|required|string|min:6|confirmed',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dữ liệu không hợp lệ',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Update basic info
            if ($request->has('name')) {
                $user->Name = $request->name;
            }

            if ($request->has('phone')) {
                $user->Phone = $request->phone;
            }

            // Update password if provided
            if ($request->has('password')) {
                if (!$request->has('current_password') || !Hash::check($request->current_password, $user->PasswordHash)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Mật khẩu hiện tại không đúng'
                    ], 422);
                }

                $user->PasswordHash = Hash::make($request->password);
            }

            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật thông tin thành công',
                'data' => $this->formatUser($user)
            ]);

        } catch (\Exception $e) {
            Log::error('Update profile error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi cập nhật thông tin',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Format user data for API response
     */
    private function formatUser($user)
    {
        return [
            'id' => $user->UserID,
            'name' => $user->Name,
            'email' => $user->Email,
            'phone' => $user->Phone,
            'role' => $user->Role,
            'status' => $user->StatusUser,
            'birth' => $user->Birth,
            'sex' => $user->Sex,
            'identity_card' => $user->IdentityCard,
            'address' => $user->Address,
            'ward' => $user->Ward,
            'district' => $user->District,
            'province' => $user->Province,
            'avatar' => $user->Avatar,
        ];
    }
}
