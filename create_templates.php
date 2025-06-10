<?php
// Script to create proper Word templates with placeholders
require_once __DIR__ . '/vendor/autoload.php';

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;

function createTemplate($filename, $title, $content) {
    $phpWord = new PhpWord();

    // Add section
    $section = $phpWord->addSection();

    // Title
    $section->addText($title, ['bold' => true, 'size' => 16], ['alignment' => 'center']);
    $section->addTextBreak(2);

    // Content with placeholders
    foreach ($content as $line) {
        $section->addText($line, ['size' => 12]);
        $section->addTextBreak();
    }

    // Save the document
    $writer = IOFactory::createWriter($phpWord, 'Word2007');
    $writer->save($filename);

    echo "Created: $filename\n";
}

// Create templates directory if not exists
$templateDir = 'public/storage/document/HopDong/';
if (!is_dir($templateDir)) {
    mkdir($templateDir, 0755, true);
}

// Template 1: Hợp đồng cho thuê
createTemplate($templateDir . 'HopDong_ChoThue.docx',
    'HỢP ĐỒNG CHO THUÊ BẤT ĐỘNG SẢN',
    [
        'Căn cứ vào Bộ luật Dân sự năm 2015;',
        'Căn cứ vào nhu cầu và khả năng của các bên;',
        '',
        'Hôm nay, ngày ${CURRENT_DATE}, chúng tôi gồm:',
        '',
        'BÊN CHO THUÊ (Bên A):',
        'Họ và tên: ${AGENT_NAME}',
        'Điện thoại: ${AGENT_PHONE}',
        'Email: ${AGENT_EMAIL}',
        '',
        'BÊN THUÊ (Bên B):',
        'Họ và tên: ${CUSTOMER_NAME}',
        'Điện thoại: ${CUSTOMER_PHONE}',
        'CMND/CCCD: ${CUSTOMER_ID}',
        'Địa chỉ: ${CUSTOMER_ADDRESS}',
        '',
        'THỎA THUẬN KÝ KẾT HỢP ĐỒNG CHO THUÊ NHÀ Ở như sau:',
        '',
        'Điều 1: ĐỐI TƯỢNG CỦA HỢP ĐỒNG',
        'Bên A đồng ý cho Bên B thuê căn nhà tại địa chỉ: ${PROPERTY_LOCATION}',
        'Diện tích: ${PROPERTY_AREA}',
        'Số phòng ngủ: ${PROPERTY_BEDROOMS}',
        'Số phòng tắm: ${PROPERTY_BATHROOMS}',
        'Hướng nhà: ${PROPERTY_DIRECTION}',
        'Tình trạng nội thất: ${PROPERTY_FURNISHING}',
        '',
        'Điều 2: GIÁ CHO THUÊ VÀ PHƯƠNG THỨC THANH TOÁN',
        'Giá cho thuê: ${PROPERTY_PRICE}/tháng',
        'Thời hạn thuê: ${RENT_MONTHS} tháng',
        'Từ ngày: ${START_DATE} đến ngày: ${END_DATE}',
        'Phương thức thanh toán: ${PAYMENT_METHOD}',
        '',
        'Điều 3: QUYỀN VÀ NGHĨA VỤ CỦA CÁC BÊN',
        '...',
        '',
        'HỢP ĐỒNG CÓ HIỆU LỰC KỂ TỪ NGÀY KÝ.',
        '',
        'BÊN A                           BÊN B',
        '(Ký và ghi rõ họ tên)          (Ký và ghi rõ họ tên)'
    ]
);

// Template 2: Hợp đồng mua bán
createTemplate($templateDir . 'HopDong_BanNha.docx',
    'HỢP ĐỒNG MUA BÁN BẤT ĐỘNG SẢN',
    [
        'Căn cứ vào Bộ luật Dân sự năm 2015;',
        'Căn cứ vào Luật Nhà ở năm 2014;',
        '',
        'Hôm nay, ngày ${CURRENT_DATE}, chúng tôi gồm:',
        '',
        'BÊN BÁN (Bên A):',
        'Họ và tên: ${AGENT_NAME}',
        'Điện thoại: ${AGENT_PHONE}',
        'Email: ${AGENT_EMAIL}',
        '',
        'BÊN MUA (Bên B):',
        'Họ và tên: ${CUSTOMER_NAME}',
        'Điện thoại: ${CUSTOMER_PHONE}',
        'CMND/CCCD: ${CUSTOMER_ID}',
        'Địa chỉ: ${CUSTOMER_ADDRESS}',
        '',
        'THỎA THUẬN KÝ KẾT HỢP ĐỒNG MUA BÁN NHÀ Ở như sau:',
        '',
        'Điều 1: ĐỐI TƯỢNG CỦA HỢP ĐỒNG',
        'Bên A đồng ý bán cho Bên B căn nhà tại địa chỉ: ${PROPERTY_LOCATION}',
        'Tiêu đề: ${PROPERTY_TITLE}',
        'Diện tích: ${PROPERTY_AREA}',
        'Chiều rộng: ${PROPERTY_WIDTH}m',
        'Chiều dài: ${PROPERTY_LENGTH}m',
        'Số phòng ngủ: ${PROPERTY_BEDROOMS}',
        'Số phòng tắm: ${PROPERTY_BATHROOMS}',
        'Số tầng: ${PROPERTY_FLOORS}',
        'Hướng nhà: ${PROPERTY_DIRECTION}',
        'Tình trạng nội thất: ${PROPERTY_FURNISHING}',
        'Mô tả: ${PROPERTY_DESCRIPTION}',
        '',
        'Điều 2: GIÁ BÁN VÀ PHƯƠNG THỨC THANH TOÁN',
        'Tổng giá bán: ${PROPERTY_PRICE}',
        'Loại hợp đồng: ${SALE_CONTRACT_TYPE}',
        'Phương thức thanh toán: ${PAYMENT_METHOD}',
        'Ngày giao dịch: ${START_DATE}',
        '',
        'Điều 3: QUYỀN VÀ NGHĨA VỤ CỦA CÁC BÊN',
        '...',
        '',
        'HỢP ĐỒNG CÓ HIỆU LỰC KỂ TỪ NGÀY KÝ.',
        '',
        'BÊN A                           BÊN B',
        '(Ký và ghi rõ họ tên)          (Ký và ghi rõ họ tên)'
    ]
);

// Template 3: Hợp đồng đặt cọc
createTemplate($templateDir . 'HopDong_DatCoc.docx',
    'HỢP ĐỒNG ĐẶT CỌC BẤT ĐỘNG SẢN',
    [
        'Căn cứ vào Bộ luật Dân sự năm 2015;',
        '',
        'Hôm nay, ngày ${CURRENT_DATE}, chúng tôi gồm:',
        '',
        'BÊN NHẬN CỌC (Bên A):',
        'Họ và tên: ${AGENT_NAME}',
        'Điện thoại: ${AGENT_PHONE}',
        'Email: ${AGENT_EMAIL}',
        '',
        'BÊN ĐẶT CỌC (Bên B):',
        'Họ và tên: ${CUSTOMER_NAME}',
        'Điện thoại: ${CUSTOMER_PHONE}',
        'CMND/CCCD: ${CUSTOMER_ID}',
        'Địa chỉ: ${CUSTOMER_ADDRESS}',
        '',
        'THỎA THUẬN KÝ KẾT HỢP ĐỒNG ĐẶT CỌC như sau:',
        '',
        'Điều 1: ĐỐI TƯỢNG ĐẶT CỌC',
        'Địa chỉ bất động sản: ${PROPERTY_LOCATION}',
        'Tiêu đề: ${PROPERTY_TITLE}',
        'Diện tích: ${PROPERTY_AREA}',
        'Giá bán: ${PROPERTY_PRICE}',
        '',
        'Điều 2: SỐ TIỀN ĐẶT CỌC',
        'Số tiền đặt cọc: ${DEPOSIT_AMOUNT}',
        'Phương thức thanh toán: ${PAYMENT_METHOD}',
        'Ngày đặt cọc: ${START_DATE}',
        '',
        'Điều 3: CAM KẾT CỦA CÁC BÊN',
        '- Bên A cam kết giữ nguyên giá bán trong thời gian 30 ngày kể từ ngày ký hợp đồng này.',
        '- Bên B cam kết hoàn thành thủ tục mua bán trong vòng 30 ngày.',
        '',
        'HỢP ĐỒNG CÓ HIỆU LỰC KỂ TỪ NGÀY KÝ.',
        '',
        'BÊN A                           BÊN B',
        '(Ký và ghi rõ họ tên)          (Ký và ghi rõ họ tên)'
    ]
);

echo "All templates created successfully!\n";
?>
