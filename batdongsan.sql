-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jun 06, 2025 at 04:43 PM
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

DELIMITER $$
--
-- Procedures
--
DROP PROCEDURE IF EXISTS `DeleteUser_Profile`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `DeleteUser_Profile` (IN `p_userId` VARCHAR(255))   BEGIN
    DECLARE userRole VARCHAR(255);

    -- Lấy vai trò của người dùng (giả sử bảng tên là `users`, cột là `Role`, `UserID`)
    SELECT Role INTO userRole FROM user WHERE UserID = p_userId;

    -- Xóa dữ liệu ở bảng profile tương ứng
    IF userRole = 'Admin' THEN
        DELETE FROM profile_admin WHERE UserID = p_userId;
    ELSEIF userRole = 'Owner' THEN
        DELETE FROM profile_owner WHERE UserID = p_userId;
    ELSEIF userRole = 'Agent' THEN
        DELETE FROM profile_agent WHERE UserID = p_userId;
    ELSEIF userRole = 'Customer' THEN
        DELETE FROM profile_customer WHERE UserID = p_userId;
    END IF;

    -- Xóa người dùng
    DELETE FROM user WHERE UserID = p_userId;
END$$

DROP PROCEDURE IF EXISTS `FilterByRatingAndStatus`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `FilterByRatingAndStatus` (IN `status_input` VARCHAR(50), IN `min_rating` FLOAT, IN `max_rating` FLOAT)   BEGIN
    SELECT *
    FROM feedbacks
    WHERE
      (status_input = 'all' OR status = status_input)
      AND
        ((min_rating = -1 AND max_rating = -1) OR rating BETWEEN min_rating AND max_rating);
END$$

DROP PROCEDURE IF EXISTS `GetActiveProperties`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `GetActiveProperties` ()   BEGIN
    SELECT * FROM properties WHERE Status = 'Active';
END$$

DROP PROCEDURE IF EXISTS `search_user`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `search_user` (IN `p_keyword` VARCHAR(255))   BEGIN
    SELECT *
    FROM user
    WHERE
        UserID   LIKE CONCAT('%', p_keyword, '%') OR
        Name  LIKE CONCAT('%', p_keyword, '%') OR
        Phone LIKE CONCAT('%', p_keyword, '%') OR
        Email LIKE CONCAT('%', p_keyword, '%');
END$$

DROP PROCEDURE IF EXISTS `select_all_from_table_int`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `select_all_from_table_int` (IN `p_table_name` INT)   BEGIN
    SET @query = CONCAT('SELECT * FROM ', p_table_name);
    PREPARE stmt FROM @query;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
END$$

DROP PROCEDURE IF EXISTS `select_all_from_table_varchar`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `select_all_from_table_varchar` (IN `p_table_name` VARCHAR(255))   BEGIN
    SET @query = CONCAT('SELECT * FROM ', p_table_name);
    PREPARE stmt FROM @query;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
END$$

DROP PROCEDURE IF EXISTS `select_all_from_user`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `select_all_from_user` ()   BEGIN
    SELECT * FROM user;
END$$

DROP PROCEDURE IF EXISTS `select_all_from_user_role`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `select_all_from_user_role` (IN `r` VARCHAR(255))   BEGIN
    SELECT * FROM user WHERE Role = r;
END$$

DROP PROCEDURE IF EXISTS `sp_add_appointment`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_add_appointment` (IN `p_PropertyID` VARCHAR(255), IN `p_AgentID` VARCHAR(255), IN `p_UserID` VARCHAR(255), IN `p_Title` VARCHAR(255), IN `p_Start` DATETIME, IN `p_End` DATETIME)   BEGIN
  DECLARE v_conflict INT;
  
  SELECT COUNT(*) INTO v_conflict
  FROM appointments
  WHERE AgentID = p_AgentID
    AND ((AppointmentDateStart <= p_End AND AppointmentDateEnd >= p_Start));

  IF v_conflict > 0 THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Agent already has a conflicting appointment';
  ELSE
    INSERT INTO appointments (PopertyID, AgentID, UserID, TitleAppoint, AppointmentDateStart, AppointmentDateEnd, Status)
    VALUES (p_PropertyID, p_AgentID, p_UserID, p_Title, p_Start, p_End, 'Khởi tạo');
  END IF;
END$$

DROP PROCEDURE IF EXISTS `update_property_id`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `update_property_id` (IN `propId` VARCHAR(20), IN `newTypePro` VARCHAR(10), IN `newType` INT)   BEGIN
    DECLARE prefix VARCHAR(2);
    DECLARE type_str VARCHAR(2);
    DECLARE base_prefix VARCHAR(10);
    DECLARE max_number INT DEFAULT 0;
    DECLARE new_number INT;

    -- Xác định prefix
    IF newTypePro = 'Sale' THEN
        SET prefix = 'PS';
    ELSEIF newTypePro = 'Rent' THEN
        SET prefix = 'PR';
    ELSE
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'TypePro must be Sale or Rent.';
    END IF;

    SET type_str = LPAD(CAST(newType AS CHAR), 2, '0');
    SET base_prefix = CONCAT(prefix, type_str, '0');

    SELECT 
        IFNULL(MAX(CAST(SUBSTRING(PropertyID, LENGTH(base_prefix) + 1) AS UNSIGNED)), 0)
    INTO max_number
    FROM properties
    WHERE PropertyID LIKE CONCAT(base_prefix, '%');

    SET new_number = max_number + 1;

    UPDATE properties
    SET 
        PropertyID = CONCAT(base_prefix, new_number),
        TypePro = newTypePro,
        PropertyType = newType
    WHERE PropertyID = propId;
END$$

--
-- Functions
--
DROP FUNCTION IF EXISTS `fn_count_agent_appointments`$$
CREATE DEFINER=`root`@`localhost` FUNCTION `fn_count_agent_appointments` (`p_agent_id` VARCHAR(255), `p_date` DATE) RETURNS INT  BEGIN
  DECLARE v_count INT;

  SELECT COUNT(*) INTO v_count
  FROM appointments
  WHERE AgentID = p_agent_id
    AND DATE(AppointmentDateStart) = p_date;

  RETURN v_count;
END$$

DROP FUNCTION IF EXISTS `GetFullAddress`$$
CREATE DEFINER=`root`@`localhost` FUNCTION `GetFullAddress` (`addr` VARCHAR(255), `ward` VARCHAR(255), `dist` VARCHAR(255), `city` VARCHAR(255), `prov` VARCHAR(255)) RETURNS VARCHAR(255) CHARSET utf8mb4 COLLATE utf8mb4_vietnamese_ci  BEGIN
    RETURN CONCAT(addr, ', ', ward, ', ', dist, ', ', city, ', ', prov);
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

DROP TABLE IF EXISTS `appointments`;
CREATE TABLE IF NOT EXISTS `appointments` (
  `AppointmentID` int NOT NULL AUTO_INCREMENT,
  `PropertyID` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci DEFAULT NULL,
  `AgentID` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci NOT NULL,
  `OwnerID` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci DEFAULT NULL,
  `CusID` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci DEFAULT NULL,
  `TitleAppoint` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci NOT NULL,
  `DescAppoint` text CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci,
  `AppointmentDateStart` datetime NOT NULL,
  `AppointmentDateEnd` datetime NOT NULL,
  `Status` enum('Đang Thực hiện','Hoàn Thành','Hủy Hẹn','Khởi tạo') CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci NOT NULL DEFAULT 'Khởi tạo',
  PRIMARY KEY (`AppointmentID`),
  KEY `FK_Appointment_Properties` (`PropertyID`),
  KEY `FK_Appointment_UserID` (`AgentID`),
  KEY `FK_Appointment_OwnerID` (`OwnerID`),
  KEY `FK_Appointment_Customer` (`CusID`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_vietnamese_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`AppointmentID`, `PropertyID`, `AgentID`, `OwnerID`, `CusID`, `TitleAppoint`, `DescAppoint`, `AppointmentDateStart`, `AppointmentDateEnd`, `Status`) VALUES
(1, 'PR00001', 'UID00003', 'UID00007', 'UID00005', 'Hẹn xem nhà & Làm Hợp đòng', 'Tôi muốn hẹn hai người tại địa chỉ nhà của chủ sở hữu để xem nhà và đồng thời có thể làm hợp đồng nếu muốn chọn nhà.', '2025-05-21 10:00:00', '2025-05-21 12:00:00', 'Hoàn Thành');

--
-- Triggers `appointments`
--
DROP TRIGGER IF EXISTS `trg_auto_update_appointment_status`;
DELIMITER $$
CREATE TRIGGER `trg_auto_update_appointment_status` BEFORE UPDATE ON `appointments` FOR EACH ROW BEGIN
  IF OLD.AppointmentDateEnd < NOW()
     AND NEW.Status NOT IN ('Khởi tạo', 'Hủy Hẹn') THEN
     SET NEW.Status = 'Hoàn Thành';
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `commission`
--

DROP TABLE IF EXISTS `commission`;
CREATE TABLE IF NOT EXISTS `commission` (
  `CommissionID` int NOT NULL AUTO_INCREMENT,
  `TransactionID` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci NOT NULL,
  `AgentID` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci DEFAULT NULL,
  `Amount` double DEFAULT NULL COMMENT 'Sẽ được tính 1 lần sau khi thuê kết thúc / Còn bán thì sau khi thanh toán xong mới được chi tiền',
  `Percentage` double NOT NULL COMMENT 'Phần trắm của tỉ lệ hoa hồng',
  `TypeCom` enum('Rent','Sale','','') CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci DEFAULT NULL,
  `StatusCommission` enum('Pending','Success','Cancelled','') CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci NOT NULL DEFAULT 'Pending',
  `PaidDate` date DEFAULT NULL,
  PRIMARY KEY (`CommissionID`),
  KEY `FK_Commssion` (`TransactionID`),
  KEY `FK_Agent_Commision` (`AgentID`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_vietnamese_ci;

--
-- Dumping data for table `commission`
--

INSERT INTO `commission` (`CommissionID`, `TransactionID`, `AgentID`, `Amount`, `Percentage`, `TypeCom`, `StatusCommission`, `PaidDate`) VALUES
(1, 'TRAR000001', 'UID00004', 196000000, 0.35, 'Rent', 'Pending', '2025-06-03');

--
-- Triggers `commission`
--
DROP TRIGGER IF EXISTS `trg_before_insert_commission`;
DELIMITER $$
CREATE TRIGGER `trg_before_insert_commission` BEFORE INSERT ON `commission` FOR EACH ROW BEGIN
    DECLARE v_agentID VARCHAR(255);
    DECLARE v_type ENUM('Rent', 'Sale', 'Rent/Sale', '');
    DECLARE v_price DOUBLE DEFAULT 0;
    DECLARE v_rent_month INT DEFAULT 0;
    DECLARE v_total DOUBLE DEFAULT 0;

    -- Lấy AgentID và TransactionType từ bảng Transactions
    SELECT AgentID, TransactionType
    INTO v_agentID, v_type
    FROM Transactions
    WHERE TransactionID = NEW.TransactionID;

    SET NEW.AgentID = v_agentID;
    SET NEW.TypeCom = v_type;

    -- Nếu là thuê (Rent): lấy Price * RentMonth từ detail_transaction
    IF v_type = 'Rent' THEN
        SELECT IFNULL(SUM(Price * RentMonth), 0)
        INTO v_total
        FROM detail_transaction
        WHERE TransactionID = NEW.TransactionID
          AND DTran_Status = 'Hoàn Thành';

        SET NEW.Amount = v_total * NEW.Percentage;

    -- Nếu là mua (Sale): lấy TotalPrice từ Transactions
    ELSEIF v_type = 'Sale' THEN
        SELECT TotalPrice
        INTO v_total
        FROM Transactions
        WHERE TransactionID = NEW.TransactionID;

        SET NEW.Amount = v_total * NEW.Percentage;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `danhmuc_pro`
--

DROP TABLE IF EXISTS `danhmuc_pro`;
CREATE TABLE IF NOT EXISTS `danhmuc_pro` (
  `Protype_ID` int NOT NULL AUTO_INCREMENT,
  `ten_pro` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci NOT NULL,
  `Type` enum('Đất nền','Biệt thự nhà','Chung cư','Văn Phòng') CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci NOT NULL,
  PRIMARY KEY (`Protype_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_vietnamese_ci;

--
-- Dumping data for table `danhmuc_pro`
--

INSERT INTO `danhmuc_pro` (`Protype_ID`, `ten_pro`, `Type`) VALUES
(1, 'Đất Nền', 'Đất nền'),
(2, 'Căn hộ chung cư', 'Chung cư'),
(3, 'Chung cư mini', 'Chung cư'),
(4, 'Nhà Riêng', 'Biệt thự nhà'),
(5, 'Nhà biệt thư, liền kề', 'Biệt thự nhà'),
(6, 'Nhà mặt phố', 'Văn Phòng'),
(7, 'Shophouse, nhà phố thương mại', 'Văn Phòng'),
(8, 'Văn Phòng', 'Văn Phòng'),
(9, 'Kho, Nhà xưởng', 'Văn Phòng'),
(10, 'Khác', 'Đất nền'),
(12, 'Phòng trọ', 'Biệt thự nhà');

-- --------------------------------------------------------

--
-- Table structure for table `detail_pro`
--

DROP TABLE IF EXISTS `detail_pro`;
CREATE TABLE IF NOT EXISTS `detail_pro` (
  `IdDetail` int NOT NULL AUTO_INCREMENT,
  `PropertyID` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci NOT NULL,
  `Levelhouse` int DEFAULT NULL,
  `Floor` int DEFAULT NULL,
  `HouseLength` int DEFAULT NULL,
  `HouseWidth` int DEFAULT NULL,
  `TotalLength` int DEFAULT NULL,
  `TotalWidth` int DEFAULT NULL,
  `Bedroom` int DEFAULT NULL,
  `Balcony` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0 là có, 1 là không',
  `Bath_WC` int DEFAULT NULL,
  `Road` int DEFAULT NULL,
  `legal` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci DEFAULT NULL,
  `view` enum('Bắc','Tây Bắc','Tây','Tây Nam','Nam','Đông Nam','Đông','Đông Bắc') CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci DEFAULT NULL,
  `near` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci DEFAULT NULL,
  `Interior` enum('Cơ Bản','Đầy đủ','') CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci DEFAULT NULL,
  `WaterPrice` enum('Thỏa thuận','Do chủ nhà quy định','Theo nhà cung cấp','') CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci DEFAULT NULL,
  `PowerPrice` enum('Thỏa thuận','Do chủ nhà quy định','Theo nhà cung cấp','') CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci DEFAULT NULL,
  `Utilities` enum('Thỏa thuận','Do chủ nhà quy định','') CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci DEFAULT NULL,
  PRIMARY KEY (`IdDetail`),
  UNIQUE KEY `PropertyID` (`PropertyID`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_vietnamese_ci;

--
-- Dumping data for table `detail_pro`
--

INSERT INTO `detail_pro` (`IdDetail`, `PropertyID`, `Levelhouse`, `Floor`, `HouseLength`, `HouseWidth`, `TotalLength`, `TotalWidth`, `Bedroom`, `Balcony`, `Bath_WC`, `Road`, `legal`, `view`, `near`, `Interior`, `WaterPrice`, `PowerPrice`, `Utilities`) VALUES
(1, 'PR00001', NULL, 2, 12, 15, NULL, NULL, 4, 1, 3, NULL, 'Sổ Đỏ/ Sổ Hồng', 'Tây Bắc', 'Gần Chợ Bách Hóa Xanh, GS25', 'Cơ Bản', 'Thỏa thuận', 'Thỏa thuận', 'Thỏa thuận'),
(2, 'PR00002', 0, 12, 12, 15, NULL, NULL, 3, 1, 2, NULL, 'Sổ hồng / Hợp đồng chuyển nhượng', 'Bắc', NULL, 'Cơ Bản', 'Thỏa thuận', 'Thỏa thuận', 'Thỏa thuận'),
(3, 'PS00001', 3, NULL, 15, 18, 17, 20, 5, 0, 6, 12, 'Sổ hồng', 'Bắc', 'Bách hóa xanh, GS25, Chợ', '', NULL, NULL, NULL),
(4, 'PS00002', 3, NULL, 15, 18, 17, 20, 5, 0, 6, 12, 'Sổ hồng', 'Bắc', 'Bách hóa xanh, GS25, Chợ', '', NULL, NULL, NULL),
(5, 'PS00003', 3, NULL, 15, 18, 17, 20, 5, 0, 6, 12, 'Sổ hồng', 'Bắc', 'Bách hóa xanh, GS25, Chợ', '', 'Theo nhà cung cấp', 'Theo nhà cung cấp', NULL),
(8, 'PS00004', 3, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, 'dsađasadsađasa', 'Tây Bắc', 'GS25', 'Cơ Bản', 'Thỏa thuận', 'Thỏa thuận', 'Thỏa thuận'),
(11, 'PS00005', NULL, NULL, 180, 180, NULL, NULL, 6, 1, 5, NULL, 'Sổ Đỏ', 'Bắc', 'Trường học, Bệnh Viện', 'Cơ Bản', 'Thỏa thuận', 'Thỏa thuận', 'Thỏa thuận'),
(12, 'PS00006', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, 35, NULL, 'Tây Bắc', 'Trường Học', 'Cơ Bản', 'Do chủ nhà quy định', 'Do chủ nhà quy định', 'Thỏa thuận'),
(13, 'PS00008', NULL, NULL, NULL, NULL, 120, 180, NULL, 0, NULL, 25, 'Sổ đỏ', 'Tây Bắc', 'Trường học, GS25, Nhà Thờ', 'Cơ Bản', NULL, NULL, NULL),
(14, 'PR00003', 3, NULL, 160, 120, 180, 160, 3, 1, 2, 20, 'Số đỏ', 'Bắc', 'GS25', 'Cơ Bản', 'Thỏa thuận', 'Thỏa thuận', 'Thỏa thuận'),
(15, 'PR00004', 4, NULL, 150, 150, 160, 180, 4, 1, 3, 15, 'Số đỏ', 'Bắc', 'gần trường tiểu học lê trọng tấn', 'Cơ Bản', 'Thỏa thuận', 'Thỏa thuận', 'Thỏa thuận'),
(16, 'PS00009', 4, NULL, 120, 60, 125, 60, 4, 1, 4, 5, 'Số đỏ / Sổ hồng', 'Bắc', 'gần trường tiểu học lê trọng tấn', NULL, 'Thỏa thuận', 'Thỏa thuận', 'Thỏa thuận');

-- --------------------------------------------------------

--
-- Table structure for table `detail_transaction`
--

DROP TABLE IF EXISTS `detail_transaction`;
CREATE TABLE IF NOT EXISTS `detail_transaction` (
  `TransactionID` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci NOT NULL,
  `Num_Pay` int NOT NULL,
  `Price` double NOT NULL,
  `RentMonth` int DEFAULT NULL COMMENT '//Số tháng',
  `DTran_Date` datetime NOT NULL,
  `InstallPayment` varchar(255) COLLATE utf8mb4_vietnamese_ci DEFAULT NULL,
  `PaymentType` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci DEFAULT NULL,
  `DTran_Status` enum('Chờ đợi','Hoàn Thành','Hủy','') CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci NOT NULL DEFAULT 'Chờ đợi',
  PRIMARY KEY (`TransactionID`,`Num_Pay`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_vietnamese_ci;

--
-- Dumping data for table `detail_transaction`
--

INSERT INTO `detail_transaction` (`TransactionID`, `Num_Pay`, `Price`, `RentMonth`, `DTran_Date`, `InstallPayment`, `PaymentType`, `DTran_Status`) VALUES
('TRAR000001', 1, 560000000, 6, '2025-06-02 12:06:55', NULL, 'MOMO', 'Hoàn Thành'),
('TRAR000002', 1, 72000000, 6, '2025-06-04 12:01:42', NULL, 'MoMo', 'Hoàn Thành');

--
-- Triggers `detail_transaction`
--
DROP TRIGGER IF EXISTS `trg_after_update_detail_transaction`;
DELIMITER $$
CREATE TRIGGER `trg_after_update_detail_transaction` AFTER UPDATE ON `detail_transaction` FOR EACH ROW BEGIN
    DECLARE v_type ENUM('Rent', 'Sale', 'Rent/Sale', '') DEFAULT '';
    DECLARE v_total DOUBLE DEFAULT 0;
    DECLARE v_required DOUBLE DEFAULT 0;

    -- Lấy loại giao dịch và số tiền yêu cầu (TotalPrice nếu là Sale)
    SELECT TransactionType, TotalPrice INTO v_type, v_required
    FROM Transactions
    WHERE TransactionID = NEW.TransactionID;

    -- Tính tổng tiền của các lần đã thanh toán
    SELECT IFNULL(SUM(Price), 0) INTO v_total
    FROM detail_transaction
    WHERE TransactionID = NEW.TransactionID
      AND DTran_Status = 'Hoàn Thành';

    -- Nếu là thuê: chỉ cập nhật tổng tiền
    IF v_type = 'Rent' THEN
        UPDATE Transactions
        SET TotalPrice = v_total
        WHERE TransactionID = NEW.TransactionID;

    -- Nếu là mua: chỉ cập nhật trạng thái nếu đã thanh toán đủ
    ELSEIF v_type = 'Sale' THEN
        IF v_total >= v_required THEN
            UPDATE Transactions
            SET TranStatus = 'Paid',
                TransactionDate = NOW()
            WHERE TransactionID = NEW.TransactionID;
        END IF;
    END IF;
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `trg_before_insert_detail_transaction`;
DELIMITER $$
CREATE TRIGGER `trg_before_insert_detail_transaction` BEFORE INSERT ON `detail_transaction` FOR EACH ROW BEGIN
    DECLARE v_next_pay INT DEFAULT 1;
    DECLARE v_typepro VARCHAR(10);
    DECLARE v_price DOUBLE DEFAULT 0;

    -- Tự động set ngày
    SET NEW.DTran_Date = NOW();

    -- Nếu chưa có Num_Pay từ người dùng
    IF NEW.Num_Pay IS NULL OR NEW.Num_Pay = 0 THEN
        SELECT IFNULL(MAX(Num_Pay), 0) + 1 INTO v_next_pay
        FROM detail_transaction
        WHERE TransactionID = NEW.TransactionID;

        SET NEW.Num_Pay = v_next_pay;
    END IF;

    -- Lấy TypePro và Price từ bảng Properties qua bảng Transactions
    SELECT p.TypePro, p.Price
    INTO v_typepro, v_price
    FROM Transactions t
    JOIN Properties p ON t.PropertyID = p.PropertyID
    WHERE t.TransactionID = NEW.TransactionID;

    -- Nếu là Rent thì tính lại Price = Giá * Số tháng
    IF v_typepro = 'Rent' THEN
        SET NEW.Price = v_price * NEW.RentMonth;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `documents`
--

DROP TABLE IF EXISTS `documents`;
CREATE TABLE IF NOT EXISTS `documents` (
  `DocumentID` int NOT NULL AUTO_INCREMENT,
  `TransactionID` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci NOT NULL,
  `UploadedDate` datetime NOT NULL,
  `DocumentType` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci NOT NULL,
  `FilePath` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci NOT NULL,
  PRIMARY KEY (`DocumentID`),
  KEY `FK_Document_Transaction` (`TransactionID`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_vietnamese_ci;

--
-- Dumping data for table `documents`
--

INSERT INTO `documents` (`DocumentID`, `TransactionID`, `UploadedDate`, `DocumentType`, `FilePath`) VALUES
(1, 'TRAR000001', '2025-06-02 05:16:55', 'DOCX', '/Document/123.docx');

-- --------------------------------------------------------

--
-- Table structure for table `feedbacks`
--

DROP TABLE IF EXISTS `feedbacks`;
CREATE TABLE IF NOT EXISTS `feedbacks` (
  `FeedbackID` int NOT NULL AUTO_INCREMENT,
  `CusID` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci NOT NULL,
  `AgentID` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci NOT NULL,
  `Title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci NOT NULL,
  `Rating` double NOT NULL,
  `Comment` text CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci,
  `FeedbackDate` datetime NOT NULL,
  `Status` enum('Chờ duyệt','Đã duyệt','Hủy bỏ','') CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci NOT NULL DEFAULT 'Chờ duyệt',
  PRIMARY KEY (`FeedbackID`),
  KEY `FK_Feedback_Agent` (`AgentID`),
  KEY `FK_Feedback_Customer` (`CusID`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_vietnamese_ci;

--
-- Dumping data for table `feedbacks`
--

INSERT INTO `feedbacks` (`FeedbackID`, `CusID`, `AgentID`, `Title`, `Rating`, `Comment`, `FeedbackDate`, `Status`) VALUES
(1, 'UID00005', 'UID00004', 'Người môi giới khá tích cực', 5, 'Người môi giới khá tích cực trong việc hỗ trợ xem nhà, và hỗ trợ chúng tôi nhiều giao dịch, lịch sử', '2025-05-28 15:47:08', 'Hủy bỏ'),
(2, 'UID00006', 'UID00004', 'Người môi giới tốt, tôi muốn họ trong tương lai', 3.5, 'Người môi giới khá tích cực trong việc hỗ trợ xem nhà, và hỗ trợ chúng tôi nhiều giao dịch, lịch sử', '2025-05-28 15:47:08', 'Đã duyệt'),
(3, 'UID00005', 'UID00003', 'Không tích cực, chửi khách', 1, 'Tôi không hài lòng về người này.', '2025-05-28 15:47:08', 'Chờ duyệt'),
(4, 'UID00006', 'UID00003', 'Không tích cực, chửi khách', 1, 'Tôi không hài lòng về người này.', '2025-05-28 15:47:08', 'Hủy bỏ');

-- --------------------------------------------------------

--
-- Table structure for table `profile_admin`
--

DROP TABLE IF EXISTS `profile_admin`;
CREATE TABLE IF NOT EXISTS `profile_admin` (
  `UserID` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci NOT NULL,
  `TenChucVu` enum('Nhân viên','Quản trị viên','Giám đốc') CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci NOT NULL DEFAULT 'Nhân viên',
  PRIMARY KEY (`UserID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_vietnamese_ci;

--
-- Dumping data for table `profile_admin`
--

INSERT INTO `profile_admin` (`UserID`, `TenChucVu`) VALUES
('UID00001', 'Quản trị viên'),
('UID00002', 'Nhân viên'),
('UID00010', 'Giám đốc');

-- --------------------------------------------------------

--
-- Table structure for table `profile_agent`
--

DROP TABLE IF EXISTS `profile_agent`;
CREATE TABLE IF NOT EXISTS `profile_agent` (
  `UserID` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci NOT NULL,
  `Certificate` text CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci COMMENT 'Link Hình ảnh GG drive',
  `DistrictAgent` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci DEFAULT NULL,
  `ProvinceAgent` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci NOT NULL,
  `ContactAgent` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci DEFAULT NULL,
  `NumberCardAgent` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci DEFAULT NULL COMMENT 'Số thẻ vật lý của Môi giới',
  PRIMARY KEY (`UserID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_vietnamese_ci;

--
-- Dumping data for table `profile_agent`
--

INSERT INTO `profile_agent` (`UserID`, `Certificate`, `DistrictAgent`, `ProvinceAgent`, `ContactAgent`, `NumberCardAgent`) VALUES
('UID00003', NULL, 'Quận 10 ', 'Thành Phố Hồ Chí Minh', NULL, NULL),
('UID00004', NULL, 'Quận 8', 'Thành Phố Hồ Chí Minh', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `profile_customer`
--

DROP TABLE IF EXISTS `profile_customer`;
CREATE TABLE IF NOT EXISTS `profile_customer` (
  `UserID` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci NOT NULL COMMENT '	Lịch sử giao dịch của khách hàng',
  `Whitelist` text CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci COMMENT '	Loại bất động sản ưu tiên',
  `PreferredPropertyType` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci DEFAULT NULL COMMENT 'Danh sách các bất động sản yêu thích	',
  PRIMARY KEY (`UserID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_vietnamese_ci;

--
-- Dumping data for table `profile_customer`
--

INSERT INTO `profile_customer` (`UserID`, `Whitelist`, `PreferredPropertyType`) VALUES
('UID00005', NULL, NULL),
('UID00006', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `profile_owner`
--

DROP TABLE IF EXISTS `profile_owner`;
CREATE TABLE IF NOT EXISTS `profile_owner` (
  `UserID` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci NOT NULL,
  `ContactOwner` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci DEFAULT NULL,
  `NumberCardOwner` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci DEFAULT NULL COMMENT 'Số Thẻ vật lý',
  `GiayTo` text CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci COMMENT 'Link Giay To GG Drive',
  PRIMARY KEY (`UserID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_vietnamese_ci;

--
-- Dumping data for table `profile_owner`
--

INSERT INTO `profile_owner` (`UserID`, `ContactOwner`, `NumberCardOwner`, `GiayTo`) VALUES
('UID00007', NULL, NULL, NULL),
('UID00008', NULL, NULL, NULL),
('UID00009', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `properties`
--

DROP TABLE IF EXISTS `properties`;
CREATE TABLE IF NOT EXISTS `properties` (
  `PropertyID` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci NOT NULL,
  `OwnerID` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci NOT NULL COMMENT 'ID của Owner',
  `AgentID` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci DEFAULT NULL COMMENT 'ID của Agent',
  `UserCreate` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci DEFAULT NULL COMMENT 'Dành cho admin (Nhân viên) đăng tin',
  `PostedDate` date NOT NULL,
  `ApprovedBy` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci DEFAULT NULL COMMENT 'Được duyệt bởi Admin nào',
  `ApprovedDate` date DEFAULT NULL COMMENT 'Ngày duyệt',
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
  KEY `FK_AgentID_UserID` (`AgentID`),
  KEY `FK_OwnerID_UserID` (`OwnerID`),
  KEY `FK_DanhMucBDS` (`PropertyType`),
  KEY `FK_Approved_UserID` (`ApprovedBy`),
  KEY `FK_UserID_CreateUser` (`UserCreate`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_vietnamese_ci;

--
-- Dumping data for table `properties`
--

INSERT INTO `properties` (`PropertyID`, `OwnerID`, `AgentID`, `UserCreate`, `PostedDate`, `ApprovedBy`, `ApprovedDate`, `Status`, `Province`, `District`, `Ward`, `Address`, `PropertyType`, `Price`, `Title`, `Description`, `TypePro`) VALUES
('PR00001', 'UID00007', 'UID00003', NULL, '2025-06-04', 'UID00002', '2025-06-04', 'pending', 'Thành Phố Hồ Chí Minh', 'Quận Tân Bình', 'Phường 11', '12 Ca Văn Thỉnh', 3, 12000000, 'Cho Thuê Chung Cư Mini Sactaim', 'Chung cư sẽ có các điều hòa', 'Rent'),
('PR00002', 'UID00007', NULL, NULL, '2025-05-22', NULL, '0000-00-00', 'pending', 'Thành Phố Hồ Chí Minh', 'Quận Tân Phú', 'Phường Tân Thành', '49 Trần Hưng Đạo', 4, 10000000, 'Cho Thuê Nhà Cấp 2', 'Nhà Đẹp', 'Rent'),
('PR00003', 'UID00007', NULL, 'UID00001', '2025-05-30', 'UID00001', '2025-05-30', 'rejected', 'Tỉnh Hoà Bình', 'Huyện Kim Bôi', 'Xã Tú Sơn', '12 Hoàng Văn Thụ', 4, 12000000000, 'Thuê Nhà Huyện Kim Bôi', 'Nhà Đẹp', 'Rent'),
('PR00004', 'UID00007', 'UID00004', 'UID00001', '2025-05-30', 'UID00001', '2025-05-30', 'rented', 'Thành phố Hồ Chí Minh', 'Quận Tân Bình', 'Phường 11', '12 Hoàng Văn Thụ', 4, 12000000000, 'Bán Nhà Riêng Tại Phường 11', 'Bán Nhà Riêng Tại Phường 11', 'Rent'),
('PS00001', 'UID00007', NULL, NULL, '2025-05-22', NULL, '0000-00-00', 'pending', 'Thành Phố Hồ Chí Minh', 'Quận Tân Bình', 'Phường 13', '4-6 Đ. Ấp Bắc', 1, 4500000000, 'Bán Nhà Quận Tân Bình cấp 2, gần sân bay', 'abc\r\n123', 'Sale'),
('PS00002', 'UID00008', NULL, NULL, '2025-05-18', NULL, '2025-05-25', 'active', 'Thành Phố Hồ Chí Minh', 'Quận Tân Bình', 'Phường 11', '1 Ca Văn Thỉnh', 3, 1200000000, 'Cho Bán Phòng 2 phòng của Sactaim', 'Chung cư này sẽ có ...', 'Sale'),
('PS00003', 'UID00009', 'UID00003', NULL, '2025-05-22', 'UID00001', '2025-05-25', 'active', 'Thành Phố Hồ Chí Minh', 'Quận Tân Bình', 'Phường 7', '60/2 Đ. Văn Còi', 6, 800000000, 'Bán nhà cho chuyên duyệt bán cửa hàng tiện lợi', 'Luôn sẽ có các ...', 'Sale'),
('PS00004', 'UID00008', NULL, 'UID00001', '2025-05-25', 'UID00001', '2025-05-26', 'active', 'Thành phố Hồ Chí Minh', 'Quận Tân Phú', 'Phường Tây Thạnh', '31 Lê Trọng Tấn', 3, 12000000, 'sdấdsadsadá', 'dsfdsfdsfsfdsf', 'Sale'),
('PS00005', 'UID00007', NULL, 'UID00001', '2025-05-25', NULL, NULL, 'pending', 'Tỉnh Bà Rịa - Vũng Tàu', 'Thành phố Vũng Tàu', 'Phường 8', '320 Trương Công Định', 1, 12000000000, 'Bán căn hộ phường 8', 'sađasadsadsadsadsa', 'Sale'),
('PS00006', 'UID00007', NULL, 'UID00001', '2025-05-25', NULL, NULL, 'pending', 'Thành phố Hồ Chí Minh', 'Thành phố Thủ Đức', 'Phường An Khánh', '136 Trần Não', 1, 96000000000, 'Bán Đất Quận 8 Gần Nhà Thờ', 'Bán Đất Quận 8 Gần Nhà Thờ', 'Sale'),
('PS00007', 'UID00007', 'UID00003', NULL, '2025-05-26', 'UID00001', '2025-05-26', 'active', 'Tỉnh Bà Rịa - Vũng Tàu', 'Thành phố Vũng Tàu', 'Phường 3', '1 Lê Hồng Phong', 2, 12000000000, 'Bán Phòng ở trên căn hộ phường 3', 'Bán Phòng ở trên căn hộ phường 3 (Tivi, Tủ Lạnh,..)', 'Sale'),
('PS00008', 'UID00007', NULL, 'UID00001', '2025-05-26', NULL, NULL, 'pending', 'Thành phố Hồ Chí Minh', 'Thành phố Thủ Đức', 'Phường An Khánh', '120 Trần Não', 1, 24000000000, 'Bán Đất Nền Tại Trần Não', 'Bán Đất Nền Tại Trần Não đang quy hoạch', 'Sale'),
('PS00009', 'UID00007', NULL, 'UID00001', '2025-06-03', NULL, NULL, 'pending', 'Tỉnh Quảng Ninh', 'Thành phố Uông Bí', 'Phường Bắc Sơn', '12 Hoàng Văn Thụ', 2, 12000000000, 'Bán Nhà Riêng Tại Phường 12 Quận 8', 'áđasadsađá', 'Sale');

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
        SET NEW.ApprovedDate = CURRENT_DATE();
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

-- --------------------------------------------------------

--
-- Table structure for table `propertyimage`
--

DROP TABLE IF EXISTS `propertyimage`;
CREATE TABLE IF NOT EXISTS `propertyimage` (
  `ImageID` int NOT NULL AUTO_INCREMENT,
  `PropertyID` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci NOT NULL,
  `ImagePath` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci NOT NULL,
  `Caption` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci DEFAULT NULL,
  `UploadedDate` datetime NOT NULL,
  PRIMARY KEY (`ImageID`),
  KEY `FK_Property_ImageProperty` (`PropertyID`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_vietnamese_ci;

--
-- Dumping data for table `propertyimage`
--

INSERT INTO `propertyimage` (`ImageID`, `PropertyID`, `ImagePath`, `Caption`, `UploadedDate`) VALUES
(3, 'PR00001', 'properties/images/anh-1.jpg', 'Sảnh phòng trọ', '2025-05-22 21:53:32'),
(4, 'PR00001', 'properties/images/anh-2.jpg', 'Phòng của nhà thuê', '2025-05-22 21:53:32'),
(5, 'PS00004', 'properties/images/QAOg68nFkBLVwOqetfeJiYbJelcGimfsofpB0Glv.jpg', NULL, '2025-05-25 16:57:10'),
(6, 'PS00005', 'properties/PS00005/THkDazS5JguIxMmf8XUzy436a9fqr9TYExbAlukS.jpg', NULL, '2025-05-25 17:56:27'),
(7, 'PS00005', 'properties/PS00005/0ca38OBC39EtdIboGHnIeCnXVQDdGup4wdThj4tl.jpg', NULL, '2025-05-25 17:56:27'),
(8, 'PS00005', 'properties/PS00005/HABVTRQpb7K7Tsm4dS95p7SqdPmVJJYwBJgpa4kh.jpg', NULL, '2025-05-25 17:56:27'),
(9, 'PS00005', 'properties/PS00005/gsrcbpw2cGtqpzdPKxl8JyuHbD8X3z8vOcbqrqfQ.jpg', NULL, '2025-05-25 17:56:27'),
(10, 'PS00006', 'properties/PS00006/igeonv4xWtd2ktAYMHtU7GDuPsuYoLjiEZSwQRPo.jpg', NULL, '2025-05-25 19:12:46'),
(11, 'PS00006', 'properties/PS00006/C7ra83wbgwPN2Tn1JwTReb0Aw1uDenLZpKt2hBTV.jpg', NULL, '2025-05-25 19:12:46'),
(12, 'PS00008', 'properties/PS00008/U2kj44cNgM9ipja2kS3aJaK8D51iz58oLowg8xhp.jpg', NULL, '2025-05-26 03:43:01'),
(13, 'PS00008', 'properties/PS00008/tUY7I4bJpzowYkFKBOvBKLVPtRQj3JG6fiL2WLCZ.jpg', NULL, '2025-05-26 03:43:01'),
(14, 'PR00003', 'properties/PR00003/5pQxccb1UIA0DQrGmRqKdNU6izfPNE2e1EOr6yYh.jpg', NULL, '2025-05-30 02:44:39'),
(15, 'PR00003', 'properties/PR00003/CSAphn7XUbgLj7ecuu61hBfPntXj62AHj6PNELcg.jpg', NULL, '2025-05-30 02:44:39'),
(16, 'PR00003', 'properties/PR00003/LaW9vvQ7tVVDEa60HGo2voaDGvaqgouIUXKueIbA.png', NULL, '2025-05-30 02:44:39'),
(17, 'PR00004', 'properties/PR00004/zv80iqgPZpqbKkJBs53R4cdXjB6Q6ML812pZxJ6E.jpg', NULL, '2025-05-30 03:00:52'),
(18, 'PR00004', 'properties/PR00004/nBQGRb1qud3XrAwTMxyBtUl6rxNCA5ilR5dGSrQm.jpg', NULL, '2025-05-30 03:00:52'),
(19, 'PR00004', 'properties/PR00004/F8fILC7RZz8lHNCTahYVpwqaYVipfH7MEQ4lPVC3.png', NULL, '2025-05-30 03:00:52'),
(20, 'PS00009', 'images/properties/PS00009/1748927648_683e84a08e987.jpg', NULL, '2025-06-03 05:14:08'),
(21, 'PS00009', 'images/properties/PS00009/1748927648_683e84a093173.jpg', NULL, '2025-06-03 05:14:08'),
(22, 'PS00009', 'images/properties/PS00009/1748927648_683e84a094bc0.jpg', NULL, '2025-06-03 05:14:08'),
(23, 'PS00009', 'images/properties/PS00009/1748927648_683e84a096ba5.jpg', NULL, '2025-06-03 05:14:08');

-- --------------------------------------------------------

--
-- Table structure for table `propertyvideos`
--

DROP TABLE IF EXISTS `propertyvideos`;
CREATE TABLE IF NOT EXISTS `propertyvideos` (
  `VideoID` int NOT NULL AUTO_INCREMENT,
  `PropertyID` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci NOT NULL,
  `VideoPath` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci NOT NULL,
  `Caption` text CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci,
  `UploadedDate` datetime NOT NULL,
  PRIMARY KEY (`VideoID`),
  KEY `FK_Property_VideoProperty` (`PropertyID`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_vietnamese_ci;

--
-- Dumping data for table `propertyvideos`
--

INSERT INTO `propertyvideos` (`VideoID`, `PropertyID`, `VideoPath`, `Caption`, `UploadedDate`) VALUES
(2, 'PS00009', 'https://www.youtube.com/watch?v=eoM5sDSoiV0', NULL, '2025-06-03 05:14:08');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

DROP TABLE IF EXISTS `transactions`;
CREATE TABLE IF NOT EXISTS `transactions` (
  `TransactionID` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci NOT NULL,
  `PropertyID` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci NOT NULL,
  `AgentID` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci NOT NULL,
  `OwnerID` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci NOT NULL,
  `CusID` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci NOT NULL,
  `TotalPrice` double NOT NULL,
  `TransactionDate` datetime DEFAULT CURRENT_TIMESTAMP,
  `TransactionType` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci NOT NULL,
  `TranStatus` enum('Pending','Paid','Cancelled','') CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci NOT NULL DEFAULT 'Pending',
  PRIMARY KEY (`TransactionID`),
  KEY `FK_Transaction_Property` (`PropertyID`),
  KEY `FK_AgentID_Transactions` (`AgentID`),
  KEY `FK_CusID_Transactions` (`CusID`),
  KEY `FK_OwnerID_Transactions` (`OwnerID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_vietnamese_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`TransactionID`, `PropertyID`, `AgentID`, `OwnerID`, `CusID`, `TotalPrice`, `TransactionDate`, `TransactionType`, `TranStatus`) VALUES
('TRAR000001', 'PR00004', 'UID00004', 'UID00007', 'UID00005', 560000000, '2025-05-31 09:42:35', 'Rent', 'Paid'),
('TRAR000002', 'PR00001', 'UID00003', 'UID00007', 'UID00006', 72000000, '2025-06-04 11:50:38', 'Rent', 'Pending');

--
-- Triggers `transactions`
--
DROP TRIGGER IF EXISTS `before_insert_transactions`;
DELIMITER $$
CREATE TRIGGER `before_insert_transactions` BEFORE INSERT ON `transactions` FOR EACH ROW BEGIN
    DECLARE max_number INT DEFAULT 0;
    DECLARE new_number INT;
    DECLARE type_pro VARCHAR(255);
    DECLARE prefix VARCHAR(4);
    DECLARE agent_id VARCHAR(255);
    DECLARE owner_id VARCHAR(255);

    -- Lấy TypePro, AgentID và OwnerID từ properties
    SELECT TypePro, AgentID, OwnerID
    INTO type_pro, agent_id, owner_id
    FROM properties
    WHERE PropertyID = NEW.PropertyID;

    -- Xác định prefix
    IF type_pro = 'Sale' THEN -- Giả sử 1 là Sale
        SET prefix = 'TRAS';
    ELSEIF type_pro = 'Rent' THEN -- Giả sử 2 là Rent
        SET prefix = 'TRAR';
    ELSE
        SET prefix = 'TRXX'; -- Nếu TypePro không rõ
    END IF;

    -- Lấy số lớn nhất đang có từ TransactionID dạng đúng prefix
    SELECT IFNULL(MAX(CAST(SUBSTRING(TransactionID, 5) AS UNSIGNED)), 0)
    INTO max_number
    FROM transactions
    WHERE LEFT(TransactionID, 4) = prefix;

    SET new_number = max_number + 1;

    -- Gán TransactionID với prefix đúng
    SET NEW.TransactionID = CONCAT(prefix, LPAD(new_number, 6, '0'));

    -- Gán AgentID và OwnerID từ bảng properties
    SET NEW.AgentID = agent_id;
    SET NEW.OwnerID = owner_id;
    SET NEW.TransactionType = type_pro;
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `trg_before_update_transaction_status`;
DELIMITER $$
CREATE TRIGGER `trg_before_update_transaction_status` BEFORE UPDATE ON `transactions` FOR EACH ROW BEGIN
    -- Kiểm tra nếu trạng thái cập nhật là 'Paid' mà trước đó chưa phải 'Paid'
    IF NEW.TranStatus = 'Paid' AND OLD.TranStatus <> 'Paid' THEN

        IF NEW.TransactionType = 'Rent' THEN
            UPDATE Properties
            SET Status = 'Rented'
            WHERE PropertyID = NEW.PropertyID;

        ELSEIF NEW.TransactionType = 'Sale' THEN
            UPDATE Properties
            SET Status = 'Sold'
            WHERE PropertyID = NEW.PropertyID;
        END IF;

    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `UserID` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci NOT NULL,
  `Name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci NOT NULL,
  `Email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci NOT NULL,
  `Birth` date NOT NULL,
  `Sex` enum('Nam','Nữ','Khác') CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci NOT NULL DEFAULT 'Khác',
  `IdentityCard` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci NOT NULL,
  `Phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci DEFAULT NULL,
  `Address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci DEFAULT NULL,
  `Ward` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci DEFAULT NULL,
  `District` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci DEFAULT NULL,
  `Province` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci DEFAULT NULL,
  `Role` enum('Customer','Agent','Admin','Owner') CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci NOT NULL DEFAULT 'Customer',
  `StatusUser` enum('active','inactive','') CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci NOT NULL DEFAULT 'active' COMMENT 'Trạng thái người dùng',
  `PasswordHash` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci NOT NULL COMMENT 'Mã băm mật khẩu',
  `Avatar` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci DEFAULT NULL,
  PRIMARY KEY (`UserID`),
  UNIQUE KEY `Email` (`Email`),
  UNIQUE KEY `IdentityCard` (`IdentityCard`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_vietnamese_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`UserID`, `Name`, `Email`, `Birth`, `Sex`, `IdentityCard`, `Phone`, `Address`, `Ward`, `District`, `Province`, `Role`, `StatusUser`, `PasswordHash`, `Avatar`) VALUES
('UID00001', 'Nguyễn Huỳnh Thanh Phát', 'everyonebody440@gmail.com', '2003-08-19', 'Nam', '012345678', '0856254139', '31/16 Bùi Xuân Phái', 'Phường Tây Thạnh', 'Quận Tân Phú', 'Thành Phố Hồ Chí Minh', 'Admin', 'active', 'e10adc3949ba59abbe56e057f20f883e', NULL),
('UID00002', 'Nguyễn Văn Lượng', 'luongvannguyen2012@gmail.com', '1992-05-19', 'Khác', '023156212351', '0856245139', '12/45/2 Tôn Đức Hoàng', 'Phường 2', 'Quận 3', 'Thành Phố Hồ Chí Minh', 'Admin', 'inactive', 'd6d1d2b50655a964810ba5592c9200a5', NULL),
('UID00003', 'Nguyễn Văn A', 'nguyenvana1203@gmail.com', '1990-03-12', 'Nam', '012345768', '012345678', '1 Lê Hồng Phong', 'Phường 8', 'Vũng Tàu', 'Tỉnh Bà Rịa-Vũng Tàu', 'Agent', 'inactive', 'e10adc3949ba59abbe56e057f20f883e', NULL),
('UID00004', 'Kha Nguyễn Văn', 'nguyenvankha234@yahoo.com', '1992-05-12', 'Khác', '012346571', '0855123149', '12 Khiêm Phạm Tường', 'Phường 2', 'Quận 3', 'Thành Phố Hồ Chí Minh', 'Agent', 'active', 'e10adc3949ba59abbe56e057f20f883e', NULL),
('UID00005', 'Nguyễn Như Quỳnh', 'nhuquynh1234@gmail.com', '2003-01-01', 'Nữ', '012345672', '0823145619', '1 Lữ Gia ', 'Phường Tây Thạnh', 'Quận Tân Phú', 'Thành Phố Hồ Chí Minh', 'Customer', 'active', 'e10adc3949ba59abbe56e057f20f883e', NULL),
('UID00006', 'Kha Hoàng Vân', 'vanhoangkha112@gmail.com', '1992-08-12', 'Nam', '012156423', '072314535', '12 Hoàng Văn Thụ', 'Phường 10', 'Quận 5', 'Thủ Đô Hà Nội', 'Customer', 'active', '0b25a447e0cf38f88a451aa33956e0f2', NULL),
('UID00007', 'Nguyễn Hoàng Phát', 'nguyenphat241203@gmail.com', '2003-12-24', 'Nam', '066203010850', '0855542696', '65/20 Nguyen Do Cung', 'Phường Tây Thạnh', 'Quận Tân Phú', 'Thành Phố Hồ Chí Minh', 'Owner', 'active', 'e10adc3949ba59abbe56e057f20f883e', NULL),
('UID00008', 'Jack NewTome', 'jacknguyen2105@gmail.com', '2003-01-15', 'Nam', '012345123', '085624131', '1 Tricker', 'Phường 2', 'Quận 12', 'Thành Phố Hồ Chí Minh', 'Owner', 'active', '4089e4ce11bc26b10bbfac9a40a670ca', NULL),
('UID00009', 'Nguyễn Văn Toàn', 'toanvannguyen2012@yahoo.com', '1992-06-16', 'Khác', '023156212251', '0856245139', '45 Tôn Đức Hoàng', 'Phường 2', 'Quận 3', 'Thành Phố Hồ Chí Minh', 'Owner', 'active', 'd6d1d2b50655a964810ba5592c9200a5', NULL),
('UID00010', 'Thanh Phát', 'everyonebody130@gmail.com', '1999-05-28', 'Nam', '123456775', '0856254187', '32/16 Bùi Xuân Phái', 'Xã Bình Giã', 'Huyện Châu Đức', 'Tỉnh Bà Rịa - Vũng Tàu', 'Admin', 'active', 'c33367701511b4f6020ec61ded352059', NULL);

--
-- Triggers `user`
--
DROP TRIGGER IF EXISTS `after_user_insert`;
DELIMITER $$
CREATE TRIGGER `after_user_insert` AFTER INSERT ON `user` FOR EACH ROW IF NEW.Role = 'Customer' THEN
        INSERT INTO profile_customer (UserID) VALUES (NEW.UserID);
    ELSEIF NEW.Role = 'Agent' THEN
        INSERT INTO profile_agent (UserID) VALUES (NEW.UserID);
    ELSEIF NEW.Role = 'Owner' THEN
        INSERT INTO profile_owner (UserID) VALUES (NEW.UserID);
    ELSEIF NEW.Role = 'Admin' THEN
        INSERT INTO profile_admin (UserID) VALUES (NEW.UserID);
    END IF
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `before_user_insert`;
DELIMITER $$
CREATE TRIGGER `before_user_insert` BEFORE INSERT ON `user` FOR EACH ROW BEGIN
    DECLARE last_uid VARCHAR(255);
    DECLARE last_num INT DEFAULT 0;
    DECLARE new_num INT;
    DECLARE new_uid VARCHAR(255);

    -- Tìm UserID lớn nhất có dạng UIDxxxxx
    SELECT MAX(UserID) INTO last_uid
    FROM user
    WHERE UserID LIKE 'UID%';

    -- Nếu có rồi, tách số ra
    IF last_uid IS NOT NULL THEN
        SET last_num = CAST(SUBSTRING(last_uid, 4) AS UNSIGNED);
    END IF;

    -- Tăng số và tạo UserID mới
    SET new_num = last_num + 1;
    SET new_uid = CONCAT('UID', LPAD(new_num, 5, '0'));

    -- Gán lại cho NEW.UserID
    SET NEW.UserID = new_uid;
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `before_user_update`;
DELIMITER $$
CREATE TRIGGER `before_user_update` BEFORE UPDATE ON `user` FOR EACH ROW IF NEW.UserID != OLD.UserID THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'UserID is auto-generated and cannot be updated.';
    END IF
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `trg_before_delete_user`;
DELIMITER $$
CREATE TRIGGER `trg_before_delete_user` BEFORE DELETE ON `user` FOR EACH ROW IF OLD.Role = 'Admin' THEN
        DELETE FROM profile_admin WHERE UserID = OLD.UserID;
    ELSEIF OLD.Role = 'Agent' THEN
        DELETE FROM profile_agent WHERE UserID = OLD.UserID;
    ELSEIF OLD.Role = 'Owner' THEN
        DELETE FROM profile_owner WHERE UserID = OLD.UserID;
    ELSEIF OLD.Role = 'Customer' THEN
        DELETE FROM profile_customer WHERE UserID = OLD.UserID;
    END IF
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `trg_check_identity_card`;
DELIMITER $$
CREATE TRIGGER `trg_check_identity_card` BEFORE INSERT ON `user` FOR EACH ROW BEGIN
IF NOT (NEW.identityCard REGEXP '^[0-9]{9}$' OR NEW.identityCard REGEXP '^[0-9]{12}$') THEN
    SIGNAL SQLSTATE '45000'
      SET MESSAGE_TEXT = 'Số CMND/CCCD không hợp lệ. Phải gồm 9 hoặc 12 chữ số.';
  END IF;
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `trg_check_identity_card_update`;
DELIMITER $$
CREATE TRIGGER `trg_check_identity_card_update` BEFORE UPDATE ON `user` FOR EACH ROW BEGIN
  IF NOT (NEW.identityCard REGEXP '^[0-9]{9}$' OR NEW.identityCard REGEXP '^[0-9]{12}$') THEN
    SIGNAL SQLSTATE '45000'
      SET MESSAGE_TEXT = 'Số CMND/CCCD khi cập nhật không hợp lệ. Phải gồm 9 hoặc 12 chữ số.';
  END IF;
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `trg_user_role_update`;
DELIMITER $$
CREATE TRIGGER `trg_user_role_update` BEFORE UPDATE ON `user` FOR EACH ROW BEGIN
    -- Xóa profile cũ theo Role trước đó
    IF OLD.Role != NEW.Role THEN
        IF OLD.Role = 'Admin' THEN
            DELETE FROM profile_admin WHERE UserID = OLD.UserID;
        ELSEIF OLD.Role = 'Agent' THEN
            DELETE FROM profile_agent WHERE UserID = OLD.UserID;
        ELSEIF OLD.Role = 'Customer' THEN
            DELETE FROM profile_customer WHERE UserID = OLD.UserID;
        ELSEIF OLD.Role = 'Owner' THEN
            DELETE FROM profile_owner WHERE UserID = OLD.UserID;
        END IF;

        -- Thêm vào bảng profile mới tương ứng với Role mới
        IF NEW.Role = 'Admin' THEN
            INSERT INTO profile_admin(UserID) VALUES (OLD.UserID);
        ELSEIF NEW.Role = 'Agent' THEN
            INSERT INTO profile_agent(UserID) VALUES (OLD.UserID);
        ELSEIF NEW.Role = 'Customer' THEN
            INSERT INTO profile_customer(UserID) VALUES (OLD.UserID);
        ELSEIF NEW.Role = 'Owner' THEN
            INSERT INTO profile_owner(UserID) VALUES (OLD.UserID);
        END IF;
    END IF;
END
$$
DELIMITER ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `FK_Appointment_Customer` FOREIGN KEY (`CusID`) REFERENCES `profile_customer` (`UserID`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `FK_Appointment_OwnerID` FOREIGN KEY (`OwnerID`) REFERENCES `profile_owner` (`UserID`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `FK_Appointment_Properties` FOREIGN KEY (`PropertyID`) REFERENCES `properties` (`PropertyID`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `FK_Appointment_UserID` FOREIGN KEY (`AgentID`) REFERENCES `profile_agent` (`UserID`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `commission`
--
ALTER TABLE `commission`
  ADD CONSTRAINT `FK_Agent_Commision` FOREIGN KEY (`AgentID`) REFERENCES `profile_agent` (`UserID`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `FK_Commssion` FOREIGN KEY (`TransactionID`) REFERENCES `transactions` (`TransactionID`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `detail_pro`
--
ALTER TABLE `detail_pro`
  ADD CONSTRAINT `FK_IdDetail` FOREIGN KEY (`PropertyID`) REFERENCES `properties` (`PropertyID`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `detail_transaction`
--
ALTER TABLE `detail_transaction`
  ADD CONSTRAINT `FK_Trans_DetailTrans` FOREIGN KEY (`TransactionID`) REFERENCES `transactions` (`TransactionID`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `documents`
--
ALTER TABLE `documents`
  ADD CONSTRAINT `FK_Document_Transaction` FOREIGN KEY (`TransactionID`) REFERENCES `transactions` (`TransactionID`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `feedbacks`
--
ALTER TABLE `feedbacks`
  ADD CONSTRAINT `FK_Feedback_Agent` FOREIGN KEY (`AgentID`) REFERENCES `profile_agent` (`UserID`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `FK_Feedback_Customer` FOREIGN KEY (`CusID`) REFERENCES `profile_customer` (`UserID`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `profile_admin`
--
ALTER TABLE `profile_admin`
  ADD CONSTRAINT `FK_Admin` FOREIGN KEY (`UserID`) REFERENCES `user` (`UserID`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `profile_agent`
--
ALTER TABLE `profile_agent`
  ADD CONSTRAINT `FK_Agent` FOREIGN KEY (`UserID`) REFERENCES `user` (`UserID`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `profile_customer`
--
ALTER TABLE `profile_customer`
  ADD CONSTRAINT `FK_Customer` FOREIGN KEY (`UserID`) REFERENCES `user` (`UserID`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `profile_owner`
--
ALTER TABLE `profile_owner`
  ADD CONSTRAINT `FK_Owner` FOREIGN KEY (`UserID`) REFERENCES `user` (`UserID`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `properties`
--
ALTER TABLE `properties`
  ADD CONSTRAINT `FK_AgentID_UserID` FOREIGN KEY (`AgentID`) REFERENCES `profile_agent` (`UserID`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `FK_Approved_UserID` FOREIGN KEY (`ApprovedBy`) REFERENCES `profile_admin` (`UserID`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `FK_DanhMucBDS` FOREIGN KEY (`PropertyType`) REFERENCES `danhmuc_pro` (`Protype_ID`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `FK_OwnerID_UserID` FOREIGN KEY (`OwnerID`) REFERENCES `profile_owner` (`UserID`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `FK_UserID_CreateUser` FOREIGN KEY (`UserCreate`) REFERENCES `profile_admin` (`UserID`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `propertyimage`
--
ALTER TABLE `propertyimage`
  ADD CONSTRAINT `FK_Property_ImageProperty` FOREIGN KEY (`PropertyID`) REFERENCES `properties` (`PropertyID`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `propertyvideos`
--
ALTER TABLE `propertyvideos`
  ADD CONSTRAINT `FK_Property_VideoProperty` FOREIGN KEY (`PropertyID`) REFERENCES `properties` (`PropertyID`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `FK_AgentID_Transactions` FOREIGN KEY (`AgentID`) REFERENCES `profile_agent` (`UserID`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `FK_CusID_Transactions` FOREIGN KEY (`CusID`) REFERENCES `profile_customer` (`UserID`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `FK_OwnerID_Transactions` FOREIGN KEY (`OwnerID`) REFERENCES `profile_owner` (`UserID`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `FK_Transaction_Property` FOREIGN KEY (`PropertyID`) REFERENCES `properties` (`PropertyID`) ON DELETE RESTRICT ON UPDATE RESTRICT;

DELIMITER $$
--
-- Events
--
DROP EVENT IF EXISTS `ev_update_status`$$
CREATE DEFINER=`root`@`localhost` EVENT `ev_update_status` ON SCHEDULE EVERY 1 HOUR STARTS '2025-05-18 02:28:05' ON COMPLETION NOT PRESERVE ENABLE DO UPDATE appointments
  SET Status = 'Hoàn Thành'
  WHERE AppointmentDateEnd < NOW()
    AND Status NOT IN ('Khởi tạo', 'Hủy Hẹn')$$

DELIMITER ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
