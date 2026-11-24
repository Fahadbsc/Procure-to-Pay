-- Acme P2P InfinityFree import: schema plus Appendix A seed data
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS=0;

-- Table: migrations
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('1', '2014_10_12_000000_create_users_table', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('2', '2014_10_12_100000_create_password_reset_tokens_table', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('3', '2019_08_19_000000_create_failed_jobs_table', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('4', '2019_12_14_000001_create_personal_access_tokens_table', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('5', '2026_11_23_154024_create_companies_table', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('6', '2026_11_23_154055_create_roles_table', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('7', '2026_11_23_154104_add_company_id_to_users_table', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('8', '2026_11_23_154115_create_role_user_table', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('9', '2026_11_23_163147_create_vendors_table', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('10', '2026_11_23_163150_create_unit_of_measures_table', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('11', '2026_11_23_163207_create_items_table', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('12', '2026_11_23_163221_create_cost_centres_table', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('13', '2026_11_23_174148_create_purchase_requisitions_table', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('14', '2026_11_23_174156_create_purchase_requisition_items_table', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('15', '2026_11_23_175729_add_approval_fields_to_purchase_requisitions_table', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('16', '2026_11_23_180627_create_purchase_orders_table', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('17', '2026_11_23_180633_create_purchase_order_items_table', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('18', '2026_11_23_182626_create_goods_receipts_table', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('19', '2026_11_23_182632_create_goods_receipt_items_table', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('20', '2026_11_23_184058_create_supplier_invoices_table', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('21', '2026_11_23_184252_create_supplier_invoice_items_table', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('22', '2026_11_24_030803_add_approval_fields_to_supplier_invoices_table', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('23', '2026_11_24_034146_create_cost_centres_table', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('24', '2026_11_24_034152_create_budgets_table', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('25', '2026_11_24_034931_add_cost_centre_id_to_purchase_requisitions_table', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('26', '2026_11_24_040943_add_approved_status_to_purchase_orders_table', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('27', '2026_11_24_042058_add_budget_committed_amount_to_purchase_orders_table', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('28', '2026_11_24_042916_add_posted_status_to_supplier_invoices_table', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('29', '2026_11_24_043604_add_three_way_matching_fields_to_supplier_invoices', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('30', '2026_11_24_044406_add_override_audit_fields_to_supplier_invoices', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('31', '2026_11_24_045807_add_vendor_id_and_unique_invoice_number_to_supplier_invoices_table', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('32', '2026_11_24_051226_create_approval_workflow_tables', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('33', '2026_11_24_061708_remove_unique_from_purchase_orders_requisition_id', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('34', '2026_11_24_063758_update_purchase_requisitions_status_enum', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('35', '2026_11_24_070000_expand_purchase_order_receipt_statuses', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('36', '2026_11_24_071000_create_audit_logs_table', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('37', '2026_11_24_072000_add_units_and_gst_components', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('38', '2026_11_24_073000_create_vendor_payments', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('39', '2026_11_24_075000_add_purchase_order_tax_components', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('40', '2026_11_24_076000_increase_quantity_precision', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('41', '2026_11_24_077000_add_category_to_approval_rules', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('42', '2026_11_24_078000_create_notifications_table', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('43', '2026_11_24_079000_add_approval_reminder_fields', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('44', '2026_11_24_080000_add_purchase_order_rejection_reason', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('45', '2026_11_24_081000_add_partially_converted_pr_status', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('46', '2026_11_24_082000_allocate_po_budgets_by_line', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('47', '2026_11_24_083000_add_partially_paid_invoice_status', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('48', '2026_11_24_084000_backfill_invoice_review_and_payment_users', '1');

-- Table: users
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint(20) unsigned DEFAULT NULL,
  `manager_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_company_id_foreign` (`company_id`),
  KEY `users_manager_id_foreign` (`manager_id`),
  CONSTRAINT `users_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`),
  CONSTRAINT `users_manager_id_foreign` FOREIGN KEY (`manager_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `users` (`id`, `company_id`, `manager_id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES ('1', '1', NULL, 'Admin Mumbai', 'admin.mum@acme.test', NULL, '$2y$10$DVCvVjRq/adsZLFa89ggeeqHCknmeuBsZZyeUWChLkW26UnYMES6K', NULL, '2026-11-24 10:21:17', '2026-11-24 10:21:17');
INSERT INTO `users` (`id`, `company_id`, `manager_id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES ('2', '1', NULL, 'Requester Mumbai', 'req.mum@acme.test', NULL, '$2y$10$axxnd/nMnxxVOk9zCkHFYOS98SsiBqUQG.QCqG2gh8UbHqu4NZQza', NULL, '2026-11-24 10:21:17', '2026-11-24 10:21:17');
INSERT INTO `users` (`id`, `company_id`, `manager_id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES ('3', '1', '7', 'Dept Head Mumbai', 'depthead.mum@acme.test', NULL, '$2y$10$klehxVOJ/PoHwCDU3/nGduIr0M1krX61kRZcpfgWxcbTJL6/SWB3W', NULL, '2026-11-24 10:21:17', '2026-11-24 10:21:19');
INSERT INTO `users` (`id`, `company_id`, `manager_id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES ('4', '1', '7', 'Alternate Dept Head Mumbai', 'depthead.backup.mum@acme.test', NULL, '$2y$10$4CKtct2.9.vni45jiMCXS.1hydRPXM8AnZeIWNQ63giiIOERCx/.C', NULL, '2026-11-24 10:21:17', '2026-11-24 10:21:19');
INSERT INTO `users` (`id`, `company_id`, `manager_id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES ('5', '1', '7', 'IT Head Mumbai', 'ithead.mum@acme.test', NULL, '$2y$10$Tmw6YXr..aEvLvvRgZSIrOEVhZ56ExXRcsOuTprWb9A.BA/CesyhK', NULL, '2026-11-24 10:21:17', '2026-11-24 10:21:19');
INSERT INTO `users` (`id`, `company_id`, `manager_id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES ('6', '1', '7', 'Finance Head Mumbai', 'finhead.mum@acme.test', NULL, '$2y$10$2lTgqipWC5RXrrCon9uAu.kavijVF3AEBzy4KB9mCcl/We5dMX6Bm', NULL, '2026-11-24 10:21:18', '2026-11-24 10:21:19');
INSERT INTO `users` (`id`, `company_id`, `manager_id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES ('7', '1', '1', 'CFO Mumbai', 'cfo.mum@acme.test', NULL, '$2y$10$pP4SWueAWedlNgWW9CWI5uIjViS5Zv3DM.Kd502zCONliJ5dNKxgO', NULL, '2026-11-24 10:21:18', '2026-11-24 10:21:19');
INSERT INTO `users` (`id`, `company_id`, `manager_id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES ('8', '1', NULL, 'Procurement Mumbai', 'proc.mum@acme.test', NULL, '$2y$10$lGQwb.HBQ4ol87DDNjSbF.jfbXgoHqNysLC8X3IJO2T3cJ2VvPgym', NULL, '2026-11-24 10:21:18', '2026-11-24 10:21:18');
INSERT INTO `users` (`id`, `company_id`, `manager_id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES ('9', '1', NULL, 'Store Keeper Mumbai', 'store.mum@acme.test', NULL, '$2y$10$6dnyvdQ6YUilrnjIepkoN.8V2cr.5LXoTOp9aAGb1rfjECEkMirOy', NULL, '2026-11-24 10:21:18', '2026-11-24 10:21:18');
INSERT INTO `users` (`id`, `company_id`, `manager_id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES ('10', '1', NULL, 'Accounts Mumbai', 'accounts.mum@acme.test', NULL, '$2y$10$YX6gJvKxKhHNt/sdqKw7t.htsdE0YCbFFKhqtfZOULx5jfyc.IpuC', NULL, '2026-11-24 10:21:18', '2026-11-24 10:21:18');
INSERT INTO `users` (`id`, `company_id`, `manager_id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES ('11', '2', NULL, 'Admin Bengaluru', 'admin.blr@acme.test', NULL, '$2y$10$ZU.gHD1I/i23sOYFh8bQM.KKXXsw4fc0sa4mfTz2WOa4OUpQdyKWK', NULL, '2026-11-24 10:21:18', '2026-11-24 10:21:18');
INSERT INTO `users` (`id`, `company_id`, `manager_id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES ('12', '2', NULL, 'Requester Bengaluru', 'req.blr@acme.test', NULL, '$2y$10$PLO4aN993QKiK2bLNkxE8eEwy1MDmM8erUSVy2h/KX6UACCgQ9u56', NULL, '2026-11-24 10:21:18', '2026-11-24 10:21:18');
INSERT INTO `users` (`id`, `company_id`, `manager_id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES ('13', '2', '18', 'Dept Head Bengaluru', 'depthead.blr@acme.test', NULL, '$2y$10$VQ.hB2NaIJVgrEiUw3NoTeLznHBv81jLCGQydvdO/9tklEGCqYe3O', NULL, '2026-11-24 10:21:18', '2026-11-24 10:21:19');
INSERT INTO `users` (`id`, `company_id`, `manager_id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES ('14', '2', '18', 'Alternate Dept Head Bengaluru', 'depthead.backup.blr@acme.test', NULL, '$2y$10$FhBMmJX.VuAf2jymLnYcfOawIqm0cw13BDSKrsTX09Vij3rWRJBWq', NULL, '2026-11-24 10:21:18', '2026-11-24 10:21:19');
INSERT INTO `users` (`id`, `company_id`, `manager_id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES ('15', '2', NULL, 'Procurement Bengaluru', 'proc.blr@acme.test', NULL, '$2y$10$QguO38zB2JigVxIDiAx8z.Wbxx6gZv.sKpbaAIqkYflHOA.QfsAQq', NULL, '2026-11-24 10:21:18', '2026-11-24 10:21:18');
INSERT INTO `users` (`id`, `company_id`, `manager_id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES ('16', '2', '18', 'IT Head Bengaluru', 'ithead.blr@acme.test', NULL, '$2y$10$3bIHz8nIoFC2L/hfIsm6sO34VNhXND4HnbZ4MFmFcJ2gVh071D1HS', NULL, '2026-11-24 10:21:18', '2026-11-24 10:21:19');
INSERT INTO `users` (`id`, `company_id`, `manager_id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES ('17', '2', '18', 'Finance Head Bengaluru', 'finhead.blr@acme.test', NULL, '$2y$10$XFMrDX9GEuW9/yLdbTi.KO/3N.tuPmeuA1091m7sV081ToQYSrVj.', NULL, '2026-11-24 10:21:18', '2026-11-24 10:21:19');
INSERT INTO `users` (`id`, `company_id`, `manager_id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES ('18', '2', '11', 'CFO Bengaluru', 'cfo.blr@acme.test', NULL, '$2y$10$J5cFeCFezvIF.7Mye3c7u.8FJqWUDH51hK95pTuRbn89j4tCopVNy', NULL, '2026-11-24 10:21:19', '2026-11-24 10:21:19');
INSERT INTO `users` (`id`, `company_id`, `manager_id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES ('19', '2', NULL, 'Store Keeper Bengaluru', 'store.blr@acme.test', NULL, '$2y$10$cD04y0FKg0fCqDGAuj..4OLylhByLTGlDcHMDiUXiYLx4Hq5zVv4K', NULL, '2026-11-24 10:21:19', '2026-11-24 10:21:19');
INSERT INTO `users` (`id`, `company_id`, `manager_id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES ('20', '2', NULL, 'Accounts Bengaluru', 'accounts.blr@acme.test', NULL, '$2y$10$VnkVxCSEf/MwGQzZ7VxN9u3pR6iBJ3hIoU7/mrkH.UB.3..t8zYoG', NULL, '2026-11-24 10:21:19', '2026-11-24 10:21:19');

-- Table: password_reset_tokens
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: failed_jobs
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: personal_access_tokens
CREATE TABLE IF NOT EXISTS `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: companies
CREATE TABLE IF NOT EXISTS `companies` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `state` varchar(255) NOT NULL,
  `gstin` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `companies_code_unique` (`code`),
  UNIQUE KEY `companies_gstin_unique` (`gstin`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `companies` (`id`, `code`, `name`, `state`, `gstin`, `is_active`, `created_at`, `updated_at`) VALUES ('1', 'ACME-MUM', 'Acme Mumbai Pvt Ltd', 'Maharashtra', '27AAACA1234A1Z5', '1', '2026-11-24 10:21:17', '2026-11-24 10:21:17');
INSERT INTO `companies` (`id`, `code`, `name`, `state`, `gstin`, `is_active`, `created_at`, `updated_at`) VALUES ('2', 'ACME-BLR', 'Acme Bengaluru Pvt Ltd', 'Karnataka', '29AAACA1234A1Z1', '1', '2026-11-24 10:21:17', '2026-11-24 10:21:17');

-- Table: roles
CREATE TABLE IF NOT EXISTS `roles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `roles` (`id`, `name`, `created_at`, `updated_at`) VALUES ('1', 'Admin', '2026-11-24 10:21:17', '2026-11-24 10:21:17');
INSERT INTO `roles` (`id`, `name`, `created_at`, `updated_at`) VALUES ('2', 'Requester', '2026-11-24 10:21:17', '2026-11-24 10:21:17');
INSERT INTO `roles` (`id`, `name`, `created_at`, `updated_at`) VALUES ('3', 'Dept Head', '2026-11-24 10:21:17', '2026-11-24 10:21:17');
INSERT INTO `roles` (`id`, `name`, `created_at`, `updated_at`) VALUES ('4', 'IT Head', '2026-11-24 10:21:17', '2026-11-24 10:21:17');
INSERT INTO `roles` (`id`, `name`, `created_at`, `updated_at`) VALUES ('5', 'Finance Head', '2026-11-24 10:21:17', '2026-11-24 10:21:17');
INSERT INTO `roles` (`id`, `name`, `created_at`, `updated_at`) VALUES ('6', 'CFO', '2026-11-24 10:21:17', '2026-11-24 10:21:17');
INSERT INTO `roles` (`id`, `name`, `created_at`, `updated_at`) VALUES ('7', 'Procurement Officer', '2026-11-24 10:21:17', '2026-11-24 10:21:17');
INSERT INTO `roles` (`id`, `name`, `created_at`, `updated_at`) VALUES ('8', 'Store Keeper', '2026-11-24 10:21:17', '2026-11-24 10:21:17');
INSERT INTO `roles` (`id`, `name`, `created_at`, `updated_at`) VALUES ('9', 'Accounts', '2026-11-24 10:21:17', '2026-11-24 10:21:17');

-- Table: role_user
CREATE TABLE IF NOT EXISTS `role_user` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `role_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `role_user_user_id_role_id_unique` (`user_id`,`role_id`),
  KEY `role_user_role_id_foreign` (`role_id`),
  CONSTRAINT `role_user_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `role_user` (`id`, `user_id`, `role_id`, `created_at`, `updated_at`) VALUES ('1', '1', '1', '2026-11-24 10:21:17', '2026-11-24 10:21:17');
INSERT INTO `role_user` (`id`, `user_id`, `role_id`, `created_at`, `updated_at`) VALUES ('2', '2', '2', '2026-11-24 10:21:17', '2026-11-24 10:21:17');
INSERT INTO `role_user` (`id`, `user_id`, `role_id`, `created_at`, `updated_at`) VALUES ('3', '3', '3', '2026-11-24 10:21:17', '2026-11-24 10:21:17');
INSERT INTO `role_user` (`id`, `user_id`, `role_id`, `created_at`, `updated_at`) VALUES ('4', '3', '2', '2026-11-24 10:21:17', '2026-11-24 10:21:17');
INSERT INTO `role_user` (`id`, `user_id`, `role_id`, `created_at`, `updated_at`) VALUES ('5', '4', '3', '2026-11-24 10:21:17', '2026-11-24 10:21:17');
INSERT INTO `role_user` (`id`, `user_id`, `role_id`, `created_at`, `updated_at`) VALUES ('6', '5', '4', '2026-11-24 10:21:17', '2026-11-24 10:21:17');
INSERT INTO `role_user` (`id`, `user_id`, `role_id`, `created_at`, `updated_at`) VALUES ('7', '6', '5', '2026-11-24 10:21:18', '2026-11-24 10:21:18');
INSERT INTO `role_user` (`id`, `user_id`, `role_id`, `created_at`, `updated_at`) VALUES ('8', '7', '6', '2026-11-24 10:21:18', '2026-11-24 10:21:18');
INSERT INTO `role_user` (`id`, `user_id`, `role_id`, `created_at`, `updated_at`) VALUES ('9', '8', '7', '2026-11-24 10:21:18', '2026-11-24 10:21:18');
INSERT INTO `role_user` (`id`, `user_id`, `role_id`, `created_at`, `updated_at`) VALUES ('10', '9', '8', '2026-11-24 10:21:18', '2026-11-24 10:21:18');
INSERT INTO `role_user` (`id`, `user_id`, `role_id`, `created_at`, `updated_at`) VALUES ('11', '10', '9', '2026-11-24 10:21:18', '2026-11-24 10:21:18');
INSERT INTO `role_user` (`id`, `user_id`, `role_id`, `created_at`, `updated_at`) VALUES ('12', '11', '1', '2026-11-24 10:21:18', '2026-11-24 10:21:18');
INSERT INTO `role_user` (`id`, `user_id`, `role_id`, `created_at`, `updated_at`) VALUES ('13', '12', '2', '2026-11-24 10:21:18', '2026-11-24 10:21:18');
INSERT INTO `role_user` (`id`, `user_id`, `role_id`, `created_at`, `updated_at`) VALUES ('14', '13', '3', '2026-11-24 10:21:18', '2026-11-24 10:21:18');
INSERT INTO `role_user` (`id`, `user_id`, `role_id`, `created_at`, `updated_at`) VALUES ('15', '14', '3', '2026-11-24 10:21:18', '2026-11-24 10:21:18');
INSERT INTO `role_user` (`id`, `user_id`, `role_id`, `created_at`, `updated_at`) VALUES ('16', '15', '7', '2026-11-24 10:21:18', '2026-11-24 10:21:18');
INSERT INTO `role_user` (`id`, `user_id`, `role_id`, `created_at`, `updated_at`) VALUES ('17', '16', '4', '2026-11-24 10:21:18', '2026-11-24 10:21:18');
INSERT INTO `role_user` (`id`, `user_id`, `role_id`, `created_at`, `updated_at`) VALUES ('18', '17', '5', '2026-11-24 10:21:18', '2026-11-24 10:21:18');
INSERT INTO `role_user` (`id`, `user_id`, `role_id`, `created_at`, `updated_at`) VALUES ('19', '18', '6', '2026-11-24 10:21:19', '2026-11-24 10:21:19');
INSERT INTO `role_user` (`id`, `user_id`, `role_id`, `created_at`, `updated_at`) VALUES ('20', '19', '8', '2026-11-24 10:21:19', '2026-11-24 10:21:19');
INSERT INTO `role_user` (`id`, `user_id`, `role_id`, `created_at`, `updated_at`) VALUES ('21', '20', '9', '2026-11-24 10:21:19', '2026-11-24 10:21:19');

-- Table: vendors
CREATE TABLE IF NOT EXISTS `vendors` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint(20) unsigned NOT NULL,
  `code` varchar(20) NOT NULL,
  `name` varchar(255) NOT NULL,
  `gstin` varchar(15) NOT NULL,
  `state` varchar(255) NOT NULL,
  `payment_terms_days` int(10) unsigned NOT NULL DEFAULT 30,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `vendors_company_id_code_unique` (`company_id`,`code`),
  UNIQUE KEY `vendors_company_id_gstin_unique` (`company_id`,`gstin`),
  CONSTRAINT `vendors_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `vendors` (`id`, `company_id`, `code`, `name`, `gstin`, `state`, `payment_terms_days`, `is_active`, `created_at`, `updated_at`) VALUES ('1', '1', 'V001', 'Shree Steel Traders', '27AABCS1111A1Z1', 'Maharashtra', '30', '1', '2026-11-24 10:21:19', '2026-11-24 10:21:19');
INSERT INTO `vendors` (`id`, `company_id`, `code`, `name`, `gstin`, `state`, `payment_terms_days`, `is_active`, `created_at`, `updated_at`) VALUES ('2', '1', 'V002', 'Bangalore Tech Supplies', '29AABCB2222B1Z2', 'Karnataka', '45', '1', '2026-11-24 10:21:19', '2026-11-24 10:21:19');
INSERT INTO `vendors` (`id`, `company_id`, `code`, `name`, `gstin`, `state`, `payment_terms_days`, `is_active`, `created_at`, `updated_at`) VALUES ('3', '1', 'V003', 'QuickClean Services', '27AABCQ3333C1Z3', 'Maharashtra', '15', '1', '2026-11-24 10:21:19', '2026-11-24 10:21:19');
INSERT INTO `vendors` (`id`, `company_id`, `code`, `name`, `gstin`, `state`, `payment_terms_days`, `is_active`, `created_at`, `updated_at`) VALUES ('4', '2', 'V004', 'Deccan Packaging', '29AABCD4444D1Z4', 'Karnataka', '30', '1', '2026-11-24 10:21:19', '2026-11-24 10:21:19');

-- Table: unit_of_measures
CREATE TABLE IF NOT EXISTS `unit_of_measures` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(255) NOT NULL,
  `base_unit` varchar(255) NOT NULL,
  `conversion_factor` decimal(12,4) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unit_of_measures_code_unique` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `unit_of_measures` (`id`, `code`, `base_unit`, `conversion_factor`, `created_at`, `updated_at`) VALUES ('1', 'PCS', 'PCS', '1.0000', '2026-11-24 10:21:19', '2026-11-24 10:21:19');
INSERT INTO `unit_of_measures` (`id`, `code`, `base_unit`, `conversion_factor`, `created_at`, `updated_at`) VALUES ('2', 'BOX', 'PCS', '12.0000', '2026-11-24 10:21:19', '2026-11-24 10:21:19');
INSERT INTO `unit_of_measures` (`id`, `code`, `base_unit`, `conversion_factor`, `created_at`, `updated_at`) VALUES ('3', 'KG', 'KG', '1.0000', '2026-11-24 10:21:19', '2026-11-24 10:21:19');
INSERT INTO `unit_of_measures` (`id`, `code`, `base_unit`, `conversion_factor`, `created_at`, `updated_at`) VALUES ('4', 'TON', 'KG', '1000.0000', '2026-11-24 10:21:19', '2026-11-24 10:21:19');
INSERT INTO `unit_of_measures` (`id`, `code`, `base_unit`, `conversion_factor`, `created_at`, `updated_at`) VALUES ('5', 'HRS', 'HRS', '1.0000', '2026-11-24 10:21:19', '2026-11-24 10:21:19');

-- Table: cost_centres
CREATE TABLE IF NOT EXISTS `cost_centres` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint(20) unsigned NOT NULL,
  `code` varchar(20) NOT NULL,
  `name` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cost_centres_company_id_code_unique` (`company_id`,`code`),
  CONSTRAINT `cost_centres_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `cost_centres` (`id`, `company_id`, `code`, `name`, `is_active`, `created_at`, `updated_at`) VALUES ('1', '1', 'PROD', 'Production', '1', '2026-11-24 10:21:19', '2026-11-24 10:21:19');
INSERT INTO `cost_centres` (`id`, `company_id`, `code`, `name`, `is_active`, `created_at`, `updated_at`) VALUES ('2', '1', 'IT', 'Information Technology', '1', '2026-11-24 10:21:19', '2026-11-24 10:21:19');
INSERT INTO `cost_centres` (`id`, `company_id`, `code`, `name`, `is_active`, `created_at`, `updated_at`) VALUES ('3', '1', 'ADMIN', 'Administration', '1', '2026-11-24 10:21:19', '2026-11-24 10:21:19');
INSERT INTO `cost_centres` (`id`, `company_id`, `code`, `name`, `is_active`, `created_at`, `updated_at`) VALUES ('4', '1', 'SALES', 'Sales', '1', '2026-11-24 10:21:19', '2026-11-24 10:21:19');
INSERT INTO `cost_centres` (`id`, `company_id`, `code`, `name`, `is_active`, `created_at`, `updated_at`) VALUES ('5', '2', 'PROD', 'Production', '1', '2026-11-24 10:21:19', '2026-11-24 10:21:19');
INSERT INTO `cost_centres` (`id`, `company_id`, `code`, `name`, `is_active`, `created_at`, `updated_at`) VALUES ('6', '2', 'IT', 'Information Technology', '1', '2026-11-24 10:21:19', '2026-11-24 10:21:19');

-- Table: items
CREATE TABLE IF NOT EXISTS `items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `category` enum('RAW_MATERIAL','IT','SERVICES','CONSUMABLES') NOT NULL,
  `base_uom_id` bigint(20) unsigned NOT NULL,
  `gst_rate` decimal(5,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `items_code_unique` (`code`),
  KEY `items_base_uom_id_foreign` (`base_uom_id`),
  CONSTRAINT `items_base_uom_id_foreign` FOREIGN KEY (`base_uom_id`) REFERENCES `unit_of_measures` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `items` (`id`, `code`, `name`, `category`, `base_uom_id`, `gst_rate`, `created_at`, `updated_at`) VALUES ('1', 'I001', 'MS Steel Sheet 2mm', 'RAW_MATERIAL', '3', '18.00', '2026-11-24 10:21:19', '2026-11-24 10:21:19');
INSERT INTO `items` (`id`, `code`, `name`, `category`, `base_uom_id`, `gst_rate`, `created_at`, `updated_at`) VALUES ('2', 'I002', 'Laptop i5 16GB', 'IT', '1', '18.00', '2026-11-24 10:21:19', '2026-11-24 10:21:19');
INSERT INTO `items` (`id`, `code`, `name`, `category`, `base_uom_id`, `gst_rate`, `created_at`, `updated_at`) VALUES ('3', 'I003', 'A4 Paper Ream', 'CONSUMABLES', '1', '12.00', '2026-11-24 10:21:19', '2026-11-24 10:21:19');
INSERT INTO `items` (`id`, `code`, `name`, `category`, `base_uom_id`, `gst_rate`, `created_at`, `updated_at`) VALUES ('4', 'I004', 'Housekeeping Service', 'SERVICES', '5', '18.00', '2026-11-24 10:21:19', '2026-11-24 10:21:19');
INSERT INTO `items` (`id`, `code`, `name`, `category`, `base_uom_id`, `gst_rate`, `created_at`, `updated_at`) VALUES ('5', 'I005', 'Corrugated Box', 'CONSUMABLES', '1', '12.00', '2026-11-24 10:21:19', '2026-11-24 10:21:19');
INSERT INTO `items` (`id`, `code`, `name`, `category`, `base_uom_id`, `gst_rate`, `created_at`, `updated_at`) VALUES ('6', 'I006', 'Rice (canteen)', 'CONSUMABLES', '3', '0.00', '2026-11-24 10:21:19', '2026-11-24 10:21:19');

-- Table: budgets
CREATE TABLE IF NOT EXISTS `budgets` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint(20) unsigned NOT NULL,
  `cost_centre_id` bigint(20) unsigned NOT NULL,
  `financial_year` varchar(9) NOT NULL,
  `budget_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `committed_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `spent_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `budgets_company_id_cost_centre_id_financial_year_unique` (`company_id`,`cost_centre_id`,`financial_year`),
  KEY `budgets_cost_centre_id_foreign` (`cost_centre_id`),
  CONSTRAINT `budgets_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `budgets_cost_centre_id_foreign` FOREIGN KEY (`cost_centre_id`) REFERENCES `cost_centres` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `budgets` (`id`, `company_id`, `cost_centre_id`, `financial_year`, `budget_amount`, `committed_amount`, `spent_amount`, `created_at`, `updated_at`) VALUES ('1', '1', '1', '2026-27', '5000000.00', '0.00', '0.00', '2026-11-24 10:21:19', '2026-11-24 10:21:19');
INSERT INTO `budgets` (`id`, `company_id`, `cost_centre_id`, `financial_year`, `budget_amount`, `committed_amount`, `spent_amount`, `created_at`, `updated_at`) VALUES ('2', '1', '2', '2026-27', '1000000.00', '0.00', '0.00', '2026-11-24 10:21:19', '2026-11-24 10:21:19');
INSERT INTO `budgets` (`id`, `company_id`, `cost_centre_id`, `financial_year`, `budget_amount`, `committed_amount`, `spent_amount`, `created_at`, `updated_at`) VALUES ('3', '1', '3', '2026-27', '200000.00', '0.00', '0.00', '2026-11-24 10:21:19', '2026-11-24 10:21:19');
INSERT INTO `budgets` (`id`, `company_id`, `cost_centre_id`, `financial_year`, `budget_amount`, `committed_amount`, `spent_amount`, `created_at`, `updated_at`) VALUES ('4', '1', '4', '2026-27', '100000.00', '0.00', '0.00', '2026-11-24 10:21:19', '2026-11-24 10:21:19');
INSERT INTO `budgets` (`id`, `company_id`, `cost_centre_id`, `financial_year`, `budget_amount`, `committed_amount`, `spent_amount`, `created_at`, `updated_at`) VALUES ('5', '2', '5', '2026-27', '3000000.00', '0.00', '0.00', '2026-11-24 10:21:19', '2026-11-24 10:21:19');
INSERT INTO `budgets` (`id`, `company_id`, `cost_centre_id`, `financial_year`, `budget_amount`, `committed_amount`, `spent_amount`, `created_at`, `updated_at`) VALUES ('6', '2', '6', '2026-27', '500000.00', '0.00', '0.00', '2026-11-24 10:21:19', '2026-11-24 10:21:19');

-- Table: purchase_requisitions
CREATE TABLE IF NOT EXISTS `purchase_requisitions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint(20) unsigned NOT NULL,
  `cost_centre_id` bigint(20) unsigned DEFAULT NULL,
  `pr_number` varchar(255) NOT NULL,
  `requested_by` bigint(20) unsigned NOT NULL,
  `required_date` date NOT NULL,
  `purpose` text DEFAULT NULL,
  `status` enum('DRAFT','PENDING','SUBMITTED','APPROVED','REJECTED','PARTIALLY_CONVERTED','CONVERTED') NOT NULL DEFAULT 'DRAFT',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `reviewed_by` bigint(20) unsigned DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `purchase_requisitions_pr_number_unique` (`pr_number`),
  KEY `purchase_requisitions_company_id_foreign` (`company_id`),
  KEY `purchase_requisitions_requested_by_foreign` (`requested_by`),
  KEY `purchase_requisitions_reviewed_by_foreign` (`reviewed_by`),
  KEY `purchase_requisitions_cost_centre_id_foreign` (`cost_centre_id`),
  CONSTRAINT `purchase_requisitions_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `purchase_requisitions_cost_centre_id_foreign` FOREIGN KEY (`cost_centre_id`) REFERENCES `cost_centres` (`id`),
  CONSTRAINT `purchase_requisitions_requested_by_foreign` FOREIGN KEY (`requested_by`) REFERENCES `users` (`id`),
  CONSTRAINT `purchase_requisitions_reviewed_by_foreign` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: purchase_requisition_items
CREATE TABLE IF NOT EXISTS `purchase_requisition_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `purchase_requisition_id` bigint(20) unsigned NOT NULL,
  `item_id` bigint(20) unsigned NOT NULL,
  `uom_id` bigint(20) unsigned DEFAULT NULL,
  `quantity` decimal(14,4) NOT NULL,
  `estimated_price` decimal(12,2) NOT NULL DEFAULT 0.00,
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `purchase_requisition_items_purchase_requisition_id_foreign` (`purchase_requisition_id`),
  KEY `purchase_requisition_items_item_id_foreign` (`item_id`),
  KEY `purchase_requisition_items_uom_id_foreign` (`uom_id`),
  CONSTRAINT `purchase_requisition_items_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`),
  CONSTRAINT `purchase_requisition_items_purchase_requisition_id_foreign` FOREIGN KEY (`purchase_requisition_id`) REFERENCES `purchase_requisitions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `purchase_requisition_items_uom_id_foreign` FOREIGN KEY (`uom_id`) REFERENCES `unit_of_measures` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: purchase_orders
CREATE TABLE IF NOT EXISTS `purchase_orders` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint(20) unsigned NOT NULL,
  `purchase_requisition_id` bigint(20) unsigned NOT NULL,
  `vendor_id` bigint(20) unsigned NOT NULL,
  `po_number` varchar(255) NOT NULL,
  `order_date` date NOT NULL,
  `expected_delivery_date` date DEFAULT NULL,
  `status` enum('DRAFT','SENT','APPROVED','PARTIALLY_RECEIVED','RECEIVED','CLOSED','CANCELLED') NOT NULL DEFAULT 'DRAFT',
  `budget_committed_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `terms_conditions` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `purchase_orders_po_number_unique` (`po_number`),
  KEY `purchase_orders_company_id_foreign` (`company_id`),
  KEY `purchase_orders_vendor_id_foreign` (`vendor_id`),
  KEY `purchase_orders_purchase_requisition_id_foreign` (`purchase_requisition_id`),
  CONSTRAINT `purchase_orders_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `purchase_orders_purchase_requisition_id_foreign` FOREIGN KEY (`purchase_requisition_id`) REFERENCES `purchase_requisitions` (`id`),
  CONSTRAINT `purchase_orders_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: purchase_order_items
CREATE TABLE IF NOT EXISTS `purchase_order_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `purchase_order_id` bigint(20) unsigned NOT NULL,
  `purchase_requisition_item_id` bigint(20) unsigned NOT NULL,
  `item_id` bigint(20) unsigned NOT NULL,
  `uom_id` bigint(20) unsigned DEFAULT NULL,
  `cost_centre_id` bigint(20) unsigned DEFAULT NULL,
  `quantity` decimal(14,4) NOT NULL,
  `unit_price` decimal(12,2) NOT NULL,
  `gst_rate` decimal(5,2) NOT NULL DEFAULT 0.00,
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `cgst_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `sgst_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `igst_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `budget_committed_amount` decimal(15,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `purchase_order_items_purchase_order_id_foreign` (`purchase_order_id`),
  KEY `purchase_order_items_purchase_requisition_item_id_foreign` (`purchase_requisition_item_id`),
  KEY `purchase_order_items_item_id_foreign` (`item_id`),
  KEY `purchase_order_items_uom_id_foreign` (`uom_id`),
  KEY `purchase_order_items_cost_centre_id_foreign` (`cost_centre_id`),
  CONSTRAINT `purchase_order_items_cost_centre_id_foreign` FOREIGN KEY (`cost_centre_id`) REFERENCES `cost_centres` (`id`),
  CONSTRAINT `purchase_order_items_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`),
  CONSTRAINT `purchase_order_items_purchase_order_id_foreign` FOREIGN KEY (`purchase_order_id`) REFERENCES `purchase_orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `purchase_order_items_purchase_requisition_item_id_foreign` FOREIGN KEY (`purchase_requisition_item_id`) REFERENCES `purchase_requisition_items` (`id`),
  CONSTRAINT `purchase_order_items_uom_id_foreign` FOREIGN KEY (`uom_id`) REFERENCES `unit_of_measures` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: goods_receipts
CREATE TABLE IF NOT EXISTS `goods_receipts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint(20) unsigned NOT NULL,
  `purchase_order_id` bigint(20) unsigned NOT NULL,
  `gr_number` varchar(255) NOT NULL,
  `received_date` date NOT NULL,
  `received_by` bigint(20) unsigned NOT NULL,
  `status` enum('DRAFT','COMPLETED') NOT NULL DEFAULT 'DRAFT',
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `goods_receipts_gr_number_unique` (`gr_number`),
  KEY `goods_receipts_company_id_foreign` (`company_id`),
  KEY `goods_receipts_purchase_order_id_foreign` (`purchase_order_id`),
  KEY `goods_receipts_received_by_foreign` (`received_by`),
  CONSTRAINT `goods_receipts_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `goods_receipts_purchase_order_id_foreign` FOREIGN KEY (`purchase_order_id`) REFERENCES `purchase_orders` (`id`),
  CONSTRAINT `goods_receipts_received_by_foreign` FOREIGN KEY (`received_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: goods_receipt_items
CREATE TABLE IF NOT EXISTS `goods_receipt_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `goods_receipt_id` bigint(20) unsigned NOT NULL,
  `purchase_order_item_id` bigint(20) unsigned NOT NULL,
  `received_uom_id` bigint(20) unsigned DEFAULT NULL,
  `received_quantity` decimal(14,4) NOT NULL,
  `accepted_quantity` decimal(14,4) NOT NULL,
  `rejected_quantity` decimal(14,4) NOT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `goods_receipt_items_goods_receipt_id_foreign` (`goods_receipt_id`),
  KEY `goods_receipt_items_purchase_order_item_id_foreign` (`purchase_order_item_id`),
  KEY `goods_receipt_items_received_uom_id_foreign` (`received_uom_id`),
  CONSTRAINT `goods_receipt_items_goods_receipt_id_foreign` FOREIGN KEY (`goods_receipt_id`) REFERENCES `goods_receipts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `goods_receipt_items_purchase_order_item_id_foreign` FOREIGN KEY (`purchase_order_item_id`) REFERENCES `purchase_order_items` (`id`),
  CONSTRAINT `goods_receipt_items_received_uom_id_foreign` FOREIGN KEY (`received_uom_id`) REFERENCES `unit_of_measures` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: supplier_invoices
CREATE TABLE IF NOT EXISTS `supplier_invoices` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint(20) unsigned NOT NULL,
  `vendor_id` bigint(20) unsigned DEFAULT NULL,
  `purchase_order_id` bigint(20) unsigned NOT NULL,
  `goods_receipt_id` bigint(20) unsigned NOT NULL,
  `invoice_number` varchar(255) NOT NULL,
  `supplier_invoice_number` varchar(255) NOT NULL,
  `invoice_date` date NOT NULL,
  `due_date` date DEFAULT NULL,
  `subtotal` decimal(15,2) NOT NULL DEFAULT 0.00,
  `tax_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `status` enum('DRAFT','SUBMITTED','APPROVED','REJECTED','PAID','POSTED','MATCHED','ON_HOLD','PARTIALLY_PAID') NOT NULL DEFAULT 'DRAFT',
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `submitted_by` bigint(20) unsigned DEFAULT NULL,
  `reviewed_by` bigint(20) unsigned DEFAULT NULL,
  `paid_by` bigint(20) unsigned DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `override_comment` text DEFAULT NULL,
  `overridden_by` bigint(20) unsigned DEFAULT NULL,
  `overridden_at` timestamp NULL DEFAULT NULL,
  `cgst_total` decimal(15,2) NOT NULL DEFAULT 0.00,
  `sgst_total` decimal(15,2) NOT NULL DEFAULT 0.00,
  `igst_total` decimal(15,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`id`),
  UNIQUE KEY `supplier_invoices_invoice_number_unique` (`invoice_number`),
  UNIQUE KEY `supplier_invoice_vendor_number_unique` (`vendor_id`,`supplier_invoice_number`),
  KEY `supplier_invoices_company_id_foreign` (`company_id`),
  KEY `supplier_invoices_purchase_order_id_foreign` (`purchase_order_id`),
  KEY `supplier_invoices_goods_receipt_id_foreign` (`goods_receipt_id`),
  KEY `supplier_invoices_submitted_by_foreign` (`submitted_by`),
  KEY `supplier_invoices_reviewed_by_foreign` (`reviewed_by`),
  KEY `supplier_invoices_paid_by_foreign` (`paid_by`),
  CONSTRAINT `supplier_invoices_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `supplier_invoices_goods_receipt_id_foreign` FOREIGN KEY (`goods_receipt_id`) REFERENCES `goods_receipts` (`id`),
  CONSTRAINT `supplier_invoices_paid_by_foreign` FOREIGN KEY (`paid_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `supplier_invoices_purchase_order_id_foreign` FOREIGN KEY (`purchase_order_id`) REFERENCES `purchase_orders` (`id`),
  CONSTRAINT `supplier_invoices_reviewed_by_foreign` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `supplier_invoices_submitted_by_foreign` FOREIGN KEY (`submitted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `supplier_invoices_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: supplier_invoice_items
CREATE TABLE IF NOT EXISTS `supplier_invoice_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `supplier_invoice_id` bigint(20) unsigned NOT NULL,
  `goods_receipt_item_id` bigint(20) unsigned NOT NULL,
  `item_id` bigint(20) unsigned NOT NULL,
  `uom_id` bigint(20) unsigned DEFAULT NULL,
  `quantity` decimal(14,4) NOT NULL,
  `unit_price` decimal(12,2) NOT NULL,
  `gst_rate` decimal(5,2) NOT NULL DEFAULT 0.00,
  `tax_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `line_total` decimal(15,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `match_status` varchar(20) NOT NULL DEFAULT 'PENDING',
  `match_reason` text DEFAULT NULL,
  `cgst_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `sgst_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `igst_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`id`),
  KEY `supplier_invoice_items_supplier_invoice_id_foreign` (`supplier_invoice_id`),
  KEY `supplier_invoice_items_goods_receipt_item_id_foreign` (`goods_receipt_item_id`),
  KEY `supplier_invoice_items_item_id_foreign` (`item_id`),
  KEY `supplier_invoice_items_uom_id_foreign` (`uom_id`),
  CONSTRAINT `supplier_invoice_items_goods_receipt_item_id_foreign` FOREIGN KEY (`goods_receipt_item_id`) REFERENCES `goods_receipt_items` (`id`),
  CONSTRAINT `supplier_invoice_items_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`),
  CONSTRAINT `supplier_invoice_items_supplier_invoice_id_foreign` FOREIGN KEY (`supplier_invoice_id`) REFERENCES `supplier_invoices` (`id`) ON DELETE CASCADE,
  CONSTRAINT `supplier_invoice_items_uom_id_foreign` FOREIGN KEY (`uom_id`) REFERENCES `unit_of_measures` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: approval_rules
CREATE TABLE IF NOT EXISTS `approval_rules` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint(20) unsigned NOT NULL,
  `document_type` varchar(20) NOT NULL,
  `category` varchar(30) DEFAULT NULL,
  `cost_centre_id` bigint(20) unsigned DEFAULT NULL,
  `min_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `max_amount` decimal(15,2) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `approval_rules_cost_centre_id_foreign` (`cost_centre_id`),
  KEY `approval_rules_lookup_index` (`company_id`,`document_type`,`is_active`),
  CONSTRAINT `approval_rules_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `approval_rules_cost_centre_id_foreign` FOREIGN KEY (`cost_centre_id`) REFERENCES `cost_centres` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `approval_rules` (`id`, `company_id`, `document_type`, `category`, `cost_centre_id`, `min_amount`, `max_amount`, `is_active`, `created_at`, `updated_at`) VALUES ('1', '1', 'PR', NULL, NULL, '0.00', '50000.00', '1', '2026-11-24 10:21:19', '2026-11-24 10:21:19');
INSERT INTO `approval_rules` (`id`, `company_id`, `document_type`, `category`, `cost_centre_id`, `min_amount`, `max_amount`, `is_active`, `created_at`, `updated_at`) VALUES ('2', '1', 'PR', NULL, NULL, '50000.01', '500000.00', '1', '2026-11-24 10:21:19', '2026-11-24 10:21:19');
INSERT INTO `approval_rules` (`id`, `company_id`, `document_type`, `category`, `cost_centre_id`, `min_amount`, `max_amount`, `is_active`, `created_at`, `updated_at`) VALUES ('3', '1', 'PR', NULL, NULL, '500000.01', NULL, '1', '2026-11-24 10:21:19', '2026-11-24 10:21:19');
INSERT INTO `approval_rules` (`id`, `company_id`, `document_type`, `category`, `cost_centre_id`, `min_amount`, `max_amount`, `is_active`, `created_at`, `updated_at`) VALUES ('4', '1', 'PR', 'IT', NULL, '0.00', '50000.00', '1', '2026-11-24 10:21:19', '2026-11-24 10:21:19');
INSERT INTO `approval_rules` (`id`, `company_id`, `document_type`, `category`, `cost_centre_id`, `min_amount`, `max_amount`, `is_active`, `created_at`, `updated_at`) VALUES ('5', '1', 'PR', 'IT', NULL, '50000.01', '500000.00', '1', '2026-11-24 10:21:19', '2026-11-24 10:21:19');
INSERT INTO `approval_rules` (`id`, `company_id`, `document_type`, `category`, `cost_centre_id`, `min_amount`, `max_amount`, `is_active`, `created_at`, `updated_at`) VALUES ('6', '1', 'PR', 'IT', NULL, '500000.01', NULL, '1', '2026-11-24 10:21:19', '2026-11-24 10:21:19');
INSERT INTO `approval_rules` (`id`, `company_id`, `document_type`, `category`, `cost_centre_id`, `min_amount`, `max_amount`, `is_active`, `created_at`, `updated_at`) VALUES ('7', '1', 'PO', NULL, NULL, '0.00', '200000.00', '1', '2026-11-24 10:21:19', '2026-11-24 10:21:19');
INSERT INTO `approval_rules` (`id`, `company_id`, `document_type`, `category`, `cost_centre_id`, `min_amount`, `max_amount`, `is_active`, `created_at`, `updated_at`) VALUES ('8', '1', 'PO', NULL, NULL, '200000.01', NULL, '1', '2026-11-24 10:21:19', '2026-11-24 10:21:19');
INSERT INTO `approval_rules` (`id`, `company_id`, `document_type`, `category`, `cost_centre_id`, `min_amount`, `max_amount`, `is_active`, `created_at`, `updated_at`) VALUES ('9', '2', 'PR', NULL, NULL, '0.00', '50000.00', '1', '2026-11-24 10:21:19', '2026-11-24 10:21:19');
INSERT INTO `approval_rules` (`id`, `company_id`, `document_type`, `category`, `cost_centre_id`, `min_amount`, `max_amount`, `is_active`, `created_at`, `updated_at`) VALUES ('10', '2', 'PR', NULL, NULL, '50000.01', '500000.00', '1', '2026-11-24 10:21:19', '2026-11-24 10:21:19');
INSERT INTO `approval_rules` (`id`, `company_id`, `document_type`, `category`, `cost_centre_id`, `min_amount`, `max_amount`, `is_active`, `created_at`, `updated_at`) VALUES ('11', '2', 'PR', NULL, NULL, '500000.01', NULL, '1', '2026-11-24 10:21:19', '2026-11-24 10:21:19');
INSERT INTO `approval_rules` (`id`, `company_id`, `document_type`, `category`, `cost_centre_id`, `min_amount`, `max_amount`, `is_active`, `created_at`, `updated_at`) VALUES ('12', '2', 'PR', 'IT', NULL, '0.00', '50000.00', '1', '2026-11-24 10:21:19', '2026-11-24 10:21:19');
INSERT INTO `approval_rules` (`id`, `company_id`, `document_type`, `category`, `cost_centre_id`, `min_amount`, `max_amount`, `is_active`, `created_at`, `updated_at`) VALUES ('13', '2', 'PR', 'IT', NULL, '50000.01', '500000.00', '1', '2026-11-24 10:21:19', '2026-11-24 10:21:19');
INSERT INTO `approval_rules` (`id`, `company_id`, `document_type`, `category`, `cost_centre_id`, `min_amount`, `max_amount`, `is_active`, `created_at`, `updated_at`) VALUES ('14', '2', 'PR', 'IT', NULL, '500000.01', NULL, '1', '2026-11-24 10:21:19', '2026-11-24 10:21:19');
INSERT INTO `approval_rules` (`id`, `company_id`, `document_type`, `category`, `cost_centre_id`, `min_amount`, `max_amount`, `is_active`, `created_at`, `updated_at`) VALUES ('15', '2', 'PO', NULL, NULL, '0.00', '200000.00', '1', '2026-11-24 10:21:19', '2026-11-24 10:21:19');
INSERT INTO `approval_rules` (`id`, `company_id`, `document_type`, `category`, `cost_centre_id`, `min_amount`, `max_amount`, `is_active`, `created_at`, `updated_at`) VALUES ('16', '2', 'PO', NULL, NULL, '200000.01', NULL, '1', '2026-11-24 10:21:19', '2026-11-24 10:21:19');

-- Table: approval_rule_steps
CREATE TABLE IF NOT EXISTS `approval_rule_steps` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `approval_rule_id` bigint(20) unsigned NOT NULL,
  `step_order` int(10) unsigned NOT NULL,
  `approver_user_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `approval_rule_step_order_unique` (`approval_rule_id`,`step_order`),
  KEY `approval_rule_steps_approver_user_id_foreign` (`approver_user_id`),
  CONSTRAINT `approval_rule_steps_approval_rule_id_foreign` FOREIGN KEY (`approval_rule_id`) REFERENCES `approval_rules` (`id`) ON DELETE CASCADE,
  CONSTRAINT `approval_rule_steps_approver_user_id_foreign` FOREIGN KEY (`approver_user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `approval_rule_steps` (`id`, `approval_rule_id`, `step_order`, `approver_user_id`, `created_at`, `updated_at`) VALUES ('1', '1', '1', '3', '2026-11-24 10:21:19', '2026-11-24 10:21:19');
INSERT INTO `approval_rule_steps` (`id`, `approval_rule_id`, `step_order`, `approver_user_id`, `created_at`, `updated_at`) VALUES ('2', '2', '1', '3', '2026-11-24 10:21:19', '2026-11-24 10:21:19');
INSERT INTO `approval_rule_steps` (`id`, `approval_rule_id`, `step_order`, `approver_user_id`, `created_at`, `updated_at`) VALUES ('3', '2', '2', '6', '2026-11-24 10:21:19', '2026-11-24 10:21:19');
INSERT INTO `approval_rule_steps` (`id`, `approval_rule_id`, `step_order`, `approver_user_id`, `created_at`, `updated_at`) VALUES ('4', '3', '1', '3', '2026-11-24 10:21:19', '2026-11-24 10:21:19');
INSERT INTO `approval_rule_steps` (`id`, `approval_rule_id`, `step_order`, `approver_user_id`, `created_at`, `updated_at`) VALUES ('5', '3', '2', '6', '2026-11-24 10:21:19', '2026-11-24 10:21:19');
INSERT INTO `approval_rule_steps` (`id`, `approval_rule_id`, `step_order`, `approver_user_id`, `created_at`, `updated_at`) VALUES ('6', '3', '3', '7', '2026-11-24 10:21:19', '2026-11-24 10:21:19');
INSERT INTO `approval_rule_steps` (`id`, `approval_rule_id`, `step_order`, `approver_user_id`, `created_at`, `updated_at`) VALUES ('7', '4', '1', '3', '2026-11-24 10:21:19', '2026-11-24 10:21:19');
INSERT INTO `approval_rule_steps` (`id`, `approval_rule_id`, `step_order`, `approver_user_id`, `created_at`, `updated_at`) VALUES ('8', '4', '2', '5', '2026-11-24 10:21:19', '2026-11-24 10:21:19');
INSERT INTO `approval_rule_steps` (`id`, `approval_rule_id`, `step_order`, `approver_user_id`, `created_at`, `updated_at`) VALUES ('9', '5', '1', '3', '2026-11-24 10:21:19', '2026-11-24 10:21:19');
INSERT INTO `approval_rule_steps` (`id`, `approval_rule_id`, `step_order`, `approver_user_id`, `created_at`, `updated_at`) VALUES ('10', '5', '2', '5', '2026-11-24 10:21:19', '2026-11-24 10:21:19');
INSERT INTO `approval_rule_steps` (`id`, `approval_rule_id`, `step_order`, `approver_user_id`, `created_at`, `updated_at`) VALUES ('11', '5', '3', '6', '2026-11-24 10:21:19', '2026-11-24 10:21:19');
INSERT INTO `approval_rule_steps` (`id`, `approval_rule_id`, `step_order`, `approver_user_id`, `created_at`, `updated_at`) VALUES ('12', '6', '1', '3', '2026-11-24 10:21:19', '2026-11-24 10:21:19');
INSERT INTO `approval_rule_steps` (`id`, `approval_rule_id`, `step_order`, `approver_user_id`, `created_at`, `updated_at`) VALUES ('13', '6', '2', '5', '2026-11-24 10:21:19', '2026-11-24 10:21:19');
INSERT INTO `approval_rule_steps` (`id`, `approval_rule_id`, `step_order`, `approver_user_id`, `created_at`, `updated_at`) VALUES ('14', '6', '3', '6', '2026-11-24 10:21:19', '2026-11-24 10:21:19');
INSERT INTO `approval_rule_steps` (`id`, `approval_rule_id`, `step_order`, `approver_user_id`, `created_at`, `updated_at`) VALUES ('15', '6', '4', '7', '2026-11-24 10:21:19', '2026-11-24 10:21:19');
INSERT INTO `approval_rule_steps` (`id`, `approval_rule_id`, `step_order`, `approver_user_id`, `created_at`, `updated_at`) VALUES ('16', '7', '1', '6', '2026-11-24 10:21:19', '2026-11-24 10:21:19');
INSERT INTO `approval_rule_steps` (`id`, `approval_rule_id`, `step_order`, `approver_user_id`, `created_at`, `updated_at`) VALUES ('17', '8', '1', '6', '2026-11-24 10:21:19', '2026-11-24 10:21:19');
INSERT INTO `approval_rule_steps` (`id`, `approval_rule_id`, `step_order`, `approver_user_id`, `created_at`, `updated_at`) VALUES ('18', '8', '2', '7', '2026-11-24 10:21:19', '2026-11-24 10:21:19');
INSERT INTO `approval_rule_steps` (`id`, `approval_rule_id`, `step_order`, `approver_user_id`, `created_at`, `updated_at`) VALUES ('19', '9', '1', '13', '2026-11-24 10:21:19', '2026-11-24 10:21:19');
INSERT INTO `approval_rule_steps` (`id`, `approval_rule_id`, `step_order`, `approver_user_id`, `created_at`, `updated_at`) VALUES ('20', '10', '1', '13', '2026-11-24 10:21:19', '2026-11-24 10:21:19');
INSERT INTO `approval_rule_steps` (`id`, `approval_rule_id`, `step_order`, `approver_user_id`, `created_at`, `updated_at`) VALUES ('21', '10', '2', '17', '2026-11-24 10:21:19', '2026-11-24 10:21:19');
INSERT INTO `approval_rule_steps` (`id`, `approval_rule_id`, `step_order`, `approver_user_id`, `created_at`, `updated_at`) VALUES ('22', '11', '1', '13', '2026-11-24 10:21:19', '2026-11-24 10:21:19');
INSERT INTO `approval_rule_steps` (`id`, `approval_rule_id`, `step_order`, `approver_user_id`, `created_at`, `updated_at`) VALUES ('23', '11', '2', '17', '2026-11-24 10:21:19', '2026-11-24 10:21:19');
INSERT INTO `approval_rule_steps` (`id`, `approval_rule_id`, `step_order`, `approver_user_id`, `created_at`, `updated_at`) VALUES ('24', '11', '3', '18', '2026-11-24 10:21:19', '2026-11-24 10:21:19');
INSERT INTO `approval_rule_steps` (`id`, `approval_rule_id`, `step_order`, `approver_user_id`, `created_at`, `updated_at`) VALUES ('25', '12', '1', '13', '2026-11-24 10:21:19', '2026-11-24 10:21:19');
INSERT INTO `approval_rule_steps` (`id`, `approval_rule_id`, `step_order`, `approver_user_id`, `created_at`, `updated_at`) VALUES ('26', '12', '2', '16', '2026-11-24 10:21:19', '2026-11-24 10:21:19');
INSERT INTO `approval_rule_steps` (`id`, `approval_rule_id`, `step_order`, `approver_user_id`, `created_at`, `updated_at`) VALUES ('27', '13', '1', '13', '2026-11-24 10:21:19', '2026-11-24 10:21:19');
INSERT INTO `approval_rule_steps` (`id`, `approval_rule_id`, `step_order`, `approver_user_id`, `created_at`, `updated_at`) VALUES ('28', '13', '2', '16', '2026-11-24 10:21:19', '2026-11-24 10:21:19');
INSERT INTO `approval_rule_steps` (`id`, `approval_rule_id`, `step_order`, `approver_user_id`, `created_at`, `updated_at`) VALUES ('29', '13', '3', '17', '2026-11-24 10:21:19', '2026-11-24 10:21:19');
INSERT INTO `approval_rule_steps` (`id`, `approval_rule_id`, `step_order`, `approver_user_id`, `created_at`, `updated_at`) VALUES ('30', '14', '1', '13', '2026-11-24 10:21:19', '2026-11-24 10:21:19');
INSERT INTO `approval_rule_steps` (`id`, `approval_rule_id`, `step_order`, `approver_user_id`, `created_at`, `updated_at`) VALUES ('31', '14', '2', '16', '2026-11-24 10:21:19', '2026-11-24 10:21:19');
INSERT INTO `approval_rule_steps` (`id`, `approval_rule_id`, `step_order`, `approver_user_id`, `created_at`, `updated_at`) VALUES ('32', '14', '3', '17', '2026-11-24 10:21:19', '2026-11-24 10:21:19');
INSERT INTO `approval_rule_steps` (`id`, `approval_rule_id`, `step_order`, `approver_user_id`, `created_at`, `updated_at`) VALUES ('33', '14', '4', '18', '2026-11-24 10:21:19', '2026-11-24 10:21:19');
INSERT INTO `approval_rule_steps` (`id`, `approval_rule_id`, `step_order`, `approver_user_id`, `created_at`, `updated_at`) VALUES ('34', '15', '1', '17', '2026-11-24 10:21:19', '2026-11-24 10:21:19');
INSERT INTO `approval_rule_steps` (`id`, `approval_rule_id`, `step_order`, `approver_user_id`, `created_at`, `updated_at`) VALUES ('35', '16', '1', '17', '2026-11-24 10:21:19', '2026-11-24 10:21:19');
INSERT INTO `approval_rule_steps` (`id`, `approval_rule_id`, `step_order`, `approver_user_id`, `created_at`, `updated_at`) VALUES ('36', '16', '2', '18', '2026-11-24 10:21:19', '2026-11-24 10:21:19');

-- Table: approval_requests
CREATE TABLE IF NOT EXISTS `approval_requests` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint(20) unsigned NOT NULL,
  `document_type` varchar(20) NOT NULL,
  `document_id` bigint(20) unsigned NOT NULL,
  `requested_by` bigint(20) unsigned NOT NULL,
  `current_step` int(10) unsigned NOT NULL DEFAULT 1,
  `status` varchar(20) NOT NULL DEFAULT 'PENDING',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `approval_request_document_unique` (`document_type`,`document_id`),
  KEY `approval_requests_requested_by_foreign` (`requested_by`),
  KEY `approval_request_status_index` (`company_id`,`status`,`current_step`),
  CONSTRAINT `approval_requests_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `approval_requests_requested_by_foreign` FOREIGN KEY (`requested_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: approval_request_steps
CREATE TABLE IF NOT EXISTS `approval_request_steps` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `approval_request_id` bigint(20) unsigned NOT NULL,
  `step_order` int(10) unsigned NOT NULL,
  `approver_user_id` bigint(20) unsigned NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'PENDING',
  `acted_by` bigint(20) unsigned DEFAULT NULL,
  `acted_at` timestamp NULL DEFAULT NULL,
  `comments` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `last_reminded_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `approval_request_step_order_unique` (`approval_request_id`,`step_order`),
  KEY `approval_request_steps_acted_by_foreign` (`acted_by`),
  KEY `approval_request_approver_status_index` (`approver_user_id`,`status`),
  CONSTRAINT `approval_request_steps_acted_by_foreign` FOREIGN KEY (`acted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `approval_request_steps_approval_request_id_foreign` FOREIGN KEY (`approval_request_id`) REFERENCES `approval_requests` (`id`) ON DELETE CASCADE,
  CONSTRAINT `approval_request_steps_approver_user_id_foreign` FOREIGN KEY (`approver_user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: audit_logs
CREATE TABLE IF NOT EXISTS `audit_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `auditable_type` varchar(255) NOT NULL,
  `auditable_id` bigint(20) unsigned NOT NULL,
  `from_status` varchar(40) DEFAULT NULL,
  `to_status` varchar(40) NOT NULL,
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `audit_logs_company_id_foreign` (`company_id`),
  KEY `audit_logs_user_id_foreign` (`user_id`),
  KEY `audit_logs_auditable_type_auditable_id_index` (`auditable_type`,`auditable_id`),
  CONSTRAINT `audit_logs_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `audit_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: vendor_payments
CREATE TABLE IF NOT EXISTS `vendor_payments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint(20) unsigned NOT NULL,
  `vendor_id` bigint(20) unsigned NOT NULL,
  `paid_by` bigint(20) unsigned NOT NULL,
  `payment_number` varchar(255) NOT NULL,
  `payment_date` date NOT NULL,
  `gross_amount` decimal(15,2) NOT NULL,
  `tds_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `net_amount` decimal(15,2) NOT NULL,
  `reference` varchar(255) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `vendor_payments_payment_number_unique` (`payment_number`),
  KEY `vendor_payments_company_id_foreign` (`company_id`),
  KEY `vendor_payments_vendor_id_foreign` (`vendor_id`),
  KEY `vendor_payments_paid_by_foreign` (`paid_by`),
  CONSTRAINT `vendor_payments_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `vendor_payments_paid_by_foreign` FOREIGN KEY (`paid_by`) REFERENCES `users` (`id`),
  CONSTRAINT `vendor_payments_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: vendor_payment_allocations
CREATE TABLE IF NOT EXISTS `vendor_payment_allocations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `vendor_payment_id` bigint(20) unsigned NOT NULL,
  `supplier_invoice_id` bigint(20) unsigned NOT NULL,
  `gross_amount` decimal(15,2) NOT NULL,
  `tds_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `vpa_payment_invoice_unique` (`vendor_payment_id`,`supplier_invoice_id`),
  KEY `vendor_payment_allocations_supplier_invoice_id_foreign` (`supplier_invoice_id`),
  CONSTRAINT `vendor_payment_allocations_supplier_invoice_id_foreign` FOREIGN KEY (`supplier_invoice_id`) REFERENCES `supplier_invoices` (`id`),
  CONSTRAINT `vendor_payment_allocations_vendor_payment_id_foreign` FOREIGN KEY (`vendor_payment_id`) REFERENCES `vendor_payments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: notifications
CREATE TABLE IF NOT EXISTS `notifications` (
  `id` char(36) NOT NULL,
  `type` varchar(255) NOT NULL,
  `notifiable_type` varchar(255) NOT NULL,
  `notifiable_id` bigint(20) unsigned NOT NULL,
  `data` text NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS=1;
