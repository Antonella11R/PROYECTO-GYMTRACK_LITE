CREATE DATABASE IF NOT EXISTS `gymtrack_lite`
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE `gymtrack_lite`;

DROP TABLE IF EXISTS `training_session_items`;
DROP TABLE IF EXISTS `training_sessions`;
DROP TABLE IF EXISTS `exercises`;
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `detalle_entrenamiento`;
DROP TABLE IF EXISTS `entrenamientos`;
DROP TABLE IF EXISTS `ejercicios`;
DROP TABLE IF EXISTS `usuarios`;

CREATE TABLE `users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `role` ENUM('admin', 'trainer', 'user') NOT NULL,
  `trainer_id` INT UNSIGNED NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_trainer_id_index` (`trainer_id`),
  CONSTRAINT `users_trainer_id_fk`
    FOREIGN KEY (`trainer_id`) REFERENCES `users` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `exercises` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `muscle_group` VARCHAR(100) NOT NULL,
  `description` TEXT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `training_sessions` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `athlete_user_id` INT UNSIGNED NOT NULL,
  `recorded_by_user_id` INT UNSIGNED NOT NULL,
  `performed_on` DATE NOT NULL,
  `duration_minutes` INT NOT NULL,
  `notes` TEXT NULL,
  `created_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  KEY `training_sessions_athlete_index` (`athlete_user_id`),
  KEY `training_sessions_recorder_index` (`recorded_by_user_id`),
  CONSTRAINT `training_sessions_athlete_fk`
    FOREIGN KEY (`athlete_user_id`) REFERENCES `users` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `training_sessions_recorder_fk`
    FOREIGN KEY (`recorded_by_user_id`) REFERENCES `users` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `training_session_items` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `training_session_id` INT UNSIGNED NOT NULL,
  `exercise_id` INT UNSIGNED NOT NULL,
  `sets` INT NOT NULL,
  `repetitions` INT NOT NULL,
  `weight` DECIMAL(8,2) NOT NULL,
  `rpe` TINYINT UNSIGNED NOT NULL,
  `position` INT NOT NULL,
  PRIMARY KEY (`id`),
  KEY `training_session_items_session_index` (`training_session_id`),
  KEY `training_session_items_exercise_index` (`exercise_id`),
  CONSTRAINT `training_session_items_session_fk`
    FOREIGN KEY (`training_session_id`) REFERENCES `training_sessions` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `training_session_items_exercise_fk`
    FOREIGN KEY (`exercise_id`) REFERENCES `exercises` (`id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
