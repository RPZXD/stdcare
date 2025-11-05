-- Migration: Create forgot_card table
-- Created: 2025-11-05
-- Usage: Run this SQL in your database (phpMyAdmin or mysql CLI)

CREATE TABLE IF NOT EXISTS `forgot_card` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `student_id` VARCHAR(50) NOT NULL,
  `forgot_date` DATE NOT NULL,
  `staff_id` VARCHAR(50) DEFAULT NULL,
  `term` VARCHAR(20) DEFAULT NULL,
  `year` VARCHAR(20) DEFAULT NULL,
  `note` TEXT DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_student_date` (`student_id`,`forgot_date`),
  KEY `idx_student` (`student_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Optional: grant appropriate privileges to the DB user if needed.
