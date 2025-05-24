-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: May 24, 2025 at 07:52 AM
-- Server version: 9.1.0
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `batdongsan`
--

-- --------------------------------------------------------

--
-- Table structure for table `properties`
--

DROP TABLE IF EXISTS `properties`;
CREATE TABLE IF NOT EXISTS `properties` (
  `PropertyID` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci NOT NULL,
  `OwnerID` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci NOT NULL COMMENT 'ID của Owner',
  `AgentID` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci DEFAULT NULL COMMENT 'ID của Agent',
  `PostedDate` date NOT NULL,
  `ApprovedBy` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci DEFAULT NULL COMMENT 'Được duyệt bởi Admin nào',
  `ApprovedDate` date NOT NULL COMMENT 'Ngày duyệt',
  `Status` enum('inactive','active','pending','sold','rented','rejected') CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci DEFAULT 'pending' COMMENT 'Tình Trạng BĐS',
  `Province` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci NOT NULL,
  `District` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci NOT NULL,
  `Ward` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci NOT NULL,
  `Address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci NOT NULL,
  `PropertyType` int NOT NULL COMMENT 'Loại Mô hình Bất Động Sản',
  `Price` double NOT NULL COMMENT 'Giá tiền',
  `Title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci NOT NULL COMMENT 'Tiêu Đề',
  `Description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci,
  `TypePro` enum('Rent','Sale') CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci NOT NULL,
  PRIMARY KEY (`PropertyID`),
  KEY `FK_AdminID_UserID` (`ApprovedBy`),
  KEY `FK_AgentID_UserID` (`AgentID`),
  KEY `FK_OwnerID_UserID` (`OwnerID`),
  KEY `FK_DanhMucBDS` (`PropertyType`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_vietnamese_ci;

--
-- Dumping data for table `properties`
--

INSERT INTO `properties` (`PropertyID`, `OwnerID`, `AgentID`, `PostedDate`, `ApprovedBy`, `ApprovedDate`, `Status`, `Province`, `District`, `Ward`, `Address`, `PropertyType`, `Price`, `Title`, `Description`, `TypePro`) VALUES
('PR00001', 'UID00007', NULL, '0000-00-00', NULL, '0000-00-00', 'pending', 'Thành Phố Hồ Chí Minh', 'Quận Tân Bình', 'Phường 11', '12 Ca Văn Thỉnh', 3, 12000000, 'Cho Thuê Chung Cư Mini Sactaim', 'Chung cư sẽ có các điều hòa', 'Rent'),
('PR00002', 'UID00007', NULL, '2025-05-22', NULL, '0000-00-00', 'inactive', 'Thành Phố Hồ Chí Minh', 'Quận Tân Phú', 'Phường Tân Thành', '49 Trần Hưng Đạo', 4, 10000000, 'Cho Thuê Nhà Cấp 2', 'Nhà Đẹp', 'Rent'),
('PR00003', 'UID00007', NULL, '2025-05-23', NULL, '2025-05-23', 'pending', 'Thành phố Hà Nội', 'Quận Bắc Từ Liêm', 'Phường Cổ Nhuế 2', '12 Lê Thánh Tông', 8, 45, '42ccc', 'cưecc', 'Rent'),
('PR00004', 'UID00007', NULL, '2025-05-23', NULL, '2025-05-23', 'pending', 'Thành phố Hà Nội', 'Huyện Thanh Trì', 'Xã Duyên Hà', '12 Lê Thánh Tông', 8, 33333, 'dđ', 'dđ', 'Rent'),
('PR00005', 'UID00007', NULL, '2025-05-23', NULL, '2025-05-23', 'pending', 'Tỉnh Hà Giang', 'Huyện Quang Bình', 'Xã Xuân Giang', '31 Lý Tự Trọng', 7, 1111, 'aa', 'â', 'Rent'),
('PS00001', 'UID00007', NULL, '2025-05-22', NULL, '0000-00-00', 'pending', 'Thành Phố Hồ Chí Minh', 'Quận Tân Bình', 'Phường 13', '4-6 Đ. Ấp Bắc', 1, 4500000000, 'Bán Nhà Quận Tân Bình cấp 2, gần sân bay', 'abc\r\n123', 'Sale'),
('PS00002', 'UID00008', NULL, '2025-05-18', NULL, '0000-00-00', 'pending', 'Thành Phố Hồ Chí Minh', 'Quận Tân Bình', 'Phường 11', '1 Ca Văn Thỉnh', 3, 1200000000, 'Cho Bán Phòng 2 phòng của Sactaim', 'Chung cư này sẽ có ...', 'Sale'),
('PS00003', 'UID00009', NULL, '2025-05-22', 'UID00001', '2025-05-20', 'active', 'Thành Phố Hồ Chí Minh', 'Quận Tân Bình', 'Phường 7', '60/2 Đ. Văn Còi', 6, 800000000, 'Bán nhà cho chuyên duyệt bán cửa hàng tiện lợi', 'Luôn sẽ có các ...', 'Sale');

--
-- Triggers `properties`
--
DROP TRIGGER IF EXISTS `check_agent_property_limit`;
DELIMITER $$
CREATE TRIGGER `check_agent_property_limit` BEFORE UPDATE ON `properties` FOR EACH ROW BEGIN
    DECLARE active_count INT;
    
    -- Chỉ kiểm tra khi AgentID được thay đổi và bất động sản ở trạng thái active
    IF (OLD.AgentID <> NEW.AgentID OR OLD.AgentID IS NULL) AND NEW.Status = 'active' THEN
        -- Đếm số lượng bất động sản active mà agent đang quản lý
        SELECT COUNT(*) INTO active_count 
        FROM properties 
        WHERE AgentID = NEW.AgentID AND Status = 'active';
        
        -- Nếu agent đã quản lý từ 10 bất động sản trở lên, ngăn không cho update
        IF active_count >= 10 THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Agent đã quản lý tối đa 10 bất động sản đang active!';
        END IF;
    END IF;
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `check_agent_property_limit_insert`;
DELIMITER $$
CREATE TRIGGER `check_agent_property_limit_insert` BEFORE INSERT ON `properties` FOR EACH ROW BEGIN
    DECLARE active_count INT;
    
    -- Chỉ kiểm tra khi có AgentID và bất động sản ở trạng thái active
    IF NEW.AgentID IS NOT NULL AND NEW.Status = 'active' THEN
        -- Đếm số lượng bất động sản active mà agent đang quản lý
        SELECT COUNT(*) INTO active_count 
        FROM properties 
        WHERE AgentID = NEW.AgentID AND Status = 'active';
        
        -- Nếu agent đã quản lý từ 10 bất động sản trở lên, ngăn không cho insert
        IF active_count >= 10 THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Agent đã quản lý tối đa 10 bất động sản đang active!';
        END IF;
    END IF;
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `trg_UpdateApprovedDate`;
DELIMITER $$
CREATE TRIGGER `trg_UpdateApprovedDate` BEFORE UPDATE ON `properties` FOR EACH ROW BEGIN
    -- Kiểm tra nếu trạng thái đang được thay đổi thành 'active'
    IF NEW.Status = 'active' AND OLD.Status != 'active' THEN
        -- Cập nhật ngày duyệt
        SET NEW.ApprovedDate = NOW();
    ELSEIF NEW.Status = 'pending' AND OLD.Status != 'pending' THEN
    	SET NEW.ApprovedDate = '0000-00-00';
    END IF;
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `trg_generate_property_id`;
DELIMITER $$
CREATE TRIGGER `trg_generate_property_id` BEFORE INSERT ON `properties` FOR EACH ROW BEGIN
    DECLARE prefix VARCHAR(2);
    DECLARE base_prefix VARCHAR(2);
    DECLARE max_number INT DEFAULT 0;
    DECLARE new_number INT;

    -- Xác định prefix theo TypePro
    IF NEW.TypePro = 'Sale' THEN
        SET prefix = 'PS';
    ELSEIF NEW.TypePro = 'Rent' THEN
        SET prefix = 'PR';
    ELSE
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'TypePro must be either Sale or Rent.';
    END IF;

    SET base_prefix = prefix;

    -- Tìm số lớn nhất hiện tại có cùng prefix
    SELECT 
        IFNULL(MAX(CAST(SUBSTRING(PropertyID, 3) AS UNSIGNED)), 0)
    INTO max_number
    FROM properties
    WHERE PropertyID LIKE CONCAT(base_prefix, '%');

    -- Tạo số mới và gán lại PropertyID
    SET new_number = max_number + 1;
    SET NEW.PropertyID = CONCAT(base_prefix, LPAD(new_number, 5, '0'));
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `trg_set_PostedDate`;
DELIMITER $$
CREATE TRIGGER `trg_set_PostedDate` BEFORE INSERT ON `properties` FOR EACH ROW BEGIN
  IF NEW.PostedDate IS NULL THEN
    SET NEW.PostedDate = CURRENT_DATE();  -- Hoặc dùng CURRENT_DATE
  END IF;
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `trg_update_property_id`;
DELIMITER $$
CREATE TRIGGER `trg_update_property_id` BEFORE UPDATE ON `properties` FOR EACH ROW BEGIN
    DECLARE prefix VARCHAR(2);
    DECLARE max_number INT DEFAULT 0;
    DECLARE new_number INT;

    -- Nếu TypePro thay đổi thì thực hiện cập nhật lại PropertyID
    IF OLD.TypePro != NEW.TypePro THEN
        -- Xác định prefix theo TypePro mới
        IF NEW.TypePro = 'Sale' THEN
            SET prefix = 'PS';
        ELSEIF NEW.TypePro = 'Rent' THEN
            SET prefix = 'PR';
        ELSE
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'TypePro must be either Sale or Rent.';
        END IF;

        -- Lấy số thứ tự lớn nhất theo prefix
        SELECT 
            IFNULL(MAX(CAST(SUBSTRING(PropertyID, 3) AS UNSIGNED)), 0)
        INTO max_number
        FROM properties
        WHERE PropertyID LIKE CONCAT(prefix, '%');

        -- Sinh số mới
        SET new_number = max_number + 1;

        -- Gán lại NEW.PropertyID
        SET NEW.PropertyID = CONCAT(prefix, LPAD(new_number, 5, '0'));
    END IF;
END
$$
DELIMITER ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `properties`
--
ALTER TABLE `properties`
  ADD CONSTRAINT `FK_AdminID_UserID` FOREIGN KEY (`ApprovedBy`) REFERENCES `profile_admin` (`UserID`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `FK_AgentID_UserID` FOREIGN KEY (`AgentID`) REFERENCES `profile_agent` (`UserID`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `FK_DanhMucBDS` FOREIGN KEY (`PropertyType`) REFERENCES `danhmuc_pro` (`Protype_ID`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `FK_OwnerID_UserID` FOREIGN KEY (`OwnerID`) REFERENCES `profile_owner` (`UserID`) ON DELETE RESTRICT ON UPDATE RESTRICT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
