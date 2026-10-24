-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 24, 2026 at 02:52 PM
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
-- Database: `movie_archive`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(10) UNSIGNED NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password_hash`) VALUES
(1, 'admin', '$2b$10$RhVyTVMTsHFgAyMWAtjhcetYPtxFTuAy05/QAaaWAI0C4aDvFv6kO');

-- --------------------------------------------------------

--
-- Table structure for table `genres`
--

CREATE TABLE `genres` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `genres`
--

INSERT INTO `genres` (`id`, `name`) VALUES
(4, 'Animation'),
(6, 'Crime'),
(1, 'Drama'),
(5, 'Fantasy'),
(8, 'Mystery'),
(7, 'Romance'),
(2, 'Sci-Fi'),
(3, 'Thriller');

-- --------------------------------------------------------

--
-- Table structure for table `movies`
--

CREATE TABLE `movies` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(150) NOT NULL,
  `release_date` date NOT NULL,
  `rating` decimal(3,1) NOT NULL DEFAULT 0.0,
  `director` varchar(120) NOT NULL,
  `cast_list` varchar(500) NOT NULL DEFAULT '',
  `synopsis` text NOT NULL,
  `poster_url` varchar(500) NOT NULL DEFAULT '',
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `movies`
--

INSERT INTO `movies` (`id`, `title`, `release_date`, `rating`, `director`, `cast_list`, `synopsis`, `poster_url`, `is_featured`, `created_at`) VALUES
(1, 'Arrival', '2016-11-11', 7.9, 'Denis Villeneuve', 'Amy Adams, Jeremy Renner, Forest Whitaker, Michael Stuhlbarg', 'Twelve alien vessels settle over Earth without explanation. A linguist is brought in to learn their language, and the more she understands it, the less she experiences time the way she used to. A quiet, patient film about communication and grief.', 'https://image.tmdb.org/t/p/w500/x2FJsf1ElAgr63Y3PNPtJrcmpoe.jpg', 0, '2026-10-23 07:51:06'),
(2, 'Parasite', '2019-05-30', 8.5, 'Bong Joon-ho', 'Song Kang-ho, Lee Sun-kyun, Cho Yeo-jeong, Choi Woo-shik, Park So-dam', 'A struggling family talks its way into the household of a wealthy one, one job at a time. What begins as a clever con turns into something far darker as the two worlds collide.', 'https://image.tmdb.org/t/p/w500/7IiTTgloJzvGI1TAYymCfbfl3vT.jpg', 0, '2026-10-23 07:51:06'),
(3, 'Spirited Away', '2001-07-20', 8.5, 'Hayao Miyazaki', 'Rumi Hiiragi, Miyu Irino, Mari Natsuki, Takashi Naito', 'A ten-year-old girl wanders into a bathhouse run for spirits and has to work there to free her parents. Hand-drawn, strange and unhurried, with a world that never explains itself.', 'https://image.tmdb.org/t/p/w500/39wmItIWsg5sZMyRUHLkWBcuVCM.jpg', 0, '2026-10-23 07:51:06'),
(4, 'The Godfather', '1972-03-24', 8.7, 'Francis Ford Coppola', 'Marlon Brando, Al Pacino, James Caan, Robert Duvall, Diane Keaton', 'The aging head of a crime family hands his empire to the son who never wanted it. A study of power, loyalty and the slow cost of both, shot in deep amber shadow.', 'https://imgs.search.brave.com/4WMFzrlK0wTO1mtb_FNFPVKzNG0FoqIQ_OEmowqZNsU/rs:fit:860:0:0:0/g:ce/aHR0cHM6Ly9pLmVi/YXlpbWcuY29tL2lt/YWdlcy9nL2Ntb0FB/T1N3VUlobHdRUGsv/cy1sOTYwLndlYnA', 0, '2026-10-23 07:51:06'),
(5, 'Blade Runner 2049', '2017-10-06', 7.5, 'Denis Villeneuve', 'Ryan Gosling, Harrison Ford, Ana de Armas, Jared Leto', 'A replicant officer uncovers a buried secret that could upend the fragile order between humans and their creations, and goes looking for a man who vanished thirty years earlier.', 'https://image.tmdb.org/t/p/w500/gajva2L0rPYkEWjzgFlBXCAVBE5.jpg', 0, '2026-10-23 07:51:06'),
(6, 'Moonlight', '2016-10-21', 7.4, 'Barry Jenkins', 'Trevante Rhodes, Mahershala Ali, Naomie Harris, Andre Holland', 'Three chapters from the life of a boy growing up in Miami, told across childhood, adolescence and adulthood. A tender film about identity and the things left unsaid.', 'https://image.tmdb.org/t/p/w500/4911T5FbJ9eD2Faz5Z8cT3SUhU3.jpg', 0, '2026-10-23 07:51:06'),
(7, 'In the Mood for Love', '2000-09-29', 8.1, 'Wong Kar-wai', 'Tony Leung Chiu-wai, Maggie Cheung, Rebecca Pan', 'Two neighbors in 1960s Hong Kong realize their spouses are having an affair, and slowly grow close themselves. Everything is in the glances, the corridors and the restraint.', 'https://imgs.search.brave.com/GGsWAoM2y4q4QJNeXIfr7KB61f4DfDVyXnLsXAqFQHc/rs:fit:860:0:0:0/g:ce/aHR0cHM6Ly9zMy5h/bWF6b25hd3MuY29t/L25pZ2h0amFycHJv/ZC9jb250ZW50L3Vw/bG9hZHMvc2l0ZXMv/MzQ5LzIwMjUvMDEv/MDMxNTA3MjQvaVl5/cFBUNGJocVhmcTFi/NkVubXh2UnQ2YjJZ/LmpwZw', 0, '2026-10-23 07:51:06'),
(8, 'Pan\'s Labyrinth', '2006-10-11', 8.2, 'Guillermo del Toro', 'Ivana Baquero, Sergi Lopez, Maribel Verdu, Doug Jones', 'In Spain in 1944, a girl moves to a military outpost with her mother and stepfather, and finds a labyrinth with a faun waiting at its center. Fairy tale and war story, neither softened.', 'https://imgs.search.brave.com/zAGb3GAed8f57pW8ir5ILggfvhQCSuN6a8hXXtWamhM/rs:fit:860:0:0:0/g:ce/aHR0cHM6Ly93YWxs/cGFwZXJjYXQuY29t/L3cvZnVsbC85LzUv/Yi8xMDAzNjAyLTIw/MDB4MzAwMC1zYW1z/dW5nLWhkLXBhbnMt/bGFieXJpbnRoLTIw/MDYtd2FsbHBhcGVy/LmpwZw', 0, '2026-10-23 07:51:06'),
(9, 'Lost in Translation', '2003-09-12', 7.7, 'Sofia Coppola', 'Bill Murray, Scarlett Johansson, Giovanni Ribisi', 'An aging actor and a newly married young woman meet by chance in a Tokyo hotel, both sleepless and a long way from home. A small, funny, melancholy friendship.', 'https://imgs.search.brave.com/bk033_fS9a3rugrg9j8d8rcU8GYnOIoaYusmeLiNN_o/rs:fit:860:0:0:0/g:ce/aHR0cHM6Ly93d3cu/bGFiMTExLm5sL3dw/LWNvbnRlbnQvdXBs/b2Fkcy8yMDE4LzA5/L2xvc3RfaW5fdHJh/bnNsYXRpb25feHhs/Zy1zY2FsZWQuanBn', 0, '2026-10-23 07:51:06'),
(10, 'Portrait of a Lady on Fire', '2019-09-18', 8.1, 'Celine Sciamma', 'Noemie Merlant, Adele Haenel, Luana Bajrami, Valeria Golino', 'On an island in 18th-century Brittany, a painter is hired to secretly portray a reluctant bride-to-be. Looking becomes the whole language of the film.', 'https://imgs.search.brave.com/NYYCRT-DRgwskelEk6c3RiB4kYal9UhoAXYgmfGY0bU/rs:fit:860:0:0:0/g:ce/aHR0cHM6Ly9tLm1l/ZGlhLWFtYXpvbi5j/b20vaW1hZ2VzL00v/TVY1QllUaGhZbUpr/WWpZdE16ZzFZUzAw/T0dRMUxXRXhNRFl0/TTJNMFlqZzFaVGt3/WkdObFhrRXlYa0Zx/Y0djQC5qcGc', 1, '2026-10-23 07:51:06');

-- --------------------------------------------------------

--
-- Table structure for table `movie_genres`
--

CREATE TABLE `movie_genres` (
  `movie_id` int(10) UNSIGNED NOT NULL,
  `genre_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `movie_genres`
--

INSERT INTO `movie_genres` (`movie_id`, `genre_id`) VALUES
(1, 1),
(1, 2),
(1, 8),
(2, 1),
(2, 3),
(3, 4),
(3, 5),
(4, 1),
(4, 6),
(5, 2),
(5, 3),
(6, 1),
(7, 1),
(7, 7),
(8, 1),
(8, 5),
(9, 1),
(9, 7),
(10, 1),
(10, 7);

-- --------------------------------------------------------

--
-- Table structure for table `watchlist`
--

CREATE TABLE `watchlist` (
  `id` int(10) UNSIGNED NOT NULL,
  `visitor_id` varchar(64) NOT NULL,
  `movie_id` int(10) UNSIGNED NOT NULL,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `genres`
--
ALTER TABLE `genres`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `movies`
--
ALTER TABLE `movies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_title` (`title`),
  ADD KEY `idx_release` (`release_date`);

--
-- Indexes for table `movie_genres`
--
ALTER TABLE `movie_genres`
  ADD PRIMARY KEY (`movie_id`,`genre_id`),
  ADD KEY `genre_id` (`genre_id`);

--
-- Indexes for table `watchlist`
--
ALTER TABLE `watchlist`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_visitor_movie` (`visitor_id`,`movie_id`),
  ADD KEY `movie_id` (`movie_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `genres`
--
ALTER TABLE `genres`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `movies`
--
ALTER TABLE `movies`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `watchlist`
--
ALTER TABLE `watchlist`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `movie_genres`
--
ALTER TABLE `movie_genres`
  ADD CONSTRAINT `movie_genres_ibfk_1` FOREIGN KEY (`movie_id`) REFERENCES `movies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `movie_genres_ibfk_2` FOREIGN KEY (`genre_id`) REFERENCES `genres` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `watchlist`
--
ALTER TABLE `watchlist`
  ADD CONSTRAINT `watchlist_ibfk_1` FOREIGN KEY (`movie_id`) REFERENCES `movies` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
