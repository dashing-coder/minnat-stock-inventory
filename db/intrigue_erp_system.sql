-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: Oct 30, 2026 at 12:26 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `intrigue_erp_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `accounting_periods`
--

CREATE TABLE `accounting_periods` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'open',
  `closed_by` bigint(20) UNSIGNED DEFAULT NULL,
  `closed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

CREATE TABLE `accounts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(30) NOT NULL,
  `name` varchar(150) NOT NULL,
  `type` varchar(30) NOT NULL,
  `subtype` varchar(50) DEFAULT NULL,
  `parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `is_system` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `accounts`
--

INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `subtype`, `parent_id`, `is_system`, `is_active`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, '1000', 'Cash on Hand', 'asset', 'cash', NULL, 1, 1, '2026-08-27 18:00:00', '2026-08-27 18:00:00', NULL),
(2, '1010', 'Bank Account', 'asset', 'bank', NULL, 1, 1, '2026-08-27 18:00:00', '2026-08-27 18:00:00', NULL),
(3, '1100', 'Accounts Receivable', 'asset', 'receivable', NULL, 1, 1, '2026-08-27 18:00:00', '2026-08-27 18:00:00', NULL),
(4, '1200', 'Inventory', 'asset', 'inventory', NULL, 1, 1, '2026-08-27 18:00:00', '2026-08-27 18:00:00', NULL),
(5, '2000', 'Accounts Payable', 'liability', 'payable', NULL, 1, 1, '2026-08-27 18:00:00', '2026-08-27 18:00:00', NULL),
(6, '2100', 'Payroll Payable', 'liability', 'payroll', NULL, 1, 1, '2026-08-27 18:00:00', '2026-08-27 18:00:00', NULL),
(7, '3000', 'Owner Equity', 'equity', 'capital', NULL, 1, 1, '2026-08-27 18:00:00', '2026-08-27 18:00:00', NULL),
(8, '3100', 'Opening Balance Equity', 'equity', 'opening', NULL, 1, 1, '2026-08-27 18:00:00', '2026-08-27 18:00:00', NULL),
(9, '4000', 'Sales Revenue', 'income', 'sales', NULL, 1, 1, '2026-08-27 18:00:00', '2026-08-27 18:00:00', NULL),
(10, '4010', 'Sales Returns & Allowances', 'income', 'sales_return', NULL, 1, 1, '2026-08-27 18:00:00', '2026-08-27 18:00:00', NULL),
(11, '4100', 'Other Income', 'income', 'other', NULL, 1, 1, '2026-08-27 18:00:00', '2026-08-27 18:00:00', NULL),
(12, '4200', 'Inventory Adjustment Gain', 'income', 'inventory_gain', NULL, 1, 1, '2026-08-27 18:00:00', '2026-08-27 18:00:00', NULL),
(13, '5000', 'Cost of Goods Sold', 'expense', 'cogs', NULL, 1, 1, '2026-08-27 18:00:00', '2026-08-27 18:00:00', NULL),
(14, '6000', 'Operating Expense', 'expense', 'operating', NULL, 1, 1, '2026-08-27 18:00:00', '2026-08-27 18:00:00', NULL),
(15, '6010', 'Salary Expense', 'expense', 'salary', NULL, 1, 1, '2026-08-27 18:00:00', '2026-08-27 18:00:00', NULL),
(16, '6020', 'Rent Expense', 'expense', 'rent', NULL, 1, 1, '2026-08-27 18:00:00', '2026-08-27 18:00:00', NULL),
(17, '6030', 'Utilities Expense', 'expense', 'utility', NULL, 1, 1, '2026-08-27 18:00:00', '2026-08-27 18:00:00', NULL),
(18, '6040', 'Inventory Adjustment Loss', 'expense', 'inventory_loss', NULL, 1, 1, '2026-08-27 18:00:00', '2026-08-27 18:00:00', NULL),
(19, '6990', 'Other Expense', 'expense', 'other', NULL, 1, 1, '2026-08-27 18:00:00', '2026-08-27 18:00:00', NULL),
(20, '1300', 'Loan Receivable', 'asset', 'loan_receivable', NULL, 1, 1, '2026-10-22 06:24:43', '2026-10-22 06:24:43', NULL),
(21, '2200', 'Loan Payable', 'liability', 'loan_payable', NULL, 1, 1, '2026-10-22 06:24:43', '2026-10-22 06:24:43', NULL),
(22, '4110', 'Interest Income', 'income', 'interest', NULL, 1, 1, '2026-10-22 06:24:43', '2026-10-22 06:24:43', NULL),
(23, '6050', 'Interest Expense', 'expense', 'interest', NULL, 1, 1, '2026-10-22 06:24:43', '2026-10-22 06:24:43', NULL),
(24, '1310', 'Accumulated Depreciation', 'asset', 'contra_asset', NULL, 1, 1, '2026-10-23 06:20:12', '2026-10-23 06:20:12', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `action` varchar(30) NOT NULL,
  `route_name` varchar(150) DEFAULT NULL,
  `method` varchar(10) NOT NULL,
  `url` text NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `action`, `route_name`, `method`, `url`, `payload`, `ip_address`, `user_agent`, `created_at`, `updated_at`) VALUES
(1, 1, 'create', 'sales.store', 'POST', '/sales', '{\"note\": \"Created sale invoice\"}', '127.0.0.1', 'Demo Browser', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(2, 1, 'post', 'sales.post', 'POST', '/sales/42/post', '{\"note\": \"Posted sale invoice\"}', '127.0.0.1', 'Demo Browser', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(3, 1, 'create', 'purchases.store', 'POST', '/purchases', '{\"note\": \"Created purchase\"}', '127.0.0.1', 'Demo Browser', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(4, 1, 'post', 'purchases.post', 'POST', '/purchases/36/post', '{\"note\": \"Posted purchase\"}', '127.0.0.1', 'Demo Browser', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(5, 1, 'create', 'expenses.store', 'POST', '/expenses', '{\"note\": \"Created expense\"}', '127.0.0.1', 'Demo Browser', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(6, 1, 'post', 'payroll-runs.post', 'POST', '/payroll-runs/1/post', '{\"note\": \"Posted payroll\"}', '127.0.0.1', 'Demo Browser', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(7, 1, 'view', 'reports.index', 'GET', '/reports', '{\"note\": \"Viewed reports\"}', '127.0.0.1', 'Demo Browser', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(8, 1, 'update', 'settings.update', 'PUT', '/settings', '{\"note\": \"Updated settings\"}', '127.0.0.1', 'Demo Browser', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(9, 1, 'patch', 'notifications.read', 'PATCH', 'http://127.0.0.1:8000/notifications/3/read', '{\"_method\":\"PATCH\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-28 04:54:06', '2026-08-28 04:54:06'),
(10, 1, 'patch', 'notifications.read', 'PATCH', 'http://127.0.0.1:8000/notifications/2/read', '{\"_method\":\"PATCH\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-28 04:54:09', '2026-08-28 04:54:09'),
(11, 1, 'patch', 'notifications.read', 'PATCH', 'http://127.0.0.1:8000/notifications/1/read', '{\"_method\":\"PATCH\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-28 04:54:10', '2026-08-28 04:54:10'),
(12, 1, 'patch', 'notifications.read-all', 'PATCH', 'http://127.0.0.1:8000/notifications/read-all', '{\"_method\":\"PATCH\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-28 04:54:11', '2026-08-28 04:54:11'),
(13, 1, 'patch', 'notifications.read-all', 'PATCH', 'http://127.0.0.1:8000/notifications/read-all', '{\"_method\":\"PATCH\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-28 05:09:07', '2026-08-28 05:09:07'),
(14, 1, 'put', 'settings.update', 'PUT', 'http://127.0.0.1:8000/settings', '{\"_method\":\"PUT\",\"settings\":{\"invoice_prefix\":\"SAL\",\"purchase_prefix\":\"PUR\",\"company_address\":null,\"company_email\":null,\"company_name\":\"Laravel ERP Demo\",\"company_phone\":null,\"currency\":\"BDT\",\"date_format\":\"Y-m-d\",\"timezone\":\"Asia\\/Dhaka\",\"allow_negative_stock\":\"0\",\"low_stock_notifications\":\"1\"}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-28 05:09:21', '2026-08-28 05:09:21'),
(15, 1, 'post', 'login.store', 'POST', 'http://127.0.0.1:8000/login', '{\"login\":\"admin@example.com\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-29 01:53:59', '2026-08-29 01:53:59'),
(16, 1, 'post', 'login.store', 'POST', 'http://127.0.0.1:8000/login', '{\"login\":\"admin@example.com\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-29 05:21:12', '2026-08-29 05:21:12'),
(17, 1, 'post', 'login.store', 'POST', 'http://127.0.0.1:8000/login', '{\"login\":\"admin@example.com\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-13 08:59:47', '2026-09-13 08:59:47'),
(18, 1, 'post', 'login.store', 'POST', 'http://127.0.0.1:8000/login', '{\"login\":\"admin@example.com\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-22 06:20:45', '2026-09-22 06:20:45'),
(19, 1, 'post', 'login.store', 'POST', 'http://127.0.0.1:8000/login', '{\"login\":\"admin@example.com\"}', '103.187.94.76', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-22 12:05:16', '2026-09-22 12:05:16'),
(20, 1, 'post', 'login.store', 'POST', 'http://127.0.0.1:8000/login', '{\"login\":\"admin@example.com\"}', '103.115.116.85', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Mobile Safari/537.36', '2026-09-22 12:13:34', '2026-09-22 12:13:34'),
(21, 1, 'post', 'login.store', 'POST', 'http://127.0.0.1:8000/login', '{\"login\":\"admin@example.com\"}', '37.111.206.155', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Mobile Safari/537.36', '2026-09-22 15:26:52', '2026-09-22 15:26:52'),
(22, 1, 'post', 'login.store', 'POST', 'http://127.0.0.1:8000/login', '{\"login\":\"admin@example.com\"}', '103.72.212.222', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-23 10:52:27', '2026-09-23 10:52:27'),
(23, 1, 'post', 'login.store', 'POST', 'http://127.0.0.1:8000/login', '{\"login\":\"admin@example.com\"}', '103.108.145.121', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-23 12:17:22', '2026-09-23 12:17:22'),
(24, 1, 'post', 'login.store', 'POST', 'http://127.0.0.1:8000/login', '{\"login\":\"admin@example.com\"}', '103.72.212.222', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-23 13:16:38', '2026-09-23 13:16:38'),
(25, 1, 'post', 'login.store', 'POST', 'http://127.0.0.1:8000/login', '{\"login\":\"admin@example.com\"}', '103.72.212.222', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-23 13:16:45', '2026-09-23 13:16:45'),
(26, 1, 'post', 'login.store', 'POST', 'http://127.0.0.1:8000/login', '{\"login\":\"admin@example.com\"}', '103.72.212.222', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-23 13:31:41', '2026-09-23 13:31:41'),
(27, 1, 'post', 'login.store', 'POST', 'http://127.0.0.1:8000/login', '{\"login\":\"admin@example.com\"}', '103.72.212.222', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-23 14:38:03', '2026-09-23 14:38:03'),
(28, 1, 'post', 'login.store', 'POST', 'http://127.0.0.1:8000/login', '{\"login\":\"admin@example.com\"}', '103.72.212.222', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-23 14:41:03', '2026-09-23 14:41:03'),
(29, 1, 'post', 'login.store', 'POST', 'http://127.0.0.1:8000/login', '{\"login\":\"admin@example.com\"}', '103.72.212.222', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-23 14:51:54', '2026-09-23 14:51:54'),
(30, 1, 'post', 'payrolls.store', 'POST', 'http://127.0.0.1:8000/payrolls', '{\"period\":\"2026-09\",\"pay_date\":null,\"notes\":null}', '103.72.212.222', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-23 15:58:27', '2026-09-23 15:58:27'),
(31, 1, 'post', 'payrolls.post', 'POST', 'http://127.0.0.1:8000/payrolls/2/post', '[]', '103.72.212.222', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-23 16:15:06', '2026-09-23 16:15:06'),
(32, 1, 'put', 'settings.update', 'PUT', 'http://127.0.0.1:8000/settings', '{\"_method\":\"PUT\",\"settings\":{\"invoice_prefix\":\"SAL\",\"purchase_prefix\":\"PUR\",\"company_address\":null,\"company_email\":null,\"company_name\":\"MS Afrin Traders\",\"company_phone\":null,\"currency\":\"BDT\",\"date_format\":\"Y-m-d\",\"timezone\":\"Asia\\/Dhaka\",\"allow_negative_stock\":\"0\",\"low_stock_notifications\":\"1\"}}', '103.72.212.222', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-23 17:40:50', '2026-09-23 17:40:50'),
(33, 1, 'put', 'settings.update', 'PUT', 'http://127.0.0.1:8000/settings', '{\"_method\":\"PUT\",\"settings\":{\"invoice_prefix\":\"SAL\",\"purchase_prefix\":\"PUR\",\"company_address\":null,\"company_email\":null,\"company_name\":\"MS Afrin Traders\",\"company_phone\":null,\"currency\":\"BDT\",\"date_format\":\"Y-m-d\",\"timezone\":\"Asia\\/Dhaka\",\"allow_negative_stock\":\"0\",\"low_stock_notifications\":\"1\"}}', '103.72.212.222', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-23 17:41:10', '2026-09-23 17:41:10'),
(34, 1, 'patch', 'notifications.read-all', 'PATCH', 'http://127.0.0.1:8000/notifications/read-all', '{\"_method\":\"PATCH\"}', '103.72.212.222', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-23 18:04:42', '2026-09-23 18:04:42'),
(35, 1, 'patch', 'notifications.read-all', 'PATCH', 'http://127.0.0.1:8000/notifications/read-all', '{\"_method\":\"PATCH\"}', '103.72.212.222', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-23 18:04:47', '2026-09-23 18:04:47'),
(36, 1, 'post', 'login.store', 'POST', 'http://127.0.0.1:8000/login', '{\"login\":\"admin@example.com\"}', '103.187.94.76', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-23 21:23:42', '2026-09-23 21:23:42'),
(37, 1, 'post', 'login.store', 'POST', 'http://127.0.0.1:8000/login', '{\"login\":\"admin@example.com\"}', '103.72.212.222', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-24 12:32:24', '2026-09-24 12:32:24'),
(38, 1, 'post', 'login.store', 'POST', 'http://127.0.0.1:8000/login', '{\"login\":\"admin@example.com\",\"remember\":\"1\"}', '202.191.122.185', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 16:55:16', '2026-09-24 16:55:16'),
(39, 1, 'post', 'login.store', 'POST', 'http://127.0.0.1:8000/login', '{\"login\":\"admin@example.com\"}', '103.72.212.222', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 10:56:06', '2026-09-25 10:56:06'),
(40, 1, 'post', 'login.store', 'POST', 'http://127.0.0.1:8000/login', '{\"login\":\"admin@example.com\"}', '103.72.212.222', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-25 14:30:48', '2026-09-25 14:30:48'),
(41, 1, 'post', 'login.store', 'POST', 'http://127.0.0.1:8000/login', '{\"login\":\"admin@example.com\"}', '103.108.145.121', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-25 15:43:24', '2026-09-25 15:43:24'),
(42, 1, 'post', 'sales.store', 'POST', 'http://127.0.0.1:8000/sales', '{\"customer_id\":\"3\",\"warehouse_id\":\"4\",\"sale_date\":\"2026-09-25\",\"due_date\":\"2026-09-27\",\"items\":[{\"product_id\":\"20\",\"quantity\":\"10\",\"unit_price\":\"150\",\"discount\":\"0\",\"tax\":\"0\"},{\"product_id\":\"25\",\"quantity\":\"10\",\"unit_price\":\"225\",\"discount\":\"0\",\"tax\":\"0\"}],\"discount\":null,\"tax\":\"0\",\"shipping\":\"0\",\"notes\":null}', '103.108.145.121', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-25 15:47:08', '2026-09-25 15:47:08'),
(43, 1, 'put', 'sales.update', 'PUT', 'http://127.0.0.1:8000/sales/43', '{\"_method\":\"PUT\",\"customer_id\":\"3\",\"warehouse_id\":\"4\",\"sale_date\":\"2026-09-25\",\"due_date\":\"2026-09-27\",\"items\":[{\"product_id\":\"20\",\"quantity\":\"10\",\"unit_price\":\"150\",\"discount\":\"0\",\"tax\":\"0\"},{\"product_id\":\"25\",\"quantity\":\"10\",\"unit_price\":\"225\",\"discount\":\"0\",\"tax\":\"0\"}],\"discount\":\"0.00\",\"tax\":\"0.00\",\"shipping\":\"0.00\",\"notes\":null}', '103.108.145.121', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-25 15:47:31', '2026-09-25 15:47:31'),
(44, 1, 'post', 'sales.post', 'POST', 'http://127.0.0.1:8000/sales/43/post', '[]', '103.108.145.121', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-25 15:47:43', '2026-09-25 15:47:43'),
(45, 1, 'post', 'sale-returns.store', 'POST', 'http://127.0.0.1:8000/sale-returns', '{\"sale_id\":null,\"customer_id\":null,\"warehouse_id\":\"1\",\"return_date\":\"2026-09-25\",\"items\":[{\"product_id\":\"9\",\"quantity\":\"1\",\"unit_price\":\"285\"}],\"tax\":\"0\",\"reason\":null}', '103.108.145.121', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-25 15:49:24', '2026-09-25 15:49:24'),
(46, 1, 'post', 'payrolls.store', 'POST', 'http://127.0.0.1:8000/payrolls', '{\"period\":\"2026-09\",\"pay_date\":null,\"notes\":null}', '103.108.145.121', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-25 15:50:30', '2026-09-25 15:50:30'),
(47, 1, 'post', 'payrolls.store', 'POST', 'http://127.0.0.1:8000/payrolls', '{\"period\":\"2026-10\",\"pay_date\":\"2026-09-26\",\"notes\":null}', '103.108.145.121', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-25 15:50:50', '2026-09-25 15:50:50'),
(48, 1, 'post', 'brands.store', 'POST', 'http://127.0.0.1:8000/brands', '{\"name\":\"\\u09b6\\u09be\\u09b9 \\u09b8\\u09bf\\u09ae\\u09c7\\u09a8\\u09cd\\u099f\",\"code\":null,\"description\":null,\"is_active\":\"1\"}', '103.108.145.121', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-25 16:28:55', '2026-09-25 16:28:55'),
(49, 1, 'put', 'brands.update', 'PUT', 'http://127.0.0.1:8000/brands/11', '{\"_method\":\"PUT\",\"name\":\"\\u09b6\\u09be\\u09b9 \\u09b8\\u09bf\\u09ae\\u09c7\\u09a8\\u09cd\\u099f\",\"code\":\"CM-Shah\",\"description\":null,\"is_active\":\"1\"}', '103.108.145.121', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-25 16:29:25', '2026-09-25 16:29:25'),
(50, 1, 'post', 'categories.store', 'POST', 'http://127.0.0.1:8000/categories', '{\"name\":\"\\u09b8\\u09bf\\u09ae\\u09c7\\u09a8\\u09cd\\u099f\",\"code\":\"CM\",\"parent_id\":null,\"is_active\":\"1\",\"description\":null}', '103.108.145.121', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-25 16:30:49', '2026-09-25 16:30:49'),
(51, 1, 'post', 'products.store', 'POST', 'http://127.0.0.1:8000/products', '{\"name\":\"\\u09b6\\u09be\\u09b9 \\u09b8\\u09bf\\u09ae\\u09c7\\u09a8\\u09cd\\u099f\",\"sku\":\"0004\",\"barcode\":null,\"category_id\":\"7\",\"brand_id\":\"11\",\"unit_id\":\"8\",\"purchase_price\":\"480.00\",\"selling_price\":\"0500\",\"wholesale_price\":\"0500\",\"minimum_selling_price\":\"500\",\"tax_rate\":\"0\",\"reorder_level\":\"0\",\"minimum_stock_level\":\"0\",\"maximum_stock_level\":null,\"track_stock\":\"1\",\"allow_negative_stock\":\"1\",\"is_active\":\"1\",\"description\":null}', '103.108.145.121', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-25 16:34:58', '2026-09-25 16:34:58'),
(52, 1, 'post', 'categories.store', 'POST', 'http://127.0.0.1:8000/categories', '{\"name\":\"\\u09b0\\u09a1\",\"code\":\"Rd\",\"parent_id\":null,\"is_active\":\"1\",\"description\":null}', '103.108.145.121', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-25 16:37:27', '2026-09-25 16:37:27'),
(53, 1, 'post', 'brands.store', 'POST', 'http://127.0.0.1:8000/brands', '{\"name\":\"BSRM\",\"code\":\"BSRM\",\"description\":null,\"is_active\":\"1\"}', '103.108.145.121', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-25 16:38:21', '2026-09-25 16:38:21'),
(54, 1, 'post', 'products.store', 'POST', 'http://127.0.0.1:8000/products', '{\"name\":\"\\u09ac\\u09bf\\u098f\\u09b8\\u0986\\u09b0\\u098f\\u09ae \\u09e7\\u09e6 \\u09ae\\u09bf\\u09b2\\u09bf\",\"sku\":\"0008\",\"barcode\":null,\"category_id\":\"8\",\"brand_id\":\"12\",\"unit_id\":\"2\",\"purchase_price\":\"093\",\"selling_price\":\"094\",\"wholesale_price\":\"094\",\"minimum_selling_price\":\"094\",\"tax_rate\":\"0\",\"reorder_level\":\"0\",\"minimum_stock_level\":\"0100\",\"maximum_stock_level\":\"1000\",\"track_stock\":\"1\",\"allow_negative_stock\":\"1\",\"is_active\":\"1\",\"description\":null}', '103.108.145.121', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-25 16:40:01', '2026-09-25 16:40:01'),
(55, 1, 'post', 'products.store', 'POST', 'http://127.0.0.1:8000/products', '{\"name\":\"\\u09ac\\u09bf\\u098f\\u09b8\\u0986\\u09b0\\u098f\\u09ae \\u09e6\\u09ee \\u09ae\\u09bf\\u09b2\\u09bf\",\"sku\":\"0009\",\"barcode\":null,\"category_id\":\"8\",\"brand_id\":\"12\",\"unit_id\":\"2\",\"purchase_price\":\"093\",\"selling_price\":\"94\",\"wholesale_price\":\"94\",\"minimum_selling_price\":\"94\",\"tax_rate\":\"0\",\"reorder_level\":\"0\",\"minimum_stock_level\":\"100\",\"maximum_stock_level\":\"1000\",\"track_stock\":\"1\",\"allow_negative_stock\":\"1\",\"is_active\":\"1\",\"description\":null}', '103.108.145.121', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-25 16:41:59', '2026-09-25 16:41:59'),
(56, 1, 'post', 'products.store', 'POST', 'http://127.0.0.1:8000/products', '{\"name\":\"\\u09ac\\u09bf\\u098f\\u09b8\\u0986\\u09b0\\u098f\\u09ae \\u09e7\\u09e8 \\u09ae\\u09bf\\u09b2\\u09bf\",\"sku\":\"0010\",\"barcode\":null,\"category_id\":\"8\",\"brand_id\":\"12\",\"unit_id\":\"2\",\"purchase_price\":\"093\",\"selling_price\":\"94\",\"wholesale_price\":\"94\",\"minimum_selling_price\":\"94\",\"tax_rate\":\"0\",\"reorder_level\":\"0\",\"minimum_stock_level\":\"100\",\"maximum_stock_level\":\"1000\",\"track_stock\":\"1\",\"allow_negative_stock\":\"1\",\"is_active\":\"1\",\"description\":null}', '103.108.145.121', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-25 16:42:44', '2026-09-25 16:42:44'),
(57, 1, 'post', 'products.store', 'POST', 'http://127.0.0.1:8000/products', '{\"name\":\"\\u09ac\\u09bf\\u098f\\u09b8\\u0986\\u09b0\\u098f\\u09ae \\u09e7\\u09ec \\u09ae\\u09bf\\u09b2\\u09bf\",\"sku\":\"0010\",\"barcode\":null,\"category_id\":\"8\",\"brand_id\":\"12\",\"unit_id\":\"2\",\"purchase_price\":\"093\",\"selling_price\":\"94\",\"wholesale_price\":\"94\",\"minimum_selling_price\":\"94\",\"tax_rate\":\"0\",\"reorder_level\":\"0\",\"minimum_stock_level\":\"100\",\"maximum_stock_level\":\"1000\",\"track_stock\":\"1\",\"allow_negative_stock\":\"1\",\"is_active\":\"1\",\"description\":null}', '103.108.145.121', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-25 16:42:59', '2026-09-25 16:42:59'),
(58, 1, 'post', 'products.store', 'POST', 'http://127.0.0.1:8000/products', '{\"name\":\"\\u09ac\\u09bf\\u098f\\u09b8\\u0986\\u09b0\\u098f\\u09ae \\u09e7\\u09ec \\u09ae\\u09bf\\u09b2\\u09bf\",\"sku\":\"0011\",\"barcode\":null,\"category_id\":\"8\",\"brand_id\":\"12\",\"unit_id\":\"2\",\"purchase_price\":\"093\",\"selling_price\":\"94\",\"wholesale_price\":\"94\",\"minimum_selling_price\":\"94\",\"tax_rate\":\"0\",\"reorder_level\":\"0\",\"minimum_stock_level\":\"100\",\"maximum_stock_level\":\"1000\",\"track_stock\":\"1\",\"allow_negative_stock\":\"1\",\"is_active\":\"1\",\"description\":null}', '103.108.145.121', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-25 16:43:08', '2026-09-25 16:43:08'),
(59, 1, 'post', 'products.store', 'POST', 'http://127.0.0.1:8000/products', '{\"name\":\"\\u09ac\\u09bf\\u098f\\u09b8\\u0986\\u09b0\\u098f\\u09ae \\u09e8\\u09e6 \\u09ae\\u09bf\\u09b2\\u09bf\",\"sku\":\"0012\",\"barcode\":null,\"category_id\":\"8\",\"brand_id\":\"12\",\"unit_id\":\"2\",\"purchase_price\":\"093\",\"selling_price\":\"94\",\"wholesale_price\":\"94\",\"minimum_selling_price\":\"94\",\"tax_rate\":\"0\",\"reorder_level\":\"0\",\"minimum_stock_level\":\"100\",\"maximum_stock_level\":\"1000\",\"track_stock\":\"1\",\"allow_negative_stock\":\"1\",\"is_active\":\"1\",\"description\":null}', '103.108.145.121', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-25 16:43:37', '2026-09-25 16:43:37'),
(60, 1, 'post', 'login.store', 'POST', 'http://127.0.0.1:8000/login', '{\"login\":\"admin@example.com\",\"remember\":\"1\"}', '103.115.116.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0', '2026-09-26 18:45:52', '2026-09-26 18:45:52'),
(61, 1, 'put', 'supplier-groups.update', 'PUT', 'http://127.0.0.1:8000/supplier-groups/2', '{\"_method\":\"PUT\",\"name\":\"Distributor\",\"code\":\"SG-DIS\",\"description\":\"Authorized distributors\",\"is_active\":\"1\"}', '103.115.116.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0', '2026-09-26 18:56:04', '2026-09-26 18:56:04'),
(62, 1, 'post', 'customer-payments.store', 'POST', 'http://127.0.0.1:8000/customer-payments', '{\"customer_id\":\"5\",\"sale_id\":null,\"account_id\":\"2\",\"payment_date\":\"2026-09-25\",\"amount\":\"10000\",\"method\":\"cheque\",\"reference\":\"555\",\"notes\":null}', '103.115.116.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0', '2026-09-26 19:04:07', '2026-09-26 19:04:07'),
(63, 1, 'post', 'quotations.store', 'POST', 'http://127.0.0.1:8000/quotations', '{\"customer_id\":\"9\",\"quotation_date\":\"2026-09-25\",\"valid_until\":\"2026-10-12\",\"items\":[{\"product_id\":\"483\",\"quantity\":\"1\",\"unit_price\":\"500\",\"discount\":\"0\",\"tax\":\"0\"}],\"discount\":null,\"tax\":\"0\",\"notes\":null}', '103.115.116.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0', '2026-09-26 19:07:58', '2026-09-26 19:07:58'),
(64, 1, 'patch', 'quotations.status', 'PATCH', 'http://127.0.0.1:8000/quotations/3/status', '{\"_method\":\"PATCH\",\"status\":\"rejected\"}', '103.115.116.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0', '2026-09-26 19:10:34', '2026-09-26 19:10:34'),
(65, 1, 'patch', 'quotations.status', 'PATCH', 'http://127.0.0.1:8000/quotations/3/status', '{\"_method\":\"PATCH\",\"status\":\"expired\"}', '103.115.116.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0', '2026-09-26 19:10:38', '2026-09-26 19:10:38'),
(66, 1, 'post', 'sale-returns.store', 'POST', 'http://127.0.0.1:8000/sale-returns', '{\"sale_id\":\"1\",\"customer_id\":\"4\",\"warehouse_id\":\"2\",\"return_date\":\"2026-09-25\",\"items\":[{\"product_id\":\"6\",\"quantity\":\"1.0001\",\"unit_price\":\"240\"}],\"tax\":\"0\",\"reason\":null}', '103.115.116.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0', '2026-09-26 19:19:22', '2026-09-26 19:19:22'),
(67, 1, 'post', 'sale-returns.post', 'POST', 'http://127.0.0.1:8000/sale-returns/3/post', '[]', '103.115.116.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0', '2026-09-26 19:20:21', '2026-09-26 19:20:21'),
(68, 1, 'post', 'sales.store', 'POST', 'http://127.0.0.1:8000/sales', '{\"customer_id\":\"8\",\"warehouse_id\":\"4\",\"sale_date\":\"2026-09-26\",\"due_date\":null,\"items\":[{\"product_id\":\"2\",\"quantity\":\"1\",\"unit_price\":\"180\",\"discount\":\"0\",\"tax\":\"0\"}],\"discount\":\"0.03\",\"tax\":\"0.03\",\"shipping\":\"0.05\",\"notes\":null}', '103.115.116.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0', '2026-09-26 19:24:32', '2026-09-26 19:24:32'),
(69, 1, 'post', 'login.store', 'POST', 'http://127.0.0.1:8000/login', '{\"login\":\"admin@example.com\"}', '37.111.212.75', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Mobile Safari/537.36', '2026-09-27 18:06:42', '2026-09-27 18:06:42'),
(70, 1, 'post', 'login.store', 'POST', 'http://127.0.0.1:8000/login', '{\"login\":\"admin@example.com\"}', '103.72.212.222', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 11:06:06', '2026-09-28 11:06:06'),
(71, 1, 'post', 'users.store', 'POST', 'http://127.0.0.1:8000/users', '{\"name\":\"Seller\",\"username\":\"seller@example.com\",\"email\":\"seller@seller.com\",\"phone\":null,\"status\":\"active\",\"roles\":[\"Seller\"]}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 01:03:31', '2026-09-28 01:03:31'),
(72, NULL, 'post', 'login.store', 'POST', 'http://127.0.0.1:8000/login', '{\"login\":\"seller\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 01:16:43', '2026-09-28 01:16:43'),
(73, 1, 'post', 'login.store', 'POST', 'http://127.0.0.1:8000/login', '{\"login\":\"admin@example.com\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 01:50:21', '2026-09-28 01:50:21'),
(74, NULL, 'post', 'login.store', 'POST', 'http://127.0.0.1:8000/login', '{\"login\":\"seller\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 02:43:44', '2026-09-28 02:43:44'),
(75, 1, 'post', 'login.store', 'POST', 'http://127.0.0.1:8000/login', '{\"login\":\"admin@example.com\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 02:45:06', '2026-09-28 02:45:06'),
(76, NULL, 'post', 'login.store', 'POST', 'http://127.0.0.1:8000/login', '{\"login\":\"seller\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 02:49:25', '2026-09-28 02:49:25'),
(77, 1, 'post', 'login.store', 'POST', 'http://127.0.0.1:8000/login', '{\"login\":\"admin@example.com\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 02:59:58', '2026-09-28 02:59:58'),
(78, NULL, 'post', 'login.store', 'POST', 'http://127.0.0.1:8000/login', '{\"login\":\"seller\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 03:02:14', '2026-09-28 03:02:14'),
(79, NULL, 'post', 'sales.store', 'POST', 'http://127.0.0.1:8000/sales', '{\"customer_id\":null,\"warehouse_id\":\"1\",\"sale_date\":\"2026-09-28\",\"due_date\":null,\"items\":[{\"product_id\":\"2\",\"quantity\":\"1\",\"bundle_quantity\":null,\"unit_price\":\"180\",\"discount\":\"0\",\"tax\":\"0\"}],\"discount\":\"0\",\"tax\":\"0\",\"shipping\":\"0\",\"labor_charge\":\"0\",\"notes\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 04:49:10', '2026-09-28 04:49:10'),
(80, 1, 'post', 'login.store', 'POST', 'http://127.0.0.1:8000/login', '{\"login\":\"admin@example.com\"}', '103.72.212.222', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 16:02:28', '2026-09-28 16:02:28'),
(81, 1, 'post', 'login.store', 'POST', 'http://127.0.0.1:8000/login', '{\"login\":\"admin@example.com\"}', '103.72.212.222', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 16:03:08', '2026-09-28 16:03:08'),
(82, 1, 'post', 'users.store', 'POST', 'http://127.0.0.1:8000/users', '{\"name\":\"Seller\",\"username\":\"seller\",\"email\":\"seller@seller.com\",\"phone\":null,\"status\":\"active\",\"roles\":[\"Seller\"]}', '103.72.212.222', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 16:03:53', '2026-09-28 16:03:53'),
(83, 3, 'post', 'login.store', 'POST', 'http://127.0.0.1:8000/login', '{\"login\":\"seller\"}', '103.72.212.222', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 16:05:04', '2026-09-28 16:05:04'),
(84, 3, 'post', 'sales.store', 'POST', 'http://127.0.0.1:8000/sales', '{\"customer_id\":null,\"warehouse_id\":\"1\",\"sale_date\":\"2026-09-28\",\"due_date\":null,\"items\":[{\"product_id\":\"13\",\"quantity\":\"1\",\"bundle_quantity\":null,\"unit_price\":\"345\",\"discount\":\"0\",\"tax\":\"0\"}],\"discount\":\"0\",\"tax\":\"0\",\"shipping\":\"0\",\"labor_charge\":\"0\",\"notes\":null}', '103.72.212.222', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 16:05:21', '2026-09-28 16:05:21'),
(85, 3, 'post', 'sales.post', 'POST', 'http://127.0.0.1:8000/sales/46/post', '[]', '103.72.212.222', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 16:05:26', '2026-09-28 16:05:26'),
(86, 3, 'post', 'login.store', 'POST', 'http://127.0.0.1:8000/login', '{\"login\":\"seller\"}', '103.72.212.222', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 16:38:51', '2026-09-28 16:38:51'),
(87, 3, 'post', 'login.store', 'POST', 'http://127.0.0.1:8000/login', '{\"login\":\"seller\"}', '103.108.145.121', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36 Edg/154.0.0.0', '2026-09-28 16:45:05', '2026-09-28 16:45:05'),
(88, 3, 'post', 'sales.store', 'POST', 'http://127.0.0.1:8000/sales', '{\"customer_id\":null,\"warehouse_id\":\"1\",\"sale_date\":\"2026-09-28\",\"due_date\":null,\"items\":[{\"product_id\":\"13\",\"quantity\":\"10\",\"bundle_quantity\":\"0.0001\",\"unit_price\":\"345\",\"discount\":\"2\",\"tax\":\"0\"},{\"product_id\":\"2\",\"quantity\":\"20\",\"bundle_quantity\":null,\"unit_price\":\"180\",\"discount\":\"03\",\"tax\":\"0\"}],\"discount\":\"10\",\"tax\":\"15\",\"shipping\":\"020\",\"labor_charge\":\"010\",\"notes\":\"done\"}', '103.108.145.121', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36 Edg/154.0.0.0', '2026-09-28 16:54:52', '2026-09-28 16:54:52'),
(89, 3, 'post', 'sales.store', 'POST', 'http://127.0.0.1:8000/sales', '{\"customer_id\":null,\"warehouse_id\":\"1\",\"sale_date\":\"2026-09-28\",\"due_date\":null,\"items\":[{\"product_id\":\"13\",\"quantity\":\"11\",\"bundle_quantity\":\"0.0001\",\"unit_price\":\"345\",\"discount\":\"2\",\"tax\":\"0\"},{\"product_id\":\"2\",\"quantity\":\"20\",\"bundle_quantity\":null,\"unit_price\":\"180\",\"discount\":\"03\",\"tax\":\"0\"}],\"discount\":\"10\",\"tax\":\"15\",\"shipping\":\"020\",\"labor_charge\":\"010\",\"notes\":\"done\"}', '103.108.145.121', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36 Edg/154.0.0.0', '2026-09-28 16:55:44', '2026-09-28 16:55:44'),
(90, 3, 'post', 'sales.store', 'POST', 'http://127.0.0.1:8000/sales', '{\"customer_id\":null,\"warehouse_id\":\"1\",\"sale_date\":\"2026-09-28\",\"due_date\":null,\"items\":[{\"product_id\":\"13\",\"quantity\":\"11\",\"bundle_quantity\":\"0.0001\",\"unit_price\":\"345\",\"discount\":\"2\",\"tax\":\"0\"},{\"product_id\":\"2\",\"quantity\":\"20\",\"bundle_quantity\":null,\"unit_price\":\"180\",\"discount\":\"03\",\"tax\":\"0\"}],\"discount\":\"10\",\"tax\":\"15\",\"shipping\":\"020\",\"labor_charge\":\"010\",\"notes\":\"done\"}', '103.108.145.121', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36 Edg/154.0.0.0', '2026-09-28 16:56:00', '2026-09-28 16:56:00'),
(91, 3, 'post', 'sales.store', 'POST', 'http://127.0.0.1:8000/sales', '{\"customer_id\":null,\"warehouse_id\":\"1\",\"sale_date\":\"2026-09-28\",\"due_date\":null,\"items\":[{\"product_id\":\"13\",\"quantity\":\"11\",\"bundle_quantity\":\"0.0001\",\"unit_price\":\"345\",\"discount\":\"2\",\"tax\":\"0\"},{\"product_id\":\"2\",\"quantity\":\"20\",\"bundle_quantity\":null,\"unit_price\":\"180\",\"discount\":\"03\",\"tax\":\"0\"}],\"discount\":\"10\",\"tax\":\"15\",\"shipping\":\"020\",\"labor_charge\":\"010\",\"notes\":\"done\"}', '103.108.145.121', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36 Edg/154.0.0.0', '2026-09-28 16:56:10', '2026-09-28 16:56:10'),
(92, 3, 'post', 'sales.post', 'POST', 'http://127.0.0.1:8000/sales/50/post', '[]', '103.108.145.121', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36 Edg/154.0.0.0', '2026-09-28 16:56:45', '2026-09-28 16:56:45'),
(93, 3, 'post', 'sales.store', 'POST', 'http://127.0.0.1:8000/sales', '{\"customer_id\":null,\"warehouse_id\":\"1\",\"sale_date\":\"2026-09-28\",\"due_date\":null,\"items\":[{\"product_id\":\"13\",\"quantity\":\"11\",\"bundle_quantity\":\"0.0001\",\"unit_price\":\"345\",\"discount\":\"2\",\"tax\":\"0\"},{\"product_id\":\"2\",\"quantity\":\"5\",\"bundle_quantity\":null,\"unit_price\":\"180\",\"discount\":\"03\",\"tax\":\"0\"}],\"discount\":\"10\",\"tax\":\"15\",\"shipping\":\"020\",\"labor_charge\":\"010\",\"notes\":\"done\"}', '103.108.145.121', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36 Edg/154.0.0.0', '2026-09-28 16:57:35', '2026-09-28 16:57:35'),
(94, 3, 'post', 'sales.post', 'POST', 'http://127.0.0.1:8000/sales/51/post', '[]', '103.108.145.121', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36 Edg/154.0.0.0', '2026-09-28 16:57:39', '2026-09-28 16:57:39'),
(95, 3, 'post', 'sales.store', 'POST', 'http://127.0.0.1:8000/sales', '{\"customer_id\":null,\"warehouse_id\":\"1\",\"sale_date\":\"2026-09-28\",\"due_date\":null,\"items\":[{\"product_id\":\"13\",\"quantity\":\"2\",\"bundle_quantity\":\"0.0001\",\"unit_price\":\"345\",\"discount\":\"2\",\"tax\":\"0\"},{\"product_id\":\"2\",\"quantity\":\"1\",\"bundle_quantity\":null,\"unit_price\":\"180\",\"discount\":\"03\",\"tax\":\"0\"}],\"discount\":\"10\",\"tax\":\"15\",\"shipping\":\"020\",\"labor_charge\":\"010\",\"notes\":\"done\"}', '103.108.145.121', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36 Edg/154.0.0.0', '2026-09-28 16:58:01', '2026-09-28 16:58:01'),
(96, 3, 'post', 'sales.post', 'POST', 'http://127.0.0.1:8000/sales/52/post', '[]', '103.108.145.121', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36 Edg/154.0.0.0', '2026-09-28 16:58:05', '2026-09-28 16:58:05'),
(97, 3, 'post', 'sales.store', 'POST', 'http://127.0.0.1:8000/sales', '{\"customer_id\":null,\"warehouse_id\":\"1\",\"sale_date\":\"2026-09-28\",\"due_date\":null,\"items\":[{\"product_id\":\"12\",\"quantity\":\"1\",\"bundle_quantity\":\"0.0001\",\"unit_price\":\"330\",\"discount\":\"2\",\"tax\":\"0\"},{\"product_id\":\"2\",\"quantity\":\"1\",\"bundle_quantity\":null,\"unit_price\":\"180\",\"discount\":\"03\",\"tax\":\"0\"}],\"discount\":\"10\",\"tax\":\"15\",\"shipping\":\"020\",\"labor_charge\":\"010\",\"notes\":\"done\"}', '103.108.145.121', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36 Edg/154.0.0.0', '2026-09-28 16:59:28', '2026-09-28 16:59:28'),
(98, 3, 'post', 'sales.post', 'POST', 'http://127.0.0.1:8000/sales/53/post', '[]', '103.108.145.121', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36 Edg/154.0.0.0', '2026-09-28 16:59:32', '2026-09-28 16:59:32'),
(99, 3, 'post', 'sales.store', 'POST', 'http://127.0.0.1:8000/sales', '{\"customer_id\":null,\"warehouse_id\":\"1\",\"sale_date\":\"2026-09-28\",\"due_date\":null,\"items\":[{\"product_id\":\"12\",\"quantity\":\"1\",\"bundle_quantity\":\"0.0001\",\"unit_price\":\"330\",\"discount\":\"2\",\"tax\":\"0\"},{\"product_id\":\"2\",\"quantity\":\"1\",\"bundle_quantity\":null,\"unit_price\":\"180\",\"discount\":\"03\",\"tax\":\"0\"}],\"discount\":\"10\",\"tax\":\"15\",\"shipping\":\"020\",\"labor_charge\":\"010\",\"notes\":\"done\"}', '103.108.145.121', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36 Edg/154.0.0.0', '2026-09-28 17:00:16', '2026-09-28 17:00:16'),
(100, 3, 'post', 'sales.store', 'POST', 'http://127.0.0.1:8000/sales', '{\"customer_id\":null,\"warehouse_id\":\"1\",\"sale_date\":\"2026-09-28\",\"due_date\":null,\"items\":[{\"product_id\":\"12\",\"quantity\":\"1\",\"bundle_quantity\":\"0.0001\",\"unit_price\":\"330\",\"discount\":\"2\",\"tax\":\"0\"},{\"product_id\":\"2\",\"quantity\":\"1\",\"bundle_quantity\":null,\"unit_price\":\"180\",\"discount\":\"03\",\"tax\":\"0\"}],\"discount\":\"10\",\"tax\":\"15\",\"shipping\":\"020\",\"labor_charge\":\"010\",\"notes\":\"done\"}', '103.108.145.121', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36 Edg/154.0.0.0', '2026-09-28 17:04:27', '2026-09-28 17:04:27'),
(101, 3, 'post', 'sales.store', 'POST', 'http://127.0.0.1:8000/sales', '{\"customer_id\":null,\"warehouse_id\":\"1\",\"sale_date\":\"2026-09-28\",\"due_date\":null,\"items\":[{\"product_id\":\"12\",\"quantity\":\"1\",\"bundle_quantity\":\"0.0001\",\"unit_price\":\"330\",\"discount\":\"2\",\"tax\":\"0\"},{\"product_id\":\"2\",\"quantity\":\"1\",\"bundle_quantity\":null,\"unit_price\":\"180\",\"discount\":\"03\",\"tax\":\"0\"}],\"discount\":\"10\",\"tax\":\"15\",\"shipping\":\"020\",\"labor_charge\":\"010\",\"notes\":\"done\"}', '103.108.145.121', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36 Edg/154.0.0.0', '2026-09-28 17:04:27', '2026-09-28 17:04:27'),
(102, 3, 'post', 'sales.store', 'POST', 'http://127.0.0.1:8000/sales', '{\"customer_id\":\"5\",\"warehouse_id\":\"1\",\"sale_date\":\"2026-09-28\",\"due_date\":null,\"items\":[{\"product_id\":\"20\",\"quantity\":\"1\",\"bundle_quantity\":\"0.0001\",\"unit_price\":\"150\",\"discount\":\"02\",\"tax\":\"0\"}],\"discount\":\"02\",\"tax\":\"02\",\"shipping\":\"02\",\"labor_charge\":\"2\",\"notes\":\"done\"}', '103.108.145.121', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36 Edg/154.0.0.0', '2026-09-28 17:09:44', '2026-09-28 17:09:44'),
(103, 3, 'post', 'sales.post', 'POST', 'http://127.0.0.1:8000/sales/57/post', '[]', '103.108.145.121', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36 Edg/154.0.0.0', '2026-09-28 17:09:49', '2026-09-28 17:09:49'),
(104, 3, 'post', 'customer-payments.store', 'POST', 'http://127.0.0.1:8000/customer-payments', '{\"customer_id\":\"5\",\"sale_id\":\"57\",\"account_id\":\"2\",\"payment_date\":\"2026-09-28\",\"amount\":\"100\",\"method\":\"bank\",\"reference\":\"01\",\"notes\":\"Notes\"}', '103.108.145.121', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36 Edg/154.0.0.0', '2026-09-28 17:10:37', '2026-09-28 17:10:37'),
(105, 3, 'post', 'customer-payments.store', 'POST', 'http://127.0.0.1:8000/customer-payments', '{\"customer_id\":\"5\",\"sale_id\":\"57\",\"account_id\":\"2\",\"payment_date\":\"2026-09-28\",\"amount\":\"5000\",\"method\":\"bank\",\"reference\":null,\"notes\":null}', '103.72.212.222', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 17:32:11', '2026-09-28 17:32:11'),
(106, 3, 'post', 'customer-payments.store', 'POST', 'http://127.0.0.1:8000/customer-payments', '{\"customer_id\":\"5\",\"sale_id\":\"57\",\"account_id\":\"2\",\"payment_date\":\"2026-09-28\",\"amount\":\"5000\",\"method\":\"bank\",\"reference\":null,\"notes\":null}', '103.72.212.222', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 17:32:23', '2026-09-28 17:32:23'),
(107, 3, 'post', 'sales.post', 'POST', 'http://127.0.0.1:8000/sales/56/post', '[]', '103.72.212.222', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 17:46:38', '2026-09-28 17:46:38'),
(108, 3, 'post', 'customer-payments.store', 'POST', 'http://127.0.0.1:8000/customer-payments', '{\"customer_id\":\"5\",\"sale_id\":\"57\",\"account_id\":\"1\",\"payment_date\":\"2026-09-28\",\"amount\":\"52\",\"method\":\"cash\",\"reference\":null,\"notes\":null}', '103.72.212.222', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 17:49:03', '2026-09-28 17:49:03'),
(109, 1, 'post', 'login.store', 'POST', 'http://127.0.0.1:8000/login', '{\"login\":\"admin@example.com\"}', '103.72.212.222', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 17:49:56', '2026-09-28 17:49:56'),
(110, 3, 'post', 'login.store', 'POST', 'http://127.0.0.1:8000/login', '{\"login\":\"seller\"}', '103.187.94.70', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-28 20:27:43', '2026-09-28 20:27:43'),
(111, 3, 'post', 'login.store', 'POST', 'http://127.0.0.1:8000/login', '{\"login\":\"seller\"}', '103.115.116.85', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-28 20:30:09', '2026-09-28 20:30:09'),
(112, 3, 'post', 'sales.store', 'POST', 'http://127.0.0.1:8000/sales', '{\"customer_id\":null,\"warehouse_id\":\"1\",\"sale_date\":\"2026-09-28\",\"due_date\":null,\"items\":[{\"product_id\":\"13\",\"quantity\":\"1\",\"bundle_quantity\":\"2\",\"unit_price\":\"345\",\"discount\":\"0\",\"tax\":\"0\"}],\"discount\":\"03\",\"tax\":\"02\",\"shipping\":\"01\",\"labor_charge\":\"05\",\"notes\":null}', '103.115.116.85', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Mobile Safari/537.36', '2026-09-28 20:35:19', '2026-09-28 20:35:19'),
(113, 3, 'post', 'sales.store', 'POST', 'http://127.0.0.1:8000/sales', '{\"customer_id\":\"5\",\"warehouse_id\":\"2\",\"sale_date\":\"2026-09-28\",\"due_date\":\"2026-09-21\",\"items\":[{\"product_id\":\"2\",\"quantity\":\"1\",\"bundle_quantity\":null,\"unit_price\":\"180\",\"discount\":\"0\",\"tax\":\"0\"}],\"discount\":\"20\",\"tax\":\"10\",\"shipping\":\"100\",\"labor_charge\":\"50\",\"notes\":null}', '103.115.116.85', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Mobile Safari/537.36', '2026-09-28 20:40:30', '2026-09-28 20:40:30'),
(114, 3, 'post', 'sales.store', 'POST', 'http://127.0.0.1:8000/sales', '{\"customer_id\":\"10\",\"warehouse_id\":\"2\",\"sale_date\":\"2026-09-28\",\"due_date\":\"2026-09-15\",\"items\":[{\"product_id\":\"18\",\"quantity\":\"10\",\"bundle_quantity\":\"10\",\"unit_price\":\"5000\",\"discount\":\"0\",\"tax\":\"0\"},{\"product_id\":\"18\",\"quantity\":\"1\",\"bundle_quantity\":null,\"unit_price\":\"420\",\"discount\":\"0\",\"tax\":\"0\"}],\"discount\":\"0\",\"tax\":\"0\",\"shipping\":\"200\",\"labor_charge\":\"100\",\"notes\":null}', '103.115.116.85', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-28 20:49:56', '2026-09-28 20:49:56'),
(115, 3, 'post', 'sales.store', 'POST', 'http://127.0.0.1:8000/sales', '{\"customer_id\":\"10\",\"warehouse_id\":\"2\",\"sale_date\":\"2026-09-28\",\"due_date\":\"2026-09-15\",\"items\":[{\"product_id\":\"18\",\"quantity\":\"10\",\"bundle_quantity\":\"10\",\"unit_price\":\"5000\",\"discount\":\"0\",\"tax\":\"0\"},{\"product_id\":\"18\",\"quantity\":\"1\",\"bundle_quantity\":null,\"unit_price\":\"420\",\"discount\":\"0\",\"tax\":\"0\"},{\"product_id\":\"20\",\"quantity\":\"1\",\"bundle_quantity\":null,\"unit_price\":\"150\",\"discount\":\"0\",\"tax\":\"0\"}],\"discount\":\"0\",\"tax\":\"0\",\"shipping\":\"200\",\"labor_charge\":\"100\",\"notes\":null}', '103.115.116.85', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-28 20:51:15', '2026-09-28 20:51:15'),
(116, 3, 'post', 'sales.store', 'POST', 'http://127.0.0.1:8000/sales', '{\"customer_id\":\"10\",\"warehouse_id\":\"2\",\"sale_date\":\"2026-09-28\",\"due_date\":\"2026-09-15\",\"items\":[{\"product_id\":\"18\",\"quantity\":\"10\",\"bundle_quantity\":\"10\",\"unit_price\":\"5000\",\"discount\":\"20\",\"tax\":\"10\"},{\"product_id\":\"18\",\"quantity\":\"1\",\"bundle_quantity\":null,\"unit_price\":\"420\",\"discount\":\"0\",\"tax\":\"0\"},{\"product_id\":\"20\",\"quantity\":\"1\",\"bundle_quantity\":null,\"unit_price\":\"150\",\"discount\":\"0\",\"tax\":\"0\"},{\"product_id\":\"2\",\"quantity\":\"1\",\"bundle_quantity\":null,\"unit_price\":\"180\",\"discount\":\"0\",\"tax\":\"0\"}],\"discount\":\"0\",\"tax\":\"0\",\"shipping\":\"200\",\"labor_charge\":\"100\",\"notes\":null}', '103.115.116.85', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-28 20:52:35', '2026-09-28 20:52:35'),
(117, 3, 'post', 'sales.post', 'POST', 'http://127.0.0.1:8000/sales/59/post', '[]', '37.111.213.95', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-28 21:19:22', '2026-09-28 21:19:22'),
(118, 3, 'post', 'sales.post', 'POST', 'http://127.0.0.1:8000/sales/59/post', '[]', '37.111.213.95', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-28 21:32:32', '2026-09-28 21:32:32'),
(119, 3, 'post', 'sales.post', 'POST', 'http://127.0.0.1:8000/sales/59/post', '[]', '103.115.116.85', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-28 21:33:33', '2026-09-28 21:33:33'),
(120, 3, 'post', 'sales.store', 'POST', 'http://127.0.0.1:8000/sales', '{\"customer_id\":\"17\",\"warehouse_id\":\"4\",\"sale_date\":\"2026-09-28\",\"due_date\":null,\"items\":[{\"product_id\":\"30\",\"quantity\":\"100\",\"bundle_quantity\":\"10\",\"unit_price\":\"300\",\"discount\":\"0\",\"tax\":\"0\"},{\"product_id\":\"9\",\"quantity\":\"20\",\"bundle_quantity\":\"20\",\"unit_price\":\"285\",\"discount\":\"0\",\"tax\":\"0\"}],\"discount\":\"0\",\"tax\":\"0\",\"shipping\":\"0\",\"labor_charge\":\"0\",\"notes\":null}', '103.115.116.85', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-28 21:38:29', '2026-09-28 21:38:29'),
(121, 1, 'post', 'login.store', 'POST', 'http://127.0.0.1:8000/login', '{\"login\":\"admin@example.com\",\"remember\":\"1\"}', '27.147.205.10', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-29 10:50:54', '2026-09-29 10:50:54'),
(122, 1, 'post', 'payrolls.store', 'POST', 'http://127.0.0.1:8000/payrolls', '{\"period\":\"2026-09\",\"pay_date\":null,\"notes\":null}', '27.147.205.10', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-29 10:53:35', '2026-09-29 10:53:35'),
(123, 1, 'post', 'login.store', 'POST', 'http://127.0.0.1:8000/login', '{\"login\":\"admin@example.com\"}', '103.72.212.222', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-29 10:53:43', '2026-09-29 10:53:43');
INSERT INTO `activity_logs` (`id`, `user_id`, `action`, `route_name`, `method`, `url`, `payload`, `ip_address`, `user_agent`, `created_at`, `updated_at`) VALUES
(124, 3, 'post', 'login.store', 'POST', 'http://127.0.0.1:8000/login', '{\"login\":\"seller\"}', '103.72.212.222', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-29 11:25:56', '2026-09-29 11:25:56'),
(125, 3, 'post', 'sales.store', 'POST', 'http://127.0.0.1:8000/sales', '{\"customer_id\":\"5\",\"warehouse_id\":\"1\",\"sale_date\":\"2026-09-29\",\"due_date\":null,\"items\":[{\"product_id\":\"13\",\"quantity\":\"1\",\"bundle_quantity\":null,\"unit_price\":\"345\",\"discount\":\"0\",\"tax\":\"0\"}],\"discount\":\"0\",\"tax\":\"0\",\"shipping\":\"0\",\"labor_charge\":\"0\",\"notes\":null}', '103.72.212.222', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-29 14:03:10', '2026-09-29 14:03:10'),
(126, 3, 'post', 'sales.post', 'POST', 'http://127.0.0.1:8000/sales/61/post', '[]', '103.72.212.222', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-29 14:03:17', '2026-09-29 14:03:17'),
(127, 3, 'post', 'customer-payments.store', 'POST', 'http://127.0.0.1:8000/customer-payments', '{\"customer_id\":\"5\",\"sale_id\":\"61\",\"payment_date\":\"2026-09-29\",\"amount\":\"200\",\"method\":\"bank\",\"reference\":null,\"notes\":null}', '103.72.212.222', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-29 14:04:11', '2026-09-29 14:04:11'),
(128, 3, 'post', 'customer-payments.store', 'POST', 'http://127.0.0.1:8000/customer-payments', '{\"customer_id\":\"5\",\"sale_id\":\"61\",\"payment_date\":\"2026-09-29\",\"amount\":\"5000\",\"method\":\"cash\",\"reference\":null,\"notes\":null}', '103.72.212.222', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-29 14:04:42', '2026-09-29 14:04:42'),
(129, 1, 'post', 'login.store', 'POST', 'http://127.0.0.1:8000/login', '{\"login\":\"admin@example.com\"}', '103.72.212.222', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-30 16:57:22', '2026-09-30 16:57:22'),
(130, 1, 'post', 'login.store', 'POST', 'http://127.0.0.1:8000/login', '{\"login\":\"admin@example.com\"}', '103.72.212.222', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-01 10:35:37', '2026-10-01 10:35:37'),
(131, 1, 'post', 'payrolls.store', 'POST', 'http://127.0.0.1:8000/payrolls', '{\"period\":\"2026-10\",\"pay_date\":null,\"notes\":null}', '103.72.212.222', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-01 11:07:33', '2026-10-01 11:07:33'),
(132, 1, 'post', 'login.store', 'POST', 'http://127.0.0.1:8000/login', '{\"login\":\"admin@example.com\"}', '103.72.212.222', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-01 16:24:06', '2026-10-01 16:24:06'),
(133, 1, 'post', 'payrolls.store', 'POST', 'http://127.0.0.1:8000/payrolls', '{\"period\":\"2026-10\",\"pay_date\":null,\"notes\":null}', '103.72.212.222', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-01 17:02:51', '2026-10-01 17:02:51'),
(134, 1, 'post', 'payrolls.store', 'POST', 'http://127.0.0.1:8000/payrolls', '{\"period\":\"2026-10\",\"pay_date\":null,\"notes\":null}', '103.72.212.222', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-01 17:02:55', '2026-10-01 17:02:55'),
(135, 1, 'post', 'payrolls.store', 'POST', 'http://127.0.0.1:8000/payrolls', '{\"period\":\"2026-10\",\"pay_date\":null,\"notes\":null}', '103.72.212.222', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-01 17:03:03', '2026-10-01 17:03:03'),
(136, 1, 'post', 'payrolls.store', 'POST', 'http://127.0.0.1:8000/payrolls', '{\"period\":\"2026-10\",\"pay_date\":null,\"notes\":null}', '103.72.212.222', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-01 17:26:28', '2026-10-01 17:26:28'),
(137, 1, 'post', 'payrolls.store', 'POST', 'http://127.0.0.1:8000/payrolls', '{\"period\":\"2026-10\",\"pay_date\":null,\"notes\":null}', '103.72.212.222', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-01 17:26:31', '2026-10-01 17:26:31'),
(138, 1, 'post', 'payrolls.store', 'POST', 'http://127.0.0.1:8000/payrolls', '{\"period\":\"2026-10\",\"pay_date\":null,\"notes\":null}', '103.72.212.222', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-01 17:27:33', '2026-10-01 17:27:33'),
(139, 1, 'post', 'payrolls.store', 'POST', 'http://127.0.0.1:8000/payrolls', '{\"period\":\"2026-10\",\"pay_date\":null,\"notes\":null}', '103.72.212.222', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-01 18:06:39', '2026-10-01 18:06:39'),
(140, 1, 'post', 'payrolls.store', 'POST', 'http://127.0.0.1:8000/payrolls', '{\"period\":\"2026-10\",\"pay_date\":null,\"notes\":null}', '103.72.212.222', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-01 18:06:44', '2026-10-01 18:06:44'),
(141, 1, 'post', 'payrolls.store', 'POST', 'http://127.0.0.1:8000/payrolls', '{\"period\":\"2026-10\",\"pay_date\":null,\"notes\":null}', '103.72.212.222', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-01 18:06:51', '2026-10-01 18:06:51'),
(142, 1, 'post', 'payrolls.store', 'POST', 'http://127.0.0.1:8000/payrolls', '{\"period\":\"2026-10\",\"pay_date\":null,\"notes\":null}', '103.72.212.222', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-01 18:07:22', '2026-10-01 18:07:22'),
(143, 1, 'post', 'payrolls.store', 'POST', 'http://127.0.0.1:8000/payrolls', '{\"period\":\"2026-10\",\"pay_date\":null,\"notes\":null}', '103.72.212.222', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-01 18:26:08', '2026-10-01 18:26:08'),
(144, 1, 'post', 'login.store', 'POST', 'http://127.0.0.1:8000/login', '{\"login\":\"admin@example.com\"}', '103.72.212.222', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-02 14:22:13', '2026-10-02 14:22:13'),
(145, 1, 'post', 'login.store', 'POST', 'http://127.0.0.1:8000/login', '{\"login\":\"admin@example.com\"}', '103.72.212.222', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-05 11:02:50', '2026-10-05 11:02:50'),
(146, 1, 'post', 'login.store', 'POST', 'http://127.0.0.1:8000/login', '{\"login\":\"admin@example.com\"}', '103.72.212.222', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-05 11:07:28', '2026-10-05 11:07:28'),
(147, 1, 'post', 'login.store', 'POST', 'http://127.0.0.1:8000/login', '{\"login\":\"admin@example.com\"}', '103.72.212.222', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-05 11:07:35', '2026-10-05 11:07:35'),
(148, 1, 'post', 'login.store', 'POST', 'http://127.0.0.1:8000/login', '{\"login\":\"iamdashingcoder@gmail.com\"}', '103.72.212.222', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-05 11:08:01', '2026-10-05 11:08:01'),
(149, 1, 'post', 'login.store', 'POST', 'http://127.0.0.1:8000/login', '{\"login\":\"iamdashingcoder@gmail.com\"}', '103.72.212.222', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-05 15:16:14', '2026-10-05 15:16:14'),
(150, 1, 'post', 'login.store', 'POST', 'http://127.0.0.1:8000/login', '{\"login\":\"iamdashingcoder@gmail.com\"}', '103.72.212.222', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-06 17:36:04', '2026-10-06 17:36:04'),
(151, 1, 'post', 'login.store', 'POST', 'http://127.0.0.1:8000/login', '{\"login\":\"iamdashingcoder@gmail.com\",\"remember\":\"1\"}', '202.191.122.185', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-08 11:19:03', '2026-10-08 11:19:03'),
(152, 1, 'patch', 'notifications.read', 'PATCH', 'http://127.0.0.1:8000/notifications/8/read', '{\"_method\":\"PATCH\"}', '202.191.122.185', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-08 11:21:21', '2026-10-08 11:21:21'),
(153, 1, 'post', 'login.store', 'POST', 'http://127.0.0.1:8000/login', '{\"login\":\"iamdashingcoder@gmail.com\"}', '103.72.212.222', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-08 11:27:15', '2026-10-08 11:27:15'),
(154, 1, 'post', 'users.store', 'POST', 'http://127.0.0.1:8000/users', '{\"name\":\"Md Nasir Uddin\",\"username\":\"NasirUddin\",\"email\":\"nasir@gmail.com\",\"phone\":\"01734685525\",\"status\":\"active\",\"roles\":[\"Seller\"]}', '202.191.122.185', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-08 11:30:36', '2026-10-08 11:30:36'),
(155, 4, 'post', 'login.store', 'POST', 'http://127.0.0.1:8000/login', '{\"login\":\"NasirUddin\"}', '202.191.122.185', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-08 11:30:51', '2026-10-08 11:30:51'),
(156, 1, 'post', 'login.store', 'POST', 'http://127.0.0.1:8000/login', '{\"login\":\"iamdashingcoder@gmail.com\"}', '202.191.122.185', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-08 11:34:22', '2026-10-08 11:34:22'),
(157, 1, 'put', 'users.update', 'PUT', 'http://127.0.0.1:8000/users/4', '{\"_method\":\"PUT\",\"name\":\"Md Nasir Uddin\",\"username\":\"NasirUddin\",\"email\":\"nasir@gmail.com\",\"phone\":\"01734685525\",\"status\":\"active\",\"roles\":[\"Sales Executive\"]}', '202.191.122.185', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-08 11:34:45', '2026-10-08 11:34:45'),
(158, 4, 'post', 'login.store', 'POST', 'http://127.0.0.1:8000/login', '{\"login\":\"NasirUddin\"}', '202.191.122.185', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-08 11:34:52', '2026-10-08 11:34:52'),
(159, 1, 'post', 'login.store', 'POST', 'http://127.0.0.1:8000/login', '{\"login\":\"iamdashingcoder@gmail.com\"}', '202.191.122.185', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-08 11:35:39', '2026-10-08 11:35:39'),
(160, 1, 'put', 'users.update', 'PUT', 'http://127.0.0.1:8000/users/4', '{\"_method\":\"PUT\",\"name\":\"Md Nasir Uddin\",\"username\":\"NasirUddin\",\"email\":\"nasir@gmail.com\",\"phone\":\"01734685525\",\"status\":\"active\",\"roles\":[\"Purchase Officer\"]}', '202.191.122.185', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-08 11:35:56', '2026-10-08 11:35:56'),
(161, 4, 'post', 'login.store', 'POST', 'http://127.0.0.1:8000/login', '{\"login\":\"NasirUddin\"}', '202.191.122.185', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-08 11:36:01', '2026-10-08 11:36:01'),
(162, 3, 'post', 'login.store', 'POST', 'http://127.0.0.1:8000/login', '{\"login\":\"seller\"}', '103.72.212.222', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-08 11:36:17', '2026-10-08 11:36:17'),
(163, 1, 'post', 'login.store', 'POST', 'http://127.0.0.1:8000/login', '{\"login\":\"iamdashingcoder@gmail.com\"}', '202.191.122.185', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-08 11:36:23', '2026-10-08 11:36:23'),
(164, 1, 'put', 'users.update', 'PUT', 'http://127.0.0.1:8000/users/4', '{\"_method\":\"PUT\",\"name\":\"Md Nasir Uddin\",\"username\":\"NasirUddin\",\"email\":\"nasir@gmail.com\",\"phone\":\"01734685525\",\"status\":\"active\",\"roles\":[\"Store Keeper\"]}', '202.191.122.185', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-08 11:36:43', '2026-10-08 11:36:43'),
(165, 4, 'post', 'login.store', 'POST', 'http://127.0.0.1:8000/login', '{\"login\":\"NasirUddin\"}', '202.191.122.185', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-08 11:36:51', '2026-10-08 11:36:51'),
(166, 1, 'post', 'login.store', 'POST', 'http://127.0.0.1:8000/login', '{\"login\":\"iamdashingcoder@gmail.com\"}', '202.191.122.185', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-08 11:37:18', '2026-10-08 11:37:18'),
(167, 1, 'put', 'users.update', 'PUT', 'http://127.0.0.1:8000/users/4', '{\"_method\":\"PUT\",\"name\":\"Md Nasir Uddin\",\"username\":\"NasirUddin\",\"email\":\"nasir@gmail.com\",\"phone\":\"01734685525\",\"status\":\"active\",\"roles\":[\"Payroll Officer\"]}', '202.191.122.185', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-08 11:37:38', '2026-10-08 11:37:38'),
(168, 4, 'post', 'login.store', 'POST', 'http://127.0.0.1:8000/login', '{\"login\":\"NasirUddin\"}', '202.191.122.185', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-08 11:37:43', '2026-10-08 11:37:43'),
(169, 1, 'post', 'login.store', 'POST', 'http://127.0.0.1:8000/login', '{\"login\":\"iamdashingcoder@gmail.com\"}', '202.191.122.185', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-08 11:38:07', '2026-10-08 11:38:07'),
(170, 4, 'post', 'login.store', 'POST', 'http://127.0.0.1:8000/login', '{\"login\":\"NasirUddin\"}', '202.191.122.185', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-08 11:38:14', '2026-10-08 11:38:14'),
(171, 4, 'put', 'payrolls.update', 'PUT', 'http://127.0.0.1:8000/payrolls/3', '{\"_method\":\"PUT\",\"items\":{\"21\":{\"bonus\":\"0.00\",\"overtime\":\"0.00\",\"deduction\":\"500.00\"},\"22\":{\"bonus\":\"0.00\",\"overtime\":\"0.00\",\"deduction\":\"500.00\"},\"23\":{\"bonus\":\"0.00\",\"overtime\":\"0.00\",\"deduction\":\"500.00\"},\"24\":{\"bonus\":\"0.00\",\"overtime\":\"0.00\",\"deduction\":\"500.00\"},\"25\":{\"bonus\":\"0.00\",\"overtime\":\"0.00\",\"deduction\":\"500.00\"},\"26\":{\"bonus\":\"0.00\",\"overtime\":\"0.00\",\"deduction\":\"500.00\"},\"27\":{\"bonus\":\"0.00\",\"overtime\":\"0.00\",\"deduction\":\"500.00\"},\"28\":{\"bonus\":\"0.00\",\"overtime\":\"0.00\",\"deduction\":\"500.00\"},\"29\":{\"bonus\":\"0.00\",\"overtime\":\"0.00\",\"deduction\":\"500.00\"},\"30\":{\"bonus\":\"0.00\",\"overtime\":\"0.00\",\"deduction\":\"500.00\"}}}', '202.191.122.185', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-08 11:38:40', '2026-10-08 11:38:40'),
(172, 4, 'post', 'payrolls.post', 'POST', 'http://127.0.0.1:8000/payrolls/3/post', '[]', '202.191.122.185', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-08 11:38:42', '2026-10-08 11:38:42'),
(173, 1, 'post', 'login.store', 'POST', 'http://127.0.0.1:8000/login', '{\"login\":\"iamdashingcoder@gmail.com\"}', '202.191.122.185', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-08 11:38:54', '2026-10-08 11:38:54'),
(174, 1, 'put', 'users.update', 'PUT', 'http://127.0.0.1:8000/users/4', '{\"_method\":\"PUT\",\"name\":\"Md Nasir Uddin\",\"username\":\"NasirUddin\",\"email\":\"nasir@gmail.com\",\"phone\":\"01734685525\",\"status\":\"active\",\"roles\":[\"Accountant\"]}', '202.191.122.185', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-08 11:39:05', '2026-10-08 11:39:05'),
(175, 4, 'post', 'login.store', 'POST', 'http://127.0.0.1:8000/login', '{\"login\":\"NasirUddin\"}', '202.191.122.185', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-08 11:39:12', '2026-10-08 11:39:12'),
(176, 1, 'post', 'login.store', 'POST', 'http://127.0.0.1:8000/login', '{\"login\":\"iamdashingcoder@gmail.com\"}', '202.191.122.185', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-08 11:41:00', '2026-10-08 11:41:00'),
(177, 1, 'post', 'login.store', 'POST', 'http://127.0.0.1:8000/login', '{\"login\":\"iamdashingcoder@gmail.com\"}', '202.191.122.185', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-08 11:45:50', '2026-10-08 11:45:50'),
(178, 1, 'post', 'login.store', 'POST', 'http://127.0.0.1:8000/login', '{\"login\":\"iamdashingcoder@gmail.com\"}', '103.187.95.68', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-11 13:17:46', '2026-10-11 13:17:46'),
(179, 1, 'post', 'login.store', 'POST', 'http://127.0.0.1:8000/login', '{\"login\":\"iamdashingcoder@gmail.com\"}', '103.187.95.68', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-11 16:19:33', '2026-10-11 16:19:33'),
(180, 3, 'post', 'login.store', 'POST', 'http://127.0.0.1:8000/login', '{\"login\":\"seller\",\"remember\":\"1\"}', '103.115.116.85', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-12 09:51:47', '2026-10-12 09:51:47'),
(181, 1, 'post', 'login.store', 'POST', 'http://127.0.0.1:8000/login', '{\"login\":\"iamdashingcoder@gmail.com\",\"remember\":\"1\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-10-22 00:29:51', '2026-10-22 00:29:51'),
(182, 1, 'post', 'backups.store', 'POST', 'http://127.0.0.1:8000/backups', '[]', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-10-22 00:53:52', '2026-10-22 00:53:52'),
(183, 1, 'post', 'backups.store', 'POST', 'http://127.0.0.1:8000/backups', '[]', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-10-22 00:54:18', '2026-10-22 00:54:18'),
(184, 1, 'post', 'business-days.close', 'POST', 'http://127.0.0.1:8000/settings/business-days/1/close', '{\"closing_notes\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-10-23 00:27:02', '2026-10-23 00:27:02'),
(185, 1, 'post', 'business-days.open', 'POST', 'http://127.0.0.1:8000/settings/business-days/open', '{\"business_date\":\"2026-10-23\",\"opening_notes\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-10-23 00:27:13', '2026-10-23 00:27:13'),
(186, 1, 'post', 'business-days.reopen', 'POST', 'http://127.0.0.1:8000/settings/business-days/1/reopen', '[]', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-10-23 00:27:18', '2026-10-23 00:27:18'),
(187, 1, 'post', 'barcode-print.preview', 'POST', 'http://127.0.0.1:8000/settings/barcode-print/preview', '{\"products\":{\"13\":\"0\"},\"label_width\":\"50\",\"label_height\":\"30\",\"show_name\":\"1\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-10-23 02:36:22', '2026-10-23 02:36:22'),
(188, 1, 'post', 'barcode-print.preview', 'POST', 'http://127.0.0.1:8000/settings/barcode-print/preview', '{\"products\":{\"13\":\"1\"},\"label_width\":\"50\",\"label_height\":\"30\",\"show_name\":\"1\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-10-23 02:36:36', '2026-10-23 02:36:36'),
(189, 1, 'post', 'barcode-print.preview', 'POST', 'http://127.0.0.1:8000/settings/barcode-print/preview', '{\"products\":{\"13\":\"0\",\"6\":\"0\",\"2\":\"0\",\"11\":\"0\",\"18\":\"0\",\"9\":\"0\",\"20\":\"0\",\"19\":\"0\",\"12\":\"0\",\"5\":\"0\",\"21\":\"0\",\"22\":\"0\",\"23\":\"0\",\"24\":\"0\",\"25\":\"0\",\"26\":\"0\",\"27\":\"0\",\"28\":\"0\",\"29\":\"0\",\"30\":\"0\",\"31\":\"0\",\"32\":\"0\",\"33\":\"0\",\"34\":\"0\",\"35\":\"0\",\"36\":\"0\",\"37\":\"0\",\"38\":\"0\",\"39\":\"0\",\"40\":\"0\",\"41\":\"0\",\"42\":\"0\",\"43\":\"0\",\"44\":\"0\",\"45\":\"0\",\"46\":\"0\",\"47\":\"0\",\"48\":\"0\",\"49\":\"0\",\"50\":\"0\",\"51\":\"0\",\"52\":\"0\",\"53\":\"0\",\"54\":\"0\",\"55\":\"0\",\"56\":\"0\",\"57\":\"0\",\"58\":\"0\",\"59\":\"0\",\"60\":\"0\"},\"label_width\":\"50\",\"label_height\":\"30\",\"show_name\":\"1\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-10-28 03:25:05', '2026-10-28 03:25:05'),
(190, 1, 'post', 'barcode-print.preview', 'POST', 'http://127.0.0.1:8000/settings/barcode-print/preview', '{\"products\":{\"13\":\"0\",\"6\":\"0\",\"2\":\"0\",\"11\":\"0\",\"18\":\"0\",\"9\":\"0\",\"20\":\"0\",\"19\":\"0\",\"12\":\"0\",\"5\":\"0\",\"21\":\"0\",\"22\":\"0\",\"23\":\"0\",\"24\":\"0\",\"25\":\"0\",\"26\":\"0\",\"27\":\"0\",\"28\":\"0\",\"29\":\"0\",\"30\":\"0\",\"31\":\"0\",\"32\":\"0\",\"33\":\"0\",\"34\":\"0\",\"35\":\"0\",\"36\":\"0\",\"37\":\"0\",\"38\":\"0\",\"39\":\"0\",\"40\":\"0\",\"41\":\"0\",\"42\":\"0\",\"43\":\"0\",\"44\":\"0\",\"45\":\"0\",\"46\":\"0\",\"47\":\"0\",\"48\":\"0\",\"49\":\"0\",\"50\":\"0\",\"51\":\"0\",\"52\":\"0\",\"53\":\"0\",\"54\":\"0\",\"55\":\"0\",\"56\":\"0\",\"57\":\"0\",\"58\":\"0\",\"59\":\"10\",\"60\":\"10\"},\"label_width\":\"50\",\"label_height\":\"30\",\"show_name\":\"1\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-10-28 03:25:24', '2026-10-28 03:25:24'),
(191, 1, 'post', 'barcode-print.preview', 'POST', 'http://127.0.0.1:8000/settings/barcode-print/preview', '{\"products\":{\"13\":\"0\",\"6\":\"0\",\"2\":\"0\",\"11\":\"0\",\"18\":\"0\",\"9\":\"0\",\"20\":\"0\",\"19\":\"0\",\"12\":\"0\",\"5\":\"0\",\"21\":\"0\",\"22\":\"0\",\"23\":\"0\",\"24\":\"0\",\"25\":\"0\",\"26\":\"0\",\"27\":\"0\",\"28\":\"0\",\"29\":\"0\",\"30\":\"0\",\"31\":\"0\",\"32\":\"0\",\"33\":\"0\",\"34\":\"0\",\"35\":\"0\",\"36\":\"0\",\"37\":\"0\",\"38\":\"0\",\"39\":\"0\",\"40\":\"0\",\"41\":\"0\",\"42\":\"0\",\"43\":\"0\",\"44\":\"0\",\"45\":\"0\",\"46\":\"0\",\"47\":\"0\",\"48\":\"0\",\"49\":\"0\",\"50\":\"0\",\"51\":\"0\",\"52\":\"0\",\"53\":\"0\",\"54\":\"0\",\"55\":\"0\",\"56\":\"0\",\"57\":\"0\",\"58\":\"0\",\"59\":\"10\",\"60\":\"10\"},\"label_width\":\"50\",\"label_height\":\"30\",\"show_name\":\"1\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-10-28 03:25:52', '2026-10-28 03:25:52');

-- --------------------------------------------------------

--
-- Table structure for table `approval_requests`
--

CREATE TABLE `approval_requests` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `approvable_type` varchar(80) NOT NULL,
  `approvable_id` bigint(20) UNSIGNED NOT NULL,
  `reference_no` varchar(120) DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `reason` text DEFAULT NULL,
  `requested_by` bigint(20) UNSIGNED DEFAULT NULL,
  `decided_by` bigint(20) UNSIGNED DEFAULT NULL,
  `decided_at` timestamp NULL DEFAULT NULL,
  `decision_notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bank_reconciliations`
--

CREATE TABLE `bank_reconciliations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `account_id` bigint(20) UNSIGNED NOT NULL,
  `statement_date` date NOT NULL,
  `statement_balance` decimal(18,2) NOT NULL,
  `system_balance` decimal(18,2) NOT NULL,
  `cleared_balance` decimal(18,2) NOT NULL DEFAULT 0.00,
  `difference` decimal(18,2) NOT NULL DEFAULT 0.00,
  `status` varchar(20) NOT NULL DEFAULT 'draft',
  `notes` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `closed_by` bigint(20) UNSIGNED DEFAULT NULL,
  `closed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bank_reconciliation_items`
--

CREATE TABLE `bank_reconciliation_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `bank_reconciliation_id` bigint(20) UNSIGNED NOT NULL,
  `journal_line_id` bigint(20) UNSIGNED NOT NULL,
  `amount` decimal(18,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `brands`
--

CREATE TABLE `brands` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(150) NOT NULL,
  `code` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `brands`
--

INSERT INTO `brands` (`id`, `name`, `code`, `description`, `is_active`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Samsung', 'BR-SAM', NULL, 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(2, 'HP', 'BR-HP', NULL, 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(3, 'Xiaomi', 'BR-XIA', NULL, 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(4, 'Walton', 'BR-WAL', NULL, 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(5, 'Logitech', 'BR-LOG', NULL, 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(6, 'Dell', 'BR-DEL', NULL, 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(7, 'Toshiba', 'BR-TOS', NULL, 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(8, 'TP-Link', 'BR-TPL', NULL, 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(9, 'Lenovo', 'BR-LEN', NULL, 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(10, 'Generic', 'BR-GEN', NULL, 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(11, 'শাহ সিমেন্ট', 'CM-Shah', NULL, 1, '2026-09-25 16:28:55', '2026-09-25 16:29:25', NULL),
(12, 'BSRM', 'BSRM', NULL, 1, '2026-09-25 16:38:21', '2026-09-25 16:38:21', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `budgets`
--

CREATE TABLE `budgets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `cost_center_id` bigint(20) UNSIGNED DEFAULT NULL,
  `project_id` bigint(20) UNSIGNED DEFAULT NULL,
  `account_id` bigint(20) UNSIGNED NOT NULL,
  `period_start` date NOT NULL,
  `period_end` date NOT NULL,
  `amount` decimal(18,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `business_days`
--

CREATE TABLE `business_days` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `business_date` date NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'open',
  `opening_notes` text DEFAULT NULL,
  `closing_notes` text DEFAULT NULL,
  `opened_by` bigint(20) UNSIGNED DEFAULT NULL,
  `opened_at` timestamp NULL DEFAULT NULL,
  `closed_by` bigint(20) UNSIGNED DEFAULT NULL,
  `closed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `business_days`
--

INSERT INTO `business_days` (`id`, `business_date`, `status`, `opening_notes`, `closing_notes`, `opened_by`, `opened_at`, `closed_by`, `closed_at`, `created_at`, `updated_at`) VALUES
(1, '2026-10-22', 'closed', NULL, NULL, NULL, '2026-10-22 12:23:57', 1, '2026-10-23 00:27:02', '2026-10-22 12:23:57', '2026-10-23 00:27:02'),
(2, '2026-10-23', 'open', NULL, NULL, 1, '2026-10-23 00:27:13', NULL, NULL, '2026-10-23 00:27:13', '2026-10-23 00:27:13');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(150) NOT NULL,
  `slug` varchar(180) NOT NULL,
  `code` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `parent_id`, `name`, `slug`, `code`, `description`, `is_active`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, NULL, 'Mobile Phones', 'mobile-phones', 'CAT-MOB', 'Mobile devices', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(2, NULL, 'Laptops', 'laptops', 'CAT-LAP', 'Laptop computers', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(3, NULL, 'Accessories', 'accessories', 'CAT-ACC', 'Computer and mobile accessories', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(4, NULL, 'Televisions', 'televisions', 'CAT-TV', 'Televisions', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(5, NULL, 'Networking', 'networking', 'CAT-NET', 'Networking products', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(6, NULL, 'Office Equipment', 'office-equipment', 'CAT-OFF', 'Office equipment', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(7, NULL, 'সিমেন্ট', 'siment', 'CM', NULL, 1, '2026-09-25 16:30:49', '2026-09-25 16:30:49', NULL),
(8, NULL, 'রড', 'rd', 'Rd', NULL, 1, '2026-09-25 16:37:27', '2026-09-25 16:37:27', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `cost_centers`
--

CREATE TABLE `cost_centers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(40) NOT NULL,
  `name` varchar(120) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `currencies`
--

CREATE TABLE `currencies` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(10) NOT NULL,
  `name` varchar(100) NOT NULL,
  `symbol` varchar(12) DEFAULT NULL,
  `is_base` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `currencies`
--

INSERT INTO `currencies` (`id`, `code`, `name`, `symbol`, `is_base`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'BDT', 'Bangladeshi Taka', '৳', 1, 1, '2026-10-23 06:20:12', '2026-10-23 06:20:12');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(50) DEFAULT NULL,
  `customer_group_id` bigint(20) UNSIGNED DEFAULT NULL,
  `market_hierarchy_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(200) NOT NULL,
  `company_name` varchar(200) DEFAULT NULL,
  `contact_person` varchar(150) DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `alternate_phone` varchar(30) DEFAULT NULL,
  `email` varchar(200) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `district` varchar(100) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `opening_balance` decimal(18,2) NOT NULL DEFAULT 0.00,
  `opening_balance_type` varchar(20) NOT NULL DEFAULT 'receivable',
  `credit_limit` decimal(18,2) DEFAULT NULL,
  `payment_terms_days` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `tax_number` varchar(100) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `code`, `customer_group_id`, `market_hierarchy_id`, `name`, `company_name`, `contact_person`, `phone`, `alternate_phone`, `email`, `address`, `city`, `district`, `country`, `opening_balance`, `opening_balance_type`, `credit_limit`, `payment_terms_days`, `tax_number`, `notes`, `is_active`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'CUS-000001', 1, NULL, 'Walk-in Customer', NULL, NULL, '01710000001', NULL, 'customer001@example.com', '101 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 75000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(2, 'CUS-000002', 2, NULL, 'Rahim Traders', 'Rahim Traders', 'Rahim Traders', '01710000002', NULL, 'customer002@example.com', '102 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 100000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(3, 'CUS-000003', 3, NULL, 'Karim Electronics', 'Karim Electronics', 'Karim Electronics', '01710000003', NULL, 'customer003@example.com', '103 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 125000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(4, 'CUS-000004', 4, NULL, 'Noor Enterprise', 'Noor Enterprise', 'Noor Enterprise', '01710000004', NULL, 'customer004@example.com', '104 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 150000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(5, 'CUS-000005', 1, NULL, 'City Tech', 'City Tech', 'City Tech', '01710000005', NULL, 'customer005@example.com', '105 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 50000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(6, 'CUS-000006', 2, NULL, 'Modern Mart', 'Modern Mart', 'Modern Mart', '01710000006', NULL, 'customer006@example.com', '106 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 75000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(7, 'CUS-000007', 3, NULL, 'Prime Solutions', 'Prime Solutions', 'Prime Solutions', '01710000007', NULL, 'customer007@example.com', '107 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 100000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(8, 'CUS-000008', 4, NULL, 'Blue Sky Trading', 'Blue Sky Trading', 'Blue Sky Trading', '01710000008', NULL, 'customer008@example.com', '108 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 125000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(9, 'CUS-000009', 1, NULL, 'Digital Point', 'Digital Point', 'Digital Point', '01710000009', NULL, 'customer009@example.com', '109 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 150000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(10, 'CUS-000010', 2, NULL, 'Green Line Store', 'Green Line Store', 'Green Line Store', '01710000010', NULL, 'customer010@example.com', '110 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 50000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(11, 'CUS-000011', 3, NULL, 'Metro Electronics', 'Metro Electronics', 'Metro Electronics', '01710000011', NULL, 'customer011@example.com', '111 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 75000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(12, 'CUS-000012', 4, NULL, 'Sunrise Traders', 'Sunrise Traders', 'Sunrise Traders', '01710000012', NULL, 'customer012@example.com', '112 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 100000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(13, 'CUS-000013', 1, NULL, 'Sample Customer 013', 'Sample Customer 013', 'Sample Customer 013', '01710000013', NULL, 'customer013@example.com', '113 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 125000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(14, 'CUS-000014', 2, NULL, 'Sample Customer 014', 'Sample Customer 014', 'Sample Customer 014', '01710000014', NULL, 'customer014@example.com', '114 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 150000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(15, 'CUS-000015', 3, NULL, 'Sample Customer 015', 'Sample Customer 015', 'Sample Customer 015', '01710000015', NULL, 'customer015@example.com', '115 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 50000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(16, 'CUS-000016', 4, NULL, 'Sample Customer 016', 'Sample Customer 016', 'Sample Customer 016', '01710000016', NULL, 'customer016@example.com', '116 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 75000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(17, 'CUS-000017', 1, NULL, 'Sample Customer 017', 'Sample Customer 017', 'Sample Customer 017', '01710000017', NULL, 'customer017@example.com', '117 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 100000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(18, 'CUS-000018', 2, NULL, 'Sample Customer 018', 'Sample Customer 018', 'Sample Customer 018', '01710000018', NULL, 'customer018@example.com', '118 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 125000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(19, 'CUS-000019', 3, NULL, 'Sample Customer 019', 'Sample Customer 019', 'Sample Customer 019', '01710000019', NULL, 'customer019@example.com', '119 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 150000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(20, 'CUS-000020', 4, NULL, 'Sample Customer 020', 'Sample Customer 020', 'Sample Customer 020', '01710000020', NULL, 'customer020@example.com', '120 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 50000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(21, 'CUS-000021', 1, NULL, 'Sample Customer 021', 'Sample Customer 021', 'Sample Customer 021', '01710000021', NULL, 'customer021@example.com', '121 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 75000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(22, 'CUS-000022', 2, NULL, 'Sample Customer 022', 'Sample Customer 022', 'Sample Customer 022', '01710000022', NULL, 'customer022@example.com', '122 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 100000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(23, 'CUS-000023', 3, NULL, 'Sample Customer 023', 'Sample Customer 023', 'Sample Customer 023', '01710000023', NULL, 'customer023@example.com', '123 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 125000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(24, 'CUS-000024', 4, NULL, 'Sample Customer 024', 'Sample Customer 024', 'Sample Customer 024', '01710000024', NULL, 'customer024@example.com', '124 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 150000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(25, 'CUS-000025', 1, NULL, 'Sample Customer 025', 'Sample Customer 025', 'Sample Customer 025', '01710000025', NULL, 'customer025@example.com', '125 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 50000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(26, 'CUS-000026', 2, NULL, 'Sample Customer 026', 'Sample Customer 026', 'Sample Customer 026', '01710000026', NULL, 'customer026@example.com', '126 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 75000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(27, 'CUS-000027', 3, NULL, 'Sample Customer 027', 'Sample Customer 027', 'Sample Customer 027', '01710000027', NULL, 'customer027@example.com', '127 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 100000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(28, 'CUS-000028', 4, NULL, 'Sample Customer 028', 'Sample Customer 028', 'Sample Customer 028', '01710000028', NULL, 'customer028@example.com', '128 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 125000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(29, 'CUS-000029', 1, NULL, 'Sample Customer 029', 'Sample Customer 029', 'Sample Customer 029', '01710000029', NULL, 'customer029@example.com', '129 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 150000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(30, 'CUS-000030', 2, NULL, 'Sample Customer 030', 'Sample Customer 030', 'Sample Customer 030', '01710000030', NULL, 'customer030@example.com', '130 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 50000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(31, 'CUS-000031', 3, NULL, 'Sample Customer 031', 'Sample Customer 031', 'Sample Customer 031', '01710000031', NULL, 'customer031@example.com', '131 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 75000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(32, 'CUS-000032', 4, NULL, 'Sample Customer 032', 'Sample Customer 032', 'Sample Customer 032', '01710000032', NULL, 'customer032@example.com', '132 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 100000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(33, 'CUS-000033', 1, NULL, 'Sample Customer 033', 'Sample Customer 033', 'Sample Customer 033', '01710000033', NULL, 'customer033@example.com', '133 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 125000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(34, 'CUS-000034', 2, NULL, 'Sample Customer 034', 'Sample Customer 034', 'Sample Customer 034', '01710000034', NULL, 'customer034@example.com', '134 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 150000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(35, 'CUS-000035', 3, NULL, 'Sample Customer 035', 'Sample Customer 035', 'Sample Customer 035', '01710000035', NULL, 'customer035@example.com', '135 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 50000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(36, 'CUS-000036', 4, NULL, 'Sample Customer 036', 'Sample Customer 036', 'Sample Customer 036', '01710000036', NULL, 'customer036@example.com', '136 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 75000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(37, 'CUS-000037', 1, NULL, 'Sample Customer 037', 'Sample Customer 037', 'Sample Customer 037', '01710000037', NULL, 'customer037@example.com', '137 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 100000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(38, 'CUS-000038', 2, NULL, 'Sample Customer 038', 'Sample Customer 038', 'Sample Customer 038', '01710000038', NULL, 'customer038@example.com', '138 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 125000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(39, 'CUS-000039', 3, NULL, 'Sample Customer 039', 'Sample Customer 039', 'Sample Customer 039', '01710000039', NULL, 'customer039@example.com', '139 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 150000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(40, 'CUS-000040', 4, NULL, 'Sample Customer 040', 'Sample Customer 040', 'Sample Customer 040', '01710000040', NULL, 'customer040@example.com', '140 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 50000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(41, 'CUS-000041', 1, NULL, 'Sample Customer 041', 'Sample Customer 041', 'Sample Customer 041', '01710000041', NULL, 'customer041@example.com', '141 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 75000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(42, 'CUS-000042', 2, NULL, 'Sample Customer 042', 'Sample Customer 042', 'Sample Customer 042', '01710000042', NULL, 'customer042@example.com', '142 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 100000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(43, 'CUS-000043', 3, NULL, 'Sample Customer 043', 'Sample Customer 043', 'Sample Customer 043', '01710000043', NULL, 'customer043@example.com', '143 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 125000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(44, 'CUS-000044', 4, NULL, 'Sample Customer 044', 'Sample Customer 044', 'Sample Customer 044', '01710000044', NULL, 'customer044@example.com', '144 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 150000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(45, 'CUS-000045', 1, NULL, 'Sample Customer 045', 'Sample Customer 045', 'Sample Customer 045', '01710000045', NULL, 'customer045@example.com', '145 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 50000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(46, 'CUS-000046', 2, NULL, 'Sample Customer 046', 'Sample Customer 046', 'Sample Customer 046', '01710000046', NULL, 'customer046@example.com', '146 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 75000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(47, 'CUS-000047', 3, NULL, 'Sample Customer 047', 'Sample Customer 047', 'Sample Customer 047', '01710000047', NULL, 'customer047@example.com', '147 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 100000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(48, 'CUS-000048', 4, NULL, 'Sample Customer 048', 'Sample Customer 048', 'Sample Customer 048', '01710000048', NULL, 'customer048@example.com', '148 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 125000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(49, 'CUS-000049', 1, NULL, 'Sample Customer 049', 'Sample Customer 049', 'Sample Customer 049', '01710000049', NULL, 'customer049@example.com', '149 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 150000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(50, 'CUS-000050', 2, NULL, 'Sample Customer 050', 'Sample Customer 050', 'Sample Customer 050', '01710000050', NULL, 'customer050@example.com', '150 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 50000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(51, 'CUS-000051', 3, NULL, 'Sample Customer 051', 'Sample Customer 051', 'Sample Customer 051', '01710000051', NULL, 'customer051@example.com', '151 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 75000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(52, 'CUS-000052', 4, NULL, 'Sample Customer 052', 'Sample Customer 052', 'Sample Customer 052', '01710000052', NULL, 'customer052@example.com', '152 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 100000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(53, 'CUS-000053', 1, NULL, 'Sample Customer 053', 'Sample Customer 053', 'Sample Customer 053', '01710000053', NULL, 'customer053@example.com', '153 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 125000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(54, 'CUS-000054', 2, NULL, 'Sample Customer 054', 'Sample Customer 054', 'Sample Customer 054', '01710000054', NULL, 'customer054@example.com', '154 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 150000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(55, 'CUS-000055', 3, NULL, 'Sample Customer 055', 'Sample Customer 055', 'Sample Customer 055', '01710000055', NULL, 'customer055@example.com', '155 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 50000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(56, 'CUS-000056', 4, NULL, 'Sample Customer 056', 'Sample Customer 056', 'Sample Customer 056', '01710000056', NULL, 'customer056@example.com', '156 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 75000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(57, 'CUS-000057', 1, NULL, 'Sample Customer 057', 'Sample Customer 057', 'Sample Customer 057', '01710000057', NULL, 'customer057@example.com', '157 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 100000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(58, 'CUS-000058', 2, NULL, 'Sample Customer 058', 'Sample Customer 058', 'Sample Customer 058', '01710000058', NULL, 'customer058@example.com', '158 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 125000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(59, 'CUS-000059', 3, NULL, 'Sample Customer 059', 'Sample Customer 059', 'Sample Customer 059', '01710000059', NULL, 'customer059@example.com', '159 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 150000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(60, 'CUS-000060', 4, NULL, 'Sample Customer 060', 'Sample Customer 060', 'Sample Customer 060', '01710000060', NULL, 'customer060@example.com', '160 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 50000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(61, 'CUS-000061', 1, NULL, 'Sample Customer 061', 'Sample Customer 061', 'Sample Customer 061', '01710000061', NULL, 'customer061@example.com', '161 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 75000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(62, 'CUS-000062', 2, NULL, 'Sample Customer 062', 'Sample Customer 062', 'Sample Customer 062', '01710000062', NULL, 'customer062@example.com', '162 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 100000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(63, 'CUS-000063', 3, NULL, 'Sample Customer 063', 'Sample Customer 063', 'Sample Customer 063', '01710000063', NULL, 'customer063@example.com', '163 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 125000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(64, 'CUS-000064', 4, NULL, 'Sample Customer 064', 'Sample Customer 064', 'Sample Customer 064', '01710000064', NULL, 'customer064@example.com', '164 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 150000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(65, 'CUS-000065', 1, NULL, 'Sample Customer 065', 'Sample Customer 065', 'Sample Customer 065', '01710000065', NULL, 'customer065@example.com', '165 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 50000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(66, 'CUS-000066', 2, NULL, 'Sample Customer 066', 'Sample Customer 066', 'Sample Customer 066', '01710000066', NULL, 'customer066@example.com', '166 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 75000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(67, 'CUS-000067', 3, NULL, 'Sample Customer 067', 'Sample Customer 067', 'Sample Customer 067', '01710000067', NULL, 'customer067@example.com', '167 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 100000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(68, 'CUS-000068', 4, NULL, 'Sample Customer 068', 'Sample Customer 068', 'Sample Customer 068', '01710000068', NULL, 'customer068@example.com', '168 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 125000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(69, 'CUS-000069', 1, NULL, 'Sample Customer 069', 'Sample Customer 069', 'Sample Customer 069', '01710000069', NULL, 'customer069@example.com', '169 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 150000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(70, 'CUS-000070', 2, NULL, 'Sample Customer 070', 'Sample Customer 070', 'Sample Customer 070', '01710000070', NULL, 'customer070@example.com', '170 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 50000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(71, 'CUS-000071', 3, NULL, 'Sample Customer 071', 'Sample Customer 071', 'Sample Customer 071', '01710000071', NULL, 'customer071@example.com', '171 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 75000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(72, 'CUS-000072', 4, NULL, 'Sample Customer 072', 'Sample Customer 072', 'Sample Customer 072', '01710000072', NULL, 'customer072@example.com', '172 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 100000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(73, 'CUS-000073', 1, NULL, 'Sample Customer 073', 'Sample Customer 073', 'Sample Customer 073', '01710000073', NULL, 'customer073@example.com', '173 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 125000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(74, 'CUS-000074', 2, NULL, 'Sample Customer 074', 'Sample Customer 074', 'Sample Customer 074', '01710000074', NULL, 'customer074@example.com', '174 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 150000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(75, 'CUS-000075', 3, NULL, 'Sample Customer 075', 'Sample Customer 075', 'Sample Customer 075', '01710000075', NULL, 'customer075@example.com', '175 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 50000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(76, 'CUS-000076', 4, NULL, 'Sample Customer 076', 'Sample Customer 076', 'Sample Customer 076', '01710000076', NULL, 'customer076@example.com', '176 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 75000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(77, 'CUS-000077', 1, NULL, 'Sample Customer 077', 'Sample Customer 077', 'Sample Customer 077', '01710000077', NULL, 'customer077@example.com', '177 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 100000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(78, 'CUS-000078', 2, NULL, 'Sample Customer 078', 'Sample Customer 078', 'Sample Customer 078', '01710000078', NULL, 'customer078@example.com', '178 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 125000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(79, 'CUS-000079', 3, NULL, 'Sample Customer 079', 'Sample Customer 079', 'Sample Customer 079', '01710000079', NULL, 'customer079@example.com', '179 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 150000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(80, 'CUS-000080', 4, NULL, 'Sample Customer 080', 'Sample Customer 080', 'Sample Customer 080', '01710000080', NULL, 'customer080@example.com', '180 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 50000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(81, 'CUS-000081', 1, NULL, 'Sample Customer 081', 'Sample Customer 081', 'Sample Customer 081', '01710000081', NULL, 'customer081@example.com', '181 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 75000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(82, 'CUS-000082', 2, NULL, 'Sample Customer 082', 'Sample Customer 082', 'Sample Customer 082', '01710000082', NULL, 'customer082@example.com', '182 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 100000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(83, 'CUS-000083', 3, NULL, 'Sample Customer 083', 'Sample Customer 083', 'Sample Customer 083', '01710000083', NULL, 'customer083@example.com', '183 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 125000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(84, 'CUS-000084', 4, NULL, 'Sample Customer 084', 'Sample Customer 084', 'Sample Customer 084', '01710000084', NULL, 'customer084@example.com', '184 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 150000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(85, 'CUS-000085', 1, NULL, 'Sample Customer 085', 'Sample Customer 085', 'Sample Customer 085', '01710000085', NULL, 'customer085@example.com', '185 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 50000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(86, 'CUS-000086', 2, NULL, 'Sample Customer 086', 'Sample Customer 086', 'Sample Customer 086', '01710000086', NULL, 'customer086@example.com', '186 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 75000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(87, 'CUS-000087', 3, NULL, 'Sample Customer 087', 'Sample Customer 087', 'Sample Customer 087', '01710000087', NULL, 'customer087@example.com', '187 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 100000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(88, 'CUS-000088', 4, NULL, 'Sample Customer 088', 'Sample Customer 088', 'Sample Customer 088', '01710000088', NULL, 'customer088@example.com', '188 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 125000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(89, 'CUS-000089', 1, NULL, 'Sample Customer 089', 'Sample Customer 089', 'Sample Customer 089', '01710000089', NULL, 'customer089@example.com', '189 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 150000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(90, 'CUS-000090', 2, NULL, 'Sample Customer 090', 'Sample Customer 090', 'Sample Customer 090', '01710000090', NULL, 'customer090@example.com', '190 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 50000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(91, 'CUS-000091', 3, NULL, 'Sample Customer 091', 'Sample Customer 091', 'Sample Customer 091', '01710000091', NULL, 'customer091@example.com', '191 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 75000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(92, 'CUS-000092', 4, NULL, 'Sample Customer 092', 'Sample Customer 092', 'Sample Customer 092', '01710000092', NULL, 'customer092@example.com', '192 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 100000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(93, 'CUS-000093', 1, NULL, 'Sample Customer 093', 'Sample Customer 093', 'Sample Customer 093', '01710000093', NULL, 'customer093@example.com', '193 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 125000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(94, 'CUS-000094', 2, NULL, 'Sample Customer 094', 'Sample Customer 094', 'Sample Customer 094', '01710000094', NULL, 'customer094@example.com', '194 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 150000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(95, 'CUS-000095', 3, NULL, 'Sample Customer 095', 'Sample Customer 095', 'Sample Customer 095', '01710000095', NULL, 'customer095@example.com', '195 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 50000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(96, 'CUS-000096', 4, NULL, 'Sample Customer 096', 'Sample Customer 096', 'Sample Customer 096', '01710000096', NULL, 'customer096@example.com', '196 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 75000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(97, 'CUS-000097', 1, NULL, 'Sample Customer 097', 'Sample Customer 097', 'Sample Customer 097', '01710000097', NULL, 'customer097@example.com', '197 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 100000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(98, 'CUS-000098', 2, NULL, 'Sample Customer 098', 'Sample Customer 098', 'Sample Customer 098', '01710000098', NULL, 'customer098@example.com', '198 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 125000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(99, 'CUS-000099', 3, NULL, 'Sample Customer 099', 'Sample Customer 099', 'Sample Customer 099', '01710000099', NULL, 'customer099@example.com', '199 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 150000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(100, 'CUS-000100', 4, NULL, 'Sample Customer 100', 'Sample Customer 100', 'Sample Customer 100', '01710000100', NULL, 'customer100@example.com', '200 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 50000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(101, 'CUS-000101', 1, NULL, 'Sample Customer 101', 'Sample Customer 101', 'Sample Customer 101', '01710000101', NULL, 'customer101@example.com', '201 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 75000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(102, 'CUS-000102', 2, NULL, 'Sample Customer 102', 'Sample Customer 102', 'Sample Customer 102', '01710000102', NULL, 'customer102@example.com', '202 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 100000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(103, 'CUS-000103', 3, NULL, 'Sample Customer 103', 'Sample Customer 103', 'Sample Customer 103', '01710000103', NULL, 'customer103@example.com', '203 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 125000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(104, 'CUS-000104', 4, NULL, 'Sample Customer 104', 'Sample Customer 104', 'Sample Customer 104', '01710000104', NULL, 'customer104@example.com', '204 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 150000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(105, 'CUS-000105', 1, NULL, 'Sample Customer 105', 'Sample Customer 105', 'Sample Customer 105', '01710000105', NULL, 'customer105@example.com', '205 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 50000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(106, 'CUS-000106', 2, NULL, 'Sample Customer 106', 'Sample Customer 106', 'Sample Customer 106', '01710000106', NULL, 'customer106@example.com', '206 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 75000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(107, 'CUS-000107', 3, NULL, 'Sample Customer 107', 'Sample Customer 107', 'Sample Customer 107', '01710000107', NULL, 'customer107@example.com', '207 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 100000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(108, 'CUS-000108', 4, NULL, 'Sample Customer 108', 'Sample Customer 108', 'Sample Customer 108', '01710000108', NULL, 'customer108@example.com', '208 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 125000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(109, 'CUS-000109', 1, NULL, 'Sample Customer 109', 'Sample Customer 109', 'Sample Customer 109', '01710000109', NULL, 'customer109@example.com', '209 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 150000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(110, 'CUS-000110', 2, NULL, 'Sample Customer 110', 'Sample Customer 110', 'Sample Customer 110', '01710000110', NULL, 'customer110@example.com', '210 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 50000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(111, 'CUS-000111', 3, NULL, 'Sample Customer 111', 'Sample Customer 111', 'Sample Customer 111', '01710000111', NULL, 'customer111@example.com', '211 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 75000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(112, 'CUS-000112', 4, NULL, 'Sample Customer 112', 'Sample Customer 112', 'Sample Customer 112', '01710000112', NULL, 'customer112@example.com', '212 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 100000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(113, 'CUS-000113', 1, NULL, 'Sample Customer 113', 'Sample Customer 113', 'Sample Customer 113', '01710000113', NULL, 'customer113@example.com', '213 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 125000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(114, 'CUS-000114', 2, NULL, 'Sample Customer 114', 'Sample Customer 114', 'Sample Customer 114', '01710000114', NULL, 'customer114@example.com', '214 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 150000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(115, 'CUS-000115', 3, NULL, 'Sample Customer 115', 'Sample Customer 115', 'Sample Customer 115', '01710000115', NULL, 'customer115@example.com', '215 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 50000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(116, 'CUS-000116', 4, NULL, 'Sample Customer 116', 'Sample Customer 116', 'Sample Customer 116', '01710000116', NULL, 'customer116@example.com', '216 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 75000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(117, 'CUS-000117', 1, NULL, 'Sample Customer 117', 'Sample Customer 117', 'Sample Customer 117', '01710000117', NULL, 'customer117@example.com', '217 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 100000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(118, 'CUS-000118', 2, NULL, 'Sample Customer 118', 'Sample Customer 118', 'Sample Customer 118', '01710000118', NULL, 'customer118@example.com', '218 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 125000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(119, 'CUS-000119', 3, NULL, 'Sample Customer 119', 'Sample Customer 119', 'Sample Customer 119', '01710000119', NULL, 'customer119@example.com', '219 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 150000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(120, 'CUS-000120', 4, NULL, 'Sample Customer 120', 'Sample Customer 120', 'Sample Customer 120', '01710000120', NULL, 'customer120@example.com', '220 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 50000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(121, 'CUS-000121', 1, NULL, 'Sample Customer 121', 'Sample Customer 121', 'Sample Customer 121', '01710000121', NULL, 'customer121@example.com', '221 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 75000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(122, 'CUS-000122', 2, NULL, 'Sample Customer 122', 'Sample Customer 122', 'Sample Customer 122', '01710000122', NULL, 'customer122@example.com', '222 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 100000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(123, 'CUS-000123', 3, NULL, 'Sample Customer 123', 'Sample Customer 123', 'Sample Customer 123', '01710000123', NULL, 'customer123@example.com', '223 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 125000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(124, 'CUS-000124', 4, NULL, 'Sample Customer 124', 'Sample Customer 124', 'Sample Customer 124', '01710000124', NULL, 'customer124@example.com', '224 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 150000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(125, 'CUS-000125', 1, NULL, 'Sample Customer 125', 'Sample Customer 125', 'Sample Customer 125', '01710000125', NULL, 'customer125@example.com', '225 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 50000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(126, 'CUS-000126', 2, NULL, 'Sample Customer 126', 'Sample Customer 126', 'Sample Customer 126', '01710000126', NULL, 'customer126@example.com', '226 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 75000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(127, 'CUS-000127', 3, NULL, 'Sample Customer 127', 'Sample Customer 127', 'Sample Customer 127', '01710000127', NULL, 'customer127@example.com', '227 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 100000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(128, 'CUS-000128', 4, NULL, 'Sample Customer 128', 'Sample Customer 128', 'Sample Customer 128', '01710000128', NULL, 'customer128@example.com', '228 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 125000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(129, 'CUS-000129', 1, NULL, 'Sample Customer 129', 'Sample Customer 129', 'Sample Customer 129', '01710000129', NULL, 'customer129@example.com', '229 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 150000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(130, 'CUS-000130', 2, NULL, 'Sample Customer 130', 'Sample Customer 130', 'Sample Customer 130', '01710000130', NULL, 'customer130@example.com', '230 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 50000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(131, 'CUS-000131', 3, NULL, 'Sample Customer 131', 'Sample Customer 131', 'Sample Customer 131', '01710000131', NULL, 'customer131@example.com', '231 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 75000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(132, 'CUS-000132', 4, NULL, 'Sample Customer 132', 'Sample Customer 132', 'Sample Customer 132', '01710000132', NULL, 'customer132@example.com', '232 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 100000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(133, 'CUS-000133', 1, NULL, 'Sample Customer 133', 'Sample Customer 133', 'Sample Customer 133', '01710000133', NULL, 'customer133@example.com', '233 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 125000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(134, 'CUS-000134', 2, NULL, 'Sample Customer 134', 'Sample Customer 134', 'Sample Customer 134', '01710000134', NULL, 'customer134@example.com', '234 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 150000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(135, 'CUS-000135', 3, NULL, 'Sample Customer 135', 'Sample Customer 135', 'Sample Customer 135', '01710000135', NULL, 'customer135@example.com', '235 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 50000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(136, 'CUS-000136', 4, NULL, 'Sample Customer 136', 'Sample Customer 136', 'Sample Customer 136', '01710000136', NULL, 'customer136@example.com', '236 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 75000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(137, 'CUS-000137', 1, NULL, 'Sample Customer 137', 'Sample Customer 137', 'Sample Customer 137', '01710000137', NULL, 'customer137@example.com', '237 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 100000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(138, 'CUS-000138', 2, NULL, 'Sample Customer 138', 'Sample Customer 138', 'Sample Customer 138', '01710000138', NULL, 'customer138@example.com', '238 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 125000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(139, 'CUS-000139', 3, NULL, 'Sample Customer 139', 'Sample Customer 139', 'Sample Customer 139', '01710000139', NULL, 'customer139@example.com', '239 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 150000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(140, 'CUS-000140', 4, NULL, 'Sample Customer 140', 'Sample Customer 140', 'Sample Customer 140', '01710000140', NULL, 'customer140@example.com', '240 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 50000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(141, 'CUS-000141', 1, NULL, 'Sample Customer 141', 'Sample Customer 141', 'Sample Customer 141', '01710000141', NULL, 'customer141@example.com', '241 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 75000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(142, 'CUS-000142', 2, NULL, 'Sample Customer 142', 'Sample Customer 142', 'Sample Customer 142', '01710000142', NULL, 'customer142@example.com', '242 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 100000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(143, 'CUS-000143', 3, NULL, 'Sample Customer 143', 'Sample Customer 143', 'Sample Customer 143', '01710000143', NULL, 'customer143@example.com', '243 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 125000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(144, 'CUS-000144', 4, NULL, 'Sample Customer 144', 'Sample Customer 144', 'Sample Customer 144', '01710000144', NULL, 'customer144@example.com', '244 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 150000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(145, 'CUS-000145', 1, NULL, 'Sample Customer 145', 'Sample Customer 145', 'Sample Customer 145', '01710000145', NULL, 'customer145@example.com', '245 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 50000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(146, 'CUS-000146', 2, NULL, 'Sample Customer 146', 'Sample Customer 146', 'Sample Customer 146', '01710000146', NULL, 'customer146@example.com', '246 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 75000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(147, 'CUS-000147', 3, NULL, 'Sample Customer 147', 'Sample Customer 147', 'Sample Customer 147', '01710000147', NULL, 'customer147@example.com', '247 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 100000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(148, 'CUS-000148', 4, NULL, 'Sample Customer 148', 'Sample Customer 148', 'Sample Customer 148', '01710000148', NULL, 'customer148@example.com', '248 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 125000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(149, 'CUS-000149', 1, NULL, 'Sample Customer 149', 'Sample Customer 149', 'Sample Customer 149', '01710000149', NULL, 'customer149@example.com', '249 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 150000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(150, 'CUS-000150', 2, NULL, 'Sample Customer 150', 'Sample Customer 150', 'Sample Customer 150', '01710000150', NULL, 'customer150@example.com', '250 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 50000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(151, 'CUS-000151', 3, NULL, 'Sample Customer 151', 'Sample Customer 151', 'Sample Customer 151', '01710000151', NULL, 'customer151@example.com', '251 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 75000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(152, 'CUS-000152', 4, NULL, 'Sample Customer 152', 'Sample Customer 152', 'Sample Customer 152', '01710000152', NULL, 'customer152@example.com', '252 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 100000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(153, 'CUS-000153', 1, NULL, 'Sample Customer 153', 'Sample Customer 153', 'Sample Customer 153', '01710000153', NULL, 'customer153@example.com', '253 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 125000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(154, 'CUS-000154', 2, NULL, 'Sample Customer 154', 'Sample Customer 154', 'Sample Customer 154', '01710000154', NULL, 'customer154@example.com', '254 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 150000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(155, 'CUS-000155', 3, NULL, 'Sample Customer 155', 'Sample Customer 155', 'Sample Customer 155', '01710000155', NULL, 'customer155@example.com', '255 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 50000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL);
INSERT INTO `customers` (`id`, `code`, `customer_group_id`, `market_hierarchy_id`, `name`, `company_name`, `contact_person`, `phone`, `alternate_phone`, `email`, `address`, `city`, `district`, `country`, `opening_balance`, `opening_balance_type`, `credit_limit`, `payment_terms_days`, `tax_number`, `notes`, `is_active`, `created_at`, `updated_at`, `deleted_at`) VALUES
(156, 'CUS-000156', 4, NULL, 'Sample Customer 156', 'Sample Customer 156', 'Sample Customer 156', '01710000156', NULL, 'customer156@example.com', '256 Demo Street', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'receivable', 75000.00, 30, NULL, 'Sample dashboard customer', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `customer_groups`
--

CREATE TABLE `customer_groups` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(150) NOT NULL,
  `code` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customer_groups`
--

INSERT INTO `customer_groups` (`id`, `name`, `code`, `description`, `is_active`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Retail', 'CG-RET', 'Retail customers', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(2, 'Wholesale', 'CG-WHO', 'Wholesale customers', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(3, 'Corporate', 'CG-COR', 'Corporate customers', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(4, 'Dealer', 'CG-DEA', 'Authorized dealers', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `customer_ledger_entries`
--

CREATE TABLE `customer_ledger_entries` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `transaction_date` date NOT NULL,
  `entry_type` varchar(50) NOT NULL,
  `reference_no` varchar(100) DEFAULT NULL,
  `source_type` varchar(100) DEFAULT NULL,
  `source_id` bigint(20) UNSIGNED DEFAULT NULL,
  `debit` decimal(18,2) NOT NULL DEFAULT 0.00,
  `credit` decimal(18,2) NOT NULL DEFAULT 0.00,
  `description` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customer_ledger_entries`
--

INSERT INTO `customer_ledger_entries` (`id`, `customer_id`, `transaction_date`, `entry_type`, `reference_no`, `source_type`, `source_id`, `debit`, `credit`, `description`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 2, '2026-03-05', 'sale', 'INV-000001', 'sale', 1, 120000.00, 0.00, 'Sale invoice', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(2, 3, '2026-03-12', 'sale', 'INV-000002', 'sale', 2, 120000.00, 0.00, 'Sale invoice', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(3, 4, '2026-03-19', 'sale', 'INV-000003', 'sale', 3, 120000.00, 0.00, 'Sale invoice', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(4, 5, '2026-03-26', 'sale', 'INV-000004', 'sale', 4, 120000.00, 0.00, 'Sale invoice', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(5, 6, '2026-04-05', 'sale', 'INV-000005', 'sale', 5, 135197.00, 0.00, 'Sale invoice', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(6, 7, '2026-04-12', 'sale', 'INV-000006', 'sale', 6, 93937.00, 0.00, 'Sale invoice', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(7, 8, '2026-04-19', 'sale', 'INV-000007', 'sale', 7, 174208.00, 0.00, 'Sale invoice', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(8, 9, '2026-04-26', 'sale', 'INV-000008', 'sale', 8, 121658.00, 0.00, 'Sale invoice', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(9, 10, '2026-05-05', 'sale', 'INV-000009', 'sale', 9, 152500.00, 0.00, 'Sale invoice', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(10, 11, '2026-05-12', 'sale', 'INV-000010', 'sale', 10, 152500.00, 0.00, 'Sale invoice', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(11, 12, '2026-05-19', 'sale', 'INV-000011', 'sale', 11, 152500.00, 0.00, 'Sale invoice', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(12, 13, '2026-05-26', 'sale', 'INV-000012', 'sale', 12, 152500.00, 0.00, 'Sale invoice', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(13, 14, '2026-06-05', 'sale', 'INV-000013', 'sale', 13, 143750.00, 0.00, 'Sale invoice', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(14, 15, '2026-06-12', 'sale', 'INV-000014', 'sale', 14, 143750.00, 0.00, 'Sale invoice', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(15, 16, '2026-06-19', 'sale', 'INV-000015', 'sale', 15, 143750.00, 0.00, 'Sale invoice', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(16, 17, '2026-06-26', 'sale', 'INV-000016', 'sale', 16, 143750.00, 0.00, 'Sale invoice', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(17, 18, '2026-07-05', 'sale', 'INV-000017', 'sale', 17, 160000.00, 0.00, 'Sale invoice', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(18, 19, '2026-07-12', 'sale', 'INV-000018', 'sale', 18, 160000.00, 0.00, 'Sale invoice', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(19, 20, '2026-07-19', 'sale', 'INV-000019', 'sale', 19, 160000.00, 0.00, 'Sale invoice', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(20, 21, '2026-07-26', 'sale', 'INV-000020', 'sale', 20, 160000.00, 0.00, 'Sale invoice', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(21, 22, '2026-08-01', 'sale', 'INV-000021', 'sale', 21, 32835.00, 0.00, 'Sale invoice', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(22, 23, '2026-08-02', 'sale', 'INV-000022', 'sale', 22, 32835.00, 0.00, 'Sale invoice', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(23, 24, '2026-08-03', 'sale', 'INV-000023', 'sale', 23, 32835.00, 0.00, 'Sale invoice', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(24, 25, '2026-08-04', 'sale', 'INV-000024', 'sale', 24, 32835.00, 0.00, 'Sale invoice', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(25, 26, '2026-08-05', 'sale', 'INV-000025', 'sale', 25, 32835.00, 0.00, 'Sale invoice', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(26, 27, '2026-08-06', 'sale', 'INV-000026', 'sale', 26, 32835.00, 0.00, 'Sale invoice', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(27, 28, '2026-08-07', 'sale', 'INV-000027', 'sale', 27, 32835.00, 0.00, 'Sale invoice', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(28, 29, '2026-08-08', 'sale', 'INV-000028', 'sale', 28, 32835.00, 0.00, 'Sale invoice', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(29, 30, '2026-08-09', 'sale', 'INV-000029', 'sale', 29, 32835.00, 0.00, 'Sale invoice', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(30, 31, '2026-08-10', 'sale', 'INV-000030', 'sale', 30, 32835.00, 0.00, 'Sale invoice', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(31, 32, '2026-08-11', 'sale', 'INV-000031', 'sale', 31, 32835.00, 0.00, 'Sale invoice', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(32, 33, '2026-08-12', 'sale', 'INV-000032', 'sale', 32, 32835.00, 0.00, 'Sale invoice', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(33, 34, '2026-08-13', 'sale', 'INV-000033', 'sale', 33, 32835.00, 0.00, 'Sale invoice', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(34, 35, '2026-08-14', 'sale', 'INV-000034', 'sale', 34, 32835.00, 0.00, 'Sale invoice', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(35, 36, '2026-08-15', 'sale', 'INV-000035', 'sale', 35, 32835.00, 0.00, 'Sale invoice', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(36, 37, '2026-08-16', 'sale', 'INV-000036', 'sale', 36, 32835.00, 0.00, 'Sale invoice', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(37, 38, '2026-08-18', 'sale', 'INV-000037', 'sale', 37, 32835.00, 0.00, 'Sale invoice', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(38, 39, '2026-08-20', 'sale', 'INV-000038', 'sale', 38, 32835.00, 0.00, 'Sale invoice', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(39, 40, '2026-08-24', 'sale', 'INV-000039', 'sale', 39, 32835.00, 0.00, 'Sale invoice', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(40, 41, '2026-08-27', 'sale', 'INV-000040', 'sale', 40, 32845.00, 0.00, 'Sale invoice', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(41, 2, '2026-08-28', 'sale', 'INV-000041', 'sale', 41, 15000.00, 0.00, 'Sale invoice', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(42, 3, '2026-08-28', 'sale', 'INV-000042', 'sale', 42, 13540.00, 0.00, 'Sale invoice', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(43, 2, '2026-03-05', 'payment', 'RCPT-000001', 'customer_payment', 1, 0.00, 108882.27, 'Customer payment', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(44, 3, '2026-03-12', 'payment', 'RCPT-000002', 'customer_payment', 2, 0.00, 108882.27, 'Customer payment', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(45, 4, '2026-03-19', 'payment', 'RCPT-000003', 'customer_payment', 3, 0.00, 108882.27, 'Customer payment', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(46, 5, '2026-03-26', 'payment', 'RCPT-000004', 'customer_payment', 4, 0.00, 108882.27, 'Customer payment', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(47, 6, '2026-04-05', 'payment', 'RCPT-000005', 'customer_payment', 5, 0.00, 122671.30, 'Customer payment', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(48, 7, '2026-04-12', 'payment', 'RCPT-000006', 'customer_payment', 6, 0.00, 85233.95, 'Customer payment', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(49, 8, '2026-04-19', 'payment', 'RCPT-000007', 'customer_payment', 7, 0.00, 158068.02, 'Customer payment', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(50, 9, '2026-04-26', 'payment', 'RCPT-000008', 'customer_payment', 8, 0.00, 110386.66, 'Customer payment', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(51, 10, '2026-05-05', 'payment', 'RCPT-000009', 'customer_payment', 9, 0.00, 138371.22, 'Customer payment', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(52, 11, '2026-05-12', 'payment', 'RCPT-000010', 'customer_payment', 10, 0.00, 138371.22, 'Customer payment', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(53, 12, '2026-05-19', 'payment', 'RCPT-000011', 'customer_payment', 11, 0.00, 138371.22, 'Customer payment', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(54, 13, '2026-05-26', 'payment', 'RCPT-000012', 'customer_payment', 12, 0.00, 138371.22, 'Customer payment', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(55, 14, '2026-06-05', 'payment', 'RCPT-000013', 'customer_payment', 13, 0.00, 130431.89, 'Customer payment', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(56, 15, '2026-06-12', 'payment', 'RCPT-000014', 'customer_payment', 14, 0.00, 130431.89, 'Customer payment', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(57, 16, '2026-06-19', 'payment', 'RCPT-000015', 'customer_payment', 15, 0.00, 130431.89, 'Customer payment', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(58, 17, '2026-06-26', 'payment', 'RCPT-000016', 'customer_payment', 16, 0.00, 130431.89, 'Customer payment', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(59, 18, '2026-07-05', 'payment', 'RCPT-000017', 'customer_payment', 17, 0.00, 145176.36, 'Customer payment', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(60, 19, '2026-07-12', 'payment', 'RCPT-000018', 'customer_payment', 18, 0.00, 145176.36, 'Customer payment', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(61, 20, '2026-07-19', 'payment', 'RCPT-000019', 'customer_payment', 19, 0.00, 145176.36, 'Customer payment', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(62, 21, '2026-07-26', 'payment', 'RCPT-000020', 'customer_payment', 20, 0.00, 145176.36, 'Customer payment', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(63, 22, '2026-08-01', 'payment', 'RCPT-000021', 'customer_payment', 21, 0.00, 29792.91, 'Customer payment', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(64, 23, '2026-08-02', 'payment', 'RCPT-000022', 'customer_payment', 22, 0.00, 29792.91, 'Customer payment', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(65, 24, '2026-08-03', 'payment', 'RCPT-000023', 'customer_payment', 23, 0.00, 29792.91, 'Customer payment', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(66, 25, '2026-08-04', 'payment', 'RCPT-000024', 'customer_payment', 24, 0.00, 29792.91, 'Customer payment', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(67, 26, '2026-08-05', 'payment', 'RCPT-000025', 'customer_payment', 25, 0.00, 29792.91, 'Customer payment', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(68, 27, '2026-08-06', 'payment', 'RCPT-000026', 'customer_payment', 26, 0.00, 29792.91, 'Customer payment', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(69, 28, '2026-08-07', 'payment', 'RCPT-000027', 'customer_payment', 27, 0.00, 29792.91, 'Customer payment', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(70, 29, '2026-08-08', 'payment', 'RCPT-000028', 'customer_payment', 28, 0.00, 29792.91, 'Customer payment', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(71, 30, '2026-08-09', 'payment', 'RCPT-000029', 'customer_payment', 29, 0.00, 29792.91, 'Customer payment', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(72, 31, '2026-08-10', 'payment', 'RCPT-000030', 'customer_payment', 30, 0.00, 29792.91, 'Customer payment', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(73, 32, '2026-08-11', 'payment', 'RCPT-000031', 'customer_payment', 31, 0.00, 29792.91, 'Customer payment', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(74, 33, '2026-08-12', 'payment', 'RCPT-000032', 'customer_payment', 32, 0.00, 29792.91, 'Customer payment', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(75, 34, '2026-08-13', 'payment', 'RCPT-000033', 'customer_payment', 33, 0.00, 29792.91, 'Customer payment', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(76, 35, '2026-08-14', 'payment', 'RCPT-000034', 'customer_payment', 34, 0.00, 29792.91, 'Customer payment', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(77, 36, '2026-08-15', 'payment', 'RCPT-000035', 'customer_payment', 35, 0.00, 29792.91, 'Customer payment', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(78, 37, '2026-08-16', 'payment', 'RCPT-000036', 'customer_payment', 36, 0.00, 29792.91, 'Customer payment', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(79, 38, '2026-08-18', 'payment', 'RCPT-000037', 'customer_payment', 37, 0.00, 29792.91, 'Customer payment', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(80, 39, '2026-08-20', 'payment', 'RCPT-000038', 'customer_payment', 38, 0.00, 29792.91, 'Customer payment', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(81, 40, '2026-08-24', 'payment', 'RCPT-000039', 'customer_payment', 39, 0.00, 29792.91, 'Customer payment', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(82, 41, '2026-08-27', 'payment', 'RCPT-000040', 'customer_payment', 40, 0.00, 29801.98, 'Customer payment', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(83, 2, '2026-08-28', 'payment', 'RCPT-000041', 'customer_payment', 41, 0.00, 13610.28, 'Customer payment', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(84, 3, '2026-08-28', 'payment', 'RCPT-000042', 'customer_payment', 42, 0.00, 12285.56, 'Customer payment', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(85, 5, '2026-09-25', 'payment', 'REC-2026-000043', 'customer_payment', 43, 0.00, 10000.00, 'Customer payment', 1, '2026-09-26 19:04:07', '2026-09-26 19:04:07'),
(86, 4, '2026-09-25', 'sale_return', 'SRT-2026-000003', 'sale_return', 3, 0.00, 240.02, 'Sales return', 1, '2026-09-26 19:20:21', '2026-09-26 19:20:21'),
(87, 5, '2026-09-28', 'sale', 'SAL-2026-000057', 'sale', 57, 152.00, 0.00, 'Sale invoice', 3, '2026-09-28 17:09:49', '2026-09-28 17:09:49'),
(88, 5, '2026-09-28', 'payment', 'REC-2026-000044', 'customer_payment', 44, 0.00, 100.00, 'Customer payment', 3, '2026-09-28 17:10:37', '2026-09-28 17:10:37'),
(89, 5, '2026-09-28', 'payment', 'REC-2026-000045', 'customer_payment', 45, 0.00, 52.00, 'Customer payment', 3, '2026-09-28 17:49:03', '2026-09-28 17:49:03'),
(90, 5, '2026-09-29', 'sale', 'SAL-2026-000061', 'sale', 61, 345.00, 0.00, 'Sale invoice', 3, '2026-09-29 14:03:17', '2026-09-29 14:03:17'),
(91, 5, '2026-09-29', 'payment', 'REC-2026-000046', 'customer_payment', 46, 0.00, 200.00, 'Customer payment', 3, '2026-09-29 14:04:11', '2026-09-29 14:04:11');

-- --------------------------------------------------------

--
-- Table structure for table `customer_payments`
--

CREATE TABLE `customer_payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `payment_no` varchar(50) NOT NULL,
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `sale_id` bigint(20) UNSIGNED DEFAULT NULL,
  `account_id` bigint(20) UNSIGNED NOT NULL,
  `payment_date` date NOT NULL,
  `amount` decimal(18,2) NOT NULL,
  `method` varchar(30) NOT NULL DEFAULT 'cash',
  `reference` varchar(100) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'posted',
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customer_payments`
--

INSERT INTO `customer_payments` (`id`, `payment_no`, `customer_id`, `sale_id`, `account_id`, `payment_date`, `amount`, `method`, `reference`, `notes`, `status`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'RCPT-000001', 2, 1, 1, '2026-03-05', 108882.27, 'cash', NULL, 'Sample receipt', 'posted', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(2, 'RCPT-000002', 3, 2, 2, '2026-03-12', 108882.27, 'bank', NULL, 'Sample receipt', 'posted', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(3, 'RCPT-000003', 4, 3, 1, '2026-03-19', 108882.27, 'cash', NULL, 'Sample receipt', 'posted', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(4, 'RCPT-000004', 5, 4, 2, '2026-03-26', 108882.27, 'bank', NULL, 'Sample receipt', 'posted', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(5, 'RCPT-000005', 6, 5, 1, '2026-04-05', 122671.30, 'cash', NULL, 'Sample receipt', 'posted', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(6, 'RCPT-000006', 7, 6, 2, '2026-04-12', 85233.95, 'bank', NULL, 'Sample receipt', 'posted', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(7, 'RCPT-000007', 8, 7, 1, '2026-04-19', 158068.02, 'cash', NULL, 'Sample receipt', 'posted', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(8, 'RCPT-000008', 9, 8, 2, '2026-04-26', 110386.66, 'bank', NULL, 'Sample receipt', 'posted', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(9, 'RCPT-000009', 10, 9, 1, '2026-05-05', 138371.22, 'cash', NULL, 'Sample receipt', 'posted', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(10, 'RCPT-000010', 11, 10, 2, '2026-05-12', 138371.22, 'bank', NULL, 'Sample receipt', 'posted', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(11, 'RCPT-000011', 12, 11, 1, '2026-05-19', 138371.22, 'cash', NULL, 'Sample receipt', 'posted', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(12, 'RCPT-000012', 13, 12, 2, '2026-05-26', 138371.22, 'bank', NULL, 'Sample receipt', 'posted', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(13, 'RCPT-000013', 14, 13, 1, '2026-06-05', 130431.89, 'cash', NULL, 'Sample receipt', 'posted', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(14, 'RCPT-000014', 15, 14, 2, '2026-06-12', 130431.89, 'bank', NULL, 'Sample receipt', 'posted', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(15, 'RCPT-000015', 16, 15, 1, '2026-06-19', 130431.89, 'cash', NULL, 'Sample receipt', 'posted', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(16, 'RCPT-000016', 17, 16, 2, '2026-06-26', 130431.89, 'bank', NULL, 'Sample receipt', 'posted', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(17, 'RCPT-000017', 18, 17, 1, '2026-07-05', 145176.36, 'cash', NULL, 'Sample receipt', 'posted', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(18, 'RCPT-000018', 19, 18, 2, '2026-07-12', 145176.36, 'bank', NULL, 'Sample receipt', 'posted', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(19, 'RCPT-000019', 20, 19, 1, '2026-07-19', 145176.36, 'cash', NULL, 'Sample receipt', 'posted', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(20, 'RCPT-000020', 21, 20, 2, '2026-07-26', 145176.36, 'bank', NULL, 'Sample receipt', 'posted', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(21, 'RCPT-000021', 22, 21, 1, '2026-08-01', 29792.91, 'cash', NULL, 'Sample receipt', 'posted', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(22, 'RCPT-000022', 23, 22, 2, '2026-08-02', 29792.91, 'bank', NULL, 'Sample receipt', 'posted', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(23, 'RCPT-000023', 24, 23, 1, '2026-08-03', 29792.91, 'cash', NULL, 'Sample receipt', 'posted', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(24, 'RCPT-000024', 25, 24, 2, '2026-08-04', 29792.91, 'bank', NULL, 'Sample receipt', 'posted', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(25, 'RCPT-000025', 26, 25, 1, '2026-08-05', 29792.91, 'cash', NULL, 'Sample receipt', 'posted', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(26, 'RCPT-000026', 27, 26, 2, '2026-08-06', 29792.91, 'bank', NULL, 'Sample receipt', 'posted', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(27, 'RCPT-000027', 28, 27, 1, '2026-08-07', 29792.91, 'cash', NULL, 'Sample receipt', 'posted', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(28, 'RCPT-000028', 29, 28, 2, '2026-08-08', 29792.91, 'bank', NULL, 'Sample receipt', 'posted', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(29, 'RCPT-000029', 30, 29, 1, '2026-08-09', 29792.91, 'cash', NULL, 'Sample receipt', 'posted', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(30, 'RCPT-000030', 31, 30, 2, '2026-08-10', 29792.91, 'bank', NULL, 'Sample receipt', 'posted', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(31, 'RCPT-000031', 32, 31, 1, '2026-08-11', 29792.91, 'cash', NULL, 'Sample receipt', 'posted', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(32, 'RCPT-000032', 33, 32, 2, '2026-08-12', 29792.91, 'bank', NULL, 'Sample receipt', 'posted', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(33, 'RCPT-000033', 34, 33, 1, '2026-08-13', 29792.91, 'cash', NULL, 'Sample receipt', 'posted', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(34, 'RCPT-000034', 35, 34, 2, '2026-08-14', 29792.91, 'bank', NULL, 'Sample receipt', 'posted', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(35, 'RCPT-000035', 36, 35, 1, '2026-08-15', 29792.91, 'cash', NULL, 'Sample receipt', 'posted', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(36, 'RCPT-000036', 37, 36, 2, '2026-08-16', 29792.91, 'bank', NULL, 'Sample receipt', 'posted', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(37, 'RCPT-000037', 38, 37, 1, '2026-08-18', 29792.91, 'cash', NULL, 'Sample receipt', 'posted', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(38, 'RCPT-000038', 39, 38, 2, '2026-08-20', 29792.91, 'bank', NULL, 'Sample receipt', 'posted', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(39, 'RCPT-000039', 40, 39, 1, '2026-08-24', 29792.91, 'cash', NULL, 'Sample receipt', 'posted', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(40, 'RCPT-000040', 41, 40, 2, '2026-08-27', 29801.98, 'bank', NULL, 'Sample receipt', 'posted', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(41, 'RCPT-000041', 2, 41, 1, '2026-08-28', 13610.28, 'cash', NULL, 'Sample receipt', 'posted', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(42, 'RCPT-000042', 3, 42, 2, '2026-08-28', 12285.56, 'bank', NULL, 'Sample receipt', 'posted', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(43, 'REC-2026-000043', 5, NULL, 2, '2026-09-25', 10000.00, 'cheque', '555', NULL, 'posted', 1, '2026-09-26 19:04:07', '2026-09-26 19:04:07'),
(44, 'REC-2026-000044', 5, 57, 2, '2026-09-28', 100.00, 'bank', '01', 'Notes', 'posted', 3, '2026-09-28 17:10:37', '2026-09-28 17:10:37'),
(45, 'REC-2026-000045', 5, 57, 1, '2026-09-28', 52.00, 'cash', NULL, NULL, 'posted', 3, '2026-09-28 17:49:03', '2026-09-28 17:49:03'),
(46, 'REC-2026-000046', 5, 61, 2, '2026-09-29', 200.00, 'bank', NULL, NULL, 'posted', 3, '2026-09-29 14:04:11', '2026-09-29 14:04:11');

-- --------------------------------------------------------

--
-- Table structure for table `document_attachments`
--

CREATE TABLE `document_attachments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `attachable_type` varchar(60) NOT NULL,
  `attachable_id` bigint(20) UNSIGNED NOT NULL,
  `original_name` varchar(255) NOT NULL,
  `stored_path` varchar(255) NOT NULL,
  `mime_type` varchar(120) DEFAULT NULL,
  `size` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `uploaded_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_no` varchar(50) NOT NULL,
  `name` varchar(180) NOT NULL,
  `department` varchar(120) DEFAULT NULL,
  `designation` varchar(120) DEFAULT NULL,
  `joining_date` date DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `basic_salary` decimal(18,2) NOT NULL DEFAULT 0.00,
  `allowance` decimal(18,2) NOT NULL DEFAULT 0.00,
  `standard_deduction` decimal(18,2) NOT NULL DEFAULT 0.00,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`id`, `employee_no`, `name`, `department`, `designation`, `joining_date`, `phone`, `email`, `address`, `basic_salary`, `allowance`, `standard_deduction`, `is_active`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'EMP-0001', 'Employee 01', 'Accounts', 'Officer', '2025-01-01', '01930000001', 'employee1@example.com', 'Dhaka', 19000.00, 2500.00, 500.00, 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(2, 'EMP-0002', 'Employee 02', 'Warehouse', 'Assistant', '2025-01-01', '01930000002', 'employee2@example.com', 'Dhaka', 20000.00, 2500.00, 500.00, 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(3, 'EMP-0003', 'Employee 03', 'Admin', 'Manager', '2025-01-01', '01930000003', 'employee3@example.com', 'Dhaka', 21000.00, 2500.00, 500.00, 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(4, 'EMP-0004', 'Employee 04', 'Sales', 'Executive', '2025-01-01', '01930000004', 'employee4@example.com', 'Dhaka', 22000.00, 2500.00, 500.00, 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(5, 'EMP-0005', 'Employee 05', 'Accounts', 'Officer', '2025-01-01', '01930000005', 'employee5@example.com', 'Dhaka', 23000.00, 2500.00, 500.00, 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(6, 'EMP-0006', 'Employee 06', 'Warehouse', 'Assistant', '2025-01-01', '01930000006', 'employee6@example.com', 'Dhaka', 24000.00, 2500.00, 500.00, 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(7, 'EMP-0007', 'Employee 07', 'Admin', 'Manager', '2025-01-01', '01930000007', 'employee7@example.com', 'Dhaka', 25000.00, 2500.00, 500.00, 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(8, 'EMP-0008', 'Employee 08', 'Sales', 'Executive', '2025-01-01', '01930000008', 'employee8@example.com', 'Dhaka', 26000.00, 2500.00, 500.00, 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(9, 'EMP-0009', 'Employee 09', 'Accounts', 'Officer', '2025-01-01', '01930000009', 'employee9@example.com', 'Dhaka', 27000.00, 2500.00, 500.00, 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(10, 'EMP-0010', 'Employee 10', 'Warehouse', 'Assistant', '2025-01-01', '01930000010', 'employee10@example.com', 'Dhaka', 28000.00, 2500.00, 500.00, 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `exchange_rates`
--

CREATE TABLE `exchange_rates` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `currency_code` varchar(10) NOT NULL,
  `rate_date` date NOT NULL,
  `rate` decimal(18,6) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `expense_no` varchar(50) NOT NULL,
  `expense_date` date NOT NULL,
  `expense_category_id` bigint(20) UNSIGNED NOT NULL,
  `expense_account_id` bigint(20) UNSIGNED NOT NULL,
  `payment_account_id` bigint(20) UNSIGNED NOT NULL,
  `amount` decimal(18,2) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'draft',
  `reference` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `attachment` varchar(255) DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `posted_by` bigint(20) UNSIGNED DEFAULT NULL,
  `posted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `cost_center_id` bigint(20) UNSIGNED DEFAULT NULL,
  `project_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `expenses`
--

INSERT INTO `expenses` (`id`, `expense_no`, `expense_date`, `expense_category_id`, `expense_account_id`, `payment_account_id`, `amount`, `status`, `reference`, `description`, `attachment`, `created_by`, `posted_by`, `posted_at`, `created_at`, `updated_at`, `cost_center_id`, `project_id`) VALUES
(1, 'EXP-000001', '2026-08-03', 2, 16, 2, 25000.00, 'posted', NULL, 'Office Rent', NULL, 1, 1, '2026-08-03 06:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL, NULL),
(2, 'EXP-000002', '2026-08-06', 1, 15, 2, 35000.00, 'posted', NULL, 'Staff Salary Support', NULL, 1, 1, '2026-08-06 06:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL, NULL),
(3, 'EXP-000003', '2026-08-09', 3, 17, 1, 12500.00, 'posted', NULL, 'Electricity and Internet', NULL, 1, 1, '2026-08-09 06:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL, NULL),
(4, 'EXP-000004', '2026-08-12', 5, 19, 1, 8500.00, 'posted', NULL, 'Transportation', NULL, 1, 1, '2026-08-12 06:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL, NULL),
(5, 'EXP-000005', '2026-08-16', 5, 19, 1, 5540.00, 'posted', NULL, 'Office Supplies', NULL, 1, 1, '2026-08-16 06:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL, NULL),
(6, 'EXP-000006', '2026-08-20', 3, 17, 2, 4000.00, 'posted', NULL, 'Cloud and Utilities', NULL, 1, 1, '2026-08-20 06:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL, NULL),
(7, 'EXP-000007', '2026-08-24', 5, 19, 1, 3500.00, 'posted', NULL, 'Maintenance', NULL, 1, 1, '2026-08-24 06:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL, NULL),
(8, 'EXP-000008', '2026-08-28', 5, 19, 1, 2500.00, 'posted', NULL, 'Miscellaneous', NULL, 1, 1, '2026-08-28 06:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL, NULL),
(9, 'EXP-000009', '2026-03-15', 2, 16, 2, 25200.00, 'posted', NULL, 'Sample March expense', NULL, 1, 1, '2026-03-15 06:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL, NULL),
(10, 'EXP-000010', '2026-03-15', 1, 15, 2, 21600.00, 'posted', NULL, 'Sample March expense', NULL, 1, 1, '2026-03-15 06:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL, NULL),
(11, 'EXP-000011', '2026-03-15', 3, 17, 1, 14400.00, 'posted', NULL, 'Sample March expense', NULL, 1, 1, '2026-03-15 06:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL, NULL),
(12, 'EXP-000012', '2026-03-15', 5, 19, 1, 10800.00, 'posted', NULL, 'Sample March expense', NULL, 1, 1, '2026-03-15 06:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL, NULL),
(13, 'EXP-000013', '2026-04-15', 2, 16, 2, 27300.00, 'posted', NULL, 'Sample April expense', NULL, 1, 1, '2026-04-15 06:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL, NULL),
(14, 'EXP-000014', '2026-04-15', 1, 15, 2, 23400.00, 'posted', NULL, 'Sample April expense', NULL, 1, 1, '2026-04-15 06:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL, NULL),
(15, 'EXP-000015', '2026-04-15', 3, 17, 1, 15600.00, 'posted', NULL, 'Sample April expense', NULL, 1, 1, '2026-04-15 06:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL, NULL),
(16, 'EXP-000016', '2026-04-15', 5, 19, 1, 11700.00, 'posted', NULL, 'Sample April expense', NULL, 1, 1, '2026-04-15 06:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL, NULL),
(17, 'EXP-000017', '2026-05-15', 2, 16, 2, 28875.00, 'posted', NULL, 'Sample May expense', NULL, 1, 1, '2026-05-15 06:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL, NULL),
(18, 'EXP-000018', '2026-05-15', 1, 15, 2, 24750.00, 'posted', NULL, 'Sample May expense', NULL, 1, 1, '2026-05-15 06:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL, NULL),
(19, 'EXP-000019', '2026-05-15', 3, 17, 1, 16500.00, 'posted', NULL, 'Sample May expense', NULL, 1, 1, '2026-05-15 06:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL, NULL),
(20, 'EXP-000020', '2026-05-15', 5, 19, 1, 12375.00, 'posted', NULL, 'Sample May expense', NULL, 1, 1, '2026-05-15 06:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL, NULL),
(21, 'EXP-000021', '2026-06-15', 2, 16, 2, 30275.00, 'posted', NULL, 'Sample June expense', NULL, 1, 1, '2026-06-15 06:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL, NULL),
(22, 'EXP-000022', '2026-06-15', 1, 15, 2, 25950.00, 'posted', NULL, 'Sample June expense', NULL, 1, 1, '2026-06-15 06:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL, NULL),
(23, 'EXP-000023', '2026-06-15', 3, 17, 1, 17300.00, 'posted', NULL, 'Sample June expense', NULL, 1, 1, '2026-06-15 06:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL, NULL),
(24, 'EXP-000024', '2026-06-15', 5, 19, 1, 12975.00, 'posted', NULL, 'Sample June expense', NULL, 1, 1, '2026-06-15 06:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL, NULL),
(25, 'EXP-000025', '2026-07-15', 2, 16, 2, 31850.00, 'posted', NULL, 'Sample July expense', NULL, 1, 1, '2026-07-15 06:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL, NULL),
(26, 'EXP-000026', '2026-07-15', 1, 15, 2, 27300.00, 'posted', NULL, 'Sample July expense', NULL, 1, 1, '2026-07-15 06:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL, NULL),
(27, 'EXP-000027', '2026-07-15', 3, 17, 1, 18200.00, 'posted', NULL, 'Sample July expense', NULL, 1, 1, '2026-07-15 06:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL, NULL),
(28, 'EXP-000028', '2026-07-15', 5, 19, 1, 13650.00, 'posted', NULL, 'Sample July expense', NULL, 1, 1, '2026-07-15 06:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `expense_categories`
--

CREATE TABLE `expense_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(150) NOT NULL,
  `expense_account_id` bigint(20) UNSIGNED DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `expense_categories`
--

INSERT INTO `expense_categories` (`id`, `name`, `expense_account_id`, `is_active`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Salary', 15, 1, '2026-08-27 18:00:00', '2026-08-27 18:00:00', NULL),
(2, 'Rent', 16, 1, '2026-08-27 18:00:00', '2026-08-27 18:00:00', NULL),
(3, 'Utilities', 17, 1, '2026-08-27 18:00:00', '2026-08-27 18:00:00', NULL),
(4, 'Inventory Loss', 18, 1, '2026-08-27 18:00:00', '2026-08-27 18:00:00', NULL),
(5, 'Other', 19, 1, '2026-08-27 18:00:00', '2026-08-27 18:00:00', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fixed_assets`
--

CREATE TABLE `fixed_assets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `asset_no` varchar(50) NOT NULL,
  `name` varchar(180) NOT NULL,
  `purchase_date` date DEFAULT NULL,
  `cost` decimal(18,2) NOT NULL,
  `salvage_value` decimal(18,2) NOT NULL DEFAULT 0.00,
  `useful_life_months` int(10) UNSIGNED NOT NULL DEFAULT 60,
  `depreciation_method` varchar(30) NOT NULL DEFAULT 'straight_line',
  `accumulated_depreciation` decimal(18,2) NOT NULL DEFAULT 0.00,
  `status` varchar(20) NOT NULL DEFAULT 'active',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fixed_asset_depreciations`
--

CREATE TABLE `fixed_asset_depreciations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `fixed_asset_id` bigint(20) UNSIGNED NOT NULL,
  `depreciation_date` date NOT NULL,
  `amount` decimal(18,2) NOT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventory_batches`
--

CREATE TABLE `inventory_batches` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `warehouse_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `batch_no` varchar(100) NOT NULL,
  `manufactured_at` date DEFAULT NULL,
  `expires_at` date DEFAULT NULL,
  `quantity` decimal(18,4) NOT NULL DEFAULT 0.0000,
  `unit_cost` decimal(18,4) NOT NULL DEFAULT 0.0000,
  `status` varchar(20) NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventory_cost_layers`
--

CREATE TABLE `inventory_cost_layers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `warehouse_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `inventory_batch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `source_type` varchar(50) DEFAULT NULL,
  `source_id` bigint(20) UNSIGNED DEFAULT NULL,
  `transaction_date` date NOT NULL,
  `original_quantity` decimal(18,4) NOT NULL,
  `quantity_remaining` decimal(18,4) NOT NULL,
  `unit_cost` decimal(18,4) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `inventory_cost_layers`
--

INSERT INTO `inventory_cost_layers` (`id`, `warehouse_id`, `product_id`, `inventory_batch_id`, `source_type`, `source_id`, `transaction_date`, `original_quantity`, `quantity_remaining`, `unit_cost`, `created_at`, `updated_at`) VALUES
(1, 1, 1, NULL, 'migration_opening', 1, '2026-10-22', 2.0000, 2.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(2, 1, 3, NULL, 'migration_opening', 3, '2026-10-22', 2.0000, 2.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(3, 1, 4, NULL, 'migration_opening', 4, '2026-10-22', 2.0000, 2.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(4, 1, 5, NULL, 'migration_opening', 5, '2026-10-22', 2.0000, 2.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(5, 1, 6, NULL, 'migration_opening', 6, '2026-10-22', 2.0000, 2.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(6, 1, 7, NULL, 'migration_opening', 7, '2026-10-22', 2.0000, 2.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(7, 1, 8, NULL, 'migration_opening', 8, '2026-10-22', 2.0000, 2.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(8, 1, 9, NULL, 'migration_opening', 9, '2026-10-22', 2.0000, 2.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(9, 1, 10, NULL, 'migration_opening', 10, '2026-10-22', 2.0000, 2.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(10, 1, 11, NULL, 'migration_opening', 11, '2026-10-22', 2.0000, 2.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(11, 1, 14, NULL, 'migration_opening', 14, '2026-10-22', 2.0000, 2.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(12, 1, 15, NULL, 'migration_opening', 15, '2026-10-22', 2.0000, 2.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(13, 1, 16, NULL, 'migration_opening', 16, '2026-10-22', 2.0000, 2.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(14, 1, 17, NULL, 'migration_opening', 17, '2026-10-22', 2.0000, 2.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(15, 1, 18, NULL, 'migration_opening', 18, '2026-10-22', 2.0000, 2.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(16, 1, 19, NULL, 'migration_opening', 19, '2026-10-22', 2.0000, 2.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(17, 1, 20, NULL, 'migration_opening', 20, '2026-10-22', 1.0000, 1.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(18, 1, 21, NULL, 'migration_opening', 21, '2026-10-22', 2.0000, 2.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(19, 1, 22, NULL, 'migration_opening', 22, '2026-10-22', 2.0000, 2.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(20, 1, 23, NULL, 'migration_opening', 23, '2026-10-22', 2.0000, 2.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(21, 1, 24, NULL, 'migration_opening', 24, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(22, 1, 25, NULL, 'migration_opening', 25, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(23, 1, 26, NULL, 'migration_opening', 26, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(24, 1, 27, NULL, 'migration_opening', 27, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(25, 1, 28, NULL, 'migration_opening', 28, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(26, 1, 29, NULL, 'migration_opening', 29, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(27, 1, 30, NULL, 'migration_opening', 30, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(28, 1, 31, NULL, 'migration_opening', 31, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(29, 1, 32, NULL, 'migration_opening', 32, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(30, 1, 33, NULL, 'migration_opening', 33, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(31, 1, 34, NULL, 'migration_opening', 34, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(32, 1, 35, NULL, 'migration_opening', 35, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(33, 1, 36, NULL, 'migration_opening', 36, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(34, 1, 37, NULL, 'migration_opening', 37, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(35, 1, 38, NULL, 'migration_opening', 38, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(36, 1, 39, NULL, 'migration_opening', 39, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(37, 1, 40, NULL, 'migration_opening', 40, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(38, 1, 41, NULL, 'migration_opening', 41, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(39, 1, 42, NULL, 'migration_opening', 42, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(40, 1, 43, NULL, 'migration_opening', 43, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(41, 1, 44, NULL, 'migration_opening', 44, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(42, 1, 45, NULL, 'migration_opening', 45, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(43, 1, 46, NULL, 'migration_opening', 46, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(44, 1, 47, NULL, 'migration_opening', 47, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(45, 1, 48, NULL, 'migration_opening', 48, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(46, 1, 49, NULL, 'migration_opening', 49, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(47, 1, 50, NULL, 'migration_opening', 50, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(48, 1, 51, NULL, 'migration_opening', 51, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(49, 1, 52, NULL, 'migration_opening', 52, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(50, 1, 53, NULL, 'migration_opening', 53, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(51, 1, 54, NULL, 'migration_opening', 54, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(52, 1, 55, NULL, 'migration_opening', 55, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(53, 1, 56, NULL, 'migration_opening', 56, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(54, 1, 57, NULL, 'migration_opening', 57, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(55, 1, 58, NULL, 'migration_opening', 58, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(56, 1, 59, NULL, 'migration_opening', 59, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(57, 1, 60, NULL, 'migration_opening', 60, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(58, 1, 61, NULL, 'migration_opening', 61, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(59, 1, 62, NULL, 'migration_opening', 62, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(60, 1, 63, NULL, 'migration_opening', 63, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(61, 1, 64, NULL, 'migration_opening', 64, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(62, 1, 65, NULL, 'migration_opening', 65, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(63, 1, 66, NULL, 'migration_opening', 66, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(64, 1, 67, NULL, 'migration_opening', 67, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(65, 1, 68, NULL, 'migration_opening', 68, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(66, 1, 69, NULL, 'migration_opening', 69, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(67, 1, 70, NULL, 'migration_opening', 70, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(68, 1, 71, NULL, 'migration_opening', 71, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(69, 1, 72, NULL, 'migration_opening', 72, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(70, 1, 73, NULL, 'migration_opening', 73, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(71, 1, 74, NULL, 'migration_opening', 74, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(72, 1, 75, NULL, 'migration_opening', 75, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(73, 1, 76, NULL, 'migration_opening', 76, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(74, 1, 77, NULL, 'migration_opening', 77, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(75, 1, 78, NULL, 'migration_opening', 78, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(76, 1, 79, NULL, 'migration_opening', 79, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(77, 1, 80, NULL, 'migration_opening', 80, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(78, 1, 81, NULL, 'migration_opening', 81, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(79, 1, 82, NULL, 'migration_opening', 82, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(80, 1, 83, NULL, 'migration_opening', 83, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(81, 1, 84, NULL, 'migration_opening', 84, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(82, 1, 85, NULL, 'migration_opening', 85, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(83, 1, 86, NULL, 'migration_opening', 86, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(84, 1, 87, NULL, 'migration_opening', 87, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(85, 1, 88, NULL, 'migration_opening', 88, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(86, 1, 89, NULL, 'migration_opening', 89, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(87, 1, 90, NULL, 'migration_opening', 90, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(88, 1, 91, NULL, 'migration_opening', 91, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(89, 1, 92, NULL, 'migration_opening', 92, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(90, 1, 93, NULL, 'migration_opening', 93, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(91, 1, 94, NULL, 'migration_opening', 94, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(92, 1, 95, NULL, 'migration_opening', 95, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(93, 1, 96, NULL, 'migration_opening', 96, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(94, 1, 97, NULL, 'migration_opening', 97, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(95, 1, 98, NULL, 'migration_opening', 98, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(96, 1, 99, NULL, 'migration_opening', 99, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(97, 1, 100, NULL, 'migration_opening', 100, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(98, 1, 101, NULL, 'migration_opening', 101, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(99, 1, 102, NULL, 'migration_opening', 102, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(100, 1, 103, NULL, 'migration_opening', 103, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(101, 1, 104, NULL, 'migration_opening', 104, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(102, 1, 105, NULL, 'migration_opening', 105, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(103, 1, 106, NULL, 'migration_opening', 106, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(104, 1, 107, NULL, 'migration_opening', 107, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(105, 1, 108, NULL, 'migration_opening', 108, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(106, 1, 109, NULL, 'migration_opening', 109, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(107, 1, 110, NULL, 'migration_opening', 110, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(108, 1, 111, NULL, 'migration_opening', 111, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(109, 1, 112, NULL, 'migration_opening', 112, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(110, 1, 113, NULL, 'migration_opening', 113, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(111, 1, 114, NULL, 'migration_opening', 114, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(112, 1, 115, NULL, 'migration_opening', 115, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(113, 1, 116, NULL, 'migration_opening', 116, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(114, 1, 117, NULL, 'migration_opening', 117, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(115, 1, 118, NULL, 'migration_opening', 118, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(116, 1, 119, NULL, 'migration_opening', 119, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(117, 1, 120, NULL, 'migration_opening', 120, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(118, 1, 121, NULL, 'migration_opening', 121, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(119, 1, 122, NULL, 'migration_opening', 122, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(120, 1, 123, NULL, 'migration_opening', 123, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(121, 1, 124, NULL, 'migration_opening', 124, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(122, 1, 125, NULL, 'migration_opening', 125, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(123, 1, 126, NULL, 'migration_opening', 126, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(124, 1, 127, NULL, 'migration_opening', 127, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(125, 1, 128, NULL, 'migration_opening', 128, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(126, 1, 129, NULL, 'migration_opening', 129, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(127, 1, 130, NULL, 'migration_opening', 130, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(128, 1, 131, NULL, 'migration_opening', 131, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(129, 1, 132, NULL, 'migration_opening', 132, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(130, 1, 133, NULL, 'migration_opening', 133, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(131, 1, 134, NULL, 'migration_opening', 134, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(132, 1, 135, NULL, 'migration_opening', 135, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(133, 1, 136, NULL, 'migration_opening', 136, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(134, 1, 137, NULL, 'migration_opening', 137, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(135, 1, 138, NULL, 'migration_opening', 138, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(136, 1, 139, NULL, 'migration_opening', 139, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(137, 1, 140, NULL, 'migration_opening', 140, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(138, 1, 141, NULL, 'migration_opening', 141, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(139, 1, 142, NULL, 'migration_opening', 142, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(140, 1, 143, NULL, 'migration_opening', 143, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(141, 1, 144, NULL, 'migration_opening', 144, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(142, 1, 145, NULL, 'migration_opening', 145, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(143, 1, 146, NULL, 'migration_opening', 146, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(144, 1, 147, NULL, 'migration_opening', 147, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(145, 1, 148, NULL, 'migration_opening', 148, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(146, 1, 149, NULL, 'migration_opening', 149, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(147, 1, 150, NULL, 'migration_opening', 150, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(148, 1, 151, NULL, 'migration_opening', 151, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(149, 1, 152, NULL, 'migration_opening', 152, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(150, 1, 153, NULL, 'migration_opening', 153, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(151, 1, 154, NULL, 'migration_opening', 154, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(152, 1, 155, NULL, 'migration_opening', 155, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(153, 1, 156, NULL, 'migration_opening', 156, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(154, 1, 157, NULL, 'migration_opening', 157, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(155, 1, 158, NULL, 'migration_opening', 158, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(156, 1, 159, NULL, 'migration_opening', 159, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(157, 1, 160, NULL, 'migration_opening', 160, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(158, 1, 161, NULL, 'migration_opening', 161, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(159, 1, 162, NULL, 'migration_opening', 162, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(160, 1, 163, NULL, 'migration_opening', 163, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(161, 1, 164, NULL, 'migration_opening', 164, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(162, 1, 165, NULL, 'migration_opening', 165, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(163, 1, 166, NULL, 'migration_opening', 166, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(164, 1, 167, NULL, 'migration_opening', 167, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(165, 1, 168, NULL, 'migration_opening', 168, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(166, 1, 169, NULL, 'migration_opening', 169, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(167, 1, 170, NULL, 'migration_opening', 170, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(168, 1, 171, NULL, 'migration_opening', 171, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(169, 1, 172, NULL, 'migration_opening', 172, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(170, 1, 173, NULL, 'migration_opening', 173, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(171, 1, 174, NULL, 'migration_opening', 174, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(172, 1, 175, NULL, 'migration_opening', 175, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(173, 1, 176, NULL, 'migration_opening', 176, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(174, 1, 177, NULL, 'migration_opening', 177, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(175, 1, 178, NULL, 'migration_opening', 178, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(176, 1, 179, NULL, 'migration_opening', 179, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(177, 1, 180, NULL, 'migration_opening', 180, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(178, 1, 181, NULL, 'migration_opening', 181, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(179, 1, 182, NULL, 'migration_opening', 182, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(180, 1, 183, NULL, 'migration_opening', 183, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(181, 1, 184, NULL, 'migration_opening', 184, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(182, 1, 185, NULL, 'migration_opening', 185, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(183, 1, 186, NULL, 'migration_opening', 186, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(184, 1, 187, NULL, 'migration_opening', 187, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(185, 1, 188, NULL, 'migration_opening', 188, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(186, 1, 189, NULL, 'migration_opening', 189, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(187, 1, 190, NULL, 'migration_opening', 190, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(188, 1, 191, NULL, 'migration_opening', 191, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(189, 1, 192, NULL, 'migration_opening', 192, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(190, 1, 193, NULL, 'migration_opening', 193, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(191, 1, 194, NULL, 'migration_opening', 194, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(192, 1, 195, NULL, 'migration_opening', 195, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(193, 1, 196, NULL, 'migration_opening', 196, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(194, 1, 197, NULL, 'migration_opening', 197, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(195, 1, 198, NULL, 'migration_opening', 198, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(196, 1, 199, NULL, 'migration_opening', 199, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(197, 1, 200, NULL, 'migration_opening', 200, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(198, 1, 201, NULL, 'migration_opening', 201, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(199, 1, 202, NULL, 'migration_opening', 202, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(200, 1, 203, NULL, 'migration_opening', 203, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(201, 1, 204, NULL, 'migration_opening', 204, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(202, 1, 205, NULL, 'migration_opening', 205, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(203, 1, 206, NULL, 'migration_opening', 206, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(204, 1, 207, NULL, 'migration_opening', 207, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(205, 1, 208, NULL, 'migration_opening', 208, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(206, 1, 209, NULL, 'migration_opening', 209, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(207, 1, 210, NULL, 'migration_opening', 210, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(208, 1, 211, NULL, 'migration_opening', 211, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(209, 1, 212, NULL, 'migration_opening', 212, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(210, 1, 213, NULL, 'migration_opening', 213, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(211, 1, 214, NULL, 'migration_opening', 214, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(212, 1, 215, NULL, 'migration_opening', 215, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(213, 1, 216, NULL, 'migration_opening', 216, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(214, 1, 217, NULL, 'migration_opening', 217, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(215, 1, 218, NULL, 'migration_opening', 218, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(216, 1, 219, NULL, 'migration_opening', 219, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(217, 1, 220, NULL, 'migration_opening', 220, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(218, 1, 221, NULL, 'migration_opening', 221, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(219, 1, 222, NULL, 'migration_opening', 222, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(220, 1, 223, NULL, 'migration_opening', 223, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(221, 1, 224, NULL, 'migration_opening', 224, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(222, 1, 225, NULL, 'migration_opening', 225, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(223, 1, 226, NULL, 'migration_opening', 226, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(224, 1, 227, NULL, 'migration_opening', 227, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(225, 1, 228, NULL, 'migration_opening', 228, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(226, 1, 229, NULL, 'migration_opening', 229, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(227, 1, 230, NULL, 'migration_opening', 230, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(228, 1, 231, NULL, 'migration_opening', 231, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(229, 1, 232, NULL, 'migration_opening', 232, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(230, 1, 233, NULL, 'migration_opening', 233, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(231, 1, 234, NULL, 'migration_opening', 234, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(232, 1, 235, NULL, 'migration_opening', 235, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(233, 1, 236, NULL, 'migration_opening', 236, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(234, 1, 237, NULL, 'migration_opening', 237, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(235, 1, 238, NULL, 'migration_opening', 238, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(236, 1, 239, NULL, 'migration_opening', 239, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(237, 1, 240, NULL, 'migration_opening', 240, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(238, 1, 241, NULL, 'migration_opening', 241, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(239, 1, 242, NULL, 'migration_opening', 242, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(240, 1, 243, NULL, 'migration_opening', 243, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(241, 1, 244, NULL, 'migration_opening', 244, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(242, 1, 245, NULL, 'migration_opening', 245, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(243, 1, 246, NULL, 'migration_opening', 246, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(244, 1, 247, NULL, 'migration_opening', 247, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(245, 1, 248, NULL, 'migration_opening', 248, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(246, 1, 249, NULL, 'migration_opening', 249, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(247, 1, 250, NULL, 'migration_opening', 250, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(248, 1, 251, NULL, 'migration_opening', 251, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(249, 1, 252, NULL, 'migration_opening', 252, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(250, 1, 253, NULL, 'migration_opening', 253, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(251, 1, 254, NULL, 'migration_opening', 254, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(252, 1, 255, NULL, 'migration_opening', 255, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(253, 1, 256, NULL, 'migration_opening', 256, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(254, 1, 257, NULL, 'migration_opening', 257, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(255, 1, 258, NULL, 'migration_opening', 258, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(256, 1, 259, NULL, 'migration_opening', 259, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(257, 1, 260, NULL, 'migration_opening', 260, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(258, 1, 261, NULL, 'migration_opening', 261, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(259, 1, 262, NULL, 'migration_opening', 262, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(260, 1, 263, NULL, 'migration_opening', 263, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(261, 1, 264, NULL, 'migration_opening', 264, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(262, 1, 265, NULL, 'migration_opening', 265, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(263, 1, 266, NULL, 'migration_opening', 266, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(264, 1, 267, NULL, 'migration_opening', 267, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(265, 1, 268, NULL, 'migration_opening', 268, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(266, 1, 269, NULL, 'migration_opening', 269, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(267, 1, 270, NULL, 'migration_opening', 270, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(268, 1, 271, NULL, 'migration_opening', 271, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(269, 1, 272, NULL, 'migration_opening', 272, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(270, 1, 273, NULL, 'migration_opening', 273, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(271, 1, 274, NULL, 'migration_opening', 274, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(272, 1, 275, NULL, 'migration_opening', 275, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(273, 1, 276, NULL, 'migration_opening', 276, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(274, 1, 277, NULL, 'migration_opening', 277, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(275, 1, 278, NULL, 'migration_opening', 278, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(276, 1, 279, NULL, 'migration_opening', 279, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(277, 1, 280, NULL, 'migration_opening', 280, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(278, 1, 281, NULL, 'migration_opening', 281, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(279, 1, 282, NULL, 'migration_opening', 282, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(280, 1, 283, NULL, 'migration_opening', 283, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(281, 1, 284, NULL, 'migration_opening', 284, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(282, 1, 285, NULL, 'migration_opening', 285, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(283, 1, 286, NULL, 'migration_opening', 286, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(284, 1, 287, NULL, 'migration_opening', 287, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(285, 1, 288, NULL, 'migration_opening', 288, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(286, 1, 289, NULL, 'migration_opening', 289, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(287, 1, 290, NULL, 'migration_opening', 290, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(288, 1, 291, NULL, 'migration_opening', 291, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(289, 1, 292, NULL, 'migration_opening', 292, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(290, 1, 293, NULL, 'migration_opening', 293, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(291, 1, 294, NULL, 'migration_opening', 294, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(292, 1, 295, NULL, 'migration_opening', 295, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(293, 1, 296, NULL, 'migration_opening', 296, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(294, 1, 297, NULL, 'migration_opening', 297, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(295, 1, 298, NULL, 'migration_opening', 298, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(296, 1, 299, NULL, 'migration_opening', 299, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(297, 1, 300, NULL, 'migration_opening', 300, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(298, 1, 301, NULL, 'migration_opening', 301, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(299, 1, 302, NULL, 'migration_opening', 302, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(300, 1, 303, NULL, 'migration_opening', 303, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(301, 1, 304, NULL, 'migration_opening', 304, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(302, 1, 305, NULL, 'migration_opening', 305, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(303, 1, 306, NULL, 'migration_opening', 306, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(304, 1, 307, NULL, 'migration_opening', 307, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(305, 1, 308, NULL, 'migration_opening', 308, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(306, 1, 309, NULL, 'migration_opening', 309, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(307, 1, 310, NULL, 'migration_opening', 310, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(308, 1, 311, NULL, 'migration_opening', 311, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(309, 1, 312, NULL, 'migration_opening', 312, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(310, 1, 313, NULL, 'migration_opening', 313, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(311, 1, 314, NULL, 'migration_opening', 314, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(312, 1, 315, NULL, 'migration_opening', 315, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(313, 1, 316, NULL, 'migration_opening', 316, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(314, 1, 317, NULL, 'migration_opening', 317, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(315, 1, 318, NULL, 'migration_opening', 318, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(316, 1, 319, NULL, 'migration_opening', 319, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(317, 1, 320, NULL, 'migration_opening', 320, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(318, 1, 321, NULL, 'migration_opening', 321, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(319, 1, 322, NULL, 'migration_opening', 322, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(320, 1, 323, NULL, 'migration_opening', 323, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(321, 1, 324, NULL, 'migration_opening', 324, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(322, 1, 325, NULL, 'migration_opening', 325, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(323, 1, 326, NULL, 'migration_opening', 326, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(324, 1, 327, NULL, 'migration_opening', 327, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(325, 1, 328, NULL, 'migration_opening', 328, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(326, 1, 329, NULL, 'migration_opening', 329, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(327, 1, 330, NULL, 'migration_opening', 330, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(328, 1, 331, NULL, 'migration_opening', 331, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(329, 1, 332, NULL, 'migration_opening', 332, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(330, 1, 333, NULL, 'migration_opening', 333, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(331, 1, 334, NULL, 'migration_opening', 334, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(332, 1, 335, NULL, 'migration_opening', 335, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(333, 1, 336, NULL, 'migration_opening', 336, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(334, 1, 337, NULL, 'migration_opening', 337, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(335, 1, 338, NULL, 'migration_opening', 338, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(336, 1, 339, NULL, 'migration_opening', 339, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(337, 1, 340, NULL, 'migration_opening', 340, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(338, 1, 341, NULL, 'migration_opening', 341, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(339, 1, 342, NULL, 'migration_opening', 342, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(340, 1, 343, NULL, 'migration_opening', 343, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(341, 1, 344, NULL, 'migration_opening', 344, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(342, 1, 345, NULL, 'migration_opening', 345, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(343, 1, 346, NULL, 'migration_opening', 346, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(344, 1, 347, NULL, 'migration_opening', 347, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(345, 1, 348, NULL, 'migration_opening', 348, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(346, 1, 349, NULL, 'migration_opening', 349, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(347, 1, 350, NULL, 'migration_opening', 350, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(348, 1, 351, NULL, 'migration_opening', 351, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(349, 1, 352, NULL, 'migration_opening', 352, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(350, 1, 353, NULL, 'migration_opening', 353, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(351, 1, 354, NULL, 'migration_opening', 354, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(352, 1, 355, NULL, 'migration_opening', 355, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(353, 1, 356, NULL, 'migration_opening', 356, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(354, 1, 357, NULL, 'migration_opening', 357, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(355, 1, 358, NULL, 'migration_opening', 358, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(356, 1, 359, NULL, 'migration_opening', 359, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(357, 1, 360, NULL, 'migration_opening', 360, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(358, 1, 361, NULL, 'migration_opening', 361, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(359, 1, 362, NULL, 'migration_opening', 362, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(360, 1, 363, NULL, 'migration_opening', 363, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(361, 1, 364, NULL, 'migration_opening', 364, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(362, 1, 365, NULL, 'migration_opening', 365, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(363, 1, 366, NULL, 'migration_opening', 366, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(364, 1, 367, NULL, 'migration_opening', 367, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(365, 1, 368, NULL, 'migration_opening', 368, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(366, 1, 369, NULL, 'migration_opening', 369, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(367, 1, 370, NULL, 'migration_opening', 370, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(368, 1, 371, NULL, 'migration_opening', 371, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(369, 1, 372, NULL, 'migration_opening', 372, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(370, 1, 373, NULL, 'migration_opening', 373, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(371, 1, 374, NULL, 'migration_opening', 374, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(372, 1, 375, NULL, 'migration_opening', 375, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(373, 1, 376, NULL, 'migration_opening', 376, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(374, 1, 377, NULL, 'migration_opening', 377, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(375, 1, 378, NULL, 'migration_opening', 378, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(376, 1, 379, NULL, 'migration_opening', 379, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43');
INSERT INTO `inventory_cost_layers` (`id`, `warehouse_id`, `product_id`, `inventory_batch_id`, `source_type`, `source_id`, `transaction_date`, `original_quantity`, `quantity_remaining`, `unit_cost`, `created_at`, `updated_at`) VALUES
(377, 1, 380, NULL, 'migration_opening', 380, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(378, 1, 381, NULL, 'migration_opening', 381, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(379, 1, 382, NULL, 'migration_opening', 382, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(380, 1, 383, NULL, 'migration_opening', 383, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(381, 1, 384, NULL, 'migration_opening', 384, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(382, 1, 385, NULL, 'migration_opening', 385, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(383, 1, 386, NULL, 'migration_opening', 386, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(384, 1, 387, NULL, 'migration_opening', 387, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(385, 1, 388, NULL, 'migration_opening', 388, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(386, 1, 389, NULL, 'migration_opening', 389, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(387, 1, 390, NULL, 'migration_opening', 390, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(388, 1, 391, NULL, 'migration_opening', 391, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(389, 1, 392, NULL, 'migration_opening', 392, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(390, 1, 393, NULL, 'migration_opening', 393, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(391, 1, 394, NULL, 'migration_opening', 394, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(392, 1, 395, NULL, 'migration_opening', 395, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(393, 1, 396, NULL, 'migration_opening', 396, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(394, 1, 397, NULL, 'migration_opening', 397, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(395, 1, 398, NULL, 'migration_opening', 398, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(396, 1, 399, NULL, 'migration_opening', 399, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(397, 1, 400, NULL, 'migration_opening', 400, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(398, 1, 401, NULL, 'migration_opening', 401, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(399, 1, 402, NULL, 'migration_opening', 402, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(400, 1, 403, NULL, 'migration_opening', 403, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(401, 1, 404, NULL, 'migration_opening', 404, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(402, 1, 405, NULL, 'migration_opening', 405, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(403, 1, 406, NULL, 'migration_opening', 406, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(404, 1, 407, NULL, 'migration_opening', 407, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(405, 1, 408, NULL, 'migration_opening', 408, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(406, 1, 409, NULL, 'migration_opening', 409, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(407, 1, 410, NULL, 'migration_opening', 410, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(408, 1, 411, NULL, 'migration_opening', 411, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(409, 1, 412, NULL, 'migration_opening', 412, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(410, 1, 413, NULL, 'migration_opening', 413, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(411, 1, 414, NULL, 'migration_opening', 414, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(412, 1, 415, NULL, 'migration_opening', 415, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(413, 1, 416, NULL, 'migration_opening', 416, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(414, 1, 417, NULL, 'migration_opening', 417, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(415, 1, 418, NULL, 'migration_opening', 418, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(416, 1, 419, NULL, 'migration_opening', 419, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(417, 1, 420, NULL, 'migration_opening', 420, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(418, 1, 421, NULL, 'migration_opening', 421, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(419, 1, 422, NULL, 'migration_opening', 422, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(420, 1, 423, NULL, 'migration_opening', 423, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(421, 1, 424, NULL, 'migration_opening', 424, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(422, 1, 425, NULL, 'migration_opening', 425, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(423, 1, 426, NULL, 'migration_opening', 426, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(424, 1, 427, NULL, 'migration_opening', 427, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(425, 1, 428, NULL, 'migration_opening', 428, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(426, 1, 429, NULL, 'migration_opening', 429, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(427, 1, 430, NULL, 'migration_opening', 430, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(428, 1, 431, NULL, 'migration_opening', 431, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(429, 1, 432, NULL, 'migration_opening', 432, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(430, 1, 433, NULL, 'migration_opening', 433, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(431, 1, 434, NULL, 'migration_opening', 434, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(432, 1, 435, NULL, 'migration_opening', 435, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(433, 1, 436, NULL, 'migration_opening', 436, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(434, 1, 437, NULL, 'migration_opening', 437, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(435, 1, 438, NULL, 'migration_opening', 438, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(436, 1, 439, NULL, 'migration_opening', 439, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(437, 1, 440, NULL, 'migration_opening', 440, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(438, 1, 441, NULL, 'migration_opening', 441, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(439, 1, 442, NULL, 'migration_opening', 442, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(440, 1, 443, NULL, 'migration_opening', 443, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(441, 1, 444, NULL, 'migration_opening', 444, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(442, 1, 445, NULL, 'migration_opening', 445, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(443, 1, 446, NULL, 'migration_opening', 446, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(444, 1, 447, NULL, 'migration_opening', 447, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(445, 1, 448, NULL, 'migration_opening', 448, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(446, 1, 449, NULL, 'migration_opening', 449, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(447, 1, 450, NULL, 'migration_opening', 450, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(448, 1, 451, NULL, 'migration_opening', 451, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(449, 1, 452, NULL, 'migration_opening', 452, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(450, 1, 453, NULL, 'migration_opening', 453, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(451, 1, 454, NULL, 'migration_opening', 454, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(452, 1, 455, NULL, 'migration_opening', 455, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(453, 1, 456, NULL, 'migration_opening', 456, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(454, 1, 457, NULL, 'migration_opening', 457, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(455, 1, 458, NULL, 'migration_opening', 458, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(456, 1, 459, NULL, 'migration_opening', 459, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(457, 1, 460, NULL, 'migration_opening', 460, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(458, 1, 461, NULL, 'migration_opening', 461, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(459, 1, 462, NULL, 'migration_opening', 462, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(460, 1, 463, NULL, 'migration_opening', 463, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(461, 1, 464, NULL, 'migration_opening', 464, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(462, 1, 465, NULL, 'migration_opening', 465, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(463, 1, 466, NULL, 'migration_opening', 466, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(464, 1, 467, NULL, 'migration_opening', 467, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(465, 1, 468, NULL, 'migration_opening', 468, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(466, 1, 469, NULL, 'migration_opening', 469, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(467, 1, 470, NULL, 'migration_opening', 470, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(468, 1, 471, NULL, 'migration_opening', 471, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(469, 1, 472, NULL, 'migration_opening', 472, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(470, 1, 473, NULL, 'migration_opening', 473, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(471, 1, 474, NULL, 'migration_opening', 474, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(472, 1, 475, NULL, 'migration_opening', 475, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(473, 1, 476, NULL, 'migration_opening', 476, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(474, 1, 477, NULL, 'migration_opening', 477, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(475, 1, 478, NULL, 'migration_opening', 478, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(476, 1, 479, NULL, 'migration_opening', 479, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(477, 1, 480, NULL, 'migration_opening', 480, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(478, 1, 481, NULL, 'migration_opening', 481, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(479, 1, 482, NULL, 'migration_opening', 482, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(480, 2, 100, NULL, 'migration_opening', 483, '2026-10-22', 1267.0000, 1267.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(481, 2, 101, NULL, 'migration_opening', 484, '2026-10-22', 40.0000, 40.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(482, 2, 102, NULL, 'migration_opening', 485, '2026-10-22', 40.0000, 40.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(483, 2, 103, NULL, 'migration_opening', 486, '2026-10-22', 40.0000, 40.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(484, 2, 104, NULL, 'migration_opening', 487, '2026-10-22', 40.0000, 40.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(485, 2, 105, NULL, 'migration_opening', 488, '2026-10-22', 40.0000, 40.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(486, 2, 106, NULL, 'migration_opening', 489, '2026-10-22', 40.0000, 40.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(487, 2, 107, NULL, 'migration_opening', 490, '2026-10-22', 40.0000, 40.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(488, 2, 108, NULL, 'migration_opening', 491, '2026-10-22', 40.0000, 40.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(489, 2, 109, NULL, 'migration_opening', 492, '2026-10-22', 40.0000, 40.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(490, 2, 110, NULL, 'migration_opening', 493, '2026-10-22', 40.0000, 40.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(491, 2, 111, NULL, 'migration_opening', 494, '2026-10-22', 40.0000, 40.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(492, 2, 112, NULL, 'migration_opening', 495, '2026-10-22', 40.0000, 40.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(493, 2, 113, NULL, 'migration_opening', 496, '2026-10-22', 40.0000, 40.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(494, 2, 114, NULL, 'migration_opening', 497, '2026-10-22', 40.0000, 40.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(495, 2, 115, NULL, 'migration_opening', 498, '2026-10-22', 40.0000, 40.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(496, 2, 116, NULL, 'migration_opening', 499, '2026-10-22', 40.0000, 40.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(497, 2, 117, NULL, 'migration_opening', 500, '2026-10-22', 40.0000, 40.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(498, 2, 118, NULL, 'migration_opening', 501, '2026-10-22', 40.0000, 40.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(499, 2, 119, NULL, 'migration_opening', 502, '2026-10-22', 40.0000, 40.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(500, 2, 120, NULL, 'migration_opening', 503, '2026-10-22', 40.0000, 40.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(501, 2, 121, NULL, 'migration_opening', 504, '2026-10-22', 40.0000, 40.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(502, 2, 122, NULL, 'migration_opening', 505, '2026-10-22', 40.0000, 40.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(503, 2, 123, NULL, 'migration_opening', 506, '2026-10-22', 40.0000, 40.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(504, 2, 124, NULL, 'migration_opening', 507, '2026-10-22', 40.0000, 40.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(505, 2, 125, NULL, 'migration_opening', 508, '2026-10-22', 40.0000, 40.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(506, 2, 126, NULL, 'migration_opening', 509, '2026-10-22', 40.0000, 40.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(507, 2, 127, NULL, 'migration_opening', 510, '2026-10-22', 40.0000, 40.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(508, 2, 128, NULL, 'migration_opening', 511, '2026-10-22', 40.0000, 40.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(509, 2, 129, NULL, 'migration_opening', 512, '2026-10-22', 40.0000, 40.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(510, 3, 200, NULL, 'migration_opening', 513, '2026-10-22', 30.0000, 30.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(511, 3, 201, NULL, 'migration_opening', 514, '2026-10-22', 30.0000, 30.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(512, 3, 202, NULL, 'migration_opening', 515, '2026-10-22', 30.0000, 30.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(513, 3, 203, NULL, 'migration_opening', 516, '2026-10-22', 30.0000, 30.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(514, 3, 204, NULL, 'migration_opening', 517, '2026-10-22', 30.0000, 30.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(515, 3, 205, NULL, 'migration_opening', 518, '2026-10-22', 30.0000, 30.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(516, 3, 206, NULL, 'migration_opening', 519, '2026-10-22', 30.0000, 30.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(517, 3, 207, NULL, 'migration_opening', 520, '2026-10-22', 30.0000, 30.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(518, 3, 208, NULL, 'migration_opening', 521, '2026-10-22', 30.0000, 30.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(519, 3, 209, NULL, 'migration_opening', 522, '2026-10-22', 30.0000, 30.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(520, 3, 210, NULL, 'migration_opening', 523, '2026-10-22', 30.0000, 30.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(521, 3, 211, NULL, 'migration_opening', 524, '2026-10-22', 30.0000, 30.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(522, 3, 212, NULL, 'migration_opening', 525, '2026-10-22', 30.0000, 30.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(523, 3, 213, NULL, 'migration_opening', 526, '2026-10-22', 30.0000, 30.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(524, 3, 214, NULL, 'migration_opening', 527, '2026-10-22', 30.0000, 30.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(525, 3, 215, NULL, 'migration_opening', 528, '2026-10-22', 30.0000, 30.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(526, 3, 216, NULL, 'migration_opening', 529, '2026-10-22', 30.0000, 30.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(527, 3, 217, NULL, 'migration_opening', 530, '2026-10-22', 30.0000, 30.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(528, 3, 218, NULL, 'migration_opening', 531, '2026-10-22', 30.0000, 30.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(529, 3, 219, NULL, 'migration_opening', 532, '2026-10-22', 30.0000, 30.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(530, 4, 300, NULL, 'migration_opening', 533, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(531, 4, 301, NULL, 'migration_opening', 534, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(532, 4, 302, NULL, 'migration_opening', 535, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(533, 4, 303, NULL, 'migration_opening', 536, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(534, 4, 304, NULL, 'migration_opening', 537, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(535, 4, 305, NULL, 'migration_opening', 538, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(536, 4, 306, NULL, 'migration_opening', 539, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(537, 4, 307, NULL, 'migration_opening', 540, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(538, 4, 308, NULL, 'migration_opening', 541, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(539, 4, 309, NULL, 'migration_opening', 542, '2026-10-22', 20.0000, 20.0000, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43'),
(540, 2, 6, NULL, 'migration_opening', 543, '2026-10-22', 1.0001, 1.0001, 100.0000, '2026-10-22 06:24:43', '2026-10-22 06:24:43');

-- --------------------------------------------------------

--
-- Table structure for table `inventory_reservations`
--

CREATE TABLE `inventory_reservations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `warehouse_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` decimal(18,4) NOT NULL,
  `reference_type` varchar(50) DEFAULT NULL,
  `reference_id` bigint(20) UNSIGNED DEFAULT NULL,
  `reference_no` varchar(100) DEFAULT NULL,
  `reserved_until` date DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'active',
  `notes` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `journal_entries`
--

CREATE TABLE `journal_entries` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `journal_no` varchar(50) NOT NULL,
  `journal_date` date NOT NULL,
  `reference_type` varchar(50) DEFAULT NULL,
  `reference_id` bigint(20) UNSIGNED DEFAULT NULL,
  `reference_no` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'posted',
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `posted_by` bigint(20) UNSIGNED DEFAULT NULL,
  `posted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `journal_entries`
--

INSERT INTO `journal_entries` (`id`, `journal_no`, `journal_date`, `reference_type`, `reference_id`, `reference_no`, `description`, `status`, `created_by`, `posted_by`, `posted_at`, `created_at`, `updated_at`) VALUES
(1, 'JV-000001', '2026-03-01', 'opening', NULL, 'OPENING', 'Demo opening balances', 'posted', 1, 1, '2026-03-01 12:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(2, 'JV-000002', '2026-03-28', 'sales_summary', NULL, 'SALES-2026-03', 'Sales summary 2026-03', 'posted', 1, 1, '2026-03-28 12:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(3, 'JV-000003', '2026-03-28', 'customer_receipts_summary', NULL, 'RCPT-2026-03', 'Customer receipts 2026-03', 'posted', 1, 1, '2026-03-28 12:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(4, 'JV-000004', '2026-03-28', 'purchase_summary', NULL, 'PUR-2026-03', 'Purchase summary 2026-03', 'posted', 1, 1, '2026-03-28 12:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(5, 'JV-000005', '2026-03-28', 'supplier_payments_summary', NULL, 'SPAY-2026-03', 'Supplier payments 2026-03', 'posted', 1, 1, '2026-03-28 12:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(6, 'JV-000006', '2026-03-28', 'expense_summary', NULL, 'EXP-2026-03', 'Expense summary 2026-03', 'posted', 1, 1, '2026-03-28 12:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(7, 'JV-000007', '2026-04-28', 'sales_summary', NULL, 'SALES-2026-04', 'Sales summary 2026-04', 'posted', 1, 1, '2026-04-28 12:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(8, 'JV-000008', '2026-04-28', 'customer_receipts_summary', NULL, 'RCPT-2026-04', 'Customer receipts 2026-04', 'posted', 1, 1, '2026-04-28 12:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(9, 'JV-000009', '2026-04-28', 'purchase_summary', NULL, 'PUR-2026-04', 'Purchase summary 2026-04', 'posted', 1, 1, '2026-04-28 12:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(10, 'JV-000010', '2026-04-28', 'supplier_payments_summary', NULL, 'SPAY-2026-04', 'Supplier payments 2026-04', 'posted', 1, 1, '2026-04-28 12:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(11, 'JV-000011', '2026-04-28', 'expense_summary', NULL, 'EXP-2026-04', 'Expense summary 2026-04', 'posted', 1, 1, '2026-04-28 12:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(12, 'JV-000012', '2026-05-28', 'sales_summary', NULL, 'SALES-2026-05', 'Sales summary 2026-05', 'posted', 1, 1, '2026-05-28 12:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(13, 'JV-000013', '2026-05-28', 'customer_receipts_summary', NULL, 'RCPT-2026-05', 'Customer receipts 2026-05', 'posted', 1, 1, '2026-05-28 12:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(14, 'JV-000014', '2026-05-28', 'purchase_summary', NULL, 'PUR-2026-05', 'Purchase summary 2026-05', 'posted', 1, 1, '2026-05-28 12:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(15, 'JV-000015', '2026-05-28', 'supplier_payments_summary', NULL, 'SPAY-2026-05', 'Supplier payments 2026-05', 'posted', 1, 1, '2026-05-28 12:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(16, 'JV-000016', '2026-05-28', 'expense_summary', NULL, 'EXP-2026-05', 'Expense summary 2026-05', 'posted', 1, 1, '2026-05-28 12:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(17, 'JV-000017', '2026-06-28', 'sales_summary', NULL, 'SALES-2026-06', 'Sales summary 2026-06', 'posted', 1, 1, '2026-06-28 12:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(18, 'JV-000018', '2026-06-28', 'customer_receipts_summary', NULL, 'RCPT-2026-06', 'Customer receipts 2026-06', 'posted', 1, 1, '2026-06-28 12:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(19, 'JV-000019', '2026-06-28', 'purchase_summary', NULL, 'PUR-2026-06', 'Purchase summary 2026-06', 'posted', 1, 1, '2026-06-28 12:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(20, 'JV-000020', '2026-06-28', 'supplier_payments_summary', NULL, 'SPAY-2026-06', 'Supplier payments 2026-06', 'posted', 1, 1, '2026-06-28 12:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(21, 'JV-000021', '2026-06-28', 'expense_summary', NULL, 'EXP-2026-06', 'Expense summary 2026-06', 'posted', 1, 1, '2026-06-28 12:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(22, 'JV-000022', '2026-07-28', 'sales_summary', NULL, 'SALES-2026-07', 'Sales summary 2026-07', 'posted', 1, 1, '2026-07-28 12:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(23, 'JV-000023', '2026-07-28', 'customer_receipts_summary', NULL, 'RCPT-2026-07', 'Customer receipts 2026-07', 'posted', 1, 1, '2026-07-28 12:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(24, 'JV-000024', '2026-07-28', 'purchase_summary', NULL, 'PUR-2026-07', 'Purchase summary 2026-07', 'posted', 1, 1, '2026-07-28 12:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(25, 'JV-000025', '2026-07-28', 'supplier_payments_summary', NULL, 'SPAY-2026-07', 'Supplier payments 2026-07', 'posted', 1, 1, '2026-07-28 12:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(26, 'JV-000026', '2026-07-28', 'expense_summary', NULL, 'EXP-2026-07', 'Expense summary 2026-07', 'posted', 1, 1, '2026-07-28 12:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(27, 'JV-000027', '2026-08-28', 'sales_summary', NULL, 'SALES-2026-08', 'Sales summary 2026-08', 'posted', 1, 1, '2026-08-28 12:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(28, 'JV-000028', '2026-08-28', 'customer_receipts_summary', NULL, 'RCPT-2026-08', 'Customer receipts 2026-08', 'posted', 1, 1, '2026-08-28 12:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(29, 'JV-000029', '2026-08-28', 'purchase_summary', NULL, 'PUR-2026-08', 'Purchase summary 2026-08', 'posted', 1, 1, '2026-08-28 12:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(30, 'JV-000030', '2026-08-28', 'supplier_payments_summary', NULL, 'SPAY-2026-08', 'Supplier payments 2026-08', 'posted', 1, 1, '2026-08-28 12:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(31, 'JV-000031', '2026-08-28', 'expense_summary', NULL, 'EXP-2026-08', 'Expense summary 2026-08', 'posted', 1, 1, '2026-08-28 12:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(32, 'JV-000032', '2026-08-28', 'payroll', 1, 'PR-2026-08', 'August payroll', 'posted', 1, 1, '2026-08-28 12:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(33, 'JV-000033', '2026-08-28', 'payroll_payment', 1, 'PR-2026-08-PAY', 'August payroll payment', 'posted', 1, 1, '2026-08-28 12:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(34, 'JV-2026-000034', '2026-09-23', 'payroll', 2, 'PAYR-2026-000002', 'Payroll 2026-09', 'posted', 1, 1, '2026-09-23 16:15:06', '2026-09-23 16:15:06', '2026-09-23 16:15:06'),
(35, 'JV-2026-000035', '2026-09-25', 'customer_payment', 43, 'REC-2026-000043', 'Customer payment REC-2026-000043', 'posted', 1, 1, '2026-09-26 19:04:07', '2026-09-26 19:04:07', '2026-09-26 19:04:07'),
(36, 'JV-2026-000036', '2026-09-25', 'sale_return', 3, 'SRT-2026-000003', 'Sales return SRT-2026-000003', 'posted', 1, 1, '2026-09-26 19:20:21', '2026-09-26 19:20:21', '2026-09-26 19:20:21'),
(37, 'JV-2026-000037', '2026-09-28', 'sale', 46, 'SAL-2026-000046', 'Sale SAL-2026-000046', 'posted', 3, 3, '2026-09-28 16:05:26', '2026-09-28 16:05:26', '2026-09-28 16:05:26'),
(38, 'JV-2026-000038', '2026-09-28', 'sale', 53, 'SAL-2026-000053', 'Sale SAL-2026-000053', 'posted', 3, 3, '2026-09-28 16:59:32', '2026-09-28 16:59:32', '2026-09-28 16:59:32'),
(39, 'JV-2026-000039', '2026-09-28', 'sale', 57, 'SAL-2026-000057', 'Sale SAL-2026-000057', 'posted', 3, 3, '2026-09-28 17:09:49', '2026-09-28 17:09:49', '2026-09-28 17:09:49'),
(40, 'JV-2026-000040', '2026-09-28', 'customer_payment', 44, 'REC-2026-000044', 'Customer payment REC-2026-000044', 'posted', 3, 3, '2026-09-28 17:10:37', '2026-09-28 17:10:37', '2026-09-28 17:10:37'),
(41, 'JV-2026-000041', '2026-09-28', 'sale', 56, 'SAL-2026-000056', 'Sale SAL-2026-000056', 'posted', 3, 3, '2026-09-28 17:46:38', '2026-09-28 17:46:38', '2026-09-28 17:46:38'),
(42, 'JV-2026-000042', '2026-09-28', 'customer_payment', 45, 'REC-2026-000045', 'Customer payment REC-2026-000045', 'posted', 3, 3, '2026-09-28 17:49:03', '2026-09-28 17:49:03', '2026-09-28 17:49:03'),
(43, 'JV-2026-000043', '2026-09-29', 'sale', 61, 'SAL-2026-000061', 'Sale SAL-2026-000061', 'posted', 3, 3, '2026-09-29 14:03:17', '2026-09-29 14:03:17', '2026-09-29 14:03:17'),
(44, 'JV-2026-000044', '2026-09-29', 'customer_payment', 46, 'REC-2026-000046', 'Customer payment REC-2026-000046', 'posted', 3, 3, '2026-09-29 14:04:11', '2026-09-29 14:04:11', '2026-09-29 14:04:11'),
(45, 'JV-2026-000045', '2026-09-26', 'payroll', 3, 'PAYR-2026-000003', 'Payroll 2026-10', 'posted', 4, 4, '2026-10-08 11:38:42', '2026-10-08 11:38:42', '2026-10-08 11:38:42');

-- --------------------------------------------------------

--
-- Table structure for table `journal_lines`
--

CREATE TABLE `journal_lines` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `journal_entry_id` bigint(20) UNSIGNED NOT NULL,
  `account_id` bigint(20) UNSIGNED NOT NULL,
  `debit` decimal(18,2) NOT NULL DEFAULT 0.00,
  `credit` decimal(18,2) NOT NULL DEFAULT 0.00,
  `party_type` varchar(30) DEFAULT NULL,
  `party_id` bigint(20) UNSIGNED DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `cost_center_id` bigint(20) UNSIGNED DEFAULT NULL,
  `project_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `journal_lines`
--

INSERT INTO `journal_lines` (`id`, `journal_entry_id`, `account_id`, `debit`, `credit`, `party_type`, `party_id`, `description`, `created_at`, `updated_at`, `cost_center_id`, `project_id`) VALUES
(1, 1, 1, 500000.00, 0.00, NULL, NULL, 'Opening cash', '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL, NULL),
(2, 1, 4, 1000000.00, 0.00, NULL, NULL, 'Opening inventory', '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL, NULL),
(3, 1, 7, 0.00, 1500000.00, NULL, NULL, 'Owner equity', '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL, NULL),
(4, 2, 3, 480000.00, 0.00, NULL, NULL, 'Accounts receivable', '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL, NULL),
(5, 2, 9, 0.00, 480000.00, NULL, NULL, 'Sales revenue', '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL, NULL),
(6, 3, 1, 435529.08, 0.00, NULL, NULL, 'Cash/Bank receipts', '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL, NULL),
(7, 3, 3, 0.00, 435529.08, NULL, NULL, 'Accounts receivable', '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL, NULL),
(8, 4, 4, 216000.00, 0.00, NULL, NULL, 'Inventory purchases', '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL, NULL),
(9, 4, 5, 0.00, 216000.00, NULL, NULL, 'Accounts payable', '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL, NULL),
(10, 5, 5, 184247.20, 0.00, NULL, NULL, 'Accounts payable', '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL, NULL),
(11, 5, 2, 0.00, 184247.20, NULL, NULL, 'Bank payments', '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL, NULL),
(12, 6, 14, 72000.00, 0.00, NULL, NULL, 'Operating expenses', '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL, NULL),
(13, 6, 1, 0.00, 72000.00, NULL, NULL, 'Cash expense payments', '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL, NULL),
(14, 7, 3, 525000.00, 0.00, NULL, NULL, 'Accounts receivable', '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL, NULL),
(15, 7, 9, 0.00, 525000.00, NULL, NULL, 'Sales revenue', '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL, NULL),
(16, 8, 1, 476359.93, 0.00, NULL, NULL, 'Cash/Bank receipts', '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL, NULL),
(17, 8, 3, 0.00, 476359.93, NULL, NULL, 'Accounts receivable', '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL, NULL),
(18, 9, 4, 252000.00, 0.00, NULL, NULL, 'Inventory purchases', '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL, NULL),
(19, 9, 5, 0.00, 252000.00, NULL, NULL, 'Accounts payable', '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL, NULL),
(20, 10, 5, 214955.04, 0.00, NULL, NULL, 'Accounts payable', '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL, NULL),
(21, 10, 2, 0.00, 214955.04, NULL, NULL, 'Bank payments', '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL, NULL),
(22, 11, 14, 78000.00, 0.00, NULL, NULL, 'Operating expenses', '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL, NULL),
(23, 11, 1, 0.00, 78000.00, NULL, NULL, 'Cash expense payments', '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL, NULL),
(24, 12, 3, 610000.00, 0.00, NULL, NULL, 'Accounts receivable', '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL, NULL),
(25, 12, 9, 0.00, 610000.00, NULL, NULL, 'Sales revenue', '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL, NULL),
(26, 13, 1, 553484.88, 0.00, NULL, NULL, 'Cash/Bank receipts', '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL, NULL),
(27, 13, 3, 0.00, 553484.88, NULL, NULL, 'Accounts receivable', '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL, NULL),
(28, 14, 4, 280800.00, 0.00, NULL, NULL, 'Inventory purchases', '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL, NULL),
(29, 14, 5, 0.00, 280800.00, NULL, NULL, 'Accounts payable', '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL, NULL),
(30, 15, 5, 239521.32, 0.00, NULL, NULL, 'Accounts payable', '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL, NULL),
(31, 15, 2, 0.00, 239521.32, NULL, NULL, 'Bank payments', '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL, NULL),
(32, 16, 14, 82500.00, 0.00, NULL, NULL, 'Operating expenses', '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL, NULL),
(33, 16, 1, 0.00, 82500.00, NULL, NULL, 'Cash expense payments', '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL, NULL),
(34, 17, 3, 575000.00, 0.00, NULL, NULL, 'Accounts receivable', '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL, NULL),
(35, 17, 9, 0.00, 575000.00, NULL, NULL, 'Sales revenue', '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL, NULL),
(36, 18, 1, 521727.56, 0.00, NULL, NULL, 'Cash/Bank receipts', '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL, NULL),
(37, 18, 3, 0.00, 521727.56, NULL, NULL, 'Accounts receivable', '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL, NULL),
(38, 19, 4, 295200.00, 0.00, NULL, NULL, 'Inventory purchases', '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL, NULL),
(39, 19, 5, 0.00, 295200.00, NULL, NULL, 'Accounts payable', '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL, NULL),
(40, 20, 5, 251804.48, 0.00, NULL, NULL, 'Accounts payable', '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL, NULL),
(41, 20, 2, 0.00, 251804.48, NULL, NULL, 'Bank payments', '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL, NULL),
(42, 21, 14, 86500.00, 0.00, NULL, NULL, 'Operating expenses', '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL, NULL),
(43, 21, 1, 0.00, 86500.00, NULL, NULL, 'Cash expense payments', '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL, NULL),
(44, 22, 3, 640000.00, 0.00, NULL, NULL, 'Accounts receivable', '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL, NULL),
(45, 22, 9, 0.00, 640000.00, NULL, NULL, 'Sales revenue', '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL, NULL),
(46, 23, 1, 580705.44, 0.00, NULL, NULL, 'Cash/Bank receipts', '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL, NULL),
(47, 23, 3, 0.00, 580705.44, NULL, NULL, 'Accounts receivable', '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL, NULL),
(48, 24, 4, 309600.00, 0.00, NULL, NULL, 'Inventory purchases', '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL, NULL),
(49, 24, 5, 0.00, 309600.00, NULL, NULL, 'Accounts payable', '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL, NULL),
(50, 25, 5, 264087.64, 0.00, NULL, NULL, 'Accounts payable', '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL, NULL),
(51, 25, 2, 0.00, 264087.64, NULL, NULL, 'Bank payments', '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL, NULL),
(52, 26, 14, 91000.00, 0.00, NULL, NULL, 'Operating expenses', '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL, NULL),
(53, 26, 1, 0.00, 91000.00, NULL, NULL, 'Cash expense payments', '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL, NULL),
(54, 27, 3, 685250.00, 0.00, NULL, NULL, 'Accounts receivable', '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL, NULL),
(55, 27, 9, 0.00, 685250.00, NULL, NULL, 'Sales revenue', '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL, NULL),
(56, 28, 1, 621763.11, 0.00, NULL, NULL, 'Cash/Bank receipts', '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL, NULL),
(57, 28, 3, 0.00, 621763.11, NULL, NULL, 'Accounts receivable', '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL, NULL),
(58, 29, 4, 316085.00, 0.00, NULL, NULL, 'Inventory purchases', '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL, NULL),
(59, 29, 5, 0.00, 316085.00, NULL, NULL, 'Accounts payable', '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL, NULL),
(60, 30, 5, 269619.32, 0.00, NULL, NULL, 'Accounts payable', '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL, NULL),
(61, 30, 2, 0.00, 269619.32, NULL, NULL, 'Bank payments', '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL, NULL),
(62, 31, 14, 96540.00, 0.00, NULL, NULL, 'Operating expenses', '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL, NULL),
(63, 31, 1, 0.00, 96540.00, NULL, NULL, 'Cash expense payments', '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL, NULL),
(64, 32, 15, 240000.00, 0.00, NULL, NULL, 'Salary expense', '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL, NULL),
(65, 32, 6, 0.00, 240000.00, NULL, NULL, 'Payroll payable', '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL, NULL),
(66, 33, 6, 240000.00, 0.00, NULL, NULL, 'Payroll payable', '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL, NULL),
(67, 33, 2, 0.00, 240000.00, NULL, NULL, 'Bank payment', '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL, NULL),
(68, 34, 15, 255000.00, 0.00, NULL, NULL, NULL, '2026-09-23 16:15:06', '2026-09-23 16:15:06', NULL, NULL),
(69, 34, 6, 0.00, 255000.00, NULL, NULL, NULL, '2026-09-23 16:15:06', '2026-09-23 16:15:06', NULL, NULL),
(70, 35, 2, 10000.00, 0.00, NULL, NULL, NULL, '2026-09-26 19:04:07', '2026-09-26 19:04:07', NULL, NULL),
(71, 35, 3, 0.00, 10000.00, 'customer', 5, NULL, '2026-09-26 19:04:07', '2026-09-26 19:04:07', NULL, NULL),
(72, 36, 10, 240.02, 0.00, NULL, NULL, NULL, '2026-09-26 19:20:21', '2026-09-26 19:20:21', NULL, NULL),
(73, 36, 3, 0.00, 240.02, 'customer', 4, NULL, '2026-09-26 19:20:21', '2026-09-26 19:20:21', NULL, NULL),
(74, 36, 4, 100.01, 0.00, NULL, NULL, NULL, '2026-09-26 19:20:21', '2026-09-26 19:20:21', NULL, NULL),
(75, 36, 13, 0.00, 100.01, NULL, NULL, NULL, '2026-09-26 19:20:21', '2026-09-26 19:20:21', NULL, NULL),
(76, 37, 1, 345.00, 0.00, NULL, NULL, NULL, '2026-09-28 16:05:26', '2026-09-28 16:05:26', NULL, NULL),
(77, 37, 9, 0.00, 345.00, NULL, NULL, NULL, '2026-09-28 16:05:26', '2026-09-28 16:05:26', NULL, NULL),
(78, 37, 13, 100.00, 0.00, NULL, NULL, NULL, '2026-09-28 16:05:26', '2026-09-28 16:05:26', NULL, NULL),
(79, 37, 4, 0.00, 100.00, NULL, NULL, NULL, '2026-09-28 16:05:26', '2026-09-28 16:05:26', NULL, NULL),
(80, 38, 1, 540.00, 0.00, NULL, NULL, NULL, '2026-09-28 16:59:32', '2026-09-28 16:59:32', NULL, NULL),
(81, 38, 9, 0.00, 540.00, NULL, NULL, NULL, '2026-09-28 16:59:32', '2026-09-28 16:59:32', NULL, NULL),
(82, 38, 13, 200.00, 0.00, NULL, NULL, NULL, '2026-09-28 16:59:32', '2026-09-28 16:59:32', NULL, NULL),
(83, 38, 4, 0.00, 200.00, NULL, NULL, NULL, '2026-09-28 16:59:32', '2026-09-28 16:59:32', NULL, NULL),
(84, 39, 3, 152.00, 0.00, 'customer', 5, NULL, '2026-09-28 17:09:49', '2026-09-28 17:09:49', NULL, NULL),
(85, 39, 9, 0.00, 152.00, NULL, NULL, NULL, '2026-09-28 17:09:49', '2026-09-28 17:09:49', NULL, NULL),
(86, 39, 13, 100.00, 0.00, NULL, NULL, NULL, '2026-09-28 17:09:49', '2026-09-28 17:09:49', NULL, NULL),
(87, 39, 4, 0.00, 100.00, NULL, NULL, NULL, '2026-09-28 17:09:49', '2026-09-28 17:09:49', NULL, NULL),
(88, 40, 2, 100.00, 0.00, NULL, NULL, NULL, '2026-09-28 17:10:37', '2026-09-28 17:10:37', NULL, NULL),
(89, 40, 3, 0.00, 100.00, 'customer', 5, NULL, '2026-09-28 17:10:37', '2026-09-28 17:10:37', NULL, NULL),
(90, 41, 1, 540.00, 0.00, NULL, NULL, NULL, '2026-09-28 17:46:38', '2026-09-28 17:46:38', NULL, NULL),
(91, 41, 9, 0.00, 540.00, NULL, NULL, NULL, '2026-09-28 17:46:38', '2026-09-28 17:46:38', NULL, NULL),
(92, 41, 13, 200.00, 0.00, NULL, NULL, NULL, '2026-09-28 17:46:38', '2026-09-28 17:46:38', NULL, NULL),
(93, 41, 4, 0.00, 200.00, NULL, NULL, NULL, '2026-09-28 17:46:38', '2026-09-28 17:46:38', NULL, NULL),
(94, 42, 1, 52.00, 0.00, NULL, NULL, NULL, '2026-09-28 17:49:03', '2026-09-28 17:49:03', NULL, NULL),
(95, 42, 3, 0.00, 52.00, 'customer', 5, NULL, '2026-09-28 17:49:03', '2026-09-28 17:49:03', NULL, NULL),
(96, 43, 3, 345.00, 0.00, 'customer', 5, NULL, '2026-09-29 14:03:17', '2026-09-29 14:03:17', NULL, NULL),
(97, 43, 9, 0.00, 345.00, NULL, NULL, NULL, '2026-09-29 14:03:17', '2026-09-29 14:03:17', NULL, NULL),
(98, 43, 13, 100.00, 0.00, NULL, NULL, NULL, '2026-09-29 14:03:17', '2026-09-29 14:03:17', NULL, NULL),
(99, 43, 4, 0.00, 100.00, NULL, NULL, NULL, '2026-09-29 14:03:17', '2026-09-29 14:03:17', NULL, NULL),
(100, 44, 2, 200.00, 0.00, NULL, NULL, NULL, '2026-09-29 14:04:11', '2026-09-29 14:04:11', NULL, NULL),
(101, 44, 3, 0.00, 200.00, 'customer', 5, NULL, '2026-09-29 14:04:11', '2026-09-29 14:04:11', NULL, NULL),
(102, 45, 15, 255000.00, 0.00, NULL, NULL, NULL, '2026-10-08 11:38:42', '2026-10-08 11:38:42', NULL, NULL),
(103, 45, 6, 0.00, 255000.00, NULL, NULL, NULL, '2026-10-08 11:38:42', '2026-10-08 11:38:42', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `loans`
--

CREATE TABLE `loans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `loan_no` varchar(50) NOT NULL,
  `direction` varchar(20) NOT NULL,
  `counterparty` varchar(200) NOT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `principal` decimal(18,2) NOT NULL,
  `method` varchar(30) NOT NULL DEFAULT 'cash',
  `interest_rate` decimal(8,4) NOT NULL DEFAULT 0.0000,
  `installment_count` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `start_date` date NOT NULL,
  `due_date` date DEFAULT NULL,
  `outstanding_principal` decimal(18,2) NOT NULL,
  `interest_paid` decimal(18,2) NOT NULL DEFAULT 0.00,
  `status` varchar(20) NOT NULL DEFAULT 'active',
  `notes` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `loan_installments`
--

CREATE TABLE `loan_installments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `loan_id` bigint(20) UNSIGNED NOT NULL,
  `installment_no` int(10) UNSIGNED NOT NULL,
  `due_date` date NOT NULL,
  `principal_due` decimal(18,2) NOT NULL DEFAULT 0.00,
  `interest_due` decimal(18,2) NOT NULL DEFAULT 0.00,
  `principal_paid` decimal(18,2) NOT NULL DEFAULT 0.00,
  `interest_paid` decimal(18,2) NOT NULL DEFAULT 0.00,
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `loan_payments`
--

CREATE TABLE `loan_payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `loan_id` bigint(20) UNSIGNED NOT NULL,
  `payment_no` varchar(50) NOT NULL,
  `payment_date` date NOT NULL,
  `principal_amount` decimal(18,2) NOT NULL DEFAULT 0.00,
  `interest_amount` decimal(18,2) NOT NULL DEFAULT 0.00,
  `method` varchar(30) NOT NULL DEFAULT 'cash',
  `reference` varchar(150) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `market_hierarchies`
--

CREATE TABLE `market_hierarchies` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(180) NOT NULL,
  `code` varchar(60) DEFAULT NULL,
  `level_type` varchar(30) NOT NULL DEFAULT 'market',
  `sort_order` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_08_28_000100_create_permission_tables', 1),
(5, '2026_08_28_000200_create_parties_tables', 1),
(6, '2026_08_28_000300_create_product_tables', 1),
(7, '2026_08_28_000400_create_inventory_tables', 1),
(8, '2026_08_28_000500_create_accounting_tables', 1),
(9, '2026_08_28_000600_create_commercial_tables', 1),
(10, '2026_08_28_000700_create_payments_expenses_tables', 1),
(11, '2026_08_28_000800_create_payroll_tables', 1),
(12, '2026_08_28_000900_create_system_tables', 1),
(13, '2026_09_28_001000_add_seller_code_to_users_table', 2),
(14, '2026_09_28_002000_add_sales_memo_fields', 3),
(15, '2026_09_29_003000_add_discount_type_to_sale_items', 4),
(16, '2026_09_30_004000_add_discount_type_to_purchase_items', 5),
(17, '2026_09_30_005000_add_discount_type_to_quotation_items', 6),
(18, '2026_10_01_001000_allow_duplicate_products_in_sale_items', 7),
(19, '2026_10_22_001000_add_price_type_to_sale_items', 8),
(20, '2026_10_22_010000_complete_pending_features', 9),
(21, '2026_10_22_120000_create_product_deliveries_tables', 10),
(22, '2026_10_22_130000_complete_settings_requirements', 11),
(23, '2026_10_23_000000_complete_production_erp_controls', 11),
(24, '2026_10_28_060000_create_pos_checkout_requests_table', 12);

-- --------------------------------------------------------

--
-- Table structure for table `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 1),
(4, 'App\\Models\\User', 4),
(10, 'App\\Models\\User', 2),
(10, 'App\\Models\\User', 3);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payroll_items`
--

CREATE TABLE `payroll_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `payroll_run_id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `basic_salary` decimal(18,2) NOT NULL DEFAULT 0.00,
  `allowance` decimal(18,2) NOT NULL DEFAULT 0.00,
  `bonus` decimal(18,2) NOT NULL DEFAULT 0.00,
  `overtime` decimal(18,2) NOT NULL DEFAULT 0.00,
  `deduction` decimal(18,2) NOT NULL DEFAULT 0.00,
  `gross_salary` decimal(18,2) NOT NULL DEFAULT 0.00,
  `net_salary` decimal(18,2) NOT NULL DEFAULT 0.00,
  `paid_amount` decimal(18,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payroll_items`
--

INSERT INTO `payroll_items` (`id`, `payroll_run_id`, `employee_id`, `basic_salary`, `allowance`, `bonus`, `overtime`, `deduction`, `gross_salary`, `net_salary`, `paid_amount`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 19000.00, 2500.00, 0.00, 0.00, 500.00, 21500.00, 21000.00, 21000.00, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(2, 1, 2, 20000.00, 2500.00, 0.00, 0.00, 500.00, 22500.00, 22000.00, 22000.00, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(3, 1, 3, 21000.00, 2500.00, 0.00, 0.00, 500.00, 23500.00, 23000.00, 23000.00, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(4, 1, 4, 22000.00, 2500.00, 0.00, 0.00, 500.00, 24500.00, 24000.00, 24000.00, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(5, 1, 5, 23000.00, 2500.00, 0.00, 0.00, 500.00, 25500.00, 25000.00, 25000.00, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(6, 1, 6, 24000.00, 2500.00, 0.00, 0.00, 500.00, 26500.00, 26000.00, 26000.00, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(7, 1, 7, 25000.00, 2500.00, 0.00, 0.00, 500.00, 27500.00, 27000.00, 27000.00, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(8, 1, 8, 26000.00, 2500.00, 0.00, 0.00, 500.00, 28500.00, 28000.00, 28000.00, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(9, 1, 9, 27000.00, 2500.00, 0.00, 0.00, 500.00, 29500.00, 29000.00, 29000.00, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(10, 1, 10, 28000.00, 2500.00, 0.00, 0.00, 500.00, 30500.00, 30000.00, 30000.00, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(11, 2, 1, 19000.00, 2500.00, 0.00, 0.00, 500.00, 21500.00, 21000.00, 0.00, '2026-09-23 15:58:27', '2026-09-23 15:58:27'),
(12, 2, 2, 20000.00, 2500.00, 0.00, 0.00, 500.00, 22500.00, 22000.00, 0.00, '2026-09-23 15:58:27', '2026-09-23 15:58:27'),
(13, 2, 3, 21000.00, 2500.00, 0.00, 0.00, 500.00, 23500.00, 23000.00, 0.00, '2026-09-23 15:58:27', '2026-09-23 15:58:27'),
(14, 2, 4, 22000.00, 2500.00, 0.00, 0.00, 500.00, 24500.00, 24000.00, 0.00, '2026-09-23 15:58:27', '2026-09-23 15:58:27'),
(15, 2, 5, 23000.00, 2500.00, 0.00, 0.00, 500.00, 25500.00, 25000.00, 0.00, '2026-09-23 15:58:27', '2026-09-23 15:58:27'),
(16, 2, 6, 24000.00, 2500.00, 0.00, 0.00, 500.00, 26500.00, 26000.00, 0.00, '2026-09-23 15:58:27', '2026-09-23 15:58:27'),
(17, 2, 7, 25000.00, 2500.00, 0.00, 0.00, 500.00, 27500.00, 27000.00, 0.00, '2026-09-23 15:58:27', '2026-09-23 15:58:27'),
(18, 2, 8, 26000.00, 2500.00, 0.00, 0.00, 500.00, 28500.00, 28000.00, 0.00, '2026-09-23 15:58:27', '2026-09-23 15:58:27'),
(19, 2, 9, 27000.00, 2500.00, 0.00, 0.00, 500.00, 29500.00, 29000.00, 0.00, '2026-09-23 15:58:27', '2026-09-23 15:58:27'),
(20, 2, 10, 28000.00, 2500.00, 0.00, 0.00, 500.00, 30500.00, 30000.00, 0.00, '2026-09-23 15:58:27', '2026-09-23 15:58:27'),
(21, 3, 1, 19000.00, 2500.00, 0.00, 0.00, 500.00, 21500.00, 21000.00, 0.00, '2026-09-25 15:50:50', '2026-09-25 15:50:50'),
(22, 3, 2, 20000.00, 2500.00, 0.00, 0.00, 500.00, 22500.00, 22000.00, 0.00, '2026-09-25 15:50:50', '2026-09-25 15:50:50'),
(23, 3, 3, 21000.00, 2500.00, 0.00, 0.00, 500.00, 23500.00, 23000.00, 0.00, '2026-09-25 15:50:50', '2026-09-25 15:50:50'),
(24, 3, 4, 22000.00, 2500.00, 0.00, 0.00, 500.00, 24500.00, 24000.00, 0.00, '2026-09-25 15:50:50', '2026-09-25 15:50:50'),
(25, 3, 5, 23000.00, 2500.00, 0.00, 0.00, 500.00, 25500.00, 25000.00, 0.00, '2026-09-25 15:50:50', '2026-09-25 15:50:50'),
(26, 3, 6, 24000.00, 2500.00, 0.00, 0.00, 500.00, 26500.00, 26000.00, 0.00, '2026-09-25 15:50:50', '2026-09-25 15:50:50'),
(27, 3, 7, 25000.00, 2500.00, 0.00, 0.00, 500.00, 27500.00, 27000.00, 0.00, '2026-09-25 15:50:50', '2026-09-25 15:50:50'),
(28, 3, 8, 26000.00, 2500.00, 0.00, 0.00, 500.00, 28500.00, 28000.00, 0.00, '2026-09-25 15:50:50', '2026-09-25 15:50:50'),
(29, 3, 9, 27000.00, 2500.00, 0.00, 0.00, 500.00, 29500.00, 29000.00, 0.00, '2026-09-25 15:50:50', '2026-09-25 15:50:50'),
(30, 3, 10, 28000.00, 2500.00, 0.00, 0.00, 500.00, 30500.00, 30000.00, 0.00, '2026-09-25 15:50:50', '2026-09-25 15:50:50');

-- --------------------------------------------------------

--
-- Table structure for table `payroll_payments`
--

CREATE TABLE `payroll_payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `payment_no` varchar(50) NOT NULL,
  `payroll_item_id` bigint(20) UNSIGNED NOT NULL,
  `payment_account_id` bigint(20) UNSIGNED NOT NULL,
  `payment_date` date NOT NULL,
  `amount` decimal(18,2) NOT NULL,
  `method` varchar(30) NOT NULL DEFAULT 'cash',
  `reference` varchar(100) DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payroll_payments`
--

INSERT INTO `payroll_payments` (`id`, `payment_no`, `payroll_item_id`, `payment_account_id`, `payment_date`, `amount`, `method`, `reference`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'PPAY-00001', 1, 2, '2026-08-28', 21000.00, 'bank', 'SAL-AUG-001', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(2, 'PPAY-00002', 2, 2, '2026-08-28', 22000.00, 'bank', 'SAL-AUG-002', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(3, 'PPAY-00003', 3, 2, '2026-08-28', 23000.00, 'bank', 'SAL-AUG-003', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(4, 'PPAY-00004', 4, 2, '2026-08-28', 24000.00, 'bank', 'SAL-AUG-004', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(5, 'PPAY-00005', 5, 2, '2026-08-28', 25000.00, 'bank', 'SAL-AUG-005', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(6, 'PPAY-00006', 6, 2, '2026-08-28', 26000.00, 'bank', 'SAL-AUG-006', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(7, 'PPAY-00007', 7, 2, '2026-08-28', 27000.00, 'bank', 'SAL-AUG-007', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(8, 'PPAY-00008', 8, 2, '2026-08-28', 28000.00, 'bank', 'SAL-AUG-008', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(9, 'PPAY-00009', 9, 2, '2026-08-28', 29000.00, 'bank', 'SAL-AUG-009', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(10, 'PPAY-00010', 10, 2, '2026-08-28', 30000.00, 'bank', 'SAL-AUG-010', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00');

-- --------------------------------------------------------

--
-- Table structure for table `payroll_runs`
--

CREATE TABLE `payroll_runs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `payroll_no` varchar(50) NOT NULL,
  `period` varchar(7) NOT NULL,
  `pay_date` date DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'draft',
  `gross_total` decimal(18,2) NOT NULL DEFAULT 0.00,
  `deduction_total` decimal(18,2) NOT NULL DEFAULT 0.00,
  `net_total` decimal(18,2) NOT NULL DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `posted_by` bigint(20) UNSIGNED DEFAULT NULL,
  `posted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payroll_runs`
--

INSERT INTO `payroll_runs` (`id`, `payroll_no`, `period`, `pay_date`, `status`, `gross_total`, `deduction_total`, `net_total`, `notes`, `created_by`, `posted_by`, `posted_at`, `created_at`, `updated_at`) VALUES
(1, 'PR-2026-08', '2026-08', '2026-08-28', 'posted', 245000.00, 5000.00, 240000.00, 'August payroll', 1, 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(2, 'PAYR-2026-000002', '2026-09', NULL, 'posted', 260000.00, 5000.00, 255000.00, NULL, 1, 1, '2026-09-23 16:15:06', '2026-09-23 15:58:27', '2026-09-23 16:15:06'),
(3, 'PAYR-2026-000003', '2026-10', '2026-09-26', 'posted', 260000.00, 5000.00, 255000.00, NULL, 1, 4, '2026-10-08 11:38:42', '2026-09-25 15:50:50', '2026-10-08 11:38:42');

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'users.view', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(2, 'users.create', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(3, 'users.edit', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(4, 'users.delete', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(5, 'roles.view', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(6, 'roles.create', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(7, 'roles.edit', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(8, 'roles.delete', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(9, 'customer_groups.view', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(10, 'customer_groups.create', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(11, 'customer_groups.edit', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(12, 'customer_groups.delete', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(13, 'customers.view', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(14, 'customers.create', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(15, 'customers.edit', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(16, 'customers.delete', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(17, 'customers.ledger', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(18, 'customers.export', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(19, 'supplier_groups.view', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(20, 'supplier_groups.create', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(21, 'supplier_groups.edit', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(22, 'supplier_groups.delete', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(23, 'suppliers.view', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(24, 'suppliers.create', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(25, 'suppliers.edit', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(26, 'suppliers.delete', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(27, 'suppliers.ledger', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(28, 'suppliers.export', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(29, 'units.view', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(30, 'units.create', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(31, 'units.edit', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(32, 'units.delete', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(33, 'categories.view', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(34, 'categories.create', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(35, 'categories.edit', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(36, 'categories.delete', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(37, 'brands.view', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(38, 'brands.create', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(39, 'brands.edit', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(40, 'brands.delete', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(41, 'products.view', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(42, 'products.create', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(43, 'products.edit', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(44, 'products.delete', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(45, 'products.export', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(46, 'products.import', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(47, 'warehouses.view', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(48, 'warehouses.create', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(49, 'warehouses.edit', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(50, 'warehouses.delete', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(51, 'inventory.view', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(52, 'inventory.ledger', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(53, 'inventory.opening', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(54, 'inventory.adjust', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(55, 'inventory.transfer', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(56, 'inventory.export', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(57, 'purchases.view', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(58, 'purchases.create', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(59, 'purchases.edit', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(60, 'purchases.delete', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(61, 'purchases.approve', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(62, 'purchases.print', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(63, 'purchases.export', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(64, 'sales.view', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(65, 'sales.create', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(66, 'sales.edit', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(67, 'sales.delete', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(68, 'sales.approve', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(69, 'sales.print', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(70, 'sales.export', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(71, 'expenses.view', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(72, 'expenses.create', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(73, 'expenses.edit', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(74, 'expenses.delete', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(75, 'expenses.approve', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(76, 'expenses.export', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(77, 'payroll.view', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(78, 'payroll.create', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(79, 'payroll.edit', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(80, 'payroll.approve', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(81, 'payroll.export', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(82, 'accounts.view', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(83, 'accounts.create', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(84, 'accounts.edit', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(85, 'accounts.delete', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(86, 'accounts.approve', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(87, 'accounts.export', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(88, 'reports.view', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(89, 'reports.print', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(90, 'reports.export', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(91, 'settings.view', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(92, 'settings.edit', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(93, 'audit.view', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(154, 'purchases.post', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(157, 'purchase_returns.view', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(158, 'purchase_returns.create', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(159, 'purchase_returns.delete', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(160, 'purchase_returns.post', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(165, 'sales.post', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(168, 'sale_returns.view', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(169, 'sale_returns.create', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(170, 'sale_returns.delete', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(171, 'sale_returns.post', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(172, 'quotations.view', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(173, 'quotations.create', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(174, 'quotations.edit', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(175, 'quotations.delete', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(176, 'quotations.print', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(177, 'customer_payments.view', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(178, 'customer_payments.create', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(179, 'supplier_payments.view', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(180, 'supplier_payments.create', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(181, 'expense_categories.view', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(182, 'expense_categories.create', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(183, 'expense_categories.edit', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(184, 'expense_categories.delete', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(189, 'expenses.post', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(191, 'employees.view', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(192, 'employees.create', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(193, 'employees.edit', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(194, 'employees.delete', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(198, 'payroll.delete', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(199, 'payroll.post', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(200, 'payroll.pay', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(206, 'journals.view', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(207, 'journals.create', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(214, 'notifications.view', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(215, 'purchase_returns.edit', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(216, 'sale_returns.edit', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(217, 'dashboard.view', 'web', '2026-08-28 13:26:02', '2026-08-28 13:26:02'),
(218, 'seller_accounts.view', 'web', '2026-09-28 01:00:07', '2026-09-28 01:00:07'),
(219, 'client_setup.view', 'web', '2026-10-22 12:23:57', '2026-10-22 12:23:57'),
(220, 'reorder_setup.view', 'web', '2026-10-22 12:23:57', '2026-10-22 12:23:57'),
(221, 'reorder_setup.edit', 'web', '2026-10-22 12:23:57', '2026-10-22 12:23:57'),
(222, 'business_days.view', 'web', '2026-10-22 12:23:57', '2026-10-22 12:23:57'),
(223, 'business_days.manage', 'web', '2026-10-22 12:23:57', '2026-10-22 12:23:57'),
(224, 'user_modules.view', 'web', '2026-10-22 12:23:57', '2026-10-22 12:23:57'),
(225, 'user_modules.edit', 'web', '2026-10-22 12:23:57', '2026-10-22 12:23:57'),
(226, 'market_hierarchy.view', 'web', '2026-10-22 12:23:57', '2026-10-22 12:23:57'),
(227, 'market_hierarchy.create', 'web', '2026-10-22 12:23:57', '2026-10-22 12:23:57'),
(228, 'market_hierarchy.edit', 'web', '2026-10-22 12:23:57', '2026-10-22 12:23:57'),
(229, 'market_hierarchy.delete', 'web', '2026-10-22 12:23:57', '2026-10-22 12:23:57'),
(230, 'barcode_print.view', 'web', '2026-10-22 12:23:57', '2026-10-22 12:23:57'),
(231, 'production_controls.view', 'web', '2026-10-23 06:20:12', '2026-10-23 06:20:12'),
(232, 'production_controls.manage', 'web', '2026-10-23 06:20:12', '2026-10-23 06:20:12'),
(233, 'approvals.view', 'web', '2026-10-23 06:20:12', '2026-10-23 06:20:12'),
(234, 'approvals.manage', 'web', '2026-10-23 06:20:12', '2026-10-23 06:20:12'),
(235, 'orders.view', 'web', '2026-10-23 06:20:12', '2026-10-23 06:20:12'),
(236, 'orders.manage', 'web', '2026-10-23 06:20:12', '2026-10-23 06:20:12'),
(237, 'bank_reconciliation.view', 'web', '2026-10-23 06:20:12', '2026-10-23 06:20:12'),
(238, 'bank_reconciliation.manage', 'web', '2026-10-23 06:20:12', '2026-10-23 06:20:12'),
(239, 'taxes.view', 'web', '2026-10-23 06:20:12', '2026-10-23 06:20:12'),
(240, 'taxes.manage', 'web', '2026-10-23 06:20:12', '2026-10-23 06:20:12'),
(241, 'planning.view', 'web', '2026-10-23 06:20:12', '2026-10-23 06:20:12'),
(242, 'planning.manage', 'web', '2026-10-23 06:20:12', '2026-10-23 06:20:12'),
(243, 'assets.view', 'web', '2026-10-23 06:20:12', '2026-10-23 06:20:12'),
(244, 'assets.manage', 'web', '2026-10-23 06:20:12', '2026-10-23 06:20:12');

-- --------------------------------------------------------

--
-- Table structure for table `pos_checkout_requests`
--

CREATE TABLE `pos_checkout_requests` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `token` char(36) NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `request_hash` char(64) NOT NULL,
  `sale_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `parent_product_id` bigint(20) UNSIGNED DEFAULT NULL,
  `code` varchar(50) DEFAULT NULL,
  `sku` varchar(100) NOT NULL,
  `barcode` varchar(150) DEFAULT NULL,
  `name` varchar(200) NOT NULL,
  `variant_name` varchar(180) DEFAULT NULL,
  `variant_attributes` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`variant_attributes`)),
  `category_id` bigint(20) UNSIGNED DEFAULT NULL,
  `brand_id` bigint(20) UNSIGNED DEFAULT NULL,
  `unit_id` bigint(20) UNSIGNED NOT NULL,
  `purchase_price` decimal(18,2) NOT NULL DEFAULT 0.00,
  `selling_price` decimal(18,2) NOT NULL DEFAULT 0.00,
  `wholesale_price` decimal(18,2) DEFAULT NULL,
  `minimum_selling_price` decimal(18,2) DEFAULT NULL,
  `tax_rate` decimal(8,4) NOT NULL DEFAULT 0.0000,
  `reorder_level` decimal(18,4) NOT NULL DEFAULT 0.0000,
  `minimum_stock_level` decimal(18,4) NOT NULL DEFAULT 0.0000,
  `maximum_stock_level` decimal(18,4) DEFAULT NULL,
  `track_stock` tinyint(1) NOT NULL DEFAULT 1,
  `track_serial` tinyint(1) NOT NULL DEFAULT 0,
  `track_batch` tinyint(1) NOT NULL DEFAULT 0,
  `track_expiry` tinyint(1) NOT NULL DEFAULT 0,
  `costing_method` varchar(20) NOT NULL DEFAULT 'average',
  `allow_negative_stock` tinyint(1) NOT NULL DEFAULT 0,
  `image` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `parent_product_id`, `code`, `sku`, `barcode`, `name`, `variant_name`, `variant_attributes`, `category_id`, `brand_id`, `unit_id`, `purchase_price`, `selling_price`, `wholesale_price`, `minimum_selling_price`, `tax_rate`, `reorder_level`, `minimum_stock_level`, `maximum_stock_level`, `track_stock`, `track_serial`, `track_batch`, `track_expiry`, `costing_method`, `allow_negative_stock`, `image`, `description`, `is_active`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, NULL, 'PRD-000001', 'SKU-00001', '8900000000001', 'Samsung Galaxy A55', NULL, NULL, 1, 1, 1, 100.00, 165.00, 151.80, 140.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(2, NULL, 'PRD-000002', 'SKU-00002', '8900000000002', 'HP Laptop 15', NULL, NULL, 2, 2, 1, 100.00, 180.00, 165.60, 153.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(3, NULL, 'PRD-000003', 'SKU-00003', '8900000000003', 'Xiaomi Redmi Note 13', NULL, NULL, 3, 3, 1, 100.00, 195.00, 179.40, 165.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(4, NULL, 'PRD-000004', 'SKU-00004', '8900000000004', 'Walton 32-inch LED TV', NULL, NULL, 4, 4, 1, 100.00, 210.00, 193.20, 178.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(5, NULL, 'PRD-000005', 'SKU-00005', '8900000000005', 'Logitech Wireless Mouse', NULL, NULL, 5, 5, 1, 100.00, 225.00, 207.00, 191.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(6, NULL, 'PRD-000006', 'SKU-00006', '8900000000006', 'Dell Inspiron 15 Laptop', NULL, NULL, 6, 6, 1, 100.00, 240.00, 220.80, 204.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(7, NULL, 'PRD-000007', 'SKU-00007', '8900000000007', 'Toshiba 1TB External HDD', NULL, NULL, 1, 7, 1, 100.00, 255.00, 234.60, 216.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(8, NULL, 'PRD-000008', 'SKU-00008', '8900000000008', 'TP-Link Archer Router', NULL, NULL, 2, 8, 1, 100.00, 270.00, 248.40, 229.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(9, NULL, 'PRD-000009', 'SKU-00009', '8900000000009', 'Lenovo ThinkPad E14', NULL, NULL, 3, 9, 1, 100.00, 285.00, 262.20, 242.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(10, NULL, 'PRD-000010', 'SKU-00010', '8900000000010', 'Samsung Galaxy S24', NULL, NULL, 4, 10, 1, 100.00, 300.00, 276.00, 255.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(11, NULL, 'PRD-000011', 'SKU-00011', '8900000000011', 'HP LaserJet Pro MFP M28a', NULL, NULL, 5, 1, 1, 100.00, 315.00, 289.80, 267.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(12, NULL, 'PRD-000012', 'SKU-00012', '8900000000012', 'Logitech Keyboard K120', NULL, NULL, 6, 2, 1, 100.00, 330.00, 303.60, 280.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(13, NULL, 'PRD-000013', 'SKU-00013', '8900000000013', 'Dell 24-inch Monitor', NULL, NULL, 1, 3, 1, 100.00, 345.00, 317.40, 293.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(14, NULL, 'PRD-000014', 'SKU-00014', '8900000000014', 'Xiaomi Power Bank 20000mAh', NULL, NULL, 2, 4, 1, 100.00, 360.00, 331.20, 306.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(15, NULL, 'PRD-000015', 'SKU-00015', '8900000000015', 'Walton Smart TV 43-inch', NULL, NULL, 3, 5, 1, 100.00, 375.00, 345.00, 318.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(16, NULL, 'PRD-000016', 'SKU-00016', '8900000000016', 'TP-Link 8 Port Switch', NULL, NULL, 4, 6, 1, 100.00, 390.00, 358.80, 331.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(17, NULL, 'PRD-000017', 'SKU-00017', '8900000000017', 'Samsung 1TB SSD', NULL, NULL, 5, 7, 1, 100.00, 405.00, 372.60, 344.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(18, NULL, 'PRD-000018', 'SKU-00018', '8900000000018', 'HP USB-C Dock', NULL, NULL, 6, 8, 1, 100.00, 420.00, 386.40, 357.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(19, NULL, 'PRD-000019', 'SKU-00019', '8900000000019', 'Logitech HD Webcam', NULL, NULL, 1, 9, 1, 100.00, 435.00, 400.20, 369.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(20, NULL, 'PRD-000020', 'SKU-00020', '8900000000020', 'Lenovo Wireless Mouse', NULL, NULL, 2, 10, 1, 100.00, 150.00, 138.00, 127.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(21, NULL, 'PRD-000021', 'SKU-00021', '8900000000021', 'Sample Product 021', NULL, NULL, 3, 1, 1, 100.00, 165.00, 151.80, 140.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(22, NULL, 'PRD-000022', 'SKU-00022', '8900000000022', 'Sample Product 022', NULL, NULL, 4, 2, 1, 100.00, 180.00, 165.60, 153.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(23, NULL, 'PRD-000023', 'SKU-00023', '8900000000023', 'Sample Product 023', NULL, NULL, 5, 3, 1, 100.00, 195.00, 179.40, 165.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(24, NULL, 'PRD-000024', 'SKU-00024', '8900000000024', 'Sample Product 024', NULL, NULL, 6, 4, 1, 100.00, 210.00, 193.20, 178.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(25, NULL, 'PRD-000025', 'SKU-00025', '8900000000025', 'Sample Product 025', NULL, NULL, 1, 5, 1, 100.00, 225.00, 207.00, 191.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(26, NULL, 'PRD-000026', 'SKU-00026', '8900000000026', 'Sample Product 026', NULL, NULL, 2, 6, 1, 100.00, 240.00, 220.80, 204.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(27, NULL, 'PRD-000027', 'SKU-00027', '8900000000027', 'Sample Product 027', NULL, NULL, 3, 7, 1, 100.00, 255.00, 234.60, 216.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(28, NULL, 'PRD-000028', 'SKU-00028', '8900000000028', 'Sample Product 028', NULL, NULL, 4, 8, 1, 100.00, 270.00, 248.40, 229.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(29, NULL, 'PRD-000029', 'SKU-00029', '8900000000029', 'Sample Product 029', NULL, NULL, 5, 9, 1, 100.00, 285.00, 262.20, 242.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(30, NULL, 'PRD-000030', 'SKU-00030', '8900000000030', 'Sample Product 030', NULL, NULL, 6, 10, 1, 100.00, 300.00, 276.00, 255.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(31, NULL, 'PRD-000031', 'SKU-00031', '8900000000031', 'Sample Product 031', NULL, NULL, 1, 1, 1, 100.00, 315.00, 289.80, 267.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(32, NULL, 'PRD-000032', 'SKU-00032', '8900000000032', 'Sample Product 032', NULL, NULL, 2, 2, 1, 100.00, 330.00, 303.60, 280.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(33, NULL, 'PRD-000033', 'SKU-00033', '8900000000033', 'Sample Product 033', NULL, NULL, 3, 3, 1, 100.00, 345.00, 317.40, 293.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(34, NULL, 'PRD-000034', 'SKU-00034', '8900000000034', 'Sample Product 034', NULL, NULL, 4, 4, 1, 100.00, 360.00, 331.20, 306.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(35, NULL, 'PRD-000035', 'SKU-00035', '8900000000035', 'Sample Product 035', NULL, NULL, 5, 5, 1, 100.00, 375.00, 345.00, 318.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(36, NULL, 'PRD-000036', 'SKU-00036', '8900000000036', 'Sample Product 036', NULL, NULL, 6, 6, 1, 100.00, 390.00, 358.80, 331.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(37, NULL, 'PRD-000037', 'SKU-00037', '8900000000037', 'Sample Product 037', NULL, NULL, 1, 7, 1, 100.00, 405.00, 372.60, 344.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(38, NULL, 'PRD-000038', 'SKU-00038', '8900000000038', 'Sample Product 038', NULL, NULL, 2, 8, 1, 100.00, 420.00, 386.40, 357.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(39, NULL, 'PRD-000039', 'SKU-00039', '8900000000039', 'Sample Product 039', NULL, NULL, 3, 9, 1, 100.00, 435.00, 400.20, 369.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(40, NULL, 'PRD-000040', 'SKU-00040', '8900000000040', 'Sample Product 040', NULL, NULL, 4, 10, 1, 100.00, 150.00, 138.00, 127.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(41, NULL, 'PRD-000041', 'SKU-00041', '8900000000041', 'Sample Product 041', NULL, NULL, 5, 1, 1, 100.00, 165.00, 151.80, 140.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(42, NULL, 'PRD-000042', 'SKU-00042', '8900000000042', 'Sample Product 042', NULL, NULL, 6, 2, 1, 100.00, 180.00, 165.60, 153.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(43, NULL, 'PRD-000043', 'SKU-00043', '8900000000043', 'Sample Product 043', NULL, NULL, 1, 3, 1, 100.00, 195.00, 179.40, 165.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(44, NULL, 'PRD-000044', 'SKU-00044', '8900000000044', 'Sample Product 044', NULL, NULL, 2, 4, 1, 100.00, 210.00, 193.20, 178.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(45, NULL, 'PRD-000045', 'SKU-00045', '8900000000045', 'Sample Product 045', NULL, NULL, 3, 5, 1, 100.00, 225.00, 207.00, 191.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(46, NULL, 'PRD-000046', 'SKU-00046', '8900000000046', 'Sample Product 046', NULL, NULL, 4, 6, 1, 100.00, 240.00, 220.80, 204.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(47, NULL, 'PRD-000047', 'SKU-00047', '8900000000047', 'Sample Product 047', NULL, NULL, 5, 7, 1, 100.00, 255.00, 234.60, 216.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(48, NULL, 'PRD-000048', 'SKU-00048', '8900000000048', 'Sample Product 048', NULL, NULL, 6, 8, 1, 100.00, 270.00, 248.40, 229.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(49, NULL, 'PRD-000049', 'SKU-00049', '8900000000049', 'Sample Product 049', NULL, NULL, 1, 9, 1, 100.00, 285.00, 262.20, 242.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(50, NULL, 'PRD-000050', 'SKU-00050', '8900000000050', 'Sample Product 050', NULL, NULL, 2, 10, 1, 100.00, 300.00, 276.00, 255.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(51, NULL, 'PRD-000051', 'SKU-00051', '8900000000051', 'Sample Product 051', NULL, NULL, 3, 1, 1, 100.00, 315.00, 289.80, 267.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(52, NULL, 'PRD-000052', 'SKU-00052', '8900000000052', 'Sample Product 052', NULL, NULL, 4, 2, 1, 100.00, 330.00, 303.60, 280.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(53, NULL, 'PRD-000053', 'SKU-00053', '8900000000053', 'Sample Product 053', NULL, NULL, 5, 3, 1, 100.00, 345.00, 317.40, 293.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(54, NULL, 'PRD-000054', 'SKU-00054', '8900000000054', 'Sample Product 054', NULL, NULL, 6, 4, 1, 100.00, 360.00, 331.20, 306.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(55, NULL, 'PRD-000055', 'SKU-00055', '8900000000055', 'Sample Product 055', NULL, NULL, 1, 5, 1, 100.00, 375.00, 345.00, 318.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(56, NULL, 'PRD-000056', 'SKU-00056', '8900000000056', 'Sample Product 056', NULL, NULL, 2, 6, 1, 100.00, 390.00, 358.80, 331.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(57, NULL, 'PRD-000057', 'SKU-00057', '8900000000057', 'Sample Product 057', NULL, NULL, 3, 7, 1, 100.00, 405.00, 372.60, 344.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(58, NULL, 'PRD-000058', 'SKU-00058', '8900000000058', 'Sample Product 058', NULL, NULL, 4, 8, 1, 100.00, 420.00, 386.40, 357.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(59, NULL, 'PRD-000059', 'SKU-00059', '8900000000059', 'Sample Product 059', NULL, NULL, 5, 9, 1, 100.00, 435.00, 400.20, 369.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(60, NULL, 'PRD-000060', 'SKU-00060', '8900000000060', 'Sample Product 060', NULL, NULL, 6, 10, 1, 100.00, 150.00, 138.00, 127.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(61, NULL, 'PRD-000061', 'SKU-00061', '8900000000061', 'Sample Product 061', NULL, NULL, 1, 1, 1, 100.00, 165.00, 151.80, 140.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(62, NULL, 'PRD-000062', 'SKU-00062', '8900000000062', 'Sample Product 062', NULL, NULL, 2, 2, 1, 100.00, 180.00, 165.60, 153.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(63, NULL, 'PRD-000063', 'SKU-00063', '8900000000063', 'Sample Product 063', NULL, NULL, 3, 3, 1, 100.00, 195.00, 179.40, 165.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(64, NULL, 'PRD-000064', 'SKU-00064', '8900000000064', 'Sample Product 064', NULL, NULL, 4, 4, 1, 100.00, 210.00, 193.20, 178.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(65, NULL, 'PRD-000065', 'SKU-00065', '8900000000065', 'Sample Product 065', NULL, NULL, 5, 5, 1, 100.00, 225.00, 207.00, 191.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(66, NULL, 'PRD-000066', 'SKU-00066', '8900000000066', 'Sample Product 066', NULL, NULL, 6, 6, 1, 100.00, 240.00, 220.80, 204.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(67, NULL, 'PRD-000067', 'SKU-00067', '8900000000067', 'Sample Product 067', NULL, NULL, 1, 7, 1, 100.00, 255.00, 234.60, 216.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(68, NULL, 'PRD-000068', 'SKU-00068', '8900000000068', 'Sample Product 068', NULL, NULL, 2, 8, 1, 100.00, 270.00, 248.40, 229.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(69, NULL, 'PRD-000069', 'SKU-00069', '8900000000069', 'Sample Product 069', NULL, NULL, 3, 9, 1, 100.00, 285.00, 262.20, 242.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(70, NULL, 'PRD-000070', 'SKU-00070', '8900000000070', 'Sample Product 070', NULL, NULL, 4, 10, 1, 100.00, 300.00, 276.00, 255.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(71, NULL, 'PRD-000071', 'SKU-00071', '8900000000071', 'Sample Product 071', NULL, NULL, 5, 1, 1, 100.00, 315.00, 289.80, 267.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(72, NULL, 'PRD-000072', 'SKU-00072', '8900000000072', 'Sample Product 072', NULL, NULL, 6, 2, 1, 100.00, 330.00, 303.60, 280.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(73, NULL, 'PRD-000073', 'SKU-00073', '8900000000073', 'Sample Product 073', NULL, NULL, 1, 3, 1, 100.00, 345.00, 317.40, 293.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(74, NULL, 'PRD-000074', 'SKU-00074', '8900000000074', 'Sample Product 074', NULL, NULL, 2, 4, 1, 100.00, 360.00, 331.20, 306.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(75, NULL, 'PRD-000075', 'SKU-00075', '8900000000075', 'Sample Product 075', NULL, NULL, 3, 5, 1, 100.00, 375.00, 345.00, 318.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(76, NULL, 'PRD-000076', 'SKU-00076', '8900000000076', 'Sample Product 076', NULL, NULL, 4, 6, 1, 100.00, 390.00, 358.80, 331.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(77, NULL, 'PRD-000077', 'SKU-00077', '8900000000077', 'Sample Product 077', NULL, NULL, 5, 7, 1, 100.00, 405.00, 372.60, 344.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(78, NULL, 'PRD-000078', 'SKU-00078', '8900000000078', 'Sample Product 078', NULL, NULL, 6, 8, 1, 100.00, 420.00, 386.40, 357.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(79, NULL, 'PRD-000079', 'SKU-00079', '8900000000079', 'Sample Product 079', NULL, NULL, 1, 9, 1, 100.00, 435.00, 400.20, 369.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(80, NULL, 'PRD-000080', 'SKU-00080', '8900000000080', 'Sample Product 080', NULL, NULL, 2, 10, 1, 100.00, 150.00, 138.00, 127.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(81, NULL, 'PRD-000081', 'SKU-00081', '8900000000081', 'Sample Product 081', NULL, NULL, 3, 1, 1, 100.00, 165.00, 151.80, 140.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(82, NULL, 'PRD-000082', 'SKU-00082', '8900000000082', 'Sample Product 082', NULL, NULL, 4, 2, 1, 100.00, 180.00, 165.60, 153.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(83, NULL, 'PRD-000083', 'SKU-00083', '8900000000083', 'Sample Product 083', NULL, NULL, 5, 3, 1, 100.00, 195.00, 179.40, 165.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(84, NULL, 'PRD-000084', 'SKU-00084', '8900000000084', 'Sample Product 084', NULL, NULL, 6, 4, 1, 100.00, 210.00, 193.20, 178.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(85, NULL, 'PRD-000085', 'SKU-00085', '8900000000085', 'Sample Product 085', NULL, NULL, 1, 5, 1, 100.00, 225.00, 207.00, 191.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(86, NULL, 'PRD-000086', 'SKU-00086', '8900000000086', 'Sample Product 086', NULL, NULL, 2, 6, 1, 100.00, 240.00, 220.80, 204.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(87, NULL, 'PRD-000087', 'SKU-00087', '8900000000087', 'Sample Product 087', NULL, NULL, 3, 7, 1, 100.00, 255.00, 234.60, 216.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(88, NULL, 'PRD-000088', 'SKU-00088', '8900000000088', 'Sample Product 088', NULL, NULL, 4, 8, 1, 100.00, 270.00, 248.40, 229.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(89, NULL, 'PRD-000089', 'SKU-00089', '8900000000089', 'Sample Product 089', NULL, NULL, 5, 9, 1, 100.00, 285.00, 262.20, 242.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(90, NULL, 'PRD-000090', 'SKU-00090', '8900000000090', 'Sample Product 090', NULL, NULL, 6, 10, 1, 100.00, 300.00, 276.00, 255.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(91, NULL, 'PRD-000091', 'SKU-00091', '8900000000091', 'Sample Product 091', NULL, NULL, 1, 1, 1, 100.00, 315.00, 289.80, 267.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(92, NULL, 'PRD-000092', 'SKU-00092', '8900000000092', 'Sample Product 092', NULL, NULL, 2, 2, 1, 100.00, 330.00, 303.60, 280.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(93, NULL, 'PRD-000093', 'SKU-00093', '8900000000093', 'Sample Product 093', NULL, NULL, 3, 3, 1, 100.00, 345.00, 317.40, 293.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(94, NULL, 'PRD-000094', 'SKU-00094', '8900000000094', 'Sample Product 094', NULL, NULL, 4, 4, 1, 100.00, 360.00, 331.20, 306.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(95, NULL, 'PRD-000095', 'SKU-00095', '8900000000095', 'Sample Product 095', NULL, NULL, 5, 5, 1, 100.00, 375.00, 345.00, 318.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(96, NULL, 'PRD-000096', 'SKU-00096', '8900000000096', 'Sample Product 096', NULL, NULL, 6, 6, 1, 100.00, 390.00, 358.80, 331.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(97, NULL, 'PRD-000097', 'SKU-00097', '8900000000097', 'Sample Product 097', NULL, NULL, 1, 7, 1, 100.00, 405.00, 372.60, 344.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(98, NULL, 'PRD-000098', 'SKU-00098', '8900000000098', 'Sample Product 098', NULL, NULL, 2, 8, 1, 100.00, 420.00, 386.40, 357.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(99, NULL, 'PRD-000099', 'SKU-00099', '8900000000099', 'Sample Product 099', NULL, NULL, 3, 9, 1, 100.00, 435.00, 400.20, 369.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(100, NULL, 'PRD-000100', 'SKU-00100', '8900000000100', 'Sample Product 100', NULL, NULL, 4, 10, 1, 100.00, 150.00, 138.00, 127.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(101, NULL, 'PRD-000101', 'SKU-00101', '8900000000101', 'Sample Product 101', NULL, NULL, 5, 1, 1, 100.00, 165.00, 151.80, 140.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(102, NULL, 'PRD-000102', 'SKU-00102', '8900000000102', 'Sample Product 102', NULL, NULL, 6, 2, 1, 100.00, 180.00, 165.60, 153.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(103, NULL, 'PRD-000103', 'SKU-00103', '8900000000103', 'Sample Product 103', NULL, NULL, 1, 3, 1, 100.00, 195.00, 179.40, 165.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(104, NULL, 'PRD-000104', 'SKU-00104', '8900000000104', 'Sample Product 104', NULL, NULL, 2, 4, 1, 100.00, 210.00, 193.20, 178.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(105, NULL, 'PRD-000105', 'SKU-00105', '8900000000105', 'Sample Product 105', NULL, NULL, 3, 5, 1, 100.00, 225.00, 207.00, 191.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(106, NULL, 'PRD-000106', 'SKU-00106', '8900000000106', 'Sample Product 106', NULL, NULL, 4, 6, 1, 100.00, 240.00, 220.80, 204.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(107, NULL, 'PRD-000107', 'SKU-00107', '8900000000107', 'Sample Product 107', NULL, NULL, 5, 7, 1, 100.00, 255.00, 234.60, 216.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(108, NULL, 'PRD-000108', 'SKU-00108', '8900000000108', 'Sample Product 108', NULL, NULL, 6, 8, 1, 100.00, 270.00, 248.40, 229.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(109, NULL, 'PRD-000109', 'SKU-00109', '8900000000109', 'Sample Product 109', NULL, NULL, 1, 9, 1, 100.00, 285.00, 262.20, 242.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(110, NULL, 'PRD-000110', 'SKU-00110', '8900000000110', 'Sample Product 110', NULL, NULL, 2, 10, 1, 100.00, 300.00, 276.00, 255.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(111, NULL, 'PRD-000111', 'SKU-00111', '8900000000111', 'Sample Product 111', NULL, NULL, 3, 1, 1, 100.00, 315.00, 289.80, 267.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(112, NULL, 'PRD-000112', 'SKU-00112', '8900000000112', 'Sample Product 112', NULL, NULL, 4, 2, 1, 100.00, 330.00, 303.60, 280.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(113, NULL, 'PRD-000113', 'SKU-00113', '8900000000113', 'Sample Product 113', NULL, NULL, 5, 3, 1, 100.00, 345.00, 317.40, 293.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(114, NULL, 'PRD-000114', 'SKU-00114', '8900000000114', 'Sample Product 114', NULL, NULL, 6, 4, 1, 100.00, 360.00, 331.20, 306.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(115, NULL, 'PRD-000115', 'SKU-00115', '8900000000115', 'Sample Product 115', NULL, NULL, 1, 5, 1, 100.00, 375.00, 345.00, 318.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(116, NULL, 'PRD-000116', 'SKU-00116', '8900000000116', 'Sample Product 116', NULL, NULL, 2, 6, 1, 100.00, 390.00, 358.80, 331.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(117, NULL, 'PRD-000117', 'SKU-00117', '8900000000117', 'Sample Product 117', NULL, NULL, 3, 7, 1, 100.00, 405.00, 372.60, 344.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(118, NULL, 'PRD-000118', 'SKU-00118', '8900000000118', 'Sample Product 118', NULL, NULL, 4, 8, 1, 100.00, 420.00, 386.40, 357.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(119, NULL, 'PRD-000119', 'SKU-00119', '8900000000119', 'Sample Product 119', NULL, NULL, 5, 9, 1, 100.00, 435.00, 400.20, 369.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(120, NULL, 'PRD-000120', 'SKU-00120', '8900000000120', 'Sample Product 120', NULL, NULL, 6, 10, 1, 100.00, 150.00, 138.00, 127.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(121, NULL, 'PRD-000121', 'SKU-00121', '8900000000121', 'Sample Product 121', NULL, NULL, 1, 1, 1, 100.00, 165.00, 151.80, 140.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(122, NULL, 'PRD-000122', 'SKU-00122', '8900000000122', 'Sample Product 122', NULL, NULL, 2, 2, 1, 100.00, 180.00, 165.60, 153.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(123, NULL, 'PRD-000123', 'SKU-00123', '8900000000123', 'Sample Product 123', NULL, NULL, 3, 3, 1, 100.00, 195.00, 179.40, 165.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(124, NULL, 'PRD-000124', 'SKU-00124', '8900000000124', 'Sample Product 124', NULL, NULL, 4, 4, 1, 100.00, 210.00, 193.20, 178.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(125, NULL, 'PRD-000125', 'SKU-00125', '8900000000125', 'Sample Product 125', NULL, NULL, 5, 5, 1, 100.00, 225.00, 207.00, 191.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(126, NULL, 'PRD-000126', 'SKU-00126', '8900000000126', 'Sample Product 126', NULL, NULL, 6, 6, 1, 100.00, 240.00, 220.80, 204.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(127, NULL, 'PRD-000127', 'SKU-00127', '8900000000127', 'Sample Product 127', NULL, NULL, 1, 7, 1, 100.00, 255.00, 234.60, 216.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(128, NULL, 'PRD-000128', 'SKU-00128', '8900000000128', 'Sample Product 128', NULL, NULL, 2, 8, 1, 100.00, 270.00, 248.40, 229.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(129, NULL, 'PRD-000129', 'SKU-00129', '8900000000129', 'Sample Product 129', NULL, NULL, 3, 9, 1, 100.00, 285.00, 262.20, 242.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(130, NULL, 'PRD-000130', 'SKU-00130', '8900000000130', 'Sample Product 130', NULL, NULL, 4, 10, 1, 100.00, 300.00, 276.00, 255.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(131, NULL, 'PRD-000131', 'SKU-00131', '8900000000131', 'Sample Product 131', NULL, NULL, 5, 1, 1, 100.00, 315.00, 289.80, 267.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(132, NULL, 'PRD-000132', 'SKU-00132', '8900000000132', 'Sample Product 132', NULL, NULL, 6, 2, 1, 100.00, 330.00, 303.60, 280.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(133, NULL, 'PRD-000133', 'SKU-00133', '8900000000133', 'Sample Product 133', NULL, NULL, 1, 3, 1, 100.00, 345.00, 317.40, 293.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(134, NULL, 'PRD-000134', 'SKU-00134', '8900000000134', 'Sample Product 134', NULL, NULL, 2, 4, 1, 100.00, 360.00, 331.20, 306.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(135, NULL, 'PRD-000135', 'SKU-00135', '8900000000135', 'Sample Product 135', NULL, NULL, 3, 5, 1, 100.00, 375.00, 345.00, 318.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(136, NULL, 'PRD-000136', 'SKU-00136', '8900000000136', 'Sample Product 136', NULL, NULL, 4, 6, 1, 100.00, 390.00, 358.80, 331.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(137, NULL, 'PRD-000137', 'SKU-00137', '8900000000137', 'Sample Product 137', NULL, NULL, 5, 7, 1, 100.00, 405.00, 372.60, 344.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(138, NULL, 'PRD-000138', 'SKU-00138', '8900000000138', 'Sample Product 138', NULL, NULL, 6, 8, 1, 100.00, 420.00, 386.40, 357.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(139, NULL, 'PRD-000139', 'SKU-00139', '8900000000139', 'Sample Product 139', NULL, NULL, 1, 9, 1, 100.00, 435.00, 400.20, 369.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(140, NULL, 'PRD-000140', 'SKU-00140', '8900000000140', 'Sample Product 140', NULL, NULL, 2, 10, 1, 100.00, 150.00, 138.00, 127.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(141, NULL, 'PRD-000141', 'SKU-00141', '8900000000141', 'Sample Product 141', NULL, NULL, 3, 1, 1, 100.00, 165.00, 151.80, 140.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(142, NULL, 'PRD-000142', 'SKU-00142', '8900000000142', 'Sample Product 142', NULL, NULL, 4, 2, 1, 100.00, 180.00, 165.60, 153.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(143, NULL, 'PRD-000143', 'SKU-00143', '8900000000143', 'Sample Product 143', NULL, NULL, 5, 3, 1, 100.00, 195.00, 179.40, 165.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(144, NULL, 'PRD-000144', 'SKU-00144', '8900000000144', 'Sample Product 144', NULL, NULL, 6, 4, 1, 100.00, 210.00, 193.20, 178.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(145, NULL, 'PRD-000145', 'SKU-00145', '8900000000145', 'Sample Product 145', NULL, NULL, 1, 5, 1, 100.00, 225.00, 207.00, 191.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(146, NULL, 'PRD-000146', 'SKU-00146', '8900000000146', 'Sample Product 146', NULL, NULL, 2, 6, 1, 100.00, 240.00, 220.80, 204.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(147, NULL, 'PRD-000147', 'SKU-00147', '8900000000147', 'Sample Product 147', NULL, NULL, 3, 7, 1, 100.00, 255.00, 234.60, 216.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(148, NULL, 'PRD-000148', 'SKU-00148', '8900000000148', 'Sample Product 148', NULL, NULL, 4, 8, 1, 100.00, 270.00, 248.40, 229.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(149, NULL, 'PRD-000149', 'SKU-00149', '8900000000149', 'Sample Product 149', NULL, NULL, 5, 9, 1, 100.00, 285.00, 262.20, 242.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(150, NULL, 'PRD-000150', 'SKU-00150', '8900000000150', 'Sample Product 150', NULL, NULL, 6, 10, 1, 100.00, 300.00, 276.00, 255.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(151, NULL, 'PRD-000151', 'SKU-00151', '8900000000151', 'Sample Product 151', NULL, NULL, 1, 1, 1, 100.00, 315.00, 289.80, 267.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(152, NULL, 'PRD-000152', 'SKU-00152', '8900000000152', 'Sample Product 152', NULL, NULL, 2, 2, 1, 100.00, 330.00, 303.60, 280.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(153, NULL, 'PRD-000153', 'SKU-00153', '8900000000153', 'Sample Product 153', NULL, NULL, 3, 3, 1, 100.00, 345.00, 317.40, 293.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(154, NULL, 'PRD-000154', 'SKU-00154', '8900000000154', 'Sample Product 154', NULL, NULL, 4, 4, 1, 100.00, 360.00, 331.20, 306.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(155, NULL, 'PRD-000155', 'SKU-00155', '8900000000155', 'Sample Product 155', NULL, NULL, 5, 5, 1, 100.00, 375.00, 345.00, 318.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(156, NULL, 'PRD-000156', 'SKU-00156', '8900000000156', 'Sample Product 156', NULL, NULL, 6, 6, 1, 100.00, 390.00, 358.80, 331.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(157, NULL, 'PRD-000157', 'SKU-00157', '8900000000157', 'Sample Product 157', NULL, NULL, 1, 7, 1, 100.00, 405.00, 372.60, 344.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(158, NULL, 'PRD-000158', 'SKU-00158', '8900000000158', 'Sample Product 158', NULL, NULL, 2, 8, 1, 100.00, 420.00, 386.40, 357.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(159, NULL, 'PRD-000159', 'SKU-00159', '8900000000159', 'Sample Product 159', NULL, NULL, 3, 9, 1, 100.00, 435.00, 400.20, 369.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(160, NULL, 'PRD-000160', 'SKU-00160', '8900000000160', 'Sample Product 160', NULL, NULL, 4, 10, 1, 100.00, 150.00, 138.00, 127.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(161, NULL, 'PRD-000161', 'SKU-00161', '8900000000161', 'Sample Product 161', NULL, NULL, 5, 1, 1, 100.00, 165.00, 151.80, 140.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(162, NULL, 'PRD-000162', 'SKU-00162', '8900000000162', 'Sample Product 162', NULL, NULL, 6, 2, 1, 100.00, 180.00, 165.60, 153.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(163, NULL, 'PRD-000163', 'SKU-00163', '8900000000163', 'Sample Product 163', NULL, NULL, 1, 3, 1, 100.00, 195.00, 179.40, 165.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(164, NULL, 'PRD-000164', 'SKU-00164', '8900000000164', 'Sample Product 164', NULL, NULL, 2, 4, 1, 100.00, 210.00, 193.20, 178.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(165, NULL, 'PRD-000165', 'SKU-00165', '8900000000165', 'Sample Product 165', NULL, NULL, 3, 5, 1, 100.00, 225.00, 207.00, 191.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(166, NULL, 'PRD-000166', 'SKU-00166', '8900000000166', 'Sample Product 166', NULL, NULL, 4, 6, 1, 100.00, 240.00, 220.80, 204.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(167, NULL, 'PRD-000167', 'SKU-00167', '8900000000167', 'Sample Product 167', NULL, NULL, 5, 7, 1, 100.00, 255.00, 234.60, 216.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(168, NULL, 'PRD-000168', 'SKU-00168', '8900000000168', 'Sample Product 168', NULL, NULL, 6, 8, 1, 100.00, 270.00, 248.40, 229.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(169, NULL, 'PRD-000169', 'SKU-00169', '8900000000169', 'Sample Product 169', NULL, NULL, 1, 9, 1, 100.00, 285.00, 262.20, 242.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(170, NULL, 'PRD-000170', 'SKU-00170', '8900000000170', 'Sample Product 170', NULL, NULL, 2, 10, 1, 100.00, 300.00, 276.00, 255.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(171, NULL, 'PRD-000171', 'SKU-00171', '8900000000171', 'Sample Product 171', NULL, NULL, 3, 1, 1, 100.00, 315.00, 289.80, 267.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(172, NULL, 'PRD-000172', 'SKU-00172', '8900000000172', 'Sample Product 172', NULL, NULL, 4, 2, 1, 100.00, 330.00, 303.60, 280.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(173, NULL, 'PRD-000173', 'SKU-00173', '8900000000173', 'Sample Product 173', NULL, NULL, 5, 3, 1, 100.00, 345.00, 317.40, 293.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(174, NULL, 'PRD-000174', 'SKU-00174', '8900000000174', 'Sample Product 174', NULL, NULL, 6, 4, 1, 100.00, 360.00, 331.20, 306.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(175, NULL, 'PRD-000175', 'SKU-00175', '8900000000175', 'Sample Product 175', NULL, NULL, 1, 5, 1, 100.00, 375.00, 345.00, 318.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(176, NULL, 'PRD-000176', 'SKU-00176', '8900000000176', 'Sample Product 176', NULL, NULL, 2, 6, 1, 100.00, 390.00, 358.80, 331.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(177, NULL, 'PRD-000177', 'SKU-00177', '8900000000177', 'Sample Product 177', NULL, NULL, 3, 7, 1, 100.00, 405.00, 372.60, 344.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(178, NULL, 'PRD-000178', 'SKU-00178', '8900000000178', 'Sample Product 178', NULL, NULL, 4, 8, 1, 100.00, 420.00, 386.40, 357.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(179, NULL, 'PRD-000179', 'SKU-00179', '8900000000179', 'Sample Product 179', NULL, NULL, 5, 9, 1, 100.00, 435.00, 400.20, 369.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(180, NULL, 'PRD-000180', 'SKU-00180', '8900000000180', 'Sample Product 180', NULL, NULL, 6, 10, 1, 100.00, 150.00, 138.00, 127.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL);
INSERT INTO `products` (`id`, `parent_product_id`, `code`, `sku`, `barcode`, `name`, `variant_name`, `variant_attributes`, `category_id`, `brand_id`, `unit_id`, `purchase_price`, `selling_price`, `wholesale_price`, `minimum_selling_price`, `tax_rate`, `reorder_level`, `minimum_stock_level`, `maximum_stock_level`, `track_stock`, `track_serial`, `track_batch`, `track_expiry`, `costing_method`, `allow_negative_stock`, `image`, `description`, `is_active`, `created_at`, `updated_at`, `deleted_at`) VALUES
(181, NULL, 'PRD-000181', 'SKU-00181', '8900000000181', 'Sample Product 181', NULL, NULL, 1, 1, 1, 100.00, 165.00, 151.80, 140.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(182, NULL, 'PRD-000182', 'SKU-00182', '8900000000182', 'Sample Product 182', NULL, NULL, 2, 2, 1, 100.00, 180.00, 165.60, 153.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(183, NULL, 'PRD-000183', 'SKU-00183', '8900000000183', 'Sample Product 183', NULL, NULL, 3, 3, 1, 100.00, 195.00, 179.40, 165.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(184, NULL, 'PRD-000184', 'SKU-00184', '8900000000184', 'Sample Product 184', NULL, NULL, 4, 4, 1, 100.00, 210.00, 193.20, 178.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(185, NULL, 'PRD-000185', 'SKU-00185', '8900000000185', 'Sample Product 185', NULL, NULL, 5, 5, 1, 100.00, 225.00, 207.00, 191.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(186, NULL, 'PRD-000186', 'SKU-00186', '8900000000186', 'Sample Product 186', NULL, NULL, 6, 6, 1, 100.00, 240.00, 220.80, 204.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(187, NULL, 'PRD-000187', 'SKU-00187', '8900000000187', 'Sample Product 187', NULL, NULL, 1, 7, 1, 100.00, 255.00, 234.60, 216.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(188, NULL, 'PRD-000188', 'SKU-00188', '8900000000188', 'Sample Product 188', NULL, NULL, 2, 8, 1, 100.00, 270.00, 248.40, 229.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(189, NULL, 'PRD-000189', 'SKU-00189', '8900000000189', 'Sample Product 189', NULL, NULL, 3, 9, 1, 100.00, 285.00, 262.20, 242.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(190, NULL, 'PRD-000190', 'SKU-00190', '8900000000190', 'Sample Product 190', NULL, NULL, 4, 10, 1, 100.00, 300.00, 276.00, 255.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(191, NULL, 'PRD-000191', 'SKU-00191', '8900000000191', 'Sample Product 191', NULL, NULL, 5, 1, 1, 100.00, 315.00, 289.80, 267.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(192, NULL, 'PRD-000192', 'SKU-00192', '8900000000192', 'Sample Product 192', NULL, NULL, 6, 2, 1, 100.00, 330.00, 303.60, 280.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(193, NULL, 'PRD-000193', 'SKU-00193', '8900000000193', 'Sample Product 193', NULL, NULL, 1, 3, 1, 100.00, 345.00, 317.40, 293.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(194, NULL, 'PRD-000194', 'SKU-00194', '8900000000194', 'Sample Product 194', NULL, NULL, 2, 4, 1, 100.00, 360.00, 331.20, 306.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(195, NULL, 'PRD-000195', 'SKU-00195', '8900000000195', 'Sample Product 195', NULL, NULL, 3, 5, 1, 100.00, 375.00, 345.00, 318.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(196, NULL, 'PRD-000196', 'SKU-00196', '8900000000196', 'Sample Product 196', NULL, NULL, 4, 6, 1, 100.00, 390.00, 358.80, 331.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(197, NULL, 'PRD-000197', 'SKU-00197', '8900000000197', 'Sample Product 197', NULL, NULL, 5, 7, 1, 100.00, 405.00, 372.60, 344.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(198, NULL, 'PRD-000198', 'SKU-00198', '8900000000198', 'Sample Product 198', NULL, NULL, 6, 8, 1, 100.00, 420.00, 386.40, 357.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(199, NULL, 'PRD-000199', 'SKU-00199', '8900000000199', 'Sample Product 199', NULL, NULL, 1, 9, 1, 100.00, 435.00, 400.20, 369.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(200, NULL, 'PRD-000200', 'SKU-00200', '8900000000200', 'Sample Product 200', NULL, NULL, 2, 10, 1, 100.00, 150.00, 138.00, 127.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(201, NULL, 'PRD-000201', 'SKU-00201', '8900000000201', 'Sample Product 201', NULL, NULL, 3, 1, 1, 100.00, 165.00, 151.80, 140.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(202, NULL, 'PRD-000202', 'SKU-00202', '8900000000202', 'Sample Product 202', NULL, NULL, 4, 2, 1, 100.00, 180.00, 165.60, 153.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(203, NULL, 'PRD-000203', 'SKU-00203', '8900000000203', 'Sample Product 203', NULL, NULL, 5, 3, 1, 100.00, 195.00, 179.40, 165.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(204, NULL, 'PRD-000204', 'SKU-00204', '8900000000204', 'Sample Product 204', NULL, NULL, 6, 4, 1, 100.00, 210.00, 193.20, 178.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(205, NULL, 'PRD-000205', 'SKU-00205', '8900000000205', 'Sample Product 205', NULL, NULL, 1, 5, 1, 100.00, 225.00, 207.00, 191.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(206, NULL, 'PRD-000206', 'SKU-00206', '8900000000206', 'Sample Product 206', NULL, NULL, 2, 6, 1, 100.00, 240.00, 220.80, 204.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(207, NULL, 'PRD-000207', 'SKU-00207', '8900000000207', 'Sample Product 207', NULL, NULL, 3, 7, 1, 100.00, 255.00, 234.60, 216.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(208, NULL, 'PRD-000208', 'SKU-00208', '8900000000208', 'Sample Product 208', NULL, NULL, 4, 8, 1, 100.00, 270.00, 248.40, 229.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(209, NULL, 'PRD-000209', 'SKU-00209', '8900000000209', 'Sample Product 209', NULL, NULL, 5, 9, 1, 100.00, 285.00, 262.20, 242.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(210, NULL, 'PRD-000210', 'SKU-00210', '8900000000210', 'Sample Product 210', NULL, NULL, 6, 10, 1, 100.00, 300.00, 276.00, 255.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(211, NULL, 'PRD-000211', 'SKU-00211', '8900000000211', 'Sample Product 211', NULL, NULL, 1, 1, 1, 100.00, 315.00, 289.80, 267.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(212, NULL, 'PRD-000212', 'SKU-00212', '8900000000212', 'Sample Product 212', NULL, NULL, 2, 2, 1, 100.00, 330.00, 303.60, 280.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(213, NULL, 'PRD-000213', 'SKU-00213', '8900000000213', 'Sample Product 213', NULL, NULL, 3, 3, 1, 100.00, 345.00, 317.40, 293.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(214, NULL, 'PRD-000214', 'SKU-00214', '8900000000214', 'Sample Product 214', NULL, NULL, 4, 4, 1, 100.00, 360.00, 331.20, 306.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(215, NULL, 'PRD-000215', 'SKU-00215', '8900000000215', 'Sample Product 215', NULL, NULL, 5, 5, 1, 100.00, 375.00, 345.00, 318.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(216, NULL, 'PRD-000216', 'SKU-00216', '8900000000216', 'Sample Product 216', NULL, NULL, 6, 6, 1, 100.00, 390.00, 358.80, 331.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(217, NULL, 'PRD-000217', 'SKU-00217', '8900000000217', 'Sample Product 217', NULL, NULL, 1, 7, 1, 100.00, 405.00, 372.60, 344.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(218, NULL, 'PRD-000218', 'SKU-00218', '8900000000218', 'Sample Product 218', NULL, NULL, 2, 8, 1, 100.00, 420.00, 386.40, 357.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(219, NULL, 'PRD-000219', 'SKU-00219', '8900000000219', 'Sample Product 219', NULL, NULL, 3, 9, 1, 100.00, 435.00, 400.20, 369.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(220, NULL, 'PRD-000220', 'SKU-00220', '8900000000220', 'Sample Product 220', NULL, NULL, 4, 10, 1, 100.00, 150.00, 138.00, 127.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(221, NULL, 'PRD-000221', 'SKU-00221', '8900000000221', 'Sample Product 221', NULL, NULL, 5, 1, 1, 100.00, 165.00, 151.80, 140.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(222, NULL, 'PRD-000222', 'SKU-00222', '8900000000222', 'Sample Product 222', NULL, NULL, 6, 2, 1, 100.00, 180.00, 165.60, 153.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(223, NULL, 'PRD-000223', 'SKU-00223', '8900000000223', 'Sample Product 223', NULL, NULL, 1, 3, 1, 100.00, 195.00, 179.40, 165.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(224, NULL, 'PRD-000224', 'SKU-00224', '8900000000224', 'Sample Product 224', NULL, NULL, 2, 4, 1, 100.00, 210.00, 193.20, 178.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(225, NULL, 'PRD-000225', 'SKU-00225', '8900000000225', 'Sample Product 225', NULL, NULL, 3, 5, 1, 100.00, 225.00, 207.00, 191.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(226, NULL, 'PRD-000226', 'SKU-00226', '8900000000226', 'Sample Product 226', NULL, NULL, 4, 6, 1, 100.00, 240.00, 220.80, 204.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(227, NULL, 'PRD-000227', 'SKU-00227', '8900000000227', 'Sample Product 227', NULL, NULL, 5, 7, 1, 100.00, 255.00, 234.60, 216.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(228, NULL, 'PRD-000228', 'SKU-00228', '8900000000228', 'Sample Product 228', NULL, NULL, 6, 8, 1, 100.00, 270.00, 248.40, 229.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(229, NULL, 'PRD-000229', 'SKU-00229', '8900000000229', 'Sample Product 229', NULL, NULL, 1, 9, 1, 100.00, 285.00, 262.20, 242.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(230, NULL, 'PRD-000230', 'SKU-00230', '8900000000230', 'Sample Product 230', NULL, NULL, 2, 10, 1, 100.00, 300.00, 276.00, 255.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(231, NULL, 'PRD-000231', 'SKU-00231', '8900000000231', 'Sample Product 231', NULL, NULL, 3, 1, 1, 100.00, 315.00, 289.80, 267.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(232, NULL, 'PRD-000232', 'SKU-00232', '8900000000232', 'Sample Product 232', NULL, NULL, 4, 2, 1, 100.00, 330.00, 303.60, 280.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(233, NULL, 'PRD-000233', 'SKU-00233', '8900000000233', 'Sample Product 233', NULL, NULL, 5, 3, 1, 100.00, 345.00, 317.40, 293.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(234, NULL, 'PRD-000234', 'SKU-00234', '8900000000234', 'Sample Product 234', NULL, NULL, 6, 4, 1, 100.00, 360.00, 331.20, 306.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(235, NULL, 'PRD-000235', 'SKU-00235', '8900000000235', 'Sample Product 235', NULL, NULL, 1, 5, 1, 100.00, 375.00, 345.00, 318.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(236, NULL, 'PRD-000236', 'SKU-00236', '8900000000236', 'Sample Product 236', NULL, NULL, 2, 6, 1, 100.00, 390.00, 358.80, 331.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(237, NULL, 'PRD-000237', 'SKU-00237', '8900000000237', 'Sample Product 237', NULL, NULL, 3, 7, 1, 100.00, 405.00, 372.60, 344.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(238, NULL, 'PRD-000238', 'SKU-00238', '8900000000238', 'Sample Product 238', NULL, NULL, 4, 8, 1, 100.00, 420.00, 386.40, 357.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(239, NULL, 'PRD-000239', 'SKU-00239', '8900000000239', 'Sample Product 239', NULL, NULL, 5, 9, 1, 100.00, 435.00, 400.20, 369.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(240, NULL, 'PRD-000240', 'SKU-00240', '8900000000240', 'Sample Product 240', NULL, NULL, 6, 10, 1, 100.00, 150.00, 138.00, 127.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(241, NULL, 'PRD-000241', 'SKU-00241', '8900000000241', 'Sample Product 241', NULL, NULL, 1, 1, 1, 100.00, 165.00, 151.80, 140.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(242, NULL, 'PRD-000242', 'SKU-00242', '8900000000242', 'Sample Product 242', NULL, NULL, 2, 2, 1, 100.00, 180.00, 165.60, 153.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(243, NULL, 'PRD-000243', 'SKU-00243', '8900000000243', 'Sample Product 243', NULL, NULL, 3, 3, 1, 100.00, 195.00, 179.40, 165.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(244, NULL, 'PRD-000244', 'SKU-00244', '8900000000244', 'Sample Product 244', NULL, NULL, 4, 4, 1, 100.00, 210.00, 193.20, 178.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(245, NULL, 'PRD-000245', 'SKU-00245', '8900000000245', 'Sample Product 245', NULL, NULL, 5, 5, 1, 100.00, 225.00, 207.00, 191.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(246, NULL, 'PRD-000246', 'SKU-00246', '8900000000246', 'Sample Product 246', NULL, NULL, 6, 6, 1, 100.00, 240.00, 220.80, 204.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(247, NULL, 'PRD-000247', 'SKU-00247', '8900000000247', 'Sample Product 247', NULL, NULL, 1, 7, 1, 100.00, 255.00, 234.60, 216.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(248, NULL, 'PRD-000248', 'SKU-00248', '8900000000248', 'Sample Product 248', NULL, NULL, 2, 8, 1, 100.00, 270.00, 248.40, 229.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(249, NULL, 'PRD-000249', 'SKU-00249', '8900000000249', 'Sample Product 249', NULL, NULL, 3, 9, 1, 100.00, 285.00, 262.20, 242.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(250, NULL, 'PRD-000250', 'SKU-00250', '8900000000250', 'Sample Product 250', NULL, NULL, 4, 10, 1, 100.00, 300.00, 276.00, 255.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(251, NULL, 'PRD-000251', 'SKU-00251', '8900000000251', 'Sample Product 251', NULL, NULL, 5, 1, 1, 100.00, 315.00, 289.80, 267.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(252, NULL, 'PRD-000252', 'SKU-00252', '8900000000252', 'Sample Product 252', NULL, NULL, 6, 2, 1, 100.00, 330.00, 303.60, 280.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(253, NULL, 'PRD-000253', 'SKU-00253', '8900000000253', 'Sample Product 253', NULL, NULL, 1, 3, 1, 100.00, 345.00, 317.40, 293.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(254, NULL, 'PRD-000254', 'SKU-00254', '8900000000254', 'Sample Product 254', NULL, NULL, 2, 4, 1, 100.00, 360.00, 331.20, 306.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(255, NULL, 'PRD-000255', 'SKU-00255', '8900000000255', 'Sample Product 255', NULL, NULL, 3, 5, 1, 100.00, 375.00, 345.00, 318.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(256, NULL, 'PRD-000256', 'SKU-00256', '8900000000256', 'Sample Product 256', NULL, NULL, 4, 6, 1, 100.00, 390.00, 358.80, 331.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(257, NULL, 'PRD-000257', 'SKU-00257', '8900000000257', 'Sample Product 257', NULL, NULL, 5, 7, 1, 100.00, 405.00, 372.60, 344.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(258, NULL, 'PRD-000258', 'SKU-00258', '8900000000258', 'Sample Product 258', NULL, NULL, 6, 8, 1, 100.00, 420.00, 386.40, 357.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(259, NULL, 'PRD-000259', 'SKU-00259', '8900000000259', 'Sample Product 259', NULL, NULL, 1, 9, 1, 100.00, 435.00, 400.20, 369.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(260, NULL, 'PRD-000260', 'SKU-00260', '8900000000260', 'Sample Product 260', NULL, NULL, 2, 10, 1, 100.00, 150.00, 138.00, 127.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(261, NULL, 'PRD-000261', 'SKU-00261', '8900000000261', 'Sample Product 261', NULL, NULL, 3, 1, 1, 100.00, 165.00, 151.80, 140.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(262, NULL, 'PRD-000262', 'SKU-00262', '8900000000262', 'Sample Product 262', NULL, NULL, 4, 2, 1, 100.00, 180.00, 165.60, 153.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(263, NULL, 'PRD-000263', 'SKU-00263', '8900000000263', 'Sample Product 263', NULL, NULL, 5, 3, 1, 100.00, 195.00, 179.40, 165.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(264, NULL, 'PRD-000264', 'SKU-00264', '8900000000264', 'Sample Product 264', NULL, NULL, 6, 4, 1, 100.00, 210.00, 193.20, 178.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(265, NULL, 'PRD-000265', 'SKU-00265', '8900000000265', 'Sample Product 265', NULL, NULL, 1, 5, 1, 100.00, 225.00, 207.00, 191.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(266, NULL, 'PRD-000266', 'SKU-00266', '8900000000266', 'Sample Product 266', NULL, NULL, 2, 6, 1, 100.00, 240.00, 220.80, 204.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(267, NULL, 'PRD-000267', 'SKU-00267', '8900000000267', 'Sample Product 267', NULL, NULL, 3, 7, 1, 100.00, 255.00, 234.60, 216.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(268, NULL, 'PRD-000268', 'SKU-00268', '8900000000268', 'Sample Product 268', NULL, NULL, 4, 8, 1, 100.00, 270.00, 248.40, 229.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(269, NULL, 'PRD-000269', 'SKU-00269', '8900000000269', 'Sample Product 269', NULL, NULL, 5, 9, 1, 100.00, 285.00, 262.20, 242.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(270, NULL, 'PRD-000270', 'SKU-00270', '8900000000270', 'Sample Product 270', NULL, NULL, 6, 10, 1, 100.00, 300.00, 276.00, 255.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(271, NULL, 'PRD-000271', 'SKU-00271', '8900000000271', 'Sample Product 271', NULL, NULL, 1, 1, 1, 100.00, 315.00, 289.80, 267.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(272, NULL, 'PRD-000272', 'SKU-00272', '8900000000272', 'Sample Product 272', NULL, NULL, 2, 2, 1, 100.00, 330.00, 303.60, 280.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(273, NULL, 'PRD-000273', 'SKU-00273', '8900000000273', 'Sample Product 273', NULL, NULL, 3, 3, 1, 100.00, 345.00, 317.40, 293.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(274, NULL, 'PRD-000274', 'SKU-00274', '8900000000274', 'Sample Product 274', NULL, NULL, 4, 4, 1, 100.00, 360.00, 331.20, 306.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(275, NULL, 'PRD-000275', 'SKU-00275', '8900000000275', 'Sample Product 275', NULL, NULL, 5, 5, 1, 100.00, 375.00, 345.00, 318.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(276, NULL, 'PRD-000276', 'SKU-00276', '8900000000276', 'Sample Product 276', NULL, NULL, 6, 6, 1, 100.00, 390.00, 358.80, 331.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(277, NULL, 'PRD-000277', 'SKU-00277', '8900000000277', 'Sample Product 277', NULL, NULL, 1, 7, 1, 100.00, 405.00, 372.60, 344.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(278, NULL, 'PRD-000278', 'SKU-00278', '8900000000278', 'Sample Product 278', NULL, NULL, 2, 8, 1, 100.00, 420.00, 386.40, 357.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(279, NULL, 'PRD-000279', 'SKU-00279', '8900000000279', 'Sample Product 279', NULL, NULL, 3, 9, 1, 100.00, 435.00, 400.20, 369.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(280, NULL, 'PRD-000280', 'SKU-00280', '8900000000280', 'Sample Product 280', NULL, NULL, 4, 10, 1, 100.00, 150.00, 138.00, 127.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(281, NULL, 'PRD-000281', 'SKU-00281', '8900000000281', 'Sample Product 281', NULL, NULL, 5, 1, 1, 100.00, 165.00, 151.80, 140.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(282, NULL, 'PRD-000282', 'SKU-00282', '8900000000282', 'Sample Product 282', NULL, NULL, 6, 2, 1, 100.00, 180.00, 165.60, 153.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(283, NULL, 'PRD-000283', 'SKU-00283', '8900000000283', 'Sample Product 283', NULL, NULL, 1, 3, 1, 100.00, 195.00, 179.40, 165.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(284, NULL, 'PRD-000284', 'SKU-00284', '8900000000284', 'Sample Product 284', NULL, NULL, 2, 4, 1, 100.00, 210.00, 193.20, 178.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(285, NULL, 'PRD-000285', 'SKU-00285', '8900000000285', 'Sample Product 285', NULL, NULL, 3, 5, 1, 100.00, 225.00, 207.00, 191.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(286, NULL, 'PRD-000286', 'SKU-00286', '8900000000286', 'Sample Product 286', NULL, NULL, 4, 6, 1, 100.00, 240.00, 220.80, 204.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(287, NULL, 'PRD-000287', 'SKU-00287', '8900000000287', 'Sample Product 287', NULL, NULL, 5, 7, 1, 100.00, 255.00, 234.60, 216.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(288, NULL, 'PRD-000288', 'SKU-00288', '8900000000288', 'Sample Product 288', NULL, NULL, 6, 8, 1, 100.00, 270.00, 248.40, 229.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(289, NULL, 'PRD-000289', 'SKU-00289', '8900000000289', 'Sample Product 289', NULL, NULL, 1, 9, 1, 100.00, 285.00, 262.20, 242.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(290, NULL, 'PRD-000290', 'SKU-00290', '8900000000290', 'Sample Product 290', NULL, NULL, 2, 10, 1, 100.00, 300.00, 276.00, 255.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(291, NULL, 'PRD-000291', 'SKU-00291', '8900000000291', 'Sample Product 291', NULL, NULL, 3, 1, 1, 100.00, 315.00, 289.80, 267.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(292, NULL, 'PRD-000292', 'SKU-00292', '8900000000292', 'Sample Product 292', NULL, NULL, 4, 2, 1, 100.00, 330.00, 303.60, 280.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(293, NULL, 'PRD-000293', 'SKU-00293', '8900000000293', 'Sample Product 293', NULL, NULL, 5, 3, 1, 100.00, 345.00, 317.40, 293.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(294, NULL, 'PRD-000294', 'SKU-00294', '8900000000294', 'Sample Product 294', NULL, NULL, 6, 4, 1, 100.00, 360.00, 331.20, 306.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(295, NULL, 'PRD-000295', 'SKU-00295', '8900000000295', 'Sample Product 295', NULL, NULL, 1, 5, 1, 100.00, 375.00, 345.00, 318.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(296, NULL, 'PRD-000296', 'SKU-00296', '8900000000296', 'Sample Product 296', NULL, NULL, 2, 6, 1, 100.00, 390.00, 358.80, 331.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(297, NULL, 'PRD-000297', 'SKU-00297', '8900000000297', 'Sample Product 297', NULL, NULL, 3, 7, 1, 100.00, 405.00, 372.60, 344.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(298, NULL, 'PRD-000298', 'SKU-00298', '8900000000298', 'Sample Product 298', NULL, NULL, 4, 8, 1, 100.00, 420.00, 386.40, 357.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(299, NULL, 'PRD-000299', 'SKU-00299', '8900000000299', 'Sample Product 299', NULL, NULL, 5, 9, 1, 100.00, 435.00, 400.20, 369.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(300, NULL, 'PRD-000300', 'SKU-00300', '8900000000300', 'Sample Product 300', NULL, NULL, 6, 10, 1, 100.00, 150.00, 138.00, 127.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(301, NULL, 'PRD-000301', 'SKU-00301', '8900000000301', 'Sample Product 301', NULL, NULL, 1, 1, 1, 100.00, 165.00, 151.80, 140.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(302, NULL, 'PRD-000302', 'SKU-00302', '8900000000302', 'Sample Product 302', NULL, NULL, 2, 2, 1, 100.00, 180.00, 165.60, 153.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(303, NULL, 'PRD-000303', 'SKU-00303', '8900000000303', 'Sample Product 303', NULL, NULL, 3, 3, 1, 100.00, 195.00, 179.40, 165.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(304, NULL, 'PRD-000304', 'SKU-00304', '8900000000304', 'Sample Product 304', NULL, NULL, 4, 4, 1, 100.00, 210.00, 193.20, 178.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(305, NULL, 'PRD-000305', 'SKU-00305', '8900000000305', 'Sample Product 305', NULL, NULL, 5, 5, 1, 100.00, 225.00, 207.00, 191.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(306, NULL, 'PRD-000306', 'SKU-00306', '8900000000306', 'Sample Product 306', NULL, NULL, 6, 6, 1, 100.00, 240.00, 220.80, 204.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(307, NULL, 'PRD-000307', 'SKU-00307', '8900000000307', 'Sample Product 307', NULL, NULL, 1, 7, 1, 100.00, 255.00, 234.60, 216.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(308, NULL, 'PRD-000308', 'SKU-00308', '8900000000308', 'Sample Product 308', NULL, NULL, 2, 8, 1, 100.00, 270.00, 248.40, 229.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(309, NULL, 'PRD-000309', 'SKU-00309', '8900000000309', 'Sample Product 309', NULL, NULL, 3, 9, 1, 100.00, 285.00, 262.20, 242.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(310, NULL, 'PRD-000310', 'SKU-00310', '8900000000310', 'Sample Product 310', NULL, NULL, 4, 10, 1, 100.00, 300.00, 276.00, 255.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(311, NULL, 'PRD-000311', 'SKU-00311', '8900000000311', 'Sample Product 311', NULL, NULL, 5, 1, 1, 100.00, 315.00, 289.80, 267.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(312, NULL, 'PRD-000312', 'SKU-00312', '8900000000312', 'Sample Product 312', NULL, NULL, 6, 2, 1, 100.00, 330.00, 303.60, 280.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(313, NULL, 'PRD-000313', 'SKU-00313', '8900000000313', 'Sample Product 313', NULL, NULL, 1, 3, 1, 100.00, 345.00, 317.40, 293.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(314, NULL, 'PRD-000314', 'SKU-00314', '8900000000314', 'Sample Product 314', NULL, NULL, 2, 4, 1, 100.00, 360.00, 331.20, 306.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(315, NULL, 'PRD-000315', 'SKU-00315', '8900000000315', 'Sample Product 315', NULL, NULL, 3, 5, 1, 100.00, 375.00, 345.00, 318.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(316, NULL, 'PRD-000316', 'SKU-00316', '8900000000316', 'Sample Product 316', NULL, NULL, 4, 6, 1, 100.00, 390.00, 358.80, 331.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(317, NULL, 'PRD-000317', 'SKU-00317', '8900000000317', 'Sample Product 317', NULL, NULL, 5, 7, 1, 100.00, 405.00, 372.60, 344.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(318, NULL, 'PRD-000318', 'SKU-00318', '8900000000318', 'Sample Product 318', NULL, NULL, 6, 8, 1, 100.00, 420.00, 386.40, 357.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(319, NULL, 'PRD-000319', 'SKU-00319', '8900000000319', 'Sample Product 319', NULL, NULL, 1, 9, 1, 100.00, 435.00, 400.20, 369.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(320, NULL, 'PRD-000320', 'SKU-00320', '8900000000320', 'Sample Product 320', NULL, NULL, 2, 10, 1, 100.00, 150.00, 138.00, 127.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(321, NULL, 'PRD-000321', 'SKU-00321', '8900000000321', 'Sample Product 321', NULL, NULL, 3, 1, 1, 100.00, 165.00, 151.80, 140.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(322, NULL, 'PRD-000322', 'SKU-00322', '8900000000322', 'Sample Product 322', NULL, NULL, 4, 2, 1, 100.00, 180.00, 165.60, 153.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(323, NULL, 'PRD-000323', 'SKU-00323', '8900000000323', 'Sample Product 323', NULL, NULL, 5, 3, 1, 100.00, 195.00, 179.40, 165.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(324, NULL, 'PRD-000324', 'SKU-00324', '8900000000324', 'Sample Product 324', NULL, NULL, 6, 4, 1, 100.00, 210.00, 193.20, 178.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(325, NULL, 'PRD-000325', 'SKU-00325', '8900000000325', 'Sample Product 325', NULL, NULL, 1, 5, 1, 100.00, 225.00, 207.00, 191.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(326, NULL, 'PRD-000326', 'SKU-00326', '8900000000326', 'Sample Product 326', NULL, NULL, 2, 6, 1, 100.00, 240.00, 220.80, 204.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(327, NULL, 'PRD-000327', 'SKU-00327', '8900000000327', 'Sample Product 327', NULL, NULL, 3, 7, 1, 100.00, 255.00, 234.60, 216.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(328, NULL, 'PRD-000328', 'SKU-00328', '8900000000328', 'Sample Product 328', NULL, NULL, 4, 8, 1, 100.00, 270.00, 248.40, 229.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(329, NULL, 'PRD-000329', 'SKU-00329', '8900000000329', 'Sample Product 329', NULL, NULL, 5, 9, 1, 100.00, 285.00, 262.20, 242.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(330, NULL, 'PRD-000330', 'SKU-00330', '8900000000330', 'Sample Product 330', NULL, NULL, 6, 10, 1, 100.00, 300.00, 276.00, 255.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(331, NULL, 'PRD-000331', 'SKU-00331', '8900000000331', 'Sample Product 331', NULL, NULL, 1, 1, 1, 100.00, 315.00, 289.80, 267.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(332, NULL, 'PRD-000332', 'SKU-00332', '8900000000332', 'Sample Product 332', NULL, NULL, 2, 2, 1, 100.00, 330.00, 303.60, 280.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(333, NULL, 'PRD-000333', 'SKU-00333', '8900000000333', 'Sample Product 333', NULL, NULL, 3, 3, 1, 100.00, 345.00, 317.40, 293.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(334, NULL, 'PRD-000334', 'SKU-00334', '8900000000334', 'Sample Product 334', NULL, NULL, 4, 4, 1, 100.00, 360.00, 331.20, 306.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(335, NULL, 'PRD-000335', 'SKU-00335', '8900000000335', 'Sample Product 335', NULL, NULL, 5, 5, 1, 100.00, 375.00, 345.00, 318.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(336, NULL, 'PRD-000336', 'SKU-00336', '8900000000336', 'Sample Product 336', NULL, NULL, 6, 6, 1, 100.00, 390.00, 358.80, 331.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(337, NULL, 'PRD-000337', 'SKU-00337', '8900000000337', 'Sample Product 337', NULL, NULL, 1, 7, 1, 100.00, 405.00, 372.60, 344.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(338, NULL, 'PRD-000338', 'SKU-00338', '8900000000338', 'Sample Product 338', NULL, NULL, 2, 8, 1, 100.00, 420.00, 386.40, 357.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(339, NULL, 'PRD-000339', 'SKU-00339', '8900000000339', 'Sample Product 339', NULL, NULL, 3, 9, 1, 100.00, 435.00, 400.20, 369.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(340, NULL, 'PRD-000340', 'SKU-00340', '8900000000340', 'Sample Product 340', NULL, NULL, 4, 10, 1, 100.00, 150.00, 138.00, 127.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(341, NULL, 'PRD-000341', 'SKU-00341', '8900000000341', 'Sample Product 341', NULL, NULL, 5, 1, 1, 100.00, 165.00, 151.80, 140.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(342, NULL, 'PRD-000342', 'SKU-00342', '8900000000342', 'Sample Product 342', NULL, NULL, 6, 2, 1, 100.00, 180.00, 165.60, 153.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(343, NULL, 'PRD-000343', 'SKU-00343', '8900000000343', 'Sample Product 343', NULL, NULL, 1, 3, 1, 100.00, 195.00, 179.40, 165.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(344, NULL, 'PRD-000344', 'SKU-00344', '8900000000344', 'Sample Product 344', NULL, NULL, 2, 4, 1, 100.00, 210.00, 193.20, 178.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(345, NULL, 'PRD-000345', 'SKU-00345', '8900000000345', 'Sample Product 345', NULL, NULL, 3, 5, 1, 100.00, 225.00, 207.00, 191.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(346, NULL, 'PRD-000346', 'SKU-00346', '8900000000346', 'Sample Product 346', NULL, NULL, 4, 6, 1, 100.00, 240.00, 220.80, 204.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(347, NULL, 'PRD-000347', 'SKU-00347', '8900000000347', 'Sample Product 347', NULL, NULL, 5, 7, 1, 100.00, 255.00, 234.60, 216.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(348, NULL, 'PRD-000348', 'SKU-00348', '8900000000348', 'Sample Product 348', NULL, NULL, 6, 8, 1, 100.00, 270.00, 248.40, 229.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(349, NULL, 'PRD-000349', 'SKU-00349', '8900000000349', 'Sample Product 349', NULL, NULL, 1, 9, 1, 100.00, 285.00, 262.20, 242.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(350, NULL, 'PRD-000350', 'SKU-00350', '8900000000350', 'Sample Product 350', NULL, NULL, 2, 10, 1, 100.00, 300.00, 276.00, 255.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(351, NULL, 'PRD-000351', 'SKU-00351', '8900000000351', 'Sample Product 351', NULL, NULL, 3, 1, 1, 100.00, 315.00, 289.80, 267.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(352, NULL, 'PRD-000352', 'SKU-00352', '8900000000352', 'Sample Product 352', NULL, NULL, 4, 2, 1, 100.00, 330.00, 303.60, 280.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(353, NULL, 'PRD-000353', 'SKU-00353', '8900000000353', 'Sample Product 353', NULL, NULL, 5, 3, 1, 100.00, 345.00, 317.40, 293.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(354, NULL, 'PRD-000354', 'SKU-00354', '8900000000354', 'Sample Product 354', NULL, NULL, 6, 4, 1, 100.00, 360.00, 331.20, 306.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(355, NULL, 'PRD-000355', 'SKU-00355', '8900000000355', 'Sample Product 355', NULL, NULL, 1, 5, 1, 100.00, 375.00, 345.00, 318.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(356, NULL, 'PRD-000356', 'SKU-00356', '8900000000356', 'Sample Product 356', NULL, NULL, 2, 6, 1, 100.00, 390.00, 358.80, 331.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(357, NULL, 'PRD-000357', 'SKU-00357', '8900000000357', 'Sample Product 357', NULL, NULL, 3, 7, 1, 100.00, 405.00, 372.60, 344.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(358, NULL, 'PRD-000358', 'SKU-00358', '8900000000358', 'Sample Product 358', NULL, NULL, 4, 8, 1, 100.00, 420.00, 386.40, 357.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(359, NULL, 'PRD-000359', 'SKU-00359', '8900000000359', 'Sample Product 359', NULL, NULL, 5, 9, 1, 100.00, 435.00, 400.20, 369.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(360, NULL, 'PRD-000360', 'SKU-00360', '8900000000360', 'Sample Product 360', NULL, NULL, 6, 10, 1, 100.00, 150.00, 138.00, 127.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL);
INSERT INTO `products` (`id`, `parent_product_id`, `code`, `sku`, `barcode`, `name`, `variant_name`, `variant_attributes`, `category_id`, `brand_id`, `unit_id`, `purchase_price`, `selling_price`, `wholesale_price`, `minimum_selling_price`, `tax_rate`, `reorder_level`, `minimum_stock_level`, `maximum_stock_level`, `track_stock`, `track_serial`, `track_batch`, `track_expiry`, `costing_method`, `allow_negative_stock`, `image`, `description`, `is_active`, `created_at`, `updated_at`, `deleted_at`) VALUES
(361, NULL, 'PRD-000361', 'SKU-00361', '8900000000361', 'Sample Product 361', NULL, NULL, 1, 1, 1, 100.00, 165.00, 151.80, 140.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(362, NULL, 'PRD-000362', 'SKU-00362', '8900000000362', 'Sample Product 362', NULL, NULL, 2, 2, 1, 100.00, 180.00, 165.60, 153.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(363, NULL, 'PRD-000363', 'SKU-00363', '8900000000363', 'Sample Product 363', NULL, NULL, 3, 3, 1, 100.00, 195.00, 179.40, 165.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(364, NULL, 'PRD-000364', 'SKU-00364', '8900000000364', 'Sample Product 364', NULL, NULL, 4, 4, 1, 100.00, 210.00, 193.20, 178.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(365, NULL, 'PRD-000365', 'SKU-00365', '8900000000365', 'Sample Product 365', NULL, NULL, 5, 5, 1, 100.00, 225.00, 207.00, 191.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(366, NULL, 'PRD-000366', 'SKU-00366', '8900000000366', 'Sample Product 366', NULL, NULL, 6, 6, 1, 100.00, 240.00, 220.80, 204.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(367, NULL, 'PRD-000367', 'SKU-00367', '8900000000367', 'Sample Product 367', NULL, NULL, 1, 7, 1, 100.00, 255.00, 234.60, 216.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(368, NULL, 'PRD-000368', 'SKU-00368', '8900000000368', 'Sample Product 368', NULL, NULL, 2, 8, 1, 100.00, 270.00, 248.40, 229.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(369, NULL, 'PRD-000369', 'SKU-00369', '8900000000369', 'Sample Product 369', NULL, NULL, 3, 9, 1, 100.00, 285.00, 262.20, 242.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(370, NULL, 'PRD-000370', 'SKU-00370', '8900000000370', 'Sample Product 370', NULL, NULL, 4, 10, 1, 100.00, 300.00, 276.00, 255.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(371, NULL, 'PRD-000371', 'SKU-00371', '8900000000371', 'Sample Product 371', NULL, NULL, 5, 1, 1, 100.00, 315.00, 289.80, 267.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(372, NULL, 'PRD-000372', 'SKU-00372', '8900000000372', 'Sample Product 372', NULL, NULL, 6, 2, 1, 100.00, 330.00, 303.60, 280.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(373, NULL, 'PRD-000373', 'SKU-00373', '8900000000373', 'Sample Product 373', NULL, NULL, 1, 3, 1, 100.00, 345.00, 317.40, 293.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(374, NULL, 'PRD-000374', 'SKU-00374', '8900000000374', 'Sample Product 374', NULL, NULL, 2, 4, 1, 100.00, 360.00, 331.20, 306.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(375, NULL, 'PRD-000375', 'SKU-00375', '8900000000375', 'Sample Product 375', NULL, NULL, 3, 5, 1, 100.00, 375.00, 345.00, 318.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(376, NULL, 'PRD-000376', 'SKU-00376', '8900000000376', 'Sample Product 376', NULL, NULL, 4, 6, 1, 100.00, 390.00, 358.80, 331.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(377, NULL, 'PRD-000377', 'SKU-00377', '8900000000377', 'Sample Product 377', NULL, NULL, 5, 7, 1, 100.00, 405.00, 372.60, 344.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(378, NULL, 'PRD-000378', 'SKU-00378', '8900000000378', 'Sample Product 378', NULL, NULL, 6, 8, 1, 100.00, 420.00, 386.40, 357.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(379, NULL, 'PRD-000379', 'SKU-00379', '8900000000379', 'Sample Product 379', NULL, NULL, 1, 9, 1, 100.00, 435.00, 400.20, 369.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(380, NULL, 'PRD-000380', 'SKU-00380', '8900000000380', 'Sample Product 380', NULL, NULL, 2, 10, 1, 100.00, 150.00, 138.00, 127.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(381, NULL, 'PRD-000381', 'SKU-00381', '8900000000381', 'Sample Product 381', NULL, NULL, 3, 1, 1, 100.00, 165.00, 151.80, 140.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(382, NULL, 'PRD-000382', 'SKU-00382', '8900000000382', 'Sample Product 382', NULL, NULL, 4, 2, 1, 100.00, 180.00, 165.60, 153.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(383, NULL, 'PRD-000383', 'SKU-00383', '8900000000383', 'Sample Product 383', NULL, NULL, 5, 3, 1, 100.00, 195.00, 179.40, 165.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(384, NULL, 'PRD-000384', 'SKU-00384', '8900000000384', 'Sample Product 384', NULL, NULL, 6, 4, 1, 100.00, 210.00, 193.20, 178.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(385, NULL, 'PRD-000385', 'SKU-00385', '8900000000385', 'Sample Product 385', NULL, NULL, 1, 5, 1, 100.00, 225.00, 207.00, 191.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(386, NULL, 'PRD-000386', 'SKU-00386', '8900000000386', 'Sample Product 386', NULL, NULL, 2, 6, 1, 100.00, 240.00, 220.80, 204.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(387, NULL, 'PRD-000387', 'SKU-00387', '8900000000387', 'Sample Product 387', NULL, NULL, 3, 7, 1, 100.00, 255.00, 234.60, 216.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(388, NULL, 'PRD-000388', 'SKU-00388', '8900000000388', 'Sample Product 388', NULL, NULL, 4, 8, 1, 100.00, 270.00, 248.40, 229.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(389, NULL, 'PRD-000389', 'SKU-00389', '8900000000389', 'Sample Product 389', NULL, NULL, 5, 9, 1, 100.00, 285.00, 262.20, 242.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(390, NULL, 'PRD-000390', 'SKU-00390', '8900000000390', 'Sample Product 390', NULL, NULL, 6, 10, 1, 100.00, 300.00, 276.00, 255.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(391, NULL, 'PRD-000391', 'SKU-00391', '8900000000391', 'Sample Product 391', NULL, NULL, 1, 1, 1, 100.00, 315.00, 289.80, 267.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(392, NULL, 'PRD-000392', 'SKU-00392', '8900000000392', 'Sample Product 392', NULL, NULL, 2, 2, 1, 100.00, 330.00, 303.60, 280.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(393, NULL, 'PRD-000393', 'SKU-00393', '8900000000393', 'Sample Product 393', NULL, NULL, 3, 3, 1, 100.00, 345.00, 317.40, 293.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(394, NULL, 'PRD-000394', 'SKU-00394', '8900000000394', 'Sample Product 394', NULL, NULL, 4, 4, 1, 100.00, 360.00, 331.20, 306.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(395, NULL, 'PRD-000395', 'SKU-00395', '8900000000395', 'Sample Product 395', NULL, NULL, 5, 5, 1, 100.00, 375.00, 345.00, 318.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(396, NULL, 'PRD-000396', 'SKU-00396', '8900000000396', 'Sample Product 396', NULL, NULL, 6, 6, 1, 100.00, 390.00, 358.80, 331.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(397, NULL, 'PRD-000397', 'SKU-00397', '8900000000397', 'Sample Product 397', NULL, NULL, 1, 7, 1, 100.00, 405.00, 372.60, 344.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(398, NULL, 'PRD-000398', 'SKU-00398', '8900000000398', 'Sample Product 398', NULL, NULL, 2, 8, 1, 100.00, 420.00, 386.40, 357.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(399, NULL, 'PRD-000399', 'SKU-00399', '8900000000399', 'Sample Product 399', NULL, NULL, 3, 9, 1, 100.00, 435.00, 400.20, 369.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(400, NULL, 'PRD-000400', 'SKU-00400', '8900000000400', 'Sample Product 400', NULL, NULL, 4, 10, 1, 100.00, 150.00, 138.00, 127.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(401, NULL, 'PRD-000401', 'SKU-00401', '8900000000401', 'Sample Product 401', NULL, NULL, 5, 1, 1, 100.00, 165.00, 151.80, 140.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(402, NULL, 'PRD-000402', 'SKU-00402', '8900000000402', 'Sample Product 402', NULL, NULL, 6, 2, 1, 100.00, 180.00, 165.60, 153.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(403, NULL, 'PRD-000403', 'SKU-00403', '8900000000403', 'Sample Product 403', NULL, NULL, 1, 3, 1, 100.00, 195.00, 179.40, 165.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(404, NULL, 'PRD-000404', 'SKU-00404', '8900000000404', 'Sample Product 404', NULL, NULL, 2, 4, 1, 100.00, 210.00, 193.20, 178.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(405, NULL, 'PRD-000405', 'SKU-00405', '8900000000405', 'Sample Product 405', NULL, NULL, 3, 5, 1, 100.00, 225.00, 207.00, 191.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(406, NULL, 'PRD-000406', 'SKU-00406', '8900000000406', 'Sample Product 406', NULL, NULL, 4, 6, 1, 100.00, 240.00, 220.80, 204.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(407, NULL, 'PRD-000407', 'SKU-00407', '8900000000407', 'Sample Product 407', NULL, NULL, 5, 7, 1, 100.00, 255.00, 234.60, 216.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(408, NULL, 'PRD-000408', 'SKU-00408', '8900000000408', 'Sample Product 408', NULL, NULL, 6, 8, 1, 100.00, 270.00, 248.40, 229.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(409, NULL, 'PRD-000409', 'SKU-00409', '8900000000409', 'Sample Product 409', NULL, NULL, 1, 9, 1, 100.00, 285.00, 262.20, 242.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(410, NULL, 'PRD-000410', 'SKU-00410', '8900000000410', 'Sample Product 410', NULL, NULL, 2, 10, 1, 100.00, 300.00, 276.00, 255.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(411, NULL, 'PRD-000411', 'SKU-00411', '8900000000411', 'Sample Product 411', NULL, NULL, 3, 1, 1, 100.00, 315.00, 289.80, 267.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(412, NULL, 'PRD-000412', 'SKU-00412', '8900000000412', 'Sample Product 412', NULL, NULL, 4, 2, 1, 100.00, 330.00, 303.60, 280.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(413, NULL, 'PRD-000413', 'SKU-00413', '8900000000413', 'Sample Product 413', NULL, NULL, 5, 3, 1, 100.00, 345.00, 317.40, 293.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(414, NULL, 'PRD-000414', 'SKU-00414', '8900000000414', 'Sample Product 414', NULL, NULL, 6, 4, 1, 100.00, 360.00, 331.20, 306.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(415, NULL, 'PRD-000415', 'SKU-00415', '8900000000415', 'Sample Product 415', NULL, NULL, 1, 5, 1, 100.00, 375.00, 345.00, 318.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(416, NULL, 'PRD-000416', 'SKU-00416', '8900000000416', 'Sample Product 416', NULL, NULL, 2, 6, 1, 100.00, 390.00, 358.80, 331.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(417, NULL, 'PRD-000417', 'SKU-00417', '8900000000417', 'Sample Product 417', NULL, NULL, 3, 7, 1, 100.00, 405.00, 372.60, 344.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(418, NULL, 'PRD-000418', 'SKU-00418', '8900000000418', 'Sample Product 418', NULL, NULL, 4, 8, 1, 100.00, 420.00, 386.40, 357.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(419, NULL, 'PRD-000419', 'SKU-00419', '8900000000419', 'Sample Product 419', NULL, NULL, 5, 9, 1, 100.00, 435.00, 400.20, 369.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(420, NULL, 'PRD-000420', 'SKU-00420', '8900000000420', 'Sample Product 420', NULL, NULL, 6, 10, 1, 100.00, 150.00, 138.00, 127.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(421, NULL, 'PRD-000421', 'SKU-00421', '8900000000421', 'Sample Product 421', NULL, NULL, 1, 1, 1, 100.00, 165.00, 151.80, 140.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(422, NULL, 'PRD-000422', 'SKU-00422', '8900000000422', 'Sample Product 422', NULL, NULL, 2, 2, 1, 100.00, 180.00, 165.60, 153.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(423, NULL, 'PRD-000423', 'SKU-00423', '8900000000423', 'Sample Product 423', NULL, NULL, 3, 3, 1, 100.00, 195.00, 179.40, 165.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(424, NULL, 'PRD-000424', 'SKU-00424', '8900000000424', 'Sample Product 424', NULL, NULL, 4, 4, 1, 100.00, 210.00, 193.20, 178.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(425, NULL, 'PRD-000425', 'SKU-00425', '8900000000425', 'Sample Product 425', NULL, NULL, 5, 5, 1, 100.00, 225.00, 207.00, 191.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(426, NULL, 'PRD-000426', 'SKU-00426', '8900000000426', 'Sample Product 426', NULL, NULL, 6, 6, 1, 100.00, 240.00, 220.80, 204.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(427, NULL, 'PRD-000427', 'SKU-00427', '8900000000427', 'Sample Product 427', NULL, NULL, 1, 7, 1, 100.00, 255.00, 234.60, 216.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(428, NULL, 'PRD-000428', 'SKU-00428', '8900000000428', 'Sample Product 428', NULL, NULL, 2, 8, 1, 100.00, 270.00, 248.40, 229.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(429, NULL, 'PRD-000429', 'SKU-00429', '8900000000429', 'Sample Product 429', NULL, NULL, 3, 9, 1, 100.00, 285.00, 262.20, 242.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(430, NULL, 'PRD-000430', 'SKU-00430', '8900000000430', 'Sample Product 430', NULL, NULL, 4, 10, 1, 100.00, 300.00, 276.00, 255.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(431, NULL, 'PRD-000431', 'SKU-00431', '8900000000431', 'Sample Product 431', NULL, NULL, 5, 1, 1, 100.00, 315.00, 289.80, 267.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(432, NULL, 'PRD-000432', 'SKU-00432', '8900000000432', 'Sample Product 432', NULL, NULL, 6, 2, 1, 100.00, 330.00, 303.60, 280.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(433, NULL, 'PRD-000433', 'SKU-00433', '8900000000433', 'Sample Product 433', NULL, NULL, 1, 3, 1, 100.00, 345.00, 317.40, 293.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(434, NULL, 'PRD-000434', 'SKU-00434', '8900000000434', 'Sample Product 434', NULL, NULL, 2, 4, 1, 100.00, 360.00, 331.20, 306.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(435, NULL, 'PRD-000435', 'SKU-00435', '8900000000435', 'Sample Product 435', NULL, NULL, 3, 5, 1, 100.00, 375.00, 345.00, 318.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(436, NULL, 'PRD-000436', 'SKU-00436', '8900000000436', 'Sample Product 436', NULL, NULL, 4, 6, 1, 100.00, 390.00, 358.80, 331.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(437, NULL, 'PRD-000437', 'SKU-00437', '8900000000437', 'Sample Product 437', NULL, NULL, 5, 7, 1, 100.00, 405.00, 372.60, 344.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(438, NULL, 'PRD-000438', 'SKU-00438', '8900000000438', 'Sample Product 438', NULL, NULL, 6, 8, 1, 100.00, 420.00, 386.40, 357.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(439, NULL, 'PRD-000439', 'SKU-00439', '8900000000439', 'Sample Product 439', NULL, NULL, 1, 9, 1, 100.00, 435.00, 400.20, 369.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(440, NULL, 'PRD-000440', 'SKU-00440', '8900000000440', 'Sample Product 440', NULL, NULL, 2, 10, 1, 100.00, 150.00, 138.00, 127.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(441, NULL, 'PRD-000441', 'SKU-00441', '8900000000441', 'Sample Product 441', NULL, NULL, 3, 1, 1, 100.00, 165.00, 151.80, 140.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(442, NULL, 'PRD-000442', 'SKU-00442', '8900000000442', 'Sample Product 442', NULL, NULL, 4, 2, 1, 100.00, 180.00, 165.60, 153.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(443, NULL, 'PRD-000443', 'SKU-00443', '8900000000443', 'Sample Product 443', NULL, NULL, 5, 3, 1, 100.00, 195.00, 179.40, 165.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(444, NULL, 'PRD-000444', 'SKU-00444', '8900000000444', 'Sample Product 444', NULL, NULL, 6, 4, 1, 100.00, 210.00, 193.20, 178.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(445, NULL, 'PRD-000445', 'SKU-00445', '8900000000445', 'Sample Product 445', NULL, NULL, 1, 5, 1, 100.00, 225.00, 207.00, 191.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(446, NULL, 'PRD-000446', 'SKU-00446', '8900000000446', 'Sample Product 446', NULL, NULL, 2, 6, 1, 100.00, 240.00, 220.80, 204.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(447, NULL, 'PRD-000447', 'SKU-00447', '8900000000447', 'Sample Product 447', NULL, NULL, 3, 7, 1, 100.00, 255.00, 234.60, 216.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(448, NULL, 'PRD-000448', 'SKU-00448', '8900000000448', 'Sample Product 448', NULL, NULL, 4, 8, 1, 100.00, 270.00, 248.40, 229.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(449, NULL, 'PRD-000449', 'SKU-00449', '8900000000449', 'Sample Product 449', NULL, NULL, 5, 9, 1, 100.00, 285.00, 262.20, 242.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(450, NULL, 'PRD-000450', 'SKU-00450', '8900000000450', 'Sample Product 450', NULL, NULL, 6, 10, 1, 100.00, 300.00, 276.00, 255.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(451, NULL, 'PRD-000451', 'SKU-00451', '8900000000451', 'Sample Product 451', NULL, NULL, 1, 1, 1, 100.00, 315.00, 289.80, 267.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(452, NULL, 'PRD-000452', 'SKU-00452', '8900000000452', 'Sample Product 452', NULL, NULL, 2, 2, 1, 100.00, 330.00, 303.60, 280.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(453, NULL, 'PRD-000453', 'SKU-00453', '8900000000453', 'Sample Product 453', NULL, NULL, 3, 3, 1, 100.00, 345.00, 317.40, 293.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(454, NULL, 'PRD-000454', 'SKU-00454', '8900000000454', 'Sample Product 454', NULL, NULL, 4, 4, 1, 100.00, 360.00, 331.20, 306.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(455, NULL, 'PRD-000455', 'SKU-00455', '8900000000455', 'Sample Product 455', NULL, NULL, 5, 5, 1, 100.00, 375.00, 345.00, 318.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(456, NULL, 'PRD-000456', 'SKU-00456', '8900000000456', 'Sample Product 456', NULL, NULL, 6, 6, 1, 100.00, 390.00, 358.80, 331.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(457, NULL, 'PRD-000457', 'SKU-00457', '8900000000457', 'Sample Product 457', NULL, NULL, 1, 7, 1, 100.00, 405.00, 372.60, 344.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(458, NULL, 'PRD-000458', 'SKU-00458', '8900000000458', 'Sample Product 458', NULL, NULL, 2, 8, 1, 100.00, 420.00, 386.40, 357.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(459, NULL, 'PRD-000459', 'SKU-00459', '8900000000459', 'Sample Product 459', NULL, NULL, 3, 9, 1, 100.00, 435.00, 400.20, 369.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(460, NULL, 'PRD-000460', 'SKU-00460', '8900000000460', 'Sample Product 460', NULL, NULL, 4, 10, 1, 100.00, 150.00, 138.00, 127.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(461, NULL, 'PRD-000461', 'SKU-00461', '8900000000461', 'Sample Product 461', NULL, NULL, 5, 1, 1, 100.00, 165.00, 151.80, 140.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(462, NULL, 'PRD-000462', 'SKU-00462', '8900000000462', 'Sample Product 462', NULL, NULL, 6, 2, 1, 100.00, 180.00, 165.60, 153.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(463, NULL, 'PRD-000463', 'SKU-00463', '8900000000463', 'Sample Product 463', NULL, NULL, 1, 3, 1, 100.00, 195.00, 179.40, 165.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(464, NULL, 'PRD-000464', 'SKU-00464', '8900000000464', 'Sample Product 464', NULL, NULL, 2, 4, 1, 100.00, 210.00, 193.20, 178.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(465, NULL, 'PRD-000465', 'SKU-00465', '8900000000465', 'Sample Product 465', NULL, NULL, 3, 5, 1, 100.00, 225.00, 207.00, 191.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(466, NULL, 'PRD-000466', 'SKU-00466', '8900000000466', 'Sample Product 466', NULL, NULL, 4, 6, 1, 100.00, 240.00, 220.80, 204.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(467, NULL, 'PRD-000467', 'SKU-00467', '8900000000467', 'Sample Product 467', NULL, NULL, 5, 7, 1, 100.00, 255.00, 234.60, 216.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(468, NULL, 'PRD-000468', 'SKU-00468', '8900000000468', 'Sample Product 468', NULL, NULL, 6, 8, 1, 100.00, 270.00, 248.40, 229.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(469, NULL, 'PRD-000469', 'SKU-00469', '8900000000469', 'Sample Product 469', NULL, NULL, 1, 9, 1, 100.00, 285.00, 262.20, 242.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(470, NULL, 'PRD-000470', 'SKU-00470', '8900000000470', 'Sample Product 470', NULL, NULL, 2, 10, 1, 100.00, 300.00, 276.00, 255.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(471, NULL, 'PRD-000471', 'SKU-00471', '8900000000471', 'Sample Product 471', NULL, NULL, 3, 1, 1, 100.00, 315.00, 289.80, 267.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(472, NULL, 'PRD-000472', 'SKU-00472', '8900000000472', 'Sample Product 472', NULL, NULL, 4, 2, 1, 100.00, 330.00, 303.60, 280.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(473, NULL, 'PRD-000473', 'SKU-00473', '8900000000473', 'Sample Product 473', NULL, NULL, 5, 3, 1, 100.00, 345.00, 317.40, 293.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(474, NULL, 'PRD-000474', 'SKU-00474', '8900000000474', 'Sample Product 474', NULL, NULL, 6, 4, 1, 100.00, 360.00, 331.20, 306.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(475, NULL, 'PRD-000475', 'SKU-00475', '8900000000475', 'Sample Product 475', NULL, NULL, 1, 5, 1, 100.00, 375.00, 345.00, 318.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(476, NULL, 'PRD-000476', 'SKU-00476', '8900000000476', 'Sample Product 476', NULL, NULL, 2, 6, 1, 100.00, 390.00, 358.80, 331.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(477, NULL, 'PRD-000477', 'SKU-00477', '8900000000477', 'Sample Product 477', NULL, NULL, 3, 7, 1, 100.00, 405.00, 372.60, 344.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(478, NULL, 'PRD-000478', 'SKU-00478', '8900000000478', 'Sample Product 478', NULL, NULL, 4, 8, 1, 100.00, 420.00, 386.40, 357.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(479, NULL, 'PRD-000479', 'SKU-00479', '8900000000479', 'Sample Product 479', NULL, NULL, 5, 9, 1, 100.00, 435.00, 400.20, 369.75, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(480, NULL, 'PRD-000480', 'SKU-00480', '8900000000480', 'Sample Product 480', NULL, NULL, 6, 10, 1, 100.00, 150.00, 138.00, 127.50, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(481, NULL, 'PRD-000481', 'SKU-00481', '8900000000481', 'Sample Product 481', NULL, NULL, 1, 1, 1, 100.00, 165.00, 151.80, 140.25, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(482, NULL, 'PRD-000482', 'SKU-00482', '8900000000482', 'Sample Product 482', NULL, NULL, 2, 2, 1, 100.00, 180.00, 165.60, 153.00, 0.0000, 5.0000, 2.0000, 2000.0000, 1, 0, 0, 0, 'average', 0, NULL, 'Sample ERP product', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(483, NULL, 'PRD-000483', '0004', NULL, 'শাহ সিমেন্ট', NULL, NULL, 7, 11, 8, 480.00, 500.00, 500.00, 500.00, 0.0000, 0.0000, 0.0000, NULL, 1, 0, 0, 0, 'average', 1, NULL, NULL, 1, '2026-09-25 16:34:58', '2026-09-25 16:34:58', NULL),
(484, NULL, 'PRD-000484', '0008', NULL, 'বিএসআরএম ১০ মিলি', NULL, NULL, 8, 12, 2, 93.00, 94.00, 94.00, 94.00, 0.0000, 0.0000, 100.0000, 1000.0000, 1, 0, 0, 0, 'average', 1, NULL, NULL, 1, '2026-09-25 16:40:01', '2026-09-25 16:40:01', NULL),
(485, NULL, 'PRD-000485', '0009', NULL, 'বিএসআরএম ০৮ মিলি', NULL, NULL, 8, 12, 2, 93.00, 94.00, 94.00, 94.00, 0.0000, 0.0000, 100.0000, 1000.0000, 1, 0, 0, 0, 'average', 1, NULL, NULL, 1, '2026-09-25 16:41:59', '2026-09-25 16:41:59', NULL),
(486, NULL, 'PRD-000486', '0010', NULL, 'বিএসআরএম ১২ মিলি', NULL, NULL, 8, 12, 2, 93.00, 94.00, 94.00, 94.00, 0.0000, 0.0000, 100.0000, 1000.0000, 1, 0, 0, 0, 'average', 1, NULL, NULL, 1, '2026-09-25 16:42:44', '2026-09-25 16:42:44', NULL),
(487, NULL, 'PRD-000487', '0011', NULL, 'বিএসআরএম ১৬ মিলি', NULL, NULL, 8, 12, 2, 93.00, 94.00, 94.00, 94.00, 0.0000, 0.0000, 100.0000, 1000.0000, 1, 0, 0, 0, 'average', 1, NULL, NULL, 1, '2026-09-25 16:43:08', '2026-09-25 16:43:08', NULL),
(488, NULL, 'PRD-000488', '0012', NULL, 'বিএসআরএম ২০ মিলি', NULL, NULL, 8, 12, 2, 93.00, 94.00, 94.00, 94.00, 0.0000, 0.0000, 100.0000, 1000.0000, 1, 0, 0, 0, 'average', 1, NULL, NULL, 1, '2026-09-25 16:43:37', '2026-09-25 16:43:37', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `product_deliveries`
--

CREATE TABLE `product_deliveries` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `delivery_no` varchar(60) NOT NULL,
  `delivery_type` varchar(20) NOT NULL,
  `purchase_id` bigint(20) UNSIGNED DEFAULT NULL,
  `sale_id` bigint(20) UNSIGNED DEFAULT NULL,
  `warehouse_id` bigint(20) UNSIGNED NOT NULL,
  `delivery_date` date NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'draft',
  `reference` varchar(120) DEFAULT NULL,
  `driver_name` varchar(150) DEFAULT NULL,
  `vehicle_no` varchar(100) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `posted_by` bigint(20) UNSIGNED DEFAULT NULL,
  `posted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_delivery_items`
--

CREATE TABLE `product_delivery_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_delivery_id` bigint(20) UNSIGNED NOT NULL,
  `source_item_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` decimal(18,4) NOT NULL,
  `remarks` varchar(500) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_serials`
--

CREATE TABLE `product_serials` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `warehouse_id` bigint(20) UNSIGNED DEFAULT NULL,
  `serial_no` varchar(160) NOT NULL,
  `imei` varchar(160) DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'in_stock',
  `source_type` varchar(50) DEFAULT NULL,
  `source_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(40) NOT NULL,
  `name` varchar(160) NOT NULL,
  `cost_center_id` bigint(20) UNSIGNED DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `purchases`
--

CREATE TABLE `purchases` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `purchase_no` varchar(50) NOT NULL,
  `supplier_id` bigint(20) UNSIGNED NOT NULL,
  `warehouse_id` bigint(20) UNSIGNED NOT NULL,
  `purchase_date` date NOT NULL,
  `due_date` date DEFAULT NULL,
  `supplier_invoice_no` varchar(100) DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'draft',
  `currency_code` varchar(10) NOT NULL DEFAULT 'BDT',
  `exchange_rate` decimal(18,6) NOT NULL DEFAULT 1.000000,
  `subtotal` decimal(18,2) NOT NULL DEFAULT 0.00,
  `discount` decimal(18,2) NOT NULL DEFAULT 0.00,
  `tax` decimal(18,2) NOT NULL DEFAULT 0.00,
  `shipping` decimal(18,2) NOT NULL DEFAULT 0.00,
  `other_cost` decimal(18,2) NOT NULL DEFAULT 0.00,
  `grand_total` decimal(18,2) NOT NULL DEFAULT 0.00,
  `paid_amount` decimal(18,2) NOT NULL DEFAULT 0.00,
  `due_amount` decimal(18,2) NOT NULL DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `posted_by` bigint(20) UNSIGNED DEFAULT NULL,
  `posted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `purchases`
--

INSERT INTO `purchases` (`id`, `purchase_no`, `supplier_id`, `warehouse_id`, `purchase_date`, `due_date`, `supplier_invoice_no`, `status`, `currency_code`, `exchange_rate`, `subtotal`, `discount`, `tax`, `shipping`, `other_cost`, `grand_total`, `paid_amount`, `due_amount`, `notes`, `created_by`, `posted_by`, `posted_at`, `created_at`, `updated_at`) VALUES
(1, 'PUR-000001', 1, 1, '2026-03-04', '2026-04-03', 'SUPINV-00001', 'posted', 'BDT', 1.000000, 75000.00, 0.00, 0.00, 0.00, 0.00, 75000.00, 54000.00, 21000.00, 'Sample posted purchase', 1, 1, '2026-03-04 05:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(2, 'PUR-000002', 2, 1, '2026-03-11', '2026-04-10', 'SUPINV-00002', 'posted', 'BDT', 1.000000, 75000.00, 0.00, 0.00, 0.00, 0.00, 75000.00, 54000.00, 21000.00, 'Sample posted purchase', 1, 1, '2026-03-11 05:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(3, 'PUR-000003', 3, 1, '2026-03-18', '2026-04-17', 'SUPINV-00003', 'posted', 'BDT', 1.000000, 75000.00, 0.00, 0.00, 0.00, 0.00, 75000.00, 54000.00, 21000.00, 'Sample posted purchase', 1, 1, '2026-03-18 05:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(4, 'PUR-000004', 4, 1, '2026-03-25', '2026-04-24', 'SUPINV-00004', 'posted', 'BDT', 1.000000, 75000.00, 0.00, 0.00, 0.00, 0.00, 75000.00, 54000.00, 21000.00, 'Sample posted purchase', 1, 1, '2026-03-25 05:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(5, 'PUR-000005', 5, 1, '2026-04-04', '2026-05-04', 'SUPINV-00005', 'posted', 'BDT', 1.000000, 87500.00, 0.00, 0.00, 0.00, 0.00, 87500.00, 63000.00, 24500.00, 'Sample posted purchase', 1, 1, '2026-04-04 05:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(6, 'PUR-000006', 6, 1, '2026-04-11', '2026-05-11', 'SUPINV-00006', 'posted', 'BDT', 1.000000, 87500.00, 0.00, 0.00, 0.00, 0.00, 87500.00, 63000.00, 24500.00, 'Sample posted purchase', 1, 1, '2026-04-11 05:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(7, 'PUR-000007', 7, 1, '2026-04-18', '2026-05-18', 'SUPINV-00007', 'posted', 'BDT', 1.000000, 87500.00, 0.00, 0.00, 0.00, 0.00, 87500.00, 63000.00, 24500.00, 'Sample posted purchase', 1, 1, '2026-04-18 05:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(8, 'PUR-000008', 8, 1, '2026-04-25', '2026-05-25', 'SUPINV-00008', 'posted', 'BDT', 1.000000, 87500.00, 0.00, 0.00, 0.00, 0.00, 87500.00, 63000.00, 24500.00, 'Sample posted purchase', 1, 1, '2026-04-25 05:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(9, 'PUR-000009', 9, 1, '2026-05-04', '2026-06-03', 'SUPINV-00009', 'posted', 'BDT', 1.000000, 97500.00, 0.00, 0.00, 0.00, 0.00, 97500.00, 70200.00, 27300.00, 'Sample posted purchase', 1, 1, '2026-05-04 05:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(10, 'PUR-000010', 10, 1, '2026-05-11', '2026-06-10', 'SUPINV-00010', 'posted', 'BDT', 1.000000, 97500.00, 0.00, 0.00, 0.00, 0.00, 97500.00, 70200.00, 27300.00, 'Sample posted purchase', 1, 1, '2026-05-11 05:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(11, 'PUR-000011', 11, 1, '2026-05-18', '2026-06-17', 'SUPINV-00011', 'posted', 'BDT', 1.000000, 97500.00, 0.00, 0.00, 0.00, 0.00, 97500.00, 70200.00, 27300.00, 'Sample posted purchase', 1, 1, '2026-05-18 05:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(12, 'PUR-000012', 12, 1, '2026-05-25', '2026-06-24', 'SUPINV-00012', 'posted', 'BDT', 1.000000, 97500.00, 0.00, 0.00, 0.00, 0.00, 97500.00, 70200.00, 27300.00, 'Sample posted purchase', 1, 1, '2026-05-25 05:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(13, 'PUR-000013', 1, 1, '2026-06-04', '2026-07-04', 'SUPINV-00013', 'posted', 'BDT', 1.000000, 127725.00, 0.00, 0.00, 0.00, 0.00, 127725.00, 91962.00, 35763.00, 'Sample posted purchase', 1, 1, '2026-06-04 05:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(14, 'PUR-000014', 2, 1, '2026-06-11', '2026-07-11', 'SUPINV-00014', 'posted', 'BDT', 1.000000, 106550.00, 0.00, 0.00, 0.00, 0.00, 106550.00, 76716.00, 29834.00, 'Sample posted purchase', 1, 1, '2026-06-11 05:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(15, 'PUR-000015', 3, 1, '2026-06-18', '2026-07-18', 'SUPINV-00015', 'posted', 'BDT', 1.000000, 64334.00, 0.00, 0.00, 0.00, 0.00, 64334.00, 46320.48, 18013.52, 'Sample posted purchase', 1, 1, '2026-06-18 05:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(16, 'PUR-000016', 4, 1, '2026-06-25', '2026-07-25', 'SUPINV-00016', 'posted', 'BDT', 1.000000, 111391.00, 0.00, 0.00, 0.00, 0.00, 111391.00, 80201.52, 31189.48, 'Sample posted purchase', 1, 1, '2026-06-25 05:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(17, 'PUR-000017', 5, 1, '2026-07-04', '2026-08-03', 'SUPINV-00017', 'posted', 'BDT', 1.000000, 107500.00, 0.00, 0.00, 0.00, 0.00, 107500.00, 77400.00, 30100.00, 'Sample posted purchase', 1, 1, '2026-07-04 05:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(18, 'PUR-000018', 6, 1, '2026-07-11', '2026-08-10', 'SUPINV-00018', 'posted', 'BDT', 1.000000, 107500.00, 0.00, 0.00, 0.00, 0.00, 107500.00, 77400.00, 30100.00, 'Sample posted purchase', 1, 1, '2026-07-11 05:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(19, 'PUR-000019', 7, 1, '2026-07-18', '2026-08-17', 'SUPINV-00019', 'posted', 'BDT', 1.000000, 107500.00, 0.00, 0.00, 0.00, 0.00, 107500.00, 77400.00, 30100.00, 'Sample posted purchase', 1, 1, '2026-07-18 05:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(20, 'PUR-000020', 8, 1, '2026-07-25', '2026-08-24', 'SUPINV-00020', 'posted', 'BDT', 1.000000, 107500.00, 0.00, 0.00, 0.00, 0.00, 107500.00, 77400.00, 30100.00, 'Sample posted purchase', 1, 1, '2026-07-25 05:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(21, 'PUR-000021', 6, 1, '2026-08-01', '2026-08-31', 'SUPINV-00021', 'posted', 'BDT', 1.000000, 28770.00, 0.00, 0.00, 0.00, 0.00, 28770.00, 20139.00, 8631.00, 'August purchase', 1, 1, '2026-08-01 05:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(22, 'PUR-000022', 7, 1, '2026-08-03', '2026-09-02', 'SUPINV-00022', 'posted', 'BDT', 1.000000, 28770.00, 0.00, 0.00, 0.00, 0.00, 28770.00, 20139.00, 8631.00, 'August purchase', 1, 1, '2026-08-03 05:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(23, 'PUR-000023', 8, 1, '2026-08-05', '2026-09-04', 'SUPINV-00023', 'posted', 'BDT', 1.000000, 28770.00, 0.00, 0.00, 0.00, 0.00, 28770.00, 20139.00, 8631.00, 'August purchase', 1, 1, '2026-08-05 05:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(24, 'PUR-000024', 9, 1, '2026-08-07', '2026-09-06', 'SUPINV-00024', 'posted', 'BDT', 1.000000, 28770.00, 0.00, 0.00, 0.00, 0.00, 28770.00, 20139.00, 8631.00, 'August purchase', 1, 1, '2026-08-07 05:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(25, 'PUR-000025', 10, 1, '2026-08-09', '2026-09-08', 'SUPINV-00025', 'posted', 'BDT', 1.000000, 28770.00, 0.00, 0.00, 0.00, 0.00, 28770.00, 20139.00, 8631.00, 'August purchase', 1, 1, '2026-08-09 05:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(26, 'PUR-000026', 11, 1, '2026-08-11', '2026-09-10', 'SUPINV-00026', 'posted', 'BDT', 1.000000, 28770.00, 0.00, 0.00, 0.00, 0.00, 28770.00, 20139.00, 8631.00, 'August purchase', 1, 1, '2026-08-11 05:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(27, 'PUR-000027', 12, 1, '2026-08-13', '2026-09-12', 'SUPINV-00027', 'posted', 'BDT', 1.000000, 28770.00, 0.00, 0.00, 0.00, 0.00, 28770.00, 20139.00, 8631.00, 'August purchase', 1, 1, '2026-08-13 05:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(28, 'PUR-000028', 13, 1, '2026-08-15', '2026-09-14', 'SUPINV-00028', 'posted', 'BDT', 1.000000, 28770.00, 0.00, 0.00, 0.00, 0.00, 28770.00, 20139.00, 8631.00, 'August purchase', 1, 1, '2026-08-15 05:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(29, 'PUR-000029', 14, 1, '2026-08-17', '2026-09-16', 'SUPINV-00029', 'posted', 'BDT', 1.000000, 28770.00, 0.00, 0.00, 0.00, 0.00, 28770.00, 20139.00, 8631.00, 'August purchase', 1, 1, '2026-08-17 05:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(30, 'PUR-000030', 15, 1, '2026-08-19', '2026-09-18', 'SUPINV-00030', 'posted', 'BDT', 1.000000, 28770.00, 0.00, 0.00, 0.00, 0.00, 28770.00, 20139.00, 8631.00, 'August purchase', 1, 1, '2026-08-19 05:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(31, 'PUR-000031', 1, 1, '2026-08-20', '2026-09-19', 'SUPINV-00031', 'posted', 'BDT', 1.000000, 28770.00, 0.00, 0.00, 0.00, 0.00, 28770.00, 20139.00, 8631.00, 'August purchase', 1, 1, '2026-08-20 05:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(32, 'PUR-000032', 2, 1, '2026-08-22', '2026-09-21', 'SUPINV-00032', 'posted', 'BDT', 1.000000, 28770.00, 0.00, 0.00, 0.00, 0.00, 28770.00, 20139.00, 8631.00, 'August purchase', 1, 1, '2026-08-22 05:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(33, 'PUR-000033', 3, 1, '2026-08-24', '2026-09-23', 'SUPINV-00033', 'posted', 'BDT', 1.000000, 28770.00, 0.00, 0.00, 0.00, 0.00, 28770.00, 20139.00, 8631.00, 'August purchase', 1, 1, '2026-08-24 05:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(34, 'PUR-000034', 4, 1, '2026-08-26', '2026-09-25', 'SUPINV-00034', 'posted', 'BDT', 1.000000, 28770.00, 0.00, 0.00, 0.00, 0.00, 28770.00, 20139.00, 8631.00, 'August purchase', 1, 1, '2026-08-26 05:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(35, 'PUR-000035', 5, 1, '2026-08-27', '2026-09-26', 'SUPINV-00035', 'posted', 'BDT', 1.000000, 28770.00, 0.00, 0.00, 0.00, 0.00, 28770.00, 20139.00, 8631.00, 'August purchase', 1, 1, '2026-08-27 05:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(36, 'PUR-000036', 1, 1, '2026-08-28', '2026-09-27', 'SUPINV-00036', 'posted', 'BDT', 1.000000, 18750.00, 0.00, 0.00, 0.00, 0.00, 18750.00, 14000.00, 4750.00, 'Today\'s purchase', 1, 1, '2026-08-28 03:15:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00');

-- --------------------------------------------------------

--
-- Table structure for table `purchase_items`
--

CREATE TABLE `purchase_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `purchase_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` decimal(18,4) NOT NULL,
  `free_quantity` decimal(18,4) NOT NULL DEFAULT 0.0000,
  `batch_no` varchar(100) DEFAULT NULL,
  `manufactured_at` date DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `unit_cost` decimal(18,4) NOT NULL,
  `discount_type` varchar(20) NOT NULL DEFAULT 'fixed',
  `discount_value` decimal(18,4) NOT NULL DEFAULT 0.0000,
  `discount` decimal(18,2) NOT NULL DEFAULT 0.00,
  `tax` decimal(18,2) NOT NULL DEFAULT 0.00,
  `line_total` decimal(18,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `purchase_items`
--

INSERT INTO `purchase_items` (`id`, `purchase_id`, `product_id`, `quantity`, `free_quantity`, `batch_no`, `manufactured_at`, `expiry_date`, `unit_cost`, `discount_type`, `discount_value`, `discount`, `tax`, `line_total`, `created_at`, `updated_at`) VALUES
(1, 1, 6, 1.0000, 0.0000, NULL, NULL, NULL, 75000.0000, 'fixed', 0.0000, 0.00, 0.00, 75000.00, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(2, 2, 11, 1.0000, 0.0000, NULL, NULL, NULL, 75000.0000, 'fixed', 0.0000, 0.00, 0.00, 75000.00, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(3, 3, 16, 1.0000, 0.0000, NULL, NULL, NULL, 75000.0000, 'fixed', 0.0000, 0.00, 0.00, 75000.00, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(4, 4, 21, 1.0000, 0.0000, NULL, NULL, NULL, 75000.0000, 'fixed', 0.0000, 0.00, 0.00, 75000.00, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(5, 5, 26, 1.0000, 0.0000, NULL, NULL, NULL, 87500.0000, 'fixed', 0.0000, 0.00, 0.00, 87500.00, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(6, 6, 31, 1.0000, 0.0000, NULL, NULL, NULL, 87500.0000, 'fixed', 0.0000, 0.00, 0.00, 87500.00, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(7, 7, 36, 1.0000, 0.0000, NULL, NULL, NULL, 87500.0000, 'fixed', 0.0000, 0.00, 0.00, 87500.00, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(8, 8, 41, 1.0000, 0.0000, NULL, NULL, NULL, 87500.0000, 'fixed', 0.0000, 0.00, 0.00, 87500.00, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(9, 9, 46, 1.0000, 0.0000, NULL, NULL, NULL, 97500.0000, 'fixed', 0.0000, 0.00, 0.00, 97500.00, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(10, 10, 51, 1.0000, 0.0000, NULL, NULL, NULL, 97500.0000, 'fixed', 0.0000, 0.00, 0.00, 97500.00, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(11, 11, 56, 1.0000, 0.0000, NULL, NULL, NULL, 97500.0000, 'fixed', 0.0000, 0.00, 0.00, 97500.00, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(12, 12, 61, 1.0000, 0.0000, NULL, NULL, NULL, 97500.0000, 'fixed', 0.0000, 0.00, 0.00, 97500.00, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(13, 13, 66, 1.0000, 0.0000, NULL, NULL, NULL, 127725.0000, 'fixed', 0.0000, 0.00, 0.00, 127725.00, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(14, 14, 71, 1.0000, 0.0000, NULL, NULL, NULL, 106550.0000, 'fixed', 0.0000, 0.00, 0.00, 106550.00, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(15, 15, 76, 1.0000, 0.0000, NULL, NULL, NULL, 64334.0000, 'fixed', 0.0000, 0.00, 0.00, 64334.00, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(16, 16, 81, 1.0000, 0.0000, NULL, NULL, NULL, 111391.0000, 'fixed', 0.0000, 0.00, 0.00, 111391.00, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(17, 17, 86, 1.0000, 0.0000, NULL, NULL, NULL, 107500.0000, 'fixed', 0.0000, 0.00, 0.00, 107500.00, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(18, 18, 1, 1.0000, 0.0000, NULL, NULL, NULL, 107500.0000, 'fixed', 0.0000, 0.00, 0.00, 107500.00, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(19, 19, 6, 1.0000, 0.0000, NULL, NULL, NULL, 107500.0000, 'fixed', 0.0000, 0.00, 0.00, 107500.00, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(20, 20, 11, 1.0000, 0.0000, NULL, NULL, NULL, 107500.0000, 'fixed', 0.0000, 0.00, 0.00, 107500.00, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(21, 21, 64, 1.0000, 0.0000, NULL, NULL, NULL, 28770.0000, 'fixed', 0.0000, 0.00, 0.00, 28770.00, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(22, 22, 67, 1.0000, 0.0000, NULL, NULL, NULL, 28770.0000, 'fixed', 0.0000, 0.00, 0.00, 28770.00, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(23, 23, 70, 1.0000, 0.0000, NULL, NULL, NULL, 28770.0000, 'fixed', 0.0000, 0.00, 0.00, 28770.00, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(24, 24, 73, 1.0000, 0.0000, NULL, NULL, NULL, 28770.0000, 'fixed', 0.0000, 0.00, 0.00, 28770.00, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(25, 25, 76, 1.0000, 0.0000, NULL, NULL, NULL, 28770.0000, 'fixed', 0.0000, 0.00, 0.00, 28770.00, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(26, 26, 79, 1.0000, 0.0000, NULL, NULL, NULL, 28770.0000, 'fixed', 0.0000, 0.00, 0.00, 28770.00, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(27, 27, 82, 1.0000, 0.0000, NULL, NULL, NULL, 28770.0000, 'fixed', 0.0000, 0.00, 0.00, 28770.00, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(28, 28, 85, 1.0000, 0.0000, NULL, NULL, NULL, 28770.0000, 'fixed', 0.0000, 0.00, 0.00, 28770.00, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(29, 29, 88, 1.0000, 0.0000, NULL, NULL, NULL, 28770.0000, 'fixed', 0.0000, 0.00, 0.00, 28770.00, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(30, 30, 91, 1.0000, 0.0000, NULL, NULL, NULL, 28770.0000, 'fixed', 0.0000, 0.00, 0.00, 28770.00, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(31, 31, 94, 1.0000, 0.0000, NULL, NULL, NULL, 28770.0000, 'fixed', 0.0000, 0.00, 0.00, 28770.00, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(32, 32, 97, 1.0000, 0.0000, NULL, NULL, NULL, 28770.0000, 'fixed', 0.0000, 0.00, 0.00, 28770.00, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(33, 33, 100, 1.0000, 0.0000, NULL, NULL, NULL, 28770.0000, 'fixed', 0.0000, 0.00, 0.00, 28770.00, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(34, 34, 3, 1.0000, 0.0000, NULL, NULL, NULL, 28770.0000, 'fixed', 0.0000, 0.00, 0.00, 28770.00, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(35, 35, 6, 1.0000, 0.0000, NULL, NULL, NULL, 28770.0000, 'fixed', 0.0000, 0.00, 0.00, 28770.00, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(36, 36, 4, 1.0000, 0.0000, NULL, NULL, NULL, 18750.0000, 'fixed', 0.0000, 0.00, 0.00, 18750.00, '2026-08-28 03:30:00', '2026-08-28 03:30:00');

-- --------------------------------------------------------

--
-- Table structure for table `purchase_orders`
--

CREATE TABLE `purchase_orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_no` varchar(50) NOT NULL,
  `supplier_id` bigint(20) UNSIGNED NOT NULL,
  `warehouse_id` bigint(20) UNSIGNED NOT NULL,
  `order_date` date NOT NULL,
  `expected_date` date DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'open',
  `grand_total` decimal(18,2) NOT NULL DEFAULT 0.00,
  `currency_code` varchar(10) NOT NULL DEFAULT 'BDT',
  `exchange_rate` decimal(18,6) NOT NULL DEFAULT 1.000000,
  `notes` text DEFAULT NULL,
  `converted_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_order_items`
--

CREATE TABLE `purchase_order_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `purchase_order_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` decimal(18,4) NOT NULL,
  `unit_price` decimal(18,4) NOT NULL,
  `line_total` decimal(18,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_returns`
--

CREATE TABLE `purchase_returns` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `return_no` varchar(50) NOT NULL,
  `purchase_id` bigint(20) UNSIGNED DEFAULT NULL,
  `supplier_id` bigint(20) UNSIGNED NOT NULL,
  `warehouse_id` bigint(20) UNSIGNED NOT NULL,
  `return_date` date NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'draft',
  `subtotal` decimal(18,2) NOT NULL DEFAULT 0.00,
  `tax` decimal(18,2) NOT NULL DEFAULT 0.00,
  `grand_total` decimal(18,2) NOT NULL DEFAULT 0.00,
  `reason` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `posted_by` bigint(20) UNSIGNED DEFAULT NULL,
  `posted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `purchase_returns`
--

INSERT INTO `purchase_returns` (`id`, `return_no`, `purchase_id`, `supplier_id`, `warehouse_id`, `return_date`, `status`, `subtotal`, `tax`, `grand_total`, `reason`, `created_by`, `posted_by`, `posted_at`, `created_at`, `updated_at`) VALUES
(1, 'PRET-000001', 1, 1, 1, '2026-08-18', 'posted', 5000.00, 0.00, 5000.00, 'Damaged item', 1, 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00');

-- --------------------------------------------------------

--
-- Table structure for table `purchase_return_items`
--

CREATE TABLE `purchase_return_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `purchase_return_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` decimal(18,4) NOT NULL,
  `unit_cost` decimal(18,4) NOT NULL,
  `line_total` decimal(18,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `purchase_return_items`
--

INSERT INTO `purchase_return_items` (`id`, `purchase_return_id`, `product_id`, `quantity`, `unit_cost`, `line_total`, `created_at`, `updated_at`) VALUES
(1, 1, 3, 1.0000, 5000.0000, 5000.00, '2026-08-28 03:30:00', '2026-08-28 03:30:00');

-- --------------------------------------------------------

--
-- Table structure for table `quotations`
--

CREATE TABLE `quotations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `quotation_no` varchar(50) NOT NULL,
  `customer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `quotation_date` date NOT NULL,
  `valid_until` date DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'draft',
  `subtotal` decimal(18,2) NOT NULL DEFAULT 0.00,
  `discount` decimal(18,2) NOT NULL DEFAULT 0.00,
  `tax` decimal(18,2) NOT NULL DEFAULT 0.00,
  `grand_total` decimal(18,2) NOT NULL DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `quotations`
--

INSERT INTO `quotations` (`id`, `quotation_no`, `customer_id`, `quotation_date`, `valid_until`, `status`, `subtotal`, `discount`, `tax`, `grand_total`, `notes`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'QUO-000001', 5, '2026-08-25', '2026-09-10', 'sent', 85000.00, 5000.00, 0.00, 80000.00, 'Corporate quotation', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(2, 'QUO-000002', 8, '2026-08-27', '2026-09-15', 'accepted', 120000.00, 0.00, 0.00, 120000.00, 'Accepted quotation', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(3, 'QUO-2026-000003', 9, '2026-09-25', '2026-10-12', 'expired', 500.00, 0.00, 0.00, 500.00, NULL, 1, '2026-09-26 19:07:58', '2026-09-26 19:10:38');

-- --------------------------------------------------------

--
-- Table structure for table `quotation_items`
--

CREATE TABLE `quotation_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `quotation_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` decimal(18,4) NOT NULL,
  `unit_price` decimal(18,4) NOT NULL,
  `discount_type` varchar(20) NOT NULL DEFAULT 'fixed',
  `discount_value` decimal(18,4) NOT NULL DEFAULT 0.0000,
  `discount` decimal(18,2) NOT NULL DEFAULT 0.00,
  `tax` decimal(18,2) NOT NULL DEFAULT 0.00,
  `line_total` decimal(18,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `quotation_items`
--

INSERT INTO `quotation_items` (`id`, `quotation_id`, `product_id`, `quantity`, `unit_price`, `discount_type`, `discount_value`, `discount`, `tax`, `line_total`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 1.0000, 85000.0000, 'fixed', 5000.0000, 5000.00, 0.00, 80000.00, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(2, 2, 9, 1.0000, 120000.0000, 'fixed', 0.0000, 0.00, 0.00, 120000.00, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(3, 3, 483, 1.0000, 500.0000, 'fixed', 0.0000, 0.00, 0.00, 500.00, '2026-09-26 19:07:58', '2026-09-26 19:07:58');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'Super Admin', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(2, 'Admin', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(3, 'Manager', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(4, 'Accountant', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(5, 'Sales Executive', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(6, 'Purchase Officer', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(7, 'Store Keeper', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(8, 'Viewer', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(9, 'Payroll Officer', 'web', '2026-08-27 18:00:00', '2026-08-27 18:00:00'),
(10, 'Seller', 'web', '2026-09-28 01:00:08', '2026-09-28 01:00:08');

-- --------------------------------------------------------

--
-- Table structure for table `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_has_permissions`
--

INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
(1, 1),
(1, 2),
(2, 1),
(2, 2),
(3, 1),
(3, 2),
(4, 1),
(4, 2),
(5, 1),
(5, 2),
(6, 1),
(6, 2),
(7, 1),
(7, 2),
(8, 1),
(8, 2),
(9, 1),
(9, 2),
(10, 1),
(10, 2),
(11, 1),
(11, 2),
(12, 1),
(12, 2),
(13, 1),
(13, 2),
(13, 3),
(13, 4),
(13, 5),
(13, 8),
(14, 1),
(14, 2),
(14, 3),
(14, 5),
(15, 1),
(15, 2),
(15, 3),
(16, 1),
(16, 2),
(16, 3),
(17, 1),
(17, 2),
(17, 3),
(17, 4),
(17, 5),
(18, 1),
(18, 2),
(18, 3),
(18, 5),
(19, 1),
(19, 2),
(20, 1),
(20, 2),
(21, 1),
(21, 2),
(22, 1),
(22, 2),
(23, 1),
(23, 2),
(23, 3),
(23, 4),
(23, 6),
(23, 8),
(24, 1),
(24, 2),
(24, 3),
(24, 6),
(25, 1),
(25, 2),
(25, 3),
(25, 6),
(26, 1),
(26, 2),
(26, 3),
(26, 6),
(27, 1),
(27, 2),
(27, 3),
(27, 4),
(27, 6),
(28, 1),
(28, 2),
(28, 3),
(28, 6),
(29, 1),
(29, 2),
(30, 1),
(30, 2),
(31, 1),
(31, 2),
(32, 1),
(32, 2),
(33, 1),
(33, 2),
(34, 1),
(34, 2),
(35, 1),
(35, 2),
(36, 1),
(36, 2),
(37, 1),
(37, 2),
(38, 1),
(38, 2),
(39, 1),
(39, 2),
(40, 1),
(40, 2),
(41, 1),
(41, 2),
(41, 3),
(41, 5),
(41, 6),
(41, 7),
(41, 8),
(42, 1),
(42, 2),
(43, 1),
(43, 2),
(44, 1),
(44, 2),
(45, 1),
(45, 2),
(46, 1),
(46, 2),
(47, 1),
(47, 2),
(47, 7),
(48, 1),
(48, 2),
(49, 1),
(49, 2),
(50, 1),
(50, 2),
(51, 1),
(51, 2),
(51, 3),
(51, 5),
(51, 6),
(51, 7),
(51, 8),
(52, 1),
(52, 2),
(52, 3),
(52, 7),
(52, 8),
(53, 1),
(53, 2),
(53, 3),
(53, 7),
(54, 1),
(54, 2),
(54, 3),
(54, 7),
(55, 1),
(55, 2),
(55, 3),
(55, 7),
(56, 1),
(56, 2),
(56, 3),
(56, 7),
(57, 1),
(57, 2),
(57, 3),
(57, 4),
(57, 6),
(57, 8),
(58, 1),
(58, 2),
(58, 3),
(58, 6),
(59, 1),
(59, 2),
(59, 3),
(59, 6),
(60, 1),
(60, 2),
(60, 3),
(60, 6),
(61, 1),
(61, 2),
(61, 3),
(61, 6),
(62, 1),
(62, 2),
(62, 3),
(62, 6),
(63, 1),
(63, 2),
(63, 3),
(63, 6),
(64, 1),
(64, 2),
(64, 3),
(64, 4),
(64, 5),
(64, 8),
(64, 10),
(65, 1),
(65, 2),
(65, 3),
(65, 5),
(65, 10),
(66, 1),
(66, 2),
(66, 3),
(67, 1),
(67, 2),
(67, 3),
(68, 1),
(68, 2),
(68, 3),
(69, 1),
(69, 2),
(69, 3),
(69, 5),
(70, 1),
(70, 2),
(70, 3),
(70, 5),
(71, 1),
(71, 2),
(71, 3),
(71, 4),
(72, 1),
(72, 2),
(72, 4),
(73, 1),
(73, 2),
(73, 4),
(74, 1),
(74, 2),
(74, 4),
(75, 1),
(75, 2),
(75, 4),
(76, 1),
(76, 2),
(76, 4),
(77, 1),
(77, 2),
(77, 4),
(77, 9),
(78, 1),
(78, 2),
(78, 9),
(79, 1),
(79, 2),
(79, 9),
(80, 1),
(80, 2),
(80, 9),
(81, 1),
(81, 2),
(81, 9),
(82, 1),
(82, 2),
(82, 4),
(83, 1),
(83, 2),
(83, 4),
(84, 1),
(84, 2),
(84, 4),
(85, 1),
(85, 2),
(85, 4),
(86, 1),
(86, 2),
(86, 4),
(87, 1),
(87, 2),
(87, 4),
(88, 1),
(88, 2),
(88, 3),
(88, 4),
(88, 8),
(89, 1),
(89, 2),
(89, 3),
(89, 4),
(90, 1),
(90, 2),
(90, 3),
(90, 4),
(91, 1),
(91, 2),
(92, 1),
(92, 2),
(93, 1),
(93, 2),
(154, 1),
(154, 2),
(154, 3),
(154, 6),
(157, 1),
(157, 2),
(157, 3),
(157, 6),
(158, 1),
(158, 2),
(158, 3),
(158, 6),
(159, 1),
(159, 2),
(159, 3),
(159, 6),
(160, 1),
(160, 2),
(160, 3),
(160, 6),
(165, 1),
(165, 2),
(165, 3),
(165, 5),
(165, 10),
(168, 1),
(168, 2),
(168, 3),
(168, 5),
(169, 1),
(169, 2),
(169, 3),
(169, 5),
(170, 1),
(170, 2),
(170, 3),
(171, 1),
(171, 2),
(171, 3),
(171, 5),
(172, 1),
(172, 2),
(172, 3),
(172, 5),
(173, 1),
(173, 2),
(173, 3),
(173, 5),
(174, 1),
(174, 2),
(174, 3),
(175, 1),
(175, 2),
(175, 3),
(176, 1),
(176, 2),
(176, 3),
(176, 5),
(177, 1),
(177, 2),
(177, 4),
(177, 5),
(177, 10),
(178, 1),
(178, 2),
(178, 4),
(178, 5),
(178, 10),
(179, 1),
(179, 2),
(179, 4),
(179, 6),
(180, 1),
(180, 2),
(180, 4),
(181, 1),
(181, 2),
(182, 1),
(182, 2),
(183, 1),
(183, 2),
(184, 1),
(184, 2),
(189, 1),
(189, 2),
(189, 3),
(189, 4),
(191, 1),
(191, 2),
(191, 9),
(192, 1),
(192, 2),
(192, 9),
(193, 1),
(193, 2),
(193, 9),
(194, 1),
(194, 2),
(194, 9),
(198, 1),
(198, 2),
(198, 9),
(199, 1),
(199, 2),
(199, 9),
(200, 1),
(200, 2),
(200, 9),
(206, 1),
(206, 2),
(206, 4),
(207, 1),
(207, 2),
(207, 4),
(214, 1),
(214, 2),
(214, 3),
(215, 1),
(215, 2),
(215, 3),
(215, 6),
(216, 1),
(216, 2),
(216, 3),
(217, 1),
(217, 2),
(218, 1),
(218, 2),
(219, 1),
(219, 2),
(220, 1),
(220, 2),
(221, 1),
(221, 2),
(222, 1),
(222, 2),
(223, 1),
(223, 2),
(224, 1),
(224, 2),
(225, 1),
(225, 2),
(226, 1),
(226, 2),
(227, 1),
(227, 2),
(228, 1),
(228, 2),
(229, 1),
(229, 2),
(230, 1),
(230, 2),
(231, 1),
(231, 2),
(232, 1),
(232, 2),
(233, 1),
(233, 2),
(234, 1),
(234, 2),
(235, 1),
(235, 2),
(236, 1),
(236, 2),
(237, 1),
(237, 2),
(238, 1),
(238, 2),
(239, 1),
(239, 2),
(240, 1),
(240, 2),
(241, 1),
(241, 2),
(242, 1),
(242, 2),
(243, 1),
(243, 2),
(244, 1),
(244, 2);

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

CREATE TABLE `sales` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `invoice_no` varchar(50) NOT NULL,
  `customer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `warehouse_id` bigint(20) UNSIGNED NOT NULL,
  `sale_date` date NOT NULL,
  `due_date` date DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'draft',
  `currency_code` varchar(10) NOT NULL DEFAULT 'BDT',
  `exchange_rate` decimal(18,6) NOT NULL DEFAULT 1.000000,
  `subtotal` decimal(18,2) NOT NULL DEFAULT 0.00,
  `discount` decimal(18,2) NOT NULL DEFAULT 0.00,
  `tax` decimal(18,2) NOT NULL DEFAULT 0.00,
  `shipping` decimal(18,2) NOT NULL DEFAULT 0.00,
  `labor_charge` decimal(18,2) NOT NULL DEFAULT 0.00,
  `grand_total` decimal(18,2) NOT NULL DEFAULT 0.00,
  `paid_amount` decimal(18,2) NOT NULL DEFAULT 0.00,
  `due_amount` decimal(18,2) NOT NULL DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `posted_by` bigint(20) UNSIGNED DEFAULT NULL,
  `posted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sales`
--

INSERT INTO `sales` (`id`, `invoice_no`, `customer_id`, `warehouse_id`, `sale_date`, `due_date`, `status`, `currency_code`, `exchange_rate`, `subtotal`, `discount`, `tax`, `shipping`, `labor_charge`, `grand_total`, `paid_amount`, `due_amount`, `notes`, `created_by`, `posted_by`, `posted_at`, `created_at`, `updated_at`) VALUES
(1, 'INV-000001', 2, 1, '2026-03-05', '2026-04-04', 'posted', 'BDT', 1.000000, 120000.00, 0.00, 0.00, 0.00, 0.00, 120000.00, 84000.00, 36000.00, 'Sample posted sale', 1, 1, '2026-03-05 09:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(2, 'INV-000002', 3, 1, '2026-03-12', '2026-04-11', 'posted', 'BDT', 1.000000, 120000.00, 0.00, 0.00, 0.00, 0.00, 120000.00, 84000.00, 36000.00, 'Sample posted sale', 1, 1, '2026-03-12 09:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(3, 'INV-000003', 4, 1, '2026-03-19', '2026-04-18', 'posted', 'BDT', 1.000000, 120000.00, 0.00, 0.00, 0.00, 0.00, 120000.00, 84000.00, 36000.00, 'Sample posted sale', 1, 1, '2026-03-19 09:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(4, 'INV-000004', 5, 1, '2026-03-26', '2026-04-25', 'posted', 'BDT', 1.000000, 120000.00, 0.00, 0.00, 0.00, 0.00, 120000.00, 84000.00, 36000.00, 'Sample posted sale', 1, 1, '2026-03-26 09:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(5, 'INV-000005', 6, 1, '2026-04-05', '2026-05-05', 'posted', 'BDT', 1.000000, 135197.00, 0.00, 0.00, 0.00, 0.00, 135197.00, 94637.90, 40559.10, 'Sample posted sale', 1, 1, '2026-04-05 09:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(6, 'INV-000006', 7, 1, '2026-04-12', '2026-05-12', 'posted', 'BDT', 1.000000, 93937.00, 0.00, 0.00, 0.00, 0.00, 93937.00, 65755.90, 28181.10, 'Sample posted sale', 1, 1, '2026-04-12 09:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(7, 'INV-000007', 8, 1, '2026-04-19', '2026-05-19', 'posted', 'BDT', 1.000000, 174208.00, 0.00, 0.00, 0.00, 0.00, 174208.00, 121945.60, 52262.40, 'Sample posted sale', 1, 1, '2026-04-19 09:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(8, 'INV-000008', 9, 1, '2026-04-26', '2026-05-26', 'posted', 'BDT', 1.000000, 121658.00, 0.00, 0.00, 0.00, 0.00, 121658.00, 85160.60, 36497.40, 'Sample posted sale', 1, 1, '2026-04-26 09:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(9, 'INV-000009', 10, 1, '2026-05-05', '2026-06-04', 'posted', 'BDT', 1.000000, 152500.00, 0.00, 0.00, 0.00, 0.00, 152500.00, 106750.00, 45750.00, 'Sample posted sale', 1, 1, '2026-05-05 09:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(10, 'INV-000010', 11, 1, '2026-05-12', '2026-06-11', 'posted', 'BDT', 1.000000, 152500.00, 0.00, 0.00, 0.00, 0.00, 152500.00, 106750.00, 45750.00, 'Sample posted sale', 1, 1, '2026-05-12 09:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(11, 'INV-000011', 12, 1, '2026-05-19', '2026-06-18', 'posted', 'BDT', 1.000000, 152500.00, 0.00, 0.00, 0.00, 0.00, 152500.00, 106750.00, 45750.00, 'Sample posted sale', 1, 1, '2026-05-19 09:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(12, 'INV-000012', 13, 1, '2026-05-26', '2026-06-25', 'posted', 'BDT', 1.000000, 152500.00, 0.00, 0.00, 0.00, 0.00, 152500.00, 106750.00, 45750.00, 'Sample posted sale', 1, 1, '2026-05-26 09:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(13, 'INV-000013', 14, 1, '2026-06-05', '2026-07-05', 'posted', 'BDT', 1.000000, 143750.00, 0.00, 0.00, 0.00, 0.00, 143750.00, 100625.00, 43125.00, 'Sample posted sale', 1, 1, '2026-06-05 09:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(14, 'INV-000014', 15, 1, '2026-06-12', '2026-07-12', 'posted', 'BDT', 1.000000, 143750.00, 0.00, 0.00, 0.00, 0.00, 143750.00, 100625.00, 43125.00, 'Sample posted sale', 1, 1, '2026-06-12 09:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(15, 'INV-000015', 16, 1, '2026-06-19', '2026-07-19', 'posted', 'BDT', 1.000000, 143750.00, 0.00, 0.00, 0.00, 0.00, 143750.00, 100625.00, 43125.00, 'Sample posted sale', 1, 1, '2026-06-19 09:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(16, 'INV-000016', 17, 1, '2026-06-26', '2026-07-26', 'posted', 'BDT', 1.000000, 143750.00, 0.00, 0.00, 0.00, 0.00, 143750.00, 100625.00, 43125.00, 'Sample posted sale', 1, 1, '2026-06-26 09:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(17, 'INV-000017', 18, 1, '2026-07-05', '2026-08-04', 'posted', 'BDT', 1.000000, 160000.00, 0.00, 0.00, 0.00, 0.00, 160000.00, 112000.00, 48000.00, 'Sample posted sale', 1, 1, '2026-07-05 09:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(18, 'INV-000018', 19, 1, '2026-07-12', '2026-08-11', 'posted', 'BDT', 1.000000, 160000.00, 0.00, 0.00, 0.00, 0.00, 160000.00, 112000.00, 48000.00, 'Sample posted sale', 1, 1, '2026-07-12 09:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(19, 'INV-000019', 20, 1, '2026-07-19', '2026-08-18', 'posted', 'BDT', 1.000000, 160000.00, 0.00, 0.00, 0.00, 0.00, 160000.00, 112000.00, 48000.00, 'Sample posted sale', 1, 1, '2026-07-19 09:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(20, 'INV-000020', 21, 1, '2026-07-26', '2026-08-25', 'posted', 'BDT', 1.000000, 160000.00, 0.00, 0.00, 0.00, 0.00, 160000.00, 112000.00, 48000.00, 'Sample posted sale', 1, 1, '2026-07-26 09:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(21, 'INV-000021', 22, 1, '2026-08-01', '2026-08-31', 'posted', 'BDT', 1.000000, 32835.00, 0.00, 0.00, 0.00, 0.00, 32835.00, 23641.20, 9193.80, 'August sample sale', 1, 1, '2026-08-01 09:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(22, 'INV-000022', 23, 1, '2026-08-02', '2026-09-01', 'posted', 'BDT', 1.000000, 32835.00, 0.00, 0.00, 0.00, 0.00, 32835.00, 23641.20, 9193.80, 'August sample sale', 1, 1, '2026-08-02 09:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(23, 'INV-000023', 24, 1, '2026-08-03', '2026-09-02', 'posted', 'BDT', 1.000000, 32835.00, 0.00, 0.00, 0.00, 0.00, 32835.00, 23641.20, 9193.80, 'August sample sale', 1, 1, '2026-08-03 09:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(24, 'INV-000024', 25, 1, '2026-08-04', '2026-09-03', 'posted', 'BDT', 1.000000, 32835.00, 0.00, 0.00, 0.00, 0.00, 32835.00, 23641.20, 9193.80, 'August sample sale', 1, 1, '2026-08-04 09:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(25, 'INV-000025', 26, 1, '2026-08-05', '2026-09-04', 'posted', 'BDT', 1.000000, 32835.00, 0.00, 0.00, 0.00, 0.00, 32835.00, 23641.20, 9193.80, 'August sample sale', 1, 1, '2026-08-05 09:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(26, 'INV-000026', 27, 1, '2026-08-06', '2026-09-05', 'posted', 'BDT', 1.000000, 32835.00, 0.00, 0.00, 0.00, 0.00, 32835.00, 23641.20, 9193.80, 'August sample sale', 1, 1, '2026-08-06 09:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(27, 'INV-000027', 28, 1, '2026-08-07', '2026-09-06', 'posted', 'BDT', 1.000000, 32835.00, 0.00, 0.00, 0.00, 0.00, 32835.00, 23641.20, 9193.80, 'August sample sale', 1, 1, '2026-08-07 09:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(28, 'INV-000028', 29, 1, '2026-08-08', '2026-09-07', 'posted', 'BDT', 1.000000, 32835.00, 0.00, 0.00, 0.00, 0.00, 32835.00, 23641.20, 9193.80, 'August sample sale', 1, 1, '2026-08-08 09:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(29, 'INV-000029', 30, 1, '2026-08-09', '2026-09-08', 'posted', 'BDT', 1.000000, 32835.00, 0.00, 0.00, 0.00, 0.00, 32835.00, 23641.20, 9193.80, 'August sample sale', 1, 1, '2026-08-09 09:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(30, 'INV-000030', 31, 1, '2026-08-10', '2026-09-09', 'posted', 'BDT', 1.000000, 32835.00, 0.00, 0.00, 0.00, 0.00, 32835.00, 23641.20, 9193.80, 'August sample sale', 1, 1, '2026-08-10 09:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(31, 'INV-000031', 32, 1, '2026-08-11', '2026-09-10', 'posted', 'BDT', 1.000000, 32835.00, 0.00, 0.00, 0.00, 0.00, 32835.00, 23641.20, 9193.80, 'August sample sale', 1, 1, '2026-08-11 09:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(32, 'INV-000032', 33, 1, '2026-08-12', '2026-09-11', 'posted', 'BDT', 1.000000, 32835.00, 0.00, 0.00, 0.00, 0.00, 32835.00, 23641.20, 9193.80, 'August sample sale', 1, 1, '2026-08-12 09:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(33, 'INV-000033', 34, 1, '2026-08-13', '2026-09-12', 'posted', 'BDT', 1.000000, 32835.00, 0.00, 0.00, 0.00, 0.00, 32835.00, 23641.20, 9193.80, 'August sample sale', 1, 1, '2026-08-13 09:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(34, 'INV-000034', 35, 1, '2026-08-14', '2026-09-13', 'posted', 'BDT', 1.000000, 32835.00, 0.00, 0.00, 0.00, 0.00, 32835.00, 23641.20, 9193.80, 'August sample sale', 1, 1, '2026-08-14 09:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(35, 'INV-000035', 36, 1, '2026-08-15', '2026-09-14', 'posted', 'BDT', 1.000000, 32835.00, 0.00, 0.00, 0.00, 0.00, 32835.00, 23641.20, 9193.80, 'August sample sale', 1, 1, '2026-08-15 09:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(36, 'INV-000036', 37, 1, '2026-08-16', '2026-09-15', 'posted', 'BDT', 1.000000, 32835.00, 0.00, 0.00, 0.00, 0.00, 32835.00, 23641.20, 9193.80, 'August sample sale', 1, 1, '2026-08-16 09:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(37, 'INV-000037', 38, 1, '2026-08-18', '2026-09-17', 'posted', 'BDT', 1.000000, 32835.00, 0.00, 0.00, 0.00, 0.00, 32835.00, 23641.20, 9193.80, 'August sample sale', 1, 1, '2026-08-18 09:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(38, 'INV-000038', 39, 1, '2026-08-20', '2026-09-19', 'posted', 'BDT', 1.000000, 32835.00, 0.00, 0.00, 0.00, 0.00, 32835.00, 23641.20, 9193.80, 'August sample sale', 1, 1, '2026-08-20 09:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(39, 'INV-000039', 40, 1, '2026-08-24', '2026-09-23', 'posted', 'BDT', 1.000000, 32835.00, 0.00, 0.00, 0.00, 0.00, 32835.00, 23641.20, 9193.80, 'August sample sale', 1, 1, '2026-08-24 09:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(40, 'INV-000040', 41, 1, '2026-08-27', '2026-09-26', 'posted', 'BDT', 1.000000, 32845.00, 0.00, 0.00, 0.00, 0.00, 32845.00, 23648.40, 9196.60, 'August sample sale', 1, 1, '2026-08-27 09:00:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(41, 'INV-000041', 2, 1, '2026-08-28', '2026-09-27', 'posted', 'BDT', 1.000000, 15000.00, 0.00, 0.00, 0.00, 0.00, 15000.00, 11250.00, 3750.00, 'Today\'s sample sale', 1, 1, '2026-08-28 04:30:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(42, 'INV-000042', 3, 1, '2026-08-28', '2026-09-27', 'posted', 'BDT', 1.000000, 13540.00, 0.00, 0.00, 0.00, 0.00, 13540.00, 10155.00, 3385.00, 'Today\'s sample sale', 1, 1, '2026-08-28 04:30:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(43, 'SAL-2026-000043', 3, 4, '2026-09-25', '2026-09-27', 'draft', 'BDT', 1.000000, 3750.00, 0.00, 0.00, 0.00, 0.00, 3750.00, 0.00, 3750.00, NULL, 1, NULL, NULL, '2026-09-25 15:47:08', '2026-09-25 15:47:08'),
(44, 'SAL-2026-000044', 8, 4, '2026-09-26', NULL, 'draft', 'BDT', 1.000000, 180.00, 0.03, 0.03, 0.05, 0.00, 180.05, 0.00, 180.05, NULL, 1, NULL, NULL, '2026-09-26 19:24:32', '2026-09-26 19:24:32'),
(45, 'SAL-2026-000045', NULL, 1, '2026-09-28', NULL, 'draft', 'BDT', 1.000000, 180.00, 0.00, 0.00, 0.00, 0.00, 180.00, 0.00, 180.00, NULL, 2, NULL, NULL, '2026-09-28 04:49:10', '2026-09-28 04:49:10'),
(46, 'SAL-2026-000046', NULL, 1, '2026-09-28', NULL, 'posted', 'BDT', 1.000000, 345.00, 0.00, 0.00, 0.00, 0.00, 345.00, 345.00, 0.00, NULL, 3, 3, '2026-09-28 16:05:26', '2026-09-28 16:05:21', '2026-09-28 16:05:26'),
(47, 'SAL-2026-000047', NULL, 1, '2026-09-28', NULL, 'draft', 'BDT', 1.000000, 7050.00, 10.00, 15.00, 20.00, 10.00, 7080.00, 0.00, 7080.00, 'done', 3, NULL, NULL, '2026-09-28 16:54:52', '2026-09-28 16:54:52'),
(48, 'SAL-2026-000048', NULL, 1, '2026-09-28', NULL, 'draft', 'BDT', 1.000000, 7395.00, 10.00, 15.00, 20.00, 10.00, 7425.00, 0.00, 7425.00, 'done', 3, NULL, NULL, '2026-09-28 16:55:44', '2026-09-28 16:55:44'),
(49, 'SAL-2026-000049', NULL, 1, '2026-09-28', NULL, 'draft', 'BDT', 1.000000, 7395.00, 10.00, 15.00, 20.00, 10.00, 7425.00, 0.00, 7425.00, 'done', 3, NULL, NULL, '2026-09-28 16:56:00', '2026-09-28 16:56:00'),
(50, 'SAL-2026-000050', NULL, 1, '2026-09-28', NULL, 'draft', 'BDT', 1.000000, 7395.00, 10.00, 15.00, 20.00, 10.00, 7425.00, 0.00, 7425.00, 'done', 3, NULL, NULL, '2026-09-28 16:56:10', '2026-09-28 16:56:10'),
(51, 'SAL-2026-000051', NULL, 1, '2026-09-28', NULL, 'draft', 'BDT', 1.000000, 4695.00, 10.00, 15.00, 20.00, 10.00, 4725.00, 0.00, 4725.00, 'done', 3, NULL, NULL, '2026-09-28 16:57:35', '2026-09-28 16:57:35'),
(52, 'SAL-2026-000052', NULL, 1, '2026-09-28', NULL, 'draft', 'BDT', 1.000000, 870.00, 10.00, 15.00, 20.00, 10.00, 900.00, 0.00, 900.00, 'done', 3, NULL, NULL, '2026-09-28 16:58:01', '2026-09-28 16:58:01'),
(53, 'SAL-2026-000053', NULL, 1, '2026-09-28', NULL, 'posted', 'BDT', 1.000000, 510.00, 10.00, 15.00, 20.00, 10.00, 540.00, 540.00, 0.00, 'done', 3, 3, '2026-09-28 16:59:32', '2026-09-28 16:59:28', '2026-09-28 16:59:32'),
(54, 'SAL-2026-000054', NULL, 1, '2026-09-28', NULL, 'draft', 'BDT', 1.000000, 510.00, 10.00, 15.00, 20.00, 10.00, 540.00, 0.00, 540.00, 'done', 3, NULL, NULL, '2026-09-28 17:00:16', '2026-09-28 17:00:16'),
(55, 'SAL-2026-000055', NULL, 1, '2026-09-28', NULL, 'draft', 'BDT', 1.000000, 510.00, 10.00, 15.00, 20.00, 10.00, 540.00, 0.00, 540.00, 'done', 3, NULL, NULL, '2026-09-28 17:04:27', '2026-09-28 17:04:27'),
(56, 'SAL-2026-000056', NULL, 1, '2026-09-28', NULL, 'posted', 'BDT', 1.000000, 510.00, 10.00, 15.00, 20.00, 10.00, 540.00, 540.00, 0.00, 'done', 3, 3, '2026-09-28 17:46:38', '2026-09-28 17:04:27', '2026-09-28 17:46:38'),
(57, 'SAL-2026-000057', 5, 1, '2026-09-28', NULL, 'posted', 'BDT', 1.000000, 150.00, 2.00, 2.00, 2.00, 2.00, 152.00, 152.00, 0.00, 'done', 3, 3, '2026-09-28 17:09:49', '2026-09-28 17:09:44', '2026-09-28 17:49:03'),
(58, 'SAL-2026-000058', NULL, 1, '2026-09-28', NULL, 'draft', 'BDT', 1.000000, 345.00, 3.00, 2.00, 1.00, 5.00, 350.00, 0.00, 350.00, NULL, 3, NULL, NULL, '2026-09-28 20:35:19', '2026-09-28 20:35:19'),
(59, 'SAL-2026-000059', 5, 2, '2026-09-28', '2026-09-21', 'draft', 'BDT', 1.000000, 180.00, 20.00, 10.00, 100.00, 50.00, 320.00, 0.00, 320.00, NULL, 3, NULL, NULL, '2026-09-28 20:40:30', '2026-09-28 20:40:30'),
(60, 'SAL-2026-000060', 17, 4, '2026-09-28', NULL, 'draft', 'BDT', 1.000000, 35700.00, 0.00, 0.00, 0.00, 0.00, 35700.00, 0.00, 35700.00, NULL, 3, NULL, NULL, '2026-09-28 21:38:29', '2026-09-28 21:38:29'),
(61, 'SAL-2026-000061', 5, 1, '2026-09-29', NULL, 'posted', 'BDT', 1.000000, 345.00, 0.00, 0.00, 0.00, 0.00, 345.00, 200.00, 145.00, NULL, 3, 3, '2026-09-29 14:03:17', '2026-09-29 14:03:10', '2026-09-29 14:04:11');

-- --------------------------------------------------------

--
-- Table structure for table `sales_orders`
--

CREATE TABLE `sales_orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_no` varchar(50) NOT NULL,
  `customer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `warehouse_id` bigint(20) UNSIGNED NOT NULL,
  `order_date` date NOT NULL,
  `expected_date` date DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'open',
  `grand_total` decimal(18,2) NOT NULL DEFAULT 0.00,
  `currency_code` varchar(10) NOT NULL DEFAULT 'BDT',
  `exchange_rate` decimal(18,6) NOT NULL DEFAULT 1.000000,
  `notes` text DEFAULT NULL,
  `converted_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sales_order_items`
--

CREATE TABLE `sales_order_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sales_order_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` decimal(18,4) NOT NULL,
  `unit_price` decimal(18,4) NOT NULL,
  `line_total` decimal(18,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sale_items`
--

CREATE TABLE `sale_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sale_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` decimal(18,4) NOT NULL,
  `free_quantity` decimal(18,4) NOT NULL DEFAULT 0.0000,
  `bundle_quantity` decimal(18,4) DEFAULT NULL,
  `unit_price` decimal(18,4) NOT NULL,
  `cost_price` decimal(18,4) NOT NULL DEFAULT 0.0000,
  `price_type` varchar(20) NOT NULL DEFAULT 'retail',
  `discount_type` varchar(20) NOT NULL DEFAULT 'fixed',
  `discount_value` decimal(18,4) NOT NULL DEFAULT 0.0000,
  `discount` decimal(18,2) NOT NULL DEFAULT 0.00,
  `tax` decimal(18,2) NOT NULL DEFAULT 0.00,
  `line_total` decimal(18,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sale_items`
--

INSERT INTO `sale_items` (`id`, `sale_id`, `product_id`, `quantity`, `free_quantity`, `bundle_quantity`, `unit_price`, `cost_price`, `price_type`, `discount_type`, `discount_value`, `discount`, `tax`, `line_total`, `created_at`, `updated_at`) VALUES
(1, 1, 8, 1.0000, 0.0000, NULL, 120000.0000, 72000.0000, 'retail', 'fixed', 0.0000, 0.00, 0.00, 120000.00, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(2, 2, 15, 1.0000, 0.0000, NULL, 120000.0000, 72000.0000, 'retail', 'fixed', 0.0000, 0.00, 0.00, 120000.00, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(3, 3, 22, 1.0000, 0.0000, NULL, 120000.0000, 72000.0000, 'retail', 'fixed', 0.0000, 0.00, 0.00, 120000.00, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(4, 4, 29, 1.0000, 0.0000, NULL, 120000.0000, 72000.0000, 'retail', 'fixed', 0.0000, 0.00, 0.00, 120000.00, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(5, 5, 36, 1.0000, 0.0000, NULL, 135197.0000, 81118.2000, 'retail', 'fixed', 0.0000, 0.00, 0.00, 135197.00, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(6, 6, 43, 1.0000, 0.0000, NULL, 93937.0000, 56362.2000, 'retail', 'fixed', 0.0000, 0.00, 0.00, 93937.00, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(7, 7, 50, 1.0000, 0.0000, NULL, 174208.0000, 104524.8000, 'retail', 'fixed', 0.0000, 0.00, 0.00, 174208.00, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(8, 8, 57, 1.0000, 0.0000, NULL, 121658.0000, 72994.8000, 'retail', 'fixed', 0.0000, 0.00, 0.00, 121658.00, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(9, 9, 64, 1.0000, 0.0000, NULL, 152500.0000, 91500.0000, 'retail', 'fixed', 0.0000, 0.00, 0.00, 152500.00, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(10, 10, 71, 1.0000, 0.0000, NULL, 152500.0000, 91500.0000, 'retail', 'fixed', 0.0000, 0.00, 0.00, 152500.00, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(11, 11, 78, 1.0000, 0.0000, NULL, 152500.0000, 91500.0000, 'retail', 'fixed', 0.0000, 0.00, 0.00, 152500.00, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(12, 12, 5, 1.0000, 0.0000, NULL, 152500.0000, 91500.0000, 'retail', 'fixed', 0.0000, 0.00, 0.00, 152500.00, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(13, 13, 12, 1.0000, 0.0000, NULL, 143750.0000, 86250.0000, 'retail', 'fixed', 0.0000, 0.00, 0.00, 143750.00, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(14, 14, 19, 1.0000, 0.0000, NULL, 143750.0000, 86250.0000, 'retail', 'fixed', 0.0000, 0.00, 0.00, 143750.00, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(15, 15, 26, 1.0000, 0.0000, NULL, 143750.0000, 86250.0000, 'retail', 'fixed', 0.0000, 0.00, 0.00, 143750.00, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(16, 16, 33, 1.0000, 0.0000, NULL, 143750.0000, 86250.0000, 'retail', 'fixed', 0.0000, 0.00, 0.00, 143750.00, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(17, 17, 40, 1.0000, 0.0000, NULL, 160000.0000, 96000.0000, 'retail', 'fixed', 0.0000, 0.00, 0.00, 160000.00, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(18, 18, 47, 1.0000, 0.0000, NULL, 160000.0000, 96000.0000, 'retail', 'fixed', 0.0000, 0.00, 0.00, 160000.00, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(19, 19, 54, 1.0000, 0.0000, NULL, 160000.0000, 96000.0000, 'retail', 'fixed', 0.0000, 0.00, 0.00, 160000.00, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(20, 20, 61, 1.0000, 0.0000, NULL, 160000.0000, 96000.0000, 'retail', 'fixed', 0.0000, 0.00, 0.00, 160000.00, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(21, 21, 90, 1.0000, 0.0000, NULL, 32835.0000, 19701.0000, 'retail', 'fixed', 0.0000, 0.00, 0.00, 32835.00, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(22, 22, 99, 1.0000, 0.0000, NULL, 32835.0000, 19701.0000, 'retail', 'fixed', 0.0000, 0.00, 0.00, 32835.00, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(23, 23, 8, 1.0000, 0.0000, NULL, 32835.0000, 19701.0000, 'retail', 'fixed', 0.0000, 0.00, 0.00, 32835.00, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(24, 24, 17, 1.0000, 0.0000, NULL, 32835.0000, 19701.0000, 'retail', 'fixed', 0.0000, 0.00, 0.00, 32835.00, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(25, 25, 26, 1.0000, 0.0000, NULL, 32835.0000, 19701.0000, 'retail', 'fixed', 0.0000, 0.00, 0.00, 32835.00, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(26, 26, 35, 1.0000, 0.0000, NULL, 32835.0000, 19701.0000, 'retail', 'fixed', 0.0000, 0.00, 0.00, 32835.00, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(27, 27, 44, 1.0000, 0.0000, NULL, 32835.0000, 19701.0000, 'retail', 'fixed', 0.0000, 0.00, 0.00, 32835.00, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(28, 28, 53, 1.0000, 0.0000, NULL, 32835.0000, 19701.0000, 'retail', 'fixed', 0.0000, 0.00, 0.00, 32835.00, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(29, 29, 62, 1.0000, 0.0000, NULL, 32835.0000, 19701.0000, 'retail', 'fixed', 0.0000, 0.00, 0.00, 32835.00, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(30, 30, 71, 1.0000, 0.0000, NULL, 32835.0000, 19701.0000, 'retail', 'fixed', 0.0000, 0.00, 0.00, 32835.00, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(31, 31, 80, 1.0000, 0.0000, NULL, 32835.0000, 19701.0000, 'retail', 'fixed', 0.0000, 0.00, 0.00, 32835.00, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(32, 32, 89, 1.0000, 0.0000, NULL, 32835.0000, 19701.0000, 'retail', 'fixed', 0.0000, 0.00, 0.00, 32835.00, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(33, 33, 98, 1.0000, 0.0000, NULL, 32835.0000, 19701.0000, 'retail', 'fixed', 0.0000, 0.00, 0.00, 32835.00, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(34, 34, 7, 1.0000, 0.0000, NULL, 32835.0000, 19701.0000, 'retail', 'fixed', 0.0000, 0.00, 0.00, 32835.00, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(35, 35, 16, 1.0000, 0.0000, NULL, 32835.0000, 19701.0000, 'retail', 'fixed', 0.0000, 0.00, 0.00, 32835.00, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(36, 36, 25, 1.0000, 0.0000, NULL, 32835.0000, 19701.0000, 'retail', 'fixed', 0.0000, 0.00, 0.00, 32835.00, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(37, 37, 34, 1.0000, 0.0000, NULL, 32835.0000, 19701.0000, 'retail', 'fixed', 0.0000, 0.00, 0.00, 32835.00, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(38, 38, 43, 1.0000, 0.0000, NULL, 32835.0000, 19701.0000, 'retail', 'fixed', 0.0000, 0.00, 0.00, 32835.00, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(39, 39, 52, 1.0000, 0.0000, NULL, 32835.0000, 19701.0000, 'retail', 'fixed', 0.0000, 0.00, 0.00, 32835.00, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(40, 40, 61, 1.0000, 0.0000, NULL, 32845.0000, 19707.0000, 'retail', 'fixed', 0.0000, 0.00, 0.00, 32845.00, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(41, 41, 52, 1.0000, 0.0000, NULL, 15000.0000, 9000.0000, 'retail', 'fixed', 0.0000, 0.00, 0.00, 15000.00, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(42, 42, 63, 1.0000, 0.0000, NULL, 13540.0000, 8124.0000, 'retail', 'fixed', 0.0000, 0.00, 0.00, 13540.00, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(45, 43, 20, 10.0000, 0.0000, NULL, 150.0000, 100.0000, 'retail', 'fixed', 0.0000, 0.00, 0.00, 1500.00, '2026-09-25 15:47:31', '2026-09-25 15:47:31'),
(46, 43, 25, 10.0000, 0.0000, NULL, 225.0000, 100.0000, 'retail', 'fixed', 0.0000, 0.00, 0.00, 2250.00, '2026-09-25 15:47:31', '2026-09-25 15:47:31'),
(47, 44, 2, 1.0000, 0.0000, NULL, 180.0000, 100.0000, 'retail', 'fixed', 0.0000, 0.00, 0.00, 180.00, '2026-09-26 19:24:32', '2026-09-26 19:24:32'),
(48, 45, 2, 1.0000, 0.0000, NULL, 180.0000, 100.0000, 'retail', 'fixed', 0.0000, 0.00, 0.00, 180.00, '2026-09-28 04:49:10', '2026-09-28 04:49:10'),
(49, 46, 13, 1.0000, 0.0000, NULL, 345.0000, 100.0000, 'retail', 'fixed', 0.0000, 0.00, 0.00, 345.00, '2026-09-28 16:05:21', '2026-09-28 16:05:21'),
(50, 47, 13, 10.0000, 0.0000, 0.0001, 345.0000, 100.0000, 'retail', 'fixed', 2.0000, 2.00, 0.00, 3448.00, '2026-09-28 16:54:52', '2026-09-28 16:54:52'),
(51, 47, 2, 20.0000, 0.0000, NULL, 180.0000, 100.0000, 'retail', 'fixed', 3.0000, 3.00, 0.00, 3597.00, '2026-09-28 16:54:52', '2026-09-28 16:54:52'),
(52, 48, 13, 11.0000, 0.0000, 0.0001, 345.0000, 100.0000, 'retail', 'fixed', 2.0000, 2.00, 0.00, 3793.00, '2026-09-28 16:55:44', '2026-09-28 16:55:44'),
(53, 48, 2, 20.0000, 0.0000, NULL, 180.0000, 100.0000, 'retail', 'fixed', 3.0000, 3.00, 0.00, 3597.00, '2026-09-28 16:55:44', '2026-09-28 16:55:44'),
(54, 49, 13, 11.0000, 0.0000, 0.0001, 345.0000, 100.0000, 'retail', 'fixed', 2.0000, 2.00, 0.00, 3793.00, '2026-09-28 16:56:00', '2026-09-28 16:56:00'),
(55, 49, 2, 20.0000, 0.0000, NULL, 180.0000, 100.0000, 'retail', 'fixed', 3.0000, 3.00, 0.00, 3597.00, '2026-09-28 16:56:00', '2026-09-28 16:56:00'),
(56, 50, 13, 11.0000, 0.0000, 0.0001, 345.0000, 100.0000, 'retail', 'fixed', 2.0000, 2.00, 0.00, 3793.00, '2026-09-28 16:56:10', '2026-09-28 16:56:10'),
(57, 50, 2, 20.0000, 0.0000, NULL, 180.0000, 100.0000, 'retail', 'fixed', 3.0000, 3.00, 0.00, 3597.00, '2026-09-28 16:56:10', '2026-09-28 16:56:10'),
(58, 51, 13, 11.0000, 0.0000, 0.0001, 345.0000, 100.0000, 'retail', 'fixed', 2.0000, 2.00, 0.00, 3793.00, '2026-09-28 16:57:35', '2026-09-28 16:57:35'),
(59, 51, 2, 5.0000, 0.0000, NULL, 180.0000, 100.0000, 'retail', 'fixed', 3.0000, 3.00, 0.00, 897.00, '2026-09-28 16:57:35', '2026-09-28 16:57:35'),
(60, 52, 13, 2.0000, 0.0000, 0.0001, 345.0000, 100.0000, 'retail', 'fixed', 2.0000, 2.00, 0.00, 688.00, '2026-09-28 16:58:01', '2026-09-28 16:58:01'),
(61, 52, 2, 1.0000, 0.0000, NULL, 180.0000, 100.0000, 'retail', 'fixed', 3.0000, 3.00, 0.00, 177.00, '2026-09-28 16:58:01', '2026-09-28 16:58:01'),
(62, 53, 12, 1.0000, 0.0000, 0.0001, 330.0000, 100.0000, 'retail', 'fixed', 2.0000, 2.00, 0.00, 328.00, '2026-09-28 16:59:28', '2026-09-28 16:59:28'),
(63, 53, 2, 1.0000, 0.0000, NULL, 180.0000, 100.0000, 'retail', 'fixed', 3.0000, 3.00, 0.00, 177.00, '2026-09-28 16:59:28', '2026-09-28 16:59:28'),
(64, 54, 12, 1.0000, 0.0000, 0.0001, 330.0000, 100.0000, 'retail', 'fixed', 2.0000, 2.00, 0.00, 328.00, '2026-09-28 17:00:16', '2026-09-28 17:00:16'),
(65, 54, 2, 1.0000, 0.0000, NULL, 180.0000, 100.0000, 'retail', 'fixed', 3.0000, 3.00, 0.00, 177.00, '2026-09-28 17:00:16', '2026-09-28 17:00:16'),
(66, 55, 12, 1.0000, 0.0000, 0.0001, 330.0000, 100.0000, 'retail', 'fixed', 2.0000, 2.00, 0.00, 328.00, '2026-09-28 17:04:27', '2026-09-28 17:04:27'),
(67, 55, 2, 1.0000, 0.0000, NULL, 180.0000, 100.0000, 'retail', 'fixed', 3.0000, 3.00, 0.00, 177.00, '2026-09-28 17:04:27', '2026-09-28 17:04:27'),
(68, 56, 12, 1.0000, 0.0000, 0.0001, 330.0000, 100.0000, 'retail', 'fixed', 2.0000, 2.00, 0.00, 328.00, '2026-09-28 17:04:27', '2026-09-28 17:04:27'),
(69, 56, 2, 1.0000, 0.0000, NULL, 180.0000, 100.0000, 'retail', 'fixed', 3.0000, 3.00, 0.00, 177.00, '2026-09-28 17:04:27', '2026-09-28 17:04:27'),
(70, 57, 20, 1.0000, 0.0000, 0.0001, 150.0000, 100.0000, 'retail', 'fixed', 2.0000, 2.00, 0.00, 148.00, '2026-09-28 17:09:44', '2026-09-28 17:09:44'),
(71, 58, 13, 1.0000, 0.0000, 2.0000, 345.0000, 100.0000, 'retail', 'fixed', 0.0000, 0.00, 0.00, 345.00, '2026-09-28 20:35:19', '2026-09-28 20:35:19'),
(72, 59, 2, 1.0000, 0.0000, NULL, 180.0000, 100.0000, 'retail', 'fixed', 0.0000, 0.00, 0.00, 180.00, '2026-09-28 20:40:30', '2026-09-28 20:40:30'),
(73, 60, 30, 100.0000, 0.0000, 10.0000, 300.0000, 100.0000, 'retail', 'fixed', 0.0000, 0.00, 0.00, 30000.00, '2026-09-28 21:38:29', '2026-09-28 21:38:29'),
(74, 60, 9, 20.0000, 0.0000, 20.0000, 285.0000, 100.0000, 'retail', 'fixed', 0.0000, 0.00, 0.00, 5700.00, '2026-09-28 21:38:29', '2026-09-28 21:38:29'),
(75, 61, 13, 1.0000, 0.0000, NULL, 345.0000, 100.0000, 'retail', 'fixed', 0.0000, 0.00, 0.00, 345.00, '2026-09-29 14:03:10', '2026-09-29 14:03:10');

-- --------------------------------------------------------

--
-- Table structure for table `sale_returns`
--

CREATE TABLE `sale_returns` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `return_no` varchar(50) NOT NULL,
  `sale_id` bigint(20) UNSIGNED DEFAULT NULL,
  `customer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `warehouse_id` bigint(20) UNSIGNED NOT NULL,
  `return_date` date NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'draft',
  `subtotal` decimal(18,2) NOT NULL DEFAULT 0.00,
  `tax` decimal(18,2) NOT NULL DEFAULT 0.00,
  `grand_total` decimal(18,2) NOT NULL DEFAULT 0.00,
  `reason` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `posted_by` bigint(20) UNSIGNED DEFAULT NULL,
  `posted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sale_returns`
--

INSERT INTO `sale_returns` (`id`, `return_no`, `sale_id`, `customer_id`, `warehouse_id`, `return_date`, `status`, `subtotal`, `tax`, `grand_total`, `reason`, `created_by`, `posted_by`, `posted_at`, `created_at`, `updated_at`) VALUES
(1, 'SRET-000001', 38, 39, 1, '2026-08-26', 'posted', 3500.00, 0.00, 3500.00, 'Customer return', 1, 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(2, 'SRT-2026-000002', NULL, NULL, 1, '2026-09-25', 'draft', 285.00, 0.00, 285.00, NULL, 1, NULL, NULL, '2026-09-25 15:49:24', '2026-09-25 15:49:24'),
(3, 'SRT-2026-000003', 1, 4, 2, '2026-09-25', 'posted', 240.02, 0.00, 240.02, NULL, 1, 1, '2026-09-26 19:20:21', '2026-09-26 19:19:22', '2026-09-26 19:20:21');

-- --------------------------------------------------------

--
-- Table structure for table `sale_return_items`
--

CREATE TABLE `sale_return_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sale_return_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` decimal(18,4) NOT NULL,
  `unit_price` decimal(18,4) NOT NULL,
  `cost_price` decimal(18,4) NOT NULL DEFAULT 0.0000,
  `line_total` decimal(18,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sale_return_items`
--

INSERT INTO `sale_return_items` (`id`, `sale_return_id`, `product_id`, `quantity`, `unit_price`, `cost_price`, `line_total`, `created_at`, `updated_at`) VALUES
(1, 1, 5, 1.0000, 3500.0000, 2100.0000, 3500.00, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(2, 2, 9, 1.0000, 285.0000, 100.0000, 285.00, '2026-09-25 15:49:24', '2026-09-25 15:49:24'),
(3, 3, 6, 1.0001, 240.0000, 100.0000, 240.02, '2026-09-26 19:19:22', '2026-09-26 19:19:22');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `key` varchar(150) NOT NULL,
  `value` text DEFAULT NULL,
  `group` varchar(80) NOT NULL DEFAULT 'general',
  `type` varchar(30) NOT NULL DEFAULT 'string',
  `is_public` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `key`, `value`, `group`, `type`, `is_public`, `created_at`, `updated_at`) VALUES
(1, 'company_name', 'MS Afrin Traders', 'general', 'string', 1, '2026-08-27 18:00:00', '2026-09-23 17:41:10'),
(2, 'company_phone', '01932-223333, 01970-479940, 01916-223333', 'general', 'string', 1, '2026-08-27 18:00:00', '2026-09-28 10:46:46'),
(3, 'company_email', NULL, 'general', 'string', 1, '2026-08-27 18:00:00', '2026-09-23 17:41:10'),
(4, 'company_address', 'Abdul Molla Super Market, Baluchar Bazar (under Dutch-Bangla Bank), Sirajdikhan, Munshiganj', 'general', 'string', 1, '2026-08-27 18:00:00', '2026-09-28 10:46:58'),
(5, 'currency', 'BDT', 'general', 'string', 1, '2026-08-27 18:00:00', '2026-09-23 17:41:10'),
(6, 'date_format', 'Y-m-d', 'general', 'string', 1, '2026-08-27 18:00:00', '2026-09-23 17:41:10'),
(7, 'timezone', 'Asia/Dhaka', 'general', 'string', 0, '2026-08-27 18:00:00', '2026-09-23 17:41:10'),
(8, 'invoice_prefix', 'SAL', 'document', 'string', 0, '2026-08-27 18:00:00', '2026-09-23 17:41:10'),
(9, 'purchase_prefix', 'PUR', 'document', 'string', 0, '2026-08-27 18:00:00', '2026-09-23 17:41:10'),
(10, 'allow_negative_stock', '0', 'inventory', 'boolean', 0, '2026-08-27 18:00:00', '2026-09-23 17:41:10'),
(11, 'low_stock_notifications', '1', 'inventory', 'boolean', 0, '2026-08-27 18:00:00', '2026-09-23 17:41:10'),
(12, 'company_name_bn', 'মেসার্স আফরিন ট্রেডার্স', 'memo', 'string', 1, '2026-09-28 10:46:17', '2026-09-28 10:46:17'),
(13, 'company_owner', 'Shamsul Molla', 'memo', 'string', 1, '2026-09-28 10:46:17', '2026-09-28 10:46:17'),
(14, 'company_owner_bn', 'শামছুল মোল্লা', 'memo', 'string', 1, '2026-09-28 10:46:17', '2026-09-28 10:46:17'),
(15, 'company_address_bn', 'আব্দুল মোল্লা সুপার মার্কেট, বালুচর বাজার, (ডাচ বাংলা ব্যাংকের নিচে) সিরাজদিখান, মুন্সীগঞ্জ', 'memo', 'string', 1, '2026-09-28 10:46:17', '2026-09-28 10:46:17'),
(16, 'memo_business_description_en', 'Dealer and retailer of TMT rod, angle, flat bar, deformed bar, binding wire, chemical, bitumen, cement and construction materials.', 'memo', 'string', 1, '2026-09-28 10:46:17', '2026-09-28 10:46:17'),
(17, 'memo_business_description_bn', 'TMT 400/500/600 গ্রেডের রড, এঙ্গেল, ফ্লাটবার, ডিফর্ম বার, বাইন্ডিং তার, কেমিকেল, বিটুমিন, সিমেন্ট ও নির্মাণ সামগ্রীর পাইকারী ও খুচরা বিক্রেতা।', 'memo', 'string', 1, '2026-09-28 10:46:17', '2026-09-28 10:46:17'),
(18, 'memo_footer_en', 'Digital weighing is available here.', 'memo', 'string', 1, '2026-09-28 10:46:17', '2026-09-28 10:46:17'),
(19, 'memo_footer_bn', 'এখানে ডিজিটাল পদ্ধতিতে ওজন দেওয়া হয়।', 'memo', 'string', 1, '2026-09-28 10:46:17', '2026-09-28 10:46:17'),
(20, 'maker_checker_enabled', '0', 'controls', 'boolean', 0, '2026-10-23 06:20:12', '2026-10-23 06:20:12'),
(21, 'credit_limit_enforced', '1', 'controls', 'boolean', 0, '2026-10-23 06:20:12', '2026-10-23 06:20:12'),
(22, 'auto_payment_terms', '1', 'controls', 'boolean', 0, '2026-10-23 06:20:12', '2026-10-23 06:20:12'),
(23, 'auto_product_tax', '1', 'controls', 'boolean', 0, '2026-10-23 06:20:12', '2026-10-23 06:20:12');

-- --------------------------------------------------------

--
-- Table structure for table `stock_adjustments`
--

CREATE TABLE `stock_adjustments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `adjustment_no` varchar(50) NOT NULL,
  `warehouse_id` bigint(20) UNSIGNED NOT NULL,
  `adjustment_date` date NOT NULL,
  `adjustment_type` varchar(30) NOT NULL,
  `status` varchar(30) NOT NULL DEFAULT 'draft',
  `reason` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `posted_by` bigint(20) UNSIGNED DEFAULT NULL,
  `posted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `stock_adjustments`
--

INSERT INTO `stock_adjustments` (`id`, `adjustment_no`, `warehouse_id`, `adjustment_date`, `adjustment_type`, `status`, `reason`, `notes`, `created_by`, `posted_by`, `posted_at`, `created_at`, `updated_at`) VALUES
(1, 'ADJ-000001', 1, '2026-08-21', 'decrease', 'posted', 'Damaged during handling', 'Demo adjustment', 1, 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(2, 'ADJ-000002', 1, '2026-08-23', 'increase', 'posted', 'Stock count correction', 'Demo adjustment', 1, 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00');

-- --------------------------------------------------------

--
-- Table structure for table `stock_adjustment_items`
--

CREATE TABLE `stock_adjustment_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `stock_adjustment_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` decimal(18,4) NOT NULL,
  `unit_cost` decimal(18,4) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `stock_adjustment_items`
--

INSERT INTO `stock_adjustment_items` (`id`, `stock_adjustment_id`, `product_id`, `quantity`, `unit_cost`, `remarks`, `created_at`, `updated_at`) VALUES
(1, 1, 15, 2.0000, 100.0000, 'Damaged', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(2, 2, 35, 5.0000, 100.0000, 'Found during stock count', '2026-08-28 03:30:00', '2026-08-28 03:30:00');

-- --------------------------------------------------------

--
-- Table structure for table `stock_movements`
--

CREATE TABLE `stock_movements` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `warehouse_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `transaction_date` date NOT NULL,
  `movement_type` varchar(50) NOT NULL,
  `reference_no` varchar(100) DEFAULT NULL,
  `source_type` varchar(50) DEFAULT NULL,
  `source_id` bigint(20) UNSIGNED DEFAULT NULL,
  `quantity_in` decimal(18,4) NOT NULL DEFAULT 0.0000,
  `quantity_out` decimal(18,4) NOT NULL DEFAULT 0.0000,
  `balance_after` decimal(18,4) NOT NULL,
  `unit_cost` decimal(18,4) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `stock_movements`
--

INSERT INTO `stock_movements` (`id`, `warehouse_id`, `product_id`, `transaction_date`, `movement_type`, `reference_no`, `source_type`, `source_id`, `quantity_in`, `quantity_out`, `balance_after`, `unit_cost`, `description`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '2026-03-01', 'opening', 'OPEN-01-000001', 'opening_stock', NULL, 2.0000, 0.0000, 2.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(2, 1, 2, '2026-03-01', 'opening', 'OPEN-01-000002', 'opening_stock', NULL, 2.0000, 0.0000, 2.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(3, 1, 3, '2026-03-01', 'opening', 'OPEN-01-000003', 'opening_stock', NULL, 2.0000, 0.0000, 2.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(4, 1, 4, '2026-03-01', 'opening', 'OPEN-01-000004', 'opening_stock', NULL, 2.0000, 0.0000, 2.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(5, 1, 5, '2026-03-01', 'opening', 'OPEN-01-000005', 'opening_stock', NULL, 2.0000, 0.0000, 2.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(6, 1, 6, '2026-03-01', 'opening', 'OPEN-01-000006', 'opening_stock', NULL, 2.0000, 0.0000, 2.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(7, 1, 7, '2026-03-01', 'opening', 'OPEN-01-000007', 'opening_stock', NULL, 2.0000, 0.0000, 2.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(8, 1, 8, '2026-03-01', 'opening', 'OPEN-01-000008', 'opening_stock', NULL, 2.0000, 0.0000, 2.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(9, 1, 9, '2026-03-01', 'opening', 'OPEN-01-000009', 'opening_stock', NULL, 2.0000, 0.0000, 2.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(10, 1, 10, '2026-03-01', 'opening', 'OPEN-01-000010', 'opening_stock', NULL, 2.0000, 0.0000, 2.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(11, 1, 11, '2026-03-01', 'opening', 'OPEN-01-000011', 'opening_stock', NULL, 2.0000, 0.0000, 2.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(12, 1, 12, '2026-03-01', 'opening', 'OPEN-01-000012', 'opening_stock', NULL, 2.0000, 0.0000, 2.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(13, 1, 13, '2026-03-01', 'opening', 'OPEN-01-000013', 'opening_stock', NULL, 2.0000, 0.0000, 2.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(14, 1, 14, '2026-03-01', 'opening', 'OPEN-01-000014', 'opening_stock', NULL, 2.0000, 0.0000, 2.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(15, 1, 15, '2026-03-01', 'opening', 'OPEN-01-000015', 'opening_stock', NULL, 2.0000, 0.0000, 2.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(16, 1, 16, '2026-03-01', 'opening', 'OPEN-01-000016', 'opening_stock', NULL, 2.0000, 0.0000, 2.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(17, 1, 17, '2026-03-01', 'opening', 'OPEN-01-000017', 'opening_stock', NULL, 2.0000, 0.0000, 2.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(18, 1, 18, '2026-03-01', 'opening', 'OPEN-01-000018', 'opening_stock', NULL, 2.0000, 0.0000, 2.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(19, 1, 19, '2026-03-01', 'opening', 'OPEN-01-000019', 'opening_stock', NULL, 2.0000, 0.0000, 2.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(20, 1, 20, '2026-03-01', 'opening', 'OPEN-01-000020', 'opening_stock', NULL, 2.0000, 0.0000, 2.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(21, 1, 21, '2026-03-01', 'opening', 'OPEN-01-000021', 'opening_stock', NULL, 2.0000, 0.0000, 2.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(22, 1, 22, '2026-03-01', 'opening', 'OPEN-01-000022', 'opening_stock', NULL, 2.0000, 0.0000, 2.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(23, 1, 23, '2026-03-01', 'opening', 'OPEN-01-000023', 'opening_stock', NULL, 2.0000, 0.0000, 2.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(24, 1, 24, '2026-03-01', 'opening', 'OPEN-01-000024', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(25, 1, 25, '2026-03-01', 'opening', 'OPEN-01-000025', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(26, 1, 26, '2026-03-01', 'opening', 'OPEN-01-000026', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(27, 1, 27, '2026-03-01', 'opening', 'OPEN-01-000027', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(28, 1, 28, '2026-03-01', 'opening', 'OPEN-01-000028', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(29, 1, 29, '2026-03-01', 'opening', 'OPEN-01-000029', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(30, 1, 30, '2026-03-01', 'opening', 'OPEN-01-000030', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(31, 1, 31, '2026-03-01', 'opening', 'OPEN-01-000031', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(32, 1, 32, '2026-03-01', 'opening', 'OPEN-01-000032', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(33, 1, 33, '2026-03-01', 'opening', 'OPEN-01-000033', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(34, 1, 34, '2026-03-01', 'opening', 'OPEN-01-000034', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(35, 1, 35, '2026-03-01', 'opening', 'OPEN-01-000035', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(36, 1, 36, '2026-03-01', 'opening', 'OPEN-01-000036', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(37, 1, 37, '2026-03-01', 'opening', 'OPEN-01-000037', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(38, 1, 38, '2026-03-01', 'opening', 'OPEN-01-000038', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(39, 1, 39, '2026-03-01', 'opening', 'OPEN-01-000039', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(40, 1, 40, '2026-03-01', 'opening', 'OPEN-01-000040', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(41, 1, 41, '2026-03-01', 'opening', 'OPEN-01-000041', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(42, 1, 42, '2026-03-01', 'opening', 'OPEN-01-000042', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(43, 1, 43, '2026-03-01', 'opening', 'OPEN-01-000043', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(44, 1, 44, '2026-03-01', 'opening', 'OPEN-01-000044', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(45, 1, 45, '2026-03-01', 'opening', 'OPEN-01-000045', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(46, 1, 46, '2026-03-01', 'opening', 'OPEN-01-000046', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(47, 1, 47, '2026-03-01', 'opening', 'OPEN-01-000047', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(48, 1, 48, '2026-03-01', 'opening', 'OPEN-01-000048', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(49, 1, 49, '2026-03-01', 'opening', 'OPEN-01-000049', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(50, 1, 50, '2026-03-01', 'opening', 'OPEN-01-000050', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(51, 1, 51, '2026-03-01', 'opening', 'OPEN-01-000051', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(52, 1, 52, '2026-03-01', 'opening', 'OPEN-01-000052', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(53, 1, 53, '2026-03-01', 'opening', 'OPEN-01-000053', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(54, 1, 54, '2026-03-01', 'opening', 'OPEN-01-000054', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(55, 1, 55, '2026-03-01', 'opening', 'OPEN-01-000055', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(56, 1, 56, '2026-03-01', 'opening', 'OPEN-01-000056', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(57, 1, 57, '2026-03-01', 'opening', 'OPEN-01-000057', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(58, 1, 58, '2026-03-01', 'opening', 'OPEN-01-000058', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(59, 1, 59, '2026-03-01', 'opening', 'OPEN-01-000059', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(60, 1, 60, '2026-03-01', 'opening', 'OPEN-01-000060', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(61, 1, 61, '2026-03-01', 'opening', 'OPEN-01-000061', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(62, 1, 62, '2026-03-01', 'opening', 'OPEN-01-000062', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(63, 1, 63, '2026-03-01', 'opening', 'OPEN-01-000063', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(64, 1, 64, '2026-03-01', 'opening', 'OPEN-01-000064', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(65, 1, 65, '2026-03-01', 'opening', 'OPEN-01-000065', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(66, 1, 66, '2026-03-01', 'opening', 'OPEN-01-000066', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(67, 1, 67, '2026-03-01', 'opening', 'OPEN-01-000067', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(68, 1, 68, '2026-03-01', 'opening', 'OPEN-01-000068', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(69, 1, 69, '2026-03-01', 'opening', 'OPEN-01-000069', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(70, 1, 70, '2026-03-01', 'opening', 'OPEN-01-000070', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(71, 1, 71, '2026-03-01', 'opening', 'OPEN-01-000071', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(72, 1, 72, '2026-03-01', 'opening', 'OPEN-01-000072', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(73, 1, 73, '2026-03-01', 'opening', 'OPEN-01-000073', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(74, 1, 74, '2026-03-01', 'opening', 'OPEN-01-000074', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(75, 1, 75, '2026-03-01', 'opening', 'OPEN-01-000075', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(76, 1, 76, '2026-03-01', 'opening', 'OPEN-01-000076', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(77, 1, 77, '2026-03-01', 'opening', 'OPEN-01-000077', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(78, 1, 78, '2026-03-01', 'opening', 'OPEN-01-000078', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(79, 1, 79, '2026-03-01', 'opening', 'OPEN-01-000079', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(80, 1, 80, '2026-03-01', 'opening', 'OPEN-01-000080', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(81, 1, 81, '2026-03-01', 'opening', 'OPEN-01-000081', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(82, 1, 82, '2026-03-01', 'opening', 'OPEN-01-000082', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(83, 1, 83, '2026-03-01', 'opening', 'OPEN-01-000083', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(84, 1, 84, '2026-03-01', 'opening', 'OPEN-01-000084', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(85, 1, 85, '2026-03-01', 'opening', 'OPEN-01-000085', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(86, 1, 86, '2026-03-01', 'opening', 'OPEN-01-000086', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(87, 1, 87, '2026-03-01', 'opening', 'OPEN-01-000087', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(88, 1, 88, '2026-03-01', 'opening', 'OPEN-01-000088', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(89, 1, 89, '2026-03-01', 'opening', 'OPEN-01-000089', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(90, 1, 90, '2026-03-01', 'opening', 'OPEN-01-000090', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(91, 1, 91, '2026-03-01', 'opening', 'OPEN-01-000091', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(92, 1, 92, '2026-03-01', 'opening', 'OPEN-01-000092', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(93, 1, 93, '2026-03-01', 'opening', 'OPEN-01-000093', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(94, 1, 94, '2026-03-01', 'opening', 'OPEN-01-000094', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(95, 1, 95, '2026-03-01', 'opening', 'OPEN-01-000095', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(96, 1, 96, '2026-03-01', 'opening', 'OPEN-01-000096', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(97, 1, 97, '2026-03-01', 'opening', 'OPEN-01-000097', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(98, 1, 98, '2026-03-01', 'opening', 'OPEN-01-000098', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(99, 1, 99, '2026-03-01', 'opening', 'OPEN-01-000099', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(100, 1, 100, '2026-03-01', 'opening', 'OPEN-01-000100', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(101, 1, 101, '2026-03-01', 'opening', 'OPEN-01-000101', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(102, 1, 102, '2026-03-01', 'opening', 'OPEN-01-000102', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(103, 1, 103, '2026-03-01', 'opening', 'OPEN-01-000103', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(104, 1, 104, '2026-03-01', 'opening', 'OPEN-01-000104', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(105, 1, 105, '2026-03-01', 'opening', 'OPEN-01-000105', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(106, 1, 106, '2026-03-01', 'opening', 'OPEN-01-000106', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(107, 1, 107, '2026-03-01', 'opening', 'OPEN-01-000107', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(108, 1, 108, '2026-03-01', 'opening', 'OPEN-01-000108', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(109, 1, 109, '2026-03-01', 'opening', 'OPEN-01-000109', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(110, 1, 110, '2026-03-01', 'opening', 'OPEN-01-000110', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(111, 1, 111, '2026-03-01', 'opening', 'OPEN-01-000111', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(112, 1, 112, '2026-03-01', 'opening', 'OPEN-01-000112', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(113, 1, 113, '2026-03-01', 'opening', 'OPEN-01-000113', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(114, 1, 114, '2026-03-01', 'opening', 'OPEN-01-000114', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(115, 1, 115, '2026-03-01', 'opening', 'OPEN-01-000115', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(116, 1, 116, '2026-03-01', 'opening', 'OPEN-01-000116', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(117, 1, 117, '2026-03-01', 'opening', 'OPEN-01-000117', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(118, 1, 118, '2026-03-01', 'opening', 'OPEN-01-000118', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(119, 1, 119, '2026-03-01', 'opening', 'OPEN-01-000119', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(120, 1, 120, '2026-03-01', 'opening', 'OPEN-01-000120', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(121, 1, 121, '2026-03-01', 'opening', 'OPEN-01-000121', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(122, 1, 122, '2026-03-01', 'opening', 'OPEN-01-000122', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(123, 1, 123, '2026-03-01', 'opening', 'OPEN-01-000123', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(124, 1, 124, '2026-03-01', 'opening', 'OPEN-01-000124', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(125, 1, 125, '2026-03-01', 'opening', 'OPEN-01-000125', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(126, 1, 126, '2026-03-01', 'opening', 'OPEN-01-000126', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(127, 1, 127, '2026-03-01', 'opening', 'OPEN-01-000127', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(128, 1, 128, '2026-03-01', 'opening', 'OPEN-01-000128', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(129, 1, 129, '2026-03-01', 'opening', 'OPEN-01-000129', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(130, 1, 130, '2026-03-01', 'opening', 'OPEN-01-000130', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(131, 1, 131, '2026-03-01', 'opening', 'OPEN-01-000131', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(132, 1, 132, '2026-03-01', 'opening', 'OPEN-01-000132', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(133, 1, 133, '2026-03-01', 'opening', 'OPEN-01-000133', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(134, 1, 134, '2026-03-01', 'opening', 'OPEN-01-000134', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(135, 1, 135, '2026-03-01', 'opening', 'OPEN-01-000135', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(136, 1, 136, '2026-03-01', 'opening', 'OPEN-01-000136', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(137, 1, 137, '2026-03-01', 'opening', 'OPEN-01-000137', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(138, 1, 138, '2026-03-01', 'opening', 'OPEN-01-000138', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(139, 1, 139, '2026-03-01', 'opening', 'OPEN-01-000139', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(140, 1, 140, '2026-03-01', 'opening', 'OPEN-01-000140', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(141, 1, 141, '2026-03-01', 'opening', 'OPEN-01-000141', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(142, 1, 142, '2026-03-01', 'opening', 'OPEN-01-000142', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(143, 1, 143, '2026-03-01', 'opening', 'OPEN-01-000143', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(144, 1, 144, '2026-03-01', 'opening', 'OPEN-01-000144', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(145, 1, 145, '2026-03-01', 'opening', 'OPEN-01-000145', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(146, 1, 146, '2026-03-01', 'opening', 'OPEN-01-000146', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(147, 1, 147, '2026-03-01', 'opening', 'OPEN-01-000147', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(148, 1, 148, '2026-03-01', 'opening', 'OPEN-01-000148', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(149, 1, 149, '2026-03-01', 'opening', 'OPEN-01-000149', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(150, 1, 150, '2026-03-01', 'opening', 'OPEN-01-000150', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(151, 1, 151, '2026-03-01', 'opening', 'OPEN-01-000151', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(152, 1, 152, '2026-03-01', 'opening', 'OPEN-01-000152', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(153, 1, 153, '2026-03-01', 'opening', 'OPEN-01-000153', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(154, 1, 154, '2026-03-01', 'opening', 'OPEN-01-000154', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(155, 1, 155, '2026-03-01', 'opening', 'OPEN-01-000155', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(156, 1, 156, '2026-03-01', 'opening', 'OPEN-01-000156', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(157, 1, 157, '2026-03-01', 'opening', 'OPEN-01-000157', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(158, 1, 158, '2026-03-01', 'opening', 'OPEN-01-000158', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(159, 1, 159, '2026-03-01', 'opening', 'OPEN-01-000159', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(160, 1, 160, '2026-03-01', 'opening', 'OPEN-01-000160', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(161, 1, 161, '2026-03-01', 'opening', 'OPEN-01-000161', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(162, 1, 162, '2026-03-01', 'opening', 'OPEN-01-000162', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(163, 1, 163, '2026-03-01', 'opening', 'OPEN-01-000163', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(164, 1, 164, '2026-03-01', 'opening', 'OPEN-01-000164', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(165, 1, 165, '2026-03-01', 'opening', 'OPEN-01-000165', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(166, 1, 166, '2026-03-01', 'opening', 'OPEN-01-000166', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(167, 1, 167, '2026-03-01', 'opening', 'OPEN-01-000167', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(168, 1, 168, '2026-03-01', 'opening', 'OPEN-01-000168', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(169, 1, 169, '2026-03-01', 'opening', 'OPEN-01-000169', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(170, 1, 170, '2026-03-01', 'opening', 'OPEN-01-000170', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(171, 1, 171, '2026-03-01', 'opening', 'OPEN-01-000171', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(172, 1, 172, '2026-03-01', 'opening', 'OPEN-01-000172', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(173, 1, 173, '2026-03-01', 'opening', 'OPEN-01-000173', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(174, 1, 174, '2026-03-01', 'opening', 'OPEN-01-000174', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(175, 1, 175, '2026-03-01', 'opening', 'OPEN-01-000175', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(176, 1, 176, '2026-03-01', 'opening', 'OPEN-01-000176', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(177, 1, 177, '2026-03-01', 'opening', 'OPEN-01-000177', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(178, 1, 178, '2026-03-01', 'opening', 'OPEN-01-000178', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(179, 1, 179, '2026-03-01', 'opening', 'OPEN-01-000179', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(180, 1, 180, '2026-03-01', 'opening', 'OPEN-01-000180', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(181, 1, 181, '2026-03-01', 'opening', 'OPEN-01-000181', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(182, 1, 182, '2026-03-01', 'opening', 'OPEN-01-000182', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(183, 1, 183, '2026-03-01', 'opening', 'OPEN-01-000183', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(184, 1, 184, '2026-03-01', 'opening', 'OPEN-01-000184', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(185, 1, 185, '2026-03-01', 'opening', 'OPEN-01-000185', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(186, 1, 186, '2026-03-01', 'opening', 'OPEN-01-000186', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(187, 1, 187, '2026-03-01', 'opening', 'OPEN-01-000187', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(188, 1, 188, '2026-03-01', 'opening', 'OPEN-01-000188', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(189, 1, 189, '2026-03-01', 'opening', 'OPEN-01-000189', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(190, 1, 190, '2026-03-01', 'opening', 'OPEN-01-000190', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(191, 1, 191, '2026-03-01', 'opening', 'OPEN-01-000191', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(192, 1, 192, '2026-03-01', 'opening', 'OPEN-01-000192', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(193, 1, 193, '2026-03-01', 'opening', 'OPEN-01-000193', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(194, 1, 194, '2026-03-01', 'opening', 'OPEN-01-000194', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(195, 1, 195, '2026-03-01', 'opening', 'OPEN-01-000195', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(196, 1, 196, '2026-03-01', 'opening', 'OPEN-01-000196', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(197, 1, 197, '2026-03-01', 'opening', 'OPEN-01-000197', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(198, 1, 198, '2026-03-01', 'opening', 'OPEN-01-000198', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(199, 1, 199, '2026-03-01', 'opening', 'OPEN-01-000199', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(200, 1, 200, '2026-03-01', 'opening', 'OPEN-01-000200', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(201, 1, 201, '2026-03-01', 'opening', 'OPEN-01-000201', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(202, 1, 202, '2026-03-01', 'opening', 'OPEN-01-000202', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(203, 1, 203, '2026-03-01', 'opening', 'OPEN-01-000203', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(204, 1, 204, '2026-03-01', 'opening', 'OPEN-01-000204', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(205, 1, 205, '2026-03-01', 'opening', 'OPEN-01-000205', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(206, 1, 206, '2026-03-01', 'opening', 'OPEN-01-000206', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(207, 1, 207, '2026-03-01', 'opening', 'OPEN-01-000207', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(208, 1, 208, '2026-03-01', 'opening', 'OPEN-01-000208', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(209, 1, 209, '2026-03-01', 'opening', 'OPEN-01-000209', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(210, 1, 210, '2026-03-01', 'opening', 'OPEN-01-000210', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(211, 1, 211, '2026-03-01', 'opening', 'OPEN-01-000211', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(212, 1, 212, '2026-03-01', 'opening', 'OPEN-01-000212', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(213, 1, 213, '2026-03-01', 'opening', 'OPEN-01-000213', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(214, 1, 214, '2026-03-01', 'opening', 'OPEN-01-000214', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(215, 1, 215, '2026-03-01', 'opening', 'OPEN-01-000215', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(216, 1, 216, '2026-03-01', 'opening', 'OPEN-01-000216', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(217, 1, 217, '2026-03-01', 'opening', 'OPEN-01-000217', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(218, 1, 218, '2026-03-01', 'opening', 'OPEN-01-000218', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(219, 1, 219, '2026-03-01', 'opening', 'OPEN-01-000219', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(220, 1, 220, '2026-03-01', 'opening', 'OPEN-01-000220', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(221, 1, 221, '2026-03-01', 'opening', 'OPEN-01-000221', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(222, 1, 222, '2026-03-01', 'opening', 'OPEN-01-000222', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(223, 1, 223, '2026-03-01', 'opening', 'OPEN-01-000223', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(224, 1, 224, '2026-03-01', 'opening', 'OPEN-01-000224', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(225, 1, 225, '2026-03-01', 'opening', 'OPEN-01-000225', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(226, 1, 226, '2026-03-01', 'opening', 'OPEN-01-000226', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(227, 1, 227, '2026-03-01', 'opening', 'OPEN-01-000227', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(228, 1, 228, '2026-03-01', 'opening', 'OPEN-01-000228', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(229, 1, 229, '2026-03-01', 'opening', 'OPEN-01-000229', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(230, 1, 230, '2026-03-01', 'opening', 'OPEN-01-000230', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(231, 1, 231, '2026-03-01', 'opening', 'OPEN-01-000231', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(232, 1, 232, '2026-03-01', 'opening', 'OPEN-01-000232', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(233, 1, 233, '2026-03-01', 'opening', 'OPEN-01-000233', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(234, 1, 234, '2026-03-01', 'opening', 'OPEN-01-000234', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(235, 1, 235, '2026-03-01', 'opening', 'OPEN-01-000235', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(236, 1, 236, '2026-03-01', 'opening', 'OPEN-01-000236', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(237, 1, 237, '2026-03-01', 'opening', 'OPEN-01-000237', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(238, 1, 238, '2026-03-01', 'opening', 'OPEN-01-000238', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(239, 1, 239, '2026-03-01', 'opening', 'OPEN-01-000239', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(240, 1, 240, '2026-03-01', 'opening', 'OPEN-01-000240', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(241, 1, 241, '2026-03-01', 'opening', 'OPEN-01-000241', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(242, 1, 242, '2026-03-01', 'opening', 'OPEN-01-000242', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(243, 1, 243, '2026-03-01', 'opening', 'OPEN-01-000243', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(244, 1, 244, '2026-03-01', 'opening', 'OPEN-01-000244', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(245, 1, 245, '2026-03-01', 'opening', 'OPEN-01-000245', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(246, 1, 246, '2026-03-01', 'opening', 'OPEN-01-000246', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(247, 1, 247, '2026-03-01', 'opening', 'OPEN-01-000247', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(248, 1, 248, '2026-03-01', 'opening', 'OPEN-01-000248', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(249, 1, 249, '2026-03-01', 'opening', 'OPEN-01-000249', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(250, 1, 250, '2026-03-01', 'opening', 'OPEN-01-000250', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(251, 1, 251, '2026-03-01', 'opening', 'OPEN-01-000251', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(252, 1, 252, '2026-03-01', 'opening', 'OPEN-01-000252', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(253, 1, 253, '2026-03-01', 'opening', 'OPEN-01-000253', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(254, 1, 254, '2026-03-01', 'opening', 'OPEN-01-000254', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(255, 1, 255, '2026-03-01', 'opening', 'OPEN-01-000255', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(256, 1, 256, '2026-03-01', 'opening', 'OPEN-01-000256', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(257, 1, 257, '2026-03-01', 'opening', 'OPEN-01-000257', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(258, 1, 258, '2026-03-01', 'opening', 'OPEN-01-000258', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(259, 1, 259, '2026-03-01', 'opening', 'OPEN-01-000259', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(260, 1, 260, '2026-03-01', 'opening', 'OPEN-01-000260', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(261, 1, 261, '2026-03-01', 'opening', 'OPEN-01-000261', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(262, 1, 262, '2026-03-01', 'opening', 'OPEN-01-000262', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(263, 1, 263, '2026-03-01', 'opening', 'OPEN-01-000263', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(264, 1, 264, '2026-03-01', 'opening', 'OPEN-01-000264', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(265, 1, 265, '2026-03-01', 'opening', 'OPEN-01-000265', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(266, 1, 266, '2026-03-01', 'opening', 'OPEN-01-000266', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(267, 1, 267, '2026-03-01', 'opening', 'OPEN-01-000267', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(268, 1, 268, '2026-03-01', 'opening', 'OPEN-01-000268', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00');
INSERT INTO `stock_movements` (`id`, `warehouse_id`, `product_id`, `transaction_date`, `movement_type`, `reference_no`, `source_type`, `source_id`, `quantity_in`, `quantity_out`, `balance_after`, `unit_cost`, `description`, `created_by`, `created_at`, `updated_at`) VALUES
(269, 1, 269, '2026-03-01', 'opening', 'OPEN-01-000269', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(270, 1, 270, '2026-03-01', 'opening', 'OPEN-01-000270', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(271, 1, 271, '2026-03-01', 'opening', 'OPEN-01-000271', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(272, 1, 272, '2026-03-01', 'opening', 'OPEN-01-000272', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(273, 1, 273, '2026-03-01', 'opening', 'OPEN-01-000273', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(274, 1, 274, '2026-03-01', 'opening', 'OPEN-01-000274', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(275, 1, 275, '2026-03-01', 'opening', 'OPEN-01-000275', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(276, 1, 276, '2026-03-01', 'opening', 'OPEN-01-000276', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(277, 1, 277, '2026-03-01', 'opening', 'OPEN-01-000277', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(278, 1, 278, '2026-03-01', 'opening', 'OPEN-01-000278', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(279, 1, 279, '2026-03-01', 'opening', 'OPEN-01-000279', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(280, 1, 280, '2026-03-01', 'opening', 'OPEN-01-000280', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(281, 1, 281, '2026-03-01', 'opening', 'OPEN-01-000281', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(282, 1, 282, '2026-03-01', 'opening', 'OPEN-01-000282', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(283, 1, 283, '2026-03-01', 'opening', 'OPEN-01-000283', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(284, 1, 284, '2026-03-01', 'opening', 'OPEN-01-000284', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(285, 1, 285, '2026-03-01', 'opening', 'OPEN-01-000285', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(286, 1, 286, '2026-03-01', 'opening', 'OPEN-01-000286', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(287, 1, 287, '2026-03-01', 'opening', 'OPEN-01-000287', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(288, 1, 288, '2026-03-01', 'opening', 'OPEN-01-000288', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(289, 1, 289, '2026-03-01', 'opening', 'OPEN-01-000289', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(290, 1, 290, '2026-03-01', 'opening', 'OPEN-01-000290', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(291, 1, 291, '2026-03-01', 'opening', 'OPEN-01-000291', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(292, 1, 292, '2026-03-01', 'opening', 'OPEN-01-000292', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(293, 1, 293, '2026-03-01', 'opening', 'OPEN-01-000293', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(294, 1, 294, '2026-03-01', 'opening', 'OPEN-01-000294', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(295, 1, 295, '2026-03-01', 'opening', 'OPEN-01-000295', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(296, 1, 296, '2026-03-01', 'opening', 'OPEN-01-000296', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(297, 1, 297, '2026-03-01', 'opening', 'OPEN-01-000297', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(298, 1, 298, '2026-03-01', 'opening', 'OPEN-01-000298', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(299, 1, 299, '2026-03-01', 'opening', 'OPEN-01-000299', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(300, 1, 300, '2026-03-01', 'opening', 'OPEN-01-000300', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(301, 1, 301, '2026-03-01', 'opening', 'OPEN-01-000301', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(302, 1, 302, '2026-03-01', 'opening', 'OPEN-01-000302', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(303, 1, 303, '2026-03-01', 'opening', 'OPEN-01-000303', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(304, 1, 304, '2026-03-01', 'opening', 'OPEN-01-000304', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(305, 1, 305, '2026-03-01', 'opening', 'OPEN-01-000305', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(306, 1, 306, '2026-03-01', 'opening', 'OPEN-01-000306', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(307, 1, 307, '2026-03-01', 'opening', 'OPEN-01-000307', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(308, 1, 308, '2026-03-01', 'opening', 'OPEN-01-000308', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(309, 1, 309, '2026-03-01', 'opening', 'OPEN-01-000309', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(310, 1, 310, '2026-03-01', 'opening', 'OPEN-01-000310', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(311, 1, 311, '2026-03-01', 'opening', 'OPEN-01-000311', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(312, 1, 312, '2026-03-01', 'opening', 'OPEN-01-000312', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(313, 1, 313, '2026-03-01', 'opening', 'OPEN-01-000313', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(314, 1, 314, '2026-03-01', 'opening', 'OPEN-01-000314', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(315, 1, 315, '2026-03-01', 'opening', 'OPEN-01-000315', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(316, 1, 316, '2026-03-01', 'opening', 'OPEN-01-000316', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(317, 1, 317, '2026-03-01', 'opening', 'OPEN-01-000317', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(318, 1, 318, '2026-03-01', 'opening', 'OPEN-01-000318', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(319, 1, 319, '2026-03-01', 'opening', 'OPEN-01-000319', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(320, 1, 320, '2026-03-01', 'opening', 'OPEN-01-000320', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(321, 1, 321, '2026-03-01', 'opening', 'OPEN-01-000321', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(322, 1, 322, '2026-03-01', 'opening', 'OPEN-01-000322', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(323, 1, 323, '2026-03-01', 'opening', 'OPEN-01-000323', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(324, 1, 324, '2026-03-01', 'opening', 'OPEN-01-000324', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(325, 1, 325, '2026-03-01', 'opening', 'OPEN-01-000325', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(326, 1, 326, '2026-03-01', 'opening', 'OPEN-01-000326', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(327, 1, 327, '2026-03-01', 'opening', 'OPEN-01-000327', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(328, 1, 328, '2026-03-01', 'opening', 'OPEN-01-000328', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(329, 1, 329, '2026-03-01', 'opening', 'OPEN-01-000329', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(330, 1, 330, '2026-03-01', 'opening', 'OPEN-01-000330', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(331, 1, 331, '2026-03-01', 'opening', 'OPEN-01-000331', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(332, 1, 332, '2026-03-01', 'opening', 'OPEN-01-000332', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(333, 1, 333, '2026-03-01', 'opening', 'OPEN-01-000333', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(334, 1, 334, '2026-03-01', 'opening', 'OPEN-01-000334', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(335, 1, 335, '2026-03-01', 'opening', 'OPEN-01-000335', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(336, 1, 336, '2026-03-01', 'opening', 'OPEN-01-000336', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(337, 1, 337, '2026-03-01', 'opening', 'OPEN-01-000337', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(338, 1, 338, '2026-03-01', 'opening', 'OPEN-01-000338', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(339, 1, 339, '2026-03-01', 'opening', 'OPEN-01-000339', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(340, 1, 340, '2026-03-01', 'opening', 'OPEN-01-000340', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(341, 1, 341, '2026-03-01', 'opening', 'OPEN-01-000341', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(342, 1, 342, '2026-03-01', 'opening', 'OPEN-01-000342', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(343, 1, 343, '2026-03-01', 'opening', 'OPEN-01-000343', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(344, 1, 344, '2026-03-01', 'opening', 'OPEN-01-000344', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(345, 1, 345, '2026-03-01', 'opening', 'OPEN-01-000345', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(346, 1, 346, '2026-03-01', 'opening', 'OPEN-01-000346', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(347, 1, 347, '2026-03-01', 'opening', 'OPEN-01-000347', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(348, 1, 348, '2026-03-01', 'opening', 'OPEN-01-000348', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(349, 1, 349, '2026-03-01', 'opening', 'OPEN-01-000349', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(350, 1, 350, '2026-03-01', 'opening', 'OPEN-01-000350', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(351, 1, 351, '2026-03-01', 'opening', 'OPEN-01-000351', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(352, 1, 352, '2026-03-01', 'opening', 'OPEN-01-000352', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(353, 1, 353, '2026-03-01', 'opening', 'OPEN-01-000353', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(354, 1, 354, '2026-03-01', 'opening', 'OPEN-01-000354', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(355, 1, 355, '2026-03-01', 'opening', 'OPEN-01-000355', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(356, 1, 356, '2026-03-01', 'opening', 'OPEN-01-000356', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(357, 1, 357, '2026-03-01', 'opening', 'OPEN-01-000357', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(358, 1, 358, '2026-03-01', 'opening', 'OPEN-01-000358', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(359, 1, 359, '2026-03-01', 'opening', 'OPEN-01-000359', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(360, 1, 360, '2026-03-01', 'opening', 'OPEN-01-000360', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(361, 1, 361, '2026-03-01', 'opening', 'OPEN-01-000361', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(362, 1, 362, '2026-03-01', 'opening', 'OPEN-01-000362', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(363, 1, 363, '2026-03-01', 'opening', 'OPEN-01-000363', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(364, 1, 364, '2026-03-01', 'opening', 'OPEN-01-000364', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(365, 1, 365, '2026-03-01', 'opening', 'OPEN-01-000365', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(366, 1, 366, '2026-03-01', 'opening', 'OPEN-01-000366', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(367, 1, 367, '2026-03-01', 'opening', 'OPEN-01-000367', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(368, 1, 368, '2026-03-01', 'opening', 'OPEN-01-000368', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(369, 1, 369, '2026-03-01', 'opening', 'OPEN-01-000369', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(370, 1, 370, '2026-03-01', 'opening', 'OPEN-01-000370', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(371, 1, 371, '2026-03-01', 'opening', 'OPEN-01-000371', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(372, 1, 372, '2026-03-01', 'opening', 'OPEN-01-000372', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(373, 1, 373, '2026-03-01', 'opening', 'OPEN-01-000373', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(374, 1, 374, '2026-03-01', 'opening', 'OPEN-01-000374', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(375, 1, 375, '2026-03-01', 'opening', 'OPEN-01-000375', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(376, 1, 376, '2026-03-01', 'opening', 'OPEN-01-000376', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(377, 1, 377, '2026-03-01', 'opening', 'OPEN-01-000377', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(378, 1, 378, '2026-03-01', 'opening', 'OPEN-01-000378', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(379, 1, 379, '2026-03-01', 'opening', 'OPEN-01-000379', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(380, 1, 380, '2026-03-01', 'opening', 'OPEN-01-000380', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(381, 1, 381, '2026-03-01', 'opening', 'OPEN-01-000381', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(382, 1, 382, '2026-03-01', 'opening', 'OPEN-01-000382', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(383, 1, 383, '2026-03-01', 'opening', 'OPEN-01-000383', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(384, 1, 384, '2026-03-01', 'opening', 'OPEN-01-000384', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(385, 1, 385, '2026-03-01', 'opening', 'OPEN-01-000385', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(386, 1, 386, '2026-03-01', 'opening', 'OPEN-01-000386', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(387, 1, 387, '2026-03-01', 'opening', 'OPEN-01-000387', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(388, 1, 388, '2026-03-01', 'opening', 'OPEN-01-000388', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(389, 1, 389, '2026-03-01', 'opening', 'OPEN-01-000389', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(390, 1, 390, '2026-03-01', 'opening', 'OPEN-01-000390', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(391, 1, 391, '2026-03-01', 'opening', 'OPEN-01-000391', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(392, 1, 392, '2026-03-01', 'opening', 'OPEN-01-000392', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(393, 1, 393, '2026-03-01', 'opening', 'OPEN-01-000393', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(394, 1, 394, '2026-03-01', 'opening', 'OPEN-01-000394', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(395, 1, 395, '2026-03-01', 'opening', 'OPEN-01-000395', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(396, 1, 396, '2026-03-01', 'opening', 'OPEN-01-000396', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(397, 1, 397, '2026-03-01', 'opening', 'OPEN-01-000397', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(398, 1, 398, '2026-03-01', 'opening', 'OPEN-01-000398', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(399, 1, 399, '2026-03-01', 'opening', 'OPEN-01-000399', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(400, 1, 400, '2026-03-01', 'opening', 'OPEN-01-000400', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(401, 1, 401, '2026-03-01', 'opening', 'OPEN-01-000401', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(402, 1, 402, '2026-03-01', 'opening', 'OPEN-01-000402', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(403, 1, 403, '2026-03-01', 'opening', 'OPEN-01-000403', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(404, 1, 404, '2026-03-01', 'opening', 'OPEN-01-000404', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(405, 1, 405, '2026-03-01', 'opening', 'OPEN-01-000405', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(406, 1, 406, '2026-03-01', 'opening', 'OPEN-01-000406', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(407, 1, 407, '2026-03-01', 'opening', 'OPEN-01-000407', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(408, 1, 408, '2026-03-01', 'opening', 'OPEN-01-000408', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(409, 1, 409, '2026-03-01', 'opening', 'OPEN-01-000409', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(410, 1, 410, '2026-03-01', 'opening', 'OPEN-01-000410', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(411, 1, 411, '2026-03-01', 'opening', 'OPEN-01-000411', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(412, 1, 412, '2026-03-01', 'opening', 'OPEN-01-000412', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(413, 1, 413, '2026-03-01', 'opening', 'OPEN-01-000413', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(414, 1, 414, '2026-03-01', 'opening', 'OPEN-01-000414', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(415, 1, 415, '2026-03-01', 'opening', 'OPEN-01-000415', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(416, 1, 416, '2026-03-01', 'opening', 'OPEN-01-000416', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(417, 1, 417, '2026-03-01', 'opening', 'OPEN-01-000417', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(418, 1, 418, '2026-03-01', 'opening', 'OPEN-01-000418', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(419, 1, 419, '2026-03-01', 'opening', 'OPEN-01-000419', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(420, 1, 420, '2026-03-01', 'opening', 'OPEN-01-000420', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(421, 1, 421, '2026-03-01', 'opening', 'OPEN-01-000421', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(422, 1, 422, '2026-03-01', 'opening', 'OPEN-01-000422', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(423, 1, 423, '2026-03-01', 'opening', 'OPEN-01-000423', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(424, 1, 424, '2026-03-01', 'opening', 'OPEN-01-000424', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(425, 1, 425, '2026-03-01', 'opening', 'OPEN-01-000425', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(426, 1, 426, '2026-03-01', 'opening', 'OPEN-01-000426', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(427, 1, 427, '2026-03-01', 'opening', 'OPEN-01-000427', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(428, 1, 428, '2026-03-01', 'opening', 'OPEN-01-000428', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(429, 1, 429, '2026-03-01', 'opening', 'OPEN-01-000429', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(430, 1, 430, '2026-03-01', 'opening', 'OPEN-01-000430', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(431, 1, 431, '2026-03-01', 'opening', 'OPEN-01-000431', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(432, 1, 432, '2026-03-01', 'opening', 'OPEN-01-000432', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(433, 1, 433, '2026-03-01', 'opening', 'OPEN-01-000433', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(434, 1, 434, '2026-03-01', 'opening', 'OPEN-01-000434', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(435, 1, 435, '2026-03-01', 'opening', 'OPEN-01-000435', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(436, 1, 436, '2026-03-01', 'opening', 'OPEN-01-000436', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(437, 1, 437, '2026-03-01', 'opening', 'OPEN-01-000437', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(438, 1, 438, '2026-03-01', 'opening', 'OPEN-01-000438', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(439, 1, 439, '2026-03-01', 'opening', 'OPEN-01-000439', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(440, 1, 440, '2026-03-01', 'opening', 'OPEN-01-000440', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(441, 1, 441, '2026-03-01', 'opening', 'OPEN-01-000441', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(442, 1, 442, '2026-03-01', 'opening', 'OPEN-01-000442', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(443, 1, 443, '2026-03-01', 'opening', 'OPEN-01-000443', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(444, 1, 444, '2026-03-01', 'opening', 'OPEN-01-000444', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(445, 1, 445, '2026-03-01', 'opening', 'OPEN-01-000445', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(446, 1, 446, '2026-03-01', 'opening', 'OPEN-01-000446', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(447, 1, 447, '2026-03-01', 'opening', 'OPEN-01-000447', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(448, 1, 448, '2026-03-01', 'opening', 'OPEN-01-000448', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(449, 1, 449, '2026-03-01', 'opening', 'OPEN-01-000449', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(450, 1, 450, '2026-03-01', 'opening', 'OPEN-01-000450', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(451, 1, 451, '2026-03-01', 'opening', 'OPEN-01-000451', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(452, 1, 452, '2026-03-01', 'opening', 'OPEN-01-000452', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(453, 1, 453, '2026-03-01', 'opening', 'OPEN-01-000453', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(454, 1, 454, '2026-03-01', 'opening', 'OPEN-01-000454', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(455, 1, 455, '2026-03-01', 'opening', 'OPEN-01-000455', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(456, 1, 456, '2026-03-01', 'opening', 'OPEN-01-000456', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(457, 1, 457, '2026-03-01', 'opening', 'OPEN-01-000457', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(458, 1, 458, '2026-03-01', 'opening', 'OPEN-01-000458', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(459, 1, 459, '2026-03-01', 'opening', 'OPEN-01-000459', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(460, 1, 460, '2026-03-01', 'opening', 'OPEN-01-000460', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(461, 1, 461, '2026-03-01', 'opening', 'OPEN-01-000461', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(462, 1, 462, '2026-03-01', 'opening', 'OPEN-01-000462', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(463, 1, 463, '2026-03-01', 'opening', 'OPEN-01-000463', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(464, 1, 464, '2026-03-01', 'opening', 'OPEN-01-000464', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(465, 1, 465, '2026-03-01', 'opening', 'OPEN-01-000465', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(466, 1, 466, '2026-03-01', 'opening', 'OPEN-01-000466', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(467, 1, 467, '2026-03-01', 'opening', 'OPEN-01-000467', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(468, 1, 468, '2026-03-01', 'opening', 'OPEN-01-000468', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(469, 1, 469, '2026-03-01', 'opening', 'OPEN-01-000469', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(470, 1, 470, '2026-03-01', 'opening', 'OPEN-01-000470', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(471, 1, 471, '2026-03-01', 'opening', 'OPEN-01-000471', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(472, 1, 472, '2026-03-01', 'opening', 'OPEN-01-000472', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(473, 1, 473, '2026-03-01', 'opening', 'OPEN-01-000473', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(474, 1, 474, '2026-03-01', 'opening', 'OPEN-01-000474', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(475, 1, 475, '2026-03-01', 'opening', 'OPEN-01-000475', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(476, 1, 476, '2026-03-01', 'opening', 'OPEN-01-000476', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(477, 1, 477, '2026-03-01', 'opening', 'OPEN-01-000477', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(478, 1, 478, '2026-03-01', 'opening', 'OPEN-01-000478', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(479, 1, 479, '2026-03-01', 'opening', 'OPEN-01-000479', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(480, 1, 480, '2026-03-01', 'opening', 'OPEN-01-000480', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(481, 1, 481, '2026-03-01', 'opening', 'OPEN-01-000481', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(482, 1, 482, '2026-03-01', 'opening', 'OPEN-01-000482', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(483, 2, 100, '2026-03-01', 'opening', 'OPEN-02-000100', 'opening_stock', NULL, 1267.0000, 0.0000, 1267.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(484, 2, 101, '2026-03-01', 'opening', 'OPEN-02-000101', 'opening_stock', NULL, 40.0000, 0.0000, 40.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(485, 2, 102, '2026-03-01', 'opening', 'OPEN-02-000102', 'opening_stock', NULL, 40.0000, 0.0000, 40.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(486, 2, 103, '2026-03-01', 'opening', 'OPEN-02-000103', 'opening_stock', NULL, 40.0000, 0.0000, 40.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(487, 2, 104, '2026-03-01', 'opening', 'OPEN-02-000104', 'opening_stock', NULL, 40.0000, 0.0000, 40.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(488, 2, 105, '2026-03-01', 'opening', 'OPEN-02-000105', 'opening_stock', NULL, 40.0000, 0.0000, 40.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(489, 2, 106, '2026-03-01', 'opening', 'OPEN-02-000106', 'opening_stock', NULL, 40.0000, 0.0000, 40.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(490, 2, 107, '2026-03-01', 'opening', 'OPEN-02-000107', 'opening_stock', NULL, 40.0000, 0.0000, 40.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(491, 2, 108, '2026-03-01', 'opening', 'OPEN-02-000108', 'opening_stock', NULL, 40.0000, 0.0000, 40.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(492, 2, 109, '2026-03-01', 'opening', 'OPEN-02-000109', 'opening_stock', NULL, 40.0000, 0.0000, 40.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(493, 2, 110, '2026-03-01', 'opening', 'OPEN-02-000110', 'opening_stock', NULL, 40.0000, 0.0000, 40.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(494, 2, 111, '2026-03-01', 'opening', 'OPEN-02-000111', 'opening_stock', NULL, 40.0000, 0.0000, 40.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(495, 2, 112, '2026-03-01', 'opening', 'OPEN-02-000112', 'opening_stock', NULL, 40.0000, 0.0000, 40.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(496, 2, 113, '2026-03-01', 'opening', 'OPEN-02-000113', 'opening_stock', NULL, 40.0000, 0.0000, 40.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(497, 2, 114, '2026-03-01', 'opening', 'OPEN-02-000114', 'opening_stock', NULL, 40.0000, 0.0000, 40.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(498, 2, 115, '2026-03-01', 'opening', 'OPEN-02-000115', 'opening_stock', NULL, 40.0000, 0.0000, 40.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(499, 2, 116, '2026-03-01', 'opening', 'OPEN-02-000116', 'opening_stock', NULL, 40.0000, 0.0000, 40.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(500, 2, 117, '2026-03-01', 'opening', 'OPEN-02-000117', 'opening_stock', NULL, 40.0000, 0.0000, 40.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(501, 2, 118, '2026-03-01', 'opening', 'OPEN-02-000118', 'opening_stock', NULL, 40.0000, 0.0000, 40.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(502, 2, 119, '2026-03-01', 'opening', 'OPEN-02-000119', 'opening_stock', NULL, 40.0000, 0.0000, 40.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(503, 2, 120, '2026-03-01', 'opening', 'OPEN-02-000120', 'opening_stock', NULL, 40.0000, 0.0000, 40.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(504, 2, 121, '2026-03-01', 'opening', 'OPEN-02-000121', 'opening_stock', NULL, 40.0000, 0.0000, 40.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(505, 2, 122, '2026-03-01', 'opening', 'OPEN-02-000122', 'opening_stock', NULL, 40.0000, 0.0000, 40.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(506, 2, 123, '2026-03-01', 'opening', 'OPEN-02-000123', 'opening_stock', NULL, 40.0000, 0.0000, 40.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(507, 2, 124, '2026-03-01', 'opening', 'OPEN-02-000124', 'opening_stock', NULL, 40.0000, 0.0000, 40.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(508, 2, 125, '2026-03-01', 'opening', 'OPEN-02-000125', 'opening_stock', NULL, 40.0000, 0.0000, 40.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(509, 2, 126, '2026-03-01', 'opening', 'OPEN-02-000126', 'opening_stock', NULL, 40.0000, 0.0000, 40.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(510, 2, 127, '2026-03-01', 'opening', 'OPEN-02-000127', 'opening_stock', NULL, 40.0000, 0.0000, 40.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(511, 2, 128, '2026-03-01', 'opening', 'OPEN-02-000128', 'opening_stock', NULL, 40.0000, 0.0000, 40.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(512, 2, 129, '2026-03-01', 'opening', 'OPEN-02-000129', 'opening_stock', NULL, 40.0000, 0.0000, 40.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(513, 3, 200, '2026-03-01', 'opening', 'OPEN-03-000200', 'opening_stock', NULL, 30.0000, 0.0000, 30.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(514, 3, 201, '2026-03-01', 'opening', 'OPEN-03-000201', 'opening_stock', NULL, 30.0000, 0.0000, 30.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(515, 3, 202, '2026-03-01', 'opening', 'OPEN-03-000202', 'opening_stock', NULL, 30.0000, 0.0000, 30.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(516, 3, 203, '2026-03-01', 'opening', 'OPEN-03-000203', 'opening_stock', NULL, 30.0000, 0.0000, 30.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(517, 3, 204, '2026-03-01', 'opening', 'OPEN-03-000204', 'opening_stock', NULL, 30.0000, 0.0000, 30.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(518, 3, 205, '2026-03-01', 'opening', 'OPEN-03-000205', 'opening_stock', NULL, 30.0000, 0.0000, 30.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(519, 3, 206, '2026-03-01', 'opening', 'OPEN-03-000206', 'opening_stock', NULL, 30.0000, 0.0000, 30.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(520, 3, 207, '2026-03-01', 'opening', 'OPEN-03-000207', 'opening_stock', NULL, 30.0000, 0.0000, 30.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(521, 3, 208, '2026-03-01', 'opening', 'OPEN-03-000208', 'opening_stock', NULL, 30.0000, 0.0000, 30.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(522, 3, 209, '2026-03-01', 'opening', 'OPEN-03-000209', 'opening_stock', NULL, 30.0000, 0.0000, 30.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(523, 3, 210, '2026-03-01', 'opening', 'OPEN-03-000210', 'opening_stock', NULL, 30.0000, 0.0000, 30.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(524, 3, 211, '2026-03-01', 'opening', 'OPEN-03-000211', 'opening_stock', NULL, 30.0000, 0.0000, 30.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(525, 3, 212, '2026-03-01', 'opening', 'OPEN-03-000212', 'opening_stock', NULL, 30.0000, 0.0000, 30.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(526, 3, 213, '2026-03-01', 'opening', 'OPEN-03-000213', 'opening_stock', NULL, 30.0000, 0.0000, 30.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(527, 3, 214, '2026-03-01', 'opening', 'OPEN-03-000214', 'opening_stock', NULL, 30.0000, 0.0000, 30.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(528, 3, 215, '2026-03-01', 'opening', 'OPEN-03-000215', 'opening_stock', NULL, 30.0000, 0.0000, 30.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(529, 3, 216, '2026-03-01', 'opening', 'OPEN-03-000216', 'opening_stock', NULL, 30.0000, 0.0000, 30.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(530, 3, 217, '2026-03-01', 'opening', 'OPEN-03-000217', 'opening_stock', NULL, 30.0000, 0.0000, 30.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(531, 3, 218, '2026-03-01', 'opening', 'OPEN-03-000218', 'opening_stock', NULL, 30.0000, 0.0000, 30.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(532, 3, 219, '2026-03-01', 'opening', 'OPEN-03-000219', 'opening_stock', NULL, 30.0000, 0.0000, 30.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(533, 4, 300, '2026-03-01', 'opening', 'OPEN-04-000300', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(534, 4, 301, '2026-03-01', 'opening', 'OPEN-04-000301', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(535, 4, 302, '2026-03-01', 'opening', 'OPEN-04-000302', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00');
INSERT INTO `stock_movements` (`id`, `warehouse_id`, `product_id`, `transaction_date`, `movement_type`, `reference_no`, `source_type`, `source_id`, `quantity_in`, `quantity_out`, `balance_after`, `unit_cost`, `description`, `created_by`, `created_at`, `updated_at`) VALUES
(536, 4, 303, '2026-03-01', 'opening', 'OPEN-04-000303', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(537, 4, 304, '2026-03-01', 'opening', 'OPEN-04-000304', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(538, 4, 305, '2026-03-01', 'opening', 'OPEN-04-000305', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(539, 4, 306, '2026-03-01', 'opening', 'OPEN-04-000306', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(540, 4, 307, '2026-03-01', 'opening', 'OPEN-04-000307', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(541, 4, 308, '2026-03-01', 'opening', 'OPEN-04-000308', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(542, 4, 309, '2026-03-01', 'opening', 'OPEN-04-000309', 'opening_stock', NULL, 20.0000, 0.0000, 20.0000, 100.0000, 'Demo opening stock', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(543, 2, 6, '2026-09-25', 'sale_return', 'SRT-2026-000003', 'sale_return', 3, 1.0001, 0.0000, 1.0001, 100.0000, 'Sales return', 1, '2026-09-26 19:20:21', '2026-09-26 19:20:21'),
(544, 1, 13, '2026-09-28', 'sale', 'SAL-2026-000046', 'sale', 46, 0.0000, 1.0000, 1.0000, 100.0000, 'Sale issue', 3, '2026-09-28 16:05:26', '2026-09-28 16:05:26'),
(546, 1, 2, '2026-09-28', 'sale', 'SAL-2026-000053', 'sale', 53, 0.0000, 1.0000, 1.0000, 100.0000, 'Sale issue', 3, '2026-09-28 16:59:32', '2026-09-28 16:59:32'),
(547, 1, 12, '2026-09-28', 'sale', 'SAL-2026-000053', 'sale', 53, 0.0000, 1.0000, 1.0000, 100.0000, 'Sale issue', 3, '2026-09-28 16:59:32', '2026-09-28 16:59:32'),
(548, 1, 20, '2026-09-28', 'sale', 'SAL-2026-000057', 'sale', 57, 0.0000, 1.0000, 1.0000, 100.0000, 'Sale issue', 3, '2026-09-28 17:09:49', '2026-09-28 17:09:49'),
(549, 1, 2, '2026-09-28', 'sale', 'SAL-2026-000056', 'sale', 56, 0.0000, 1.0000, 0.0000, 100.0000, 'Sale issue', 3, '2026-09-28 17:46:38', '2026-09-28 17:46:38'),
(550, 1, 12, '2026-09-28', 'sale', 'SAL-2026-000056', 'sale', 56, 0.0000, 1.0000, 0.0000, 100.0000, 'Sale issue', 3, '2026-09-28 17:46:38', '2026-09-28 17:46:38'),
(551, 1, 13, '2026-09-29', 'sale', 'SAL-2026-000061', 'sale', 61, 0.0000, 1.0000, 0.0000, 100.0000, 'Sale issue', 3, '2026-09-29 14:03:17', '2026-09-29 14:03:17');

-- --------------------------------------------------------

--
-- Table structure for table `stock_transfers`
--

CREATE TABLE `stock_transfers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `transfer_no` varchar(50) NOT NULL,
  `from_warehouse_id` bigint(20) UNSIGNED NOT NULL,
  `to_warehouse_id` bigint(20) UNSIGNED NOT NULL,
  `transfer_date` date NOT NULL,
  `status` varchar(30) NOT NULL DEFAULT 'draft',
  `notes` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `posted_by` bigint(20) UNSIGNED DEFAULT NULL,
  `posted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `stock_transfers`
--

INSERT INTO `stock_transfers` (`id`, `transfer_no`, `from_warehouse_id`, `to_warehouse_id`, `transfer_date`, `status`, `notes`, `created_by`, `posted_by`, `posted_at`, `created_at`, `updated_at`) VALUES
(1, 'TRF-000001', 1, 2, '2026-08-22', 'posted', 'Main to branch transfer', 1, 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(2, 'TRF-000002', 1, 4, '2026-08-25', 'posted', 'Showroom replenishment', 1, 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', '2026-08-28 03:30:00');

-- --------------------------------------------------------

--
-- Table structure for table `stock_transfer_items`
--

CREATE TABLE `stock_transfer_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `stock_transfer_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` decimal(18,4) NOT NULL,
  `unit_cost` decimal(18,4) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `stock_transfer_items`
--

INSERT INTO `stock_transfer_items` (`id`, `stock_transfer_id`, `product_id`, `quantity`, `unit_cost`, `created_at`, `updated_at`) VALUES
(1, 1, 100, 20.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(2, 2, 300, 10.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00');

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(50) DEFAULT NULL,
  `supplier_group_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(200) NOT NULL,
  `company_name` varchar(200) DEFAULT NULL,
  `contact_person` varchar(150) DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `alternate_phone` varchar(30) DEFAULT NULL,
  `email` varchar(200) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `district` varchar(100) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `opening_balance` decimal(18,2) NOT NULL DEFAULT 0.00,
  `opening_balance_type` varchar(20) NOT NULL DEFAULT 'payable',
  `payment_terms_days` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `tax_number` varchar(100) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`id`, `code`, `supplier_group_id`, `name`, `company_name`, `contact_person`, `phone`, `alternate_phone`, `email`, `address`, `city`, `district`, `country`, `opening_balance`, `opening_balance_type`, `payment_terms_days`, `tax_number`, `notes`, `is_active`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'SUP-000001', 1, 'Electro Mart', 'Electro Mart', 'Contact 001', '01820000001', NULL, 'supplier001@example.com', '201 Supply Road', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'payable', 30, NULL, 'Sample dashboard supplier', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(2, 'SUP-000002', 2, 'Computer Source', 'Computer Source', 'Contact 002', '01820000002', NULL, 'supplier002@example.com', '202 Supply Road', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'payable', 30, NULL, 'Sample dashboard supplier', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(3, 'SUP-000003', 3, 'Tech Distribution Ltd.', 'Tech Distribution Ltd.', 'Contact 003', '01820000003', NULL, 'supplier003@example.com', '203 Supply Road', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'payable', 30, NULL, 'Sample dashboard supplier', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(4, 'SUP-000004', 1, 'Global Imports', 'Global Imports', 'Contact 004', '01820000004', NULL, 'supplier004@example.com', '204 Supply Road', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'payable', 30, NULL, 'Sample dashboard supplier', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(5, 'SUP-000005', 2, 'Metro Supply', 'Metro Supply', 'Contact 005', '01820000005', NULL, 'supplier005@example.com', '205 Supply Road', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'payable', 30, NULL, 'Sample dashboard supplier', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(6, 'SUP-000006', 3, 'Prime Wholesale', 'Prime Wholesale', 'Contact 006', '01820000006', NULL, 'supplier006@example.com', '206 Supply Road', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'payable', 30, NULL, 'Sample dashboard supplier', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(7, 'SUP-000007', 1, 'Smart Electronics', 'Smart Electronics', 'Contact 007', '01820000007', NULL, 'supplier007@example.com', '207 Supply Road', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'payable', 30, NULL, 'Sample dashboard supplier', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(8, 'SUP-000008', 2, 'Digital Distributor', 'Digital Distributor', 'Contact 008', '01820000008', NULL, 'supplier008@example.com', '208 Supply Road', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'payable', 30, NULL, 'Sample dashboard supplier', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(9, 'SUP-000009', 3, 'Sample Supplier 009', 'Sample Supplier 009', 'Contact 009', '01820000009', NULL, 'supplier009@example.com', '209 Supply Road', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'payable', 30, NULL, 'Sample dashboard supplier', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(10, 'SUP-000010', 1, 'Sample Supplier 010', 'Sample Supplier 010', 'Contact 010', '01820000010', NULL, 'supplier010@example.com', '210 Supply Road', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'payable', 30, NULL, 'Sample dashboard supplier', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(11, 'SUP-000011', 2, 'Sample Supplier 011', 'Sample Supplier 011', 'Contact 011', '01820000011', NULL, 'supplier011@example.com', '211 Supply Road', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'payable', 30, NULL, 'Sample dashboard supplier', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(12, 'SUP-000012', 3, 'Sample Supplier 012', 'Sample Supplier 012', 'Contact 012', '01820000012', NULL, 'supplier012@example.com', '212 Supply Road', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'payable', 30, NULL, 'Sample dashboard supplier', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(13, 'SUP-000013', 1, 'Sample Supplier 013', 'Sample Supplier 013', 'Contact 013', '01820000013', NULL, 'supplier013@example.com', '213 Supply Road', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'payable', 30, NULL, 'Sample dashboard supplier', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(14, 'SUP-000014', 2, 'Sample Supplier 014', 'Sample Supplier 014', 'Contact 014', '01820000014', NULL, 'supplier014@example.com', '214 Supply Road', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'payable', 30, NULL, 'Sample dashboard supplier', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(15, 'SUP-000015', 3, 'Sample Supplier 015', 'Sample Supplier 015', 'Contact 015', '01820000015', NULL, 'supplier015@example.com', '215 Supply Road', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'payable', 30, NULL, 'Sample dashboard supplier', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(16, 'SUP-000016', 1, 'Sample Supplier 016', 'Sample Supplier 016', 'Contact 016', '01820000016', NULL, 'supplier016@example.com', '216 Supply Road', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'payable', 30, NULL, 'Sample dashboard supplier', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(17, 'SUP-000017', 2, 'Sample Supplier 017', 'Sample Supplier 017', 'Contact 017', '01820000017', NULL, 'supplier017@example.com', '217 Supply Road', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'payable', 30, NULL, 'Sample dashboard supplier', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(18, 'SUP-000018', 3, 'Sample Supplier 018', 'Sample Supplier 018', 'Contact 018', '01820000018', NULL, 'supplier018@example.com', '218 Supply Road', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'payable', 30, NULL, 'Sample dashboard supplier', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(19, 'SUP-000019', 1, 'Sample Supplier 019', 'Sample Supplier 019', 'Contact 019', '01820000019', NULL, 'supplier019@example.com', '219 Supply Road', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'payable', 30, NULL, 'Sample dashboard supplier', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(20, 'SUP-000020', 2, 'Sample Supplier 020', 'Sample Supplier 020', 'Contact 020', '01820000020', NULL, 'supplier020@example.com', '220 Supply Road', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'payable', 30, NULL, 'Sample dashboard supplier', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(21, 'SUP-000021', 3, 'Sample Supplier 021', 'Sample Supplier 021', 'Contact 021', '01820000021', NULL, 'supplier021@example.com', '221 Supply Road', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'payable', 30, NULL, 'Sample dashboard supplier', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(22, 'SUP-000022', 1, 'Sample Supplier 022', 'Sample Supplier 022', 'Contact 022', '01820000022', NULL, 'supplier022@example.com', '222 Supply Road', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'payable', 30, NULL, 'Sample dashboard supplier', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(23, 'SUP-000023', 2, 'Sample Supplier 023', 'Sample Supplier 023', 'Contact 023', '01820000023', NULL, 'supplier023@example.com', '223 Supply Road', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'payable', 30, NULL, 'Sample dashboard supplier', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(24, 'SUP-000024', 3, 'Sample Supplier 024', 'Sample Supplier 024', 'Contact 024', '01820000024', NULL, 'supplier024@example.com', '224 Supply Road', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'payable', 30, NULL, 'Sample dashboard supplier', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(25, 'SUP-000025', 1, 'Sample Supplier 025', 'Sample Supplier 025', 'Contact 025', '01820000025', NULL, 'supplier025@example.com', '225 Supply Road', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'payable', 30, NULL, 'Sample dashboard supplier', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(26, 'SUP-000026', 2, 'Sample Supplier 026', 'Sample Supplier 026', 'Contact 026', '01820000026', NULL, 'supplier026@example.com', '226 Supply Road', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'payable', 30, NULL, 'Sample dashboard supplier', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(27, 'SUP-000027', 3, 'Sample Supplier 027', 'Sample Supplier 027', 'Contact 027', '01820000027', NULL, 'supplier027@example.com', '227 Supply Road', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'payable', 30, NULL, 'Sample dashboard supplier', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(28, 'SUP-000028', 1, 'Sample Supplier 028', 'Sample Supplier 028', 'Contact 028', '01820000028', NULL, 'supplier028@example.com', '228 Supply Road', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'payable', 30, NULL, 'Sample dashboard supplier', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(29, 'SUP-000029', 2, 'Sample Supplier 029', 'Sample Supplier 029', 'Contact 029', '01820000029', NULL, 'supplier029@example.com', '229 Supply Road', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'payable', 30, NULL, 'Sample dashboard supplier', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(30, 'SUP-000030', 3, 'Sample Supplier 030', 'Sample Supplier 030', 'Contact 030', '01820000030', NULL, 'supplier030@example.com', '230 Supply Road', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'payable', 30, NULL, 'Sample dashboard supplier', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(31, 'SUP-000031', 1, 'Sample Supplier 031', 'Sample Supplier 031', 'Contact 031', '01820000031', NULL, 'supplier031@example.com', '231 Supply Road', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'payable', 30, NULL, 'Sample dashboard supplier', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(32, 'SUP-000032', 2, 'Sample Supplier 032', 'Sample Supplier 032', 'Contact 032', '01820000032', NULL, 'supplier032@example.com', '232 Supply Road', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'payable', 30, NULL, 'Sample dashboard supplier', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(33, 'SUP-000033', 3, 'Sample Supplier 033', 'Sample Supplier 033', 'Contact 033', '01820000033', NULL, 'supplier033@example.com', '233 Supply Road', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'payable', 30, NULL, 'Sample dashboard supplier', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(34, 'SUP-000034', 1, 'Sample Supplier 034', 'Sample Supplier 034', 'Contact 034', '01820000034', NULL, 'supplier034@example.com', '234 Supply Road', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'payable', 30, NULL, 'Sample dashboard supplier', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(35, 'SUP-000035', 2, 'Sample Supplier 035', 'Sample Supplier 035', 'Contact 035', '01820000035', NULL, 'supplier035@example.com', '235 Supply Road', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'payable', 30, NULL, 'Sample dashboard supplier', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(36, 'SUP-000036', 3, 'Sample Supplier 036', 'Sample Supplier 036', 'Contact 036', '01820000036', NULL, 'supplier036@example.com', '236 Supply Road', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'payable', 30, NULL, 'Sample dashboard supplier', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(37, 'SUP-000037', 1, 'Sample Supplier 037', 'Sample Supplier 037', 'Contact 037', '01820000037', NULL, 'supplier037@example.com', '237 Supply Road', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'payable', 30, NULL, 'Sample dashboard supplier', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(38, 'SUP-000038', 2, 'Sample Supplier 038', 'Sample Supplier 038', 'Contact 038', '01820000038', NULL, 'supplier038@example.com', '238 Supply Road', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'payable', 30, NULL, 'Sample dashboard supplier', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(39, 'SUP-000039', 3, 'Sample Supplier 039', 'Sample Supplier 039', 'Contact 039', '01820000039', NULL, 'supplier039@example.com', '239 Supply Road', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'payable', 30, NULL, 'Sample dashboard supplier', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(40, 'SUP-000040', 1, 'Sample Supplier 040', 'Sample Supplier 040', 'Contact 040', '01820000040', NULL, 'supplier040@example.com', '240 Supply Road', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'payable', 30, NULL, 'Sample dashboard supplier', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(41, 'SUP-000041', 2, 'Sample Supplier 041', 'Sample Supplier 041', 'Contact 041', '01820000041', NULL, 'supplier041@example.com', '241 Supply Road', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'payable', 30, NULL, 'Sample dashboard supplier', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(42, 'SUP-000042', 3, 'Sample Supplier 042', 'Sample Supplier 042', 'Contact 042', '01820000042', NULL, 'supplier042@example.com', '242 Supply Road', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'payable', 30, NULL, 'Sample dashboard supplier', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(43, 'SUP-000043', 1, 'Sample Supplier 043', 'Sample Supplier 043', 'Contact 043', '01820000043', NULL, 'supplier043@example.com', '243 Supply Road', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'payable', 30, NULL, 'Sample dashboard supplier', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(44, 'SUP-000044', 2, 'Sample Supplier 044', 'Sample Supplier 044', 'Contact 044', '01820000044', NULL, 'supplier044@example.com', '244 Supply Road', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'payable', 30, NULL, 'Sample dashboard supplier', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(45, 'SUP-000045', 3, 'Sample Supplier 045', 'Sample Supplier 045', 'Contact 045', '01820000045', NULL, 'supplier045@example.com', '245 Supply Road', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'payable', 30, NULL, 'Sample dashboard supplier', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(46, 'SUP-000046', 1, 'Sample Supplier 046', 'Sample Supplier 046', 'Contact 046', '01820000046', NULL, 'supplier046@example.com', '246 Supply Road', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'payable', 30, NULL, 'Sample dashboard supplier', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(47, 'SUP-000047', 2, 'Sample Supplier 047', 'Sample Supplier 047', 'Contact 047', '01820000047', NULL, 'supplier047@example.com', '247 Supply Road', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'payable', 30, NULL, 'Sample dashboard supplier', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(48, 'SUP-000048', 3, 'Sample Supplier 048', 'Sample Supplier 048', 'Contact 048', '01820000048', NULL, 'supplier048@example.com', '248 Supply Road', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'payable', 30, NULL, 'Sample dashboard supplier', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(49, 'SUP-000049', 1, 'Sample Supplier 049', 'Sample Supplier 049', 'Contact 049', '01820000049', NULL, 'supplier049@example.com', '249 Supply Road', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'payable', 30, NULL, 'Sample dashboard supplier', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(50, 'SUP-000050', 2, 'Sample Supplier 050', 'Sample Supplier 050', 'Contact 050', '01820000050', NULL, 'supplier050@example.com', '250 Supply Road', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'payable', 30, NULL, 'Sample dashboard supplier', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(51, 'SUP-000051', 3, 'Sample Supplier 051', 'Sample Supplier 051', 'Contact 051', '01820000051', NULL, 'supplier051@example.com', '251 Supply Road', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'payable', 30, NULL, 'Sample dashboard supplier', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(52, 'SUP-000052', 1, 'Sample Supplier 052', 'Sample Supplier 052', 'Contact 052', '01820000052', NULL, 'supplier052@example.com', '252 Supply Road', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'payable', 30, NULL, 'Sample dashboard supplier', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(53, 'SUP-000053', 2, 'Sample Supplier 053', 'Sample Supplier 053', 'Contact 053', '01820000053', NULL, 'supplier053@example.com', '253 Supply Road', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'payable', 30, NULL, 'Sample dashboard supplier', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(54, 'SUP-000054', 3, 'Sample Supplier 054', 'Sample Supplier 054', 'Contact 054', '01820000054', NULL, 'supplier054@example.com', '254 Supply Road', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'payable', 30, NULL, 'Sample dashboard supplier', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(55, 'SUP-000055', 1, 'Sample Supplier 055', 'Sample Supplier 055', 'Contact 055', '01820000055', NULL, 'supplier055@example.com', '255 Supply Road', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'payable', 30, NULL, 'Sample dashboard supplier', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(56, 'SUP-000056', 2, 'Sample Supplier 056', 'Sample Supplier 056', 'Contact 056', '01820000056', NULL, 'supplier056@example.com', '256 Supply Road', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'payable', 30, NULL, 'Sample dashboard supplier', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(57, 'SUP-000057', 3, 'Sample Supplier 057', 'Sample Supplier 057', 'Contact 057', '01820000057', NULL, 'supplier057@example.com', '257 Supply Road', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'payable', 30, NULL, 'Sample dashboard supplier', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(58, 'SUP-000058', 1, 'Sample Supplier 058', 'Sample Supplier 058', 'Contact 058', '01820000058', NULL, 'supplier058@example.com', '258 Supply Road', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'payable', 30, NULL, 'Sample dashboard supplier', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(59, 'SUP-000059', 2, 'Sample Supplier 059', 'Sample Supplier 059', 'Contact 059', '01820000059', NULL, 'supplier059@example.com', '259 Supply Road', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'payable', 30, NULL, 'Sample dashboard supplier', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(60, 'SUP-000060', 3, 'Sample Supplier 060', 'Sample Supplier 060', 'Contact 060', '01820000060', NULL, 'supplier060@example.com', '260 Supply Road', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'payable', 30, NULL, 'Sample dashboard supplier', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(61, 'SUP-000061', 1, 'Sample Supplier 061', 'Sample Supplier 061', 'Contact 061', '01820000061', NULL, 'supplier061@example.com', '261 Supply Road', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'payable', 30, NULL, 'Sample dashboard supplier', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(62, 'SUP-000062', 2, 'Sample Supplier 062', 'Sample Supplier 062', 'Contact 062', '01820000062', NULL, 'supplier062@example.com', '262 Supply Road', 'Dhaka', 'Dhaka', 'Bangladesh', 0.00, 'payable', 30, NULL, 'Sample dashboard supplier', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `supplier_groups`
--

CREATE TABLE `supplier_groups` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(150) NOT NULL,
  `code` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `supplier_groups`
--

INSERT INTO `supplier_groups` (`id`, `name`, `code`, `description`, `is_active`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Local Supplier', 'SG-LOC', 'Local suppliers', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(2, 'Distributor', 'SG-DIS', 'Authorized distributors', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(3, 'Importer', 'SG-IMP', 'Import suppliers', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `supplier_ledger_entries`
--

CREATE TABLE `supplier_ledger_entries` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `supplier_id` bigint(20) UNSIGNED NOT NULL,
  `transaction_date` date NOT NULL,
  `entry_type` varchar(50) NOT NULL,
  `reference_no` varchar(100) DEFAULT NULL,
  `source_type` varchar(100) DEFAULT NULL,
  `source_id` bigint(20) UNSIGNED DEFAULT NULL,
  `debit` decimal(18,2) NOT NULL DEFAULT 0.00,
  `credit` decimal(18,2) NOT NULL DEFAULT 0.00,
  `description` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `supplier_ledger_entries`
--

INSERT INTO `supplier_ledger_entries` (`id`, `supplier_id`, `transaction_date`, `entry_type`, `reference_no`, `source_type`, `source_id`, `debit`, `credit`, `description`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 1, '2026-03-04', 'purchase', 'PUR-000001', 'purchase', 1, 0.00, 54000.00, 'Purchase invoice', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(2, 2, '2026-03-11', 'purchase', 'PUR-000002', 'purchase', 2, 0.00, 54000.00, 'Purchase invoice', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(3, 3, '2026-03-18', 'purchase', 'PUR-000003', 'purchase', 3, 0.00, 54000.00, 'Purchase invoice', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(4, 4, '2026-03-25', 'purchase', 'PUR-000004', 'purchase', 4, 0.00, 54000.00, 'Purchase invoice', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(5, 5, '2026-04-04', 'purchase', 'PUR-000005', 'purchase', 5, 0.00, 63000.00, 'Purchase invoice', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(6, 6, '2026-04-11', 'purchase', 'PUR-000006', 'purchase', 6, 0.00, 63000.00, 'Purchase invoice', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(7, 7, '2026-04-18', 'purchase', 'PUR-000007', 'purchase', 7, 0.00, 63000.00, 'Purchase invoice', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(8, 8, '2026-04-25', 'purchase', 'PUR-000008', 'purchase', 8, 0.00, 63000.00, 'Purchase invoice', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(9, 9, '2026-05-04', 'purchase', 'PUR-000009', 'purchase', 9, 0.00, 70200.00, 'Purchase invoice', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(10, 10, '2026-05-11', 'purchase', 'PUR-000010', 'purchase', 10, 0.00, 70200.00, 'Purchase invoice', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(11, 11, '2026-05-18', 'purchase', 'PUR-000011', 'purchase', 11, 0.00, 70200.00, 'Purchase invoice', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(12, 12, '2026-05-25', 'purchase', 'PUR-000012', 'purchase', 12, 0.00, 70200.00, 'Purchase invoice', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(13, 1, '2026-06-04', 'purchase', 'PUR-000013', 'purchase', 13, 0.00, 91962.00, 'Purchase invoice', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(14, 2, '2026-06-11', 'purchase', 'PUR-000014', 'purchase', 14, 0.00, 76716.00, 'Purchase invoice', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(15, 3, '2026-06-18', 'purchase', 'PUR-000015', 'purchase', 15, 0.00, 46320.48, 'Purchase invoice', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(16, 4, '2026-06-25', 'purchase', 'PUR-000016', 'purchase', 16, 0.00, 80201.52, 'Purchase invoice', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(17, 5, '2026-07-04', 'purchase', 'PUR-000017', 'purchase', 17, 0.00, 77400.00, 'Purchase invoice', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(18, 6, '2026-07-11', 'purchase', 'PUR-000018', 'purchase', 18, 0.00, 77400.00, 'Purchase invoice', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(19, 7, '2026-07-18', 'purchase', 'PUR-000019', 'purchase', 19, 0.00, 77400.00, 'Purchase invoice', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(20, 8, '2026-07-25', 'purchase', 'PUR-000020', 'purchase', 20, 0.00, 77400.00, 'Purchase invoice', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(21, 6, '2026-08-01', 'purchase', 'PUR-000021', 'purchase', 21, 0.00, 20139.00, 'Purchase invoice', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(22, 7, '2026-08-03', 'purchase', 'PUR-000022', 'purchase', 22, 0.00, 20139.00, 'Purchase invoice', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(23, 8, '2026-08-05', 'purchase', 'PUR-000023', 'purchase', 23, 0.00, 20139.00, 'Purchase invoice', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(24, 9, '2026-08-07', 'purchase', 'PUR-000024', 'purchase', 24, 0.00, 20139.00, 'Purchase invoice', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(25, 10, '2026-08-09', 'purchase', 'PUR-000025', 'purchase', 25, 0.00, 20139.00, 'Purchase invoice', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(26, 11, '2026-08-11', 'purchase', 'PUR-000026', 'purchase', 26, 0.00, 20139.00, 'Purchase invoice', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(27, 12, '2026-08-13', 'purchase', 'PUR-000027', 'purchase', 27, 0.00, 20139.00, 'Purchase invoice', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(28, 13, '2026-08-15', 'purchase', 'PUR-000028', 'purchase', 28, 0.00, 20139.00, 'Purchase invoice', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(29, 14, '2026-08-17', 'purchase', 'PUR-000029', 'purchase', 29, 0.00, 20139.00, 'Purchase invoice', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(30, 15, '2026-08-19', 'purchase', 'PUR-000030', 'purchase', 30, 0.00, 20139.00, 'Purchase invoice', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(31, 1, '2026-08-20', 'purchase', 'PUR-000031', 'purchase', 31, 0.00, 20139.00, 'Purchase invoice', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(32, 2, '2026-08-22', 'purchase', 'PUR-000032', 'purchase', 32, 0.00, 20139.00, 'Purchase invoice', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(33, 3, '2026-08-24', 'purchase', 'PUR-000033', 'purchase', 33, 0.00, 20139.00, 'Purchase invoice', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(34, 4, '2026-08-26', 'purchase', 'PUR-000034', 'purchase', 34, 0.00, 20139.00, 'Purchase invoice', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(35, 5, '2026-08-27', 'purchase', 'PUR-000035', 'purchase', 35, 0.00, 20139.00, 'Purchase invoice', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(36, 1, '2026-08-28', 'purchase', 'PUR-000036', 'purchase', 36, 0.00, 14000.00, 'Purchase invoice', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(37, 1, '2026-03-04', 'payment', 'PAY-000001', 'supplier_payment', 1, 46061.80, 0.00, 'Supplier payment', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(38, 2, '2026-03-11', 'payment', 'PAY-000002', 'supplier_payment', 2, 46061.80, 0.00, 'Supplier payment', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(39, 3, '2026-03-18', 'payment', 'PAY-000003', 'supplier_payment', 3, 46061.80, 0.00, 'Supplier payment', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(40, 4, '2026-03-25', 'payment', 'PAY-000004', 'supplier_payment', 4, 46061.80, 0.00, 'Supplier payment', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(41, 5, '2026-04-04', 'payment', 'PAY-000005', 'supplier_payment', 5, 53738.76, 0.00, 'Supplier payment', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(42, 6, '2026-04-11', 'payment', 'PAY-000006', 'supplier_payment', 6, 53738.76, 0.00, 'Supplier payment', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(43, 7, '2026-04-18', 'payment', 'PAY-000007', 'supplier_payment', 7, 53738.76, 0.00, 'Supplier payment', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(44, 8, '2026-04-25', 'payment', 'PAY-000008', 'supplier_payment', 8, 53738.76, 0.00, 'Supplier payment', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(45, 9, '2026-05-04', 'payment', 'PAY-000009', 'supplier_payment', 9, 59880.33, 0.00, 'Supplier payment', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(46, 10, '2026-05-11', 'payment', 'PAY-000010', 'supplier_payment', 10, 59880.33, 0.00, 'Supplier payment', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(47, 11, '2026-05-18', 'payment', 'PAY-000011', 'supplier_payment', 11, 59880.33, 0.00, 'Supplier payment', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(48, 12, '2026-05-25', 'payment', 'PAY-000012', 'supplier_payment', 12, 59880.33, 0.00, 'Supplier payment', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(49, 1, '2026-06-04', 'payment', 'PAY-000013', 'supplier_payment', 13, 78443.24, 0.00, 'Supplier payment', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(50, 2, '2026-06-11', 'payment', 'PAY-000014', 'supplier_payment', 14, 65438.46, 0.00, 'Supplier payment', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(51, 3, '2026-06-18', 'payment', 'PAY-000015', 'supplier_payment', 15, 39511.19, 0.00, 'Supplier payment', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(52, 4, '2026-06-25', 'payment', 'PAY-000016', 'supplier_payment', 16, 68411.59, 0.00, 'Supplier payment', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(53, 5, '2026-07-04', 'payment', 'PAY-000017', 'supplier_payment', 17, 66021.91, 0.00, 'Supplier payment', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(54, 6, '2026-07-11', 'payment', 'PAY-000018', 'supplier_payment', 18, 66021.91, 0.00, 'Supplier payment', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(55, 7, '2026-07-18', 'payment', 'PAY-000019', 'supplier_payment', 19, 66021.91, 0.00, 'Supplier payment', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(56, 8, '2026-07-25', 'payment', 'PAY-000020', 'supplier_payment', 20, 66021.91, 0.00, 'Supplier payment', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(57, 6, '2026-08-01', 'payment', 'PAY-000021', 'supplier_payment', 21, 17178.49, 0.00, 'Supplier payment', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(58, 7, '2026-08-03', 'payment', 'PAY-000022', 'supplier_payment', 22, 17178.49, 0.00, 'Supplier payment', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(59, 8, '2026-08-05', 'payment', 'PAY-000023', 'supplier_payment', 23, 17178.49, 0.00, 'Supplier payment', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(60, 9, '2026-08-07', 'payment', 'PAY-000024', 'supplier_payment', 24, 17178.49, 0.00, 'Supplier payment', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(61, 10, '2026-08-09', 'payment', 'PAY-000025', 'supplier_payment', 25, 17178.49, 0.00, 'Supplier payment', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(62, 11, '2026-08-11', 'payment', 'PAY-000026', 'supplier_payment', 26, 17178.49, 0.00, 'Supplier payment', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(63, 12, '2026-08-13', 'payment', 'PAY-000027', 'supplier_payment', 27, 17178.49, 0.00, 'Supplier payment', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(64, 13, '2026-08-15', 'payment', 'PAY-000028', 'supplier_payment', 28, 17178.49, 0.00, 'Supplier payment', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(65, 14, '2026-08-17', 'payment', 'PAY-000029', 'supplier_payment', 29, 17178.49, 0.00, 'Supplier payment', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(66, 15, '2026-08-19', 'payment', 'PAY-000030', 'supplier_payment', 30, 17178.49, 0.00, 'Supplier payment', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(67, 1, '2026-08-20', 'payment', 'PAY-000031', 'supplier_payment', 31, 17178.49, 0.00, 'Supplier payment', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(68, 2, '2026-08-22', 'payment', 'PAY-000032', 'supplier_payment', 32, 17178.49, 0.00, 'Supplier payment', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(69, 3, '2026-08-24', 'payment', 'PAY-000033', 'supplier_payment', 33, 17178.49, 0.00, 'Supplier payment', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(70, 4, '2026-08-26', 'payment', 'PAY-000034', 'supplier_payment', 34, 17178.49, 0.00, 'Supplier payment', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(71, 5, '2026-08-27', 'payment', 'PAY-000035', 'supplier_payment', 35, 17178.49, 0.00, 'Supplier payment', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(72, 1, '2026-08-28', 'payment', 'PAY-000036', 'supplier_payment', 36, 11941.97, 0.00, 'Supplier payment', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00');

-- --------------------------------------------------------

--
-- Table structure for table `supplier_payments`
--

CREATE TABLE `supplier_payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `payment_no` varchar(50) NOT NULL,
  `supplier_id` bigint(20) UNSIGNED NOT NULL,
  `purchase_id` bigint(20) UNSIGNED DEFAULT NULL,
  `account_id` bigint(20) UNSIGNED NOT NULL,
  `payment_date` date NOT NULL,
  `amount` decimal(18,2) NOT NULL,
  `method` varchar(30) NOT NULL DEFAULT 'cash',
  `reference` varchar(100) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'posted',
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `supplier_payments`
--

INSERT INTO `supplier_payments` (`id`, `payment_no`, `supplier_id`, `purchase_id`, `account_id`, `payment_date`, `amount`, `method`, `reference`, `notes`, `status`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'PAY-000001', 1, 1, 2, '2026-03-04', 46061.80, 'bank', NULL, 'Sample supplier payment', 'posted', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(2, 'PAY-000002', 2, 2, 1, '2026-03-11', 46061.80, 'cash', NULL, 'Sample supplier payment', 'posted', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(3, 'PAY-000003', 3, 3, 2, '2026-03-18', 46061.80, 'bank', NULL, 'Sample supplier payment', 'posted', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(4, 'PAY-000004', 4, 4, 1, '2026-03-25', 46061.80, 'cash', NULL, 'Sample supplier payment', 'posted', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(5, 'PAY-000005', 5, 5, 2, '2026-04-04', 53738.76, 'bank', NULL, 'Sample supplier payment', 'posted', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(6, 'PAY-000006', 6, 6, 1, '2026-04-11', 53738.76, 'cash', NULL, 'Sample supplier payment', 'posted', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(7, 'PAY-000007', 7, 7, 2, '2026-04-18', 53738.76, 'bank', NULL, 'Sample supplier payment', 'posted', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(8, 'PAY-000008', 8, 8, 1, '2026-04-25', 53738.76, 'cash', NULL, 'Sample supplier payment', 'posted', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(9, 'PAY-000009', 9, 9, 2, '2026-05-04', 59880.33, 'bank', NULL, 'Sample supplier payment', 'posted', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(10, 'PAY-000010', 10, 10, 1, '2026-05-11', 59880.33, 'cash', NULL, 'Sample supplier payment', 'posted', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(11, 'PAY-000011', 11, 11, 2, '2026-05-18', 59880.33, 'bank', NULL, 'Sample supplier payment', 'posted', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(12, 'PAY-000012', 12, 12, 1, '2026-05-25', 59880.33, 'cash', NULL, 'Sample supplier payment', 'posted', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(13, 'PAY-000013', 1, 13, 2, '2026-06-04', 78443.24, 'bank', NULL, 'Sample supplier payment', 'posted', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(14, 'PAY-000014', 2, 14, 1, '2026-06-11', 65438.46, 'cash', NULL, 'Sample supplier payment', 'posted', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(15, 'PAY-000015', 3, 15, 2, '2026-06-18', 39511.19, 'bank', NULL, 'Sample supplier payment', 'posted', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(16, 'PAY-000016', 4, 16, 1, '2026-06-25', 68411.59, 'cash', NULL, 'Sample supplier payment', 'posted', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(17, 'PAY-000017', 5, 17, 2, '2026-07-04', 66021.91, 'bank', NULL, 'Sample supplier payment', 'posted', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(18, 'PAY-000018', 6, 18, 1, '2026-07-11', 66021.91, 'cash', NULL, 'Sample supplier payment', 'posted', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(19, 'PAY-000019', 7, 19, 2, '2026-07-18', 66021.91, 'bank', NULL, 'Sample supplier payment', 'posted', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(20, 'PAY-000020', 8, 20, 1, '2026-07-25', 66021.91, 'cash', NULL, 'Sample supplier payment', 'posted', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(21, 'PAY-000021', 6, 21, 2, '2026-08-01', 17178.49, 'bank', NULL, 'Sample supplier payment', 'posted', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(22, 'PAY-000022', 7, 22, 1, '2026-08-03', 17178.49, 'cash', NULL, 'Sample supplier payment', 'posted', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(23, 'PAY-000023', 8, 23, 2, '2026-08-05', 17178.49, 'bank', NULL, 'Sample supplier payment', 'posted', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(24, 'PAY-000024', 9, 24, 1, '2026-08-07', 17178.49, 'cash', NULL, 'Sample supplier payment', 'posted', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(25, 'PAY-000025', 10, 25, 2, '2026-08-09', 17178.49, 'bank', NULL, 'Sample supplier payment', 'posted', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(26, 'PAY-000026', 11, 26, 1, '2026-08-11', 17178.49, 'cash', NULL, 'Sample supplier payment', 'posted', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(27, 'PAY-000027', 12, 27, 2, '2026-08-13', 17178.49, 'bank', NULL, 'Sample supplier payment', 'posted', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(28, 'PAY-000028', 13, 28, 1, '2026-08-15', 17178.49, 'cash', NULL, 'Sample supplier payment', 'posted', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(29, 'PAY-000029', 14, 29, 2, '2026-08-17', 17178.49, 'bank', NULL, 'Sample supplier payment', 'posted', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(30, 'PAY-000030', 15, 30, 1, '2026-08-19', 17178.49, 'cash', NULL, 'Sample supplier payment', 'posted', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(31, 'PAY-000031', 1, 31, 2, '2026-08-20', 17178.49, 'bank', NULL, 'Sample supplier payment', 'posted', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(32, 'PAY-000032', 2, 32, 1, '2026-08-22', 17178.49, 'cash', NULL, 'Sample supplier payment', 'posted', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(33, 'PAY-000033', 3, 33, 2, '2026-08-24', 17178.49, 'bank', NULL, 'Sample supplier payment', 'posted', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(34, 'PAY-000034', 4, 34, 1, '2026-08-26', 17178.49, 'cash', NULL, 'Sample supplier payment', 'posted', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(35, 'PAY-000035', 5, 35, 2, '2026-08-27', 17178.49, 'bank', NULL, 'Sample supplier payment', 'posted', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(36, 'PAY-000036', 1, 36, 1, '2026-08-28', 11941.97, 'cash', NULL, 'Sample supplier payment', 'posted', 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00');

-- --------------------------------------------------------

--
-- Table structure for table `system_notifications`
--

CREATE TABLE `system_notifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `type` varchar(50) NOT NULL DEFAULT 'info',
  `title` varchar(180) NOT NULL,
  `message` text NOT NULL,
  `link` varchar(255) DEFAULT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `system_notifications`
--

INSERT INTO `system_notifications` (`id`, `user_id`, `type`, `title`, `message`, `link`, `read_at`, `created_at`, `updated_at`) VALUES
(1, 1, 'warning', 'Low Stock Alert', '23 products are at or below reorder level.', '/inventory', '2026-08-28 04:54:10', '2026-08-28 03:30:00', '2026-08-28 04:54:10'),
(2, 1, 'success', 'Sale Posted', 'Invoice for today\'s sale has been posted successfully.', '/sales', '2026-08-28 04:54:09', '2026-08-28 03:30:00', '2026-08-28 04:54:09'),
(3, 1, 'info', 'Supplier Payment', 'Latest supplier payment was recorded.', '/supplier-payments', '2026-08-28 04:54:06', '2026-08-28 03:30:00', '2026-08-28 04:54:06'),
(4, 1, 'warning', 'Receivable Follow-up', 'Outstanding customer receivable is 325,680.00.', '/reports', '2026-08-28 04:54:11', '2026-08-28 03:30:00', '2026-08-28 04:54:11'),
(5, 1, 'info', 'Payroll Posted', 'August payroll has been posted.', '/payroll-runs', '2026-08-28 04:54:11', '2026-08-28 03:30:00', '2026-08-28 04:54:11'),
(6, NULL, 'warning', 'Low stock', 'Dell 24-inch Monitor at Main Warehouse has 1.0000 units remaining.', '/inventory', NULL, '2026-09-28 16:05:26', '2026-09-28 16:05:26'),
(8, NULL, 'warning', 'Low stock', 'HP Laptop 15 at Main Warehouse has 1.0000 units remaining.', '/inventory', '2026-10-08 11:21:21', '2026-09-28 16:59:32', '2026-10-08 11:21:21'),
(9, NULL, 'warning', 'Low stock', 'Logitech Keyboard K120 at Main Warehouse has 1.0000 units remaining.', '/inventory', NULL, '2026-09-28 16:59:32', '2026-09-28 16:59:32'),
(10, NULL, 'warning', 'Low stock', 'Lenovo Wireless Mouse at Main Warehouse has 1.0000 units remaining.', '/inventory', NULL, '2026-09-28 17:09:49', '2026-09-28 17:09:49'),
(11, NULL, 'danger', 'Out of stock', 'HP Laptop 15 at Main Warehouse has 0.0000 units remaining.', '/inventory', NULL, '2026-09-28 17:46:38', '2026-09-28 17:46:38'),
(12, NULL, 'danger', 'Out of stock', 'Logitech Keyboard K120 at Main Warehouse has 0.0000 units remaining.', '/inventory', NULL, '2026-09-28 17:46:38', '2026-09-28 17:46:38'),
(13, NULL, 'danger', 'Out of stock', 'Dell 24-inch Monitor at Main Warehouse has 0.0000 units remaining.', '/inventory', NULL, '2026-09-29 14:03:17', '2026-09-29 14:03:17');

-- --------------------------------------------------------

--
-- Table structure for table `tax_rates`
--

CREATE TABLE `tax_rates` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(40) NOT NULL,
  `name` varchar(120) NOT NULL,
  `rate` decimal(8,4) NOT NULL DEFAULT 0.0000,
  `type` varchar(20) NOT NULL DEFAULT 'vat',
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tax_rates`
--

INSERT INTO `tax_rates` (`id`, `code`, `name`, `rate`, `type`, `is_default`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'TAX-0', 'Zero Rated', 0.0000, 'vat', 0, 1, '2026-10-23 06:20:12', '2026-10-23 06:20:12'),
(2, 'VAT-5', 'VAT 5%', 5.0000, 'vat', 0, 1, '2026-10-23 06:20:12', '2026-10-23 06:20:12'),
(3, 'VAT-7.5', 'VAT 7.5%', 7.5000, 'vat', 0, 1, '2026-10-23 06:20:12', '2026-10-23 06:20:12'),
(4, 'VAT-10', 'VAT 10%', 10.0000, 'vat', 0, 1, '2026-10-23 06:20:12', '2026-10-23 06:20:12'),
(5, 'VAT-15', 'VAT 15%', 15.0000, 'vat', 0, 1, '2026-10-23 06:20:12', '2026-10-23 06:20:12');

-- --------------------------------------------------------

--
-- Table structure for table `units`
--

CREATE TABLE `units` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `short_name` varchar(30) DEFAULT NULL,
  `allow_decimal` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `units`
--

INSERT INTO `units` (`id`, `name`, `short_name`, `allow_decimal`, `is_active`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Piece', 'PCS', 0, 1, '2026-08-27 18:00:00', '2026-08-27 18:00:00', NULL),
(2, 'Kilogram', 'KG', 1, 1, '2026-08-27 18:00:00', '2026-08-27 18:00:00', NULL),
(3, 'Gram', 'GM', 1, 1, '2026-08-27 18:00:00', '2026-08-27 18:00:00', NULL),
(4, 'Liter', 'LTR', 1, 1, '2026-08-27 18:00:00', '2026-08-27 18:00:00', NULL),
(5, 'Meter', 'MTR', 1, 1, '2026-08-27 18:00:00', '2026-08-27 18:00:00', NULL),
(6, 'Box', 'BOX', 0, 1, '2026-08-27 18:00:00', '2026-08-27 18:00:00', NULL),
(7, 'Carton', 'CTN', 0, 1, '2026-08-27 18:00:00', '2026-08-27 18:00:00', NULL),
(8, 'Pack', 'PACK', 0, 1, '2026-08-27 18:00:00', '2026-08-27 18:00:00', NULL),
(9, 'Set', 'SET', 0, 1, '2026-08-27 18:00:00', '2026-08-27 18:00:00', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'active',
  `last_login_at` timestamp NULL DEFAULT NULL,
  `last_login_ip` varchar(45) DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `seller_code` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `username`, `email`, `phone`, `email_verified_at`, `password`, `status`, `last_login_at`, `last_login_ip`, `remember_token`, `created_at`, `updated_at`, `seller_code`) VALUES
(1, 'Super Admin', 'admin', 'iamdashingcoder@gmail.com', NULL, NULL, '$2y$12$7ZYtim.YVP3D0SyGSqAfG.RwAsg06uFX8QrBVrAO/vvOvmgT1OF2C', 'active', '2026-10-22 00:29:51', '127.0.0.1', 'A04xxriLrvR3vmI7ocQhYbumkqV2UtBgkeGqRxnrcGTkLko4NOSBQTbvMyDO', '2026-08-27 18:00:00', '2026-10-22 00:29:51', NULL),
(3, 'Seller', 'seller', 'seller@seller.com', NULL, NULL, '$2y$12$u/Tz4r.g6m7oshOugCdVKexujG0JIdm5Mksus8lSN8AVMeubKISj.', 'active', '2026-10-12 09:51:47', '103.115.116.85', 'a6NIgKOp1K0TyzPg2GvfF6LfrV6Z2hrFZLKZcQSqVNNOZWlvaDAWlaut7KOa', '2026-09-28 16:03:53', '2026-10-12 09:51:47', 'SEL-00003'),
(4, 'Md Nasir Uddin', 'NasirUddin', 'nasir@gmail.com', '01734685525', NULL, '$2y$12$WfeSmhHqco0V0VuZGWMPHuLbegaLwPg4cLnzYkoforUxlcxTuFnHC', 'active', '2026-10-08 11:39:12', '202.191.122.185', NULL, '2026-10-08 11:30:36', '2026-10-08 11:39:12', 'SEL-00004');

-- --------------------------------------------------------

--
-- Table structure for table `warehouses`
--

CREATE TABLE `warehouses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(50) NOT NULL,
  `name` varchar(150) NOT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `warehouses`
--

INSERT INTO `warehouses` (`id`, `code`, `name`, `phone`, `email`, `address`, `is_default`, `is_active`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'WH-001', 'Main Warehouse', NULL, NULL, NULL, 1, 1, '2026-08-27 18:00:00', '2026-08-27 18:00:00', NULL),
(2, 'WH-002', 'Branch Warehouse', NULL, NULL, 'Uttara, Dhaka', 0, 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(3, 'WH-003', 'Secondary Warehouse', NULL, NULL, 'Mirpur, Dhaka', 0, 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL),
(4, 'WH-004', 'Showroom', NULL, NULL, 'Dhanmondi, Dhaka', 0, 1, '2026-08-28 03:30:00', '2026-08-28 03:30:00', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `warehouse_stocks`
--

CREATE TABLE `warehouse_stocks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `warehouse_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` decimal(18,4) NOT NULL DEFAULT 0.0000,
  `reserved_quantity` decimal(18,4) NOT NULL DEFAULT 0.0000,
  `average_cost` decimal(18,4) NOT NULL DEFAULT 0.0000,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `warehouse_stocks`
--

INSERT INTO `warehouse_stocks` (`id`, `warehouse_id`, `product_id`, `quantity`, `reserved_quantity`, `average_cost`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 2.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(2, 1, 2, 0.0000, 0.0000, 0.0000, '2026-08-28 03:30:00', '2026-09-28 17:46:38'),
(3, 1, 3, 2.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(4, 1, 4, 2.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(5, 1, 5, 2.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(6, 1, 6, 2.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(7, 1, 7, 2.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(8, 1, 8, 2.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(9, 1, 9, 2.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(10, 1, 10, 2.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(11, 1, 11, 2.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(12, 1, 12, 0.0000, 0.0000, 0.0000, '2026-08-28 03:30:00', '2026-09-28 17:46:38'),
(13, 1, 13, 0.0000, 0.0000, 0.0000, '2026-08-28 03:30:00', '2026-09-29 14:03:17'),
(14, 1, 14, 2.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(15, 1, 15, 2.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(16, 1, 16, 2.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(17, 1, 17, 2.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(18, 1, 18, 2.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(19, 1, 19, 2.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(20, 1, 20, 1.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-09-28 17:09:49'),
(21, 1, 21, 2.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(22, 1, 22, 2.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(23, 1, 23, 2.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(24, 1, 24, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(25, 1, 25, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(26, 1, 26, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(27, 1, 27, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(28, 1, 28, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(29, 1, 29, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(30, 1, 30, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(31, 1, 31, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(32, 1, 32, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(33, 1, 33, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(34, 1, 34, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(35, 1, 35, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(36, 1, 36, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(37, 1, 37, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(38, 1, 38, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(39, 1, 39, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(40, 1, 40, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(41, 1, 41, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(42, 1, 42, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(43, 1, 43, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(44, 1, 44, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(45, 1, 45, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(46, 1, 46, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(47, 1, 47, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(48, 1, 48, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(49, 1, 49, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(50, 1, 50, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(51, 1, 51, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(52, 1, 52, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(53, 1, 53, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(54, 1, 54, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(55, 1, 55, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(56, 1, 56, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(57, 1, 57, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(58, 1, 58, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(59, 1, 59, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(60, 1, 60, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(61, 1, 61, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(62, 1, 62, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(63, 1, 63, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(64, 1, 64, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(65, 1, 65, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(66, 1, 66, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(67, 1, 67, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(68, 1, 68, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(69, 1, 69, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(70, 1, 70, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(71, 1, 71, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(72, 1, 72, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(73, 1, 73, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(74, 1, 74, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(75, 1, 75, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(76, 1, 76, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(77, 1, 77, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(78, 1, 78, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(79, 1, 79, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(80, 1, 80, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(81, 1, 81, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(82, 1, 82, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(83, 1, 83, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(84, 1, 84, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(85, 1, 85, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(86, 1, 86, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(87, 1, 87, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(88, 1, 88, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(89, 1, 89, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(90, 1, 90, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(91, 1, 91, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(92, 1, 92, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(93, 1, 93, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(94, 1, 94, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(95, 1, 95, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(96, 1, 96, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(97, 1, 97, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(98, 1, 98, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(99, 1, 99, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(100, 1, 100, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(101, 1, 101, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(102, 1, 102, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(103, 1, 103, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(104, 1, 104, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(105, 1, 105, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(106, 1, 106, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(107, 1, 107, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(108, 1, 108, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(109, 1, 109, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(110, 1, 110, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(111, 1, 111, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(112, 1, 112, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(113, 1, 113, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(114, 1, 114, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(115, 1, 115, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(116, 1, 116, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(117, 1, 117, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(118, 1, 118, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(119, 1, 119, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(120, 1, 120, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(121, 1, 121, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(122, 1, 122, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(123, 1, 123, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(124, 1, 124, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(125, 1, 125, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(126, 1, 126, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(127, 1, 127, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(128, 1, 128, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(129, 1, 129, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(130, 1, 130, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(131, 1, 131, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(132, 1, 132, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(133, 1, 133, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(134, 1, 134, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(135, 1, 135, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(136, 1, 136, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(137, 1, 137, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(138, 1, 138, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(139, 1, 139, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(140, 1, 140, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(141, 1, 141, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(142, 1, 142, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(143, 1, 143, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(144, 1, 144, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(145, 1, 145, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(146, 1, 146, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(147, 1, 147, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(148, 1, 148, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(149, 1, 149, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(150, 1, 150, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(151, 1, 151, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(152, 1, 152, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(153, 1, 153, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(154, 1, 154, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(155, 1, 155, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(156, 1, 156, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(157, 1, 157, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(158, 1, 158, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(159, 1, 159, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(160, 1, 160, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(161, 1, 161, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(162, 1, 162, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(163, 1, 163, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(164, 1, 164, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(165, 1, 165, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(166, 1, 166, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(167, 1, 167, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(168, 1, 168, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(169, 1, 169, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(170, 1, 170, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(171, 1, 171, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(172, 1, 172, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(173, 1, 173, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(174, 1, 174, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(175, 1, 175, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(176, 1, 176, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(177, 1, 177, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(178, 1, 178, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(179, 1, 179, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(180, 1, 180, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(181, 1, 181, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(182, 1, 182, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(183, 1, 183, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(184, 1, 184, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(185, 1, 185, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(186, 1, 186, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(187, 1, 187, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(188, 1, 188, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(189, 1, 189, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(190, 1, 190, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(191, 1, 191, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(192, 1, 192, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(193, 1, 193, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(194, 1, 194, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(195, 1, 195, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(196, 1, 196, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(197, 1, 197, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(198, 1, 198, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(199, 1, 199, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(200, 1, 200, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(201, 1, 201, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(202, 1, 202, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(203, 1, 203, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(204, 1, 204, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(205, 1, 205, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(206, 1, 206, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(207, 1, 207, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(208, 1, 208, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(209, 1, 209, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(210, 1, 210, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(211, 1, 211, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(212, 1, 212, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(213, 1, 213, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(214, 1, 214, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(215, 1, 215, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(216, 1, 216, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(217, 1, 217, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(218, 1, 218, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(219, 1, 219, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(220, 1, 220, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(221, 1, 221, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(222, 1, 222, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(223, 1, 223, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(224, 1, 224, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(225, 1, 225, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(226, 1, 226, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(227, 1, 227, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(228, 1, 228, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(229, 1, 229, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(230, 1, 230, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(231, 1, 231, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(232, 1, 232, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(233, 1, 233, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(234, 1, 234, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(235, 1, 235, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(236, 1, 236, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(237, 1, 237, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(238, 1, 238, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(239, 1, 239, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(240, 1, 240, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(241, 1, 241, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(242, 1, 242, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(243, 1, 243, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(244, 1, 244, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(245, 1, 245, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(246, 1, 246, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(247, 1, 247, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(248, 1, 248, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(249, 1, 249, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(250, 1, 250, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(251, 1, 251, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(252, 1, 252, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(253, 1, 253, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(254, 1, 254, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(255, 1, 255, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(256, 1, 256, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(257, 1, 257, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(258, 1, 258, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(259, 1, 259, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(260, 1, 260, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(261, 1, 261, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(262, 1, 262, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(263, 1, 263, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(264, 1, 264, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(265, 1, 265, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(266, 1, 266, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(267, 1, 267, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(268, 1, 268, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(269, 1, 269, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(270, 1, 270, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(271, 1, 271, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(272, 1, 272, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(273, 1, 273, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(274, 1, 274, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(275, 1, 275, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(276, 1, 276, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(277, 1, 277, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(278, 1, 278, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(279, 1, 279, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(280, 1, 280, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(281, 1, 281, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(282, 1, 282, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(283, 1, 283, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(284, 1, 284, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(285, 1, 285, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(286, 1, 286, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(287, 1, 287, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(288, 1, 288, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(289, 1, 289, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(290, 1, 290, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(291, 1, 291, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(292, 1, 292, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(293, 1, 293, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(294, 1, 294, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(295, 1, 295, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(296, 1, 296, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(297, 1, 297, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(298, 1, 298, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(299, 1, 299, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(300, 1, 300, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(301, 1, 301, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(302, 1, 302, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(303, 1, 303, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(304, 1, 304, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(305, 1, 305, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(306, 1, 306, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(307, 1, 307, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(308, 1, 308, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(309, 1, 309, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(310, 1, 310, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(311, 1, 311, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(312, 1, 312, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(313, 1, 313, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(314, 1, 314, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(315, 1, 315, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(316, 1, 316, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(317, 1, 317, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(318, 1, 318, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(319, 1, 319, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(320, 1, 320, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(321, 1, 321, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(322, 1, 322, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(323, 1, 323, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(324, 1, 324, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(325, 1, 325, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(326, 1, 326, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(327, 1, 327, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(328, 1, 328, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(329, 1, 329, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(330, 1, 330, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(331, 1, 331, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(332, 1, 332, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(333, 1, 333, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(334, 1, 334, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(335, 1, 335, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(336, 1, 336, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(337, 1, 337, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(338, 1, 338, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(339, 1, 339, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(340, 1, 340, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(341, 1, 341, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(342, 1, 342, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(343, 1, 343, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(344, 1, 344, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(345, 1, 345, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(346, 1, 346, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(347, 1, 347, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(348, 1, 348, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(349, 1, 349, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(350, 1, 350, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(351, 1, 351, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(352, 1, 352, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(353, 1, 353, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(354, 1, 354, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(355, 1, 355, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(356, 1, 356, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(357, 1, 357, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(358, 1, 358, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(359, 1, 359, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(360, 1, 360, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(361, 1, 361, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(362, 1, 362, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(363, 1, 363, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(364, 1, 364, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(365, 1, 365, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(366, 1, 366, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(367, 1, 367, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(368, 1, 368, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(369, 1, 369, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(370, 1, 370, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(371, 1, 371, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(372, 1, 372, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(373, 1, 373, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(374, 1, 374, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(375, 1, 375, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(376, 1, 376, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(377, 1, 377, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(378, 1, 378, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(379, 1, 379, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(380, 1, 380, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(381, 1, 381, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(382, 1, 382, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(383, 1, 383, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(384, 1, 384, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(385, 1, 385, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(386, 1, 386, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(387, 1, 387, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(388, 1, 388, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(389, 1, 389, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(390, 1, 390, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(391, 1, 391, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(392, 1, 392, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(393, 1, 393, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(394, 1, 394, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(395, 1, 395, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(396, 1, 396, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(397, 1, 397, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(398, 1, 398, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(399, 1, 399, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(400, 1, 400, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(401, 1, 401, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(402, 1, 402, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(403, 1, 403, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(404, 1, 404, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(405, 1, 405, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(406, 1, 406, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(407, 1, 407, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(408, 1, 408, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(409, 1, 409, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(410, 1, 410, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(411, 1, 411, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(412, 1, 412, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(413, 1, 413, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(414, 1, 414, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(415, 1, 415, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(416, 1, 416, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(417, 1, 417, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(418, 1, 418, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(419, 1, 419, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(420, 1, 420, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(421, 1, 421, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(422, 1, 422, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(423, 1, 423, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(424, 1, 424, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(425, 1, 425, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(426, 1, 426, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(427, 1, 427, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(428, 1, 428, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(429, 1, 429, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(430, 1, 430, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(431, 1, 431, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(432, 1, 432, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(433, 1, 433, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(434, 1, 434, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(435, 1, 435, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(436, 1, 436, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(437, 1, 437, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(438, 1, 438, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(439, 1, 439, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(440, 1, 440, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(441, 1, 441, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(442, 1, 442, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(443, 1, 443, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(444, 1, 444, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(445, 1, 445, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(446, 1, 446, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(447, 1, 447, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(448, 1, 448, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(449, 1, 449, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(450, 1, 450, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(451, 1, 451, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(452, 1, 452, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(453, 1, 453, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(454, 1, 454, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(455, 1, 455, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(456, 1, 456, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(457, 1, 457, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(458, 1, 458, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(459, 1, 459, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(460, 1, 460, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(461, 1, 461, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(462, 1, 462, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(463, 1, 463, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(464, 1, 464, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(465, 1, 465, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(466, 1, 466, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(467, 1, 467, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(468, 1, 468, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(469, 1, 469, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(470, 1, 470, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(471, 1, 471, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(472, 1, 472, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(473, 1, 473, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(474, 1, 474, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(475, 1, 475, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(476, 1, 476, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(477, 1, 477, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(478, 1, 478, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(479, 1, 479, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(480, 1, 480, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(481, 1, 481, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(482, 1, 482, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(483, 2, 100, 1267.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(484, 2, 101, 40.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(485, 2, 102, 40.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(486, 2, 103, 40.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(487, 2, 104, 40.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(488, 2, 105, 40.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(489, 2, 106, 40.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(490, 2, 107, 40.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(491, 2, 108, 40.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(492, 2, 109, 40.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(493, 2, 110, 40.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(494, 2, 111, 40.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(495, 2, 112, 40.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(496, 2, 113, 40.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(497, 2, 114, 40.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(498, 2, 115, 40.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(499, 2, 116, 40.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(500, 2, 117, 40.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(501, 2, 118, 40.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(502, 2, 119, 40.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(503, 2, 120, 40.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(504, 2, 121, 40.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(505, 2, 122, 40.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(506, 2, 123, 40.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(507, 2, 124, 40.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(508, 2, 125, 40.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(509, 2, 126, 40.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(510, 2, 127, 40.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(511, 2, 128, 40.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(512, 2, 129, 40.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(513, 3, 200, 30.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(514, 3, 201, 30.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(515, 3, 202, 30.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(516, 3, 203, 30.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(517, 3, 204, 30.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(518, 3, 205, 30.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(519, 3, 206, 30.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(520, 3, 207, 30.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(521, 3, 208, 30.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(522, 3, 209, 30.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(523, 3, 210, 30.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(524, 3, 211, 30.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(525, 3, 212, 30.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(526, 3, 213, 30.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(527, 3, 214, 30.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(528, 3, 215, 30.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(529, 3, 216, 30.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(530, 3, 217, 30.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(531, 3, 218, 30.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(532, 3, 219, 30.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(533, 4, 300, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(534, 4, 301, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(535, 4, 302, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(536, 4, 303, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(537, 4, 304, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(538, 4, 305, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(539, 4, 306, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(540, 4, 307, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(541, 4, 308, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(542, 4, 309, 20.0000, 0.0000, 100.0000, '2026-08-28 03:30:00', '2026-08-28 03:30:00'),
(543, 2, 6, 1.0001, 0.0000, 100.0000, '2026-09-26 19:20:21', '2026-09-26 19:20:21');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounting_periods`
--
ALTER TABLE `accounting_periods`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `accounting_periods_dates_unique` (`start_date`,`end_date`),
  ADD KEY `accounting_periods_status_dates_index` (`status`,`start_date`,`end_date`),
  ADD KEY `accounting_periods_closed_by_foreign` (`closed_by`);

--
-- Indexes for table `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `accounts_code_unique` (`code`),
  ADD KEY `accounts_type_active_idx` (`type`,`is_active`),
  ADD KEY `accounts_parent_fk` (`parent_id`);

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`,`created_at`);

--
-- Indexes for table `approval_requests`
--
ALTER TABLE `approval_requests`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `approval_requests_approvable_unique` (`approvable_type`,`approvable_id`),
  ADD KEY `approval_requests_status_created_index` (`status`,`created_at`),
  ADD KEY `approval_requests_requested_by_foreign` (`requested_by`),
  ADD KEY `approval_requests_decided_by_foreign` (`decided_by`);

--
-- Indexes for table `bank_reconciliations`
--
ALTER TABLE `bank_reconciliations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bank_reconciliations_account_date_index` (`account_id`,`statement_date`),
  ADD KEY `bank_reconciliations_created_by_foreign` (`created_by`),
  ADD KEY `bank_reconciliations_closed_by_foreign` (`closed_by`);

--
-- Indexes for table `bank_reconciliation_items`
--
ALTER TABLE `bank_reconciliation_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `bank_reconciliation_items_unique` (`bank_reconciliation_id`,`journal_line_id`),
  ADD KEY `bank_reconciliation_items_journal_line_foreign` (`journal_line_id`);

--
-- Indexes for table `brands`
--
ALTER TABLE `brands`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `budgets`
--
ALTER TABLE `budgets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `budgets_period_index` (`period_start`,`period_end`),
  ADD KEY `budgets_cost_center_foreign` (`cost_center_id`),
  ADD KEY `budgets_project_foreign` (`project_id`),
  ADD KEY `budgets_account_foreign` (`account_id`);

--
-- Indexes for table `business_days`
--
ALTER TABLE `business_days`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `business_days_business_date_unique` (`business_date`),
  ADD KEY `business_days_status_business_date_index` (`status`,`business_date`),
  ADD KEY `business_days_opened_by_foreign` (`opened_by`),
  ADD KEY `business_days_closed_by_foreign` (`closed_by`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `categories_parent_fk` (`parent_id`);

--
-- Indexes for table `cost_centers`
--
ALTER TABLE `cost_centers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cost_centers_code_unique` (`code`);

--
-- Indexes for table `currencies`
--
ALTER TABLE `currencies`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `currencies_code_unique` (`code`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `customer_group_id` (`customer_group_id`),
  ADD KEY `name` (`name`),
  ADD KEY `phone` (`phone`),
  ADD KEY `customers_market_hierarchy_id_foreign` (`market_hierarchy_id`);

--
-- Indexes for table `customer_groups`
--
ALTER TABLE `customer_groups`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `customer_ledger_entries`
--
ALTER TABLE `customer_ledger_entries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`,`transaction_date`),
  ADD KEY `source_type` (`source_type`,`source_id`),
  ADD KEY `cle_user_fk` (`created_by`);

--
-- Indexes for table `customer_payments`
--
ALTER TABLE `customer_payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `payment_no` (`payment_no`),
  ADD KEY `cp_customer_fk` (`customer_id`),
  ADD KEY `cp_sale_fk` (`sale_id`),
  ADD KEY `cp_account_fk` (`account_id`);

--
-- Indexes for table `document_attachments`
--
ALTER TABLE `document_attachments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `document_attachments_attachable_index` (`attachable_type`,`attachable_id`),
  ADD KEY `document_attachments_uploaded_by_foreign` (`uploaded_by`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `employee_no` (`employee_no`);

--
-- Indexes for table `exchange_rates`
--
ALTER TABLE `exchange_rates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `exchange_rates_currency_date_unique` (`currency_code`,`rate_date`);

--
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `expense_no` (`expense_no`),
  ADD KEY `e_category_fk` (`expense_category_id`),
  ADD KEY `e_expense_account_fk` (`expense_account_id`),
  ADD KEY `e_payment_account_fk` (`payment_account_id`),
  ADD KEY `expenses_cost_center_id_foreign` (`cost_center_id`),
  ADD KEY `expenses_project_id_foreign` (`project_id`);

--
-- Indexes for table `expense_categories`
--
ALTER TABLE `expense_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD KEY `ec_account_fk` (`expense_account_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `fixed_assets`
--
ALTER TABLE `fixed_assets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `fixed_assets_asset_no_unique` (`asset_no`);

--
-- Indexes for table `fixed_asset_depreciations`
--
ALTER TABLE `fixed_asset_depreciations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `fixed_asset_depreciations_unique` (`fixed_asset_id`,`depreciation_date`),
  ADD KEY `fixed_asset_depreciations_created_by_foreign` (`created_by`);

--
-- Indexes for table `inventory_batches`
--
ALTER TABLE `inventory_batches`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `inv_batch_unique` (`warehouse_id`,`product_id`,`batch_no`),
  ADD KEY `inventory_batches_expires_at_status_index` (`expires_at`,`status`),
  ADD KEY `inventory_batches_product_id_foreign` (`product_id`);

--
-- Indexes for table `inventory_cost_layers`
--
ALTER TABLE `inventory_cost_layers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cost_layer_lookup` (`warehouse_id`,`product_id`,`transaction_date`),
  ADD KEY `inventory_cost_layers_source_type_source_id_index` (`source_type`,`source_id`),
  ADD KEY `inventory_cost_layers_product_id_foreign` (`product_id`),
  ADD KEY `inventory_cost_layers_inventory_batch_id_foreign` (`inventory_batch_id`);

--
-- Indexes for table `inventory_reservations`
--
ALTER TABLE `inventory_reservations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `inventory_reservations_stock_status_index` (`warehouse_id`,`product_id`,`status`),
  ADD KEY `inventory_reservations_product_foreign` (`product_id`),
  ADD KEY `inventory_reservations_created_by_foreign` (`created_by`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `journal_entries`
--
ALTER TABLE `journal_entries`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `journal_no_unique` (`journal_no`),
  ADD UNIQUE KEY `journal_reference_unique` (`reference_type`,`reference_id`),
  ADD KEY `journal_date_status_idx` (`journal_date`,`status`),
  ADD KEY `je_created_by_fk` (`created_by`),
  ADD KEY `je_posted_by_fk` (`posted_by`);

--
-- Indexes for table `journal_lines`
--
ALTER TABLE `journal_lines`
  ADD PRIMARY KEY (`id`),
  ADD KEY `journal_lines_account_idx` (`account_id`,`journal_entry_id`),
  ADD KEY `jl_entry_fk` (`journal_entry_id`),
  ADD KEY `journal_lines_cost_center_id_foreign` (`cost_center_id`),
  ADD KEY `journal_lines_project_id_foreign` (`project_id`);

--
-- Indexes for table `loans`
--
ALTER TABLE `loans`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `loans_loan_no_unique` (`loan_no`),
  ADD KEY `loans_direction_status_index` (`direction`,`status`),
  ADD KEY `loans_due_date_index` (`due_date`),
  ADD KEY `loans_created_by_foreign` (`created_by`);

--
-- Indexes for table `loan_installments`
--
ALTER TABLE `loan_installments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `loan_installments_loan_id_installment_no_unique` (`loan_id`,`installment_no`),
  ADD KEY `loan_installments_due_date_status_index` (`due_date`,`status`);

--
-- Indexes for table `loan_payments`
--
ALTER TABLE `loan_payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `loan_payments_payment_no_unique` (`payment_no`),
  ADD KEY `loan_payments_loan_id_payment_date_index` (`loan_id`,`payment_date`),
  ADD KEY `loan_payments_created_by_foreign` (`created_by`);

--
-- Indexes for table `market_hierarchies`
--
ALTER TABLE `market_hierarchies`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `market_hierarchies_code_unique` (`code`),
  ADD KEY `market_hierarchies_parent_id_sort_order_index` (`parent_id`,`sort_order`),
  ADD KEY `market_hierarchies_level_type_is_active_index` (`level_type`,`is_active`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `payroll_items`
--
ALTER TABLE `payroll_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `payroll_run_id` (`payroll_run_id`,`employee_id`),
  ADD KEY `payi_employee_fk` (`employee_id`);

--
-- Indexes for table `payroll_payments`
--
ALTER TABLE `payroll_payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `payment_no` (`payment_no`),
  ADD KEY `payp_item_fk` (`payroll_item_id`),
  ADD KEY `payp_account_fk` (`payment_account_id`);

--
-- Indexes for table `payroll_runs`
--
ALTER TABLE `payroll_runs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `payroll_no` (`payroll_no`),
  ADD UNIQUE KEY `period` (`period`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `pos_checkout_requests`
--
ALTER TABLE `pos_checkout_requests`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `pos_checkout_requests_token_unique` (`token`),
  ADD KEY `pos_checkout_requests_sale_id_foreign` (`sale_id`),
  ADD KEY `pos_checkout_requests_user_id_created_at_index` (`user_id`,`created_at`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sku` (`sku`),
  ADD UNIQUE KEY `code` (`code`),
  ADD UNIQUE KEY `barcode` (`barcode`),
  ADD KEY `products_category_fk` (`category_id`),
  ADD KEY `products_brand_fk` (`brand_id`),
  ADD KEY `products_unit_fk` (`unit_id`),
  ADD KEY `products_parent_product_id_is_active_index` (`parent_product_id`,`is_active`);

--
-- Indexes for table `product_deliveries`
--
ALTER TABLE `product_deliveries`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `product_deliveries_delivery_no_unique` (`delivery_no`),
  ADD KEY `product_deliveries_warehouse_id_foreign` (`warehouse_id`),
  ADD KEY `product_deliveries_created_by_foreign` (`created_by`),
  ADD KEY `product_deliveries_posted_by_foreign` (`posted_by`),
  ADD KEY `product_deliveries_delivery_type_delivery_date_index` (`delivery_type`,`delivery_date`),
  ADD KEY `product_deliveries_purchase_id_status_index` (`purchase_id`,`status`),
  ADD KEY `product_deliveries_sale_id_status_index` (`sale_id`,`status`);

--
-- Indexes for table `product_delivery_items`
--
ALTER TABLE `product_delivery_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `product_delivery_items_product_delivery_id_source_item_id_unique` (`product_delivery_id`,`source_item_id`),
  ADD KEY `product_delivery_items_product_id_foreign` (`product_id`),
  ADD KEY `product_delivery_items_source_item_id_product_id_index` (`source_item_id`,`product_id`);

--
-- Indexes for table `product_serials`
--
ALTER TABLE `product_serials`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `product_serials_serial_no_unique` (`serial_no`),
  ADD UNIQUE KEY `product_serials_imei_unique` (`imei`),
  ADD KEY `product_serials_product_status_index` (`product_id`,`status`),
  ADD KEY `product_serials_warehouse_foreign` (`warehouse_id`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `projects_code_unique` (`code`),
  ADD KEY `projects_cost_center_foreign` (`cost_center_id`);

--
-- Indexes for table `purchases`
--
ALTER TABLE `purchases`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `purchases_no_unique` (`purchase_no`),
  ADD KEY `supplier_id` (`supplier_id`),
  ADD KEY `warehouse_id` (`warehouse_id`);

--
-- Indexes for table `purchase_items`
--
ALTER TABLE `purchase_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pi_product_fk` (`product_id`),
  ADD KEY `purchase_items_purchase_product_index` (`purchase_id`,`product_id`);

--
-- Indexes for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `purchase_orders_order_no_unique` (`order_no`),
  ADD KEY `purchase_orders_supplier_id_foreign` (`supplier_id`),
  ADD KEY `purchase_orders_warehouse_id_foreign` (`warehouse_id`),
  ADD KEY `purchase_orders_created_by_foreign` (`created_by`);

--
-- Indexes for table `purchase_order_items`
--
ALTER TABLE `purchase_order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `purchase_order_items_order_foreign` (`purchase_order_id`),
  ADD KEY `purchase_order_items_product_foreign` (`product_id`);

--
-- Indexes for table `purchase_returns`
--
ALTER TABLE `purchase_returns`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `return_no` (`return_no`),
  ADD KEY `pr_purchase_fk` (`purchase_id`),
  ADD KEY `pr_supplier_fk` (`supplier_id`),
  ADD KEY `pr_warehouse_fk` (`warehouse_id`);

--
-- Indexes for table `purchase_return_items`
--
ALTER TABLE `purchase_return_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pri_return_fk` (`purchase_return_id`),
  ADD KEY `pri_product_fk` (`product_id`);

--
-- Indexes for table `quotations`
--
ALTER TABLE `quotations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `quotation_no` (`quotation_no`),
  ADD KEY `quotation_customer_fk` (`customer_id`);

--
-- Indexes for table `quotation_items`
--
ALTER TABLE `quotation_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `qi_quotation_fk` (`quotation_id`),
  ADD KEY `qi_product_fk` (`product_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `rhp_role_fk` (`role_id`);

--
-- Indexes for table `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `invoice_no` (`invoice_no`),
  ADD KEY `sales_customer_fk` (`customer_id`),
  ADD KEY `sales_warehouse_fk` (`warehouse_id`);

--
-- Indexes for table `sales_orders`
--
ALTER TABLE `sales_orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sales_orders_order_no_unique` (`order_no`),
  ADD KEY `sales_orders_customer_id_foreign` (`customer_id`),
  ADD KEY `sales_orders_warehouse_id_foreign` (`warehouse_id`),
  ADD KEY `sales_orders_created_by_foreign` (`created_by`);

--
-- Indexes for table `sales_order_items`
--
ALTER TABLE `sales_order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sales_order_items_order_foreign` (`sales_order_id`),
  ADD KEY `sales_order_items_product_foreign` (`product_id`);

--
-- Indexes for table `sale_items`
--
ALTER TABLE `sale_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sale_id` (`sale_id`,`product_id`),
  ADD KEY `si_product_fk` (`product_id`),
  ADD KEY `sale_items_sale_id_product_id_index` (`sale_id`,`product_id`);

--
-- Indexes for table `sale_returns`
--
ALTER TABLE `sale_returns`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `return_no` (`return_no`),
  ADD KEY `sr_sale_fk` (`sale_id`),
  ADD KEY `sr_customer_fk` (`customer_id`),
  ADD KEY `sr_warehouse_fk` (`warehouse_id`);

--
-- Indexes for table `sale_return_items`
--
ALTER TABLE `sale_return_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sri_return_fk` (`sale_return_id`),
  ADD KEY `sri_product_fk` (`product_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `key` (`key`);

--
-- Indexes for table `stock_adjustments`
--
ALTER TABLE `stock_adjustments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `adjustment_no` (`adjustment_no`),
  ADD KEY `sa_warehouse_fk` (`warehouse_id`),
  ADD KEY `sa_created_by_fk` (`created_by`),
  ADD KEY `sa_posted_by_fk` (`posted_by`);

--
-- Indexes for table `stock_adjustment_items`
--
ALTER TABLE `stock_adjustment_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `stock_adjustment_id` (`stock_adjustment_id`,`product_id`),
  ADD KEY `sai_product_fk` (`product_id`);

--
-- Indexes for table `stock_movements`
--
ALTER TABLE `stock_movements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `warehouse_id` (`warehouse_id`,`product_id`,`transaction_date`),
  ADD KEY `source_type` (`source_type`,`source_id`),
  ADD KEY `reference_no` (`reference_no`),
  ADD KEY `sm_product_fk` (`product_id`),
  ADD KEY `sm_user_fk` (`created_by`);

--
-- Indexes for table `stock_transfers`
--
ALTER TABLE `stock_transfers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `transfer_no` (`transfer_no`),
  ADD KEY `st_from_fk` (`from_warehouse_id`),
  ADD KEY `st_to_fk` (`to_warehouse_id`),
  ADD KEY `st_created_by_fk` (`created_by`),
  ADD KEY `st_posted_by_fk` (`posted_by`);

--
-- Indexes for table `stock_transfer_items`
--
ALTER TABLE `stock_transfer_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `stock_transfer_id` (`stock_transfer_id`,`product_id`),
  ADD KEY `sti_product_fk` (`product_id`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `supplier_group_id` (`supplier_group_id`),
  ADD KEY `name` (`name`),
  ADD KEY `phone` (`phone`);

--
-- Indexes for table `supplier_groups`
--
ALTER TABLE `supplier_groups`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `supplier_ledger_entries`
--
ALTER TABLE `supplier_ledger_entries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `supplier_id` (`supplier_id`,`transaction_date`),
  ADD KEY `source_type` (`source_type`,`source_id`),
  ADD KEY `sle_user_fk` (`created_by`);

--
-- Indexes for table `supplier_payments`
--
ALTER TABLE `supplier_payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `payment_no` (`payment_no`),
  ADD KEY `sp_supplier_fk` (`supplier_id`),
  ADD KEY `sp_purchase_fk` (`purchase_id`),
  ADD KEY `sp_account_fk` (`account_id`);

--
-- Indexes for table `system_notifications`
--
ALTER TABLE `system_notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`,`read_at`);

--
-- Indexes for table `tax_rates`
--
ALTER TABLE `tax_rates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tax_rates_code_unique` (`code`);

--
-- Indexes for table `units`
--
ALTER TABLE `units`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_username_unique` (`username`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_seller_code_unique` (`seller_code`);

--
-- Indexes for table `warehouses`
--
ALTER TABLE `warehouses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `warehouse_stocks`
--
ALTER TABLE `warehouse_stocks`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `warehouse_product_unique` (`warehouse_id`,`product_id`),
  ADD KEY `ws_product_fk` (`product_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accounting_periods`
--
ALTER TABLE `accounting_periods`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `accounts`
--
ALTER TABLE `accounts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=192;

--
-- AUTO_INCREMENT for table `approval_requests`
--
ALTER TABLE `approval_requests`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bank_reconciliations`
--
ALTER TABLE `bank_reconciliations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bank_reconciliation_items`
--
ALTER TABLE `bank_reconciliation_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `brands`
--
ALTER TABLE `brands`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `budgets`
--
ALTER TABLE `budgets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `business_days`
--
ALTER TABLE `business_days`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `cost_centers`
--
ALTER TABLE `cost_centers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `currencies`
--
ALTER TABLE `currencies`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=157;

--
-- AUTO_INCREMENT for table `customer_groups`
--
ALTER TABLE `customer_groups`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `customer_ledger_entries`
--
ALTER TABLE `customer_ledger_entries`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=92;

--
-- AUTO_INCREMENT for table `customer_payments`
--
ALTER TABLE `customer_payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `document_attachments`
--
ALTER TABLE `document_attachments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `exchange_rates`
--
ALTER TABLE `exchange_rates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `expense_categories`
--
ALTER TABLE `expense_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fixed_assets`
--
ALTER TABLE `fixed_assets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fixed_asset_depreciations`
--
ALTER TABLE `fixed_asset_depreciations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventory_batches`
--
ALTER TABLE `inventory_batches`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventory_cost_layers`
--
ALTER TABLE `inventory_cost_layers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=541;

--
-- AUTO_INCREMENT for table `inventory_reservations`
--
ALTER TABLE `inventory_reservations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `journal_entries`
--
ALTER TABLE `journal_entries`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `journal_lines`
--
ALTER TABLE `journal_lines`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=104;

--
-- AUTO_INCREMENT for table `loans`
--
ALTER TABLE `loans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `loan_installments`
--
ALTER TABLE `loan_installments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `loan_payments`
--
ALTER TABLE `loan_payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `market_hierarchies`
--
ALTER TABLE `market_hierarchies`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `payroll_items`
--
ALTER TABLE `payroll_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `payroll_payments`
--
ALTER TABLE `payroll_payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `payroll_runs`
--
ALTER TABLE `payroll_runs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=245;

--
-- AUTO_INCREMENT for table `pos_checkout_requests`
--
ALTER TABLE `pos_checkout_requests`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=489;

--
-- AUTO_INCREMENT for table `product_deliveries`
--
ALTER TABLE `product_deliveries`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product_delivery_items`
--
ALTER TABLE `product_delivery_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product_serials`
--
ALTER TABLE `product_serials`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchases`
--
ALTER TABLE `purchases`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `purchase_items`
--
ALTER TABLE `purchase_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase_order_items`
--
ALTER TABLE `purchase_order_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase_returns`
--
ALTER TABLE `purchase_returns`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `purchase_return_items`
--
ALTER TABLE `purchase_return_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `quotations`
--
ALTER TABLE `quotations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `quotation_items`
--
ALTER TABLE `quotation_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT for table `sales_orders`
--
ALTER TABLE `sales_orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sales_order_items`
--
ALTER TABLE `sales_order_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sale_items`
--
ALTER TABLE `sale_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT for table `sale_returns`
--
ALTER TABLE `sale_returns`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `sale_return_items`
--
ALTER TABLE `sale_return_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `stock_adjustments`
--
ALTER TABLE `stock_adjustments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `stock_adjustment_items`
--
ALTER TABLE `stock_adjustment_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `stock_movements`
--
ALTER TABLE `stock_movements`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=552;

--
-- AUTO_INCREMENT for table `stock_transfers`
--
ALTER TABLE `stock_transfers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `stock_transfer_items`
--
ALTER TABLE `stock_transfer_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT for table `supplier_groups`
--
ALTER TABLE `supplier_groups`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `supplier_ledger_entries`
--
ALTER TABLE `supplier_ledger_entries`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

--
-- AUTO_INCREMENT for table `supplier_payments`
--
ALTER TABLE `supplier_payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `system_notifications`
--
ALTER TABLE `system_notifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `tax_rates`
--
ALTER TABLE `tax_rates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `units`
--
ALTER TABLE `units`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `warehouses`
--
ALTER TABLE `warehouses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `warehouse_stocks`
--
ALTER TABLE `warehouse_stocks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=544;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `accounting_periods`
--
ALTER TABLE `accounting_periods`
  ADD CONSTRAINT `accounting_periods_closed_by_foreign` FOREIGN KEY (`closed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `accounts`
--
ALTER TABLE `accounts`
  ADD CONSTRAINT `accounts_parent_fk` FOREIGN KEY (`parent_id`) REFERENCES `accounts` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `al_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `approval_requests`
--
ALTER TABLE `approval_requests`
  ADD CONSTRAINT `approval_requests_decided_by_foreign` FOREIGN KEY (`decided_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `approval_requests_requested_by_foreign` FOREIGN KEY (`requested_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `bank_reconciliations`
--
ALTER TABLE `bank_reconciliations`
  ADD CONSTRAINT `bank_reconciliations_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`),
  ADD CONSTRAINT `bank_reconciliations_closed_by_foreign` FOREIGN KEY (`closed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `bank_reconciliations_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `bank_reconciliation_items`
--
ALTER TABLE `bank_reconciliation_items`
  ADD CONSTRAINT `bank_reconciliation_items_journal_line_foreign` FOREIGN KEY (`journal_line_id`) REFERENCES `journal_lines` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bank_reconciliation_items_reconciliation_foreign` FOREIGN KEY (`bank_reconciliation_id`) REFERENCES `bank_reconciliations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `budgets`
--
ALTER TABLE `budgets`
  ADD CONSTRAINT `budgets_account_foreign` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`),
  ADD CONSTRAINT `budgets_cost_center_foreign` FOREIGN KEY (`cost_center_id`) REFERENCES `cost_centers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `budgets_project_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `business_days`
--
ALTER TABLE `business_days`
  ADD CONSTRAINT `business_days_closed_by_foreign` FOREIGN KEY (`closed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `business_days_opened_by_foreign` FOREIGN KEY (`opened_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_parent_fk` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `customers`
--
ALTER TABLE `customers`
  ADD CONSTRAINT `customers_group_fk` FOREIGN KEY (`customer_group_id`) REFERENCES `customer_groups` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `customers_market_hierarchy_id_foreign` FOREIGN KEY (`market_hierarchy_id`) REFERENCES `market_hierarchies` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `customer_ledger_entries`
--
ALTER TABLE `customer_ledger_entries`
  ADD CONSTRAINT `cle_customer_fk` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
  ADD CONSTRAINT `cle_user_fk` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `customer_payments`
--
ALTER TABLE `customer_payments`
  ADD CONSTRAINT `cp_account_fk` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`),
  ADD CONSTRAINT `cp_customer_fk` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
  ADD CONSTRAINT `cp_sale_fk` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `document_attachments`
--
ALTER TABLE `document_attachments`
  ADD CONSTRAINT `document_attachments_uploaded_by_foreign` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `expenses`
--
ALTER TABLE `expenses`
  ADD CONSTRAINT `e_category_fk` FOREIGN KEY (`expense_category_id`) REFERENCES `expense_categories` (`id`),
  ADD CONSTRAINT `e_expense_account_fk` FOREIGN KEY (`expense_account_id`) REFERENCES `accounts` (`id`),
  ADD CONSTRAINT `e_payment_account_fk` FOREIGN KEY (`payment_account_id`) REFERENCES `accounts` (`id`),
  ADD CONSTRAINT `expenses_cost_center_id_foreign` FOREIGN KEY (`cost_center_id`) REFERENCES `cost_centers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `expenses_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `expense_categories`
--
ALTER TABLE `expense_categories`
  ADD CONSTRAINT `ec_account_fk` FOREIGN KEY (`expense_account_id`) REFERENCES `accounts` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `fixed_asset_depreciations`
--
ALTER TABLE `fixed_asset_depreciations`
  ADD CONSTRAINT `fixed_asset_depreciations_asset_foreign` FOREIGN KEY (`fixed_asset_id`) REFERENCES `fixed_assets` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fixed_asset_depreciations_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `inventory_batches`
--
ALTER TABLE `inventory_batches`
  ADD CONSTRAINT `inventory_batches_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `inventory_batches_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`);

--
-- Constraints for table `inventory_cost_layers`
--
ALTER TABLE `inventory_cost_layers`
  ADD CONSTRAINT `inventory_cost_layers_inventory_batch_id_foreign` FOREIGN KEY (`inventory_batch_id`) REFERENCES `inventory_batches` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `inventory_cost_layers_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `inventory_cost_layers_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`);

--
-- Constraints for table `inventory_reservations`
--
ALTER TABLE `inventory_reservations`
  ADD CONSTRAINT `inventory_reservations_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `inventory_reservations_product_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `inventory_reservations_warehouse_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`);

--
-- Constraints for table `journal_entries`
--
ALTER TABLE `journal_entries`
  ADD CONSTRAINT `je_created_by_fk` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `je_posted_by_fk` FOREIGN KEY (`posted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `journal_lines`
--
ALTER TABLE `journal_lines`
  ADD CONSTRAINT `jl_account_fk` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`),
  ADD CONSTRAINT `jl_entry_fk` FOREIGN KEY (`journal_entry_id`) REFERENCES `journal_entries` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `journal_lines_cost_center_id_foreign` FOREIGN KEY (`cost_center_id`) REFERENCES `cost_centers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `journal_lines_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `loans`
--
ALTER TABLE `loans`
  ADD CONSTRAINT `loans_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `loan_installments`
--
ALTER TABLE `loan_installments`
  ADD CONSTRAINT `loan_installments_loan_id_foreign` FOREIGN KEY (`loan_id`) REFERENCES `loans` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `loan_payments`
--
ALTER TABLE `loan_payments`
  ADD CONSTRAINT `loan_payments_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `loan_payments_loan_id_foreign` FOREIGN KEY (`loan_id`) REFERENCES `loans` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `market_hierarchies`
--
ALTER TABLE `market_hierarchies`
  ADD CONSTRAINT `market_hierarchies_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `market_hierarchies` (`id`);

--
-- Constraints for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `mhp_permission_fk` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `mhr_role_fk` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payroll_items`
--
ALTER TABLE `payroll_items`
  ADD CONSTRAINT `payi_employee_fk` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`),
  ADD CONSTRAINT `payi_run_fk` FOREIGN KEY (`payroll_run_id`) REFERENCES `payroll_runs` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payroll_payments`
--
ALTER TABLE `payroll_payments`
  ADD CONSTRAINT `payp_account_fk` FOREIGN KEY (`payment_account_id`) REFERENCES `accounts` (`id`),
  ADD CONSTRAINT `payp_item_fk` FOREIGN KEY (`payroll_item_id`) REFERENCES `payroll_items` (`id`);

--
-- Constraints for table `pos_checkout_requests`
--
ALTER TABLE `pos_checkout_requests`
  ADD CONSTRAINT `pos_checkout_requests_sale_id_foreign` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_brand_fk` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `products_category_fk` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `products_parent_product_id_foreign` FOREIGN KEY (`parent_product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `products_unit_fk` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`);

--
-- Constraints for table `product_deliveries`
--
ALTER TABLE `product_deliveries`
  ADD CONSTRAINT `product_deliveries_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `product_deliveries_posted_by_foreign` FOREIGN KEY (`posted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `product_deliveries_purchase_id_foreign` FOREIGN KEY (`purchase_id`) REFERENCES `purchases` (`id`),
  ADD CONSTRAINT `product_deliveries_sale_id_foreign` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`),
  ADD CONSTRAINT `product_deliveries_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`);

--
-- Constraints for table `product_delivery_items`
--
ALTER TABLE `product_delivery_items`
  ADD CONSTRAINT `product_delivery_items_product_delivery_id_foreign` FOREIGN KEY (`product_delivery_id`) REFERENCES `product_deliveries` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_delivery_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `product_serials`
--
ALTER TABLE `product_serials`
  ADD CONSTRAINT `product_serials_product_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `product_serials_warehouse_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `projects`
--
ALTER TABLE `projects`
  ADD CONSTRAINT `projects_cost_center_foreign` FOREIGN KEY (`cost_center_id`) REFERENCES `cost_centers` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `purchases`
--
ALTER TABLE `purchases`
  ADD CONSTRAINT `purchases_supplier_fk` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`),
  ADD CONSTRAINT `purchases_warehouse_fk` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`);

--
-- Constraints for table `purchase_items`
--
ALTER TABLE `purchase_items`
  ADD CONSTRAINT `pi_product_fk` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `pi_purchase_fk` FOREIGN KEY (`purchase_id`) REFERENCES `purchases` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  ADD CONSTRAINT `purchase_orders_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `purchase_orders_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`),
  ADD CONSTRAINT `purchase_orders_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`);

--
-- Constraints for table `purchase_order_items`
--
ALTER TABLE `purchase_order_items`
  ADD CONSTRAINT `purchase_order_items_order_foreign` FOREIGN KEY (`purchase_order_id`) REFERENCES `purchase_orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `purchase_order_items_product_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `purchase_returns`
--
ALTER TABLE `purchase_returns`
  ADD CONSTRAINT `pr_purchase_fk` FOREIGN KEY (`purchase_id`) REFERENCES `purchases` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `pr_supplier_fk` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`),
  ADD CONSTRAINT `pr_warehouse_fk` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`);

--
-- Constraints for table `purchase_return_items`
--
ALTER TABLE `purchase_return_items`
  ADD CONSTRAINT `pri_product_fk` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `pri_return_fk` FOREIGN KEY (`purchase_return_id`) REFERENCES `purchase_returns` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `quotations`
--
ALTER TABLE `quotations`
  ADD CONSTRAINT `quotation_customer_fk` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `quotation_items`
--
ALTER TABLE `quotation_items`
  ADD CONSTRAINT `qi_product_fk` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `qi_quotation_fk` FOREIGN KEY (`quotation_id`) REFERENCES `quotations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `rhp_permission_fk` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `rhp_role_fk` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sales`
--
ALTER TABLE `sales`
  ADD CONSTRAINT `sales_customer_fk` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `sales_warehouse_fk` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`);

--
-- Constraints for table `sales_orders`
--
ALTER TABLE `sales_orders`
  ADD CONSTRAINT `sales_orders_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `sales_orders_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `sales_orders_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`);

--
-- Constraints for table `sales_order_items`
--
ALTER TABLE `sales_order_items`
  ADD CONSTRAINT `sales_order_items_order_foreign` FOREIGN KEY (`sales_order_id`) REFERENCES `sales_orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sales_order_items_product_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `sale_items`
--
ALTER TABLE `sale_items`
  ADD CONSTRAINT `si_product_fk` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `si_sale_fk` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sale_returns`
--
ALTER TABLE `sale_returns`
  ADD CONSTRAINT `sr_customer_fk` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `sr_sale_fk` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `sr_warehouse_fk` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`);

--
-- Constraints for table `sale_return_items`
--
ALTER TABLE `sale_return_items`
  ADD CONSTRAINT `sri_product_fk` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `sri_return_fk` FOREIGN KEY (`sale_return_id`) REFERENCES `sale_returns` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `stock_adjustments`
--
ALTER TABLE `stock_adjustments`
  ADD CONSTRAINT `sa_created_by_fk` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `sa_posted_by_fk` FOREIGN KEY (`posted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `sa_warehouse_fk` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`);

--
-- Constraints for table `stock_adjustment_items`
--
ALTER TABLE `stock_adjustment_items`
  ADD CONSTRAINT `sai_header_fk` FOREIGN KEY (`stock_adjustment_id`) REFERENCES `stock_adjustments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sai_product_fk` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `stock_movements`
--
ALTER TABLE `stock_movements`
  ADD CONSTRAINT `sm_product_fk` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `sm_user_fk` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `sm_warehouse_fk` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`);

--
-- Constraints for table `stock_transfers`
--
ALTER TABLE `stock_transfers`
  ADD CONSTRAINT `st_created_by_fk` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `st_from_fk` FOREIGN KEY (`from_warehouse_id`) REFERENCES `warehouses` (`id`),
  ADD CONSTRAINT `st_posted_by_fk` FOREIGN KEY (`posted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `st_to_fk` FOREIGN KEY (`to_warehouse_id`) REFERENCES `warehouses` (`id`);

--
-- Constraints for table `stock_transfer_items`
--
ALTER TABLE `stock_transfer_items`
  ADD CONSTRAINT `sti_header_fk` FOREIGN KEY (`stock_transfer_id`) REFERENCES `stock_transfers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sti_product_fk` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD CONSTRAINT `suppliers_group_fk` FOREIGN KEY (`supplier_group_id`) REFERENCES `supplier_groups` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `supplier_ledger_entries`
--
ALTER TABLE `supplier_ledger_entries`
  ADD CONSTRAINT `sle_supplier_fk` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`),
  ADD CONSTRAINT `sle_user_fk` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `supplier_payments`
--
ALTER TABLE `supplier_payments`
  ADD CONSTRAINT `sp_account_fk` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`),
  ADD CONSTRAINT `sp_purchase_fk` FOREIGN KEY (`purchase_id`) REFERENCES `purchases` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `sp_supplier_fk` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`);

--
-- Constraints for table `system_notifications`
--
ALTER TABLE `system_notifications`
  ADD CONSTRAINT `sn_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `warehouse_stocks`
--
ALTER TABLE `warehouse_stocks`
  ADD CONSTRAINT `ws_product_fk` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `ws_warehouse_fk` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
