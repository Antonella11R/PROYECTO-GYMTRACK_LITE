USE `gymtrack_lite`;

INSERT INTO `users` (`id`, `name`, `email`, `password_hash`, `role`, `trainer_id`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Admin Demo', 'admin@gymtrack.test', '$2y$10$D/7h6vGbk2ijU1NOPSB0SuRllc2fcK/jWLAzmJmfG3Kr5S5yeMidS', 'admin', NULL, 1, '2026-05-01 08:00:00', '2026-05-01 08:00:00'),
(2, 'Trainer Demo', 'trainer@gymtrack.test', '$2y$10$D/7h6vGbk2ijU1NOPSB0SuRllc2fcK/jWLAzmJmfG3Kr5S5yeMidS', 'trainer', NULL, 1, '2026-05-01 08:05:00', '2026-05-01 08:05:00'),
(3, 'User Demo', 'user@gymtrack.test', '$2y$10$D/7h6vGbk2ijU1NOPSB0SuRllc2fcK/jWLAzmJmfG3Kr5S5yeMidS', 'user', 2, 1, '2026-05-01 08:10:00', '2026-05-01 08:10:00');

INSERT INTO `exercises` (`id`, `name`, `muscle_group`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Press de banca', 'Pecho', 'Empuje horizontal con barra.', 1, '2026-05-01 09:00:00', '2026-05-01 09:00:00'),
(2, 'Sentadilla trasera', 'Piernas', 'Sentadilla con barra sobre la espalda.', 1, '2026-05-01 09:00:00', '2026-05-01 09:00:00'),
(3, 'Peso muerto rumano', 'Piernas', 'Dominante de cadera con control excéntrico.', 1, '2026-05-01 09:00:00', '2026-05-01 09:00:00'),
(4, 'Remo con barra', 'Espalda', 'Tirón horizontal con barra.', 1, '2026-05-01 09:00:00', '2026-05-01 09:00:00'),
(5, 'Press militar', 'Hombros', 'Empuje vertical con barra.', 1, '2026-05-01 09:00:00', '2026-05-01 09:00:00'),
(6, 'Curl de bíceps', 'Brazos', 'Flexión de codo con barra Z.', 1, '2026-05-01 09:00:00', '2026-05-01 09:00:00');

INSERT INTO `training_sessions` (`id`, `athlete_user_id`, `recorded_by_user_id`, `performed_on`, `duration_minutes`, `notes`, `created_at`) VALUES
(1, 3, 2, '2026-05-02', 65, 'Primer bloque técnico con énfasis en control.', '2026-05-02 18:00:00'),
(2, 3, 3, '2026-05-06', 70, 'Sesión autónoma enfocada en progresión de fuerza.', '2026-05-06 18:30:00'),
(3, 3, 2, '2026-05-10', 75, 'Mejor ejecución y más confianza en las cargas.', '2026-05-10 19:00:00'),
(4, 3, 3, '2026-05-14', 72, 'Cierre de microciclo con volumen moderado.', '2026-05-14 18:45:00');

INSERT INTO `training_session_items` (`training_session_id`, `exercise_id`, `sets`, `repetitions`, `weight`, `rpe`, `position`) VALUES
(1, 1, 4, 8, 45.00, 7, 1),
(1, 4, 4, 10, 35.00, 7, 2),
(1, 6, 3, 12, 15.00, 6, 3),
(2, 2, 4, 6, 60.00, 8, 1),
(2, 3, 4, 8, 55.00, 7, 2),
(2, 1, 4, 8, 47.50, 8, 3),
(3, 1, 5, 5, 50.00, 8, 1),
(3, 5, 4, 8, 30.00, 7, 2),
(3, 4, 4, 8, 40.00, 8, 3),
(4, 2, 4, 6, 65.00, 8, 1),
(4, 1, 4, 6, 52.50, 8, 2),
(4, 3, 3, 10, 60.00, 8, 3);
