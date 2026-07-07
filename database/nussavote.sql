-- ============================================================
--  NUSSA Vote – Student Department Voting System
--  Database Schema | MySQL 5.7+ / MariaDB 10+
-- ============================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE DATABASE IF NOT EXISTS `nussavote` 
  CHARACTER SET utf8mb4 
  COLLATE utf8mb4_unicode_ci;

USE `nussavote`;

-- -------------------------------------------------------
-- Table: departments
-- -------------------------------------------------------
CREATE TABLE `departments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `code` varchar(20) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------
-- Table: tokens
-- -------------------------------------------------------
CREATE TABLE `tokens` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `token` varchar(20) NOT NULL,
  `status` enum('unused','used') NOT NULL DEFAULT 'unused',
  `matric_number` varchar(50) DEFAULT NULL,
  `used_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `token` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------
-- Table: users
-- -------------------------------------------------------
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `department_id` int(11) DEFAULT NULL,
  `first_name` varchar(80) NOT NULL,
  `last_name` varchar(80) NOT NULL,
  `student_id` varchar(50) NOT NULL COMMENT 'Matriculation/Student Number',
  `email` varchar(150) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','voter') NOT NULL DEFAULT 'voter',
  `level` varchar(10) DEFAULT NULL COMMENT 'e.g. 100, 200, 300',
  `gender` enum('male','female','other') DEFAULT NULL,
  `profile_photo` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `last_login` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `student_id` (`student_id`),
  UNIQUE KEY `email` (`email`),
  KEY `fk_users_department` (`department_id`),
  CONSTRAINT `fk_users_department` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------
-- Table: elections
-- -------------------------------------------------------
CREATE TABLE `elections` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `department_id` int(11) DEFAULT NULL COMMENT 'NULL = all departments',
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `session` varchar(20) NOT NULL COMMENT 'e.g. 2025/2026',
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `status` enum('pending','active','closed','published') NOT NULL DEFAULT 'pending',
  `created_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_elections_department` (`department_id`),
  KEY `fk_elections_creator` (`created_by`),
  CONSTRAINT `fk_elections_department` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_elections_creator` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------
-- Table: positions
-- -------------------------------------------------------
CREATE TABLE `positions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `election_id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL COMMENT 'e.g. President, Secretary',
  `description` text DEFAULT NULL,
  `max_votes` int(11) NOT NULL DEFAULT 1 COMMENT 'Max candidates a voter can pick for this position',
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_positions_election` (`election_id`),
  CONSTRAINT `fk_positions_election` FOREIGN KEY (`election_id`) REFERENCES `elections` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------
-- Table: candidates
-- -------------------------------------------------------
CREATE TABLE `candidates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `election_id` int(11) NOT NULL,
  `position_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `manifesto` text DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `vote_count` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_candidate` (`election_id`,`position_id`,`user_id`),
  KEY `fk_candidates_position` (`position_id`),
  KEY `fk_candidates_user` (`user_id`),
  CONSTRAINT `fk_candidates_election` FOREIGN KEY (`election_id`) REFERENCES `elections` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_candidates_position` FOREIGN KEY (`position_id`) REFERENCES `positions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_candidates_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------
-- Table: votes
-- -------------------------------------------------------
CREATE TABLE `votes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `election_id` int(11) NOT NULL,
  `position_id` int(11) NOT NULL,
  `candidate_id` int(11) NOT NULL,
  `voter_id` int(11) NOT NULL,
  `voted_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_vote` (`election_id`,`position_id`,`voter_id`) COMMENT 'One vote per position per election',
  KEY `fk_votes_candidate` (`candidate_id`),
  KEY `fk_votes_voter` (`voter_id`),
  CONSTRAINT `fk_votes_candidate` FOREIGN KEY (`candidate_id`) REFERENCES `candidates` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_votes_election` FOREIGN KEY (`election_id`) REFERENCES `elections` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_votes_position` FOREIGN KEY (`position_id`) REFERENCES `positions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_votes_voter` FOREIGN KEY (`voter_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------
-- Table: settings
-- -------------------------------------------------------
CREATE TABLE `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(100) NOT NULL,
  `value` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `key` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------
-- Seed: Default Department
-- -------------------------------------------------------
INSERT INTO `departments` (`name`, `code`, `description`) VALUES
('Computer Science', 'CSC', 'Department of Computer Science'),
('Business Administration', 'BUS', 'Department of Business Administration'),
('Electrical Engineering', 'EEE', 'Department of Electrical & Electronic Engineering');

-- -------------------------------------------------------
-- Seed: Admin User  (password: Admin@1234)
-- -------------------------------------------------------
INSERT INTO `users` (`department_id`, `first_name`, `last_name`, `student_id`, `email`, `phone`, `password`, `role`, `is_active`) VALUES
(1, 'Super', 'Admin', 'ADMIN001', 'admin@nussavote.com', '08000000000', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 1);

-- -------------------------------------------------------
-- Seed: Default Settings
-- -------------------------------------------------------
INSERT INTO `settings` (`key`, `value`) VALUES
('site_name', 'NUSSA Vote'),
('site_tagline', 'Your Voice. Your Choice.'),
('institution_name', 'University Student Union'),
('allow_self_register', '0'),
('maintenance_mode', '0'),
('results_public', '0');

COMMIT;
