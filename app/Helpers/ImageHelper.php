<?php

namespace App\Helpers;

class ImageHelper
{
    /**
     * Chuyển đổi đường dẫn ảnh thành URL có thể truy cập được
     *
     * @param string $imagePath
     * @return string
     */
    public static function getImageUrl($imagePath)
    {
        if (empty($imagePath)) {
            return asset('storage/images/no-image.jpeg');
        }

        // Xử lý đường dẫn hình ảnh với format: images/properties/{propertyID}/filename
        // Loại bỏ 'public/' hoặc '\public\' nếu có
        $imagePath = preg_replace('/^(\\\\)?public(\\\\|\/)/', '', $imagePath);
        // Thay thế dấu gạch chéo ngược bằng dấu gạch chéo
        $imagePath = str_replace('\\', '/', $imagePath);

        // Chuẩn hóa đường dẫn
        $normalizedPath = ltrim($imagePath, '/');
        if (strpos($normalizedPath, 'storage/') === 0) {
            $normalizedPath = substr($normalizedPath, 8); // Bỏ 'storage/' ở đầu
        }

        // Kiểm tra file có tồn tại trong public/storage không
        $fullPath = public_path('storage/' . $normalizedPath);
        if (file_exists($fullPath)) {
            return asset('storage/' . $normalizedPath);
        }

        // Nếu file không tồn tại, trả về ảnh no-image mặc định
        return asset('storage/images/no-image.jpeg');
    }

    /**
     * Lấy URL ảnh đại diện của property với fallback
     * Ưu tiên: Thumbnail -> Ảnh đầu tiên -> No-image
     *
     * @param \Illuminate\Database\Eloquent\Collection $images
     * @return string
     */
    public static function getPropertyImageUrl($images)
    {
        if (!$images || $images->isEmpty()) {
            return asset('storage/images/no-image.jpeg');
        }

        // Tìm ảnh thumbnail trước
        $thumbnailImage = $images->where('IsThumbnail', 1)->first();
        if ($thumbnailImage) {
            return self::getImageUrl($thumbnailImage->ImagePath);
        }

        // Nếu không có thumbnail, lấy ảnh đầu tiên
        $firstImage = $images->first();
        if ($firstImage) {
            return self::getImageUrl($firstImage->ImagePath);
        }

        // Fallback to no-image
        return asset('storage/images/no-image.jpeg');
    }

    /**
     * Kiểm tra file ảnh có tồn tại không
     *
     * @param string $imagePath
     * @return bool
     */
    public static function imageExists($imagePath)
    {
        if (empty($imagePath)) {
            return false;
        }

        // Xử lý đường dẫn hình ảnh
        $imagePath = preg_replace('/^(\\\\)?public(\\\\|\/)/', '', $imagePath);
        $imagePath = str_replace('\\', '/', $imagePath);

        $normalizedPath = ltrim($imagePath, '/');
        if (strpos($normalizedPath, 'storage/') === 0) {
            $normalizedPath = substr($normalizedPath, 8);
        }

        return file_exists(public_path('storage/' . $normalizedPath));
    }
}
