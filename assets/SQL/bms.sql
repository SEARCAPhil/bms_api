-- phpMyAdmin SQL Dump
-- version 4.7.9
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 18, 2018 at 12:51 PM
-- Server version: 10.1.31-MariaDB
-- PHP Version: 7.2.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bms`
--

-- --------------------------------------------------------

--
-- Table structure for table `account`
--

CREATE TABLE `account` (
  `id` int(11) NOT NULL,
  `company_id` int(11) DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `uid` varchar(255) DEFAULT NULL COMMENT 'contains openID from azure AD through O365 API',
  `status` int(11) NOT NULL,
  `date_created` datetime DEFAULT CURRENT_TIMESTAMP,
  `date_modified` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_deleted` int(11) DEFAULT '0',
  `is_deactivated` int(11) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `account_role`
--

CREATE TABLE `account_role` (
  `id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `role` varchar(255) NOT NULL,
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `account_session`
--

CREATE TABLE `account_session` (
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
-- Table structure for table `bidding`
--

CREATE TABLE `bidding` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `deadline` date NOT NULL,
  `status` int(11) NOT NULL DEFAULT '0',
  `excemption` int(11) NOT NULL DEFAULT '0',
  `created_by` int(11) NOT NULL,
  `approved_by` varchar(255) NOT NULL,
  `recommended_by` varchar(255) NOT NULL,
  `requested_by` varchar(255) NOT NULL,
  `approved_by_position` varchar(255) NOT NULL,
  `recommended_by_position` varchar(255) NOT NULL,
  `requested_by_position` varchar(255) NOT NULL,
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `bidding_attachments`
--

CREATE TABLE `bidding_attachments` (
  `id` int(11) NOT NULL,
  `bidding_id` int(11) NOT NULL,
  `account_id` int(11) DEFAULT NULL,
  `filename` varchar(255) NOT NULL,
  `original_filename` varchar(255) NOT NULL,
  `size` float NOT NULL,
  `type` varchar(100) NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` int(11) NOT NULL,
  `copy` varchar(255) NOT NULL DEFAULT 'original',
  `original_copy_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `bidding_collaborators`
--

CREATE TABLE `bidding_collaborators` (
  `id` int(11) NOT NULL,
  `bidding_id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `bidding_requirements`
--

CREATE TABLE `bidding_requirements` (
  `id` int(11) NOT NULL,
  `particular_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit` varchar(100) NOT NULL,
  `budget_amount` double NOT NULL,
  `budget_currency` varchar(50) NOT NULL DEFAULT 'PHP',
  `bidding_excemption_request` int(11) NOT NULL,
  `status` int(11) NOT NULL COMMENT 'excempted , normal , failure of bidding',
  `deadline` date NOT NULL,
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `bidding_requirements_attachments`
--

CREATE TABLE `bidding_requirements_attachments` (
  `id` int(11) NOT NULL,
  `bidding_requirements_id` int(11) NOT NULL,
  `account_id` int(11) DEFAULT NULL,
  `filename` varchar(255) NOT NULL,
  `original_filename` varchar(255) NOT NULL,
  `size` float NOT NULL,
  `type` varchar(100) NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` int(11) NOT NULL,
  `copy` varchar(255) NOT NULL DEFAULT 'original',
  `original_copy_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `bidding_requirements_awardees`
--

CREATE TABLE `bidding_requirements_awardees` (
  `id` int(11) NOT NULL,
  `bidding_requirements_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `proposal_id` int(11) NOT NULL,
  `remarks` text NOT NULL,
  `status` int(11) NOT NULL,
  `reference_no` varchar(255) NOT NULL,
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `bidding_requirements_funds`
--

CREATE TABLE `bidding_requirements_funds` (
  `id` int(11) NOT NULL,
  `bidding_requirements_id` int(11) NOT NULL,
  `fund_type` varchar(255) NOT NULL,
  `cost_center` varchar(255) NOT NULL,
  `line_item` varchar(255) NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `bidding_requirements_invitation`
--

CREATE TABLE `bidding_requirements_invitation` (
  `id` int(11) NOT NULL,
  `bidding_requirements_id` int(11) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `approved_by` varchar(255) NOT NULL,
  `status` int(11) NOT NULL,
  `date_created` datetime NOT NULL,
  `date_modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `bidding_requirements_proposals`
--

CREATE TABLE `bidding_requirements_proposals` (
  `id` int(11) NOT NULL,
  `bidding_requirements_id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `currency` varchar(255) NOT NULL DEFAULT 'PHP',
  `amount` double NOT NULL DEFAULT '0',
  `discount` double NOT NULL DEFAULT '0',
  `remarks` text NOT NULL,
  `bidders_remarks` text NOT NULL,
  `status` int(11) NOT NULL,
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `date_submitted` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `bidding_requirements_proposals_attachments`
--

CREATE TABLE `bidding_requirements_proposals_attachments` (
  `id` int(11) NOT NULL,
  `bidding_requirements_proposals_id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `original_filename` varchar(255) NOT NULL,
  `size` float NOT NULL,
  `type` varchar(50) NOT NULL,
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `bidding_requirements_proposals_specs`
--

CREATE TABLE `bidding_requirements_proposals_specs` (
  `id` int(11) NOT NULL,
  `bidding_requirements_proposals_id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `value` text,
  `position` int(11) DEFAULT NULL COMMENT 'move specification up or down',
  `bidding_requirements_specs_id` int(11) NOT NULL COMMENT 'this should match with original specs if given otherwise use the value of name field',
  `date_created` datetime DEFAULT CURRENT_TIMESTAMP,
  `date_modified` datetime DEFAULT CURRENT_TIMESTAMP,
  `status` int(11) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `bidding_requirements_specs`
--

CREATE TABLE `bidding_requirements_specs` (
  `id` int(11) NOT NULL,
  `bidding_requirements_id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `value` text,
  `position` int(11) DEFAULT NULL COMMENT 'move specification up or down',
  `date_created` datetime DEFAULT CURRENT_TIMESTAMP,
  `date_modified` datetime DEFAULT CURRENT_TIMESTAMP,
  `status` int(11) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `company`
--

CREATE TABLE `company` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `alias` varchar(255) NOT NULL,
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `company_contact_info`
--

CREATE TABLE `company_contact_info` (
  `id` int(11) NOT NULL,
  `type` enum('email','phone','mobile') NOT NULL,
  `value` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `currency`
--

CREATE TABLE `currency` (
  `id` int(11) NOT NULL,
  `country_code` varchar(255) DEFAULT NULL,
  `currency` varchar(45) DEFAULT NULL,
  `date_created` datetime DEFAULT CURRENT_TIMESTAMP,
  `date_modified` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `department`
--

CREATE TABLE `department` (
  `dept_id` int(11) NOT NULL,
  `dept_name` varchar(255) NOT NULL,
  `dept_alias` varchar(100) DEFAULT NULL,
  `is_active` int(11) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `feedback` text NOT NULL,
  `date_created` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `industry`
--

CREATE TABLE `industry` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL COMMENT 'ex. Telecommunication\nConstruction\nConsultation Firm\netc...',
  `date_created` datetime DEFAULT CURRENT_TIMESTAMP,
  `date_modified` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `logs`
--

CREATE TABLE `logs` (
  `id` int(11) NOT NULL,
  `account_id` int(11) DEFAULT NULL,
  `message` text NOT NULL,
  `date_created` datetime DEFAULT CURRENT_TIMESTAMP,
  `event` varchar(45) DEFAULT NULL COMMENT 'executed events includes CRUD for:\n\naccount\ncompany\nproducts\n\n'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `particulars`
--

CREATE TABLE `particulars` (
  `id` int(11) NOT NULL,
  `bidding_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `deadline` date NOT NULL,
  `status` int(11) NOT NULL,
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `price`
--

CREATE TABLE `price` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `amount` double DEFAULT NULL,
  `currency` varchar(45) DEFAULT NULL,
  `date_created` datetime DEFAULT CURRENT_TIMESTAMP,
  `date_modified` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `privilege`
--

CREATE TABLE `privilege` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `product_category_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `date_created` datetime DEFAULT CURRENT_TIMESTAMP,
  `date_modified` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `product_category`
--

CREATE TABLE `product_category` (
  `id` int(11) NOT NULL,
  `company_id` int(11) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `description` text,
  `date_created` datetime DEFAULT CURRENT_TIMESTAMP,
  `date_modified` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `product_template`
--

CREATE TABLE `product_template` (
  `id` int(11) NOT NULL,
  `account_id` int(11) DEFAULT NULL COMMENT 'User can create their own template',
  `name` varchar(255) NOT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `date_created` datetime DEFAULT CURRENT_TIMESTAMP,
  `date_modified` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `product_template_specifications`
--

CREATE TABLE `product_template_specifications` (
  `id` int(11) NOT NULL,
  `product_template_id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `active` int(11) DEFAULT '1',
  `date_created` datetime DEFAULT CURRENT_TIMESTAMP,
  `date_modified` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `profile`
--

CREATE TABLE `profile` (
  `id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `signatories`
--

CREATE TABLE `signatories` (
  `id` int(11) NOT NULL,
  `department` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `position` varchar(255) NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `specifications`
--

CREATE TABLE `specifications` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `value` text,
  `position` int(11) DEFAULT NULL COMMENT 'move specification up or down',
  `date_created` datetime DEFAULT CURRENT_TIMESTAMP,
  `date_modified` datetime DEFAULT CURRENT_TIMESTAMP,
  `status` int(11) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `account`
--
ALTER TABLE `account`
  ADD PRIMARY KEY (`id`),
  ADD KEY `company_id` (`company_id`);

--
-- Indexes for table `account_role`
--
ALTER TABLE `account_role`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `account_session`
--
ALTER TABLE `account_session`
  ADD PRIMARY KEY (`id`),
  ADD KEY `account_id` (`account_id`);

--
-- Indexes for table `bidding`
--
ALTER TABLE `bidding`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bidding_attachments`
--
ALTER TABLE `bidding_attachments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `basket_id` (`bidding_id`),
  ADD KEY `profile_id` (`account_id`);

--
-- Indexes for table `bidding_collaborators`
--
ALTER TABLE `bidding_collaborators`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bidding_requirements`
--
ALTER TABLE `bidding_requirements`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bidding_requirements_attachments`
--
ALTER TABLE `bidding_requirements_attachments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `basket_id` (`bidding_requirements_id`),
  ADD KEY `profile_id` (`account_id`);

--
-- Indexes for table `bidding_requirements_awardees`
--
ALTER TABLE `bidding_requirements_awardees`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bidding_requirements_funds`
--
ALTER TABLE `bidding_requirements_funds`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bidding_requirements_invitation`
--
ALTER TABLE `bidding_requirements_invitation`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bidding_requirements_proposals`
--
ALTER TABLE `bidding_requirements_proposals`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bidding_requirements_proposals_attachments`
--
ALTER TABLE `bidding_requirements_proposals_attachments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bidding_requirements_proposals_specs`
--
ALTER TABLE `bidding_requirements_proposals_specs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bidding_requirements_specs`
--
ALTER TABLE `bidding_requirements_specs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`bidding_requirements_id`);

--
-- Indexes for table `company`
--
ALTER TABLE `company`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `company_contact_info`
--
ALTER TABLE `company_contact_info`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `currency`
--
ALTER TABLE `currency`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `department`
--
ALTER TABLE `department`
  ADD PRIMARY KEY (`dept_id`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
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
-- Indexes for table `particulars`
--
ALTER TABLE `particulars`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `price`
--
ALTER TABLE `price`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `privilege`
--
ALTER TABLE `privilege`
  ADD PRIMARY KEY (`id`),
  ADD KEY `account_id` (`account_id`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `product_category`
--
ALTER TABLE `product_category`
  ADD PRIMARY KEY (`id`),
  ADD KEY `company_id` (`company_id`);

--
-- Indexes for table `product_template`
--
ALTER TABLE `product_template`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `product_template_specifications`
--
ALTER TABLE `product_template_specifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_template_id` (`product_template_id`);

--
-- Indexes for table `profile`
--
ALTER TABLE `profile`
  ADD PRIMARY KEY (`id`),
  ADD KEY `account` (`account_id`);

--
-- Indexes for table `signatories`
--
ALTER TABLE `signatories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `specifications`
--
ALTER TABLE `specifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `account`
--
ALTER TABLE `account`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `account_role`
--
ALTER TABLE `account_role`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `account_session`
--
ALTER TABLE `account_session`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=243;

--
-- AUTO_INCREMENT for table `bidding`
--
ALTER TABLE `bidding`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT for table `bidding_attachments`
--
ALTER TABLE `bidding_attachments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT for table `bidding_collaborators`
--
ALTER TABLE `bidding_collaborators`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `bidding_requirements`
--
ALTER TABLE `bidding_requirements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT for table `bidding_requirements_attachments`
--
ALTER TABLE `bidding_requirements_attachments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `bidding_requirements_awardees`
--
ALTER TABLE `bidding_requirements_awardees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `bidding_requirements_funds`
--
ALTER TABLE `bidding_requirements_funds`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=74;

--
-- AUTO_INCREMENT for table `bidding_requirements_invitation`
--
ALTER TABLE `bidding_requirements_invitation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT for table `bidding_requirements_proposals`
--
ALTER TABLE `bidding_requirements_proposals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `bidding_requirements_proposals_attachments`
--
ALTER TABLE `bidding_requirements_proposals_attachments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `bidding_requirements_proposals_specs`
--
ALTER TABLE `bidding_requirements_proposals_specs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=82;

--
-- AUTO_INCREMENT for table `bidding_requirements_specs`
--
ALTER TABLE `bidding_requirements_specs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=83;

--
-- AUTO_INCREMENT for table `company`
--
ALTER TABLE `company`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `company_contact_info`
--
ALTER TABLE `company_contact_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `currency`
--
ALTER TABLE `currency`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `department`
--
ALTER TABLE `department`
  MODIFY `dept_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `industry`
--
ALTER TABLE `industry`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `logs`
--
ALTER TABLE `logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `particulars`
--
ALTER TABLE `particulars`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `price`
--
ALTER TABLE `price`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `privilege`
--
ALTER TABLE `privilege`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `product_category`
--
ALTER TABLE `product_category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product_template`
--
ALTER TABLE `product_template`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product_template_specifications`
--
ALTER TABLE `product_template_specifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `profile`
--
ALTER TABLE `profile`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `signatories`
--
ALTER TABLE `signatories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `specifications`
--
ALTER TABLE `specifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=87;

--
-- Constraints for dumped tables
--

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
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
