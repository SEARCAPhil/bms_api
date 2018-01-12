-- phpMyAdmin SQL Dump
-- version 4.2.11
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Jan 12, 2018 at 04:20 AM
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

--
-- Dumping data for table `account`
--

INSERT INTO `account` (`id`, `company_id`, `username`, `password`, `uid`, `status`, `date_created`, `date_modified`, `is_deleted`, `is_deactivated`) VALUES
(1, 1, 'test@domain.org', NULL, 'abc-test-token-1234', 0, '2017-10-23 11:42:16', '2017-10-24 16:36:10', 0, 0),
(5, 17, 'admin', '33a3820c8905a59e46ff244bc93e56c441e7b3e2', NULL, 0, '2018-01-10 14:54:47', '2018-01-12 11:14:05', 0, 0),
(6, 17, 'jkga', '12fdc8dd92130d207dcc1e5ae23aa8d436a1bffd', NULL, 0, '2018-01-11 11:22:36', '2018-01-12 10:59:22', 0, 0);

--
-- Dumping data for table `company`
--

INSERT INTO `company` (`id`, `name`, `alias`, `tagline`, `about`, `established_month`, `established_date`, `established_year`, `location`, `industry`, `logo`, `status`, `date_created`) VALUES
(1, 'Southeast Asian Regional Center for Graduate Study and Research in Agriculture (SEARCA)', 'SEARCA', '', 'The Southeast Asian Regional Center for Graduate Study and Research in Agriculture (SEARCA) is a non-profit organization established by the Southeast Asian Ministers of Education Organization (SEAMEO) in 1966.\r\n\r\nFounded in 1965, SEAMEO is a chartered international organization whose purpose is to promote cooperation in education, science and culture in the Southeast Asian region. Its highest policymaking body is the SEAMEO Council, which comprises the Ministers of Education of the 11 SEAMEO Member Countries, namely: Brunei Darussalam, Cambodia, Indonesia, Lao PDR, Malaysia, Myanmar, the Philippines, Singapore, Thailand, Timor-Leste, and Vietnam.\r\n\r\nSEAMEO also has Associate Member Countries, namely: Australia, Canada, France, Germany, Netherlands, New Zealand, Spain, and the United Kingdom.\r\n\r\nThe Center derives its juridical personality from the SEAMEO Charter and possesses full capacity to contract; acquire, and dispose of, immovable and movable property; and institute legal proceedings. Moreover, SEARCA enjoys in the territory of each of its member states such privileges and immunities as are normally accorded United Nations institutions. Representatives of member states and officials of the Center shall similarly enjoy such privileges and immunities in the Philippines as are necessary for the exercise of their functions in connection with SEARCA and SEAMEO.', NULL, NULL, 1960, 'UPLB', 'Agriculture', 'http://www.searca.org/images/SEARCA-web-logo.png', 0, '2017-10-17 11:06:35'),
(8, 'University of the Philippines', '', 'Southeast Asian Regional Center for Graduate Study and Research in Agriculture (SEARCA)', 'The University of the Philippines Los Baños (UPLB), a coeducational, publicly funded academic, research and extension institution, is one of the seven constituent universities of the University of the Philippines System. It started out as a College of Agriculture in 1909, became a full-fledged university in 1972, and has emerged as a leading academic institution in Southeast Asia.\r\n\r\nUPLB continues to endeavor to develop a critical mass of professionals in its traditional strongholds of agriculture and forestry and allied fields, and in its niche fields in natural resources management and conservation, environmental science, and in other areas such as engineering, biotechnology, nanotechnology, and informatics and computer science. It will develop leaders who are committed to advance inclusive growth through education, research and public service.\r\n\r\nIts outstanding achievements in the basic and applied sciences are testaments to the great strides it has made for the past years. The alumni continue to be the prime movers in academe, in government and in business.', NULL, NULL, 1960, 'UPLB', 'Agriculture', 'http://www.searca.org/images/SEARCA-web-logo.png', 0, '2017-10-17 11:06:35'),
(9, 'Southeast Asian Regional Center for Graduate Studies in Research and Agriculture (SEARCA)', '', 'Southeast Asian Regional Center for Graduate Study and Research in Agriculture (SEARCA)', 'The Southeast Asian Regional Center for Graduate Study and Research in Agriculture (SEARCA) is a non-profit organization established by the Southeast Asian Ministers of Education Organization (SEAMEO) in 1966.\r\n\r\nFounded in 1965, SEAMEO is a chartered international organization whose purpose is to promote cooperation in education, science and culture in the Southeast Asian region. Its highest policymaking body is the SEAMEO Council, which comprises the Ministers of Education of the 11 SEAMEO Member Countries, namely: Brunei Darussalam, Cambodia, Indonesia, Lao PDR, Malaysia, Myanmar, the Philippines, Singapore, Thailand, Timor-Leste, and Vietnam.\r\n\r\nSEAMEO also has Associate Member Countries, namely: Australia, Canada, France, Germany, Netherlands, New Zealand, Spain, and the United Kingdom.\r\n\r\nThe Center derives its juridical personality from the SEAMEO Charter and possesses full capacity to contract; acquire, and dispose of, immovable and movable property; and institute legal proceedings. Moreover, SEARCA enjoys in the territory of each of its member states such privileges and immunities as are normally accorded United Nations institutions. Representatives of member states and officials of the Center shall similarly enjoy such privileges and immunities in the Philippines as are necessary for the exercise of their functions in connection with SEARCA and SEAMEO.', NULL, NULL, 1960, 'UPLB', 'Agriculture', 'http://www.searca.org/images/SEARCA-web-logo.png', 0, '2017-10-17 11:06:35'),
(14, 'SEARCA', '', '', '', 0, 0, 0, '', '', NULL, 0, '2017-11-22 10:32:12'),
(15, 'Accent Micro Technology Inc., Phil', 'AMTI', 'Innovative Technology Solutions', 'The most dynamic and diversified company in the local technology landscape today, AMTI is a technology solutions and systems provider that helps companies manage and transform their business by taking advantage of the advances in technology. AMTI&amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;acirc;&amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;Acirc;&amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;Acirc;&amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;Acirc;&amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;Acirc;&amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;Acirc;&amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;Acirc;&amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;Acirc;&amp;amp;amp;amp;amp;amp;amp;amp;Acirc;&amp;amp;amp;amp;amp;amp;Acirc;&amp;amp;amp;amp;Acirc;&amp;amp;Acirc;&Acirc;&amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;Acirc;&amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;Acirc;&amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;Acirc;&amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;Acirc;&amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;Acirc;&amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;Acirc;&amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;Acirc;&amp;amp;amp;amp;amp;amp;amp;amp;Acirc;&amp;amp;amp;amp;amp;amp;Acirc;&amp;amp;amp;amp;Acirc;&amp;amp;Acirc;&Acirc;s technology solutions are anchored on three key elements: (1) the credibility and bigness of global technology; (2) the innovation and ingenuity of Filipinos; and (3) holistic and integrated plans and systems that address the most pressing technological issues.\n\nAMTI started as a technological hardware distributor for a globally-known computer brand, but it has since created a name for itself as a trusted global technology expert. Currently, the company offers 12 types of technological solutions that transcend multiple industries.', 0, 0, 0, '', 'ICT , Telecom , test', NULL, 0, '2017-11-22 10:36:24'),
(16, 'sdftwss', '', '', '', 0, 0, 0, '', '', NULL, 0, '2018-01-08 15:39:50'),
(17, 'abcde', '', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation', '', 0, 0, 0, '', '', NULL, 0, '2018-01-10 09:10:02');

--
-- Dumping data for table `logs`
--

INSERT INTO `logs` (`id`, `account_id`, `message`, `date_created`, `event`) VALUES
(1, 1, 'events.js:160\r\nthrow er; // Unhandled ''error'' event\r\n^\r\n\r\nError: C:/xampp/htdocs/popup-es/dist/src/js/popup-es.min.js: original.line and original.column are not numbers -- you probably meant to omit the original mapping entirely and only map the generated position. If so, pass null for the original mapping instead of an object with empty or null values.\r\nat SourceMapGenerator_validateMapping [as _validateMapping] (C:\\xampp\\htdocs\\popup-es\\node_modules\\source-map\\lib\\source-map-generator.js:276:15)', '2017-10-23 11:45:22', 'warning'),
(2, 1, 'Account has been blocked', '2018-01-09 11:08:24', 'account'),
(3, 1, 'Account has been blocked', '2018-01-09 11:10:03', 'account'),
(4, 1, 'Account has been unblocked', '2018-01-09 11:10:24', 'account');

--
-- Dumping data for table `price`
--

INSERT INTO `price` (`id`, `product_id`, `amount`, `currency`, `date_created`, `date_modified`) VALUES
(1, 1, 25000, 'PHP', '2017-10-23 00:00:00', '2017-10-23 00:00:00'),
(4, 1, 100, 'PHP', '2017-10-24 11:37:22', '2017-10-24 11:37:22'),
(5, 1, 100, 'PHP', '2017-10-24 15:27:49', '2017-10-24 15:27:49'),
(6, 1, 250, 'USD', '2017-10-24 15:28:21', '2017-10-24 15:28:21');

--
-- Dumping data for table `privilege`
--

INSERT INTO `privilege` (`id`, `account_id`, `manage_company`, `manage_company_accessibility`, `update_company`, `manage_account`, `manage_product`, `manage_logs`, `date_created`, `date_modified`, `author_id`) VALUES
(1, 1, 1, NULL, NULL, NULL, NULL, NULL, '2017-10-23 14:28:13', '2017-10-23 14:28:13', NULL);

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`id`, `product_category_id`, `name`, `date_created`, `date_modified`, `is_deleted`) VALUES
(1, 1, 'HP PRO (Unclassified)', '2017-10-23 10:02:01', '2017-10-24 10:59:57', 0);

--
-- Dumping data for table `product_category`
--

INSERT INTO `product_category` (`id`, `company_id`, `parent_id`, `name`, `description`, `date_created`, `date_modified`, `is_deleted`) VALUES
(1, 1, NULL, 'Computers', 'Look around your environment for a while. See how everyone – from businessmen to students, to kids and adults, parents and children – has some form of computer that people use for multiple reasons and purposes. Many years ago, this type of device was quite an outstanding technology, that it was only accessible and used for large business and government projects. Today, anyone can have any different form of this technology – from a trusted desktop PC to a more portable laptop and even the defining accessories for these types of tech such as the innovating gaming mouse and gaming keyboards that refined the standard ones.While the two variants of the gadget can serve you the same purpose, there’s still a degree of contrast between them that you may want to check out the two computer types’ features before deciding.', '2017-10-23 09:13:16', '2017-11-16 15:18:29', 0),
(2, 1, 1, 'Desktop', NULL, '2017-10-23 09:13:38', '2017-10-23 09:13:38', 0);

--
-- Dumping data for table `product_template`
--

INSERT INTO `product_template` (`id`, `account_id`, `name`, `logo`, `date_created`, `date_modified`) VALUES
(1, NULL, 'Laptop', NULL, '2017-10-23 14:53:26', '2017-10-23 14:53:26');

--
-- Dumping data for table `product_template_specifications`
--

INSERT INTO `product_template_specifications` (`id`, `product_template_id`, `name`, `active`, `date_created`, `date_modified`) VALUES
(1, 1, 'brand', 1, '2017-10-23 14:54:00', '2017-10-23 14:54:00'),
(2, 1, 'model', 1, '2017-10-23 14:54:00', '2017-10-23 14:54:00');

--
-- Dumping data for table `profile`
--

INSERT INTO `profile` (`id`, `uid`, `profile_name`, `last_name`, `first_name`, `middle_name`, `email`, `department`, `department_alias`, `position`, `image`, `date_created`) VALUES
(1, 1, 'Profile Name Sample', 'Last Name Sample', 'Fname', 'mname', 'email', 'Department Sample', 'Dept Allias', 'Position Sample', NULL, '2017-10-23 13:38:51');

--
-- Dumping data for table `specifications`
--

INSERT INTO `specifications` (`id`, `product_id`, `name`, `value`, `position`, `date_created`, `date_modified`, `is_deleted`) VALUES
(2, 1, 'brand', 'HP', NULL, '2017-10-23 14:41:34', '2017-10-23 14:41:34', 0),
(3, 1, 'model', 'EiteOne 800 G3', NULL, '2017-10-23 14:41:57', '2017-10-23 14:41:57', 0);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
