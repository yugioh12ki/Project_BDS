<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RegisterController extends Controller
{
    //
    public function register()
    {
        return view('auth.register');
    }

    public function formRegister(Request $request)
    {
        // Validate the request data
        $request->validate([
            'name' => 'required|string|min:3|max:255',
            'email' => 'required|string|email|max:255|unique:user,Email',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'required|string|min:10|max:10',
            'identity_card' => 'required|string|numeric',
            'birth' => 'required|date|before:today',
            'address' => 'required|string|min:10|max:255',
            'province' => 'required|string|max:100',
            'district' => 'required|string|max:100',
            'ward' => 'required|string|max:100',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ], [
            'name.required' => 'Vui lòng nhập họ tên',
            'name.min' => 'Họ tên phải có ít nhất 3 ký tự',
            'email.required' => 'Vui lòng nhập email',
            'email.email' => 'Email không đúng định dạng',
            'email.unique' => 'Email đã được sử dụng',
            'password.required' => 'Vui lòng nhập mật khẩu',
            'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp',
            'phone.required' => 'Vui lòng nhập số điện thoại',
            'phone.min' => 'Số điện thoại phải có 10 chữ số',
            'phone.max' => 'Số điện thoại phải có 10 chữ số',
            'identity_card.required' => 'Vui lòng nhập CCCD/CMND',
            'identity_card.numeric' => 'CCCD/CMND chỉ được chứa số',
            'birth.required' => 'Vui lòng nhập ngày sinh',
            'birth.date' => 'Ngày sinh không đúng định dạng',
            'birth.before' => 'Ngày sinh phải trước ngày hiện tại',
            'address.required' => 'Vui lòng nhập địa chỉ',
            'address.min' => 'Địa chỉ phải có ít nhất 10 ký tự',
            'province.required' => 'Vui lòng chọn tỉnh/thành phố',
            'district.required' => 'Vui lòng chọn quận/huyện',
            'ward.required' => 'Vui lòng chọn phường/xã',
            'avatar.image' => 'File phải là hình ảnh',
            'avatar.mimes' => 'Avatar chỉ chấp nhận định dạng: jpeg, png, jpg, gif',
            'avatar.max' => 'Kích thước avatar không được vượt quá 5MB',
        ]);

        // Additional custom validation
        $errors = [];

        // Check name contains only letters and spaces
        if (!preg_match('/^[a-zA-ZÀ-ỹ\s]+$/u', $request->name)) {
            $errors['name'] = 'Họ tên chỉ được chứa chữ cái và khoảng trắng';
        }

        // Check password strength
        if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/', $request->password)) {
            $errors['password'] = 'Mật khẩu phải chứa ít nhất 1 chữ hoa, 1 chữ thường và 1 số';
        }

        // Check phone format (starts with 0 and specific prefixes)
        if (!preg_match('/^0[3-9][0-9]{8}$/', $request->phone)) {
            $errors['phone'] = 'Số điện thoại không đúng định dạng (VD: 0981234567)';
        }

        // Check identity card length (9 or 12 digits)
        $identityCard = $request->identity_card;
        if (!in_array(strlen($identityCard), [9, 12])) {
            $errors['identity_card'] = 'CCCD/CMND phải có đúng 9 hoặc 12 chữ số';
        }

        // Check age (minimum 18 years old)
        if ($request->birth) {
            $birthDate = new \DateTime($request->birth);
            $today = new \DateTime();
            $age = $today->diff($birthDate)->y;
            if ($age < 18) {
                $errors['birth'] = 'Bạn phải đủ 18 tuổi để đăng ký';
            }
        }

        // If there are validation errors, return back with errors
        if (!empty($errors)) {
            return back()->withInput()->withErrors($errors);
        }

        try {
            // Handle avatar upload
            $avatarFileName = null;
            if ($request->hasFile('avatar')) {
                $avatar = $request->file('avatar');

                // Ensure avatars directory exists
                $avatarPath = public_path('storage/avatars');
                if (!file_exists($avatarPath)) {
                    mkdir($avatarPath, 0755, true);
                }

                // Generate unique filename - chỉ sử dụng số để tránh lỗi với tên tiếng Việt
                $extension = $avatar->getClientOriginalExtension();
                $avatarFileName = 'avatar_' . time() . '_' . uniqid() . '.' . $extension;

                // Move the file
                if (!$avatar->move($avatarPath, $avatarFileName)) {
                    return back()->withInput()->with('error', 'Có lỗi khi tải lên ảnh đại diện. Vui lòng thử lại.');
                }
            }

            // Create the user
            $user = \App\Models\User::create([
                'Name' => $request->name,
                'Email' => $request->email,
                'PasswordHash' => md5($request->password), // Sử dụng MD5 trực tiếp
                'Phone' => $request->phone,
                'IdentityCard' => $request->identity_card,
                'Birth' => $request->birth,
                'Address' => $request->address,
                'Province' => $request->province,
                'District' => $request->district,
                'Ward' => $request->ward,
                'Avatar' => $avatarFileName, // Chỉ lưu tên file
                'Role' => 'Customer', // Default role
                'StatusUser' => 'active', // Default status
            ]);

            // Profile customer sẽ được tạo tự động bởi trigger database

            // Redirect or return a response
            return redirect()->route('login')->with('success', 'Đăng ký thành công! Vui lòng đăng nhập để tiếp tục.');

        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Có lỗi xảy ra trong quá trình đăng ký: ' . $e->getMessage());
        }
    }
}
