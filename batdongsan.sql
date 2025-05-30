-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: May 26, 2025 at 09:08 PM
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
-- Table structure for table `admin_question`
--

DROP TABLE IF EXISTS `admin_question`;
CREATE TABLE IF NOT EXISTS `admin_question` (
  `id` int NOT NULL AUTO_INCREMENT,
  `question_text` text CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci NOT NULL,
  `pattern` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci NOT NULL,
  `handler` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci DEFAULT NULL,
  `desc` text CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_vietnamese_ci;

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

DROP TABLE IF EXISTS `appointments`;
CREATE TABLE IF NOT EXISTS `appointments` (
  `AppointmentID` int NOT NULL AUTO_INCREMENT,
  `PropertyID` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci NOT NULL,
  `AgentID` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci NOT NULL,
  `OwnerID` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci DEFAULT NULL,
  `CusID` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci DEFAULT NULL,
  `TitleAppoint` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci NOT NULL,
  `DescAppoint` text CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci,
  `AppointmentDateStart` datetime NOT NULL,
  `AppointmentDateEnd` datetime NOT NULL,
  `Status` enum('Khởi tạo','Đang Thực hiện','Hoàn Thành','Hủy Hẹn') CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci NOT NULL DEFAULT 'Khởi tạo',
  PRIMARY KEY (`AppointmentID`),
  KEY `FK_Appointment_Properties` (`PropertyID`),
  KEY `FK_Appointment_UserID` (`AgentID`),
  KEY `FK_Appointment_OwnerID` (`OwnerID`),
  KEY `FK_Appointment_Customer` (`CusID`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_vietnamese_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`AppointmentID`, `PropertyID`, `AgentID`, `OwnerID`, `CusID`, `TitleAppoint`, `DescAppoint`, `AppointmentDateStart`, `AppointmentDateEnd`, `Status`) VALUES
(5, 'PR00002', 'UID00004', 'UID00007', 'UID00005', 'ABC', 'AC', '2025-05-24 21:36:21', '2025-05-24 21:36:21', 'Khởi tạo');

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
  `RentMonth` int DEFAULT NULL COMMENT 'Thuê sẽ tính đếm số lượng của chi tiết thanh toán',
  `Percentage` double NOT NULL COMMENT 'Phần trắm của tỉ lệ hoa hồng',
  `TypeCom` enum('Rent','Sale','','') CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci DEFAULT NULL,
  `StatusCommission` enum('Pending','Success','Cancel','') CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci NOT NULL DEFAULT 'Pending',
  `PaidDate` date DEFAULT NULL,
  PRIMARY KEY (`CommissionID`),
  KEY `FK_Commssion` (`TransactionID`),
  KEY `FK_Agent_Commision` (`AgentID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_vietnamese_ci;

--
-- Triggers `commission`
--
DROP TRIGGER IF EXISTS `trg_commission_before_insert`;
DELIMITER $$
CREATE TRIGGER `trg_commission_before_insert` BEFORE INSERT ON `commission` FOR EACH ROW BEGIN
    DECLARE v_agentID VARCHAR(255);
    DECLARE v_transactionType VARCHAR(10);
    DECLARE v_totalPrice DOUBLE;
    DECLARE v_rentMonth INT DEFAULT 0;

    -- Lấy thông tin từ bảng Transactions
    SELECT AgentID, TransactionType, TotalPrice
    INTO v_agentID, v_transactionType, v_totalPrice
    FROM Transactions
    WHERE TransactionID = NEW.TransactionID;

    -- Gán AgentID và TypeCom
    SET NEW.AgentID = v_agentID;
    SET NEW.TypeCom = v_transactionType;

    -- Nếu là Rent thì tính số tháng thanh toán
    IF v_transactionType = 'Rent' THEN
        SELECT COUNT(*) INTO v_rentMonth
        FROM Detail_transaction
        WHERE TransactionID = NEW.TransactionID;

        SET NEW.RentMonth = v_rentMonth;
        SET NEW.Amount = v_totalPrice * NEW.Percentage;
    ELSE
        SET NEW.RentMonth = 0;
        SET NEW.Amount = v_totalPrice * NEW.Percentage;
    END IF;

    -- Nếu trạng thái là Success thì gán ngày thanh toán
    IF NEW.StatusCommission = 'Success' THEN
        SET NEW.PaidDate = CURRENT_DATE;
    END IF;
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `trg_commission_before_update`;
DELIMITER $$
CREATE TRIGGER `trg_commission_before_update` BEFORE UPDATE ON `commission` FOR EACH ROW BEGIN
    -- Nếu trạng thái được chuyển thành 'Success' mà trước đó không phải
    IF NEW.StatusCommission = 'Success' AND OLD.StatusCommission <> 'Success' THEN
        SET NEW.PaidDate = CURRENT_DATE;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `contracts`
--

DROP TABLE IF EXISTS `contracts`;
CREATE TABLE IF NOT EXISTS `contracts` (
  `ContractID` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci NOT NULL,
  `TransactionID` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci NOT NULL,
  `SignedDate` datetime NOT NULL,
  `Details` text CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci,
  PRIMARY KEY (`ContractID`),
  KEY `FK_Contracts_Transactions` (`TransactionID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_vietnamese_ci;

-- --------------------------------------------------------

--
-- Table structure for table `danhmuc_pro`
--

DROP TABLE IF EXISTS `danhmuc_pro`;
CREATE TABLE IF NOT EXISTS `danhmuc_pro` (
  `Protype_ID` int NOT NULL AUTO_INCREMENT,
  `ten_pro` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci NOT NULL,
  PRIMARY KEY (`Protype_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_vietnamese_ci;

--
-- Dumping data for table `danhmuc_pro`
--

INSERT INTO `danhmuc_pro` (`Protype_ID`, `ten_pro`) VALUES
(1, 'Đất Nền'),
(2, 'Căn hộ chung cư'),
(3, 'Chung cư mini'),
(4, 'Nhà Riêng'),
(5, 'Nhà biệt thư, liền kề'),
(6, 'Nhà mặt phố'),
(7, 'Shophouse, nhà phố thương mại'),
(8, 'Bán đất'),
(9, 'Văn Phòng'),
(10, 'Kho, Nhà xưởng'),
(11, 'Khác');

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
  `Interior` enum('Cơ Bản','Đầy đủ') CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci DEFAULT NULL,
  `WaterPrice` enum('Thỏa thuận','Do chủ nhà quy định','Theo nhà cung cấp','') CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci DEFAULT NULL,
  `PowerPrice` enum('Thỏa thuận','Do chủ nhà quy định','Theo nhà cung cấp','') CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci DEFAULT NULL,
  `Utilities` enum('Thỏa thuận','Do chủ nhà quy định','') CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci DEFAULT NULL,
  PRIMARY KEY (`IdDetail`),
  UNIQUE KEY `PropertyID` (`PropertyID`),
  UNIQUE KEY `PropertyID_2` (`PropertyID`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_vietnamese_ci;

--
-- Dumping data for table `detail_pro`
--

INSERT INTO `detail_pro` (`IdDetail`, `PropertyID`, `Levelhouse`, `Floor`, `HouseLength`, `HouseWidth`, `TotalLength`, `TotalWidth`, `Bedroom`, `Balcony`, `Bath_WC`, `Road`, `legal`, `view`, `near`, `Interior`, `WaterPrice`, `PowerPrice`, `Utilities`) VALUES
(1, 'PR00001', NULL, 2, 12, 15, NULL, NULL, 4, 1, 3, NULL, 'Sổ Đỏ/ Sổ Hồng', 'Tây Bắc', 'Gần Chợ Bách Hóa Xanh, GS25', 'Cơ Bản', 'Thỏa thuận', 'Thỏa thuận', 'Thỏa thuận'),
(2, 'PR00002', 0, 12, 12, 15, NULL, NULL, 3, 1, 2, NULL, 'Sổ hồng / Hợp đồng chuyển nhượng', 'Bắc', NULL, 'Cơ Bản', 'Thỏa thuận', 'Thỏa thuận', 'Thỏa thuận'),
(3, 'PS00001', 3, NULL, 15, 18, 17, 20, 5, 0, 6, 12, 'Sổ hồng', 'Bắc', 'Bách hóa xanh, GS25, Chợ', '', NULL, NULL, NULL),
(4, 'PS00002', 3, NULL, 15, 18, 17, 20, 5, 0, 6, 12, 'Sổ hồng', 'Bắc', 'Bách hóa xanh, GS25, Chợ', '', NULL, NULL, NULL),
(5, 'PS00003', 3, NULL, 15, 18, 17, 20, 5, 0, 6, 12, 'Sổ hồng', 'Bắc', 'Bách hóa xanh, GS25, Chợ', '', 'Theo nhà cung cấp', 'Theo nhà cung cấp', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `detail_transaction`
--

DROP TABLE IF EXISTS `detail_transaction`;
CREATE TABLE IF NOT EXISTS `detail_transaction` (
  `TransactionID` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci NOT NULL,
  `Num_Pay` int NOT NULL,
  `Price` double NOT NULL,
  `DTran_Date` datetime NOT NULL,
  `DTran_Status` enum('Chờ đợi','Hoàn Thành','Hủy','') CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci NOT NULL DEFAULT 'Chờ đợi',
  PRIMARY KEY (`TransactionID`,`Num_Pay`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_vietnamese_ci;

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

    -- Tự động set ngày
    SET NEW.DTran_Date = NOW();

    -- Nếu chưa có Num_Pay từ người dùng
    IF NEW.Num_Pay IS NULL OR NEW.Num_Pay = 0 THEN
        SELECT IFNULL(MAX(Num_Pay), 0) + 1 INTO v_next_pay
        FROM detail_transaction
        WHERE TransactionID = NEW.TransactionID;

        SET NEW.Num_Pay = v_next_pay;
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
  `DocumentID` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci NOT NULL,
  `TransactionID` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci NOT NULL,
  `UploadedDate` datetime NOT NULL,
  `DocumentType` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci NOT NULL,
  `FilePath` blob NOT NULL,
  PRIMARY KEY (`DocumentID`),
  KEY `FK_Document_Transaction` (`TransactionID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_vietnamese_ci;

--
-- Triggers `documents`
--
DROP TRIGGER IF EXISTS `before_insert_document`;
DELIMITER $$
CREATE TRIGGER `before_insert_document` BEFORE INSERT ON `documents` FOR EACH ROW BEGIN
  DECLARE next_id INT;
  SET next_id = (SELECT IFNULL(MAX(CONVERT(SUBSTRING(DocumentID, 4), UNSIGNED INTEGER)), 0) + 1 FROM documents);
  SET NEW.DocumentID = CONCAT('DOC', LPAD(next_id, 6, '0'));
END
$$
DELIMITER ;

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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_vietnamese_ci;

-- --------------------------------------------------------

--
-- Table structure for table `profile_admin`
--

DROP TABLE IF EXISTS `profile_admin`;
CREATE TABLE IF NOT EXISTS `profile_admin` (
  `UserID` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci NOT NULL,
  `TenChucVu` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci NOT NULL DEFAULT 'Nhân Viên',
  PRIMARY KEY (`UserID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_vietnamese_ci;

--
-- Dumping data for table `profile_admin`
--

INSERT INTO `profile_admin` (`UserID`, `TenChucVu`) VALUES
('UID00001', 'Quản Trị Viên'),
('UID00002', 'Nhân Viên');

-- --------------------------------------------------------

--
-- Table structure for table `profile_agent`
--

DROP TABLE IF EXISTS `profile_agent`;
CREATE TABLE IF NOT EXISTS `profile_agent` (
  `UserID` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci NOT NULL,
  `Certificate` text CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci COMMENT 'Link Hình ảnh GG drive',
  `AreaAgent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci,
  `ContactAgent` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci DEFAULT NULL,
  `NumberCardAgent` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci DEFAULT NULL COMMENT 'Số thẻ vật lý của Môi giới',
  PRIMARY KEY (`UserID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_vietnamese_ci;

--
-- Dumping data for table `profile_agent`
--

INSERT INTO `profile_agent` (`UserID`, `Certificate`, `AreaAgent`, `ContactAgent`, `NumberCardAgent`) VALUES
('UID00003', NULL, NULL, NULL, NULL),
('UID00004', NULL, NULL, NULL, NULL);

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
('PR00002', 'UID00007', 'UID00004', '2025-05-22', NULL, '2025-05-24', 'active', 'Thành Phố Hồ Chí Minh', 'Quận Tân Phú', 'Phường Tân Thành', '49 Trần Hưng Đạo', 4, 10000000, 'Cho Thuê Nhà Cấp 2', 'Nhà Đẹp', 'Rent'),
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

-- --------------------------------------------------------

--
-- Table structure for table `propertyimage`
--

DROP TABLE IF EXISTS `propertyimage`;
CREATE TABLE IF NOT EXISTS `propertyimage` (
  `ImageID` int NOT NULL AUTO_INCREMENT,
  `PropertyID` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci NOT NULL,
  `ImagePath` blob NOT NULL,
  `Caption` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci DEFAULT NULL,
  `UploadedDate` datetime NOT NULL,
  PRIMARY KEY (`ImageID`),
  KEY `FK_Property_ImageProperty` (`PropertyID`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_vietnamese_ci;

--
-- Dumping data for table `propertyimage`
--

INSERT INTO `propertyimage` (`ImageID`, `PropertyID`, `ImagePath`, `Caption`, `UploadedDate`) VALUES
(1, 'PR00001', 0xffd8ffe000104a46494600010101006000600000fffe003b43524541544f523a2067642d6a7065672076312e3020287573696e6720494a47204a50454720763830292c207175616c697479203d2039350affdb0043000201010101010201010102020202020403020202020504040304060506060605060606070908060709070606080b08090a0a0a0a0a06080b0c0b0a0c090a0a0affdb004301020202020202050303050a0706070a0a0a0a0a0a0a0a0a0a0a0a0a0a0a0a0a0a0a0a0a0a0a0a0a0a0a0a0a0a0a0a0a0a0a0a0a0a0a0a0a0a0a0a0a0a0a0a0a0affc0001108011c023203011100021101031101ffc4001f0000010501010101010100000000000000000102030405060708090a0bffc400b5100002010303020403050504040000017d01020300041105122131410613516107227114328191a1082342b1c11552d1f02433627282090a161718191a25262728292a3435363738393a434445464748494a535455565758595a636465666768696a737475767778797a838485868788898a92939495969798999aa2a3a4a5a6a7a8a9aab2b3b4b5b6b7b8b9bac2c3c4c5c6c7c8c9cad2d3d4d5d6d7d8d9dae1e2e3e4e5e6e7e8e9eaf1f2f3f4f5f6f7f8f9faffc4001f0100030101010101010101010000000000000102030405060708090a0bffc400b51100020102040403040705040400010277000102031104052131061241510761711322328108144291a1b1c109233352f0156272d10a162434e125f11718191a262728292a35363738393a434445464748494a535455565758595a636465666768696a737475767778797a82838485868788898a92939495969798999aa2a3a4a5a6a7a8a9aab2b3b4b5b6b7b8b9bac2c3c4c5c6c7c8c9cad2d3d4d5d6d7d8d9dae2e3e4e5e6e7e8e9eaf2f3f4f5f6f7f8f9faffda000c03010002110311003f00faa3f68df820ff001321b7d6aeb5bd485b59603d959dc84c28dd961c7ca3e6196183f20c11cd7eaf9fd0c4495e0dd8fca783eb6169be5a895ce7fc1ff0cbc01e06b48cf84fc39690480625b9dbbe6773c9ccad9773d3924d7c156e6e6b48fd5a9c23157469eb7e3ff0968aeb6be20f13594331184827b8532938ec9f78fe558bd56a5f2a6ee792fed411f81fe2d7c23d7be19eb5a2788eead358d3e4b69cd969be4b046192c1ee1550600241e79c114924b61d91f3c7fc12eff6cbf1cf8a63d4bf648f115ed95cf887c16f343a76a1abea67ccd42c6395a3555f2e36595a2f9431dc095dbcf0d5d5149c518cb467d4bf1075df1069ba1c97fe35f88d67a65ae183269ba608c938e81ee1e60c7e8a3e95338f604b9b73f2e3fe0a34747f1ffc4ff0c41e02975ed535abe334267bb560b22b488b1888040aade6752a072509ea2ae14db43e44b5b9f76fc00fd94fc751785b40d3be366a705f5d7f65c20ded9c42212cbb46f8e575c3f983dc90fd41c8228fabcc8f6f13d93c6dfb12feceff0013fe1c5f7c3bf11f86a1b2b7d45523bbbab5bf78ae485911c84631301bb6609eb8ee0d691a4e3132ad59f2e8cfa23e06e9be09f86df0e74ef859e0a3a7e99a2e891791a6595a5e6e58e3eb8ced03afd6b9ab292899d39a9c753ae4d7ade27c47e294c67382d19c1fc4571f3334b2193f886ce49017f122b1572c3fd293ef7738ec68e691b2e5b07fc24168ea15b5e042fdd06e138fca9f3cbb932711ede2cd3ba49e2388b8271baf1339fce9fb49f727dc7a0f83c5f6eb80be27450bcaecbb030718ed8ed4fdad4ee1ece9f617fe12eb2dff00bcf1728c0c60de76f4eb584ead6e6f8987b3876126f1669b2c67fe2ab036e5891727bf07bd10a955bd58bd953ec8c2f19f8b3ede60f05e9fe2f7171aaee5b89a2bb3be0b318f39f39e0b2e23527bc99e429ad79e5dc7ece9f63762f16e9b146b6d178bfcb8e350a805eb00a0600039e3a0fca8e79772bd9d2ec16de2fd2ad8b2dbf8d1d43312f9d45b0491827af714734bb8b920b645a5f1a69a705fc56718e585f37b7bfb0fca8e697713843b07fc25be1f31887fe12a251482145f36010300fe5c52bb23963d843e31d093213c4a3a939f38924fae7d68bb34518db6237f1ae924ab49e259728bb5099db81e828e6909c636d87af8eb43d843f8918ee24b66e0f39393f99e69f34bb93cb1ec247e3af0ec5188a2f11b2a8e8ab34807f3a7ed2a770e54249e3cf0dba2c52f8909546dc8ad2c9853cf239e3a9fccd1ed2a7739eac7de1cdf10bc3c471e24627000093499c03c76a3da547d49a70f7b504f889e1b2184fe23b919ec5a56cf39ecbebcd2e79773a7921d8825f1ef841a37b61acc851c6193649f30f718e68e797717b2a7d8a32f88bc001b734a012739fb2b0cf18fee7a1fd693a935b30f674fb11eb9ae78175ad292d5ef644922b8fb44174887cc8e4c15dca590807079c8c10195832b107a69d7adfccc9f6145fd9460f883e2a47e17d1ae352bbf0acdaebdbc45d22d1610f7172072544526d0588c8d818e49e08e83a3eb15ff0099fde1ec28ff002a2acbe2dd73c6312d85fcc9e15b2750d29b5b633dfc87fb8095314073d588938e983cd3facd7fe662fabd0bfc28dbf0fea3f0bbc21a647a2f8574f36f146eee585b4864919ce59ddc82d248c79676249ee7d65d7ad25672652a54a3b22ec3e3af0d5ab17b57bb8cb03b8c56ccb9c924e7a75249fc4d64f5dcae487601e3ff000f30f2a3b8beea48510375ea7bd1a0ec858fc57a05d4ea1e2bc932dfbc2d09fba719ce7af614ac985915c6bfe172de62585cc32ecdbe65b21494f7ea0839cf3f5a5cb17d077688e6d76d2588c4daaeb8887a07225c9c63fe5a6ec70076a3921d83dabee675f27846fd4a6a5a7c979215dae6ff0045826561e870ab91d29f2c7b194ab6a501a3f808c420b8f845e1c9a31f75134182100fae0237f3ef4d24b62a9d48c9ea412f863c14b2ac9a578022b0917959b4dd4e7b42bd071e498c0e9e95328425ba37e7822cb699791e9e66b6bcf161da46d4b4f15cb74c3fe0378e14f7a764723ab1b95d752f11aa98ec75ff001e478e19750d034b9d01ff00b660311ff03fc69e84b719ea46be39f8ab685a38648af2307694bcf015cdbc8c3a7df8af993f12a6a94a56b5c718c79864bf16fe24e94544fe008a58dbef37937b1edc7bada4c075f6fc7b4bd4dec88cfed257560ea357f03dbc7e59f93cbf1141138fc2ebecfcfb1c7e141cd2e55366b697f1fecee63f3e3f86baf2a37224b3b9d32e549fa437cedfa54cf99ad0719d8b2bf19fc2f23186e7c23e29872385ff008446ee4cfe30c6c0fe66b3e5a9dc6e51622fc72f861631ad86a5a8c96001c22ea566d6f8e318cca57b76c55fef17521ca2f73474cf8a5e02d5a5169a56bd6179211958e0d46066c1f612e6ab9ebada4c51a34a4af636e0d61a60641e1fbc652002762f38e9fc549d4c4a5f1329e1e97643cead15cc9e67fc23d74ec08cb657208e9dea3dae2bbb0fabd2f2162d46e532b6fe19ba507aedf2f9fae39a5cf55ee52a14974435b5573f34fe19bc6c7431a0fe99ace4ea4b664cd4299f2dfed13adeb5a97c71bcb4d574ff00b3dbadbc09671b727cb0a4827d0e59b8f7f7af7b2dba81e0e6126e4738617808b8b462922b8dae38c11cd7b94b957c478d5a377746c5e243e2cd264b39f488268e78bcbbab79610cae3a1dc08c107ad7b786a542a53f851c352bd7a5a7333c475af0f689fb2f7c5db6f1a0d3cdc786eead9ada7b942649ad4b3233aaeee14ee195e30caa4649a71c2c69d6bb5a15f5aa93a56b977f6bfd5fc13a87c32d3bc41a4eb6655f124b0c62c6cc79cb7d6c097f3813ca4909fbb27decc850e4138f271b86a337f0a3a72ea95154f7d9f989fb517893c4fa57c42ff847356d0619869c4cb697b3a9305f5ab83855ca8f919b9604f05080015e7c8f63aec7bfcea0af71fe3ffda4ecb5ef0ac3a3ea1e05173613245e44d1ea26330c883870429c3a37381c7038c1a89d2b0e9e2d3d0d2f875fb467c5af8936561f0c7c1bf0a6ebc61ac5c660feccb3f36e2e6f460a8221442cc36862d807807d39c9ca31dd1b294aa688e7bc37f19fe2ff00c28d66fbe116a1f0e2eadb558f5254b1f0e6b31cf14f6cc4f16c232a1d5d891b47b63a9e5fb5a5d8afabc8fd0cfd8f3fe091be39f11f86ae3e387ed3de37bcf875ab78a42c363e1dd06cc5d25b2b709757a8e46589c2f9513ab01825c3656bcead36ea3b6c7a9429c55249a3dbbe30ff00c1ba63c41e0bb6f167c2df8ff7b6be38d33f7b63acdfe9b9b59f83f2c891fef6146cf2bfbfc751530ad283bb2313494a0ac796fc5dff00825afc6fd19f43f823f10fc276be2683c4b63148fa9e93031b2b3b950125cc9f3792509deaedb5ca9e9925468b1099c2e8491ce7fc13f7f64cd2ff006c8bbf8d779e2bd4ac1a1baf1d797359de5a068ae51e5ba62246d8e62521792149e7a138aedc5c9fd4d1c185e573badcedac7fe097ff00b76fc08d73c4ba4ea52f85fe25fc28d5269af2ef4fd2b5848ef7420dc9f222ba088f0f5fdd21c8553b141c678a9b7c88f4ea3f66cf9f2fff00669fd9f21be9a2ff0085a9a545b6561e535f4794c13f29cb678e9cf35a5d98fd611fae2ba5eaaadb86b3228e8d1c36f1aab0ee0fca4e3f1afd5b130855a4e2d1f93508d4c3d755212d8f3cf883f0a743d06e9357b5d15aeece793694b890c9e5127257e6c80bcf0001d6bf33ce32fa984afccb54f53f57c833aa78ba3c93d24b4329347d0f47cc167656965137df11c6173f5239af9ef6b7d8faa71716ae79cfc69f1d5bd9690da37853c39ab6bd7055c226936dbd0762bbc11d7d0e7dc55466df42252513f38bf679fd973e28fc63ff00828a6b1e20f00782351f07597833c40b79ab476762ff00e83218114dbaaa281fbc20334781f7d81f5af469c1b8239dd4a77d59faafe0cf843676f6110f1359ea7793f1b9e6d1ae724fae37fe956a95cc6ad684568cb9aa7ecd9f0475df8b5a07c6af10f80e69fc45e17b6921d0ae9b4cba45b41270ee23126c2e471b981c0e9cf35bc2167639258b5b5cf463158dd5bbda4b033c2e30626d1e6031f9d77fb29f632e78ff003156055b1bc8e196099950feeee64b691037fb2d9cfcde9ebf515cf5a2e2f5437513564ee47e2fd46c0c905b6b463f315371892de5d9b0e30418e407ae7b9e95e5625fb8d23a287bbb98e26f09c9c8b48cfd12f3ff008f8af3b5ec74f321f1d9f82f7095ac23e792441719ff00d28a2cc9f68ae4cabe0e8ffd45975ebfe8b39fe7734599329a7d4992ebc288a00b2e40e07d966ffe49a126116b996a48973e1790e26d3b23b6eb494ffedd1abe45dceabc3b8f59bc23c62c48f6162f8fd6e297b35dc8738a64d3ea7e0fd3ed66d42f922b682085a59ae5ad4af96aa324e4cfc7031f8d270e5d46a716cc5f0437866e567f196b9a03a5deae5254b7165c5b5b283e420dd7190d8632303d1e4700000522bddee74aba87865719d336af6ff471c7fe46a76462e4ae29bdf0e4a7315aaa81d77403ff008e55c69f36cc6a6913477fa3c601fb21c01fc30a1fd371aaf62fb84aa2489135bd033892ca53e99b78d7f91147b25dccbdb226fed8d036e174d90f1c13127ff5c552c3546ae8978ca51766116b5a642d9874ec1239db041ffc453585abd507d6e9cf6241e23b3c7fc8358fbf9107ff00114feab503eb0bb0e3e22d3474b227fedde0fe91d1f54a867f5d876157c45664652d64519e820847fed2a7f54a81f5853d5207f10599520c32fe314407e9151f5498a58954d733447fdbd63b82880f3eaa9ffc6a8faa4ccffb469f6253e23411ed160c540ebe547c8faf954beab337589e657486c7e248149db6457f04e7f28e87839b0fac3ec49ff0938dbc58123fbdb17ff8d55c70f3883c4b4b6226f125c6f492267428d905760ffda62afd94ccbebaff009491bc50f21df2ef2c7ae6651fc92b48e1aa495c3ebaff00948478a6327f74833fec9cff00ecb55f5592f883ebaff9470f125d3731c44fd78fe947d5e3fcc1f5d7fca4e35d919403031e3901d81fd297b1a7fcc1f5d7fca32e3c5dab6936cf7ba3699e75c438921b79646fde38e40e7e87db246697b1874909e365d202d9f8916f614d42daddde0923578dcc8f960555864718c86fcf23b51ec63fcc4cb1b3b6b0246f11ce7eed863fe04ffe34fd8d3fe631faec44ff008486eb3ff1e5fabff8d2f634ff0098cdd59cdde2b41e35eb81c8808fc1ff00a9a7ec69ff003174ea554fe11ebe229c0c34121ff759c7f2347b1a7fcc6dedaa7f296adb59bb7b42a2c5f93d7ce93fa1a3d8d3fe638dd5a9d863dfde39cfd9a41ff6d643fccd1ec69ff31d146b5451f848defefd81536e48f46de7fad1ec69ff0031bac44d3bf28437778324db05f65de33f9351ec69ff00317f5a9ff216d35abb587c8788f97b70cbf310477e09228f634ff98e7954a92937ca665f695e19d59ffd3fc25a6dd60e479fa70723f33551a5493d64253a8ba1564f04780e55f2e5f867a0b2765fec48c1fcc557250fe61fb5a9d87c7e12f0ddb9dd65e13fb3a9e8b6d7373063f18e553f867153ece8ff00393cf53b10df7817c37ab1ddaa6957731c6009756bb6007fc0a526a953a1d641cf557429dafc23f0ee9afbf406bcb27ec6310c98f5ff005b13552861d7da0752b2e85f5f09788a2c2c5e30d4028ec6d6d47fe831034ed87ee2f6b5bb16134af14db264788a770a380f6a873f5c62a5ac3f70f6b57b0e6b5f1bcc8c21d71118fdd77d1f791ff8f8ace71a2d68ccaa4eac9ad0f9fbf6a8f855f12749f10d97c4e8fc55a5ba5eba59ce5f462aeb2a8247cbe7f21941cf0305073c8ae8c1d48c5d8e4c5424d6a70cba57c459a05997c47a4051cb2ff6348b9fc44f5eba9731e728b347459fc6fa7c8258c6992af461b654cfe01988aeac3e265465639ebd28d445bd7b47f147883459742d4bc39e1abeb5be84c7318f5fbb87e523b16b56e7b8f4238af7e33f6d4ae8f362a2aa723d0f03f0b7c3997e1078ee3bcf89ff000f2db50d16232c563757fae23d9452c8460ef920057201182abc927ad71d5a0db3ad4e30f87522fdbc3f640f873f1cfe0e47aae9bf0ea1f0d6a3a3159747f1058ea76725b949658e368cc4658c32bfcb82181dc17dc1e19d1513be9394d6a7c93fb307fc12c7e2a7ed2daeeafa17846e75f4d1346bf367e21d4753f070b78ed2e907ef22883dd7ef2e10e4145cae33b987047898fc62c3492e5bdcf570f8572575a9fa6dff0004ecfd897e167ec03e14bbbdb4f84fe2fd6bc71a879d6daa78c1bc3259fc81c18208d64731459fbd8cb39c06242a05f16a62dd47648f62961e34e1ccfa1f4e7c3bd73e0afc73d4f4ad1fe3dfc3191fe21f86632f6faa4de1399e4b467ddb1edee25877a1641d9801b48c74359de46aa49f433ff680d77e18787746b9b6d1bc71a8096124e2f7c3f731c6a58018790a85076b0e838eb59bab67668e88aba13c39ff000513f84fe04d1f4dd03c41e34d2ef751b4b2116fb65489270704afcaf83d0753eb9acea4f9d590f95187e2eff8299f82eebc4f6ba2f88be1747a7787ee244337899ae96589475ff965210ac3070c5bbfb56694afb8a508d99f227fc1137c59a6f8587c61b2b82d74d2f8b12e1c5acd1aec5125e2e0f98e879c678071f967dec5cffd8d23e7f03846aa6e7d71e37fdafb40f0d5ddd782fc3725e1d54d9b5c0d0ecd92e6ee775c18516388b08d98e00799d305876c9ae0855e582d0f6311415d23f3875afda4bfe0e119358bb7b2fd92ae7c96b990c3e77c37b099f6ee38dce2221ce3ab0273d726abdbaec717d50fd5fda8bce00afd6e0f99ea7e4656bd8a19616b6689648d87ef226e8c0f19fa8ae4cc70b0c4d2749adcd70f5eae0f10abc5e8ba1f3f7c56f03fc429be33c1f0d347f0d5f0d12ef4c3a8cde28000b686357da6d8739331e0edc1e327a673f9a62f2e782ace16d19fab6579cff68e1b99ee8f26fdb7fe3e78bbf656f0245a27c28f132cbe269668cda5bde5a5b3c71424642b2f94497707200200c64e6a29d0f23d78394cf99fc4dff056dfda77e10e9905deafe1ed321bcbd6325e476f65671b0651f316fdc8278eedcd764694d2b24454961e9fc68e79ff00e0bdff001aa31f68b8b35424f52f698c77e7c9c0ad234aabfb2cc56232fa8ec9a3b3f02ffc1643f69ef897afdb695e07f02cfa92dd30dd33c76aab128001727cafba1b233f875a392717b1d50a581a8b43d5a5fdbbff006c886ca79dbc3fa78f2626908516f9c019c60c03dbf3ad57d6e5b325e1306ba1e2d37fc167ff006aff0017ea6fe16d2b4ab2d0cc7283a86a7a9e936f2c3656b9f9e6f2c28323000951c6481cd6ce95674af35a9e6e2551a55b960cfb53fe095ffb4a7c13fdba7e084f2c3e26d52e3c67e17bd6b2f145a6ad7a1e6b9507f717ca8146125058e3eea38741c0427e6f1b3a94d37637a3153958fa961f821e10624149f83c857c7f4af27eb8757d5d772cbfc19f01c09916777bc0e0997a9fce9fd6d91f56221f0bfc0f11c496b31ff7e6ff00eb1ad218a6c3eac46df0e7c09b8a2d95c93d0627383fa8fe556f11a0d61d0abf0e7c2101dc9a7cab9ffa6a7fa3565ed87ec1f7248fc01e0b642d25b4c307e622e1b8ff00c7be95bc2a3711ac3df739cf127817c2be32f1343f0f2d2dcfd9e0f2ef35e68ee1fe58c3e6180b678f3594b11da38c83f7d4d5733ea3787b2dcead7c15e13976cada5b39651b8c8ef9cf420f3ed45d13ec5964f833c3bb7e6d3548f43237f8d67cd21fd590d3e0bf0cf6d2c0ff0076571fc8d690ad282d83eaebb8eff843fc36063fb2d7fefb6cff003aa7899356b03c326b7163f08f8750e574b43fef331fe66b3f68c5f5444a3c31a015c1d262e7ae4552c56263a4760fa9e1faad423f09786b3ff20683a7f7693c4e2e5d49783a3f6341e3c27e1b1d34583fef8a3db62fb93f53407c35a0b70da25b11ef08ff000a3db62bb9a7b0c2ff0028f8fc39a022e06876839ff9f75ff0a3db62bb89d0a7f610f1a0e86a72ba3db0fa40bfe147b6c5772654236f796838689a467234cb61f5817fc28f6d8bee65ec30dfca29d134bef616df4f2571fca97b7c4ff31aaa0ada0d7d134bfe1d36d3f0b65ff0a3dbe29fda1fb013fb1b4f1d2c2df8ec215ff0a7edb17dc3d80834eb3fe1b1847bf923fc28f6d8bee69f54a62ff66db1eb670ffdf95ff0ad615b15cbb8fea901eda5db27ccb65083ea205ff0a73c462146f261f55a630d8db9386b48c9f6887f8561f5c987d5698e3a50906c16aa33ff004c87f8567eda6c5f54a6317498a170e618c9539194071c63fad3588a91d8a5429c0ced0f4e834f379a2bc0a561baf32dc95e7ca932f83eb876703fd914feb355e82952a4a2ee8d0feceb5ff9e51ffdf2297b5aa72f2e17f93f00fecfb51ff2ca3ffbe452f6b50e9852a4e3a2096d230bf2a2673d9053f6d57b94e9416c867d97fe99a7fdf028f6d54cdc69a25b7b68859b9f2406c71f2d1ed2a9cfece3d8812394c484019239f928752a9b53a70b6c020933c81ff7cd2f6b50bf670ec29b4ddd547fdf34fdb550e580f167084fba9bb1e947b5aac396031ad377551f82e29395690725310590cf4a2d5bb87253362c34082583ed17377b507f770695ab770e5810dd5be9ce775a26553ef164e4fb516abdc392055fb3c60eedabf4029af6b1d6e2708583ca8bfb83f2a7ed2a91ece1d889930c70bdfd29f2d47adc3d9c3b0a8e378498baab0203a8fba7f3e2ae2a696e67521156d0f98bf69ff89abe31f1e2785f47bb66b1d195e2760cc166b8cfef18e7d368403d51bb115eb60a94ad7678f8da9ad9238ed2272d0f94413b8600af5e137d4e0e424bab3313160e197fba79c57426ad739a69fb568d5f0f5c413a1d39a05059b740c1718c7057dfa6715eee02b45e92763ccc561f9e5744fafe93a06a3a14fa4f89618e5b4bc0f14f6ceb8599594e7240eb81c1ec6bd29c3995d1c9fc2d19f382fc3af1ae95e2fd3ff658f13eb910f0ddf6a50eaba25c5c1c6e8a3977aa0dc1b1b847b76f199318c6327c9ae9abdcfa0a56e4562f7ed21f16bc5be01f887e2bb387c63abe99e14d5e232f88a1f0daabdc4772966db268f793e59130433aa80d2a796c080b293f2d99414e68fa0caaaca175247d6369e32f89fac7c2cf02378bed618f55d4bc3b6cfe20fb2df64fdbc469b8065e3703c95070497e4ed5dde6fb1b6a76e324fec96fe34d878a759d4af6fbe1b68f61a7ea779a7a593eb535c05753195764620fca8e0797bb8601b0a451cb1270bef3d4eb7c37a078cbc4be10d4e5f17f8aa7b069eded6db42f0ec9731c31db7930e2555732309e499da4756201c796b82412786ae9519e9724ba2380f867a9e85e2ff115efc3cf1840906ad6dba4d2351d987ba8548dd0beefbcc3702a7d01e4d412d347a1f87668bc17aaae97ae6956577a55c60c9e744aeadff5d15bafaf71cd35b92f63e68ff8238f82be136aff0011ff006918b5a855ae23f89f7315baff0067453c2910babe084ab2f23d476c02066bd7c63ff634797834f9ce87f684fd867c0bf083e20ffc2f5f85be15d2aff467bfdfac5b5c6991cb1c32b13fbcf29d32226ddb4c7d011c75e3ce4d7223df8d28cd6a7456fe23fd9b5ade36baf821a12485079891f86ad8aab63900ece4668ba2fead13dd64cbae0035fb24373f9e88658cc486e5d80541cfad4e2ebc28c3535a1879e26aa82d8e23e307c5587e1d7856ebc4dab4d134b0a62c6d247004928fbabcff0008ea4fa7e15f018daf2af88d363f51c9b2e586a493dcfcc6f8bdf10eff00e24f8c353f8b1e25bb9fecd14d294bbf2771b99492728bd0a9246131e8052a74cfa6a5ee1f1c78f25d47c79e35b86d515849bb174a5b291a0e56dc7fc07963ee077af630584ad3926d687c871467146853714f526b3f86fa6ea71adbb6948cd8fbf12e58e4800003be71c77c62bdcc4e1e9d0a29c773f3fcaf158dc6633dcdba9f687ecbdf00ad7e16f8320b14d2da2b9be58e4bada58b4319cec8c60fcc305bb3773c6e39f9ac4abc8fda70d858d3c3271dc9bf69cf8a9a67817481e1cd22cc5c6a97d1342b6c242abf306dadf29040ea4103858c72315be1285d98637154a8506a5f11f2eea3e0ff000d476b73a16bca2692f101bb792ede333f24b10515d8072483c63e4efc9adb153f652e43e6f0fed7109d49f73dbffe09d1a7f88bf641f8b1a67c64f862e776adfe83a9e95737d348d2e94245674c14882ef28caaed1b9fdc90a41193e063f0aead16cf570756f5940fdb1f0ef8d7c37e2af0fd978b3c357cb3d86a16eb3c1227a1ea0e3b839073ce41af89af8674ea1ec06a1e2181db293608f553fe14d61db43e748c1bdf13c3148c25b8ebd3e53fe15a429ba61ce8aa7c59081b85c8e3a1240aa6aeac1ce8583c5f148487bd8ce1720095727f5acfd9873a23d5fc75736f6f1d868f2c26fa7606077019605c732c9e8067030412780416dcbd34d5a160e745bf0947a3e81a7fd82c64ccb2bb4d7f7934c1a5bd9d8fcd33927ef31e70385185002a801cf60e746d26a36c07cd7718fa383fd6b20e7248f5cb076f2cdcafe75d0839c99351b061ff001f718ff818a039c1f51d3d14b1bc8f8f5900feb40738d4d6f4919dfa8c0be999d7fc680e71dfdb9a37fd056dbfeffaff008d02e71575ed141c9d52dffeff00aff8d03e711bc4de1f43b5f59b607d0cebfe34073827897c3d236d1addaffdff005a042bf88f40438fedab53f498501cd613fe125f0fff00d066dbfeff002ff8d01cd7157c47a037fcc66dbfefe8a006b78abc32a4a9d7edb23b6f3fe1584a1a873d841e2af0e1fbbaddb9fa49554e361f3887c5de1853b5b5cb707d371ff0ad439c1bc5de1a4196d622fc013fd280e61bff00098f864f4d590fd11bfc280e711bc75e1a61b46aaa7fed9bff00854cb61735c997c55e1dd9ba3d4558fa84231edc8ae3a805a5f16e80500fed15071d7069ad87ce324f16e8770424976839c060a7f335d14b61735cc49bc53a0c3ae3dd7db86d7b72920d8727691b4fe196fceb50d87b78ebc30bd6fdbf0858ff004a039c4ff84f7c2ddf516ffbf2dfe158cb70e701e3cf0b9ff97f7ffc076ff0a92a32b8a3c73e1b232975211ea206a0e5adf112daf8db40b8495639a5cc6a739848ae82d6c558bc77a0ac614b4bff007c63f99acea6e691959120f1c68446434bff007eeb31b95d01f1d78797fd64d2afa7ee4d0663478fbc345b6acb2924e07eebbd6f1f84079f1ae8ff00c2b29fc00feb4c04ff0084e344076b9901f4da3fc6802e3f8f34c8edfc983cc23772303fc6802b4be39d217e4db2293ce081fe340119f1be8e064090ff00c068013fe138d30fddb798fd168014f8d74d0bbbecf2fe43fc68032fc59e3ebe87c397f73e13b1924d4a3b194d8c4ea0abcb801463d77631ef81deaa1b99d4f84f8aa1d60bdb7da1e4924766cbbb9e4b13924faf27af7af730bf0a3c2c4fc46f786b5306457901c01d2bb8c0d895d589c7739a14ecec63521cda925a19229d0c59011b23eb5d0aa58e3a94ee8a7f19fe38f803e13787e0f10f8bedb5348e598ac66cf4e32ee600161bc1c03d5b04f4dc4700d7b785cc572f29c15706e67977c47fda07e0afc5df021fb0c7abdd5d699299f47bc8e3f2dec2e38dbff2d0348090372a83b76efe00c89c6544e373d3c12b348f23f881e30d7b5086fbc51aff008ae097509637171034e44d75200ceff221db81f29c0e303fba4ad7cb621de47d561fe02cff00c138bfe0a07e1cfd9dfc63a7fecd3f123c3f18f0a78a3c533dde8be23b8d41b6e8af3a285842b8ded18970725b849198e769c7156bfb276dce94949d99fa2be14fda33f67ad1fc43e3ed33e246bf61a5e99e1078cea7adea6c8b6473b03224a7218c46486338e77caa0024d7917c40e10842a1f374bf03bc17a3db78a34df859e1cf11fc44f13f88ae2f5f4af125c69b7d3c76b1cd765ed26b7d45e6fb3416d0db6d564501d9cb06c05c51ef75dcf6d34e92b1ef5fb437c3f8efedf49f89bf0ebc476536aab0c5f6c7d32f1647b5d4238c1f357fbe24c3e71d5b2472f801e7d5dcdff879e3bd3be347c3b935791238353b76f2358b641f2adc2e01383c61fa8180074ed922dcc5ec7c77ff0004cef881a17c2bf8edfb505df8b75e5d2f48d2fc69777b7d73203848d2f2f94f4f715eae33fdcd1c386f88fbe7c1be32f84bf19fc00b73e0ef1043ac69f75a546f7762c482048b8258e305482d8209209cd795f651eab763caaeff0065ff0088105dcb0d92e9b2429232c324b3619941e0918e091d682bdbf99d9c5f1f3f67b956009f1b3c360cf218d4beb30e030ebbb91b7ea4015fa8cb36a71d99f8c2c8f1aa5668e47c4bfb61fecc516a3168507c77f0d173712c6ec9a92b80630acc4b28c28c1c2b3615bb6735e166799cabd4493d2c7d7e4b90c68c39e6b5b9f16fed51fb54f83bf683f1a59f87bc3bf102c6db456670f32ca418a05c8239030ce46e3c839c006bcaa75a1cc7da52c2ad19f367c71f19f887c613c1e0df8471da3e991db94d32fa4b830c2ee002652cc70b8070370c92dc74e7d4a3570cf734c7d374f0f7a7f11e51e10f00dd5cdfe99e1eb0bcd3fccbc7746d42ff005445863c9e6499c64a72589ef82b807bfd252cdf034a828763f1bccb25ccb30c6ca55354d9eb9fb3a784fe1f784fe242df7c4ef885a39b6b1998d95c348f1c170ea461c875ca2e49c170011cf0466bcec6e6f42a24a9b3eeb86f87e197d372e5d59f484df1d7e0b1d3c5d587c43d39de5dc2dd242e8864eed202bb630770553c0da808ef9f2258aa737767d6d3a5386c7cdfe32bbb3d6fc537ff00103c47adda4fe4a49168b6b038492ea42fb9e6313b6538c0419055131839c1f470f8da10a776f53e5336cbf1589c727d0c9f056910dfeb505feb3e3e8adada6df3de4ba7ea9a9464c31e09488ac11a1931f2a072887d4e091e757c62ad539a4cf41602b538a8ad8f79fd9e757f0378cbc7567e1ed1fc49697fab6bb7bf66d37ecf04cd385518cbb38f9563589550b9dc44687390c4e15f18fd9348ba5805467ed3a9fa1de1dd074df09787edbc37a5fcb6f67188e201b39ee5b3d09624b1c7735f37895ceee755d85c5da6fda10673dd6b24ac845578ada524b44a7d7229495c89b6b6206b3b2f3306d63e4f5c52e433e69106b171a5e8769f6a6d3fce9ddb65a5bc670d2ca7a007b700927b0058e429057ba4f3cb6b96746d2ee6c62c6a179e7dc48c1ae644e1246c7403fb83a007b75ab5b1b42fcba9b16f681872011ee050d265589bc90bc0e3d8014b92216011f390cdf9d50587ac2edfc67f4a02c396d249182090f340589574c923fbf239cffb6680b00b2e705dfaff007cff008d27721f35f463ff00b38370aeff00f7d9ff001a4db5b8bdfee034cf524fb96ffebd2e741ef0ff00ecc41cedfceb5e4932bdb21469303f2d1aff00df228e4909d54c3fb1adbfe792ff00df029f2484aac50e4d1cff00cbb80a3be052e4915ed9036909b4875c9ee734b9199baaae363d1e35ce370fa37f853e4635513241a342c325093ee69a8487ed2228d0edd4e4c40ffbc73fccd57b2911ed507f645b0ff9623f0a7ec5d83daa2ee93a15ab49233c00ede16b194594aaea5f4d1ada204247b73c900d64e927b97ed90efecd888dbb6b3b4519734df5241a746f1b2b47c631f855464a3b17194cccf14e8524da78d46ca1669a2c88a345e64619254fb100ae7fdb1e956a7ccec3739d8a5058db4b124d651031ba06126dc139e7a7a608c1fad6dc9233e6992ae8bbd7ccfb3a1f729cd2e4875139541a74d71c943f953e4a6caa752717a8a34b2c3241fc051ece98e52849ea5bd374a2933284c8914ee0573dab5f668cfdb488df4848d8a2420007b2d673a704f50f69396c4eba12150c624e47f7454f2d21a954b8aba143fc70afe0829f2d22b9e64aba1db84c888702b44a95b60e6980d3215fbaa47d0d5a8425b132ab521d2e3d34c80804c4a4fab2834dd2891f589ff292a696243b64b18d47a84151cb4d15ed2a0f1a3db8e0db21ff00800ad614694d5ef613a9541f4a8b6f310c7d2afeaf4bf985ed2b0d4d3e241854a3ead4bb87b4ac1fd9704870621cd72c94149a2bda5403a128ff00536c0faf3f87f522b394947614a737a33e5cfda6be1c5b7817e26892c2ce68acb5ab5fb66dd830672c44817d810addbfd61ebdbd5c1d56e27998a859dd1c86971f90eaaa7033eb5dded26727bc7470468e067d297336c1a762e2232366b7e67ca72545226934fd0b55f26d7c49a159ea36de686fb3df5a24c88d8c6f0ae08c8048cfb9f5aeac3c6117709691d076a5f0dbc03776e96d7be05d1ee2ce23fb98d74c8808d58e40501781ebf5fc6bde74e8d6a373c9a588c4d3af63e02f8f9a25c786fc5faff83f54b36bb116a33daa40d21c22827cb6008e0aa90320b1e473d15be66b518aa8d1f7383a8e54ae780f8f34bd374e30f8575ab496d623a823add4b70710ba382b2a8da40c118218f2382704d73d4a70506ceb8c9dcfaa3c1b71e0afda2fc19e24d0fc7de3c125878a74e6b37d32c9310a6ab74b87d5909e7ca2228e544c9fdeb3a9e5185705cd1c22ddcddfd87bfe0a09e32fd93bc523f62ffdb4ecb5ad76c3c2f03db78667d0a78e38f51b29706295e1948370531855578f6ab30659366538717095af0dce88e22a461cab63ee7f0a7c7cf07fc74b4847853455b2d3957fd115a7f9873c79db79523b6c2db79f6ac229a87bdb93cee4f521862baf85df1113c57042cb657a822d5ad970124439db360704863b43608ed81d6813d8f987f603f0c7857c79fb667ed35e15f14590b8b0b9f18cbe7dbf9cc81f1a95c3e32a41ea4f7af5aa2f6985499e350ab35267deff000b7c33e05f873a64de06f0578722b3482d9665895d9c88f2547ccc49c7ca703f1ea6b296162e2ac7a91a929c753a617fe60f3096f9b9e6a7eaa3e547ca9a7ffc13c7e09cd6be4bf84ae393cee08c3f231d7932c75796ccd2ad2a4a5748b76fff0004ddfd9d2181b7f84e4425b2c64b64519faed007e34e1899dbde66b4e5151f7502ff00c13abe07c7262e3e19a9847565b55dc476232983dbd6afeb468aacd32687fe09e5fb3bc672bf0bddbfdf8f6ffe8083f5a7f5c9ad8c652ab39ebb17f4cfd843f66fb37287e17323f456112b60fae0ae4d672c45597525515cd7b175bf615f810a4c90fc3a9a43d82d885c7e486a69d6ab16db6747b5941583fe18c3e0a43812fc309b703ce6323f4082b758a98beb12b0eb9fd8afe075f46a93fc294942bee0ad6c7838ebf76b0ab8baeea7baf4142b5f592d4bb6bfb17fc1b997cbb6f8445c01d50328fcb6d747d75376b854ab392ba389f1358fc06fd9df5ebdd5b44f0ce9fa7ad9c06de5bbb89ddc97fe248d4720e3391d783c56eaa4e470baf372b33c17e34ffc1517fb0206d2be1ff84616d9f72f6750ea0760b0a96320239c83dfbe3145afb8f9e2794f86bfe0a29fb5ff00c40d6047e16b0d252d8b9db2c968131e8814db8604703b66b17b967af7c3efda43e3dcb7110f88fa9c6ae72a5b4f548fcb6eeaf19462c31fee9fee86240a71715b99d43bef89ff0015be20fc3ff87da9789b4ad5aeaf6fa1b076d220fdd3c53dc942624dc22ced2c7938c85466c106bb70b4215abc632d9b57f43293b2b9f334ff00b67fed8175e1a6f105e5de82de25d2e13f644fecd6fecf85a5210873e613216191e67cb83bd42b82587d7ff60e4ffcc79152ae2155e6b687daff00b3c5afc56d7be1ee8daefc62d75a3d565d34dcea76567108e1b6908202053b8e77123a9cf94feb5f279a6168e1b192a749de2ac7b142b2a94d33a6f865f112f6ef58b8f0febc16578642b16f40be6ae4ed718e318c579974742d4f5cd32db41bd4590e92993d723ffaf4c7cacb96fa3685e6e46911f1edff00d7a9e7427a139d1b443f774b8d7ea99feb473c449a7b0e8f45d1d583ff0066c47eb1ff00f5e8e788c94697a49e9630a7fbb0f5fd68e788f958f1a569b8e34c8187f78a819fd0d66fdab775b14907f64696dc3e916f8fa0ff00e26972d57b8590e5d2b4c51b574d840f40b4bd9cd0590f1a6e999f9aca161e8f1e453e7aa839298e1a5e927a69769ff7e68f69547c94c55d334956c8d2a0fc620452f695439299247a7696f9ff00428131fddb75e68f69545ece98ff00eccd300cf9117fe032d3e7aa4fb380dfecfd2bfe7845ff0080cb439d51aa74c3ec7a60f9059467dfc95a154aa3f674c1b4cd300e2d207f66b75abf6b509f654c69d2ec1b91691afb244a053fac4d2b07b2a65cb2b3b236f886c638ce7ef28e6b0956a8354a9dc7369d1b1f9a343fef2d47b5a83f674c134f556cb2a11e816a1b932790735bdb20cb5b8a2f21a5629ea70096cf6c0369f338e7a538b9732b0da563374ed3841036140533c9b00edceec7b0019401ed5d77ac4f29605b28391fceb194a69ea52846da8f923122ed3eb554e526c3920f7236b750338fcab6d47ece912c102c538de319e00ac3db5617b2a435ec8895815e41e7149d49cb70e4a6b619f675ce306973481c622fd997fc9a39a44f28d36793d3f5ada3ced09c6c27d9d63e5bbfbd527523b12a567a2b8600385a7ed2b0f9e4fec8e28c8096fa54fbe438588994e7a527094ba8e318b4312db6beec7eb4bd93ee572447f940f2d47b27dc39220d0ae38eb50f9d32390922963b389af2695235850b3cb21c2a2f724fa0eb4e319cc89250dcf8d3e337c555f8bbf12ee7c41668e34eb456b6d23cc6e902bb6188fef3925b3e85476af5f0709c16a79f899424f430e28d5b6952060f3935e85d1cb646ad8dec7b844a791551d75264e28d78e5490fca6ba62d3d0c5c532d24a88b8634ef25b19469b52d4bfa2dcc4f70b64a096043c631e8738faff9e2bd4c1622cb96470e2a8da5cc8f9ebf6b3fd9a7c59e26f1bde78f3c196725f196e1642d05bc724f01f2c2f0649546d25413d082c4640008ac4e0aaca4a51d8f530198e1e9c3964cf9ff00e3a7eca3f153e18784f4bf1e5cf80ef5e34b249b5af923952dae239311cd36d91c2e570cc46e53b705f82079d5f0b555367ab4f1d879c9599e41f0cfc79ff083a5e5edad96a975a979b0c6ba75c4e192e62988dd16e4f95b7ecdca58828e8b8c2b3ab7912a538ee77fb585ae7bbfc73f82b63fb5e7c1bb7d43c2866b4f1c787035c7862fe456f3a5507e685f70ce0aaed647f9919493921b2e8d2bb6e5b11f58a6637ec07fb586afe1ad72e3c2be338bec5aa69d72d67a86973a36fb1b8562195c609009079e7a0073d6b8f13879a95d6c5c2b41b3f40745fda63e1578bbe1bdfddfc43d634fb2b3d3e348eeeeaeb501144a7381873f709257a0cb1006324d72fb39f62e5523667cf9fb0978b3c27e0bfdaebf68bd6352b9b4b159fc56122fb54fb4ee1732966dce00cb0cbf07bb7602bd57097b151ea7874671e635fc47ff00054ef875f09ff69ef1ceb1e21f195acba658f872c74cb0b60d24f17db219ae657f962c8dd89a2c6700e31bb8a6e355a4a27a94eac523caae3fe0bdbf108dc39b2d3ee0c3bcf927cdd2d32b9e3e568c95e3b1248ee4d2e4ae69eda0697ecbdff050bfdaff00e20f83acf5af12e9da55afda103086eeea7673ef8e31fd2be7aad2503a232e63e84d3ff6abf8f9a85ac725d7f60290b80374dfd5c5732773451e52edbfed1df18a3c4ad65a1381fc08b2e0fd07998a63342d7f696f8aa549974ad323f4f2e3939fcda80164fda37e2aed690e97a66dc672c24fe5baad6c5ad875bfed27f13b6fcf61a2a83d0959493f86e34c1ab8f93f696f8800ec92d341dc3fbe2653f9026864f20e83f699f8808d892cf420a47f079ec7f2c8acc7c86c68dfb41f8d1f4ad63c53adc3a743a3e91a7b5cea3716f14ab236036d8e325f0189007231cd7452a5ccb98caa4b95f29f217c51f176b1f11f5fb8d6bc43700a0b97296806634089bfcb4524f3960a33904a963fc2075c347639a51b2b981e05f801fb3e78d7c432ea1fb44fc523a46896d10b944578d62ba999b3b7cc951d630bf2ee6dbe639270ca062b5333775abaf817a77889b4efd9d35bd4e4d0e2b711bfda5a668a7973feb912550eb028ce5dc60ff013584b7375b1afe1ff000eeafaeebd6da5e9466b99ee648da34ddd02e48ce7ee8e379cf455c9ed50f722a1eff6ff0009bc33ae7812d744f18f86ad2fed9447e5aea56c2409b23d8b20523a952d938c95723bd77537251f777319c9c62da33bc35e07fd9cfc51e32ff8427c35a4f826f35c9104f716566d6f3dcc512be3cc91402635c8073c724b77e5f3e3975392389ab53ec9ef1ac5c68be12f0b8d56fee16ded1e48e47b8919516dadd46d42e4e028f2fe6624f5909ec6b29ba8fe3dce9a6dbdcf2df0b682de15f8c1716365a8c33689a8dcb5fe8a127064b759773cd0a8030d0f9803a107037b00315cab73b627bdf876fb7471018c1406a8b3a0b31970726b07b9738685a550dd69185ac4eb0a150727a500b717c84f5341a0f55c00a2b78fc203d6072707f9d301df666ff2687b00ff00b131e99ac1ee002ce40302900a2d8e7904fd28017ecce3ee2b0ff7850049e4bb26d2a3a564e766037ec2c7a66aa32e600fb0c83919ab7b00b1da4ee7050f4fee9ff0ac7da0122dbf9636b839f714d3bea03ecfe65f2624249f419a52d809fc89470ca47fbc3152029b760b91fce80229e320056ef4014efa33194895c05c6fdce8704fa66aa1f1a02236df67b3884b246c5999f31367ae077038f9723d88aee02bc934101fdf48067eee4e38ef9ae6a9f10115d6a3a7dbc2647d4ed539ca99675008fc48e6aa97c406cf876c34fd54adcc3aadbbc6e32ac93a118fceb702b5f5df86a2d7d74e9fc55a6acca0131adf464f5f4dd58fb302c6a579e0bd3a5945d78d74b470bb82cb7a88cdef827a51ecc0e4757f8b1f0a349dd25ffc51f0ddbaaffac6b8d72dd02fe6f47b3033adbe3efc0aba9fc987e367841c819222f125ab1ffd183147b30124fda13e0222e4fc6af0a03dd5bc436f91f93d6a959132f846c1f1f3e076a17296969f183c312bb0385875c858f4f4dd4cca3f131f17c70f82ad766cdbe2ae84644c6f58f514623bf41416473fc7ff00828d3adb8f8a1a3a82bb84ad76361e48fbdc007da81cf6281fda73f679fb70d3e1f8c7a14d2162bb2dee44ad91ec99a0ce1b00fda5be05052c7e2558b63a858a5cff00e834164727ed43f002199219be26d92349f7018a439eff00dda00c8d7ff6d5fd9ab402b1cdf111e667fb9f65d22e66fd110e7f31584b703c6ff692ff008281fc2af19f87e4f855f07f5fd425d4af2641a8bc96325bab5a60f98aace01392555b00e158f2320d7461a376ce5c4cb9523c77c3fe2bb29d0491be548f938c71dabdaa70b44f1aa4fde3a1b5d5ccb18923230699668e957c1a4dcc7935b43e1319fc46e69ba82ca8198fe55ac3e224bdf695600863d2b502486e6e6da6fb5dab7ef03065246718f6ada0f9668cf12e3523646b5bceb716d1eaba7aa34864267496356f2dfdb8fc8d7d3e0e71ab4eccf9ead4ea509142faf9f57d36ef4bd52ca0bab4bc81a1b98246cc7b4820a9c9efe87a1e782335d55b094e749d8ca18ca94ea2773e09fda57f6473f0367bcbeb3db3786b519a496d6fe3889fb3140a7ca9b8f9980390cb853b1b2bc153f2b8dc22823ebf038b75e99f367c60d3fe28eb3e1a6d5be17f8935dbdd734a4ff89a69fa25f5cc7f6f8370ff00495489f2d3a9da24c72cb893a8909f292b68779f396b7e2cf88ba3eb571a8f8834ad6ecf559c0fb64d7725cc73cc471b9dd98339e07535a538c252f78a8bb327d1fc63f13be24c51f862d2db58d7278998c70196e2e9a2520fdd0cec141ce08039e9c9231bfb2a1d8b75343d7bf6add46f6cf51d46d9234b7b97f14dea5c453b10aa41c60fa11d33e83eb9d7eaa8f128cfde3c834ef869e31d7b54b0b0bfb3900d46e512d934db61713dcb39dabe5448c1a5ce31d7a900e0734bf77434923d7a5252895b59f857e23d1f58bbd2268e22f6b73242c42edc9562a782323a74355ede876343f70b40fd9b350f0ddbc369a4c215523c101391eb5f02d49ee7a9747451fc2bd7fc908ac188e32d11c8f6a9e4b1a465a0f83e176b51c8ae563623aa84c1fd693868573125c7c3bd5188125bedc7b67f9547230e742afc35d682870f1aaf6ca7228e461ce870f86dadc99f2afe24f525073fad1c81ce27fc2aed7b1bbcc67ff691383f80a1c1b0f68490fc2cf133b7968cc449f29022009c9edc7069469b8ee1ce745e35f063e9df0de5f064f7093417703477ef0b8432c8e842b853f7ca7ca5477da07f157a1464940e7a8ef23e38f88da77c65f08dccbff142dceb10c83745a8e910493c5367f8c089247b63d0b099107400fcd5a39a441e610780bf689f8a3ad9d3a2f056be96dcaf9767a4cc14f5ea6daec11f8c67e94bdac45647d11f00bf631f88367a745ff09643068b6865565b5965592676c7facf2a255943772d3223678f379a86ee33e9ff0002fc24f09f81eccd958da3a4d71b56f6fe79479b7318c92a79da809c7ca0f5c6e672324ba42946e8dbd7affc25e34b4bef05ff00c244bbaf2d24b794d94db24895d4a128e78046786ec45694ebfb19a9f638eb519558b877d087f670fd98fe15fc1ad4efb56f0a1ba92e7528fc89ae2ff5033b4709cb4db140da80a2b267a92c83a2d7755c7c319e471d2c154c1799e81f1274a7f11f872eb4c95d41bfb770629220ca232b82ad1b7cb200a7051b86e9df35c138f24ad7b9dd19ba9ef33e03ff008284fc51f8b1fb217893c15e0af845e356d3f52974e95a690da24be4db249b5517cf4650724a1403e5f27b023243737a8da81ce7813f6e4fdb17c436b15d3fc68bc0c8762f936967129200eeb0e3bf5c56b64383763d1348fdae3f6b3b960e9f1a35273d675592d7318032491e571f4e2b9da5734bb2cebbfb607ed6da14d03ffc2ebd4654ba987d8cac5679900009c9588ed1edba8b211a927ed8ff00b5e5e99ac53e2b6a104e8caa004b57d871e9b0efc7a646707a734590d6e56d2ff69dfdafa5f120b5d4fe3feaa63740c90c6624773df811e4738ea3b8a2c8b35f52fda57f69db2b5fb6dcfc66f10dbc72b88c93229c7a7dd438cfad0436ee56b1fda3bf68fd4d259edbe3aebf30b7562d6f2ea124464232000460819ee062815d97ef3f68ff008f5a5bdac9a9fc66d6a2992cd249845a9cbe586650c15b71cb100807181ed405d9a97bf1cbe3a78b2d6e3599be286b62ceda167529a94b00c8e460a952c3209c9cd16417679a5efed13f1c3ed11eaf67f177c51670baecb3846bf74c26906eff0068e01f95727033df8345905d9daea5fb43fc77b5c35e7c56d56ed23b706edd7c40f1887230323702bf310323393c7ad1640679f8c3f1d223e5a7c56d796429e66c8fc4772eeaa4f040f30e47d28b201d7ffb47fed07e1e4f3ecbe2d78a236f230b235e3cb1938eeed23283f419f6ad54636d82ecab27ed13fb48465269fe34789de6b962be526a13f5c1e814fbfa0cf5c8a2504fa018fad7ed1ff1fa5986a6ff001a3c531c7968608e3d6ee3e76ff9e8407c8539db9e72781cf352a9ab85d9b7ff000d01f17218e0bb83e2eea1e749645afc37886f0148f8627e7620f3c707278c75abe48f60322f3e217c54b980ce3e25ea805de5ad8c5ae5c867c63fd5b349ce73d3f2ace6926069787be2a7c42d126135c789fc433da18ca37fc4d64215bd59449bc60f278a8b21a763b08be20fc538e7b7d317e23eb8897218ff00a35fce41dca395ccbbb04e0827a73472a1f3c4935bd4be2b5a5bda9baf893e22b908f99628f5cb890151fde5df93f43edd7a1e2953f785ce86ea7e32d734cb59f53d7bc797cb18412430b5fcdba5191f20f98e188c8e831e94e34c1c93d8f2b87c7facf893c7af7de23d4b5a97ecf02bccfa7dc4aeca08c8576e91a91fdddcc47205765386a22cf893c53e38d56e6dafef353d4ae2e1ace67b976695120f9dd5238c11b550214db9dc70304f15d364053f0a9b8f89135e6b171abdc085e1114a24249918c4538f98e30463f0a5cb17d0d6324919971f07a5f0b6ab0cb0d934f0db8cca619490077621bf8b8c71c73d3a54b8ae8294aeb43bfd0f5487c21a5b599b8fb3cd71978624b49194819cc6e54614b638e878f714b90ceecb1a15f5f4fe293a80b00ef238513db4604a0e46783ce3b706aec89e7474bf102fb5ed19ed8da412c91c96e7ed123c7f333f98d8e0e49e0a8f6a2c8b83ba38dd3b4383c6df6f7d574f689658f6490c839624fa1017f4cf028b229ec73f75f04edf47d496fb49b169e62bfe92b1111875ebb0738cf279f6f7a76441a375e1f16961169f3bdc3ce1834312598f2a12393e66de4f1d304f4527826b096e03349f0cde5e096cf528a59d1830cc27ca78dbb3061c30fcb83d0f5a71570e650d4d6d4fc2faf5a4b6696d633848ac235991003fbc550092324e4919cf6cf4a7c82f6c89fc1be13b8f1158ea77baa5bb5b5cdc4842db88c92a49cf50003f955fb264f3a38ed4fc11abf8567b6b2932d7d73219c2cafb5d17002b21e99241c83c10051ec9994e6afa1b3aa2dcb5ca489a26a6b24d6a0456f0cfe60924e32b91c8e327d0fb9e6a654da44a9dd905f6813e99656f7b2e9fa8451345be7329598c449c01c31da718ce071d08e0d67c8cabb33751f0cddea964b7d61abc7e5c3216f242a2b32fbb8419e3b6055aa66ee6b951e69f619a6f16dbea3711450c52caf125c4f23104ed38550a7824e383e9d6b5a51e5670627dfb58eefc3c2489c467829c150780457ad4be13cd9c353aed3b5062a89b88c1e99aa24ddb0bc759f03d2ba29fc243dcddb0be5439dd81e9cd6823574dbaf3be453939e9401a301619e4e73eb40ac8b7a2ea0da45ebc332848a64d92b9190993f2b7e07f42477aedc2629509fbcce0c751756c9166f7c2fa959cef7c66b767906efb29da4baff08c03c8391f303dc64735f534b1f4eb53b23c77839535aa385f8cdf0f17e30fc3dd5fe1f4961125c6a16ac620d09dcb2a1dc98dec003bb009f42d9ae3c74a97b33bf2ef690a963f39bc59f0b754f801f1563bcbdd42258a0b9227961b885e487901b6846753c1c3152472466be4e6ea393e5d8fab8a9495cf23fdab7c15663c6d27c52d0ac6c2f3c3badea6f69034b7f2a9b49d177b4331c808a15c32be4aba2f05995aa79eb435b14a0d985fb3efed51e1afd947e2149f107e1a78423bdf11416ef1e9f7ff006d962b3b59f8c16b79159ae5548ca9668f0ddb0053f6f59f41ba2ec2fed8333cdae6a3abcb379d7379e39d4dda74938006323f1c835d579f73829e19f2b387f83df1bfc53f08b5f8bc43e13d59e19a290b36d453bb20020161c12060918e3aee1953b41526bdf7a997b0a9aea7d5f63ff0595f8b56f650dbb78620631c4aa59ececdc9c0c64b347927dcf26aed85ee89f615bb9f5878dffe0b5be0af03ea474cd5bc071bcca824905bbb379687a06f99486ef8f4af8da981aeb13eca0ae8fa178aa1ec79efa9ca5dff00c1787e18c7282be02bc2b20dc8d15948e187affade3e95b4b2caf4fe245aaf154ee9a632dffe0bcfe03bfba4d3ad3e176a8ef2c8123074e948249f412d723c3fbdcaca8568ce3bea7a145ff054bb20abbfc11e5b4b0f9881ac6eba63393c9ec0f4ef55f54475469de376ce0bc63ff05bcf0a7847599745bff87e5e7b7e2631db48006ec3e7993f1ad259754e54e1adce1ab8aa746aa8c99923fe0bcfe133f345f0ce563eb1da2b63ff0026aabfb2eaa4b996e4fd768b9b517a22c697ff0005e0d3b55bf874ad2fe125dcd7371279698d1b764fd45dd1572ff61f1b37a756357e0773d1ac7fe0ab7af5aac77575f0d208814dc2396c6484c9fecf139273e83ae3ad63530d1b6e691bbdcf6ef8c5f1e342f8e3fb1addfc64fd9bf5b82cf541a73fdaac6cee99a5f3d71f69b29111db13295254e7920119566538c28a8ab5c26accf94be00ffc14e3c01777cba4f8dcdd4ffda720364a2fa55f22e5b9689951d06d3f7831e30d83c9c05529fbba3274ea7abeb3fb7a784442d6b7b77a1c488c7105ceb72c663f400274fcc9cf7ac3d93ee3bc3b98f7ff00f0507f0ea2203e32d162d80794c75bbe7c28e83997047e15a479faa1da3dc6cdff00052fbb6bc8a3d0bc43a1ddc5e5b25cdae9d138f4c373b88efce69b84a4d762652e5d8f17f17fedd5a97c3bfda0f4ef1be83ad5abd9eae1d7ec3657ef208ee4f3f3e0f0b2a8c6093f360f6188f6537269ec669be6b9f647c35fdb263f1569563af698975e4cf1a33c91db2908495241c3e48e7047ad61523ecb666ee69eeae7bd7833e26e87e3710eaa639d2e2dc8df6f32e090b83b7db2a430e00c67fba6ae9556e3a9cb35273ba47c53ff0005bd6935f9fe1bda181583ea5a8f9d731a0f337b2407cb1c8cab1dccd93d557d0e7b28fbd215494943547cdbf0db40bdd3add6dedaf99763ff00a346c8db07037641002a8fafbd6d24d0a150f56f8737dad6aee1960b3580398372a1324cfc83818f9d383cf5e9cd73b4cdb9d1e99a27812f5646b7d5ef2d6589c2bc28aa4a4601f40d90fef838ec693ba293b96742f02d9787bc4136a4b7f298b69f211dc36c0d925474ce491c9008e71c1a4db487b13eb16f6367aafd9f4fd3d2666dc7cd9882c06090432e4eec67938eb8a8e76573156d7c0ba6dceadf68b8bb92333442532dcdd960c7a962a14671dc7a8f4ad15dab92f534afb4fd2f472ce21b4ba9cc6a6de3175b9c8072adc9e31d36f3d73c014ecc0961f09586b7685f5486d2d258edd44915c5c941823b104647b9c8a2cc0b3a6ea3637960da3699a6dadc69f1931bdb437e332e3395c2e40539c75e45166473a394d13c03e2ad62fe6d71fc3e640d1e2533615148e3680a080070062ae34dc95c39d1b3ff0008068f74ed7e9a24b753dc605c24215e0b70bce4a17f9c95da4e4e7e7ef9c557b261ce8b3e3af02df3416f3e8da3cf6b756a6378dd638d4c2bb7ef901802a7046cea0b0eb47b261ce8b771a5dcc3e1668a5b985af2e17ca952e0008884e370009f9ba9ce71dfaf356a2d19bada962c3c12a971fda76974a0476d27ef6d0aba4ca53057cc5c1e9d7d383d81aa8c592ebd8e58f86358f1778de6d574eb6b059226f2e34f350470463854c039381d3f33cf355ca2fac1724f05693a71fb66b9a65a5dea314661b7469711c67fbd86386232c003c0e0f45a7ec98feb0696bff0abc41e2df0ac434cf065d4f2c603472c0b132071939561fea803d074e3f1ada19754ad1e6443c6422ecc5f0ff807c5ba168f25c5c59de36a71bb0b79e44dca303ab0c02e4e7bfa1209abfec9aa673c752b6e74fe0ef0ef8ca14b13a8f8724b94b70db658576b21ce4af38e3bf1eb47f64d5f332fafd1ee747a85dc7a8dbc7a7e91e1d9166113605cc64206c60ee21b39eb8f7c565fd9156e6ab1741adce17c59e10f1aea6ad6b7da74adbb6246151184407f749c1527f1fad38e515fa0d63b0f0dd92699f06e5d2aea1d7343d27fb3f505dcc6e2ce0892766ee55baaaa9e490c0f6e7a56cb2bc42e83fed1c377361b4ff1c5eddcb69e2ad01355b758995ee5635b7b98ce00044d1fde393ff2d124271c6294b2eaeba133cc6847666749f0abc437f6eb79e0b778a296e104d05ddba1ba1f2ff17ccd9e7233b8f4e09c1db0b035fb197f69d2364786b5db6b78b493a7badc2a05373e586087182ccbb8061ea3de8fa8d75d07fda748a32fc39f144103a5f5d9b9df14885e171e5a07c7cc17771d0fddc9e7d856b0cbeb3e81fda944e8349f87d37d920b99ac891e5233947c316ec0863d00ebeb42cb310fa18ff68e1fb92eb9f0ff00c4971aa2dee9f75956832eb34aad91cf200e9f8fa7b569fd935ec690cd68416e4137c31d4f5fb711693657364e586e9527063908ebfc449fcb1594b2daeba14f37c3b46ac3e00d72d2dd74adfb1a0c6fb8203313d8e1b0091cfe7570caebcba0bfb56877336c3e136b5a569d337f6835c4cf1b4616595506e3edbb039cf6c727ea0793566ca79a61d46f716cfc177305e43a2dee8d64d12a67ed11ea3f36400029014e460007d6aa1936216c73cb34a55340b8f84facfdafedfa66d43e41c83765838cf4e52b4593e256ad18cb318c4a5e2ff0f5a68da7e9b0cfa55e5acb712912c9652a16047a81838233dab3951a74d7be9fdc6b4b31a755d9193e26f09596bd750dfe9d69aa96b54584cc8a1f6a81819dde83158b549eb14d9d57954778b5f32c43a469ba1ed8fc2d05f4f7c91365a72b9832a46ee846704e0f4f7cd6135ccac91ac694a3ef49af9142cad269f4c920bed3753306f62603a72156727ef16e32063a8c1acd525f69a35e689cd4de13d0f469de0d02d269e1d43292cd342a83715df83855c0e7a9e7b1e950e74569706d338eff00857f70ef25a0d2c59c0a1a48dee26030723e55500a951eb953cf7a5edb0f1de69193a5397c2ae594f0b5d6957eb6cacd334880a80ca598e3b63a8fd6ba218ec3c74e78fde652c2569743a3d13c11e30d41c0d37c397d70c397482c657207af0bc53963e84769c7ef32fa8d6ec763a3fc18f8bb7ca2eec3e17788ae10f05e0d12e1d57b724271531ce30f0566d7c889602bdf4474fa7fece7f1c646db3fc36d4ed54024cb796de4c63fe04e40ade39b61e5b111c1621cacd1a16bf047c73a69f32fafbc3f0107063b8f13d946e3fe0265e9f8d6d1cc2948d3ea154d8d3fe185b2465f54f89de1681bba26a2f311f8448f9fc0d62f30a97d221f5487f3178fc3ff008631daf95aa7c64b495c1f9d6c34a9e4fcfcc11e3f1ae7ad8bc4cdae58950c1e1f9af291a1a4787fe09ea107d8350f88bafc93592996d67b7d11124403ef004dc9054f3807b13f875e1732c6d156e40a980c1d47f19e7df1dbe297ecc5a03c1f0af43f1578cedef621f6ad46ef4f3a74531520158984b2b7943b93ef8256b6af99636b46dca4d3cbe8d395d33e4df8b9a37ec4be33bcbdd57c5de1cf8a3783679b2cf6b7da532ca40c1f2d9617180073b58e327eb5e6b7995fdd5a1e9c28c1c773c17c53e36ff8271e93a7ea3e0df14fc23f8bdaae91a8802e161f1ada5ab6d52ccb200f62e1a442cc54f1f788ce0d11fed16fded0a708c353c8edfe2fff00c1223c3de228fc3e9fb08fc4bd61cdc88e3babff008e0b12caa5b018793609818ed56e38cdae4dd763d97f687fdb0ffe09dde09ba49eeffe096765e269e6d66fb6cdad7c4fd5260b2a8877b9540b9dd919f4c561f54cc7f9d98f3d382691e683fe0a89fb2ee8d6ed6fe15ff823bfc0db73bb31c9abcfa9deb81efbee07a56f4b038c947df9b39bdaa4f443d7fe0b0de1b894470ffc12aff65c08a30a1bc03744e3b64fdaf9fad69fd9d5ff009d9a7b4876373f63afd9c1bc73e2ebcf8c5e3e55d42d22bbf36dfedea255bc9f3f349b5b21954f007424f1c2d7ee5c27c2d0c56279ebc2cbccfc27c4ee3d8e5dfecf8476a9dba977f6cbfd926c75ad76e3e29784fc35a7d9da436a8f7f2c5e44085c161cc6cca0e40070a39231debbf8af85e8e19f3d38fbb6dcf0bc3fe3cc6e3d7d5f112bcefb791cff00ecc5fb3d586acc3e27ead6d0c16d0030d85bf92ade63f2af2804fdd07e41f8b57e178ca74a38bf7763fa5f2ea0e5495496e7a67c65d56c7e1878326d7a186dee2fae8982c2d1c2aacd70e8c5558638445de4b1e9b71deaa34e32d8f56b4e34a95db383fd9f7f625d2be2a7860fc40f891ac6a38d4642fa64400dee84f33b170492cd9200fe103d6bf58e15e09fafe1e15aae9192bea7f3c71f78891cb718e8d0d5c5d9d8ebfe22fec03f0a3c0fe0abff00175ef8c2e2ca1b1b6699e4b9d2049bb038032ea092702bdacf38472ecb68f3b9a6fa1f219078879966998aa318bd4f3cfd8fbe0769dae6a771f137c46fe459c329874f8954299392249917b9046c007b9ed5f8866f28d5aee32d123fa7f27a4e861233a9bb3eebf067ec069f10be1cc7af7897e225d786ee2f61692c6387495ba2a922fc8ef13b4609c64aaae7b578d29c5ec7a72715b1c9fc03f84bf06ffe09d3ad6a06d7f6a9d6fc5b69aea37dbbc356fa04023b9b85247da0bf9edb24504a945f98838c1db8ac926cc7992dcf973e257eccdfb3a78f7e27eb7e28f87ba278cad2cb53be6b94d397534b65b467e5d5449604ed0dc81d06783c52716c9938ca36b9ea9f08ff0061df817a8e99068be35bef1f69f0c8ab9b986f6da6382792aaf689bcfaf97e61c7f01a5ecccb91773dcb4dff00822f7ec99ace9b0788ac3e2478caf6ce58bcfb79cdfc01648bae407832380782339182335a283a9351484dce27847c49fd95ff00636f057c76d4fe0a68be01f893aeda69091c7a8f89db5eb3b6b15b82b96863dd6dba794310a5220cc181e3e56c7d5c786aa57c039ad1f4387158c545abb35ff67dfd86bf65bfda2ff69397e02dafc0ff001e58c3a643f6d93c551f8c6d64b345468b691b2d97792d20050e08390dd2b3c770e470f96aa9cfef2d5a33a78b9ce4ac7d21f1cbfe09bbf0ff00f659f8489f11be0c6a9e21bc4d19dae758b1d47525915ac9a5ff00591ed8810e33b989cfcbd0640cfc2d784a5b23daa377b9c8e95fb52fc19f857a2dd25e7c6dbaf0f5adcd9931cf71a6c8d78172e5a311a2932ca0b10ac990739c81caf3538d48cecd1bd57cab43c6bf680f8ef27ed87e29d3a0d0df51b1f0ee811b47a5fdb59a5bab876c2c97531eccdb46172428007279af730fc8b567955e7527a246e7803478343d12ee1d0a59ef8e1566792319cb754604e7dab49f237a3221ceba1e87e1cb5bbd4664116997ecb3c02284fd8cac48475192bb4631c64ff0e79ce6b1b44e8b4fb1d77843c3de1ab5d4e565d42596e507fa442b7443ec1c01b30393c65b071efcd44944a4e487f8f6ea6b696db49d364b79a595c6eb2b70a648a3fa8239ebc67d38acdba7b5ca8b97318ba3e9723eb335e25e982e55024168ec4c449186ce33bb9da47a007b6413911b94e0f873e2bd3fc5b0eb6e92de47e6013968d56588e705c64004118efd3f87b56b151480d9f14d86aab753c5a0f8224b99e183cc88dd5aef8564240ddb792edcf7e3be302a928b32ab2e54666b9e1a99228b4dd6ec2ec6a17566aef13c6c17cc39c0dbd7d30003f8f4a39518fb4475bf0efe1f69579a4cba34b6d134ea564bfb875f94007e48d48e98e7711d78f4cb3e4467ed090c6346d624d234ad2d0580dc0343726346ef82307273cf1835d54230e4d47ce6941e19d26d506a4ba0492ddcd0efb78bcef9963538ddb49e5810a3dfb74ad9c69a0e765237577736e04051bf7a15e29ee0a6c9180049565c86e49fc3e94bf73dc39d8afe1fd5a589eefc51a0db3222797159c173b9e540787fbc073fddc03de9f25338a73a9cecdbf0bdddb6b3147a7c51c16aaca636f32211ef23aaa267eee08e47ad54614c86eb4b6457f1bf8765f0f2f95e15b3f36655633c70427080e3e62ebd383eb54a14efb8ad5fb15b4ff0faeaab2dfdce916d38b7dad1f98ac88ce7231c8cb64b73d7a9ab5ec9f51b8e21743baf0bda6a10e836d1dcd808253237ee83160aa4e40caf0719afa4cb654a386499e4633eb1edb634d74996520dec96eb0bb19066039de73f28240c753f9d77caad1846f2b58e29c713256b166d6eaed2436b3591b748f0a246390a3db8acbeb784ee8c7d862bb32c5ee8d23e929fd9300696590ec213ef8278cd47d6b09dd1d4b0d8bb6cc8b48f02f8aaf6f92ca7f0f49203f7cada93b383d7afafe9512c76129f545c7038ba9d19724f87bf112eadbedf07846e96dc38dc62b261b7d3231d7df3f85672ccf0aa2ecd16b2dc537b326d3be117c429a713bdb4b6e1c8244d0460b63a7fac703f4af3e79ad07b58e98e53887ac932afc48f05f8bfe1e58d9f8d9ede116d63a95bb5f31b80aa6de49041213b4950544864ebcec159c732a4ef62ff00b26abd4e834ff871ac6a911b9b6b45ff004a8049653cb7091ace8c321d5c900a91c8209fad28e6b426ec9a1bca2b257641e16f853af6a71c86dbc57a0ca6008d3ffc4d622abe664a2b36ec1242b639ec6a7fb52329f243564cb29ab08734ae91bb71f06f55f34247e33d08aa26cdb1dd20c0ce40f94b1c67dab5866b35f648feca8bfb43bfe146ea170c5a6f1fe9b01c6d64866762c3eaa94e79bc96d11ff6547f98926f8382c1a3687c73a6a85501da482e7278ff00ae75ceb35aae7f096b27ef2d09af3e0f787e648a793e22d9090f27cab2ba7e7b67f77f5ef572cd6b2da057f64d1fe624b7f861e0189776a5f1183321dcc5347989cf5ead81f9d259a629af819ac328a6fed104be01f84d2ddee7f1e5dcec47caa9a13647e2ae7f95655b33c6f2fbb168e8fecac353fb68b317847e0dc0a12f753d5e4dab8dea0c591d718319c57975332ce1bf762cd1e5d818af7e68a5268dfb385dd9bc7aae8dab5e244c2488c975b7cbe38c1daa7a1e99e68e5e24c56e92142794e19fc488342d43f66bb695ad61f02dfb07931996e5f19fc64ae5ab95f1345da125634966592a97bfbf918dad5cfc12b1d6eea1d37f67e81e65e57cdf105d0597d494f3076c9fc2bccaf80e226b96a4d58eca38eca1eb4d3b99daefc5df06e87a6db34bf03bc2c9b6f0c4d693d9cd7ac108e1befb6390c0f7e95c6f25cd2a3d6a33a7ebb847f64e5f5cf8c7e21b691d34df06f80a0883b34417c291808327aaba48c7fe02035754722c528a4e64bccb0cb4e52b7893f681f883796f0dfe812693a2892d7132dae8b66c1a41d5b0602c3e8738ec48c54cb876357f8f26fb584f3387d889e75adfedc1f1bf4391aca6f8c976b3db921c4761140131d31b620318fa54ae17c0df7912f3376d892cff006a3f8ffe2485279be3af88a68e51bc25beaf346003fee1518ada1c2f97bddc8c3fb4e5d8b13fc4df889ad0106bde3cd66e9074fb56a734a3f26635dd4b26c1e1e3c91dbcce7a98dad3774ec32c6f1e48c2cb74cdcf46aeea581c3d377b1c53c4e21eccd183e570e38e3ae2bbe187a1fca67f58c577346da573c973f9d5fd5a987b6a85a88c61804207ae293a71a6f457224e750bd6573f63b84d42cc2f9d0b06562a0f23a75e0fe3c56aa4a31bd8494a0ef73e4cfdab3e05df7c32f1bbf8c3c17745ac7c4f7a97161f689c6eb79739b88b2cdb99b2db864e4a14072dc566eb456e8f430f52533c7bc4baa59a68d7169a8b8112c72ec9ad79fdf9055df6866c36320e0edc7b734d54525752b1dca725a1e27e36b5f0c3c8fa6d9697179135c0d9024b96e1f011c346ccc18e08c3f0060f39ac6ba94e36522d393dce2ec7e11c1f12bc6f6be0f8fc21058ebb26a36efa3dca4416296612a86b5601140595082ac4655f009c162bcbecea5fe207b1ca7ed45aa5ddd5ae9d71e5bac73eb1a9cb1cb9203e5e21c1efc05fcc57a31a0ded33cc8c65276679269b15c6a52a5859422492698297c6760f5f6aeda149d38d9ea6f2846942f267bce9ff00b1de97756105d49e31915a4855d8045c02403fdcadb9576393eb587fe647eb9691f0cbe14f83342b6f0f683e058acac6ce211c10bfc4bb458d140c0e77707dfd4b7a9afbac1712e7b82a4da68fc371fc1193e718ff00ace25bb9c77c53f87df0afe25e9d69e12d5fe1e683716726a312dc7f6b7c77b6d3d402a595c05932e015c73b7e6f5af99cff008df30c641d2ad3fb8fb4e09f0ef2ccaf152c4528df5dd9bbe01f01f8062f074162ff00b34f872ca1b38cadbda59fc5b8d91230405f985c2eefaf5e735f9a3c650526e73d59faed38e22a4f9631d1199e26f805f05fe317896083c53fb1dd9dc2db46e91dd5dfc5f096ca8cc0b0c457db8938183b0f4ea2bd2c262f08fed9e766d471b3a7cb13d06cfe1258c704767a57ecebe1c86de34096f6b6fe34bc75550301542ce7231c6066bee307c6d89c261d518629251564bb1f94e65e1e6171d59d6ad876e52d5bee56f1cfecc1e23f1bd9c3e12f10fec1d63af69936256923f13eaab11f4042ceacdfcbdebe7b3ee37c5e22314ebf3fa743e9386bc36cb3055955851e46773f0dff006599fc3d1c3e1fb6fd8a347d274bb380088c777abdd345dc7c86e08273cf3d79e0f4af8cad9f52abef4dea7e8d1c0e22357d9b5a238ff8e7e18fdad7e20c93e83e12f821e32b5d2638c451c7fd8770925c2b123636e423078f970c00032246fbbc5feb0c3fe7f2fb89fa9623b1e0ba97fc13f7f6cff1feb11e9da07c0ad6e08af64f264bcbfb57855221c1323ca556340723122dd13da341c5672cef05377a92727dd1ac30734bde3d73c2ff00f04f4f8c1f04459d8eb3e01b1b8bb92d8cb2dde99aadb0589b1f770e9106fa85039ef59cf39cb52bfbc57d45d4d13b1d65bfecff00f142ceccdadce9ba2d80b98cb9b2bbd7f4e8373a9ca3aaf9c30dd4e47f30318ff6de5bfde0fec997f31ec7f087e1478b57c16d6379e2cd04b47a94f2c72af886d6628ac919dbb6390e57cef3188f739fbdced4f8972e6ef08cae885963bfbd23cfe6ff00826b5beb933ddf8b3e31e87713c936e92e5ee2f231212cac5da28af4c65c904b36d192c4f3c63d7a5c7d88e5f67462f4ee6cb29c0b57a8ee7b1fc01fd8fbc17f02aff58bdf0f7c4dd22f27d724100904d3ed8a2024691137f98e08591f04b919da3b0aaa9c55986260d38bb321e070717681d47c62f05f897c45e06bb93c0de38d120d4e1b8478ed2ea52f6d7cb8d8f6d3a14c98a48df0703aaa9208054f99fda95bf945f538c3ed1f937e3dff00825efc30d2be38eb57de05fda3be17d8786ef35477d1747d4bc577427d351150cd6b816722ed8a42c8a73caa8ef571cdaac15dd3225423276e63dabc23fb1efc3af04f8205f6a5fb5dfc1af0ff0087ac5e39f55d5eefc4371279699f2d54b3db451ed694ae32c0e76f5c629bcfab3d15227ea117af31afafc7fb0cd9ea905f47fb7efc1cd467b3c79657c6b6d031dca03a36f94311c6de01e00a5fdb988ff9f43fecf87f31a7aa78f7f614f0fe8d676da5feddff000c660a732dbda78cac9c7b8f9e7cf147f6de23fe7d7e01f544bed156dfe347fc13fad56e6ea2fdb57c1f3c92c614a9d674d8d23607390e24918fe18a5fdb55e5ff002e86b090eb22d691fb49ff00c13726b999fc6ffb6be862e52322296df524baf317b0cc76bb8e7fde39a4f30c454da99a430b43995e661e97fb4e7fc128ed2da49f57fdac6d647f3088a31e1abbbaf2803d030b60154f1803d0d4fd6f1bfc875fd5709fce4d75ff000501ff008243f87618d25fda8fc5374f10ff0057a4785a7007fb29e622f1f5c7e14beb38e7f60e7961287369330f53ff0082b07fc121f477dd65a87c5fd5a481b7c5245a7e9f07cdeabe6dcaecff008081ef9aa8cf1f55db96c633a1420b595ca7e39ff82ae7fc12eb4d8f47f14de786be2f5db6bda51b99a0b0d4f4833594715ccd6eab2fcc42b3ec6700124a9524f357ecf31e8472e18b1e1fff0082c77fc12434bd3a5b7b1f04fc5eb613303334b2e90c588e07fcbc0ed473713ff2a2b9b084d73ff05a0ff824ec56e2d6dbc03f14e643cb485f4b049f4f96ec01f873ef54a5c556d228a8bc1d8a12ff00c16eff00e097b06a89711fc24f8ab74c907930249a9d8a00b927194bb271cb763d47a66a66b8b2a47954515cd833d6ff0066afdb73f62afda9753d52cbc0bfb3678ceda3d3678164b8d4fc48abe6b48ae46363bf002773deb0faa7177907360cf43f14f8ff00e0658896e61fd9bddcae4a4971e27b9dd27fb048e727fd9047ae2b454f3c8ab4eb24fb0feb7818e9ca51f0a78ebc2bf6b852d3f67ed32c5c12f0c177ac5e4eca0f01c3b9201cfa647d6ae382cdf15bd6bd8ce78fc1c7689a9ff0b8fc15617979a6dffc30d30ddfca41cbba87392a0eec9f98063d3b5691c9b34babd433798e152d22585f13a0b09ac66f87fe1ef2e5659218feca7f7b9048046ef988c7ff00aebd1a791e37fe7e1cd3cd68ff0029a7e10f1febf69e1648ef3c31a4c6d0ee1fb8b3211403818c927f0af6b0b9029534ead577f23c5c566b47da7c2695bfc63f1098e19a0b4d2e265dccad0d8c2920fa1c1af5a86414d4af1aadbf33ce966b4afa44cf6f8bff0012ae751948d7123b72f8402c63393df031cfd6bb3fb0a7fcc4ff006ac3f94bb3fc60f88169a44115d6bf238132a0f25845ff002d081d051fd853fe627ebf3648ff00107c5da9ccf7b2ebb7aa30415174d9c30e0f519c73570c9e14fe37713cc6b4763cc7f6acf1a7c48d1be0b6b5e28f06f8f751b4bbb27b496d6eadeedd1d2359e25906eec0a96cf048e738ab594d0a8f952d592f37c441735f63e13d6bf6d3fdad9757b9b48be2deb570b13e1643e26b94cf19fe115d943845cdec78789e37fabcf966cc2d7bf699fda6bc6166da3eb3f10754f26e2e6dfcf94f896edbe55b8889ca9e1b807ad4e63c2cf0385954b1dd94f17471f5f9133db7f6b0f8a7f1abc0baceaf65e0af196aba55be87e2fb8d23ec31789f508235b66413c12a088285320321231fc3d4f26be6f87f2578cc43563d7cf788560307cd7ea607c36f8d5f1efc57f027e2569efe39be6d4ada1d2355b79a2f146a2cea22b97b5906e762db717a1b6f41b4f1dc7d0623865e0718a56dcf9ea1c631c760dc6e799e99f17fe3b47a8f9f27c42b90bbb2c23d7b52ce076ff595f42b845c95ec7cc4b8e22a4d5cea753f1e7c46d53436d587890a348f8db2ea97ce7a0e725ffa5297083ec4be3b51ea72179e20f897239964f10593a83cab4770fbbfefa9b07f11447841f36c4bf11634d6e559359f174fff001f373a383d8b68e1c9ff00bea53fa62b7ff543c88ff88990ec4406b7764c724fa0b6fe183786a039cfae5b9ae88f09b51b585ff111232d6e27fc23fa8c7347286d062da7fe587846d189fae49aceaf09bb6c44fc4785257dc72dac7a56a90deebcba45dd9b3149a2ff008456ce32a5b803210f5240cf6cd79f53871d24ddb63bf01c7b0cd26a291f7e7ec31e371e2dfd9fd7c07a8c8b7175e0c90e927cd94191ad942bda12c71902dde34ddc6e3139eb9af2d51f667d6bfdff00bc7b1dacb05fd938d36ca30d1804c2922965f538e47e22a1bbcac63561d4caf1b6a7aed969961e23f0fc816e95fcb9d8a67224dca09c678dd9fc1bdabc7c752b7bc7ab819dec8cef0eead2cb6373746de417a98179e445e707cae410c0f4c1fbd8ec6bcc3db81cedcd9787af5ccb631dc1174b2b30108da4927804b7279edd3be2a1ee27b9ceea3a2dba59bc5a8dc44862766b795e7f20ae4e738942febd6a24544f3ad7fc27a05d788e7852ce3ba916cd045f65944d18c920066e85ce3ebe879a95b94f629e856f3596aab613a42988c22ac6ddd793f87ccb8e84e7a0c115a199d3da9f365da148e3f885652f8c4f62fd9be48e3bd6f0d8e7ea6c5b5c895844131c75cd754066944762e3ad37b81340fceec743480d1d3542b6e6e43738a52575613d8afe34f05e91e3ff000b5d7837565222b9264b6980dcd6b30076cabc120805813d70c475c572ce074509fb33e4cf89b1f8f3e0ada6a1a1dddfdaafd9ef0c2eb0ce0b2b300a0fca87cbc865756fb8fb8156208279dab1ebc6509d352ea791ff00c2e9f89fa7783d2d6f96f209acafcf9b6d762358dedbcb31b8e554af2c09270013c7ddc52145cafa9c2694be21d7bc75a2ea7af4936a1236b56af677c97aea6241703113766c003e518273d78c90a7b1e11f1b7e1feb7e34f827a178cbc3d7b1dcc9a349712eafa6a29f362b799a255b907a3a07011c0f9937c67043f1d346a6a614e178dd9e5ba068d79e1ab887580a5d08cbe06370ce08fd2bd9a2ef13cac557f692e43d32dfe23e8a204ff8accc5f20fdd7da3ee71d3a76ad8f39e0f53f72fe227eda9f0ffc03690dc6a5a2fc33d1e59943acfaad8eaccb1459c34c522837c807a02a3dc57166f96622149aa755af9986458ea19bbe695256470be1efdae25bdd4e49f47f137c10083cc8af3c9f026be192447dd18cc913924ab06273cf00938afce6ae518e94db755b3f4ac2e2a8528254e9a5619f117fe0a49e3df06f93a5f87bf6b0f847a5cd139dc975e1dd570aa7ee80a964581e40f98e39eb59d2e1ca95abaf693d0daae66e850972ab1d17c2cff8286fc65bab9d3ef7c7bfb46f83af6276cdcc3e1df0a5e2b3c7c7dc927b261cfbe0d7d361384f0fdff13e471bc453a6cdaf8fdff050e97c1f6da8f8e343fda72d7c37a2797fe830f88b41dcab2638dc4596f64ce73ec3ad7ab88c8f2bcbf0ea55209b3e7a19ce6199e3553a73695cf29f06fed5be38f88973278bfc5dfb4bf847c43757f81657ba5fc369824f11fbb933c4c4b0c9e817b71cd7c9d6c260a5272842c8fbca152b50b2bea8f45f8b5e37f15f82fe1959e957de20b7b99aef50b7fed6bdb2d323b30cae25655f2d11723e58d4e46793eb5c92cb3053de0754b1b8993bb91e19378d6f574b924bfd6e570b6fe5177b938120943166f5dca33f439e052fec8c17f2223ebd8afe6332f3f6e0f1dfc0a7bfbdf86dacf9b36a813ed1bad6dae6057450b90279e254e73c2c83dc915db87c06169d3b28213c5569ead9ccf847e3d7c46f8bdad378ff00e2278aaef59bcbf5483ed4f3452a47f37112bc388618ff00e98db99a53f74b735a54c1e19c7e14355eab7b9f447c24f87fad7c40951d564b1d3ad483a8ea3788bf23a8f95420272dc6163ce47de90a8eb87d4b0bfc887edaa773e8ff0003e970e93a0c36ba6dafd9e374458627624aa2f2a18f1938cf271cb1e95db85c1e094ed0497c8e4a95e718b72678c78cbf680fda4fc45f143fe15cfc28f86474db24d785a9d6b57f0bea1706486393f7b22b978a058c8ddb5989f91720b33003daad93e0a94549a4db3e6a59ad68d6716d9f59db695368d653696a15a60a2de22926e3b890d3367a8e8a99f638ef5e7d6a3469c1a8ab1ec509d5ab4dcae7917c447f885a77c45b5b7b6d6f53b5b29ac6537265512d8caa0e3cd0f8dd1dd032636e7690bbb1c573e1e8d29ee8752a4bd96af53f3674fb36d0fe33f88343f1378a62dfe1fb996cee0cc8f2fda2e77bef6e33dc28c7e06bd78e1a82a5a451e650ad5653d59c1ffc145fe23ea5e1af841a07c21fb45bc6facea4daacd6d6ee70b676a4040fd8abcce00079060901ae6a74293abf0a3a9d7aaa56be87e796a176754f104f788d21577f97e63d3b7e95dd0c2517bc5056af5231d197adf4bce1955c3638258d76acba87f22381e36b5fe227b7d0a240c25018b9cf26b4865d864b58231ab8baed5d488b54b282d226302283b304633f8734de128a76e543c3e22b4f7918e2d902902d5483eb103fd29fd530ff00ca8ebf6953b92c5a74461188c0c8e94d61a82fb284eb55ee23d92a47b02a807ae140a7f57a1fca255aa2ea4b146047b032a9c6d2546091e848e4d2787a3d8af6f3b6e29b71d4b81f5cd72fb088bda20fb2ab0c06047b66b7a5421cbaa13a9ae8c75a5a3bea104718c1699541039e4814ea51a6a00aa37d4fd68ff823b69cf07c22f156b6be4bcb2f8a618d649115da34482360067d3cd6e9cf5af32ade2f4348b6cfa635ad2f5b9af236ba8218e0494086e2c1b68e7a798ad9201fee8c8c7e55b429c2504da469763fc3515ceaba85d5d691a547e5db5b166698e18b646d18cf272060918001ef806fd953ec4ca4ca3773df441f46d3f494babbf28a5f967fdfa9c003cbe78c00013d8e40c7347b387627999afe20f1c69f26a76da7d9e95770dcc16893470c32e45c36dc04391f2a739c8e98ea738a4a941193d4ebb40b974f0d45773e9b736c5a77336e9726324f4723aff3af7700a0a8ad0f2b1d149dd1a6f15ddda84b6d4943a1c2ee8d594fb03d457a91e548f262e4d5c8559ac536ba0564c86766e109ebd7a8c64fe14f990f9aa0ba946b616eb7f3a2baaa8f955b28ee464633e8486e7b63bf1545dd89a7ea3aa5b69b717f79a747219a30d9524f0bc1200e9d454cad6d4b86bb9cc7c6ad26efc47f03fc5ba48d2c8865f0c5d1825794967710b48bc1e49de01a9a7354eb29223111bd0925d8fcdbd7e0b5d3b5692dd208c065049551c9048afbfcaab3a903f11e20a75a38ad59445e476e82ea3450d13190123aed19039f714710ca53caea4796f6477f0ad57473382e6b267d79ff000503b2d2f4cf0d7f6ac96318bdf176bb61a8dbb46b82d143a3c0aefc7acd72ebf588d7e6bc0ceb54c7cd3764aeff0013f46f10fd9c72d82a2aedb5f91e5bfb19e89a878bbe28ea5e01d3fc3d717ade23f0aea5a65d082d5a4f2775acb242ee003b7134518078e5bdabebf8a331a34609f37bc8fcff008632dc7ca6f9e2f959c878cfe16f8ebc0f6a6ebc43e1f96c97cff295659a3f309e47fabddbc0c2e725475afa0cab3fc3e2e0975b1e266f91e2303272d4769975237846780c8731ca1947a702bd996221ceb5d0f9b70ab522ec9e8663cb23c80ec61bb9e95b2949c935b1cd18c61ad443e270ad86efdab7752fd099ce9f444bb8c520dc36a93c6462875256d11ced733d0b56cd13b91e69e9fc07fc2b1755bf885c9552d15fd46eb96a2ef41bdb52df7e1f94b2f20f63ebc5706295194257eccf6f25c56230b5938c62b53db3fe09ebf128e87f1362d1f50da96fe29d364d336cae142ddc1be7b7539e384fb4a67d4a0f415f97e32d1bd8fe83caabcf1987e68bb1f684d2db58069e5fb3c618ed49a265c103be73c739e3b579509bdd9d70555c5a915ed7c47e19be4bcd3afbc47671325a38c99d18e470a571d08acb11cb529bb9d784e78554797786ae2df469268e3d7a685240e8007272a4825c74c601edf957895528ec7d0d16da2de9761e2e6b08b5fd7fc45bade2636d6d630c593749b89dce1c7505bf90ef58a49a36695cccf1b5af887c29235ff9326a96ce177c51940e391c073c1ea72a724d4cd582c61eb3a65a6ade1bb8f16dae826068dca8311c29d84925901dade99ea0a9cf1c566b706720fa15e69faab6bb6b12dc4b71005469661b95b391904f04e79ff78d68666ce9f2bb46b280412a0139ce69593606b5922e4715a5349cac438c6c69db00ae197ae2ba5684d91a11c92360673f850416e2181d2802ed9ccca40df4017e2632290c7d391d7f3ff3d29349ee07927ed6ff0007e2f1be836ff1134df0e0babed1ac1ed6f846a70d62c7264645c79e63e4edfbdb7183f228ae3ab0b49d8edc2d54a5696c7c93e2cf0ae93e3bb692ef42b76595a368ee52fa692496708007dccc77b296c73fc2c0f27765b95f3267b0b965b1e67e0cd361f0ff008c92ec19566b391e6ded70c63b730c4ec0b2b93e61f9472307d68bb329e8504d1db45d0fc37a9f8767b48a21a21796c668c489731b1749222a7aa3a86438e08720e031a6a4e3b18c5b8c1a479dfc46fd9de2d2a18bc4be0cbc33786f559646b6b5672f3da3a91bedf27fd6326f8fa72c193aee247ab83aed537cccf9fc5ae5adcc91e2efe149779dda72839e40907f8d767b732fac55ee7edf4716ade39f124763e0bf8a7f12e4bcd50dd6996eedf0b3484125cdbba99374af381e4a63703908cc08ec71e2e26bd6aeadcc7a984c14300ed41591eb70fc14d5b6b4d7de24f88d7570efbae2e26f0ff8777c8ffc4c775d9eff00862bcfa7869dbde91e9bc5558e8918fa87ecbda95ede3ea73f8e7e252cd274921f0df8511947619321e957f569af865a932c455acb925b3083f667f19c2bfbbf8d5f19941ea889e168c63db0d5d345d7a7bc8e49e5f86a9f122c3fecdfe3d90c65fe2bfc649a38f05146b7e1c80923d488df1f518a8a956bd6938d5775d0aa397e170d554e92b33b0f0cfc2ff1369fa8c13dc6b9f11365b47958f57f165a5c5bdcb631b648ed973ef9c804815e7e229c609729eaa97b4a8d9c6fc7dd3b41b69dacb5bd5f4b55d4216b6d474db8d45229658c6d6c0eaf132615d5d4119507041c1e4e64997cacf9b7c59fb147c4df1a29d67e14fc61f09dfc170a7c88757f12476572f11390ad25b45730dd73c86211db96603a5747b6a5d88e5665786bfe0987f1ba4d4e0d67e23fc40f0469d146c7f7afa95bccea3d018f4857ff00be64143ad0e85462ec7d05f0bbf65af843e07bd8356d7fc517de23bd27136fb7b88a123a6d670659a41ec258d7fd9ed512ad168a49a67bd35969d0f8760b1b496c60863444b6d3e08d6de189037dc8a3651b46d2781d49f7acbdac4a392f1cfed43e05f02f8fa3f046bda3cf6325ca27d985ca92b3839e519030c8c1cae720738c7357ece927cd0bdce6a94f9f73d57c29acd8dc490df5bd9da6e4c34693dda346cde8d85c8c7a7e1eb5d71c7ba51e59dd9ccf2cc3cf5ea76767776413cfb094ce96d179424277191f8dcc48cf25c9fceb39d78d5d8153952d16c51d562b69744bb8af515da73b70d9daa320eef6e768fc4d6b42d4dea675294e7b1f8f773f0834df0c7c57f1378335ad6353d4ad749f16dda2dcdba30b9d45d659301cb6000c4025b3c93919216bdaa71f69453479b27ec310e2cf92ff00e0a0df11ec7c57f1d3c4eda2e7fb2740957c3ba40326e016db2b348a467779973e7b93ee2a285093acee6b2f7ddd1f3b68d672bcc10a64f723a1af4a3068e6c454bab23781c7cac08faa9af51385b63c96a7715402db830e293946d6297328d9a33758752cc80f24d652b33ab0f78ee5451b5541ef51cacece64291838a39589eac4650dc11472b2649b42344a8d82a7f2a39591cb225906172ca48f6153ec993c931a8be61c2291f5a7c8e282d25a167c39b2e3c43676c7209b85c123d0e7fa563564941a2ed28ae667ebb7fc131fe16ea11fc00b2f12cf1c96b6da97882fa54649da3deaa522f9b0383ba16c13d36fbd7935b5676534dc6e7d27afc56835486de2b865b58ff762d482ea2704609c64efceecf3d4f435b42494122cbda5e8f7be1bb5bcf14db88adae028097464c82410c08047cd8c039ec7071c735ce8991ce783fc112ea6f25c2eb5611246086682567690e3ae76f1d79efee68534d926a5c5ed96856efa3db6a102cf71179535fdcc6c43a9c121586768c8cee1823a023835af2b27959d5f84b50b77f0ba697793e9c65566580a3cafe5f3d49d9f3fe3f9d7a58576a691e66360e4ed71be1ed43c37a2c0eb1db492b448154dbdb4efb58f5230b939fa715e8a93b1e5c68b83d59634cd4edee19a2b7d2afae39258b69570413c9c67673c51765f21078bee585c45a5c3a46aeec58baadbe972ed5caaf19200ec6b455e283d8c8952ceeec608ee1340d46eb03e78cca991f81938fa1c54cab45ec5c69490a52ffc5ad2595cf87ef61fb4dbb5bce6e1231b1186d3cab1c0c1e95cee6d3e61ba6dad4fcc5f1ee8d776fabdba269b792c86d83c8967a7cd39560837e444ac57e60793c57d66559b50c251e6a87e739c6418accb16fd8a3d2ff635fd863e357ed7bad1b7d07c2daa689a05933bdf7893c43a35cd959c608ce11e48c199c6d3f2a024719c039af3788bc40cb70f849d2516dcbd0f5b867c35ce7178d8564b6693dcfd25f137fc13c3e177c53f047826d7e277c40b9f10cbe08f0c43a3c13456682d6e1a3791da72a4860ff3e396db85f995b8c7e19478daa65f889f23b367f4155f0fe8e294635ad78dae9ee755f0c3f66af843f0eb52b21a6783f5265b39418e4d42eb65bc4ca72196da211c0adc7de284e40f4e7c2cc78d3118b9de723eb30dc0f93e1f08bd9c55ce73c6dfb117fc13f7c4c5351bdf83c20dd21f3ffb3b559e28d8862081189187504718adb03c6d9a61be191c789f0b30399d6e454eebbf434742fd8fbf61b3e1dbad0bc39fb3cc896771079526a50ea97065849eeb99783dba75383e95e854f11f3da725273d0f1ab7841c39424e8b4b98f92fe2cffc1207e316b5addfdffecc17de1ad63478ae9a2fecff0010eb32e9da942e1b84655b7923900047ceae338ced1cd7e8b95789d5e8508fd61bd7a9f9b710f83b86a55bdc5a7cf53e7afda77f63afda7ff647109f8f3a7f83741b49d01b5bfb9f10dc4b6f2138f912468624661c654163df815f5d97f1ec731972d39a3e16bf87982c1a6ea537a791e1f77f14bc2da0a24d7bf1cfc0b6cedcca96914974ebebc25ce49fc057d34b39c7469a7ce99e652e0dc0d5a8b968cacfabb125a7c61d035785db4ef8b5717c1572bfd8ff000f66949fc5d981ae7ab99e6138a7bfa1e8be15c8f0adc6ad35f3682d7e2278dae571a3d8fc52bc46ce1ed7e1d69f0c67e8d30c5713c5e63376e566d4322e15a52bcb917cce83e07eb9e2dd2bc417a92683ace91a8477315d69737881ad7ce965421e26c40028504007a7facc5726330f52cefa1e8fb4c1d0aa96166b97b1fa87e0cd5ed7e22782ec3c7be16d42358359d26deee206100c7e6461b630dc7241241041e41af06549a95ae7afeda9349a45ed3fc377373a9bd8cfae4f379aa4491b471a47d3fd94cd29d09f28a189a6a7b14af3c19a7f866eceb3168b04d04d08f35f77ddf5ea3a62bc7c452923d7c35783d8c6bfd32e354d385cd9c36d2209b0b04817a120ec18e4f4e067bfb560a0ec76f326457da5d8595a8b7d6f44b13008f64f1413e5a40470bce7e5c8e7a11efdb3a906813b9ccdd1b7d3ed2e2d60bcb7b6f321cd9c3e695653f3028d8001c83807af23d39cf958ce6b46f0d7db6069351d4a29a1254410c70a65093fdf0a198e0938e9c71544f2b2b6a9630d95fb5b5b60c58c8954e533c646477e73f81a4dd85cac9f4d2250197f5a71a8a0eec4d3b1a70821803e95b2ad16472b2fdbf071eb5b1916c4ca06306802680166561d28034ed0fcb8f514016ad89849568d1d5c90e8e701811820e4118fc0d672873109be7b23e40fda77f654d0fc23e366d7bc17e1abd8347d62d59ad66b394816d300a1a239e4ff000e0316cee51d7757256a6e2ae7bd859fbb667886a5a01d33c1dae6b5aa6a3a9cefa6e897515b45a8d927990b34053e5e7852f20c6d38e0e403c5731a567ca67bdae8da8fc30d1b42beb7951134c8a5fb65bd9192779c97648f09ca2296e41e0e01f5c0472be5386b8bdd2e7f0adcfc3cd7b4e92ea1d4af81bebb86eca4b6a54622b9419c064cf201f9a3774e8c08d233715638e7875d4f2cd47e1e7c478f509e383c31ae5e22ccc12eeda60d1ce3270e84c60953d41201c1e955ed6472bc1ebb1fd33695f083e2be9f7297561f09bc3f6d2468ea9247a1e991ed566deca08e80b0dc47427939af9dfaf3ee7d3bc344ddb6f86ff1c664679b47d2adc96fb874fd3cf6ea30a7147f6849751ac2c5892fc2bf8ce416f3f4853fec69f6191ff8e52fed19772961237196df0e3e37ace219d2c194f43f66d3d73f9a51fda32ee57d56269ffc2bcf8d312ed88db0da3e50bf60e3f0d98a4f1edf53195049d8a1abfc26f8d1ae984dcdca23db399220b73670ef3b48da4c6a095e73827a807b5635316e7612a6a07e70ff00c1627e117c5ff095847f17fc17a74b67e24f075a99b528ade45cdf69e46304212264405b9e7e56901c135b527ccb51b3e0df803ff0576f8a3f0ef4a97c11afeb4f069324d2484421cb5a48e413e5ec7fb83e6fbc303f2abb3333db6e7fe0a69a96b3a4a5ee8ba9f882688021a54bb9d0cb83f7b91c8fd296a44a6d3396d4bfe0a7daf86f24691e279b3ff2cd357b804fe4327f1a695c709b72285d7fc1417c51ac49106f0b78961911b7c53cd7f73285f6203a647b67f0a7c9736ba392f8e7fb5378cfc77a0d9eaed63af0d4b4abc175617d2e551083f347c93b5241f2905bb024715b25a19bdcfa27f635fdbc26f11e9569a0ebba93c6228d7ece5a169dd98ee26262d2672a781fec80bc11919d48736a44a7ca7dabf097f68817cd6c2df54795439796dcda9632c4080e517b32e7b63760e07385e672945e86918a96acf40f899fb527c0bf87fe19fedcf187c45b3b38da3f2a3b7889924b82dff3cd132cec0e33b41c7702bd0c3529cd99d5a9081f9e3fb467c4ed01756f881fb63d8dd3d89b6b37bad1ec380d25d796905b0e0903e710b9f404fa57bf42f08281e1626d52ab99f961e2fbd3e54f65ab3b4d34409999db25a52d92c7dc9279af468535cd738dca5276462e9f61732bfdaa0b948f3c004d75f2194ab2a7b9652df5367da6f08f728c6b632f6d4d8e920bd897325c239ec591854c8d2138332f502ed328668cf3ce37549d5170e5d06750010bc74c1cd003b00aee3d6800854bcca83a13cd000c662e4647071d280079242bcdd27fdf35a004524c0b15915b03a81532d8ce5f11abe00b5925f1bd9863c29dfc7ae0d70d6f84d27fc33f6c7fe09ffa16b76ffb21781a2b3d261bc9d63bbbbb68e42bd24bbb82a7e6ea76b0ee39af2ea1d747e03daa1d320f0d05d475bd0905cca9be611c6ae9183d558331248f5fba4f6ab5b0ce8ecbc332f8c34796e9ad44b636d80b98f2586012589efcf34c991cc78cd1fc3b736d6ba05a5b3c0e0094431f971a027196653c1e7a77a716ae489a79d3f56b57be9f4fd31deca5057cc00ab3e4aae3d4648c9e481935d6069695e2ad2b472da6de3082473b9523b798eeced3f2945619e4f19cfb575d09351b1e563212954762ed86bb7bac069ec964f225ba6598adabf207fbc07f3aec53763815293958b09e20d5d750fecfb5f0a6a185e92ef458cf6ce189e94f9d9a7d5e46b4536a9750cba8b699b5914893fe2631ee1edcd66e6ee696b6873519f1fc9a8c8c9a1dac501c981a6be491e4c7fb832052e7606ce9da27c4cb8b6b392c2cf4a77ba2ce02452b162bc6013df3ce7dab3ab885469b9be87461b0d3c5578d25f69d8e37c0bfb387ecedf0125bef197c55d0ed3c55ad09a6b9926bf52d636aacecc1443f764da5b04b8392320282147c3e65c495653f67067f4df09784142b50a556a46ee56e8544ff0082a1f85fc31e3cb7f0f59f87af355b08eceec6d82c71676a2281c80400029caede318fc2be5733cc29d24a559ddb3f6bc5786384cbf094e8d28aa7792f7975287837fe0b2bf0425f014ba65bd8eac9aea0966fec6b4d226924964c128b1b47f260b154c9208ce70715f9ee3f11879e37daa7a58f3732f0d330c3e64d4758b6af2f2388f1a7fc1523e27e9df0e265f125a47a75cbe9f04d0bcd28927966900628918914e15b0a4614008e41e156b8dd5c24fa9b52e09c3623318e1307f1f531be0aff00c15abc5ffd951f842e3e0add78aa6174ed6f1dbb059e718dc772a2bedc12cddc6188209e6878b705a1f579878472c3e17eb2f15ecdf63d4dbfe0acdf06ad3c39771be817f6da84926c5b28894ba8ce0060e36a9531b02429f94ed4c124e0cd1c4cb1356d7d8f9aa1e16e6d8cc647d8454d6ed92fc04fdb734ffd9eb4ed43c6bf153c4f7fabd8f8ab5e81f499540dc8922000a4592f90a55c938c86c019183d8f359e2684f0c9ea8cb3ee08ccb3b97d5a8e1d4654f767d15e39f8dbfb3afed3fe14b9f86de278bc33e2ad22f2e7ec7a9787b5eb58ee63fb4ec120408e1b332f246d4246d63d471ea70e63eae0310b9e67e479bf044e8529d0c4d3d7d0fcdafda03f671d1be047c55d67c1ba06936e6c22265d36e6dad638b7c0c46d3b5500040382063906bfacf83aa61335c1d3727d0fe20f10f2fcd326cdea469ce6a09e96bec70e2cefa48c4297b3c6179197603f9d7e890c161f0d2fddab9f9554c7e36555ba7524df5bb2a5c695710ca6e6eee64943fcaacf21fbdef5ba82bfc28f1b135eb4a77a9377317c5f047637f63af08d15d0ed99c2803cb2769e9d300fe82bcacf3090ab07c8ac7d670de65c95d7b59367db9ff04edf17d9f89fc05aafc39bcfb425d68b726e6cb11e58dadd334a39f4597cfc7fb3b6bf38ab42587aae2cfdb2856a38aa2a74f63de6decd34a7b9b89270d2b663456382a77019fa6323eb556bc6c3e5b487ea263baf055e584b6e16485570d29fe13c6457958a82b9ea610e41fe1d25947045677d148d24824c47180c486191d71b7f1efd2bcfd8f516c41aedb68771ae3693732490bfd9e2784c52361582ed652486538653db1ce33d2b1aa5c4e5b52f0f4133b4dae3e9972b1826749202a678cf0130338246791ec71cd62514bc59a159cf6f199ad0595ba5b6db47b790ef008c0450319ebdfa75a00f3ad7bc69a358e953f878eb0ad71653079a497f88e0f3bb1c7dec303e9d475a896e06a6817ab756515dc4576c881c6c3b860f4e471532d8996c6cc0fbd778ea38ab8125fb53bb04d772d8e77b93d005bb6384047a5005db599f03a74a00baac5800e0107a82383419bf76571bace83a6f8a7469fc37ad806dee179b8922121b775c959402392a7b77048ea6b9b15f023b30b59fb4b1f18fede1e0bf14fc32f87be26d4b5ff0cbda5d5ddd59d869d710ca3ecf3a4d70933f9488b80a62b59981ee0f6ce2b80f66494e2876b1f0eaf74ad2cc5e2dd0f4f8e79add02c4d68c6368805c798071b09e013f74ae4506ea0ac791f8e7c0306a97fa9dadc3dc4db0a5b5bcb77a547125ac38dc56475cb1009da3820820671800ba2274aece2ffe143787a6fdf45f12f4c08ff3281001c1e9c16c8a08f607f4c51ead6cac2236926edc06093fd2be0b9e9f73dd9724fa165756b79080ba5f983b396600f38e0f7a39e9f721528a7b0497cad180964ca5ba008495a39e9f73a630495d4422b9689773c20ab7476424715128c27d4a5183f891c57c5ff8e63e1fc5068fa3e9d6cda84cbe63195711c4beac78e4f50322bd0a18352826705694632691e0df147fe0a2b37c27699bc4bade8f15c451798f60b622590af66396da8bfed3b2a9fef575fd51c16872395cf953e3bffc154fc63f1e2082d34df8316ba96996aecb1ea37f633c65b3c3286458a168dbee9513b8e3d704d468c94912f63e57f0f7ec57fb3cfc41f135d788ee7f646d2b509af2e25babd86cf57d4a38d599831cc76fabb88876d983924607415ebd9181f48fc0dfd8c3f623f19c56fe0fbafd9ace9774fe62e996efe2ad5de3ba78d77c91425ae559250bf3794ea0e3904e45653dcb8ec7a5597fc137bf613b4b67b997e04693145142f35d4b73ae6a4de4428bbde425ae8edc2f393c53a34557ad1a6f66d206e296a7c7ff000e23f819f13bc65a94507fc1373c2be1ed0edee9e1d26eb5fb6d466bfd4a20c42cf15b9c19a31c3390c022963963915fa962f8432bc161e9c69493525a9e2e2719530b2e68c59eb3fb047c29f097ed11fb40f887c0be33fd82bc0fe1bf0af85f4b9269bc4d6d6972eb78a5ca2792d2a2860ea8cc1881809820138af3f88b24cbf2cc241d077ba47261b31c4d7a9ef23dcbf6a7ff00825c7c0bf16fc26fb5fecf1f0c749f08f8e74c5fed2d3b52d36d444b7536777d9ee11782a5792c06e8db0e33cab7c228462aeba9ef723714cfcedf823ff0511fdb43c0facf88fe1178e7e17e8b64fa06a42d2eed751d3ee84d1de3e7118559461f0a5891c15008ec4be54f43473508d8e9fc39aa7887f699f8b13f8ebc6e0457daa18d66b7b1b49220f1c39f950024a80324863824b135df865c9b9e6d67ce8c1ff829f78fe2f0f781bc3bf0bb4cd3dec7ed774756b9b1982ab9b2b1568ad919474f36e25998fab47ed5dd07cd56e70c95b43f3c756bcfed5bc96f2404969cf41fc3dabd46af4ce57a32e5abcb144121d3e7907aaa8c7ea6b484199ca853a9bb256bbba51fbbd2aeb776dd20c53e69a32fab52ee56bb96e6e4879ec5d31c00d20a69b7b9a428d38e899932ce8f74585bb63a7faca675c611512613c6a9b45b37fdfc1fe1400d13a6368b7941ec7cf18fcb14009e6c9921e20d8ec48fea28d4341cb2878c62d57fefa1fe1400c77247fc7b45f813400892bc64b7d963c0c67233fd687b01d5fc20b26bff001637976e8a12dd8bb38e00c1f735c788f8183d8fde7fd923c3cbe1afd9bfc01a78b68e3483c1560cfe5901f7c90a487764f3c93db8af0f107a30f80ebb58b13069526ad2ca905aacebbae766f3bf700107767391c741531f85151d8dcf0a4fe227d5adceb56f1d84cb6f22e9b04484f9659703cd23bb9e48393cd4d46f42ba1b3a8da695a945700c10c177628bfdaba7476441917d42e30fc9ce79c67b56b4b7460f71be19f07da6a1e19bcb6bb458624d510955bdf2f72a86dac093f2924b600ec056d3fe21449a37872f1edafc4379e44306a4c90fdb1487930898e831d39c8ea0e6bdcc2c92a28f23308b722edef83eee2d385bbcf0cb74f7ef6e55f3b58aaaf20eded9239ae873563ce841d8a1ac7846f2fec628b46d5fed12b5eaa2cd131d91611b70607f8462b6a7345f2b2d787bc25abcff006895f55b392ddc79bf6ab4dcaabe5860c08c75cb0fc29392b9a2a6cbf6fa0c7a888618efa17dd1abacb2ae23937121718192720f5acead4a74e9ba92e86d87c1d4af5d286acadfb5178ef46fd913e085df8b27be5baf19dce9ef67a3442da4922d3a49c3e5f62231c27cf21c8c02a001c861f94f10f11ce55dc63b1fbff865c018acdf19075a1eedd7e67e67f8d3c77f16fe316af0de5dfc47b075d4d659b4fb1bb428f2db61a3f3194211f3046f973d38af85c6e749f53fd1ae1ccab2ac060e2e10bf2349fe079d6a56fe3ed32da0f1b3f8a34e9decb4e8f549f4a442267b26431c9b7e5032237933d324935e04f358d69a4d9c3c5183c14f0b2f671bcdb7f99ddfeca1fb5b7863e0c784e5f867f11bc152dcd9e9d34d6a25b1894b3625dd961b932d83c1c938279e40af9bcee2e55eeba9f293e0ccd7896842a61ef78e8f73b3f157edcbf09bc5f09f045ffc2b58bc352ba9ba6b9d2a2b99a755c15011e40a8c1864b1dd9c9381819f0a8d0961a5cecd17851c43805ed296e79dfc3ef8f9f0c7c1df14edfc59e1ff000aeafa34a961736d79aae950da44e4ca85219e2b40be523c6080c7710fd71cd7ab89a8bea973d1c5f0071362702e84aaf3cbb1e81fb437ed69f0ff00c75e1dd4bc2be16b14d7df5b4b795754d57468ece7d3a54003b178fe69e56201dd950a015e41003c8e9d29e36339bd2d635e07f0ef88b0998aab8a97b35176e5fe6d373d47f673f84df07bf681fd9a34ed1fc6372865d304b6b67ab5bc1225d69f7dba4981f3b0488c2052770d9f2ae4f35e7e719857c9b1d27495d48f81e3dcd71dc29c615654de8fecf7b9e67f1efe15f83fc1de01d4b49f865e25d77c4dad6a3ab44baa456ba6b9297b6ce865958467f724acac41c10ccac530b8cfa784ce678b70f69a1e8f0ee6d88c766f49e654a31a2a3a5edaa67d3bfb1b5b7873f6b9fd9747c1ff008bda95cc9f12bc34d7274ed4b5b626f2682591e4822949e5e2c12013d030c706bfa478233a8e59858b83dcfe62f1dfc2fc0d6c7d5cc32c85e8cd35a6a91f3df8abe13ebbe1dd5af741bcd35c5dd8b98e44756243862a473f4afe86ca731a78ac22aadeacff003a33fe19595e3a58682d8e65fc09abff00674c750d3a455073845c1af6e9e2a9f73e6ea6512ec71fe37d06de6d2e7b58d3cc59a2056375f9b2072a7d88c8ad311516269591cd84a4f0d8c4d9dcfec25f161bc17f187c353dd5c85b4d4e53a0ea32b9e04774cbb1d8ff00d7d470e0f60cdeb5f9ee698774b10cfdb726aeaa61d58fd13934b8357b87b4d4f764b1403033b8f4ce7df15e14a76763d8953bcee4525b4d222e9cc64891ed0a9cc7fc41b00f1e99af2b1733d5c242c717278bb59b4bebcd29824d1acd1b4450af11f3b891d703073d39f5ae75b1e8ec6dc73e9d71e1bfecdb4bc2f677119106d24b0ce4f6e71cf5edcd6557a1a53391d6740d0adad9ec57c431157600229ce39e4171c03dbd8d60f6343265d0679f4e413d8389a161e51b62ac3cb07ef6031c8ed9c6726b003ce7c51e0ad0ee3c5121d7ad64df6f6de74303005c8eed81fc59e8de9db20506728dd9cf6896b1e897afa54566d1444ef553108d613dd3a01cfde1f8d543e233945a47576ec270a91f2715b199a56acf800fe35d6b6227f0978100a934cc16ecb36d22ab06278a0a2ec522b743d6801e1a456c2e319a00b0a494c3f61c0f5ed59d5f8497a1f3effc145a7d67e2ccff00043f663d28bb5ff89fe22e55837ccd691471c2448072c81ae9f07f0ae199df876cfa4bf699fd92d6f348bad56d34d2d6f6cc8fe4c8ac76c6198b281904e4161c107eef3f3135c27bcb63e61d4ec8417d6da4e81a347343611bd9433240c935a33ae1e34954ef743b860672012473c1e9c365d89c5cf9e9ec71e3f37c1e5b41fb5dce1aeff657f127daa5f2be23e8b12f98db62729b9067ee9fde751d2bd6fec3c79f1af8ef2ebee7b6f88ff6a4f8ba4a1b2fdb9afe58ae8ecb845d76e6078ffdb5c3aaa8f4387e6bf1ff00a8e30fe8c8c7258b35e1fdaef56b7b3874193f6abd6eee5823122de9f17cca01c1016425b74b8383c607349e0b188d39b2629eb1fb57bf88b518fc512fed7bafdacb6f1ec1158f8c2ea3b6b8507249855fab1e37331c63d292c1631f42d62327a7ad867c2dfda2f5df1d7895fc37a27c7df17ea7aa6b32ba5be92fe3cb8b892319cb32b0729122a863800918ea2bba865f5bed186615727f61cf4f73e83f19f877c65a36831f84b48d6efe7bc82df6b6a9aadebdc4fb76e0832c849f3173f33313b4fcabf3676fb7493a5051ec7e795f96ad5725b33c43c5bf0134cd26fd6e359d3259aec8f31ae1a4d923c8ec0099a63b9a2249c60869dfa657815d0aa49987b3513d4be1e7ec91ad784fc271fc44d47e06eb974f3a866bfb5b9b6b79a35ce37ac7731cb39ce3397743cf403003751a570e54cedb4ff8103c556d147a8df23472e9ad7da55fbdba5bded94899c248f1a2fcdf2c8010030740771acfeb953b0bd8c4d5b3f83096b25b6b5a2c491ea5716165a8955030ba847730c6ae07f0821e4e0632af8ec309626727aa17b348eefe2d7c01d5b58d37c59a478365b586e2ff0050169ba6f37cbfb2e733479851d943a650f1f778e0f35d542ac94ee8db0f1a2aa7ef363c4a4ff82785ceabe24b5f14f8a75b9af6e2d23658ad535cd656de10ebf322ec883a2e4e4286e81412dce7db866f8d8d2e472bfa9eb3a995ca3670b9ee5f087e00597c0cf8532d8dddcbcf7fa9de096fee1a59242d6f1eddb1ee972c1090831f2e4bc9c0e839eb63b135d5aa4ae7cf62b0d86a7272a6b739ef8bde1cd43c67f0eefa3d2bc5577a2eaa6ed6e34ad52c1c892dee15f7062072c9c6d65e8783c6057324a34e52bea7146b4a124ba33f353fe0a2b61e17f196ab0fc458f49b6d13e206932dadaf8a4592836dacc8c8d1f9c0a8c995100c67aafcb9caad77e130eaad25391c52c5a9d774cb1fb3b782748f0a1d3a0f28cd7f38585618e1d934bbc1dca091c91dbdf19c8e2bb5508a2e568ec7c1dff051bf8cf6bf143f683f19f8a34abe4b8b3d2e71a0e932a1fdd791687ca2c98fe169bcd981ee5c9ade9528a7739e694a5a9f31d8cbe64d1451b9c91cfbd7a317a58e79c2105ccce8edd5e340bbfa7b0ae9a6cf16ad66e7640b2867dad1afd715bfbbd88f7bb953529bca8b74480e0e79158d5b5f43a682bfc4cc55590932792d9073c566b567a31e4b6e0259bf8add8569cb11f343a92212704ae3d8d1cb11e8f61c540cb67a8a396203637554c13472c4047618fddf27de8e588ae84f98c2db87395fe7513b4760ba3b9f82d6b3cda86a1347857484227be49af3f1126a0cba6a339a47f427e17d3344f02780b4dd12492e520b1d2ad20698dab4c17cb8447821178fb99cfbf4af1aafbdb9e8a8a4ac6cf84f50d32eacc5de1a646b89134bba7b73b6d5d90677a0c6010073ebcfb565cd6d012b224d1f5a86c74db8bcd13498755890b0ba9258fcb7b927efba2b13b4280708bc939cd0df30ca16faa68d1dc4b676ba925c6aef19bab19a7b53fe8d6e4afeee5f4f95495c8f97391d401a41d990e2b72e8f186b1a647f64d222d3750b793f7b3492125570bf2f99b5b9182b9c73963d2ba5abcae67cccdcd07c51e2abeb1bf9e582c66125e99822420a467cb5dbb39ce3e53c1af5b0728ca0933ccc6ca77d026f17dfbc7188f4c9180d41ae2331105a3908008c1278e2bb1c29a76b9c34bda5ad620b6f196ab2cf2b59da5bdb1174484861c7cf8e8debb81607df1549417534b4fb0e7f166a59367a7e9f142646784411db954c3b7247fb4571c9e28b41f52af53b110f1e3fc3cb5bad42ee005f44d396551f67122bedc32e790301e403391ce3af43e1e7f5e9e1b01295cfbcf0f72ffed2e20842a7c3d4f88fe2af8a3e3ffed3ff00b4acef71a65a3dc476f1cf1cba8b4a534d897e45650b205059199c86196dc3236a647f3e6699ca839395acf73fd00e17970fe4396c6aa7afc8f20f1037c6ef04fc441f0ff59f0ae9f7dace936bb74cd4469d18927b2030191908053665b20139ddcd7c7e27175710ef495d1fb7f0a57e1fc46513c4aa92e49cbbadf43806f1a6b0730dcdb4299d13fb265478704438c1c8cfdeebcfe95e5d3ad52189e67d0fd2e3c3397d5c2c6a4e3abd57cc875dd277e99a4f8b23b6021d52c44570abff002caeedcf9522b7bb4620933dfcc3e95dd8ea92af05347ccf04629e0b37c5e5d5d2524db8fa159ece3ba89598a2e06d000c5554c23c560b9d3d4fd4a841c5394cb973a7582780edf518e11f685d66689e4efb3c98c85fa02735e6621bfa9d8f80829d5e2ca94a32718c629e9ddb665da2196e41c76ebbb0056780539d68c6f647dae3a82a349576eee3addf4475ba1fc51f8a5f08ef26b7f007c44d5b4b4b8b553343697ae91e5d549223ced0d8d9f363767bf15db9ca8c5a8b49fa9f0b5b85b8778af193c6e36829a7a26753fb3e7ed6fe33f814fac083c3965aea6b176b7974352918ca665560cc24218fccac4377c0e08e73e7d38aa966f4b763e5b8cbc2dcb335ab43ea6dc3d9ab68ed7bf71da57ed4ff0011b46f8c4df1afc2b769a25f08e18c595802b02c11c6a8b111c12b8504f4e4e78afd4f84f37a708aa4dec1ff0010b32bc3f094b27a979452727276724fc9db63ed8bdf125bfc608ad7e2a6abe1c10cde26d3adb509e33c086692152e3d793cf27d6bfa7b866bd59e053e87f8cbe31e4d82ca38bf1186a3b293d4e6fc49a5e9da64125a476e8548e4633dabeb69d4a87e2d5a108dcf9e3e23dd59e95e26fb4c3628115f2cae3e500f1fd6be97069a89f138f4a15ee8f35b4b71e1ff0014dfe9514c60b7ba2a6c98360824e060f66563191e9cfbd7cfe7941ceb73791f7bc2f8ee7a4a0fb9fa7ff03fe25cff0017fe16e85f12d0e2f350b045d4a38e40de55fc65a1b84c7a79a8c47aa907be6bf3ec539d3a87e8fcb16932f784f5996f6f2fedf55370258a5730c6f18c004f38000c0af26bcdcdea7ad429c631b9c7f8a2caff00c3d6da9df2ccd025ccc21b5980f9a13b839da3b70a33eed5a47e14684569e26f1045e119f4b9f4cb3beb711bce85e262703976014fbe76e3b922b1aced634a647a0f89b51d7efe5bfb3f07259e9be4656eae1c0977aae181c020fcddba8e466b9db76343235ed7b4fb69a4b1f19da7f67bb159204926c4522938dc0ab0656c1c7a735915ca8e6fc41e0eb7376fa9e83acb48b1c2d25c432cfe6c8f186e0239ced2003f29eb8cd4b6d3348d38b4725aba4d75e211aa6a3a94b6f66ea638bc91b63f35b94c82320723a01dfda9c6766455a71e535f45bc3e6283195ff65ce597d891d48ae88b4ce192b1b36d3b64e00eb54abcaf625aba2fc4c652037e95d10939232945459614a28001e455924f6f233707b0e3140138badac14e3ad005ab79c4d32c64800f53e82b2abf0825ccec7cd7aeeb179f11bfe0a95e165d31e6fb3fc36f09eff397f8279124b862bee5ae2d3df29ec2b96514cebbba4ae8fd3497e2ef83b5ff00859378e7c47791c11d8424eb10c85408db9c6d0dd431e9ce0739fba48cb0d80c4e267a6c198e7d472bc1fb4abf11f9c3fb47fed17e14d5a2d493e08bfd974d8e4927d575f9f623dbc1965223751b54150b96cee238e335f4d86a7fd9d1e583bf53e0f1598ffacd4dce4b952d343e47bafdaa3e1241732411b6b570a8e556e11240b2807ef0dd38383d7900f3c815ddfda55fc8f09f0a65d7f8e5f8177c19ff00048efdab3c617f1d8ea1f0c6f3434f2b6fda359d64342a0738608ccd93d3807ad7e7ca7419fd0d15524ae989aeff00c1223f6a7d3f54b8d260f84125e2db4c562beb5d7a330dc0e3f789e6b2b007dc03c544e7878ee0a355ecc8a3ff0082417ed50d1863f04a556c72e75bb3271df8f33d2929d0946e8bf67517c6f43efeff0082467fc13222fd91f4bd57f68cf8c3e12834ff001dead245a6785f4f376b3ff65c134a00b8ca120bbba6e039c2c207735c956725f0238aacaab9593d0fa82d75bd0f5a974d94e9f1ac7abea176d0db72c26fb29710440ff78b21cf762d9ea0d73ae66aecd134958c1d13e347c31f08eb7a2f8b345f0c0f10ea3637d38d674fb3ba45b96762db6ebca6c09700a6dec0ab804115718bb83d763a6f895ff0509d3358493c25e04f05df689737308432eb4d0cb7bb7772ab690c8ecbb8670d232e78c03926aa5095856641a37c4e5d7f425d6bc8748e387c8daacaff006641822df00732b1072319073ef8e4e5916775e01ba6d42ea0d4750b5024c6d8a28cf123a1c47b4ff723383eee48e76f170a737ad8ce4d26777aee930416c751d46e205b7b5479ee2ea6d5ee6db000259dc47c7be727815d94938bd489d58d38f33384f03fc74f847f107c7d69f0fbc19abc9a9decd1199becf26a455235e48dd346ab9e8719c907debd0f6353939ada1c0b37c24a7c8b56775f19f505b3d052cedd0159e411c61471b10127a762e5bf03591d5564eaa563e59d63e207c5ab9f89d3e85a56b30c1636d2c4b75637f631b231769079b148a371f910168c80a0b205209cd44b99c925b18ca941c6ecfcf1fdab3c39ac685fb52c9e03b1d725bcb7d36f96f3507332caed713e2655618daee8a549603f8feee4951f531f64b09151dfa9f371a52862dca45fbbf8fde34f87bf0a3c47f196eed1906816ed1e91732da471c8b7ac7cbb5f281196633bc409c81b558e064e21352d8eb9c9b3f2d7e26c9fd9b15a684cc370044db4939209c93ee6ba21a6e249f2dd987a4595bc5279c877fcb80057540e4c54a2e16b9a3f6f994e0dac83d30c2ba60d24714e95071df514deb80490df881473b21423d0ab71761a333b1181d01ef49bb9b53a4d99f36ab3b0c24a36fa28e691d94e942fab0b4bb792700b3301fdea8bb156a715b3269e5125c31850119e314f9a44c5a8c15c588c809dd18e9de973343f6902556876fcf6c09f514b9df70f691104017903355cd230e663d20561961dfd2949b68399ad4f56fd977c307c51e30b3d0d146754d66d2d5773000ee902e327a724570625a54d9d185e695547ef6f83fc41ab6bea6e23d1665924258a17c281f3609008c701bb11c62bc8934cf5ecc51aada1d4af5f48b522078e34bb85d30af20ea0af601b1f4c1ac5a770b31ffdb777a5785cdf788122b7fb14867b4b68368595d46225de3b6492d93c80477aa8a616670f71e32bbf10c33d8788e0b5cc8de649f618cabcae304bb103a13ce0773c62ad277134ec6a69504b6ffda3ab5feb11da594f6c123b651b5a594c60600242b28396e7e5c6dc6062ba6e8c7964771e1ed4a5bbf0a5ccda4d9c7668d22ac10c120730a2800027279c03923a735ea60a3071bb67978e94a0ec5ff0a78f35abfbe65d4a188895d879e9719fdd924e71f53c1e0d7a6e945bb9c14ea4ac5d9fc59a9dd5dce9f661098220308bcb36df5ee73dfad1ec51afb590b0f8c6eacf4b92e26b60ceb2b185d94900150bb791d8e4d1ec509576dd91e71f17fe2e6a1e16f0740969a2d94f6935ec52dddb3a9df22a48a821195394fde798ddff00d1c76c91f9778978dfa8e12104ed74cfd6fc2ac2cf1199ce6f4b1e27abfed67f07be18f8f22f0f78a345bc3a85ce8d722f753b4b778a60c5dcc5114931bc6d76049c8531eee77311fce58a9d4c553b3d99fd7b92f0863f33cae72a4f9b9756afd0f31ff828ef86fc7d3f8b340f8a177e1d96cb43d5f48dba6c593bedcb28964590af0bcc8c00cf446c00b8039f075274bf7715748fd5fc23c465f5b0d5727c4ced28bbd9f747cdbac68d75a588653224d14d089229e1fbae31f30e7ba9e083df1ea32f154e52f7a99fd2b9667343190951bda74d5adf97a9b3e1b93fb7bc3da9781622b24b237f68e91bb3937291ed921fa490e7fe070c78eb574796ad174e72b3e87c371461b119766b87ce2946d75cb3f24df539e8e4591f7c67e56c914b0aabd18ba7291faae1b154f170a7283d1ab9bb77081f0c2d6741ccde229fff001db6849ffd087e74ebc6f86e55b9f0942ac69f17d69bdb922be69b2bf84f45b4bcbbb8bcd6900b0b083ed17b206ce40236c7c720bb6231feff00b1a30518d39294ba1e87176790a586861e8bbcaa696f2ee54b8fed6d775649ae23335edec9f2aa2e4c9231e1540eb92701475e82bab33a4eba528ea7660ab60b24ca611a92492d7e64da95bdae909fd8514cb2dc1c1bc9e371b55b3f2c7c70761c1247de6e3a0e7cda8952a56ea71e0aa57c6ca55e4f961d0d2f85be0c3f103c79a6786af3544d3f4cbabf861bdbe9e33e5c5e6385d991cee209017a935d7914abc3169aeacf238b7892196e4d5a3467cd53d9bf5d99f76f8e759ff841fc497de09d04b3d8e8f726c6dc82708919d8aa7dc6dfd6bfb83835a96494d3dcff0008fc48cdaae6fc4d8ac44f7e792fc4e6bc43e2963a60bcf314b02778671c735f694a936cfcaf115124cf15f89d76be2369248ad1537f00a377afa7c2c5461a9f1f8f6a6af13cd3c560cd6d0dc4b1b036876cec39213a311eac14923dc0ae6cc6846545cdec7470e63a587c428d476d4fabbfe097df16ef256f11fc1b79115e48d359b039e4b9c5adcc600f464b77f732bf6afcaf3382737ca7ee984af0a94e3aee7d3f36950da5c476316bed697974a5635725448e49e177363f004fd2be72ac2499f434649c0cbd6fc0fe2fbbd08e9fa86a8b3aaccd22497281407e383803f9568a4921f32313c31e02d5b518e5b8bef1126e81f02385ce01ebdb1edd6b0ad28cad634a6d3b963c457b24e34dd32daeac625d3dd0c31dd0607cdde464b0ed80003db1ce41ac19aadccbf1cf85b51f1d68ad6335b69ad777454285999f76369c9000f90100ee1f37151666863e9fa1dbf87f4ab9f0e6a3a7bcf294324ef026046ab9030e797c2f009f9b1b8f6159cb466d0f84e3e28f48ba64f0e25ee2529b9126b732464063cee894807a632d8f6a893b21544e51b226bad1ae348b88cdc471a0bc4dc1a3390ac0818e7b9193dba5542a58f3aac5c5ea5c89941600e39ada3ab332fda9deb853935dd4be132a9b9600238ef5a104a1885001c1ef4012248a146e393ef4b992026b7b8b6497ced46e560b4891a4be958e0240aa4c849ec020624fa0358d69c54752a2bde3c07f60df0eeafe36f1efc49fda63c4713c5278b3c4b3ff0064c0dc986d99fcc58971d1514db463a1fdd9cf7ac92e6d8dea3bc343da7e3ef82b51f88ff0e2e34cb2bdba373665ae21b3b6bb6892ef6f22290f2190e3a15241c608ad6139613dda72ba3c5c5e0218da4bda6afb1f96ff00b5c681f147453a5789cb93e1b9a595174b299fb3de82f9f301ea323e5c8f51d41aefa124e17bdc50c251a349454394f9ddbe2278be3631cbe12b1765386611b727d786c56dcc66e850bfc47e8e6a7ff07186996f7ad6ba07ec9179384fbcb73e2a11c83fe02b6c47eb5f16b25c6cdda27e852cc70185d2521b6bff00071cdba1c5f7ec637aef9e0a78a91863fe056e29cb8633392bb0fedcca9527696a7d19ff0004dcff0082a47893fe0a31fb4341f057c35fb2bdde87a559d949a8f897c4173e205923b1b28f68384108dceeec88ab91f7f77f09ae1960713829f2cf60a589a5898f34247d803c67aff8d3c5be231a45b6ed492f6c356f0c69ad205497ec32332d903d033c52381ea73c6700d9a1e0373e23bbd56c26d374a9752d77c3ba66af25eaa683204d6fc373392d24335a1746233b3182a720bc6cfbcd43dc0c4d47e247c21b484d9cf71a66b1712cccc57c4bf07b5096e1246392bba258e3de4fb1e792c7b5d3dca899ba76a37daa6bb168961a24962d7e710da41a747a5bdc2e07dd8e312dcba631f2aa03d3a6726dec51efbf0ffc03a8785f4e8a4f1d5eb9bc7910da7876d8eebb0dc2a29313116a8724b292d2be4e4a12457201f47780fc1b368aa86fa38fed76fe5f9b15b2288602148485076544247b9727d2ba297c2653dcd6f88de058bc7fe0cbff00065f6afa9d843a8c1e54d73a45c08675524642b90d8cf43c7427a75ae9a359d09f3a8f35ba1e766383963f0ae8c65cb7ea61fc16f803a1fc2ad7752f14e9be2cd7756d565b648a1935ebd498c085989f282a284cb7cccd8258aaf3c55cf349631fb392e53c9c164dfd972f6937cc49f136fad27d70d8da92d05844b02073bbe7c67273d7d3df34256d0f7d35257479ef8abc15a1f896c25b9d42d765d281e45edbf122c839e7bb0c02319e723a538fc488abb1f90be2df02f8dfc29fb4d78a13e34de236b0faf4d3ea8b6d2798886495190a1ff9e7e598c0ee148c8e315ef51fe0b3c2affc4303fe0a31e3e7d27e1b780fe18ea5e2086ee4d72e67d626b5b5804691595a178ed8e074df33cc70727f728734e80cfce5f135cdfeabe257bcb8fde07c85c9e9ea7f3af4230e6629ced11fa7402150a1b35d518d8f2eaceecb372db3071daa8c56acad7774b1c4463af7a0da10bb2bcc0c96a228c823d49c5075c63ca8a0f6ac80927a77a4f62d6e4b60aa8cce5ba62a09a84f6e47984fbd062f62fac6ac3345ae72cdd85f2568e433e724f256b6f663e7156355ed43a7a1a27781f437fc13ebc3a35cf8f1e06d316dfccfb478cf4e2571d317084fe583cfb579b8d85a8b67a381f8cfdb98e17d06c6f6f6c2cd9a3b4636d2931abbc8c0a1237302481bb0093d3f5f0cf5c7e89a46b37f753bfd863368f336f9e594c729627a6c18c0f418f6c0a0097c65a1b9d3adacec7438afdf2e268a590a22640c1e327d7bd5440c8d034ad3fc3562b67a9f87556edd5b6c8bf36dc9ce0900741dbf5aa5b8199ab693a9c9ac5ad9dae9515ec5290a8ae81002725b2cbf781ea73efea31a199d5f856c74bb2d2278bfb1f61595c4aa8e4e1d40057078fa9f5cd7a983f80f1b31f88b562845f5cc1656f046b1c7ba2551b72707bf1db1f9d7b51d8f3a9fc236ce0bdb8bd13dd3c31c6e0390eaade6120719c55146a5ee9f24b650c7a6c51ef1290c1d381c13c8cd0654fe36737f11fc0f75adf8523b4b7d36daeb55b3b817164629042c1772ef04f1f2e3193918c64738afce3c46ca96372cf6d6f8533f5af0df33860b1ee9bde56386f8b1fb367c3afda3088fc61e0fbcb74b2b76fec7d6e00b10ba2d9f91241b5a53f272a63509b81527766bf95a55b95b8763fa8721e2fcd32ba938d07ba38ffda3f5df0efc20f00697f0f7c67e2cba9b4a78d4dad8eb2af7bf6d458c2b67f7987cb31181b8aec538ca1c6996be7a923edf82a86233fcd9e230c9aaa9ead6c7c3daa6b5e18fedfbdb6d374db84f0e5f5d998594f2079e26c67cd8989c2bf2383c32e03740476bd23ca7f54e1320c5bcbe9626357fda22b7e8eddca373a65df84353b2d774cbf59ad4dc2cda66a76edf2b9520e3d5645e3721e47b8209c3d9724d48f4a9e6947883093c062e3c935ba7d5f75e4278c974797c4926b3a2bc70d96a4a6e21b661b4dbbe00921f4f95c363fd9da7bd693ff7a41c3759e5f82960f132fde537ff0092f4239f5dd3b54f0f58785b4c98bdc5bdd5c4d34657019e411200bfde3b621ff7d63b51530f52c4e0ab53a19fe2318eb45c2c5b448aee18fc13e15749d164371aadd23848e69304292ec4030c6acc01240cb31eeb59724a14ecfb9c719aad52799631a76d20bcbfe1cb17d7fa77856cce9de16bdfb6df382977a92c6540c8c18e156c360f42e4648fba14658f7e2549e1128ee7360675736af2ab98ae5a6b65dca5178620b046bcf174cb66a48616106c377231ee50fcb10e472e3b8c2b0af329c397f8a7755cf655bfd972da4ddbed4b44bd0f62fd9e3e18d9f8eaf1b5af1bd85ee93e1fd20add68d1694f8926b95f9943b96c9246d2d2eddd8652b80a16bf4de06e1fa9986323350bc7a1fcc7e36f88982e06a13752baa95e516a5ead1ec5e28f1acb7534d713cb99677df2c8c4fcefdd8e727278c924f4afebfcb70d4f0d828518c6cd1fe47f106670c6e695b157fe249cadea705ab6a36771e72dddf28de727121e3f0af768533e2f158ba7a9c8dfeada2a61e29da440c79039e3d8d7b34cf98af50e3b54bbb6bbb9996d10b0930006006d7e71ec78fd2b4c4384b0ee0ce5a552bd2a91ab157572ff00ecc1f1964f815f1d7c3be3d9aecc36da76a8a9acc87386b49545b5ce71d308eb3e3fbca3d2bf31ce29429b958fdeb21c5cb13878392e87eab79166218759fb2c5b2d27646b9ba5276a91923820800103757c7d63ec286c4d747fb5a182d0bc73452ca44f8e368273fe26b8a733a5c0e77c59e146f0f69973a859e940421f7168e765e0e076047a7e758c65cccb846c72df6ed2756d1d56e74e98462601dbe66dafd36e78fe58aa345b91ebe74ad15a336ba5b5bac51fcd3b2b2ac60e082586703e6c761d0f422834399d4a2d560ba86eedb4b8669844ea923b8612231dd80769e3e50783d8608c9ce53f88da1f0991a069baae896afaedcf859b4e7df2235ac567ba500118731eec15fbd820e79e9c5653f84252e55720bbbbb0f1c59ff6368f7ad1cb696fbf7dd2957dc3380464f703a1e791c66b13926b9ccad32779615574903ab149832fdc232393d0f208aee81c8f466c584de5703e6c9aeea5f09954dcba926e61c75ad0825a000e70718ce38c9e2b196e078c7ed8df14756d13c236df077c156a6e75cf1b2bdab6091f65b00c16e19f1c8f301108f5f35c7515cb88f80b86e7b27c25f08a7803e1e695e12555135ad9a2ddcb0f0af3edf9dd31d8b720f5200cd6b47e1347b1d4c193f303838c018ce7daa26630dcf9fbf68dfd926ebc7fe294d49e5b3b4f095cc2c9ae4d74802c049dec55739790aaf18c72339e58850c7cb0ab952b9a4b051c4eadd8f0c97f648ff8274dacad6b278c7c5f2b46c55a55d50287238ce04640cf5c0271ea6aff00b62a7f2987f62c3f98f933e137c62f83ff000fb4c9a08ee22babf9e4325ddd4bbc79bc70a8a11880a073cf3d6be8f019f60a12d628f033fe1bccb39973e1aa38af23abd37f6a2f861797243f853ed1827e64938c0ff7d41fe95efd4e28cba34fe047cc2e08cf5d9aad2d0fd58ff82187c5bfd9bbc53f08f5ff000efc2af10f87d3e206b972f3f89b48babb78af2d6c61dc900887941658f1234858310aeec1b680b5f9867799471d8abd35647ea5c3396d6c15054eb4aefccf7af1cf8c3e11fc2abbbcf08f8a2c351875985b3632cf2ac093ccc7f77b65270aaca010febf2fde071e0ce523e9a74e50ea43a67c03f84bfb57dac1f107c77f0f746d7aead8180eae35792c757b370090924f62b1b3f4386676ce382715873cbb9ac22b955cd8b4ff00827afc19b5b98ef3fe110d5af54316b7b3d4fe276bd2c0a3d3679c411d8e411512ab28f5092b6c745e1afd9ebc4fe0cd326d2be1f7857c3fe18b3998f98ba14c6291fae4bcbf63f35bb9f998f53daa7dbcfb91691d17c2bf84f7df0e7506d51469af76ace22b8313ced1b1e49dccd1e0f07f841cf7aaf69e669cac8ae7c35e38d3edf54f14685e2cd6a4925bb926934731c76e27932370491e390aa9238e48c018aa55649e8cce51773cec7ed57a2eade3183c13e30d6f54d120bc922b4b3ba9a354923d40bb2cb6b2a98c72197e593a302300035d11c454a6af1761c21172f791ed1e09b2d5fc192cf7a9a94baaee42aad7abb2465ec376084c7b2f3efdb17539a5ccf72ea5284d5ac73f15aeb3e25d6b511b2dd24598c97314b200ca09241209e3daba215a4fa9c6e9496c67f886c2fad664d1ef2c402417565e43e7b83fd6bae9cdb64ca2d4753e16fda77e19fc2bf187ed1f7527c3cd2e56bb9a28bfe121b9b2b8f32296f99c2e406e3246c560a7ae3e50735ead1aed4796e7975a10bdec7e587fc1437e30c7e3afda33c4faa787aed24d134091341f0fcb1480f9915be51a504767903c9c7f7ebd5c2a4ce0aaefb1f35497924faa24511511a285ddea715e92d25a193bfb2d4950ba20ea0e6b65b1c125ef0348cdc3be7ea6815915af50c9195033e9533ba3aa97c467fda1a293ca725b9e87b5426d9d728ab1389432ed238229b6ec60afcc445cc7271c293cd45d9ab49ee5cb552650427191daa96c72d5d19a4a07963029a6d1c4f56183e94f998ac872124f26b4e6616449100cf822a5ca57dc9949ad11f5b7fc1283447d63f6a8f8796cb218bcbd42f2f26936f4105a4f30278f5518f722bc7c74e7cb6b9ece5eb4b9fb27a6784aee69edfc41378bd869f3b192ea08a2d8c491c22b03c60a8c9ea391debc9bb3d60fed2d6ad35382da4b212c65731dc22146553fc67a60e41f72075ed56b6036b463a2ea3692de5cea2d21b72c6e1d5f0491d1480cb8efdb9a00e175ed0753bdf124bab694f716fa7e4e002ca653b720f0e49e78e4118c60f5ada280bba1d8dfd9e853ea125f48a86716c93142cd970467ef1e70a5874e9542b22fe91617d6bf69fb5bbdcc4c5007872ac8c57182b924923baf1eb5ec65fcbc9a9e46631bb2d43a2586a3e73cfa85e405240c80b11b93a7e4719af5273b3d0f1a11913dec72d8c2b75a12225bc7285092018ce06719febf854fb434e499b5a14be64ab6ac8c8f3cbbc3004a938c9507a8fc6a6550b853b329fc40f06c5f11f4abff0006de4cf6b677b652412dc44db1b0e0a655b070c324820820f39e2bcacd39317819e1e6afcc7d270e6328e0338a752abd0f957c75fb42fc59f83fa1e89e1ef8b969ac6a3aae8ef2d9eb0da7cf1addb08b252ee359232b2dbcd1b16de0610aec7c9195fe67cff855e031127cba33fb3784b2bc0716417d5aa28c8f9c7f69cf8d3e23fda4bc671f88e5d4ec9ec74f436fa269ff00687f3eda1249dac654432b93f333727733735f394b034b0eeea563fae7c32e1bc270c61274ead29734b79f439496f27b9b241e2bf004b75b7837f628609db1c0dee14a3e3b1281b031bb15d34e14a326dea8fa4af83ab82af296598af7ef7719fc1f2394f17789dbc1f673587863507bb82f88696c754b736e372e7ae18a97009c302adcf039c1ba185f6d897a6878f9a6698da4bdbe2945d65a271d8f239fc4be27f126a4d66925d4b6e927cca6731a459e067a0cfb9eb5ee52cbf0f7bca0ae7e715b36ce332c7ca6a6e2de8df7456bcb0b8d3b517b75b9485d252b1b437818a9e790c3ef742723d45743cb53fb2650a92a75dd39cac9efa9d77c38f1419e5b2f0ceb37be65aa4a0496bf6c8ed44a41001799c30181d883c0e31d6b9b1391d3a94b9de963dac066d8da35552a32f691ecfa7a1ebcdaadbe956ada7699acd8698911e5f4c8e4b89d81e066e49c1f4c2151ed5c14b030a9174b9b53ee706aabb4b13cd56ff65ec8ebff00660f80fe13f8d3e36b9b6f12eb37161a0e9204fadea77332ac841dccb144a5b26472a793b8601e09383a65d9157c562fd928737a9f31e2ef8834b81f873e349b5a453d51eb7e20f886d0ebdfd9de1dd3740f0fe8b6311b7d374f7d77220854fcaa49b700fa9e4f24f27ad7f55706e5382cab2fa5094546496a7f8fbe22f15e2b8af37af5aace6f9a4dea79a7c63f8e96be0fb08f59d4bc63e0e9ac92fa24d4628afc3cc90b36d322e2552db4904a8192093d883f693c5d1a53bf35cfcd29e495310dca29b7e672be23f8e3f07f4bd2ee2ecfc71f06cd30b7611a5b5ec6549cb6186267c9c60d6d0cd6825a338eaf0de29cece9a3809ff6a1f8276b0f9975f1f61f3724986c34f492219e7bdb3671d3af35cffeb0416d26743e12a8d6b491e79e27fda9be0c5debba5eb9ff000b13c4904f6b3bc77674f83625ddbb0e8c142053bb1d0038279e2b97159dcab25c9367ad97f0be2295271f61171f3ee6c7847e367c23f1f788e6b4f02eaf7d76b2666bdb2d4bcc2445c23aa97625b21973f7b804f7ae3c562a862ec96afa9e960f2ccdf04dfb58a50f23f607f636f8d117c57fd92bc29e28d65c1bbb2b46d2f586924cc9f6ab5731167f94ff00ac411cc09ed301dabe4f1fee3d0fa8c1539496ace9357f88da67866f3ec0ba74b1addb6f95a346678c74dd90dce79cf19e41c715e4dee7a9648d4b0f19e91e3cf0b5cd9c574d7d2d85c6d9a10aa1955bee920e41c804703d7d285a6c164326f0669775630dc69962f6ebe4e405b5c441d4646e1bb04f4ee33e9cd3bb1d9146f6d758bcbcb5bfd07467bdb45b778afaea5b8317ceae474ec3681f8628bb038a9f45f09b6a2c8da3347117633c36d71885883b97247ddc9eaca3240ace5b9b43e122b8920d434f37173a8c84c4ebbe177cef1ce06e3d73e9fecfbd26931b49ee79bfc40d4351f0e319748d06dcdadeb79b796e23c048cfcaa01231bb20903a734b950b9628e53c2be3486f3c4ada6491dd45148a1edd6e370453d0aa93cb703a355c1b3cda9169bb1dfdb31c2fcb8249fcabaa326918a5dcb911937af5c66ae3295f71492b16d377539f6ae93219a8df69da769b71a8eab7715b5b5adbb4d73713300b1c6a0966627b000fe55cd55b4cb824cf9f7f66db0d7ff682f8e3adfed2de27b16b7d2a25b7b7f0ed95c2ed786d90eeb7183d19c169dc76252b99fbceccd5a49687d436414441b39518ff0080e7a03fcab784e31473c9546f464165e3bd064d6bfb1347b73ad5f5ab8f3ed6c9c2c76e0ff1cf2e085e738404b71d3043089b4cba51e57a97359f0647ae59de4be38d4cead25c2bc0aa89e5db5bc0db4f950c79f931819727731192d58fb3ee765e2b63cfff00e19d3c169f24368a107081ad37103b64e79faf7a3d9a0e73f27bfe098fff0004d7f1dfed6fad5ffc5ef16da88fe1e7856575d42f2ecb795a95caa6e36e84152caa0a33b0230180ef5cdc896c8f6232e487baec7b6fed67ff0004f8f815fb2efc09b8f8bde26d0264bff135f25a7816c5b708de0540f25ebab12c1767dc049dd8cf236d76d1c37b7a4d1c8f1bc93f23ccbf61cb2f88fa17c52f0d9f82fab5d699e2cbfba8decef627f2db4b48f2dbcbe32a0286690f7008c11815d2f2da51c2ca36d7b9e4e2339e4c4a5147e937c27ff829cfc28fdb83c68ffb2d7c6d9f4dd1be2c7877523a7785bc5b00dba6789993f76f048e0620999c3606046c7ee901b61f9bc460a54fa9eee13191ab1bccee6d7e21f8cbe0ef8d6ea7b99e5f0e6bfa51d8b2bd9b4e4ee20957b50c3cc4070f8dc338dc39c38f3650713d15562d5d1f5efecfff00b4fe9bf14b4844f13f8834e83550010d63936d7b191f24b033e586475877129dcb0daefcf522e5b0d545d4ec24f885a92eb8da65dc17f6b0dc9ff597535bf968a010c72b2962186181c60600ee4565eca46d1507d4893c77e03d3f5bbad0af7e2324b768164586eeea389224604aaabb2a2b648e99665efc1aae498fda5322b4f8cfe04b6664d5fc6ba059796a3ce67f13da36c6e7e5cee19edf9d5469c9ea653a90b9f3b7edb9adfecb3f103e1fdf788749f8e5e036d72d23324b6a7c6164ada84407cd18c38fdf63ee3704901738c01aa84ad6339548db431bf659fdb674af0f682ba3fc44f1f6989e1db7d311e2f136b12c70cb6eaac80473c980ac9b5862423774dcdceea3d8cfb8e15947a1ed773fb4c7ecabe2d1178afc35fb587c38371128fb2dc5a78bec64690b8dc237532e6443c6010bce3183cd6f1a12b6e57b54fa1f18fed09fb6b7c42f8dbe27d57e1aff00c27d6965e1eb59a38648f46b3db1dd923e722401a5f2c90b801f1b4f35dd4138ee6159f3474390f8d9e30b0fd9f3f674d7bc53a1e953dbaf87b44b8bf8ef244fddcdaaca45be9eabdfe49a6472a4f67fee8aeca70939a773c8c445b4cfc62f1feb0ed3dae9334aceca37cacc7272492c4fa924e3fe022bdfa12e43cd517d4e7a38bcb98b11d64dc3e95e8c65cda9cf56767cb62ce24033274fae6b58cefa1c8e2dbb8d600aef0dd0e3a555d0723236604fdd6e9e95129f31b42f16555d3de49cc8b196e7b2938fcb3509d8e8955bad8b1fd99718ddf67207fbadfe14dcb431bfbd710e8d7771858206383cfc8dfe159f31a7b5f22dc3a5dfc270d6cc3fe027fc2a94f430a9ef32c8b7badbb4423f33fe14d4ee73f20e5b7bac00621ff7d7ff005a9f307b3623412c237c8d163fd99958fe42b4e60f66c229630776ef6a894b531a916a563efdff00822d682c9fb4be8fad416f14d269be1cbf9d448b909bd160ddf51e69c1f5c578b8ea97763decbe8be5b9fabb7b77143a7c763796704b207de6495832f527b103a93cf279e7935e71e9fb36715e2ed4bc413f8a626d124576650e90939661c7519e100cf7cf355cc3e437fc1573e241a75fc5aeda3c5e6dca1134637108324938c67ae07d29a772651b1a8ba8690da3dbadaeadf68097e639945a3063b8800e4b7182c3b1c633c56b19d8921d43c2f6dade91e66bf713e9cb6578d29b2d3ee16353b436c326eeac5777e755cc055f09ebb2476b75f6cd463b44699648d6701d801d89047a8fcebbf0b5d535a9c58aa4ea1a1178bed126b873aadbb623440915ac848fcd88aed96254b5b1e7c30ce32d59cbfc40f1bd8c5a0eed1f5599ae3ed19061b760cc380557279c75e87a0ce054fd63c8d7d82ee2780fc61adcd7f62167b89a184ee7b7dc99208fba0f238c8ee7f1ace58bf212a093bdcecac35bbeb5d4534fb9d13507dd03166478822e00ebbb1c924e3af7acd5453d6c12c2c275149bd8cff197c21b6f8cfa71b1f1ff0083b4cbbd3a5b1785109093445c153246ea4f944ae016079e73c715e166b95d2cca94a125ab47e85c1dc7199f0862a35b0f2bf2b4d2e9f33e08fdaa7f602f8abf0175bbdd57c39e19bbd63c2f0869e1d46c13cd7b38864b2ceaa0142a01f988da40073d457e539af06e270ff02b9fe96f85de3e70df10e490a798558c2b5b6d2c786496ab1058eeecae95db2446cdb4e000739da7820e41f7e2be5eb64f8ca54d45c5ab1fb3e1b39cb33882584953949ff5dce1be3af88db4ed1ed6148d944b74c77c93b3b600c63278fd2bd3cb708e0acf73e078deb61b2b4a326b9dbd6db58f2ad63c7296b651c5a48b78ae9492f3963b981e307078efcf5e6bdba582755d93b1f8ae77c5587a11fdccb95f738a7f176a726a735d6a1a81112b2b6141270c7181f9feb5efac02b6e7e4f89e34c4ac77354a9ccbc8ecbc2d35c788f578355d0253e7b246c904adc13bb6907f9e6b8b198172b412bdcfbbc873be7a91c6516ee9ebae963eb3f861f0cf5ff897e0e975387c59a2699776939b7537b6ed2299546092a8c8481d896e48ee01cfd0641c0f4b1894e4d23dff0013be90b4782b87d2cba1cd566adcde670fe37ff8275eb7e33d6a4d7bc7bfb4cea77ac7fd5dae91a1131a2fa2aacd851d3a0afd2b2ee11c1e5ab9a9b5cddec7f9cdc5de2ce79c5d8d9e2b1d2949f457d17c88749ff827a7eced64b1a789fc67e34d48ae3747f698ad55bdbe688ed07ddb8f5afa6865719454a52d4fcc6a716632a49c9d2577e475167fb21fec7fa4d9b5a45e08b54651cdceabe262ec7eab92a4fe1f4c57453caf0ff69dce49f1366b24f96297c8a3aafc02fd9a5949b3f06f8554e319b395589fa3190e7fef915d2b2ec0dac7975b88f3c52d2272ebfb2cfc039eebed29e00df20eaa2e59f3ee40cff2acff00b230042e27e25969c8cd25fd9f3e14dadd2dc597c2346c22a863a14d2a0fbddc47ee323da9472bc053a8e4f53b16719e57a11736e2ee50d07e1158e96faaf8a748f85571677dff00091cd1c2b6be1f30b35946822500ec1c32ef6273f316f615c52c06168424e3bbd8fa058cc74e54f9e4dab6c7d9ff00f048ff008b93f87be21f897e04f8c77793ab58aea5a68b852816eed8797711853c80f0bc2d8c6716bec4d7c9e6386bcb73e9b01889347dade33f0f25e6a7686e596033c849dd19001e7e5e838fa1af0651e5d0f6d554c8743b4d0b49d5d74d48d201732246648a3ff5c73c60019c29cf738049acf98d16a50d4fc49aad8eb177a2e8f74d70afb1adc93b7cbc90b800fcca41e79c0ed834730ce834dd4346bcd31752d2eea2103b12f1361773646e701b201c82318ef9e2872b01ccdd68de1ab184dc26aeeaa662f2416a4954ea307d3af7a9bf36a5c656461369cd0ea13456d22dde9f34608ccca19181e3208c8273d3da82a32bb386f88be1f8e5f0e5d5c6ab6416dd645f3678e40e703a47b41cf273cf039fc82ce535ad3d52148ac3ce68638b779a2156da483b4e14e719c73e99ce29c5d8e59c548d3d2f53b7bfb64ba872a4fcb244dd6375e194f63c838238239ae9849491cd3872b35ade55936a8eb5a2b27739e6ec8b9b1898e35466dedb46d1939ad7daa338a723c0bf697f8a57ff00123c7da7fec81f0c6d64bed4354915fc537b09c45656a01710c8e01db9c6e907076155fbd20158549733358c794f73f02f80fc3ff0dbc236de15d062730d8c18f3180df3375676c75627d3b01e9592f75dcbe5f68ac21d13c6de37944175aabe81a5213e64166e0df5c8fee97e56353e8327dfb56525290d72c773b0d0345d37c35a4a687a0d9476b6a872b046bc67d73d727df3f5ade316b725979d99e1d8e7233d2b4d0126c83cb038068d07caceabc1da5feccffb227c30d1fe01e9c34ed17c31e1bd292d92df5406185f70207993347e5bdc4ce4b302c776f7ce0102b7f610395e3ea7358fc7dff82c3fed8773fb547ed4efe0ff000e5ea4da0784b36362b15cac91c93872669032f0df30f2c3838658b72f0fcf7e128c6d65b918eafece8738df8176de21f835fb396b1f1874bb49dbc71e3a797c39e0085caee08bcdddee58f05703e63c0d8467e7aecc452e4a2d3dcf130589a537edaa3d16e7d03ff0417ff827978a6cbe32eabfb60fc73d3163b5f0d5bb5b7866dfeda936fbc9633e65c1da70be54240008ce66ddd541af8fc726b73ee3015f0788a5782b9ef5ff000502f1078f6f7e26e95e2e8fecda7e811dbdc4288561697559f10794b1e09912342598e40dcc4f5dbcf8f285d1eac7952b23c9342f8893f8cfc39a87c35d4ada1b64bfbe79276550859ca61a65207df5ca92c739db83ea3285257d4538b92d0f89fc73fb2bfc6ff077ed051e95e3cd465d434a6924bcd2359d36e03417b067e543c930b2b150c84e7824641cd69ec62d9ccdd48bd0eb93e146bf34371acdeb473491966795e30cc476e3a600cfd33ee6b7fab407fbd23d3fe1a5acda3bdc1fb1c52300b1a3a1126df5f43d7a9aa54216348d39495d99da8f80754bd5fb2d94c3632eef39130001ce79ebd3af1f5a1d18a2953716773f0c757d7b49f0e4bf0b7c5325c49a5ddc0f6b0dfc528568032b2b20206e20ab30ef80e47dd353ece232b7853e057887e0f7886e3473aabdf5c4d7cbaa3bf94a18db1c25b44c3b00bb988f65edc55f216b63eacf809f0b27d72c17c51aec82eee65bef34f9d27998c761b986e18edd327eb4d2498a5b1e55ff0005aaf8d73e89f0e3c1df0174592589f5ad5d75ed4ca4990d6d6fba28030c9014caf31c671ba11d2bb28ad51e5d769267e5a789ae2e755f125d4c91b3b02572ab9cf5f4f7cfe75ebd368f3b5b1198f53f210dc59cdb881f7a2233fa575c6692dce69c2f226b7b3d5fccfdce97779c7f0c2f56aaabee47b364f2697e267428341bf6e84e6d5cf4e9daabdb47b8fd9b08bc3be2e5f9d742d41589e0fd8e4efed8a8f68bb9a2822d0f01f8feec878bc33a9bb0e369b2957ff6434bda20e45d407c31f8a524a231e07d5b2dd02d94873f4e39a4ea2b0f91139f84bf1454a893c0bace4f66b171fceb3f6be61ecd3251f093e29ecdcbe06d4029e8cd6f8fe747b55dccdd3d4727c24f8a2eb9ff00845ee863a93220fe6d551aaafb8bd9b7b224ff008545f13234066f0b91ef2ea90467f22f55ed9771fb29761e3e12fc456253fb260c83cefd66d8e3f390569ede3dc5ec9f63434df80bf1435050d6ba6e94073f2cbafda2b1c7520197a7bd35560fa932c3b7ad8fd01ff8224785e4b9f8c7e26bab89a2b7fecff07242e59b7265aee12790c339f2ba83fa578f8c69d4d0f4f049c558fd2bd5b51b37fb45c0d574f0b6f164e650573c71b33f7bf13f4ae33d130b4e93ed76bfda260db24f265dee90fc809ce1fb8ca824118c1eb401786ad7ba168ab05ec263178593c9fb46e24e3018c80e327b67d2aa2673463e83a0ea1a5dde9e961725eea2ba48e4844c5ca798ec43e3be436df4c7e1549ab99d99d77885350d36fe2b57d1a491a543f68f2a57db2f23e52c33b4fa120e3a6315a5d009a2580d556e26bcf0fa6147c88d33aed1fef13cfde5e71fcabae852e757396b55e47627b4f0a785b4b5ba68ed5a012b07f951dc0cf63907fc4d7a14e824f5382a57e6d1115d68be1e9b548ae5608e58a30374ef0f28fdb68239fe95bfb18197b491a3a7d8e9d697b3cf2490adc6d0be5b5a286254fdd047a5733a1a87b4905ca5adf5e3eb3abca8a91a8899bcb5390c477da3fbb4d52b2294a5237f419eda5d1e5b98e58590bbf96644018af6158ba4dcb429d39c9593333c6f6d6dac7c3cd5b40d59556d750d1ae6da5523e6c4b1326ecaab138ed8c1f7abf66fedd99d582c53cbea73529b53f23f29740fd9d7f6a2963586e3f63bf1ccdaa4888aad75a3858a32bc310de722905896cb6383e9532c1e578956a90d4fd0720f15789f87eb3ab86c44bdaae97d0e7bf6bcfd94ff0069bd1be1049e37f1ff00eccda8f87f49d1254b9bed5f36856246fdd0044572ec417913a2f6ed5f299ce4b82c352e7a2ba9fb070af8cbc47c618bf659bdb6d1df5b9f31cff003c4ed2687a85ce853aa788a474d2c9b88ff007e50aee03e6f970aea7e6c70ca7a1e7e5e8d1f673bd8fd0732cae96674b994b53574cfd913e232a5c6bbae783ee60d2edae45a6a57493c67c961202475c6540c8cfa0cd7af18ca4b447cf52ca7288cff007ba35dcdd3f0b3c75f06fc4d7367a9f8465b592c35a934d954ba393323b7c8546480769c7041d8c074ac1d1c44f15151d8fb8e1dc665b470d5e8d3574e36f99ee5a77c2afda3be0f7c46817c41a168d656be26d36d2e609a3d5eddad8a4a50472798b1388d419e00c4213fbe04f0723ecf2ead89c2d5846fa1f8ef10e59957116495f05357a94db699f405efec0bfb6c6a4d24aba2f842df6cbb5fc9f173b053dc1dba71cfe3eb5f51f5ea87f2863787a380c6ce9b2c49ff0004eafdb2440d247a9782e3214912378c2e86c38ea40d34671d7008fa8a3fb4aaa229e4387945330fc51ff04e8fdbcac4457367e35f06de09485f262f135f28881eb2126dd72075c0e4f60694b34aab614b23c3c2ccd0d6bfe095dfb79cda7413ff00c2fef0944a402f1c5a96a6e163ff009e9931af7c7ca40e1bb63351fdad5cd2395e120b6356c3fe0933fb52eada55cc97ff00b55783ed6ea1b569d2d069baa49e6818e14b4cbc9cf1c76acbfb6ab950c0d3ec8ab1ff00c122be366a16f1df6adfb50787d65918ae23f0adec9b00ec59af539fc3f1ad639ad694475f2fa328a6d0e97fe08cfe32babc9a2ff86cdd3818d37009f0dae083dfef3ea241fcb9aca798d57a32abe0e925148eb3f67eff00825078a3e0dfc4cf0ffc69d37f6bd9af2fb42d492f059dbf80a18639a25052589b75db1c49133c448048dfd33c8f33118973dce9c3d1505a1f56f8bac7c437cf6fa9bb7db2185b75b2a2889a107bb6dfbc00f7ce474af366ee7a118cbb197abeb3731b40ba5f94d3a374b81f28dc319563cf4fbdeff515cfd4e949d8835cd3af7c31acead7c9b158586f819065125723e619cf739c1cf4aa5b8ecccef0478d359d3e0bab1d6b44b6bc86e0977b92c36f99d38080956619ce064e3a138a260662fc42d1ad843a6e8de14696ea3b9905e471c8775be64c295249ddb89ec4633511d8067c40f10d8f87efc5aea9a15c5bc2f0a34d7b147bbcb720e44bb41240f6f5aa2e09dce6357f0fd978a2d0c89a84b6d238da10ced25bb6470006638c8edf7baf141a9ce6bb65e20d02ca5d1752b30f73224625747d81149f95a32181f988e739e9907a64ba3069dccaf0a697fd8d66ba8df6ac679ef27912ea162311381fbbe9d7e553cf39c0e6ae1348c2aa774755616f2ab46e1720b000953826af999c5257763c3ff69efdae350d1fc46dfb3d7ecc56dff092f8f6f1fc8b99a37516fa08381e6cae46c3200776c2405c658af147333484526773fb257ecf3e1ff80be19bb58f529fc4be24d667fb47893c4139608666c33c6ad2637a03c96404b1507a6d515b84b467b2c1b6d999d64525970590e3fcfd78a6973094922781b702a18e0f50589aae43194f52c40c4e0b1a66c4bd57da82a3b0df2e23cfdab1f85051cf7c6efdb97f661f8f1f03f5ef865f08be3669fe0ed435bd2a6b2d2eff00e21f86afac6dedda452af3319a00b33852c539e1f69c8c62a3da1cea94207e777c36fd807c15ff0008b5ccf79ff051df86ba75e4334a22d13c5fa6c90a1d9c26e94492288d80c2c89b988071b4835a47193c22f6b1dc2584a58f7eca5b1cc68bfb4a7c0783e2edb7827f693f1559692be13d1c685a55ff00c3d923d5747b48f71796ea151324999782ce1a571b7ee8c9034a59b54c64d55a9d0e5c470cd28537429ed23edffda9ff00e0a95f007f62cfd947c0ff000d3f62ef883e1af1d6b5e27b11169c52432bdaee00bde4f1a92c4b39c0818658918255483e76635d5767a180c3cb2e87b28743c8350f13fc42d4fc37a0cff1cfe23ea3af6b96f1289eda47556425d9dc9545548d119f6a28e5820edcd70c15a091ec41c9eaf735fc3de13f02dde9d7b77abeb2d25eeaf337d8ad93efa640036e33df19c64e4f435333a8e4ed3e1c5845a94fa4c7e367d567d3c79772b1ce0a608dc40208ce01e4803273e9598125af807c296e16f74ab868edcf2c567660091c631c91d7f23f88058d67c21ac691696da858dad9dfc087724a88e5a363fde07afb027028032e3f85ba0d8dadddd5cb4972f2b2cac18e4ef2dd36f25106384fd681adcbf65f030dddaa94d07ec37178ab217f37609230721d8800c6303a73904e682ceaad7c05026bc2c347b57d421bb822b6b8bdbc42b24ee485ca6d076aaf451d76f5e718dd6c66f73da344baf86a6c21f0adbc77fa3c7a75bc667bab4b951280029da98c962dbc7419e49381cd4c96a8996c78dfed05fb04683fb55f8caf7e38f887e38cfa4db5dc16b6563a6c5e17599ece08b0123494ccbc9650ecdb46599b8e6ba29d6e438a741c9983a0ff00c116fc1915dc17375fb4bf8a61464dde641a2401a2fc1a4c1cf3dcf73dab7fad223eac45ae7fc119bc2965aa4120fda0fc71358cd2f971c98883090f55619c027938e78ab8e2d58ce585772de97ff045ff00841abea9fd8ba7fc7df1bcd286fde332da46912820172c22c803af4e9f8512c52682385771d7bff045ef835e1d8246d53f68bf8825270445b2ead94b00705813091b73f2e71d78a8fad234fab1168dff000476f80ec651ae7c57f8851a5bc65e497fb62d80db9c13916c71d7938c01cd69f5b461ec1dcb7a5ffc1243f654d62cedd349f11fc53be9669c27951ebb6636a80bb81636a43119ebda9fd6d170c3465b9d0ea7ff000482fd867c39a9450ea975f11e4568d15a77d7ad880c71804ada673923a71cd44f149c5a3458585ceabc3fff000456fd87a7bc7897fe131b8668c32acbe2a451b7bb02b6ea08c7279edebc5737b634faac08e5ff00823bfec57e1cbe974ed53c0fadddc119596268fc4f70cc50f232015e7af000ad157d05f55a66b7863fe08e3fb125d5b5eea31fc31d4a61bc476962de23bc2ef21618057cd200c13ef56aaa64ca8c69aba33751ff0082687ec530ea29e1dd1fe0a5b34fbca7da3fb76f8a16070467cef5f6e69f3a2092e7fe09c3fb16786acad6fee3f67fb779669f61dd7f76fc0e182a99b04f418e01ce393804f680755e18ff0082767ec96ba8cfa84dfb31e831d9c91aa436f3de5d3cca47214666c10d9e49381d3a01512a9a81d67c2cf841f083e05f88b55d3fe1ff00c1ad13c317770ab1cb7569929280e4ac64b12c4e09e391ce7d8253bb2a1f11e817da0693e207fb64cb6d6f74b09293aa0208032cc42821b201033839ab3639c92fc45a746e2f6058163027f35b71dc0b61b0473c165db91ef9a00dcd7fc3da3e8be1cb5bdb641f648230c04ac09b8b866241e83238e47f749e33c800e5749b19b59d424f172decc8c2e13ed0eb20569be6c6508c9e9dfad027b1a52dd7c43d5355d5347d2af2630c96051ee6576051772ee201caeec0382480074f94a9a0c0ea3c290df43e1d659b5e4bc6886d96e5e2032e3aa74e71d33d0e38c74af4305b9c78bf859674af15e9172f359dc4c44f01c5c796e1954f6cf523f103a57b07994b72cdccd670ff00c4c62bbf32660618d25076c6400dbf91d7e61f963bd06e49a123dbba5addc6f7521b46f264456232548c92c7ae4e73c735a0153c55732da5f2e84ba64d28b755170632a519f00e72581e010a38fe1cf7ab8ec6353743e2ba89f4f96da14db2360c71ce4f0e38038c8f4efc9e95854d8b86c6ee93a65ec13d85e6af761665b65430a31e1493807207cbd2b8cb25bfd2bfb2677d452ea62af2ef9e150a431c01919c60679e3d6ad6c0677c55f871a07c6ff825e29f851ae23a59ebfa1cf6525c301fb86954aa48a3b9472ae01eea2b8f30a7ed30ed1eff000ce3de5b9d52acba347e4af85be03ebff11fe14daf8af56bb9add7e1a7daf4ed590a958ee6ea09e356d8d82777d9658e46040c25ab938c57cad4c23fabb3fab31dc414b0f4632a6f5a88f6cf839e10d37c7770de12beb6d4d740f106950dbf88bed0d1b25cc5e65a5b2b00bf72668ee972e093b91f19c9dba60ab2c3ae567cbe7d995b26fad3fe29a7fb7a7c09d2e7f845aafc5bf0888d7567d074fbcd22ee23c5e496728b4be898b0f99dd92339c6e67bd19ea2bbaa2b4d3ee7cbf0d715635d5f657b296e751fb2ad8fc3bfdacff604f0f788b44f0c89b55f055f9b6bed26d55659a7d39dda292d37360e7ecd73288d8918920889215735e960be3470e6798e3328cca526fdd9ee7d9df0eb4ed434ff0000e951ea974d7d241a6411cf7e0e5ae955762cbdb25d555fa03f31cf35ed9f90665596371d3aa8d7d4358b5b7b74b58ad172251e6c85581c371c1c75a0e08ec5792ca3bbfdd5b5a3b391b8057208f7e38fce81925f6a51c70ae9f25bcd23980fca324839efcd27b0143c4f3dbcbac24d6570121b7863da18fcc00503181ff02ae586e04575258df41242212219141419c1aea8ec05eb0b7d334b8ed74f8ae627648c4775b813b4e7257a75c5658876a4c2d7d0c5f8a9088f4d9b55f0f6b1144d67b5a20182b72475e9e9dabc6a933b28d32af806e75fd46c6e6e2e35185ae604250601128fba5b247ccb90467d4573bd59e9c7633adf4cb8b9d3ef74fd4e096da612ba2c8b2811af3dc961c7a0c1fa52192f8a818fc1326967cd9de548fcc75e5d447938383c03da8d84f6389f0eea1a93cc348d06d0cc5e4f3099909c050792013ce7bd3e7460745a1e9f65e1fb5d5754bcd213fb5264f2a168e1119463fc4c791807e6c819e84d2bdcda1f09977f3daea5e174d36737924e19a5998db6e94073b40c77553d369e0e3e9414791b687ad7877e205de9f66baa2d83dc2ceee2361015fbc76bb903b6d11b166e4e01c6480751e36d31ae75ab5d5745d4639ad527688db4930223dc992c48c9da0e7191819ef900e0f703c8fe2d7c74f807f04ed6eaebc67e25b24ba42662963708eecebc202090aaa49232719c9001353d4899e63a47c78fda6bf6c3d2aefc37fb3bf81a5f0368170be5bf8f75a6921324247cc6d14a8676c70aeab8cf522bbe97c079953e33daff00677fd94fe117ece7a4470f85346fb6eaa63cdf6b3a8e2492ea6392cf8e9182493c64fab13935449ea0d23f4247239c2e00a0997c24b6aa58607a534ec60a3cd22e5b9f2c7cc29f397ecd9603aa9ce6a8d89d082898c82b26eddd73ed8a0a8ec48d6b348c5c489f31cfdda0a3f056d3f682f8e3e1f85d7c4df13fc156a3a9b7fec4b79ddcf61b6d23207fc088ae4b5437586a5534380d5bc79abfc48f13a588d3b4fbd96562b1da69da0fcb3331e3e45f9f71e40c73d31d4d526be196e5cb0d0c2d1e683d4f78f831ff0004ccf176a76de1ff008c3fb445f9f06fc3ad6255965bd9732ccc8cbfba7dab9f21252760790f04e4820835d14b09297f84e7c466d470d4d7f31ea1f0ebe03f85351fda32caebe1b6b52ea7f0f3e1f0377a2586a5630a4b6ba8e76c68658c667550a251216704a2609ce4f2e2a8429bd0ac162bdbcf9fa9f4240740b8b79535d9d2ee4998ba213fbcdc7b74ebed5c0dd91ea5db9b6ce9aefe1c47aab43e28d535c6b3d3608035825ac07cfbb6031f280777033bb180368f9466b1e672dce81f1f82347d1bc3726bc9e12657b89f13b46d89963c7cb249e801ec3a67d280389f075af886db5cbdd1b4cf0c09a3693119593314e37052cadfc001047d4f1401dc6a97973e1ebabaf0846c7cfb989550e7f76800c900fae78cf39a00b3a578623bad4edaeb4ed26d6436ceacde646a4fca738563ebc8cf6cd38abb1a7636be20f8ef4dd4ee1340f0dc7e619081a8dce7f79337f7067188d06001df19f4c69c883999bde00ba877c1a1d9f86e499603e6334ff216c1edc8ebd86455ec4393b9dbc5e04f06d95fdc6a30c70ea1e20bd95848b39222b50186e5dc005df91ca8e07a0c850ad725ea749e25bb5d2bc1c66bdb884db04f2ae6ee44513a0c0c955c1c20fbb8ebd0f353ece234ec63fc24d37c57af34ba7ea9add822db39330b68d59523232ab81fc41873d79c8c8c1147b388f999d025ddfeab70ba5dc2b9b7b6b83243728e42bb1e84f1f2b01853cf6a6a090af735f4bd31b45d4e7d62eef256484342906f2020dc72463af41c9e879eb4382609b479ff008afc59378bbc472ded858b5c4c27f2e3b05c08ca28f9570385519fc297b388f999d5f872c7c417f6ef6faed8470c46161761a2f306cc648e7a9ca9e3bf4a7ca8cf9132fd86ade1eb5d5e2d2fc3c8da669961121822727ce98e016762482189ea0f23d4d1ca85c88abf1575ad3f58d2234b1d4ae4fdb25f2ac66b5400db1272c000a7728191b4f2778c668e5435048ddf00e8373a77869e3d5ef2f37794628a2986c10b8c8f3783c91953d48c631c51c911d910e9e353b86bad7f5eb449a79fe557dc4e40ce09c671c1e738f6a7ca85ca8bcd2ea9e19d02eee3c21a53adddda333042598330c314c9fbc01c7d0d541588a9156387d07c3de39d5ae1bfb2b4b680e42cf34b107dcc072391c3678f6ad2ecc7911d5f87b4bbcd2445a8eaf6c352b946db05b3b6103e0ee7c77ef91919c7041e68bb1f244b3e15bbd665ba9b54bdd02f5eee7848750adb5a2dc436c03a718241f5eb49ab8b911cef8cfc2b3f8a3c4705c5a69823b486276b3bb9e5d804855431721893b76600c64eec77cd0b4771a8a4ee741a8d969b61e128747d359ace4ca348d1c8770774e55b23a6d60783d48c1e6af9d94416be17bdb5b0845c5b4575187557902150c46e18605b93f31e7039dbd735a2d500bf1274ad7ee2d61d36c2248e1b40921b7967008cf7047d31f9d302868ff0afc453d91d42ff005516c911f362b7866255467773cf27d0771409ec6badbb49a67fc23be1abcb52275db7092165690b10c149233b318e319e3a00050606ff0084b4ad5ac34c92d357d36cc42176a470cf9dbce73d3e95e860b739714bdc25b2d1e6d2f5c93ed76d12bbb6e629ce41e4039ef8f5af616e7994d225d55adee274bdba611b286db2ae1482c36f39c81d3a75abb2352d68ba435b5b968de291bcadbe7166eb8e0e081cfd7149b60625f6810ddea5e545ab44db80f3be6390ddfbd5464ec4ca2a4cbe9a1db689e53c4cd214c124373f81eff8f4a994535a8d248b6b3c9a95ff009b26aeb0cd8dac2542df80c103a563eca23352e745b9d46dfc88f5c8cc7818f2d361723f8701864543493b00fb05b4b3b4743701537a8be12a86563903006fee71d3ebdab1acaf0b1b61f9bdaa68f9abc7ff00b1adbd9f8abc5961e06b79c5af8eb54935a9228e157b7b0d41a2161731a82b968aeadaea42fd768473dc63cb9d14e1ca8fd4709c43094693af2d61b1e2bf027f65cf8a5fb33f8a6ebe1bfc61f8d9e18d0fc3efadae8ba278cfc457ec91a6145fdaddbc01c81b9a350b9914931907ee367e7719154eadd1efe618ca19ce1fdad14efdba7dc7b97883c2b07c41f819168be149b4cf10a687e3c092cd632473437d637575e6cd2200cd8d924d1dc853c86b5450582866f570b27898a73e87c9c955ca6b4675159bd4affb0d7eca571fb2e7fc24125b69bfd9d63752b416fa545303b764ee779cae0a8612bc6d924c773df00afbb86a508b4d1e77136771c646318b3e96b4beb4920499ade7458e42e1427ca189c9fbbcb0cfe35e89f151f72f6ea26b57dbecdcfdb32c3f7d3208d86d03903a66833b24519b505b2db1d9c8f09963dd22852d827f8483839ef9a993680d3d261bcbeb786e6f93f7a4944932486507a77e7b76acf9d81535bd360bbba29269de61ce72f21c81e83daa124809e29f4d5b75b48ac163940da8a188144aab820d5b39ff00126a1776da9c16d168d0dc23ef528ca3682149cf3c7e3debcfaf8a9ce3ca7550a5194d265bb48a01b63bbf0f2881533298a04273d406caf1c115c2ddf73d28d28c762b2a2dc6a913e9565b6dae2292468e2c0f28820ed518ca86c9381c12a7f146866e9da678c1358595ae869f8b96f3cba2c824e9bc26e070840397c761c1a00e8a4d26ce0d16e12ce7b72fe618ccbe58da1c0ea07000dde98a03739ad0742f13e9fa836ab77aac529785892cc501c631d49e39eb9e054f2a23910fbcd36ebc577326ab3c3e5b2a0092c6e0ae47276e0f3cf7200c552d096dc5d910689a5f8de1b5b9927d4a66fb2c456cdcc61d933901933d4e581e72780335326d22a326d9f26f8d7c47fb49f80fc4f7ba06950c7e2699efc4165a67882e9215699dbfe3e04879da0018e70411c362a39d961aefc1dfda7be2d5b4d69f187e39f86bc2f148eac9a0691a5c977240aac199bed722a2a9201fbaa319ebc0539393b81a5f07ff00e09e5fb2c7c39b88bc51ae784ee3c57e205d41671acf8aae1afb6b93feb23491044b8cae309c0279e295d913b9ea3e2ab6b5b7d6244827c284dab10c600078038edffeaada15a6b4396a518db98a366c4b906baa326ce49685d44579086ad1e8c95ef44b56c044df277a89bb205149dc9dfef1acf99943a050d260d5c2a49ee5f2a2ec280a7e35add82562daa49b4600e94aec67e49fc25ff8266786b4af15dc6bbf1cbc3d673dcd9470c6be09d235d064fed0b88d9a18eecaa116f02853249b642c117f87209ebf648e358d6ff8676df123c753693a3da43fb2bb68ba6e9fe00d36eb4fb6d72c74f8a24d53510b0c77522ba80b2f98658e20d93bde1620ed620eb0a187e5e696e7257af98cf1118b5ee9e4bf04fe217c7af02fc2ab3f0c7c48f19e91e23d2ac749fb45a5acdab25ccda3c72bb44b63720365e398e170b9f2db60e0838c1e27d9cadf64f4b179652c672ca1b9f5efecb7fb3d69de1bf853a1e95e2b9af6dafa6b313eb314070a974e0131a93c128bb119bb9527bd79b8bc4d29ec75e0f053c39db7897c2be1cf0cdb21d0b4c33fd941134841c7dec820f19e09cd707c51d0f4d277bb29d9699a478c54ea37d717d692db9dd88d0aed8d7a12790063a28acd539236ba36b54f09e9973a5df5c2f88257b6bab554c4b29f31811d72146d5f5a7c920ba399d2f46d33c29a1c17d650982395d62825841790c7c31219d411f7475ed9f51472482e8def08695ab6b9e229f53bbd0dee6cb70ff4e91bca3c00a523528c430e4f5524f73472482e8ea6e7c33a4f83f4cf3a5b6b8b998ca046a8723af527206ef539c115518b4c2e89fc0be13f0adeeba3c47e29f0aa5a79ae3ecc59b86033927e6c75f735a05d1db5de6dade4d5f43b24820b68b1fbb5dece00ebc67b77a087b97746d234fbeb20dadf8254ce0895591b0f28c9c851ed8e7eb401a1abc5e11d4b47bd906830199a2f39205b53b55067e6da7967183f374c9a00988d5741d334f4b7d26e924b827f751db046f28838e15b0792318e01efd680363c21e19d71185fb5fdacb69772b470dacd6ab2ba9ea5a471823a8391e847268034fc45a1bd869b1d97846ca032292ccd290d8ed838c91d077fe6680327c3b6fad68320bbd7ac16e2495ce4595b2b32aff7760dc73d7a504b9c63b92f88e6d5efac46af6d2389ca2341140c0123a608cf5e7a5051a3a741e2fd12c628ef2dace6bd88196359e2deb8c8dc5b03d8e3db1c0a04e490df116bc65f0f5ca699a7c82473be5247cf34a464a16dd8c03d4502e788cd56cefcbc1169d1d95cc62d665b96322b2163b768186c850072bd791d28173c4d2f077858586f2fafdcce675065b33fea635017e50bd0fb7f1763c0f99d9949dd078bac351d7ade0b1d0af521b74521d9576739e14f07a7d2ae316c99ec66d84b71e1d90e91a75cc577e5c643112a86573f3127381c607d455f248c88752f0cde6b92db5cd849b6e1a43f689044ca6356fbc1811900818ed90719e68e4901a0f26b16113db9d7f6ba046959b9599031fddfe1d33d09071d324e49014bc41ac47addba49a86fb758ef636cadba9c41b87187f94923819cf4eb47248086eec86a7afdc4767a95cca8f1c692dac50387917079195c6d2bc8009240fe1e28e4901d2f85f49f0cf87a00344d2044108c61b63338cfdd0c01c648e3d7e82b45a2020d5fc136be24d65f55bb7bb0511415705f66d51ce013dff9531369152cf51b749a5d1d7575861727f75711b6e550338527680319e4fa1a09724cb2fe0df0feb9aa41a95e5e9558d433ae01271d1971d46393db1d6823924686982d6caf675b3940687e52d274d84b004f727d7debbb092517a9c98a4d409e4d4ace09a0d46e9c94485849b6408c38eddabda8eba9e653d8934fb6d32491350b589b6b4bb879d26636e832a7b83d3d6aee8d0d6b6bb29e62c6837dc312889c80a3181f8647e750f7029c7a7dbd9dbc924568b8c334adbb92ddea93480cfb6d42db66c57933130560dca29f603a7d73f8536d017ada0d1b6497cd21dc00ef80bc9039fa835004a672116e9ecdc80dcb0e4707d41ace516d92e493203ab5bc36f70d26992c8b34e0e122e43b707a1e3a0e7fad615a12e4d0155947e1dcb1a9dc6a9a68b6974f5482712a24d016c030be448739c0c6fdc3d4c63d6b38c15b53a684e4ddea33f337fe0b33e17f89be24fdadb4fd4afbc53058f8661f07da0d3d2d2efc965957ed21a40c23ce418a5e073b42fa1af96cc3095bdaec7edfc07528d5cb5c92b9d77fc1336d3c45fb327c7cb9f829e33f1fcfa95cf8e34f63069f34ef342f71670bce2489a4c37cf1f99f3704abafca0a8aeac1c5d08da663c6985a98fc1bad4e3f023f40ed2fb16248b589136fca634c86c74ebd7f4afa0a125a1f88f2caa42351ed72e5cedb6b58e05d3577c6a5f2a84e7a0539fa0aece78945065478c4eb6d19471f7871ff00eaa39e243dc7e9761ae1bd83ed498b7452de62cb938c918e4ff515129262b5c98dd45a6ddc42fa0953628059df2a3e98a81d99a367086d616290b9796df25e37009dadd3f2350e7141668c0f899ad3787de75d32f9d2e5e4d803480b1008dd8c7a75ae0c4d64e5a1d1468caa46e8a1a0ea7a89d461d29244dd3365667e89df9ce07fc0abcef7dcaeceea54a507765bd565d76e6f1ee9e079235764b9f224df246abc72bb73c63b67f3cd59d264c72a4d35b5e5aeb691da493aabbb76f980c30c64750318efef53ce8972499ada6f82751d02d6ea4bdd42da798cdfb98f66d8826cc6d75e7e6e7afaf734d34c39d19f6fab6a77b7c348d534929b60131f246438e460e06082463d78a668a2dab9d2d98f0adfc52de456f6ef1245b494619c8182a41e870071c1a093ce4f836f1bc4375ac69d6578b6690309a485c0084904636127d09e07ddefd68329277366c6df54bbf0fea2faade5d23c1b2423c80088fcc50594e319048ebd4673513d874e2dc8f33f8dff000cee5f4c8bc46ba536a7bf6181a35f2e5b72a396da719f5daa73edcd60e7146dc9232bc2be12f0cf8c265b7d7135880ed8d92f6d9c468083d199d0eddc548c12320fa9353b87248ea3c57a504d4859785afe686ebece33041fbb8d8228556c1c60e30491d681f23ea60e9178b0e8b7337886d5647b862269124532755dacc83afdd63c7276838c83551dc8ab07ecd99b05cc443624054390a477f7aeda6d1e3d57c9b96a06120052b56d5c883bc132f2ff00ab1f5a896a8b2587ee7e35937602c456f207c903f3a7076dcd0b10ab21e6b74d35a01375a00fc05f84ff0017be327c461a8f85efbc737ab278afc470dd6adabdd3b117120dc2692766eb188f2c54ff00b2bea2b3fadb3a6a60609fba5bf8e7fb4acde38bbb6f861f0dee35187c33a55d2a58344efe5900aaacc912018dc15480dbb04f001c65bc5b689a5817bc8f50ff0082787eccde24f8e1fb611ff84a34d8adf46f87914777aedac52abdbbc8189b4b67688ec765906f38c8fdd364f06bcdab8a6dd8f4a1495347e8f6a1ad596a3e21974983c3d2cf1c60b06b372863e003b8e339217b71ef592f7cb2fa45a46ada3359dee92e96c501772b960c33f29c672318ff00bebd8d6c95958092da0835399e6f0f58c2121b6d863463c93c7cca7a743f9500655fda6bb1c731d37c3334e0c7997c9627318fe1c053c77e9d6802c6969ac5ee890f893c55079164a7cb8ada5b356990865025f9070a47cb86e7233d050076de19bdb6b98e4b7f09ca677b5b954f264400444ae493d98e323239a00ebb48f055c8b517b717a8559b2f38006d6ee1493401ce35eeb93f8b65b7b1582e20894f98d2c4ae1547439e84fd73401daf84a7bdbfd3c5e5ddbc2a82758989398db760a85cb70c391e9df1c5001aaeb9336b71e91a937d8ae63b31b6290ae0a0f9542107e6cf53c1ce0938a00cfd1741f116ab732e9ba7cf6ad249199240ca48538e18a838ce38cfb9a00dfd2b49f12e89a14faaf892591af226d874981b3198c64804bfca4f3c72001c50069f867c48fabc6f04510b199655124f29e195b8283273db8e3f4a00eac69da55ae94d737b2ba44480eefbf24938190a013cf3f4a00f35d66cb597f1aa4d657334369e53c8d39664ca2b1dbd705b800e17d7a8a0c2a6e741e1593521a4cdab4f79fbbb458a280ca3e794b31041c0e990c33c70bd79068365b10789af358d1efad6e7c5d7122da5e446282fed99bcbde40c2b29e54e31863c73419cf72be93a04dae6a634d975936ecf6efbfcc7dceaa49033c8ce0f7e09c7714105dd1fc2d1e8567298fc42da96aad2930ccca55060b36c0a87382cc093dc8cf3400ff0f789358d4d6f25d7acdeded56e51df5186265e4b60838fbc08c127d6ad6c6d1f84eb2ea5d22cac5f579a38cc6c079314586ddee79c74e6b5a7b8a7b1e673f87f50bef11c7e20bbf3adade5595ad630ca4c8c1f030573b579cf3e84d68646fe832bd8e8eda85ccb2c0a97290dbc50af2ace1c61b1c6700f5cf4cf7a00a3e20b3d57c28f1ea4ed3ead6c61db7200c4d0a6f2c58e061c0cf4033de8029e93e1dd0b5ad492cef92e1ede6560b218dc0572acdb73b480db46ef6e075a00dcd3fc3ba0e91a227857c22f2c42090cbf685909903700b3338f986001b7d338a0093c3ba84a10ea1ab4b15dc1048248d11f2ec3af0bdbe86803b0b7d6c6a5a236aba4279ed3336336ee0000739183d38e9419cce463f0a5c585eaf89afe081e59d5e2f2d21c7944e7e660dd72a7381dcfa8a0cd6e6c5869c961e118f536b0599af351588b4b2305285189001e991818cf7a0e82d59f86468fa9b5ee9c19964810b5acae1d405ebb0f51c9ee6bab0fb9c18cf84bd0584f15f37f6ae8e0daba91182cacccea796c765cf4cf5af7a1f01e553d89ee365fd918a5b6f292da3db09da148c1c8031ef41a1078791aca2b8864732c89200e24e0aee19c039f43fa564e605f9a491fc3f0476ee17ce07ef632c39041ef47b4033a7f0cdde9d188ad89669937cdb829624771c818f6eb4d4eeec05b9ded74bd22c6cee2555905cc9e7020138d91e03019cf73f8d680477563b5924d3665855b0597a2b7e1d8d01c8996b498dec659ac9678ae4125a440bf346f9e99e84608e9eb59d5f84528d892569a58e5fb5e01c7ca14f0bee7fcfa5739b4363e2dff0082befc3dd7755f0cf833e226836b05da437b75a36a31b8501e7205d5a824f40cf1491311d1267ee735e4661fc43f59e00c43a7887854789f8c2f75097c2df05bf6c1d126bf13c7a9e9d25c416d2a82b1431cb2b3138e8c915e4657209fb3ed3d71584b789fa542950c44711849ad5a6cfd457b3b4d5221716b35b2dbc902cc9b32102101830ee011ea2bd6a0ed03f9bf33c13c3d4708eca4c988f2754b35b3b49a402c155e50bf2e7737af5e08adbda1e5942eec6ca4bf3388cc2cc363ec38041f51d28f6843dc9b4b16ef1496d04e55031308909214018c71d7919fc68f683890ea4cb6f626768fe689c3b4928fbc71d3e949d4d0a39dd67c77a5e91af69d7f7974f048114c91a9c29049dd95ee319e7e95e74ebb2a9c79ce8f556861f17b4b25bcea1662a25864277679c63a03ce09c573f373ea7a9461c90b1565d2ec6de4fb7de59083c993e68fcbcee3c306e06769dca3eb9a0d872af89cebfa8df36896f1e9b3dcb4e0a3b0018f248c8f507f122802aacda3cf753deffc23eb239cc8b34a014965e71b94633d48071dfdab07b8b92e4d6377616da2861be3920768fec9fc2573f2a9620ee18cf7ab8752e34ee6378fdb5dba860baf0bdb2bcd2a27996acdfea9149fa6e56cb74e077ad04e5cba195e14f1a5dd9f8b61d36ff43fb3cb787ecd3cb1a1f2e1cf20b6ec93d3af1f8d023b2d2ec2eaeda4b29cc7343370d7124bb7cb6cf03681cfe00d052826ae55bab530ca2cf4db4f267bccdbdd5caef2226756190181054d44f62e30517739bf11786750f066809a7df6bd71aa667291aa85d9b7aae4e339c7f915c732ce1efdb57d3a37b2958c28f725e58e1c287e4b0538c77e073c66ad01a3abde3a595b347a42594d329f30a3ac830481cb0279efd78e6803c97e2458ebfa0ebe9aa699aaa9b48a3fb3aa7980f9b274e4e7924a9c771c600c9c9b0a4af12de996be30f3a4bcf12e9f226e21223bb20803209e3ef1cf27dab684cf1b154cdab207036722ba53bab9cd4d5a091a1b008c73de996588604d9d4d63302da7de154683eb586c048bd07d2ac0fc67fdafbc49fb327c2bbaf067c12f86fe0fbe1a5cd7b36b7e2cf32647be9ae2745863b6124202b469860170061f20658d717b489db454dbd59c97ecff00f03f5ed5fc33acf8b7c11e03d425f1178cf5e7f087c2cd34dab86bbd4e59a36bd9637dcaa82d2d648d19db2a1a704f2871cd5a5cd2d0f462ac8fd2bfd8f7f636ff008646f83b27c1ad2bc65a76b3aa5d5f3dd78c757d2a76db7539c0f2e32e033222831a13b49259b1f362b28a5cc396c76adf0f63b2d5678bc3b1cb6f6b7212479ae1c33aafab3f393cf4c678f6add24b620d35bff0ebf8893c35a36b91cf67a3a8ccf02109757072188e39001da33dc123ad0043a8f866c353d7d7c49a4ea8b61e5c3fe9b14926d4954648271c8619ec326802df87fc39a8e82352d561bf37cb2c8be4dc5c46a982140217baae46077c0e79a006eabaa59d86953cbae5e177743fbc865f9173c1c0e0bbf3d3a7ad005ef06585beb367a72f862f3ec73cd346c2066dcf7079cef279efd073da803d03563e1cf10ea56de1750ebb091b9e152adc6491ea7ae0d004cfe0dd15c0874f4b48234cc05a08bcb5998725be4c6f273dfe9da801da85d259be9be16d2fc317ff0060d3b74f7977c2466e390181ce00087681ee48ef40163c430f86fc55f64835289f6c32acb0bb124a707277061c1f4079a007786346d2bfb46e359d32d8dab79244934d7058b91d7207ca0e00c018c679e79a005d53c40345d2a5d496ea79097d82792d959d39e91ae4e727bf4ebcd0073da5f8d2c7fb38e93afdb988b4e05b450b8571924a6700e5877c1033c73594dbb81e8fa98922953479b4e92e844aa8d22162fe6f73c75c71d78a706db01de21b4b27132ea569776d6f611c66dfed112929d73b01ce48503d01c7079e34134999de22d37c4d3e9f6567e1bd3ac6df4b8673713df6a326d924ca285f3073b7b11eee476a0655b8d67fb461fecebbb78e6b7de05c342018cf39c9de0679e4714194f726d3a5b26bff003e5b65b64b4f37ca921b6019f79183bb1c12bc63d3141045aeea36da5dbdd6bba3c172af348eb0cf09679264ca9644049f6c9c00319ed401e7fa5fc43f115899d350d3bed114b8f2ed955f11a85002e4fcbdba67f035a2d82ecf4db1fed0baf0de9d0e929043b2cc3dcdab8e43bed7dac4771923afe35a4370bb2d6bd08b125f578a208da723182176604ef2493f2e41c6eee79dbea6b4032b57d075fd6b42b2bbb1d71745d32dd0dd4f69e50695485c127819e133827ab630718a00ce6d5afefd7ed16f7724f0c7831dc49122961c80d8c719e463a8ad6297219cdea5dd3b50d4751d45b54b8b5f32ded108488300ab900365723ab1ce30791594be222ecad77a835cdbdc7d99e05528de5db44814b37f7c91d540ce4fd7de815d9c6f856e3c753ead35e695726e5247297335adc288cb0e9b481818ee01cf1cd06f1d8f4ed2eeafaf7c2b6161aedf3dadc4369fe96e0921a5279271d7b503b5ce919e0586c1ecf55178d3da32c48205da76866c67d3e52c01fc2815915e3f08699e24d120bdf10add48f6eaf23c30b94504636f008c9db81f9f340ccbd3753bcb4dd2d9cbf664954470dbc99671b796073c74f7fc4d529729cb562e5234344d76fef2f8dd4d3daee58b76c9032311d01c8e31c62b45899c35b984b0fccac912df6b3a82c53a3a43e6ba6e01e46d88bfde27b1f4f7c7e2febd2ee67f546713a6de78db5717177a3dfc3115918ca6ea20ad215fe22082471ec3d8f7a5f5d7dc7f54675d6175aee97a1dbddf8882badbdb16b89900196dc718cf4edc51f5d7dcd29e0efb9af67e2e3ac8d3d2c6191249607468ee1972bc16048cfb2f4ecc3d68faeb34fa90fd3eee44d3a35bf16ef71046e646923df86c657b91c81dfdbda9fd7a5dc3ea48c58756d767990ea160c19a6390917c8a073b867b51f5e9770fa913687ae4c2ea5864b7c4861c8d814f19ea403c1e9c54bc6c9ab5c4f0b4e3f18b7979ab4092b093f768332b000051e98cfcc723f4a9fad484a961bb9e03fb617c19f14fc72fd9d7c49a2595ccff00da0205bbd3618a562b7450b7eec73cb32874518192dd79ae5aee533ec382330c265f9da9547a33e7bfd99342f13f89fe065b7c04d5bc3ed36a7e1f9e2d5adaca4b49638cc2f27f682444c8a0461adee754b727fdb8883f30a7868b517cc7ea79a63f0783cc3dbd295d4a27dbbfb3c6a5e2fb4f823e0af08f8ee468354b2d122b3bf79486696348f6a4bb9739628aa4e7d4f735d6a524b73f11cca7edf1d526b6677b6daadfc5a70d2df529613e498c4ccf83b761c1cf7e7b75a7cd2ee79dcb1ec658b3f115b3242f79e629259a6958ee23b7cd8c668e697721c637d8a3a46a373f6e93c3c27db28814f9be61cee182ca0f4e011c9a97295b7172c7b16758d4eea5d2a48aeb5982647c2450aa976271dcd4f34bb8d462dec64e93e18d3a1d3bced76d92fa4d80ef1b416cff000ed3c71e9d79a93ad422b64753a8fdaed7c3171ac6973309563fdea8219836327233dc633f4ace52516515f47f150d62e608b535de8da5ab4b248472bc6debd0e54f03a7eb44669b13d8e84e9f05ce9d2e8fa8ed6df132ab1600924a90a09fe2fea715a1a5349a33e5f0f47a5c7f609755580240c91c6c42b0c8e0907903a75ae5937cc4bbdc8f4d82f60bebad3aec5c1b68c064220ca90171fae323eb4e2d85d991afb43fd953416f338372c4361c858b8c73d31d01f5aabb115b6da58e9705ac7730cb18887fc7cb92d1a7cbdd7afcdce4fe745d812f8bdec2ebc3b6fa958b44248989dcb8d8c4753b87439e7f01ea68bb01be17f114ff006ab9b8d6248cdadf0856da558ce5c80581e9c9f9b183ee45293762e0ddcd6f103583a3e8f7777c298d776e3851dc32ff0008008f987ebdb335383f12c7e151713c1612978a793097015d932067839e40c60f6e46680327c3da7dfc5a64f673ad9c88f237d91a198636b2866fbd800f247b10464d00739e3dd123b3d2ad353f10e9966969048a91c85813238fe12172a327183d78342dc04b55b3432234773e4436fe5baa61c7cd9c1c027a1e33f5ef5a5bb1cd5e9f3942d6de6b4ba78274d9b40e18f18f6aea87c28f2a71e59346842a77e18714dec28ee5c000e00a82ec8b1803a0a002b7a5f08064fad6b6407e04fc03f0978f7e2bfc51b9f8bde10d2c6a3aa5bea1059784749bb3e62cdaa5c4896f68b97641889a449016f977a2678271f3f767b74284ae7e837ec99fb357c67f833f1ebc0b73e2fd62d6db4ff861e19bc0ba7dbc8e6582eaf3297378e199c0bb9594a484100208c82c016a0eba94b915cfae346d5fc2d75a43ae89a4dcbdf6a132c4a249480a3760b8c1c30c0271551f88e652e646a68ba31bbd5aeafb5701a18592d563371b53ee9c16001392381c6063deb611cd6ae5a5d624b0d074c4b6b5423c84b18154c99ebbbffd740156dac62bbd4d74a314e1653f3ac032f9ee4e71c751fcb3401a92e9121b94b4b312cb6b1c6abf6798a8380064b903e5c9c9e7d68030b5cd09bc4dadc02e6d9ffb3a18fe4bd232210a4065dbc05009fbc4f3401e8be02f04786ec601abe853b2c96b88e2bbb86e876fde53ebd7e61ebed401a5e11f8842dfc4075386d6ddad6d669628ae1622647751c80338231d3de803b086c0dcdfe9ba86b123030c8ed28404c6a4e4af3df033d075fad003fc49a6ea3abce96cb74fe45bb796c8f20db211c63fdde7803be7d2803075cd29acada285005675042af4500fe8a3b9fd28035ae3c3764d6713cd1411011e5a48c81bc91cb3606723ae7340197ac687a9f88251a65b5d21f2b631b854003a6e18451d01f7e4d006c786bc11a35e6a8825f0f2ac81cc865dc1e225403919ebd01e83a5633f8804f166af7ba2ebd25be9d797023b34fb4cd22e096c9e108e7ef31e2945f2b03634db2bdf17e9379aa5dc9e7bce031b67987000ca60e381d0e31fc557ed00b1e22d37fb6f4586c0a30b62a3ed0cf85cb0c70c73823233c01f7867a73a01852e8e2df4eb865d45e7f29498e18e36760b8e7b927dce31e8282651e61da05c470787049aadd3333e64168f0150470430cf3b7a609193e83a504fb332756b7b7bb917478756b889276659e5f336944c8ddb4007dce7e941125ca47a5d9d9e976d1e9d697d6b3208cabdc15f9c12400303ef75e4f5fa55ad895aa26f893790996db4cb4930b3dc2a3ed76fdded1df3f87fdf38ad69ee30f02ead3dfdf48faf4a229a2b431246ca59230c73bdbd41c061ec0d6806ddcde437167268af7d047b5c07959b6acb1654b0ec0f38c7b668039eb6ff008451e79b4fb2d5249196174575631a2f4ddc11c6e27ef76c715a45e96329ee2787f48bfb4b5bdbcbdd34cac8a9e52c773bb7282006c0e597af3d4e09fa4b8dddc82978825168c2cee6d1617bb71e5cb29c3b1248007b16dbc74e73d88272813e96da3e9570904704b63019d04cf141fbac93f7719e1b27b526ac6f1d8d5f18f896c0f871f490c60b9cc6e18f3b7703853e84f4f4e79f4329dd8a52b16bc37a95c69daed8d9ce25904a5dede323042045dd91ce49f9883efdf39a64a9dd9d4ea1afe84b771d8c9772012c4371d9f347c01bd3be71e9ce1beb59fb435b2ee477b7fa509609ec7479e6b749bcb9a50a013b81c923b1e80e79a9726d89c13ea1a068f1c976faadb5fa4914f6e3cb5b9b7c03b46391938e001f281d3a1a96f425c15b42b78a8dc695a03dcdd10124976341102ce491c11919033818c1c54dd7627925dc826b113e99f60b4b3468a28d182acb8661c600cf03009cf5c8345d760e49770f115ec767a0b6892e9a607ba81f642aca3cac1ce01c601c9c7b9cfa5178f62a29c4cdd234f93479b4ed5e020cd2f976f2466524db9c040c01ee49e738c827db0ef1ec5abb6757e21d68787ee6d964b33e65dee42123cacb9fe16f41807079238247404bc7b1a727985e6a3aadd98bec90c76e521555b56c9761ec78cfe545e3d8393ccac7fb26c6f9f57d42da6b6b894796ea6e1b0a0904b0001e7e514e2d762274e2d6ba99dae6abe1e5d1ee5ac357379346c42dbb02371e339c8ef8cf3fe355cc8c7d8d2ec433897fb2177dc3451c5018a6202ed4906e3cf3ca70dc7bd69ca694a34e947dd5af72b789f55d3acb4783c69a58b20d0b4cfad5c41180af6ed0321438f98a0c2be32398c7635516968774b1f5ead1f6527f31356be8b4eb4b1b06bb58ef022cf6cbc2f9909c7071c0ebd3bfca7bf1adb4b9c553dc8b5b9d57886f353b3b38751d3e68a56031e4890a100fca4e476032691c9cc65ea12ea3a95d4e1f55902dbe49f226e51872232b8e0b1c8ef93f5ad553bab98caa599a57705c785ac1b58bc8a3bdbc73e5b6170506785dc7d8fb01d854ce9d9053a9ceec72babf8966b0864d21fc2f2c335cb62dae890caa7d77ae46474c75e2b2e535564cb3a7ea76775a3ab4ced70b0440cb3458c95e9bf83f797f97ad46a6fce4f16bd6d75a09b5d39e75fb1cc16e84c9891a52bc8de4e01038c8f4ac2aa6e43e7451bcd4b49d2349b1d0ee19c3dcda4776b719f9a37dc400c3df1d33fc75304e32b8737368761a8ebba05e6836faae97ac2db4cc431121ddbf07078fcba752c7be31b7b4358cb9498db69f36a571693dd437778aca2659643b949e5581ea57158cbb92f52713cd1cd2269fa995903ee62e449b36f4cf4c8e4f19a88caf70317c54be2c9f4a6cc9a7dc594cc5e492284f98a80658edddd7af39abe6028e87790cfa6b18253b16311c4f282de61fee6dce, 'Sảnh', '2025-05-22 19:55:00');
INSERT INTO `propertyimage` (`ImageID`, `PropertyID`, `ImagePath`, `Caption`, `UploadedDate`) VALUES
(2, 'PR00001', 0xffd8ffe000104a46494600010101006000600000fffe003b43524541544f523a2067642d6a7065672076312e3020287573696e6720494a47204a50454720763830292c207175616c697479203d2039350affdb0043000201010101010201010102020202020403020202020504040304060506060605060606070908060709070606080b08090a0a0a0a0a06080b0c0b0a0c090a0a0affdb004301020202020202050303050a0706070a0a0a0a0a0a0a0a0a0a0a0a0a0a0a0a0a0a0a0a0a0a0a0a0a0a0a0a0a0a0a0a0a0a0a0a0a0a0a0a0a0a0a0a0a0a0a0a0a0affc0001108008d011b03011100021101031101ffc4001f0000010501010101010100000000000000000102030405060708090a0bffc400b5100002010303020403050504040000017d01020300041105122131410613516107227114328191a1082342b1c11552d1f02433627282090a161718191a25262728292a3435363738393a434445464748494a535455565758595a636465666768696a737475767778797a838485868788898a92939495969798999aa2a3a4a5a6a7a8a9aab2b3b4b5b6b7b8b9bac2c3c4c5c6c7c8c9cad2d3d4d5d6d7d8d9dae1e2e3e4e5e6e7e8e9eaf1f2f3f4f5f6f7f8f9faffc4001f0100030101010101010101010000000000000102030405060708090a0bffc400b51100020102040403040705040400010277000102031104052131061241510761711322328108144291a1b1c109233352f0156272d10a162434e125f11718191a262728292a35363738393a434445464748494a535455565758595a636465666768696a737475767778797a82838485868788898a92939495969798999aa2a3a4a5a6a7a8a9aab2b3b4b5b6b7b8b9bac2c3c4c5c6c7c8c9cad2d3d4d5d6d7d8d9dae2e3e4e5e6e7e8e9eaf2f3f4f5f6f7f8f9faffda000c03010002110311003f00fbb62d27703dcfa015dcab5f64738f1a43a9c84248ab8cd3026874a2ec01419fa52a8d7281606932838442477c560e5cb1b812a696e300818c75c552775701ff00d95c7ca83f2a600fa4ab70cb8a00a773a3c832443919ea050066de68ab283841fe141328a919175a5f94595978078a0e69b716539ac193194fce82ae9918b55391b79ec6821a717743a3b45fba40fa0a07195dea4ab6fb06d11f7ee282ae871b77206781ec680ba244b707a293414937b0f6b30ad800f3eb416a0ada8a2cc83900503e48966fad14f8375395885da2d40cf7ff004a8aa5c54b714af18e879f69d00bab966033be66238eb926a8229729d8476860b744009da30714198dc1f4a02cc4650c3068020743c8028022650be940119553c014010cc85c05c81f5a00acf18ce083c5034ecc6ba9f4e82806db6452aa85dc41fc28115e440c4d0044d183d40a06db643246bfc4b9c7a502202a02e727eb9e941a53dc609f1c0279e077cd06868dbf827c57730adc43e1fba2ac32a4a6323e879a56901b1f037f699f007c5ef0e47e27f855f11745f17694ca3373a75ea4ad1e7f85cafcc8dfecb80df4ad5c3b038ca3f11eb5a1f88740d6709e6f9129eb14dc7e4dd0d66e352e06e2e88187c88173551836fde0248f429557729eff00a54ca2f9ad6d00b11e8a841321391e944a2e2ec801b447ce467146bd42cc8df44979083028b3227271d88a6d21f66c2a39a0add19975a0139c23019e98a049282b5cc9bef0f90486880e7a8a01a8cd1917ba14a010a060f6a05ece26749a6491c9b5a3381df1409c3b0d168d9e140fad0472f912476cdb4ee240f6a0d5455b6258add1463683914038c587d94c6d95e01340d249587bc60ae36fe940c154018c0a006789e616fe06ba5e826b985091fec9327fed3a052d8e2bc176c24bb856504f39ce3d0668220d729d8c8140d84e38e2832a8b91e845e546e9907e80d0657910bc20b12a78a0d88b00fe3d6802b4d16ceb8a00af2c889939fc0501b9566bdb74214ba86f73c8a076644678589dd32e4f2467a501664173a9db4598c48189e94045372b32b99fcdc10f91d7ad0392516857381b83638ef414e31ee4134c0283bc018e79a0ccceb8d5a349961889795ce1634e493e8050166cd0d23c2d79a905bdf11eab0e936db882b30fdf37ae23ea3f1c506ca2a27a3782340f03e990fdafc3e82f245233773fcce3e99e17f00285768a3a337609ceda396607e4478eff00e084ff0017be11f8847c4cfd81ff006a7d4b48d4a03bad74fd76ea4b49d475dab776a30d9e9b5a251ead5b4a373a7da7b4dd16bc37ff000538ff00829efec23751685fb7afecbd73e27d020711ff00c253670089f1d322eed83db4871ced650e4f5619a985efa849529e8b73ee3fd8f7fe0b0ffb15fed3a6d346f06fc678342d6a7c28f0cf8c76d94e589c6c4666314849e82390b1f4a1d45d0cdd19a3eced0b58d3b50441771988b7464f994ff5fe756b546474769e1c86ea312db05957fbc8c0d292ba0d8b83c2a1d31e4022b2e5916b52adc7841cb6634c56b0568932dccfbaf0dceacdfba2401d6a1c629f988a173a23a0f994f1c62972484d292d4cfbcd0c48092983e98eb47248124b6316fb412bb9562c7e1523312fb43930415fad00654da7b44d860460d0030da282412734000b3c303ce3de801ed1a9f959471400dfb30209c9e3ad0035adf030b40197f124b47f0f820520ff006b47f75b191e44ff00d48a08a8da898de06b0dccd391c220c1f73ffd6a098c39a28dfb8843364b1a0734dec5798042029ed41991f9c01219680212ca3926802b5d4c18e17d3d28031b567bd2bb6dd48e71b850553d5b387f89daaea5e0cf04eade31d435ad3ac23b0b29658ee358bc4b7b7f302128aceee8aa0b003961d7a8a0eaa34a75e7cb0577e4781fc3aff829f7ec5dade97a2d878e7f689b3d3b5dbfb380dfda369779e4da5c328df19996268c00c48ddbc803a9eb45d1d32cb71f07ad33eabb2f0e58cd6d1de5ade09a3963578a68df72ba9190c08e0820820d079ce6d3689ffb321b73f28278fe2a0893e6630d935c4ab6f044d248fc222ae49f603d6816a741a17c12d7757db36bb30b183ba63748df8745fc79f6a06a3361e2e9343f8664e89e10d3962bc68bf7fa83fcd2807b027a7e18a0d631e5382692f2f6e72a5a59647e7272598d051ea1e05d217c1fe19377e25be8ad5a66f3184cfb760c74c9ef570dc01be29f82b71f22eeea650702486c64656fa1dbcd6a06758e85652387b667849eaa0e57f234041da26ba7862e6e2d5ed2e6ce2bb82642b2c4ea087523054a9e083e9513d869d8f9c7f686ff008231fec19fb4734fa96aff0007cf82f5b989235bf04b0d3e4dc7b98429b7724f5263dc7d6b38c5b896aa34792f87bf60cff82c1fec06e2f3f619fdab6c7e28f84edb0d1780bc75fb993675f2e21339897fde8e6849feed5c24de83e683dd1ebbf067fe0bd963f0afc496df0cff00e0a45fb3278c7e08788256110d6a7d327b8d22e98705d1b6ef09e853ce5ff6ab40e48c97baf5ec7e8efc02fda3be0afc7bf0945e35f84be3ed07c59a44a07fc4c740d49270a4ff000b8563b1bfd960a7dab9aac657f758b9651f88f4b8ed7c37aa63ece177633b7386fcab9d54ae86945a20bcf0658cd1e2d4e189e0373571af67ef038ab1cf6a9e10f25cabc5cfa8aea854537a191cf6a3e1d00950b935a01897ba09e549e7b0c5612f898d2b987a9688aae5085570a18ae79c1c807f43f9521181aae8c002415071eb401897369b0e5980c7b500441d0128c41f426802195c30248cfbd004618af1b87d334011fda60ce3cd5fce8031fe26cf1af84a1b75914996e5980079c80a3f939a0993b19de12b8f234c66c005d8739f6a052972b2f49792b1c171c7bd06726e4f4207b840d967031408864bc03a100d0056d42fc5b5acb7b206610c4d2305ea768cf19fa50000004b79d9f5a000a211f7bf4a035b687e557fc16774ff00da57c7dfb4a47f0e6cbc2be22d5bc36fa55bbf84adb4fb79459a48514cf2390363c9b83a924e5576f38e0f3622b469537296c8fd2786ebe5584c994aebdac9bbf7b23c2bf64cff008253fc7dfda675bb8d4649ac744d1f4bd4fec9ab5ddfa4caf048a46f55468c798769c8da48ec48ed9e1aa2a89492dcf3f1d9b51a336fbec7ed87827c3507833c1ba2f80746b8b9bb8f48d2edec2dde5f9e5952189630cd8eac42826bb4f88ab52756aca76dd9d8f87fe1adfea72a49af5c1b488ffcb28c8327e3d97f5fa50118b6b53bfd17c2fe1dd02344d234f8e363c198fccedf563cfe1d28358ae5468cbb150b3118519627803dcd033ccbc77a47857c57ae349a6dc5e5fdd8509241a5c224008e06e73f2afe7413297295344f853e32d35e5bcd3e5b4d2da51fbb3201733a7b670157ea39a0a35b43f865616eef7de28925d56ecb67cfd41fcc03d82fdd1f950075314490c62248d5428c00074a5ef770398d3ecd5b6b6c04f7e2ba40e8f44b36dcacbc544d5d01d7695671ce8ab756e1c1e0e4751534969703a2d33c0da15d0cc08d0311d633fe456aa0af7337369e8696b3fb3ce85f113c3571e15f19f86748f10e8d78bb6eb4bd6ec63b88265f468e45656fc454ce51a6b56546acdbb58f97fc69ff0006fcfecddff095c9f153f64cf1bf8c3f67ef192e5a2d5be1deb0eb64efff004d2cdd8a3267fe59c6d1af6c5652c4412d0da339b7cad686be8967ff000592fd932c161f88fe1bf067ed23e1bb4186d53c2930d03c4c912ff1b5b4c3ecb70d8e76a38627b9a88d5a552567bb2dc60d6d63a7f833ff000567fd99fe2c78a8fc339fe21de782fc696efe55e7807e24d8be91aa432ffcf3093e1653e9e5bbe6b474d2774b423d9cdfc3a9f415a78d2eb562a003b9d7239c83571f6763197b4dc74c2f6e3e6317f2abb25b157453974b9a777f94295009c9edfe4566d39aba1a4e4ec8f8e7e38ffc15f7f61cf813f1913e16fc4bf89b7ba6ac8c60bbd48e8d3bc314a8370036c6cc5712a12d818e3af6718b5b9f4f8ae15cdb0342152ba4b9973257d6cfc8f66d77c6fe12d73e0bc5f1ebe1dfc46d3756f0c5de9c97b63abe9f17da63b9818801936919e4e08c6410411918ac9ee7cdd48b85d3e878b3fed0d6face24d2fc5f63708c7afd9da2c0fa48ab41cfed3c89e0f897a9dd2ef4bdb79013c18c83fc8d05ad513af8c7506224922077739048a0249b56448be2412fcf207527a827ad061692959b1dfda16f331c5c03f5a06d5918fe3bbb840d360671fbb32b924ff78a8ffd928257335b3093c57e12d2210da978974fb7c2e4f9f7b1a63f33402537f65985a97c77f823a4e7fb67e30f856cc0fbdf6af115b478ff00be9c50528557b459cf6a9fb637ec87a38ff89a7ed4ff000e2123f864f1c5803f979b9a07eceaff002b312eff006fff00d886de4ff93a6f044efe969e20867fd2366a0af6359fd9327c53ff000504fd96eebc397b6be0cf8a963abea735b3c7a7da436778639e561b514c890304049037741d4e0500e9545ba2e7fc37d7ece127eeb4fd57c5f7d229e469df0bbc41383ec0ad8907f3a0bfabd4b5f43adf831f1f34af8df73accde1bf05f8a34ed374a7b78a1bff137866eb4afb6c8eacce228ee912460802024a0197182682274dd34ae7a05ac3757f30b6b0b592795ba2c4a49ff00eb54ca2a4acccf5e86c780ff0067ed3bc3b6925b25947a6dacd72f732595a364991ce5892721727b0e3d314e31e5562d41b5a9dde91e0dd274888c3a65824591f330c966fa93c9a610925a22b6a979a6584a6d16efcfb9cf1696c86597fef95048fc7028352aa69bf10afa68e6d3520b08d09656be3bdffefda9c7e6d4013d8fc3f96eb7dc78e3589b577de0c51c8e56041ff5c970a7f1cd006f0b5b7b3b55b7b5b78e2451858e240a00fa0e280219172a79a008b0719c50030c484e4afeb401c9e8f72b808071eb5d0074da3dc4795dadd3ae694ad603aed1a545c73c0ef4455a3603b3f0fcb196539fe1e2b44ac6127efd8f4cf08bc6d61b5181e46466b8b15f123a2935ca72fe37f162e93e2b3617778d6cb80639036011c639a70845d246729c94cee2dd36dba23c9e67c832c4e77715c6fad8ea5ef4753e73fdaeff0066bfd9a7f689d3e5f0afed0ff05346f125b4795864d4ac12496df3de19d42cd01f78d81f7af5e945ba47246a4e9d44d773e66f0afec43f19bf676bd797fe09ff00fb746a7e1ab58958c1f0f3e2de75ed00f1f2c30cd215bbb25f75694fb526926777d6a94b4acae177ff000516fdbb3e0a6b32e89fb63fc1cf0978334900087c7fe0cf0bdff8af44403ef49706cefd6e2d53de58947bd0da67761e390b82e6e794bb5d25f8a67aa781ff0068ef177c60d3ee3c57f0b7f6e8f839ade8e96d0bc97fe1ef86d777d18ce7e52575a251864e55d411c6452492d8273cb294bdca13ff00c0d7ff00227c69f153fe0911f063e2c788f469ac3f6a6b5bcb73e2dd5358d6266f0f5d828d77696f07ee83df39ca8b608327a4b939d83394a4d9d98ce2078daeaad6576a2a3abec7ab697fb187c45f821fb25db7ece5f067f6dcf1049e1fd02c1a1d2741b3f076985a557b933c8a65b8b79643f3bbb0f989e8071815078f5717467ef3a6bef67cd9a8fc06f8a3677124773fb5078f229558892182c74683691d8ffc4bc907f5a0e6facd1ff9f51fc4bbe1cf825e20d54017bfb49fc5190e48654d7ad61cff00df9b54a09fac47a411d7e95fb295a5e20fed0f8ebf16a60dd76fc4ad421ffd1322500f12d6aa2bee366d7f625f8737983a9f88be246a271ff2fbf16fc42e0fe02f40fd2813c5566af65f71a969ff0004e7f811a990f7bf0ef56bc27bea1e2ed5ee013efe6dcb0a08589acdd89e6ff825bfec9fa96a0355d4fe05787a28aded922985f44d70aec097691bcd620121c0ff0080d06bed2adb736343ff008269fec796437c7fb3afc3d419fddb4de0ab27661ea77479a09e7aef791d2d8fec2bfb36e8e8ada47c22f025ab01d2dfc1b651e3f24a0779f565a6fd9a3e1f6851f9ba2e85e1eb523a1b7d161423f202827deee7937ed69f1d7e0efec63f0f22f1dfc48f115fc897979f64d3ec7c3fa5c52cf2cbe5bb9c06755550a8492c7d0724e2a65250dcedc1e13118da9c94d9cefec05fb577c1ff00db37e1c5f78834af1a6b567aaf85e3b3b6f10ff6de9b0431cf712c6dfbc8b63b2b2b34727cbc15c0c8c104d0b1584ab8392537b9ecda96abe10b2f96dbc631bf60c563504fa70dfd293692d4e43bff00865e1ef0cf8a74ab3bf027943c823919e418639e48c0e9cd34ee80f448e2f0cf84a3fb12882d437dd89065dfe80659bf5a0055d4f5cbe1ff00124d0cc687fe5e3507d83ea1172c7e876d0029f0c5c5f01ff0906b171720f582063045f921dc47b331a0c24d37a1a7a569361a6c02db4eb18ade307ee451851fa505295913b5b8f9885fc282a0db5a919429c638f4a0b229c7ca001c679a00ad24450f1dfa0a00af2f5c11839a4ae0021c8cf982981e7fa64ac30464015d1a7703a6d2ae1957720f7fad6736981d4e8b7d803cc6e31918356b557329292d4ec341d4712afcfc0e9cd6ab620f44f06f8812da55477055bad6388a3cd0e6438cb95dcdaf15f81bc21e3d860fedcb059c40e1e190632a7eb5c119ce94add19d3cb0a8ae8b97d776da169aa91b711a048831e7daaa953756766394e34e36479378f6fc6a33b3cedbcb37cd5eba8b853b1cf192ea792f8d34c89e692e61c0607a571d49348abc64ed62a7846d4dc6a11daddcae519b042b9f4a509bbd98d4228e63e287fc136ff645f8cbacb78c753f8773f87bc521ccb078c7c15a8c9a36ad14c7fe5afda2d4a348dff5d438f6ab8c6c6aaad48ab23e30fdb021fdb6bf625f8a717847e1e7c43b6f8a5e1e3a6457704be2cb282d35b8919dd4c4f736c123ba236677bc41ce793c66b277ee572d3aabdedceb7f661fdb275ff8956b616df17e493e1c5d5fce21d3dfc5f6f25ad8deb06287c9bd61f6673bc15d82432647dda443a4a2bddd4faa3c4dfb333f8b6c12ebc55e3ef070668879778dab7952e0a060436dc38dac08192307a734193a53bec7c7df07ff00697fd93753fda625fd97fc65e0ef18687ab41ab6a7652f89fc597165a6e8c64b28fcd7659c5c333248ac86260b87f314f032406ef0957d9f31f505a78d7f63af0d8d96df16be1a485701fccf88114ac067b03363f95044694edb176cbe3f7c0486de2fb078c7c1d3b390bb74ad49aeb04fa7979cfe150e6914a94e4ed6259be30f82a41e65869f2dde791f62f06eaf7408ff00b6701cd2f69dcb7859455c834ff10697adeb6ba8ff00c223aa952018967f841e21291b150ac41167c838e84e3f1ab4ee8870923d57c1ff0009878b7c2b77e23b35b58cdb26f36cff000f2fa095be91cfe5b9ff00be698285cf23f8e5e33b8f827a96a3a5ebdf0d6f6e6e200a6c121f0bdac62eb722b0399af53601920939c63d78a03919e1d7dfb6378dd9f6d97ecd6eaa47cc64bcd15483ec0ea1d3f1fc28138a5d4778a7c4fe02f8d1e053e1bf8afe15f041b7d42d15aeb4fd4a1b59cdaca53b1f9d048858e1d49c11906a271e61c2ad4a4ef09599dafc03f859a4e81e0bb4f0dfc0ef06f8574dd10b158d346d0e310cae802177754db23e1465d98b1ee6ad0a552a54779bbb3d0f53f04fc3bd01c43f123c73a624a4e0d95a58c2256cf60a159bf4a2c99274da0c3149a2c16be06f0adc69fa5d85bf970cb788202e473b95396e73d48193401d67867458edb4e8eebca896e24dc6591392f93ddb193401ac902274fd6801cc88c3951f5a0c6694751c911c600c0140462dea0ca41e01a0d88a488b1c8fd6802bc8bb410ffca8eb602accecad93823b557b2f302aba927b13553b2880e5452a09603db35981e7f628360031ef4d24f70377472553693c6782694ac9e806fe953856018f02ae9b95f513b5b53a3d2afca6d70d9c75c1ae84cc1d9bd0eab46d7b6e32f827af3c5691d5d80eab49f19cf0a6d8e723ea78ac6a518390eed15f5ff154d76312cd9f4e6b5a34e305a0be2670de21d484d292ac7ebd8d5ce492687cbad8e1bc50e763b819e738ae09b4d9ac62a257f013249aec2acbfc638229462dcae517fc6bfb4efc0af85fe289fc1be37f19b596a56ca8668069970e00740eb8758ca9f9581eb5a4db44b9a4ec783fed21ad7877e38fc43b3f13fc3bb4bcd56c868f15b99c69b2805c4b2b11f328eccbcfbd6409a91ef9fb1efc20f05dd7c34b7f0ef8d7c288c6e23b84b8d3efe13e5bab48e42ba371b4823231ce6932d392774cf20fdafbe007ec71f017c5fa569df0bfc0165e12d4fc492cb058da68da45a9b2b99a180c8c1ade6430c7f2293b90c4ed8c6ece336f979743b292a95694a71da3abee791f86bc6de34d1bc6cb269361657f676f1b41ac5968d149693aca0ed5918488e7cb53b87c8f2e4671d78939dd9b766cd5f03dbfed15e35fda266f89da57c63f0c1f8771599b47f054f3dcc179f6c589461eea5b50c30ede6709d1d530460996ceb7530ff5350517cfdeff00a1eba7c47f142c7c5a9a44767e1f8b4f962771776baebdd386c64c7b5a389703905f23a7ddc9204bb58e3e7a89e87a5e89a4fc46d474b8ae058cd878c9c9b44ea3f1f5efd2b3e5bbb8a55311256ba342c7c13f10676f32e6d6e1473c2c6838fe95a45a8e8616ab7d4d0d3aefc49e12b896ca68aede78945cbd9a95dd2c2a1f9c0c6e04a818cf5e322ad357343c73e337c33f0c7c53f1dc9e2cf13d9cf1de3cd14d298e4dad2ed8d5503fa8da00c770003d28d6e0db7b94d3e18f81edb3e4f86ed41c6398c1a62e58f62c5afc3ff08c127eef42b61b8f3ba153fcc503347c636365e1ef87f6f6da35ba5b44d3b86481428e41cf03d71409dfa1f9f9f197c5df143c47fb5d695f08be1f78a2e34cd0751d967ab47a180b3acab34373bdda13e6c6bb1190b1c0c4a3b126b0bce53b1697eedb3f4f228c0d0a527b4478fc2b657b19c7e145cd1803a3c0e3182a47ea699459a04dd90e1b48000a08738b5a8e4741de82d5ada031e0e28190cae5137014015a793cd1f747e5d6ab9795dd8156601f0bb483daae4ed10229a34880233cf5ac9bbad408f00f383401c269ca1c0c8c64d26930352d5c0f954f1daa93b01a7a7dce303392319f7ad934d5c0d9b3d43ca7ca0e18e714a337768c2517166cd86a8ac01463c76ad14ecf708eb2362d7599827cadf9d573263969222bbd6a4950867c7b134735ba8a3f118daa5ef98b8cfd49ace757536b2b9cc6b771e66429c8ce2b9b5d462780547fc2516ec075940357076b5c0f2bfda6f4882f7e35ead2c8b9252db048ff00a778c7f4a257b984be26735f097e2d787b50f893aa7c1ab38654d5342b18aeee0965d8d1ca415200391d7b8ed52552f84fa87e177885ad2dd1cc9c8039cd1adcd4f29fdb67e18eadf17f55f096bba3c4d2cde1fd627b908afb77092dde1e5baa81bf2700e718ef5862255211bc1753d4cb6b51852ab4aabb29ab5fb1ccf812fbe1bdf7ed137ff0c63f88fa4c9e23bab459eeb4e7be46bbb580b164fdc97dc41dc76fdd1c7031d3484af157dce5585af2a6eac22f9175e87d39e1df0658fc28ba9b4eb6d6daedb50b662931b7f2f61017b6e39ea6b1ab2719a30a536ddcf3afda2bc7ff0016fc3df18be11781fe1e78ea0d1b4ff167896ff4cd63cdd163bc3285b192e21dbbff00d5e0c0cb90470fed8a539b842ece9a11552a5a450bff001bfc5bd5fe306bdf0abfe17469da2be8fa56f8750b9b0b78d669498b960e0818599381d5b1db8ae7ab5b11ecd3a51bb3d3c3e1684eba8b8392eb6dec7e757fc14eff00e0a4ff00b6d7c0ef86df04b57f85ff001ea6b197c4fa06b6fae5ed85942175096d753682397ee9c662dbc2e073918e2bb695e504e4b53c6c44796b492565d0fb27f643f8bbe2bf89ff00b3e7c18f89de3af1addea1aef887c11e17935abfb99417ba997519096755c60cafb816030718c71c68f43077b1eabe32b7d3adbc4d71169ba9c977186f9a691c310ffc4b90070ad9503a8000e7ad674e5295ee6b560e9c926ada26644d2aa1cb9fd2b53328dc5fbc631bf1ef409c9220b3b7b0d4f55825d4eca1b85595462740c319f7a05cf1bd8f93fe1b7c46f0a4dfb7c78e3549f55b786daf2259a37893cd5916e2e6dad6d13280ec223b0690a9e9f6800f35946dcecd1e913ef2d26ec5e786fcfdd9592d3703f54cd6a4a562e786a6dda1c658e70cc33f8d005a1739e8b40a4ae8724c18ed231418ba7263c7068358a696a2925ba1fc282886e588000ab8f2f5021552c339a52bdfc808e5d8bf313cf6acddada815e7195c9f5aa0202f838c5170385b08c80501c90680346d0152031c7ad005f86342bba3ebd339a2c068d94ecac370e8280ea684374bc3edc7ae051602f4176f180db8e3daa94a4b6225156b8cb9be62a420edce6936dee28c35bdcccbcbc1b3e5c9f5cd23430f569f76540efeb40163e1d328f115b91c9130efef42d5d80e23f686b2f3fe2e5f3942774301ce7fe9928fe94dab325c63bb397f04783ed34af19df78a832fda2f218e12a621908a179ddf81e3d8545a4d873c568773e3ef8bd63f03fe137893e2c6a566f776fe1bd06ef537b48a408f38821797cb5246016dbb413dcd5147c97e0bff0082e87c2af8b7e1cf146b5a5fc3c9a39743d0defed2c23d5cc92dd32aa6e570d0208a3df3dba798378cc84e0050581349ad4f3cff0082707c7af07fed6dff00050fd2bf69f1a49b0bef10e9175a54fa35cc71cf0595dd8a6f458e76c334af0132711a6d08c32735cf57da4649a3e93019cc68647572f70bf36cfb1fa7bfb5cfc64b3f80df0335ef8e979a18d4e4f0be96f3c1a7fdb7ecff0068ce10aefdad8eb9fba7a5454b4e68f066fd9d36d743e73fd8e7f6fbf05ffc1433c4bf0c3e2ae81f0beff4c8f40f89979a6323ea7e6159db40bd91988f2941003a73efc63be38a8fb386a8c329c64b1aa538ab59d8f4cf8dbf0bf57f137ed41aa462fe1d32dffb3eeae45e5ddeca81b29a3a18f728383bb69518e7d7a01c553154e850529b693d15bb9f7bc3d9bd1c971b2af521cc9c6c7cf1f1bbf61df0e5e7c36f00e8fe29f08c1e2dd6bc056bae5969034bd126d461325c5e1ba61e580a2372146243929b5b6e0ba357a14a75e5bbd3f1f99f2d8ec561eb632757e16ddcb9fb3d4fe36b3f17de69f7fe3a93c4eb65e29d324323c8d29d3eccea1198edc484e0f94243b8292013b8603855eb8735b5386728cf547d1577736d757b713da1253ed730e518722461fc4493eb9f53eb9a54e2e2ddcdf198b9632719356b4547eeebf3336f2fe305879a32a70475c1eb5a1c6da5b9937378092646e9d062830388fda27e37683fb3f7c08f14fc66d7e778edf41d2649c18cfcde69c2461791962eca00ce49e0726a26da5a0e11737a1f35ff00c1313e35fc0af8c3e1af197c50f0b7852ea2d3acfc41a5e936b7f7f639486cb4fb6d3605964971b559dfcd99b273990e73d4c53dee7434d1f6afc1dfda87e02fc55b8bff00067c34f89fa56b73e8f0f93a84da5ce26b582504a7d9cdc2e62f3f8c9843170a431501949d82ccdafd9c3c43e31d7be16aea5e398047732eaf7cd6ac244632da1b8736f2610614345b180ea148cf39a0476f1dc2b31f9f8fa50056d33c4d6777ad5de871871359842fb860306190450653938b3650b30c95c7b6682e0db42a480e4a1e8714140c030c30cd0047384540a57e9ed4014e72a70a680209d8b9da0741d7340117d9ddb903ad1cf25d00e1f4d4da4e46de7806802ea60f05b1e940172cc91c83c6791ef4016d776f071c77a3602dc127f7495c5005a37442e739e3d28135721927942966e845016b2b228ddccb9c7b64fd28057ea655f5c7cb8ce703038a065ff87c367882dd994f32aff3a17c680e73e3ad997f89b713ff007ade2e7fe038a72f899955bb8d9189a55ab472ab018e6919eb747c97ff000570ff008285f84bf665f86badfc0bd43e1eeb3a96a1e29f0acd0a5fc4d1c76b0adc2cb0fde66dceebb433285c01247f37cd809b48de51e75a1f8e7f033e3e5dfc0af883a378d2e3438b54b11a7c916a1a5de27eeee6ce783ecf20239c1d9e5caa4676bc284722a14f5d4b8c6db9eb1e13fda3756fd9d3568fe347ecdde28d42c2ed3c471de6a9a3df58443ecf2c4626dc9202c4097cc28fb76b127a9c305a9ae6883d19fbc310b4ff008294fec4ba5ea5e19f1b2697178d7c2d12dc6a73da24ef048554966872a33919d871c362b95dd4872f7e367d483fe09f3ff04bdf879fb0f7c3e8fc02ff0012b51f154b178b5f5fb0d45a23612da5cbda25a305f26424af94a4609fe36f6a726aa34a44e06852cbe2e34dfc5a9f45fc57f0dc5e21f0fcd1786748b37d51e09e01777da9dc2b1599111893970e47951300e08cc631b7ad44f0ea51d11db52bb70699f38fc78f83bfb4278dfc11e0ff000c7847e1ee936da8f86b5a4d52e7503ac2344d2c51cad08889f9e40b33464a48aab8c91e95e750c366786c0f273dea5f7b2d9bdbe48753fb3ea63139abc2df8ffc39e75f09be0f7c66f87df147c48fe3df0de8fa33f89bec3ae9b1b0d53cd513417961f692cca000cd2a2b7030410b815ed47da24933cebc55ecf4e83ff6d0fda8fc31f057c01abe9be29f17df683e23f10ea373a7f84d7c3b606faeee6f39f244706019093b3728c1c301b81208d4aa72bbd55cfcc5f0bffc161bf6e3f821f1465f137ed21e38b2f19ddc16924575e1bd4f49b4b41f676997256e2c23022995d0ae2447003118391b43a950a1563f159f9ec7bdf85ffe0e20fd97f5ed46c74df14fc2bf19e86d713247777623b5bab7b5c9c172c92891917a92b196c0e14f4a894edb1c6a9f2ee788ff00c14e7e347c36f1878d74bf16eb7f14bc47e3ed3351d2a64d3fc33a5f8896df49370048d0c8f690012c90ef68d588943903224c8d829fb374aef72e11e5f84f953c15fb526a1e1af87be39f825e34f08ea56de0ff0012e937d27877c31a65e496f6d61aeccf6ab15f4a9bc195238adca046ca82dbb692589c5368d5a56d4f4afd8eff00e0a0f73fb017c0cf147853e10783f4fd5fc6de2fbd2bae788f5bbb32c3656d1a4890a5bc28c0b96dccc5d9872402846daa8cf5d4cddcfae7fe08bbfb5efc7ff1c78175bbcf1978d279751d53c489136bbaaed92e3538a381228e169a60cc5210d84542aaa4b704b3675dc996913f56fc33e26d361f0b5aea11ea8f7ab32076b892424bb9e5ba818e73c6063a504a92b6a52d13e2a7852efc4338d39d2eefe5458e38ec17cc32019e0b0e060eece6814d4648f41867de164c1e70704608a0a826a3a92c7282e5ba668289524562573c8a0064cc8e3af4fd6802a5c2ef4da28028c8fe5bedee0d1ba01ff6dc7551401c4d92312493c0e940169412c303bd005ab66604e4719cd005eb7757f9436081ce6802649907dd53f8d003bcf180a0139cf028339c9a7a0d96729111337d050545dd19f71719521f1c9ea0d051465c12771ebd050068f821fecdafdbcd37eee3f3802cfc01f8d0be3426d2317e39ea3a4da78f1ee352d4e1426d2364446dcc4007b0ce3ea7029cbe2667369ec79878b3e22ead633341e19091030aba4b3229663960570720741cf348cdab9f9a5ff05bad326f1a782a0f88175a84d717b6573059bf9cdbcf90ead2b01e877c310fa66a269b4694b467e60dade5dc76abb66e21731cd1b370d16e5383edbb9ac6eef63a7a1ea9f0825bed73c463c197f692dfc1aab2e9b33a44669151b2219571cee0a48cae4ec7931935bdda8e844b4dcfdedff82467ecb9f17bf64cfd93f4df04fc4bf145cdd5e6a33b6a36ba70b5318d36190022175639593f8981c15276ff000d64e0e4cce724e3647d73a43de8b85692f08d8738dfcb67a7e35a4695d6c651694b7356f9af406ccedb4e48dcbc9f7e7b1e6af925135e68b7b983757e6160a6f186783938e3f0a0cdb6ddcc2f150d2b54d3043ac6b36b6d8202dc48172ac086182ddbe5191e9408fcd5ff00829178a2f3e1afc52d0f55f85fa969d6df12fe216bf3e8565e38d4952e17c2fa4c312cf766d7780aac5660dbcfcce1c8dc02c6102a126aa289f9f5e22f8b53f8467d7eedbc49ad6afe20d33c5c6ded6f3c537d71fda37b0300f7ccdbe236cd0c8eb978ee164dbba30564dc4d26d23a1c9b39bb8f0a7c1ffda4675d37c13e31d03c13e262599345f155a1b2fed072170b1dcac9f606504360886d4b1c058dc9e3169a13bb3ccbe237c1cf8cdfb3f78ca3d2fc79e0e9749d4a3225b5662103af3865c10391c804671ce314df2f41df9762bfc3bd5fc19ac78aa5d27e27f88bfb19f50f94f89af6c9f518ec08e5775ba104ab36d0ce048cab92b1b1e0c94df32d4c5f16785cf877c5124d2eb96ba969f35f4d1a6b3a25bb8b4bb2ac0b1837a47c0dca769552a18640c8152b71687bff00ecbbfb50fc56f81be18d3f4fd6ad2d6f3c3b71a881627ed70fda2d6e188015f9de9b82655642990010c40ae88fc2633bbd8fd57d33f6a9f1b78bfe0ae9fe13f0d783ee25babc12a4fa9594eab141090927cc4f05b0c467774cf5c8aa32b3b5ceb3fe09fdfb47fc389ef2efc3bafebad06a13b95b09ae61c0c0ce46ee40249f5a0716af73ed2b2d7209acd1a2b8573d032b023ebc506d7458b6b892e64da6524e7d6819a284ae393400aed8c8340104b32a9eb9f4c50050b964694953cfa554127a0116e51d587e75a72440e4616651f2e47af35972bbdc0bf0a175043741d295a4b702cdb2fcf8cf1dc13401691304b37e181400f50ccb8047d33cd0032f6e56cece5be955b6c319760bc3103ae3af6f6a0c9a4e7629f87afee3c456126ab73a78b0b78a5f2d5ee246556e07cc5a43804e718e3e940d3e5762b6ade23f0be9e1b75fbdd4a0709691e57e859b03f11ba81caa453b1cb7887c7fae7921741d2a2840701c1c3c8cbdc02c36e7b8c01d31919a09e7637c0fa9dddf788ad6e6f6f2599bed716d32b924619f230791f4a4be3429494867ed13e541f118cb2751a646cb9eb8dcc3f9e3f3ad2a7c449e51e31d7b4cb69a28a59504f25be224ee7e62011eb839e2a00f0ff8c9fb08a7ed51a5de691f12f5a3a7595c5c8b8b24b390bc91caa08579338520673b0707a127a5034da3a8f813ff00049afd93be14698b6363f0c2cf5795a25136a7ae47e7cb2b73f31c80a38ec147e352e2989b6fa9f467c20fd993e037c29d420bdf047c23f0ee9f7292798b716ba4431b873cee042e77673ce735495915ccdab1f427d99acbc3825966cb4ee42e46768c6477e38a09b332609596ed238a40403c96cf271f5ade82bbb1c389abeceec9afb559a389e60c36e3072c46d03db9ed8ae9ad439617473d1c4b94f4395f106a6c27215582e4952d9ce3ea6bcf6ac7a8a49a30355b9b561b2fe1596376fb92c7b9723d8d055d1f2f7fc14c3f675d0ff68af879a5ea1e07f12c7e1cf1cf83f504d4fc27aac70b04328fbd6f32a027ca90000900e0aaf0465480b49731f95f75e049be237c62d4741f8bf69a8787751b492f6ebc5fa9f87b4d7bdbb8dc87fb3dbc3129f30c4db230d28766084804062ad0bdedce84d3391f889fb2c5bf863408f5a163a0ebde1cb863359f8b7c31732cf240a17f7b0dc27cecac83e624a1d9ce777f0935745733383d5be377c4af85fa6e93f0fb41f89775e26f0cc76cf2c7a0789a3fb569ad1b3953e54727fab276303b36b295386c92064d34164cb1ad69ff0001be217872c751b7f04cde1dd62f732dc3c178e6d846ad2a93971b13e68c0e4f208c723e6b50525a8dbb232be28fc3bf85fe1dd125bbf0eeadaa2a45a6dbdc696d3ea71982ee666db39890264a9013a312361c96c80aa71518e84ad598be3ff8a1f11be39d9e9f178aadeda69b4cb6482da6b5b148a5b972c163de547cc70c3d320e7af3429b5113f724923e92fd8c7e357c68fd9a756b3b91e3c7d4bc337f6fe6dee817501752fb3f77242c4831bf72a305c06408edc0d62eeae2714e363d87e07fc518e3d7d3c516f7ccc0cab2208db821b92463b60d33922e49dac7ea77c31f1941e24f06e85adc372185cd9c6f6d30739e402549eb91861cf5033d7341a1dee87e2ed76ca38e68ef9dca70cb361b711c1ebcd02bca2b43a8f0c7c4e6d516782f6c156e2d98095226232a7eeb007b1e7bf50476a0da32bee6d278a74ab9b25bd69fca59002be6f19cf4f5a0b1eb3dbde279b6d70b22f728e081f9502ba655997cb3939ebd477ad2124dd8635523c75cfd6a1bd77039c48942f4040f6adc0b16a514f5c544f602e451876051473deb202e2e9b7223f3dcac7103869a57091afd59b007e7405d1425f15784ecd8345ab35e3f65b18f2bf42ed81f8aeea0ce55231336f3e20deea10edd274cb7b48d946d675f36420f625be5e9e8a2839e73e67a185aac336b7bceb1792dc071b1bcc94e403e873f2fe1413cccce36f77a6f9705dc82e2361e58b8618704676ee0060e7a64639edcd017bcae56b86561ba36efcfb506ae5141e1b95ad7c4d6f347cb35cc781bb196f9b3f8e31f95017525a1adf1f7c2f7de34f1886d1e488225946bf6cdc0843bd891c7278c647a7522aa4ee28c5c7732fc3df07f4e8e44bdbd8e19a644d9f68923f9f079207f7467b0a928eb6d3e1dc1e5a864411a9c85f2f39fd6802ea786ef17e409c29c2ece4fd4e7a7eb40badcd4d27459daea366943286c7ca41c7d6819e8b6f72abe1b901119da4f96d8e4f18cf5e2835fb071c1d9dc8f342b64918e38f535d18771e6d4f1b191e662dfec9e2d824c023064239624fbf5eb5e9d7941c0e1a109466731abd8c52cc425f3364fcb80320d78b2f899edd257460f8874e74b2612ddca02e368da07e1d291a38dddd9c178c74db1bc845bdde64c1e097e17e9c7f5a0b3f3c7fe0a77f097c63f0cfe21e9bfb48fc30bd934ab2b9d3d74ff0014ea56b6a66166c923186e6641f33c45649227208dbfbb6e718a2e8d29bdcf9474cf0dfc50f06ea53fc47f057c5fd2e2bab95067d6345be636f7c8015db7314d98e488ae01f306300e4f2682a5268e63c73fb48c5f12f55d4355f8a9f03b42d46ee7b95fb5f887c277b75a4b5d4aaa1252a159e17dcc0b349e5e59c93dcd0294e3157679bd9f8ab5dd4d17428bc4c9676b6f3318c5d40cc22cfcc558c609766cf2768058672096a951b3b949a6ae54f134faceade0e97c23a469b24b6a7536bc8a492dd227576e18040ce1508c1e1b39c939c8014d5e2175177295ce91ace94e2d3c3b15db44d3c4f02b46b980a3161c9c96c1271f5e695ad4cce53bcaefa1d8697e23f1c69dadcc9731cafa36a16f2a470e4b0b7241668495394747c105bef055230194d547e134524d5cf59f835e34b84d511015549a09666572a84c911413151900e43c6fb7a932363b5519ced6b9fa89fb027c555f1e7c2c1e1679e3fb5e8728318273ba2625e37fa02594fd3de8328cae8fa63c3da8c77b6a571b5848e4293db71fe5d2828bf1452457497b6b288e75046f3d1d0ff037a8f4f43cfa8201ab1de2ddf86d608e4c496aeb1cc80fdc6183f963041f4228376949588e28e681bcd8e528c3f894e0d028c79466a7e35d5bc3f24571797c5ace460934b30dc226270ac4f50a4f04e7838f7a0a3463f165e6c1badd73df648d8febfce8b2ec05bb7b397cf5b39268a367194134aa991ebf31ade4ecae1d6c49aa6a1a37859d6df558aee59ca6e11436ccaa7fe072000fd5430a8f6829b5057663cff11b5b9a792d347d2ad6ca311a95948f364e4b6725be5ec3a28acdeac8f68665e5d6afaa5c8b9d4b549ee64c7de9e42f8f6e7a7e1410ddd94a0d12482e9b52d26f8c4266dd3dacabba266e72c3a1527d8e0f523d4339fc25ad3e5961825b3bd8d565b72b8d8491226461871eb918ec450645859a1650e922b29e84720d0050d4664789f73f6e5476f4340d2bb2c68be14d5f5f9fed08b2416edcf99247c371d54753dbb81417ecceaf44f875a4696bba0b2df31e0c92b7cc7f1f4f61c50525ca74565e16d3603e75c47bdcf3827007e1e9414dc9f5346d3c3fa7464ce96a8083c7ca2802f7f67e9ac02ca5003c0000193ed4018d0eb1e117f16c9e0d8eec1d4441e70b492171fbbce32188dac783900938e7a5005c3696b67261215450d80a16802dea3ac472e9e6c212a0429d02743ec7bf1fce82b9bddb1c9deddc714be7ac7f2e3e5f9075f6fceae13e430ab0538906bbaec7369b1c7167709530073d181ab9d6e65b9952c35e5cc65c972b7059f600d20e9e958ea762a2909259c6b66c5a10d9c6738fcfde814e36d0e6f5eb2d0a58d84f2e260bb5c28c7d0e78cf6acaa34b464ebd4f29f1a7853c3dae4775a7ea96f6cc25465c4bb48950f50430c1e3d6b019f26fc4cff8259fec75af6a373af45f0f20b59a66f30db586a73c0b9c9276c6920400fa01f856919f296e7732ff00e1debf0e134a5d234dd1b5236d0a054b69f50ba08883a2ed67c63db8ad9c925722e721aeff00c138fe13c973bee3c1cb1311cb248c0e476cab66872b2b81cfcdff0004e9f8772b490d85fcf6cd8ca85b824aff00df439a8752c26ae8e724ff008271789a0ba9648b7cf6a39867b7b80ce7eaa62007e04d68a49ab84973a38af885fb0efc5ad1236bff0087da64f757455567b4bc4022b803a6e3b810c3270c307048ce38a6447dd7ca52f86bfb247ed0da9eaf1cfe36f87d16936d6504ab651c1a8a48e6494c664919949cf1146a071803d72485b57563eaff00d8fa0f1e7ecd1e299f50f105835fd8de5bf93224636cd10dc0e41202b0e3eef1f5a0854d23ee0f85df127c27e38d2e193c39ab46f788ced2d8cade5cca0bb13953c918ee3233c668343bfb4b8fb426e1cff0b7a83400f92f0d8a4d296f9678f6480f6201dadfd3f11e941a53ea6b45324a9b8f07f887a1a0d086eed21bb061ba459219236492275cab838e08efc668031ffe119d72dff71a578be682dd7886192d524283d3730c903b67b5007f3fdf07bfe0acdfb57fc19f1a9f883a078da4bcd519f7bdd6a4c67790e73f333925bf1ad5d5bf421d197467acfc40ff008381bf6f1fda135cd134df1f78934b8ec74dba597c9b2b3f237903196742187af0474ac9b4b645c68d6a9251dcf6bf06ff00c1623c55f083c19a76b3f13b40bdd4adfc41a9a3e91e24d3209e59248e09825e59ddbdc37cdb432489e56e40264046f46029c6cae3af87546af2a67ea2687f10fe0dfc5cd12c7e25fc02f149d67c25abdaacfa46a0776e913eeb060caacac18302aca181041008a939dab32da2a8428bc6de71eb9a0ce7f0952f241bd2507055863df2471fe7d28322ce83e1ad6bc4976dfd99b961561e6cac008d08c0e588393d381cd034aecedfc39f0cf48b3996f6f7177385c19245c20e7b274fc4e4fa628368ab23aab5d2102e378dbdf8c7141718f322e45a55946988e350077e681fb24f72dc3690468080a3dc8a0d1452d8f9bbfe0a2dff000522f835fb00fc3f5d5fc5b7a9a86bda81f2b4cd1ad9d1e40e55b63326e04824600e01e49200268195fc0dfb4278d3c01f02b47d73c4ba9d95ff008c3c53609a8ea7796f70b731d9a4e03adb432af05115802c98467dcc8a8855143294ae7ceff1a3f682f11f87bc67a2f8aa1f12dd596a5e6cc609ad3cb2ec46c18c302cdc337dd07ef7d2820fa4ff00668fdbd3e0f7c6cf887a8fece9e20f11dbda7c47d1ed8dc9d2258a50750b30a98ba8df6f964ee660d186de3617dbb4f016a9c773ddef2e6da1b03026180e093fc541325ef183a9c9e7c65120257070a4f7a05ab30e79a08ee56db23713cf6e306836826a3a90ce96c9279800c9c85a0a367c2fa55a5fa1fb496dcd9da10e0918e9401cef8e7c3169a56a2582178a78f82e47c8c33fcc7afa5635bb9954dcf3df127876d6590cd058c0ce3fbebfd6b120e52e3c3f15c3baa42995fbc8f9193ec7d3eb401565b1861db6f3db297c9f9891c81dbde80286a7e16b2963f3a0b289096dc7f760d35b818d75e1fb0bb530dc5aa46e17e5930062a9c95c0ad2f8761b68fca89b208c364f1fa62a799dc0cbb8f0d432ce44b6f819197dd4dcae0175e1a486068fec1b908c17440703ea2a40c5bff035bb0f3eca533a28c984ae08a69d9dc0e6af2c62b0b813db4af6d2c4f9431b95653ea3b835d319732b81da7843f6a7f1e782b16be21c6b764576913b6cb803b112804b11d7e7049c6322a80f57f017ed15e04f8891bd85b6ae2daf5a06c585fe22998ed3c28c90e7fdd27de8368c794f515d496c2e0492b2ac40112331c051d727b76eb419c65cacd069372f9d1bee0572b41b09e6c679df9fc2803f94233283803341a1369d74d6f729722528cadc15a01369dd3b1f4efec39fb2efc54fdba3c53a3782b54b8bf93c1be1bb868522594a89a596569deda26c6232d9792490862883383845219ce4ef767eedfc08f841e08fd9bfe0f681f03fc0d631d9e9be1fb2105bc10b48fbdc9324b20dcccf8695ddb9271bbaf4a0c1bbb3afb48ef358992cb4ab7b879e404c4b1c6770f5273c01ea4f1408ecbc3bf0b23302cfe2b9c4b2800fd9a062101eb966ea7e838ed96140ac8eb2d748804696d676a151000b1a0da883d8741f85027a7435adb4b4b7506e26cedf4a0b51728dc960b742e4e0edc742682528a64c91a33ed44279ea4d06bed15ec789fedf3fb6f7803f628f8609aadfdcda6a3e2fd68b5bf843c2df69513ea33f19609b812899058f0071c8cd06963f9e2fda5fc71f1bbc45f14752f8dffb59497f73e2fd45a4bad3341d6ada5921d3d260cc1f6ab011609508ad8384cec0823dc0a525147dddff000467f1b7897e31fec6725df8935f9f50d4340f13dd69625bd9da4716e163b9404b1271bee65c7e5d860339bd6c773f1a34bd5358f1ee99e11bfb1be0ff00695113da99563dce7a36df958602fdee073d39a083d2ff00e09b7a46953fede9f156cf57f0fdac9731786f49b9b59e68d59e19608c2191188251b6dfce84ae090ec0f53416e578d8fbd6e20791198b0209c8001181f9d04187ad89cca796d83ee9071c83eb9a0da1f0987a8c2004f294960725c9273ebfce8287430dbe51a68cb63fbcc7f3a00dbd0e774631dbca5011f2b46318f5e33cf1ef40199f1134a696cfce4be73f29284e43671edd3d2a671e646550f32f10da5ddb4665596670c0962187191ee7e95ccd497420e79acefd652269028f42a41c7e3480c7d6f4895e53225ecfd7f81dbe53ea39e2801227b9de4c974ee47dec9e9efe9f95003350b7b7990a9b38db23970a339a00c8974a757e6360982011dbeb40111d31a38038951bd063340103aa01f2314ddd093c1a00638785364f671b6390fb4134019da9785f41d757cf9aca3691539ca0cfe74d6e0731a9780e18a5221b384281956f2c9fe95d60636a5e0685f3e65b43c8e02c3405d9d47837e3d7c53f00c89697ba836af60bff002eba8b13228f4497ef2f61cee03b0a00f67f86bfb44781bc572476c7523a5dcb8c3e9fa832a8763d0c727dd6fa704fa505464a3b9e9682e6451240ff00237230a0d06cb547f28b41a1eb3fb12fecf1a37ed45fb44e8bf08bc45e21b8d36c6f0bcb753dac21e42883251724052471bb9c7a1a89dccea49c15d1fd007ec89fb3a7c33f817e1f3a47c37d2dac2c344d2e3b1b1b18f688c798c1e5998e373cae426e6627ee718dcd9a5b1137785cf68f0ae87178af5892cae6e1e18e28c48fe5804b0271804f03eb834cc56c7a0e9f63a7e870258e956490c6ec37edfbcc7d598f2c7eb403f891a110524efc918e99a0669472a243e6ac781b73b41a04e6d2b0b133cceccce7e94029368996e4a8c6cf6eb40c66a3ab7f62e8f77ab083ccfb25a4936c2f8ddb54b6338e338eb40753f323f66ff881e29f895f057e30ff00c144fe275dc3adfc40d47c5771a0e8971716cbe5f87f4989374563661b77951825cbe3994953216dbf3690b1bb763f36bf68db31e319f50f166b37534b71785e799a4937bb393c9663d7bf6acc8a8735fb0cfedfdf10bf613f195fe8de1ef0d5aebfe1af11ce8752d0aeee0c05265f95668a50ade5b60e0e55830ed90081e85349a3f41e6fdabae3e227c70f04c717856e74e8b5482d647b68f538658c179987cdbadb7376e8cbd38c75a09946c8f55ff82556b9278cff00e0a25f193c4f2c5f67fb3787c69e2d91810e2396cc0909c039e3a7607f1a0cfa1fa0d77752f3183804f4fce812d8c5d5ee9a00f3919d84f1d33f5a0de1f09cf26a1712dfec2c002a4103ea28143588dbebc7b7bbf2e35ea7192682cdbd36e59236802f24060e3a838a0ce536a4914353d4279a592297e641caa939c0e38fd682da5638cd6364b29dca70496001e063903fa506061789ac5efa3568aede129839400eec67839ac254d313d0c6b591b73838c67007a0a5caa4329dfc0964a5ed94292771c0c64d43566052943bd9b4e8e5199782a071f9d11dc08ed2f1a72e9246a762f523ad3fb40472c712c059625186c6070289015b54b581e056d983cfdde2a40e6b4ed7a7d44b33ab01f300a5c1c631edcd0052b8b8d5f4fd4d49d4c4b04bbb31490282bf465c7ea0d6914265a8f5692e9841242b81c75ae8190ea8f1d9442ed62dc73ca93c74a00a3ac69961728b2496cbbbd4500729a9e9c2c2ed6359772caa4ed2bd3dbde803634df18f8db4fb18ecb4df1b6b16d046b88e0b7d4e64441e8143003f0a02ecffd9, 'Phòng Ngủ', '2025-05-22 19:55:00');

-- --------------------------------------------------------

--
-- Table structure for table `propertyvideos`
--

DROP TABLE IF EXISTS `propertyvideos`;
CREATE TABLE IF NOT EXISTS `propertyvideos` (
  `VideoID` int NOT NULL AUTO_INCREMENT,
  `PropertyID` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci NOT NULL,
  `VideoPath` blob NOT NULL,
  `Description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci,
  `UploadedDate` datetime NOT NULL,
  PRIMARY KEY (`VideoID`),
  KEY `FK_Property_VideoProperty` (`PropertyID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_vietnamese_ci;

-- --------------------------------------------------------

--
-- Table structure for table `revenuereported`
--

DROP TABLE IF EXISTS `revenuereported`;
CREATE TABLE IF NOT EXISTS `revenuereported` (
  `ReportID` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci NOT NULL,
  `Year` year NOT NULL,
  `Month` int NOT NULL,
  `AgentID` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci NOT NULL,
  `TotalTransactions` double NOT NULL,
  `TotalSaleValue` double NOT NULL,
  `TotalRentalValue` double NOT NULL,
  `TotalRentCommission` double NOT NULL,
  `TotalSaleCommission` double DEFAULT NULL,
  PRIMARY KEY (`ReportID`),
  KEY `FK_RevenueReport_User` (`AgentID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_vietnamese_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transactionlog`
--

DROP TABLE IF EXISTS `transactionlog`;
CREATE TABLE IF NOT EXISTS `transactionlog` (
  `LogID` int NOT NULL AUTO_INCREMENT,
  `TransactionID` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci NOT NULL,
  `LogTimestamp` datetime NOT NULL,
  `Action` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci NOT NULL,
  `Status` enum('Start','End','','') CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci NOT NULL,
  PRIMARY KEY (`LogID`),
  KEY `FK_Transaction_Logs` (`TransactionID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_vietnamese_ci;

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
  `TransactionDate` datetime DEFAULT NULL,
  `TransactionType` enum('Rent','Sale') CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci NOT NULL,
  `TranStatus` enum('Pending','Paid','Cancelled','') CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci NOT NULL DEFAULT 'Pending',
  PRIMARY KEY (`TransactionID`),
  KEY `FK_Transaction_Property` (`PropertyID`),
  KEY `FK_AgentID_Transactions` (`AgentID`),
  KEY `FK_CusID_Transactions` (`CusID`),
  KEY `FK_OwnerID_Transactions` (`OwnerID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_vietnamese_ci;

--
-- Triggers `transactions`
--
DROP TRIGGER IF EXISTS `trg_generate_transaction_id`;
DELIMITER $$
CREATE TRIGGER `trg_generate_transaction_id` BEFORE INSERT ON `transactions` FOR EACH ROW BEGIN
    DECLARE max_number INT DEFAULT 0;
    DECLARE new_number INT;

    -- Lấy số lớn nhất đang có từ phần số của TRANSACTION 
    SELECT 
        IFNULL(MAX(CAST(SUBSTRING(TransactionID, 5) AS UNSIGNED)), 0) 
    INTO max_number
    FROM transactions;

    -- Tăng lên 1
    SET new_number = max_number + 1;

    -- Gán lại PropertyID theo định dạng TRAN000001
    SET NEW.TransactionID = CONCAT('TRAN', LPAD(new_number, 6, '0'));
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `trg_set_transaction_type`;
DELIMITER $$
CREATE TRIGGER `trg_set_transaction_type` BEFORE INSERT ON `transactions` FOR EACH ROW BEGIN
    DECLARE v_typepro ENUM('Rent', 'Sale', 'Rent/Sale', '') DEFAULT '';

    -- Lấy TypePro từ bảng Properties dựa theo PropertyID
    SELECT TypePro INTO v_typepro
    FROM Properties
    WHERE PropertyID = NEW.PropertyID;

    -- Nếu là 'Rent' hoặc 'Sale' thì tự động gán TransactionType
    IF v_typepro IN ('Rent', 'Sale') THEN
        SET NEW.TransactionType = v_typepro;

    -- Nếu là 'Rent/Sale' thì kiểm tra người dùng nhập đúng Rent hoặc Sale
    ELSEIF v_typepro = 'Rent/Sale' THEN
        IF NEW.TransactionType NOT IN ('Rent', 'Sale') THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'TransactionType phải là Rent hoặc Sale đối với bất động sản Rent/Sale.';
        END IF;

    -- Nếu TypePro không hợp lệ thì báo lỗi
    ELSE
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Loại TypePro không hợp lệ để tạo giao dịch.';
    END IF;
    
    SET NEW.TranStatus = 'Pending';
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
  `Avatar` blob,
  PRIMARY KEY (`UserID`),
  UNIQUE KEY `Email` (`Email`),
  UNIQUE KEY `IdentityCard` (`IdentityCard`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_vietnamese_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`UserID`, `Name`, `Email`, `Birth`, `Sex`, `IdentityCard`, `Phone`, `Address`, `Ward`, `District`, `Province`, `Role`, `StatusUser`, `PasswordHash`, `Avatar`) VALUES
('UID00001', 'Nguyễn Huỳnh Thanh Phát', 'everyonebody440@gmail.com', '2003-08-19', 'Nam', '012345678', '0856254139', '31/16 Bùi Xuân Phái', 'Phường Tây Thạnh', 'Quận Tân Phú', 'Thành Phố Hồ Chí Minh', 'Admin', 'active', '123456', NULL),
('UID00002', 'Nguyễn Văn Lượng', 'luongvannguyen2012@gmail.com', '1992-05-19', 'Khác', '023156212351', '0856245139', '12/45/2 Tôn Đức Hoàng', 'Phường 2', 'Quận 3', 'Thành Phố Hồ Chí Minh', 'Admin', 'inactive', '121121', NULL),
('UID00003', 'Nguyễn Văn A', 'nguyenvana1203@gmail.com', '1990-03-12', 'Nam', '012345768', '012345678', '1 Lê Hồng Phong', 'Phường 8', 'Vũng Tàu', 'Tỉnh Bà Rịa-Vũng Tàu', 'Agent', 'inactive', '123456', NULL),
('UID00004', 'Kha Nguyễn Văn', 'nguyenvankha234@yahoo.com', '1992-05-12', 'Khác', '012346571', '0855123149', '12 Khiêm Phạm Tường', 'Phường 2', 'Quận 3', 'Thành Phố Hồ Chí Minh', 'Agent', 'active', '123456', NULL),
('UID00005', 'Nguyễn Như Quỳnh', 'nhuquynh1234@gmail.com', '2003-01-01', 'Nữ', '012345672', '0823145619', '1 Lữ Gia ', 'Phường Tây Thạnh', 'Quận Tân Phú', 'Thành Phố Hồ Chí Minh', 'Customer', 'active', '123456', NULL),
('UID00006', 'Kha Hoàng Vân', 'vanhoangkha112@gmail.com', '1992-08-12', 'Nam', '012156423', '072314535', '12 Hoàng Văn Thụ', 'Phường 10', 'Quận 5', 'Thủ Đô Hà Nội', 'Customer', 'active', '121516', NULL),
('UID00007', 'Nguyễn Hoàng Phát', 'nguyenphat241203@gmail.com', '2003-12-24', 'Nam', '066203010850', '0855542696', '65/20 Nguyen Do Cung', 'Phường Tây Thạnh', 'Quận Tân Phú', 'Thành Phố Hồ Chí Minh', 'Owner', 'active', '123456', NULL),
('UID00008', 'Jack NewTome', 'jacknguyen2105@gmail.com', '2003-01-15', 'Nam', '012345123', '085624131', '1 Tricker', 'Phường 2', 'Quận 12', 'Thành Phố Hồ Chí Minh', 'Owner', 'active', '123145', NULL),
('UID00009', 'Nguyễn Văn Toàn', 'toanvannguyen2012@yahoo.com', '1992-06-16', 'Khác', '023156212251', '0856245139', '45 Tôn Đức Hoàng', 'Phường 2', 'Quận 3', 'Thành Phố Hồ Chí Minh', 'Owner', 'active', '121121', NULL);

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
-- Constraints for table `contracts`
--
ALTER TABLE `contracts`
  ADD CONSTRAINT `FK_Contracts_Transactions` FOREIGN KEY (`TransactionID`) REFERENCES `transactions` (`TransactionID`) ON DELETE RESTRICT ON UPDATE RESTRICT;

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
-- Constraints for table `properties`
--
ALTER TABLE `properties`
  ADD CONSTRAINT `FK_AdminID_UserID` FOREIGN KEY (`ApprovedBy`) REFERENCES `profile_admin` (`UserID`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `FK_AgentID_UserID` FOREIGN KEY (`AgentID`) REFERENCES `profile_agent` (`UserID`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `FK_DanhMucBDS` FOREIGN KEY (`PropertyType`) REFERENCES `danhmuc_pro` (`Protype_ID`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `FK_OwnerID_UserID` FOREIGN KEY (`OwnerID`) REFERENCES `profile_owner` (`UserID`) ON DELETE RESTRICT ON UPDATE RESTRICT;

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
-- Constraints for table `revenuereported`
--
ALTER TABLE `revenuereported`
  ADD CONSTRAINT `FK_RevenueReport_User` FOREIGN KEY (`AgentID`) REFERENCES `user` (`UserID`) ON DELETE RESTRICT ON UPDATE RESTRICT;

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
