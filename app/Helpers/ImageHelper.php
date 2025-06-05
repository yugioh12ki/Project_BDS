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
            return 'public/storage/images/no-image.jpg';
        }

        // Xử lý đường dẫn hình ảnh giống như trong property-detail
        // Loại bỏ 'public/' hoặc '\public\' nếu có
        $imagePath = preg_replace('/^(\\\\)?public(\\\\|\/)/', '', $imagePath);
        // Thay thế dấu gạch chéo ngược bằng dấu gạch chéo
        $imagePath = str_replace('\\', '/', $imagePath);

        // Nếu đã có storage/ ở đầu thì thêm public/
        if (strpos($imagePath, 'storage/') === 0) {
            return 'public/' . $imagePath;
        }

        // Nếu chưa có storage/ thì thêm public/storage/
        return 'public/storage/' . ltrim($imagePath, '/');
    }
}
