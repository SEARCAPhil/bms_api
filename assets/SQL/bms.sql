-- phpMyAdmin SQL Dump
-- version 4.2.11
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Oct 23, 2017 at 10:57 AM
-- Server version: 5.6.21-log
-- PHP Version: 7.1.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `bms`
--

-- --------------------------------------------------------

--
-- Table structure for table `account`
--

CREATE TABLE IF NOT EXISTS `account` (
`id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `uid` varchar(255) DEFAULT NULL COMMENT 'contains openID from azure AD through O365 API',
  `date_created` datetime DEFAULT CURRENT_TIMESTAMP,
  `date_modified` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_deleted` int(11) DEFAULT '0',
  `is_deactivated` int(11) DEFAULT '0'
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `account`
--

INSERT INTO `account` (`id`, `company_id`, `username`, `uid`, `date_created`, `date_modified`, `is_deleted`, `is_deactivated`) VALUES
(1, 1, 'jkga@searca.org', 'abc-test-token-1234', '2017-10-23 11:42:16', '2017-10-23 11:42:16', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `account_session`
--

CREATE TABLE IF NOT EXISTS `account_session` (
`id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `uuid` varchar(255) DEFAULT NULL COMMENT 'DEVICE token (for mobile)',
  `user_agent` varchar(255) DEFAULT NULL,
  `token` varchar(255) NOT NULL,
  `validity` datetime DEFAULT NULL,
  `date_created` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `company`
--

CREATE TABLE IF NOT EXISTS `company` (
`id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `tagline` varchar(255) DEFAULT NULL,
  `about` longtext,
  `established_month` int(11) DEFAULT NULL,
  `established_date` int(11) DEFAULT NULL,
  `established_year` int(11) DEFAULT NULL,
  `location` text,
  `industry` varchar(255) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `status` int(11) NOT NULL,
  `date_created` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `company`
--

INSERT INTO `company` (`id`, `name`, `tagline`, `about`, `established_month`, `established_date`, `established_year`, `location`, `industry`, `logo`, `status`, `date_created`) VALUES
(1, 'SEARCA', 'Southeast Asian Regional Center for Graduate Study and Research in Agriculture (SEARCA)', 'The Southeast Asian Regional Center for Graduate Study and Research in Agriculture (SEARCA) is a non-profit organization established by the Southeast Asian Ministers of Education Organization (SEAMEO) in 1966.\r\n\r\nFounded in 1965, SEAMEO is a chartered international organization whose purpose is to promote cooperation in education, science and culture in the Southeast Asian region. Its highest policymaking body is the SEAMEO Council, which comprises the Ministers of Education of the 11 SEAMEO Member Countries, namely: Brunei Darussalam, Cambodia, Indonesia, Lao PDR, Malaysia, Myanmar, the Philippines, Singapore, Thailand, Timor-Leste, and Vietnam.\r\n\r\nSEAMEO also has Associate Member Countries, namely: Australia, Canada, France, Germany, Netherlands, New Zealand, Spain, and the United Kingdom.\r\n\r\nThe Center derives its juridical personality from the SEAMEO Charter and possesses full capacity to contract; acquire, and dispose of, immovable and movable property; and institute legal proceedings. Moreover, SEARCA enjoys in the territory of each of its member states such privileges and immunities as are normally accorded United Nations institutions. Representatives of member states and officials of the Center shall similarly enjoy such privileges and immunities in the Philippines as are necessary for the exercise of their functions in connection with SEARCA and SEAMEO.', NULL, NULL, 1960, 'UPLB', 'Agriculture', 'http://www.searca.org/images/SEARCA-web-logo.png', 0, '2017-10-17 11:06:35'),
(2, 'SEARCA copy', 'Southeast Asian Regional Center for Graduate Study and Research in Agriculture (SEARCA)', 'The Southeast Asian Regional Center for Graduate Study and Research in Agriculture (SEARCA) is a non-profit organization established by the Southeast Asian Ministers of Education Organization (SEAMEO) in 1966.\r\n\r\nFounded in 1965, SEAMEO is a chartered international organization whose purpose is to promote cooperation in education, science and culture in the Southeast Asian region. Its highest policymaking body is the SEAMEO Council, which comprises the Ministers of Education of the 11 SEAMEO Member Countries, namely: Brunei Darussalam, Cambodia, Indonesia, Lao PDR, Malaysia, Myanmar, the Philippines, Singapore, Thailand, Timor-Leste, and Vietnam.\r\n\r\nSEAMEO also has Associate Member Countries, namely: Australia, Canada, France, Germany, Netherlands, New Zealand, Spain, and the United Kingdom.\r\n\r\nThe Center derives its juridical personality from the SEAMEO Charter and possesses full capacity to contract; acquire, and dispose of, immovable and movable property; and institute legal proceedings. Moreover, SEARCA enjoys in the territory of each of its member states such privileges and immunities as are normally accorded United Nations institutions. Representatives of member states and officials of the Center shall similarly enjoy such privileges and immunities in the Philippines as are necessary for the exercise of their functions in connection with SEARCA and SEAMEO.', NULL, NULL, 1960, 'UPLB', 'Agriculture', 'http://www.searca.org/images/SEARCA-web-logo.png', 0, '2017-10-17 11:06:35');

-- --------------------------------------------------------

--
-- Table structure for table `currency`
--

CREATE TABLE IF NOT EXISTS `currency` (
`id` int(11) NOT NULL,
  `country_code` varchar(255) DEFAULT NULL,
  `currency` varchar(45) DEFAULT NULL,
  `date_created` datetime DEFAULT CURRENT_TIMESTAMP,
  `date_modified` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `industry`
--

CREATE TABLE IF NOT EXISTS `industry` (
`id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL COMMENT 'ex. Telecommunication\nConstruction\nConsultation Firm\netc...',
  `date_created` datetime DEFAULT CURRENT_TIMESTAMP,
  `date_modified` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `logs`
--

CREATE TABLE IF NOT EXISTS `logs` (
`id` int(11) NOT NULL,
  `account_id` int(11) DEFAULT NULL,
  `message` text NOT NULL,
  `date_created` datetime DEFAULT CURRENT_TIMESTAMP,
  `event` varchar(45) DEFAULT NULL COMMENT 'executed events includes CRUD for:\n\naccount\ncompany\nproducts\n\n'
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `logs`
--

INSERT INTO `logs` (`id`, `account_id`, `message`, `date_created`, `event`) VALUES
(1, 1, 'events.js:160\r\nthrow er; // Unhandled ''error'' event\r\n^\r\n\r\nError: C:/xampp/htdocs/popup-es/dist/src/js/popup-es.min.js: original.line and original.column are not numbers -- you probably meant to omit the original mapping entirely and only map the generated position. If so, pass null for the original mapping instead of an object with empty or null values.\r\nat SourceMapGenerator_validateMapping [as _validateMapping] (C:\\xampp\\htdocs\\popup-es\\node_modules\\source-map\\lib\\source-map-generator.js:276:15)', '2017-10-23 11:45:22', 'warnings');

-- --------------------------------------------------------

--
-- Table structure for table `price`
--

CREATE TABLE IF NOT EXISTS `price` (
`id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `amount` float DEFAULT NULL,
  `currency` varchar(45) DEFAULT NULL,
  `date_created` datetime DEFAULT CURRENT_TIMESTAMP,
  `date_modified` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `price`
--

INSERT INTO `price` (`id`, `product_id`, `amount`, `currency`, `date_created`, `date_modified`) VALUES
(1, 1, 25000, 'PHP', '2017-10-23 00:00:00', '2017-10-23 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `privilege`
--

CREATE TABLE IF NOT EXISTS `privilege` (
`id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `manage_company` int(11) DEFAULT NULL,
  `manage_company_accessibility` int(11) DEFAULT NULL COMMENT 'ADMIN accessibility function that comprises\n\n*company deletion\n*block/unblock company\n\nDEVELOPER NOTES: This is not covered under ''manage_company'' option',
  `update_company` int(11) DEFAULT NULL,
  `manage_account` int(11) DEFAULT NULL,
  `manage_product` int(11) DEFAULT NULL,
  `manage_logs` int(11) DEFAULT NULL,
  `date_created` datetime DEFAULT CURRENT_TIMESTAMP,
  `date_modified` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `author_id` int(11) DEFAULT NULL COMMENT 'The one who assigned these privileges to you.\nMitigate the issue of finger pointing, looking for\nthe one who made the wrong settings for a specific account'
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `privilege`
--

INSERT INTO `privilege` (`id`, `account_id`, `manage_company`, `manage_company_accessibility`, `update_company`, `manage_account`, `manage_product`, `manage_logs`, `date_created`, `date_modified`, `author_id`) VALUES
(1, 1, 1, NULL, NULL, NULL, NULL, NULL, '2017-10-23 14:28:13', '2017-10-23 14:28:13', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE IF NOT EXISTS `product` (
`id` int(11) NOT NULL,
  `product_category_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `date_created` datetime DEFAULT CURRENT_TIMESTAMP,
  `date_modified` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_deleted` int(11) DEFAULT '0'
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`id`, `product_category_id`, `name`, `date_created`, `date_modified`, `is_deleted`) VALUES
(1, 1, 'HP PRO (Unclassified)', '2017-10-23 10:02:01', '2017-10-23 10:02:01', 0);

-- --------------------------------------------------------

--
-- Table structure for table `product_category`
--

CREATE TABLE IF NOT EXISTS `product_category` (
`id` int(11) NOT NULL,
  `company_id` int(11) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `description` text,
  `date_created` datetime DEFAULT CURRENT_TIMESTAMP,
  `date_modified` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_deleted` int(11) DEFAULT '0'
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `product_category`
--

INSERT INTO `product_category` (`id`, `company_id`, `parent_id`, `name`, `description`, `date_created`, `date_modified`, `is_deleted`) VALUES
(1, 1, NULL, 'Computers', NULL, '2017-10-23 09:13:16', '2017-10-23 09:13:16', 0),
(2, 1, 1, 'Desktop', NULL, '2017-10-23 09:13:38', '2017-10-23 09:13:38', 0);

-- --------------------------------------------------------

--
-- Table structure for table `product_template`
--

CREATE TABLE IF NOT EXISTS `product_template` (
`id` int(11) NOT NULL,
  `account_id` int(11) DEFAULT NULL COMMENT 'User can create their own template',
  `name` varchar(255) NOT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `date_created` datetime DEFAULT CURRENT_TIMESTAMP,
  `date_modified` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `product_template`
--

INSERT INTO `product_template` (`id`, `account_id`, `name`, `logo`, `date_created`, `date_modified`) VALUES
(1, NULL, 'Laptop', NULL, '2017-10-23 14:53:26', '2017-10-23 14:53:26');

-- --------------------------------------------------------

--
-- Table structure for table `product_template_specifications`
--

CREATE TABLE IF NOT EXISTS `product_template_specifications` (
`id` int(11) NOT NULL,
  `product_template_id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `active` int(11) DEFAULT NULL,
  `date_created` datetime DEFAULT CURRENT_TIMESTAMP,
  `date_modified` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `product_template_specifications`
--

INSERT INTO `product_template_specifications` (`id`, `product_template_id`, `name`, `active`, `date_created`, `date_modified`) VALUES
(1, 1, 'brand', 1, '2017-10-23 14:54:00', '2017-10-23 14:54:00'),
(2, 1, 'model', 1, '2017-10-23 14:54:00', '2017-10-23 14:54:00');

-- --------------------------------------------------------

--
-- Table structure for table `profile`
--

CREATE TABLE IF NOT EXISTS `profile` (
`id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `profile_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `middle_name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `department` varchar(255) DEFAULT NULL,
  `department_alias` varchar(255) DEFAULT NULL,
  `position` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `date_created` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `profile`
--

INSERT INTO `profile` (`id`, `uid`, `profile_name`, `last_name`, `first_name`, `middle_name`, `email`, `department`, `department_alias`, `position`, `image`, `date_created`) VALUES
(1, 1, 'Profile Name Sample', 'Last Name Sample', 'Fname', 'mname', 'email', 'Department Sample', 'Dept Allias', 'Position Sample', NULL, '2017-10-23 13:38:51');

-- --------------------------------------------------------

--
-- Table structure for table `specifications`
--

CREATE TABLE IF NOT EXISTS `specifications` (
`id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `value` text,
  `position` int(11) DEFAULT NULL COMMENT 'move specification up or down',
  `date_created` datetime DEFAULT CURRENT_TIMESTAMP,
  `date_modified` datetime DEFAULT CURRENT_TIMESTAMP,
  `is_deleted` int(11) DEFAULT '0'
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `specifications`
--

INSERT INTO `specifications` (`id`, `product_id`, `name`, `value`, `position`, `date_created`, `date_modified`, `is_deleted`) VALUES
(2, 1, 'brand', 'HP', NULL, '2017-10-23 14:41:34', '2017-10-23 14:41:34', 0),
(3, 1, 'model', 'EiteOne 800 G3', NULL, '2017-10-23 14:41:57', '2017-10-23 14:41:57', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `account`
--
ALTER TABLE `account`
 ADD PRIMARY KEY (`id`), ADD KEY `company_id` (`company_id`);

--
-- Indexes for table `account_session`
--
ALTER TABLE `account_session`
 ADD PRIMARY KEY (`id`), ADD KEY `account_id` (`account_id`);

--
-- Indexes for table `company`
--
ALTER TABLE `company`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `currency`
--
ALTER TABLE `currency`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `industry`
--
ALTER TABLE `industry`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `logs`
--
ALTER TABLE `logs`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `price`
--
ALTER TABLE `price`
 ADD PRIMARY KEY (`id`), ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `privilege`
--
ALTER TABLE `privilege`
 ADD PRIMARY KEY (`id`), ADD KEY `account_id` (`account_id`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
 ADD PRIMARY KEY (`id`), ADD KEY `product_category_id` (`product_category_id`);

--
-- Indexes for table `product_category`
--
ALTER TABLE `product_category`
 ADD PRIMARY KEY (`id`), ADD KEY `company_id` (`company_id`);

--
-- Indexes for table `product_template`
--
ALTER TABLE `product_template`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `product_template_specifications`
--
ALTER TABLE `product_template_specifications`
 ADD PRIMARY KEY (`id`), ADD KEY `product_template_id` (`product_template_id`);

--
-- Indexes for table `profile`
--
ALTER TABLE `profile`
 ADD PRIMARY KEY (`id`), ADD KEY `account` (`uid`);

--
-- Indexes for table `specifications`
--
ALTER TABLE `specifications`
 ADD PRIMARY KEY (`id`), ADD KEY `product_id` (`product_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `account`
--
ALTER TABLE `account`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `account_session`
--
ALTER TABLE `account_session`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `company`
--
ALTER TABLE `company`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `currency`
--
ALTER TABLE `currency`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `industry`
--
ALTER TABLE `industry`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `logs`
--
ALTER TABLE `logs`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `price`
--
ALTER TABLE `price`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `privilege`
--
ALTER TABLE `privilege`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `product_category`
--
ALTER TABLE `product_category`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `product_template`
--
ALTER TABLE `product_template`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `product_template_specifications`
--
ALTER TABLE `product_template_specifications`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `profile`
--
ALTER TABLE `profile`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `specifications`
--
ALTER TABLE `specifications`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `price`
--
ALTER TABLE `price`
ADD CONSTRAINT `product_price` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `product`
--
ALTER TABLE `product`
ADD CONSTRAINT `product_category` FOREIGN KEY (`product_category_id`) REFERENCES `product_category` (`id`);

--
-- Constraints for table `product_category`
--
ALTER TABLE `product_category`
ADD CONSTRAINT `product_category_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `company` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `product_template_specifications`
--
ALTER TABLE `product_template_specifications`
ADD CONSTRAINT `product_template` FOREIGN KEY (`product_template_id`) REFERENCES `product_template` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `specifications`
--
ALTER TABLE `specifications`
ADD CONSTRAINT `product_specification` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
