-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 19, 2024 at 10:20 PM
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
-- Database: `rethink`
--

-- --------------------------------------------------------

--
-- Table structure for table `activities`
--

CREATE TABLE `activities` (
  `ActivityID` int(11) NOT NULL,
  `ActivityName` varchar(30) NOT NULL,
  `ActivityIcon` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activities`
--

INSERT INTO `activities` (`ActivityID`, `ActivityName`, `ActivityIcon`) VALUES
(1, '勉強', '<i class=\"fa-solid fa-graduation-cap\"></i>'),
(2, '仕事', '<i class=\"fa-solid fa-briefcase\"></i>'),
(3, '家事', '<i class=\"fa-solid fa-broom\"></i>'),
(4, 'スポーツ', '<i class=\"fa-solid fa-football\"></i>'),
(5, '絵描き', '<i class=\"fa-solid fa-brush\"></i>'),
(6, '写真撮影', '<i class=\"fa-solid fa-camera-retro\"></i>'),
(7, '料理', '<i class=\"fa-solid fa-kitchen-set\"></i>'),
(8, 'ゲーム', '<i class=\"fa-solid fa-gamepad\"></i>'),
(9, 'リラックス', '<i class=\"fa-solid fa-couch\"></i>'),
(10, '音楽', '<i class=\"fa-solid fa-music\"></i>'),
(11, '山登り', '<i class=\"fa-solid fa-person-hiking\"></i>'),
(12, '読書', '<i class=\"fa-solid fa-book\"></i>'),
(13, 'テレビ・映画', '<i class=\"fa-solid fa-tv\"></i>'),
(14, '買物', '<i class=\"fa-solid fa-basket-shopping\"></i>'),
(15, 'パーティー', '<i class=\"fa-solid fa-champagne-glasses\"></i>'),
(16, 'デート', '<i class=\"fa-solid fa-heart\"></i>'),
(17, '泳ぐ', '<i class=\"fa-solid fa-person-swimming\"></i>'),
(18, '誰かと遊ぶ', '<i class=\"fa-solid fa-comments\"></i>');

-- --------------------------------------------------------

--
-- Table structure for table `blockedwords`
--

CREATE TABLE `blockedwords` (
  `blockedWordID` int(11) NOT NULL,
  `blockedWord` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `blockedwords`
--

INSERT INTO `blockedwords` (`blockedWordID`, `blockedWord`) VALUES
(70, 'Bumblebee'),
(71, 'Pickle');

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `CommentID` int(11) NOT NULL,
  `Comment` varchar(255) DEFAULT NULL,
  `UserID` int(11) NOT NULL,
  `PostID` int(11) NOT NULL,
  `Date` datetime DEFAULT current_timestamp(),
  `Hidden` varchar(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`CommentID`, `Comment`, `UserID`, `PostID`, `Date`, `Hidden`) VALUES
(142, 'Dolor nisl fames augue maximus hendrerit tempus, a hendrerit. Maximus et commodo feugiat lectus mattis tellus risus. Blandit sit arcu lacinia maecenas lectus. Consectetur nulla convallis aenean posuere; magnis sagittis senectus facilisis.', 133, 464, '2024-12-19 11:31:03', NULL),
(143, 'n condimentum suscipit metus dignissim ridiculus nam viverra. Porta fermentum leo, urna vehicula quisque elementum amet.', 133, 461, '2024-12-19 11:31:27', NULL),
(144, 'Vel mauris mus rhoncus suspendisse aenean arcu urna consectetur commodo.', 133, 462, '2024-12-19 11:48:22', NULL),
(145, 'Eros nullam erat luctus sodales dui bibendum. Sed in praesent maximus ipsum sem ultricies duis. Molestie molestie torquent condimentum maximus bibendum rhoncus. ', 133, 463, '2024-12-19 11:49:23', NULL),
(146, 'Urna netus turpis dictumst varius nibh sodales. Gravida per ligula enim libero nam.', 134, 466, '2024-12-19 12:04:57', NULL),
(147, ' Urna netus turpis dictumst varius nibh sodales. Gravida per ligula enim libero nam.', 134, 465, '2024-12-19 12:05:37', NULL),
(148, 'Convallis lectus dictumst conubia blandit purus montes porttitor nisi massa. Rutrum finibus nec orci pharetra semper. Gravida viverra non arcu litora est; faucibus euismod eleifend mauris?', 134, 464, '2024-12-19 12:06:03', NULL),
(149, ' Fringilla fringilla felis pulvinar ornare lectus auctor justo? Nostra odio ullamcorper nibh potenti vivamus gravida inceptos. Iaculis hendrerit lacus mollis porttitor ornare nisl mus velit.', 137, 467, '2024-12-19 12:06:44', NULL),
(150, 'Montes augue sem sapien ante erat faucibus pharetra. Nam ultricies hendrerit sapien posuere per suscipit netus.', 137, 461, '2024-12-19 12:06:57', NULL),
(151, 'Porttitor platea dis facilisi consequat viverra. ', 137, 461, '2024-12-19 12:07:55', NULL),
(152, 'Porttitor platea dis facilisi consequat viverra. ', 137, 467, '2024-12-19 12:08:01', NULL),
(153, 'Nam adipiscing fames id porta ornare, gravida vehicula tempus curae. Curae feugiat adipiscing erat etiam vitae consectetur dapibus. Porttitor platea dis facilisi consequat viverra. Non eu pretium hac suscipit tempus hendrerit fringilla. Habitant lectus se', 135, 467, '2024-12-19 12:09:34', NULL),
(154, ' Rutrum molestie viverra suscipit dis odio feugiat morbi id. Vel mauris mus rhoncus suspendisse aenean arcu urna consectetur commodo.', 135, 463, '2024-12-19 12:09:55', NULL),
(155, 'Quam luctus elementum laoreet venenatis, phasellus mauris blandit?', 135, 468, '2024-12-19 12:10:06', NULL),
(156, 'Lorem ipsum odor amet, consectetuer adipiscing elit. Nisi convallis torquent ut consequat tristique mus? ', 136, 467, '2024-12-19 12:12:35', NULL),
(157, 'Leo felis porttitor donec curabitur metus augue, sapien massa. Egestas ante placerat ipsum neque ad. Ad rutrum lacus imperdiet nam et suspendisse feugiat etiam cubilia.', 136, 462, '2024-12-19 12:13:08', NULL),
(158, 'Integer viverra conubia ligula arcu lacus mattis? Eu nisi urna diam; sociosqu vestibulum gravida. ', 131, 470, '2024-12-19 12:15:20', NULL),
(159, 'Integer viverra conubia ligula arcu lacus mattis? Eu nisi urna diam; sociosqu vestibulum gravida. Lectus eros non quisque praesent nam cras tristique imperdiet ac.', 131, 459, '2024-12-19 12:15:50', NULL),
(160, ' Sed vulputate urna quam malesuada sem primis ligula. Urna netus turpis dictumst varius nibh sodales. Gravida per ligula enim libero nam.', 131, 466, '2024-12-19 12:16:02', NULL),
(161, ' Turpis platea nec scelerisque class euismod. Nunc et montes facilisis neque, etiam aliquet. Dolor nostra fermentum blandit conubia dui etiam; purus tristique.', 132, 471, '2024-12-19 12:16:50', NULL),
(162, 'Venenatis gravida pharetra etiam tempus molestie. Nec sodales fusce praesent in mollis; cursus tempus odio. ', 132, 463, '2024-12-19 12:17:55', NULL),
(163, ' Eu cursus aptent vulputate sociosqu consectetur fringilla donec nunc. Platea torquent morbi luctus potenti vehicula sapien sodales.', 132, 462, '2024-12-19 12:18:03', NULL),
(164, 'Venenatis gravida pharetra etiam tempus molestie. Nec sodales fusce praesent in mollis; cursus tempus odio.', 138, 472, '2024-12-19 12:18:43', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `company`
--

CREATE TABLE `company` (
  `CompanyID` int(11) NOT NULL,
  `CompanyName` varchar(30) NOT NULL,
  `CompanyIcon` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `company`
--

INSERT INTO `company` (`CompanyID`, `CompanyName`, `CompanyIcon`) VALUES
(1, '一人', '<i class=\"fa-solid fa-user\"></i>'),
(2, '家族', '<i class=\"fa-solid fa-people-roof\"></i>'),
(3, 'パートナー', '<i class=\"fa-solid fa-face-kiss-wink-heart\"></i>'),
(4, '友人', '<i class=\"fa-solid fa-people-group\"></i>'),
(5, '同僚', '<i class=\"fa-solid fa-user-tie\"></i>'),
(6, 'クラスメイト', '<i class=\"fa-solid fa-user-graduate\"></i>'),
(7, '知り合い', '<i class=\"fa-solid fa-user-group\"></i>'),
(8, '初対面', '<i class=\"fa-solid fa-person\"></i>');

-- --------------------------------------------------------

--
-- Table structure for table `dailytracking`
--

CREATE TABLE `dailytracking` (
  `TrackingID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `Date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dailytracking`
--

INSERT INTO `dailytracking` (`TrackingID`, `UserID`, `Date`) VALUES
(1376, 130, '2024-12-04'),
(1377, 130, '2024-12-19'),
(1378, 130, '2024-12-06'),
(1379, 130, '2024-12-17'),
(1380, 130, '2024-12-08'),
(1381, 130, '2024-12-14'),
(1382, 130, '2024-12-09'),
(1383, 130, '2024-12-10'),
(1384, 130, '2024-12-11'),
(1385, 130, '2024-12-18'),
(1386, 130, '2024-12-16'),
(1387, 130, '2024-12-13'),
(1388, 130, '2024-12-07'),
(1389, 130, '2024-12-03'),
(1390, 130, '2024-12-12'),
(1391, 130, '2024-12-01'),
(1394, 133, '2024-12-17'),
(1395, 133, '2024-12-15'),
(1396, 133, '2024-12-16'),
(1397, 133, '2024-12-14'),
(1398, 133, '2024-12-13'),
(1399, 133, '2024-12-12'),
(1400, 133, '2024-12-10'),
(1401, 133, '2024-12-09'),
(1402, 133, '2024-12-08'),
(1403, 133, '2024-12-07'),
(1405, 133, '2024-12-06'),
(1406, 133, '2024-12-04'),
(1407, 133, '2024-12-03'),
(1408, 133, '2024-12-02'),
(1409, 133, '2024-12-01'),
(1410, 129, '2024-12-19'),
(1413, 133, '2024-12-19');

-- --------------------------------------------------------

--
-- Table structure for table `feelingloadings`
--

CREATE TABLE `feelingloadings` (
  `FeelingLoadingID` int(11) NOT NULL,
  `FeelingLoading` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feelingloadings`
--

INSERT INTO `feelingloadings` (`FeelingLoadingID`, `FeelingLoading`) VALUES
(1, 'Positive'),
(2, 'Neutral'),
(3, 'Negative');

-- --------------------------------------------------------

--
-- Table structure for table `feelings`
--

CREATE TABLE `feelings` (
  `FeelingID` int(11) NOT NULL,
  `FeelingName` varchar(30) NOT NULL,
  `FeelingLoadingID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feelings`
--

INSERT INTO `feelings` (`FeelingID`, `FeelingName`, `FeelingLoadingID`) VALUES
(1, 'スッキリ', 2),
(2, 'ドキドキ', 2),
(3, '安心', 2),
(4, '穏やか', 2),
(5, '普通', 2),
(6, '退屈', 2),
(7, 'モヤモヤ', 2),
(8, '緊張', 2),
(9, '満足', 1),
(10, '感謝', 1),
(11, '嬉しい', 1),
(12, 'ワクワク', 1),
(13, '好き', 1),
(14, '感心', 1),
(15, '面白い', 1),
(16, '楽しい', 1),
(17, '不安', 3),
(18, '悲しい', 3),
(19, '疲れた', 3),
(20, '後悔', 3),
(21, '恐れる', 3),
(22, 'イライラ', 3),
(23, '怒り', 3),
(24, '嫌い', 3);

-- --------------------------------------------------------

--
-- Table structure for table `foods`
--

CREATE TABLE `foods` (
  `FoodID` int(11) NOT NULL,
  `FoodName` varchar(30) NOT NULL,
  `FoodIcon` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `foods`
--

INSERT INTO `foods` (`FoodID`, `FoodName`, `FoodIcon`) VALUES
(1, '手作り', '<i class=\"fa-solid fa-kitchen-set\"></i>'),
(2, '弁当', '<i class=\"fa-solid fa-bowl-food\"></i>'),
(3, 'レストラン', '<i class=\"fa-solid fa-utensils\"></i>'),
(4, 'ファーストフード', '<i class=\"fa-solid fa-burger\"></i>'),
(5, 'ヘルシー', '<i class=\"fa-solid fa-carrot\"></i>'),
(6, '菓子なし', '<i class=\"fa-solid fa-cookie-bite\"></i>'),
(7, '肉なし', '<i class=\"fa-solid fa-drumstick-bite\"></i>');

-- --------------------------------------------------------

--
-- Table structure for table `goalcategories`
--

CREATE TABLE `goalcategories` (
  `GoalCategoriesID` int(11) NOT NULL,
  `GoalCategoryName` varchar(30) NOT NULL,
  `GoalCategoryNameJp` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `goalcategories`
--

INSERT INTO `goalcategories` (`GoalCategoriesID`, `GoalCategoryName`, `GoalCategoryNameJp`) VALUES
(1, 'Get Fit', '体を鍛える'),
(2, 'Break Bad Habits', '悪い習慣を改める'),
(3, 'Live Healthy ', 'より健康的な生活を'),
(4, 'Reduce Stress', 'ストレスを低減');

-- --------------------------------------------------------

--
-- Table structure for table `goals`
--

CREATE TABLE `goals` (
  `GoalID` int(11) NOT NULL,
  `GoalName` varchar(30) NOT NULL,
  `GoalCategoriesID` int(11) NOT NULL,
  `GoalIcon` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `goals`
--

INSERT INTO `goals` (`GoalID`, `GoalName`, `GoalCategoriesID`, `GoalIcon`) VALUES
(1, '運動', 1, '<i class=\"fa-solid fa-dumbbell\"></i>'),
(2, 'ランニング', 1, '<i class=\"fa-solid fa-person-running\"></i>'),
(3, 'お酒を飲まない', 2, '<i class=\"fa-solid fa-martini-glass-citrus\"></i>'),
(4, '禁煙する', 2, '<i class=\"fa-solid fa-smoking\"></i>'),
(5, '8時間寝る', 3, '<i class=\"fa-solid fa-bed\"></i>'),
(6, 'お菓子を食べない', 3, '<i class=\"fa-solid fa-cookie-bite\"></i>'),
(7, '瞑想する', 4, '<i class=\"fa-solid fa-spa\"></i>'),
(8, '屋外で過ごす', 4, '<i class=\"fa-solid fa-mountain-city\"></i>');

-- --------------------------------------------------------

--
-- Table structure for table `locations`
--

CREATE TABLE `locations` (
  `LocationID` int(11) NOT NULL,
  `LocationName` varchar(30) NOT NULL,
  `LocationIcon` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `locations`
--

INSERT INTO `locations` (`LocationID`, `LocationName`, `LocationIcon`) VALUES
(1, 'お家', '<i class=\"fa-solid fa-house-chimney\"></i>'),
(2, '職場', '<i class=\"fa-solid fa-briefcase\"></i>'),
(3, '学校', '<i class=\"fa-solid fa-graduation-cap\"></i>'),
(4, '乗り物', '<i class=\"fa-solid fa-van-shuttle\"></i>'),
(5, 'モール', '<i class=\"fa-solid fa-bag-shopping\"></i>'),
(6, '公園', '<i class=\"fa-solid fa-tree-city\"></i>'),
(7, 'レストラン・バー', '<i class=\"fa-solid fa-utensils\"></i>'),
(8, 'ジム', '<i class=\"fa-solid fa-dumbbell\"></i>'),
(9, 'アウトドア', '<i class=\"fa-solid fa-tree\"></i>'),
(10, 'カフェ', '<i class=\"fa-solid fa-mug-saucer\"></i>'),
(11, 'ビーチ', '<i class=\"fa-solid fa-umbrella-beach\"></i>');

-- --------------------------------------------------------

--
-- Table structure for table `memos`
--

CREATE TABLE `memos` (
  `MemoID` int(11) NOT NULL,
  `TrackingID` int(11) NOT NULL,
  `Memo` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `memos`
--

INSERT INTO `memos` (`MemoID`, `TrackingID`, `Memo`) VALUES
(1, 1377, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed bibendum odio ut ipsum vehicula, vitae pellentesque nisl venenatis. Suspendisse vitae orci purus. Aenean massa enim, efficitur non mauris ut, aliquet pretium nulla.'),
(2, 1378, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. '),
(3, 1379, 'Lorem ipsum odor amet, consectetuer adipiscing elit.'),
(4, 1380, 'Lorem ipsum odor amet, consectetuer adipiscing elit. Pulvinar nec est turpis iaculis id lorem eros.'),
(5, 1381, 'Dolor nisl fames augue maximus hendrerit tempus, a hendrerit.'),
(6, 1382, 'Imperdiet gravida libero fermentum ullamcorper consectetur dolor conubia. Suscipit nisi in taciti ante ornare dolor volutpat. Tempor nullam duis faucibus tellus mus.'),
(7, 1383, 'Anisl in facilisi himenaeos aptent facilisis sem. Nibh natoque viverra natoque egestas nam vehicula vel curae vehicula. '),
(8, 1384, 'Mus dictumst est rutrum sodales porta orci cursus? Enim semper habitasse aliquet nec ipsum gravida tempus auctor.'),
(9, 1385, 'Mus dictumst est rutrum sodales porta orci cursus? Enim semper habitasse aliquet nec ipsum gravida tempus auctor.'),
(10, 1386, 'Mus dictumst est rutrum sodales porta orci cursus? Enim semper habitasse aliquet nec ipsum gravida tempus auctor.'),
(11, 1387, 'Ultrices leo sollicitudin dignissim nullam rutrum lacinia convallis risus. Augue turpis mauris magna feugiat tempor parturient maecenas tellus enim!'),
(12, 1388, 'Facilisis interdum commodo massa purus aptent non. Consequat lobortis lorem tempor eros bibendum molestie tristique.'),
(13, 1389, 'Tempor nulla ante ornare pellentesque orci porttitor. Donec efficitur viverra litora eget cubilia.'),
(14, 1390, 'Tempor nulla ante ornare pellentesque orci porttitor. Donec efficitur viverra litora eget cubilia.'),
(15, 1391, 'Est blandit sodales class potenti lobortis proin elit platea condimentum.'),
(18, 1394, 'Lorem ipsum odor amet, consectetuer adipiscing elit. Tristique nec semper parturient dapibus nam vehicula aliquet nisl. '),
(19, 1395, ' Aptent semper potenti viverra quis urna vivamus semper diam. Auctor posuere dolor erat congue auctor. Suscipit potenti class neque, ex taciti tellus. '),
(20, 1396, 'Quisque ornare habitant nunc purus ultrices elit litora parturient neque.'),
(21, 1397, 'Eros nullam erat luctus sodales dui bibendum. Sed in praesent maximus ipsum sem ultricies duis. Molestie molestie torquent condimentum maximus bibendum rhoncus. '),
(22, 1398, 'Vel mauris mus rhoncus suspendisse aenean arcu urna consectetur commodo.'),
(23, 1399, 'Eros nullam erat luctus sodales dui bibendum. Sed in praesent maximus ipsum sem ultricies duis. Molestie molestie torquent condimentum maximus bibendum rhoncus. '),
(24, 1400, 'Nam adipiscing fames id porta ornare, gravida vehicula tempus curae. Curae feugiat adipiscing erat etiam vitae consectetur dapibus. Porttitor platea dis facilisi consequat viverra.'),
(25, 1401, 'Non eu pretium hac suscipit tempus hendrerit fringilla. Habitant lectus sem auctor dolor pretium vitae ad malesuada. '),
(26, 1402, 'Dictumst posuere praesent finibus tempor; class taciti nam. Lacinia proin vitae, nisi ante hendrerit mollis! '),
(27, 1403, 'Habitant lectus sem auctor dolor pretium vitae ad malesuada.'),
(28, 1405, ' Nostra odio ullamcorper nibh potenti vivamus gravida inceptos. Iaculis hendrerit lacus mollis porttitor ornare nisl mus velit.'),
(29, 1406, 'Hendrerit massa nisi fermentum aptent natoque turpis neque. Metus pretium parturient vulputate platea ridiculus; in leo. '),
(30, 1407, 'Urna netus turpis dictumst varius nibh sodales. Gravida per ligula enim libero nam.'),
(31, 1408, '. Non eu pretium hac suscipit tempus hendrerit fringilla. Habitant lectus sem auctor dolor pretium vitae ad malesuada. Dictumst posuere praesent finibus tempor; class taciti nam.'),
(32, 1409, 'endrerit massa nisi fermentum aptent natoque turpis neque. Metus pretium parturient vulputate platea ridiculus; in leo. Elit dapibus tellus non neque parturient dui natoque. '),
(33, 1410, 'Quam luctus elementum laoreet venenatis, phasellus mauris blandit? Rutrum molestie viverra suscipit dis odio feugiat morbi id. Vel mauris mus rhoncus suspendisse aenean arcu urna consectetur commodo.'),
(35, 1413, 'En condimentum suscipit metus dignissim ridiculus nam viverra. Porta fermentum leo, urna vehicula quisque elementum amet.');

-- --------------------------------------------------------

--
-- Table structure for table `moods`
--

CREATE TABLE `moods` (
  `MoodID` int(11) NOT NULL,
  `MoodName` varchar(30) NOT NULL,
  `JapaneseMoodName` varchar(30) DEFAULT NULL,
  `moodEmoji` varchar(255) DEFAULT NULL,
  `moodEmojiColor` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `moods`
--

INSERT INTO `moods` (`MoodID`, `MoodName`, `JapaneseMoodName`, `moodEmoji`, `moodEmojiColor`) VALUES
(1, 'Great', '最高', '<i class=\"fa-regular fa-face-laugh-beam\"></i>', '<i class=\"fa-solid fa-face-laugh-beam\"></i>'),
(2, 'Good', '良い', '<i class=\"fa-regular fa-face-smile-beam\"></i>', '<i class=\"fa-solid fa-face-smile-beam\"></i>'),
(3, 'Okay', '普通', '<i class=\"fa-regular fa-face-meh\"></i>', '<i class=\"fa-solid fa-face-meh\"></i>'),
(4, 'Bad', '悪い', '<i class=\"fa-regular fa-face-frown\"></i>', '<i class=\"fa-solid fa-face-frown\"></i>'),
(5, 'Awful', '最悪', '<i class=\"fa-regular fa-face-tired\"></i>', '<i class=\"fa-solid fa-face-tired\"></i>');

-- --------------------------------------------------------

--
-- Table structure for table `postlikes`
--

CREATE TABLE `postlikes` (
  `LikeID` int(11) NOT NULL,
  `PostID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `postlikes`
--

INSERT INTO `postlikes` (`LikeID`, `PostID`, `UserID`) VALUES
(184, 462, 130),
(185, 464, 130),
(186, 464, 133),
(187, 461, 133),
(188, 459, 133),
(189, 466, 133),
(190, 466, 134),
(191, 464, 134),
(192, 463, 134),
(193, 462, 134),
(194, 467, 134),
(195, 465, 134),
(196, 467, 137),
(197, 464, 137),
(198, 465, 137),
(199, 462, 137),
(200, 467, 135),
(201, 466, 135),
(202, 464, 135),
(203, 463, 135),
(204, 468, 135),
(205, 467, 136),
(206, 464, 136),
(207, 461, 136),
(208, 465, 136),
(209, 470, 136),
(210, 462, 136),
(211, 470, 131),
(212, 464, 131),
(213, 471, 131),
(214, 469, 131),
(215, 459, 131),
(216, 471, 132),
(217, 467, 132),
(218, 464, 132),
(219, 472, 132),
(220, 462, 132),
(221, 468, 132),
(222, 472, 138),
(223, 471, 138),
(224, 464, 138);

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `PostID` int(11) NOT NULL,
  `MoodID` int(11) NOT NULL,
  `PostedText` varchar(500) DEFAULT NULL,
  `UserID` int(11) NOT NULL,
  `Date` datetime DEFAULT current_timestamp(),
  `Hidden` varchar(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`PostID`, `MoodID`, `PostedText`, `UserID`, `Date`, `Hidden`) VALUES
(459, 1, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed bibendum odio ut ipsum vehicula, vitae pellentesque nisl venenatis. ', 130, '2024-12-16 09:23:19', '0'),
(461, 3, 'Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam.', 130, '2024-12-19 11:13:47', '0'),
(462, 1, 'Lorem ipsum odor amet, consectetuer adipiscing elit. Platea malesuada urna hac pellentesque mollis penatibus. At curae quisque penatibus pulvinar class vestibulum commodo augue.', 130, '2024-12-12 15:28:22', '0'),
(463, 2, 'Dolor nisl fames augue maximus hendrerit tempus, a hendrerit.', 130, '2024-12-16 22:49:57', '0'),
(464, 4, 'Mus dictumst est rutrum sodales porta orci cursus? Enim semper habitasse aliquet nec ipsum gravida tempus auctor.', 130, '2024-12-19 11:23:45', '0'),
(465, 2, 'Est blandit sodales class potenti lobortis proin elit platea condimentum.', 133, '2024-12-15 18:36:44', '0'),
(466, 1, 'Quisque ornare habitant nunc purus ultrices elit litora parturient neque.', 133, '2024-12-19 11:36:26', '0'),
(467, 4, 'Hendrerit massa nisi fermentum aptent natoque turpis neque. Metus pretium parturient vulputate platea ridiculus; in leo. Elit dapibus tellus non neque parturient dui natoque. ', 134, '2024-12-19 12:05:15', '0'),
(468, 2, 'Montes augue sem sapien ante erat faucibus pharetra. Nam ultricies hendrerit sapien posuere per suscipit netus.', 137, '2024-12-11 15:07:11', '0'),
(469, 3, 'Convallis lectus dictumst conubia blandit purus montes porttitor nisi massa. Rutrum finibus nec orci pharetra semper. Gravida viverra non arcu litora est; faucibus euismod eleifend mauris? Fringilla fringilla felis pulvinar ornare lectus auctor justo? Nostra odio ullamcorper nibh potenti vivamus gravida inceptos. Iaculis hendrerit lacus mollis porttitor ornare nisl mus velit. Montes augue sem sapien ante erat faucibus pharetra. Nam ultricies hendrerit sapien posuere per suscipit netus.', 135, '2024-12-18 20:38:55', '0'),
(470, 3, 'Nam mollis penatibus maximus leo mattis. Sit sagittis etiam fermentum ipsum; orci nibh tempus felis.', 136, '2024-12-19 12:12:53', '0'),
(471, 1, 'Vitae porta aliquam tristique rutrum erat cras. Consequat fermentum finibus nisi montes vitae nunc turpis. Donec ridiculus donec integer platea ac auctor ullamcorper ipsum pulvinar. Purus taciti ullamcorper consectetur enim ut cras. Donec viverra sapien habitasse mauris a. Non placerat velit pretium adipiscing sem ac consectetur dignissim.', 131, '2024-12-19 12:15:35', '0'),
(472, 2, 'In velit ullamcorper, at mus natoque donec conubia? Malesuada accumsan varius ad orci volutpat sapien egestas cursus? ', 132, '2024-12-19 12:16:40', '0'),
(473, 5, 'Bumblebee sit voluptates voluptate ab impedit assumenda qui blanditiis velit aut dolorum nihil eos illo tempora id laborum voluptatem qui iste repellendus. ', 129, '2024-12-19 16:00:35', '1');

-- --------------------------------------------------------

--
-- Table structure for table `requestcheck`
--

CREATE TABLE `requestcheck` (
  `RequestCheckID` int(11) NOT NULL,
  `PostID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `requestcheck`
--

INSERT INTO `requestcheck` (`RequestCheckID`, `PostID`) VALUES
(51, 473);

-- --------------------------------------------------------

--
-- Table structure for table `trackactivities`
--

CREATE TABLE `trackactivities` (
  `TrackActivityID` int(11) NOT NULL,
  `TrackingID` int(11) NOT NULL,
  `ActivityID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `trackactivities`
--

INSERT INTO `trackactivities` (`TrackActivityID`, `TrackingID`, `ActivityID`) VALUES
(1029, 1376, 3),
(1030, 1376, 9),
(1031, 1377, 6),
(1032, 1378, 1),
(1033, 1379, 8),
(1034, 1380, 1),
(1035, 1380, 14),
(1036, 1381, 15),
(1037, 1381, 16),
(1038, 1382, 13),
(1039, 1383, 8),
(1040, 1384, 3),
(1041, 1384, 13),
(1042, 1385, 13),
(1043, 1386, 1),
(1044, 1386, 8),
(1045, 1387, 16),
(1046, 1388, 1),
(1047, 1388, 16),
(1048, 1389, 7),
(1049, 1389, 8),
(1050, 1390, 8),
(1051, 1390, 9),
(1052, 1391, 7),
(1053, 1391, 16),
(1059, 1394, 2),
(1060, 1394, 4),
(1061, 1395, 10),
(1062, 1395, 15),
(1063, 1396, 2),
(1064, 1396, 4),
(1065, 1396, 13),
(1066, 1397, 11),
(1067, 1397, 16),
(1068, 1398, 2),
(1069, 1398, 15),
(1070, 1399, 2),
(1071, 1399, 3),
(1072, 1399, 14),
(1073, 1400, 2),
(1074, 1401, 9),
(1075, 1402, 4),
(1076, 1402, 11),
(1077, 1403, 4),
(1078, 1403, 15),
(1082, 1405, 2),
(1083, 1405, 16),
(1084, 1405, 18),
(1085, 1406, 2),
(1086, 1406, 3),
(1087, 1407, 2),
(1088, 1407, 4),
(1089, 1408, 2),
(1090, 1408, 4),
(1091, 1408, 9),
(1092, 1409, 4),
(1093, 1409, 11),
(1094, 1409, 16),
(1095, 1410, 13),
(1099, 1413, 4),
(1100, 1413, 9),
(1101, 1413, 13);

-- --------------------------------------------------------

--
-- Table structure for table `trackcompany`
--

CREATE TABLE `trackcompany` (
  `TrackCompanyID` int(11) NOT NULL,
  `TrackingID` int(11) NOT NULL,
  `CompanyID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `trackcompany`
--

INSERT INTO `trackcompany` (`TrackCompanyID`, `TrackingID`, `CompanyID`) VALUES
(603, 1376, 1),
(604, 1377, 4),
(605, 1378, 1),
(606, 1379, 1),
(607, 1379, 3),
(608, 1380, 1),
(609, 1381, 3),
(610, 1382, 1),
(611, 1383, 1),
(612, 1384, 1),
(613, 1384, 3),
(614, 1385, 3),
(615, 1385, 4),
(616, 1386, 1),
(617, 1387, 3),
(618, 1388, 1),
(619, 1388, 3),
(620, 1389, 1),
(621, 1389, 4),
(622, 1390, 2),
(623, 1390, 3),
(624, 1391, 3),
(628, 1394, 5),
(629, 1394, 7),
(630, 1395, 4),
(631, 1396, 4),
(632, 1396, 5),
(633, 1397, 8),
(634, 1398, 4),
(635, 1398, 5),
(636, 1399, 1),
(637, 1400, 1),
(638, 1401, 1),
(639, 1402, 4),
(640, 1403, 4),
(642, 1405, 8),
(643, 1406, 1),
(644, 1406, 5),
(645, 1407, 5),
(646, 1408, 1),
(647, 1408, 5),
(648, 1409, 7),
(649, 1410, 1),
(652, 1413, 1),
(653, 1413, 4);

-- --------------------------------------------------------

--
-- Table structure for table `trackfeelings`
--

CREATE TABLE `trackfeelings` (
  `TrackFeelingID` int(11) NOT NULL,
  `TrackingID` int(11) NOT NULL,
  `FeelingID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `trackfeelings`
--

INSERT INTO `trackfeelings` (`TrackFeelingID`, `TrackingID`, `FeelingID`) VALUES
(1118, 1376, 10),
(1119, 1376, 1),
(1120, 1377, 9),
(1121, 1377, 13),
(1122, 1378, 7),
(1123, 1378, 8),
(1124, 1379, 14),
(1125, 1379, 3),
(1126, 1380, 8),
(1127, 1380, 19),
(1128, 1381, 12),
(1129, 1381, 15),
(1130, 1381, 16),
(1131, 1382, 18),
(1132, 1382, 22),
(1133, 1382, 24),
(1134, 1383, 8),
(1135, 1383, 19),
(1136, 1384, 1),
(1137, 1384, 5),
(1138, 1385, 9),
(1139, 1385, 11),
(1140, 1385, 13),
(1141, 1385, 16),
(1142, 1386, 3),
(1143, 1386, 5),
(1144, 1387, 12),
(1145, 1387, 15),
(1146, 1388, 10),
(1147, 1388, 12),
(1148, 1389, 11),
(1149, 1390, 12),
(1150, 1390, 13),
(1151, 1391, 9),
(1152, 1391, 11),
(1153, 1391, 12),
(1154, 1391, 13),
(1157, 1394, 9),
(1158, 1394, 16),
(1159, 1395, 12),
(1160, 1395, 16),
(1161, 1396, 1),
(1162, 1396, 5),
(1163, 1397, 9),
(1164, 1397, 12),
(1165, 1397, 13),
(1166, 1398, 12),
(1167, 1398, 1),
(1168, 1399, 5),
(1169, 1399, 6),
(1170, 1400, 17),
(1171, 1400, 19),
(1172, 1401, 19),
(1173, 1401, 22),
(1174, 1402, 15),
(1175, 1402, 5),
(1176, 1403, 9),
(1177, 1403, 12),
(1181, 1405, 9),
(1182, 1405, 12),
(1183, 1405, 16),
(1184, 1406, 5),
(1185, 1406, 6),
(1186, 1408, 9),
(1187, 1408, 5),
(1188, 1409, 9),
(1189, 1409, 12),
(1190, 1409, 15),
(1191, 1410, 17),
(1192, 1410, 18),
(1193, 1410, 20),
(1196, 1413, 12),
(1197, 1413, 1);

-- --------------------------------------------------------

--
-- Table structure for table `trackfoods`
--

CREATE TABLE `trackfoods` (
  `TrackFoodID` int(11) NOT NULL,
  `TrackingID` int(11) NOT NULL,
  `FoodID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `trackfoods`
--

INSERT INTO `trackfoods` (`TrackFoodID`, `TrackingID`, `FoodID`) VALUES
(492, 1376, 1),
(493, 1377, 2),
(494, 1378, 4),
(495, 1379, 1),
(496, 1380, 4),
(497, 1381, 3),
(498, 1382, 4),
(499, 1383, 4),
(500, 1384, 2),
(501, 1385, 1),
(502, 1386, 1),
(503, 1387, 3),
(504, 1388, 3),
(505, 1388, 5),
(506, 1389, 1),
(507, 1390, 1),
(508, 1391, 3),
(512, 1394, 2),
(513, 1394, 6),
(514, 1395, 3),
(515, 1396, 2),
(516, 1397, 2),
(517, 1397, 6),
(518, 1398, 1),
(519, 1400, 2),
(520, 1400, 7),
(521, 1401, 2),
(522, 1402, 1),
(523, 1403, 3),
(525, 1405, 3),
(526, 1406, 2),
(527, 1406, 6),
(528, 1407, 2),
(529, 1408, 1),
(530, 1409, 2),
(531, 1409, 3),
(532, 1410, 4),
(534, 1413, 1);

-- --------------------------------------------------------

--
-- Table structure for table `trackgoals`
--

CREATE TABLE `trackgoals` (
  `TrackGoalID` int(11) NOT NULL,
  `UserGoalID` int(11) NOT NULL,
  `Date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `trackgoals`
--

INSERT INTO `trackgoals` (`TrackGoalID`, `UserGoalID`, `Date`) VALUES
(634, 481, '2024-12-04'),
(635, 481, '2024-12-19'),
(636, 482, '2024-12-19'),
(637, 481, '2024-12-17'),
(638, 481, '2024-12-14'),
(639, 482, '2024-12-14'),
(640, 482, '2024-12-11'),
(641, 481, '2024-12-18'),
(642, 482, '2024-12-18'),
(643, 481, '2024-12-16'),
(644, 482, '2024-12-13'),
(645, 481, '2024-12-07'),
(646, 482, '2024-12-07'),
(647, 481, '2024-12-03'),
(648, 482, '2024-12-03'),
(649, 482, '2024-12-12'),
(650, 481, '2024-12-01'),
(651, 484, '2024-12-12'),
(652, 484, '2024-12-09'),
(653, 483, '2024-12-08'),
(654, 484, '2024-12-08'),
(655, 483, '2024-12-07'),
(657, 483, '2024-12-06'),
(658, 484, '2024-12-04'),
(659, 483, '2024-12-03'),
(660, 484, '2024-12-03'),
(661, 483, '2024-12-02'),
(662, 484, '2024-12-02'),
(663, 483, '2024-12-01'),
(664, 484, '2024-12-01'),
(665, 483, '2024-12-19');

-- --------------------------------------------------------

--
-- Table structure for table `tracklocations`
--

CREATE TABLE `tracklocations` (
  `TrackLocationID` int(11) NOT NULL,
  `TrackingID` int(11) NOT NULL,
  `LocationID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tracklocations`
--

INSERT INTO `tracklocations` (`TrackLocationID`, `TrackingID`, `LocationID`) VALUES
(620, 1376, 1),
(621, 1377, 9),
(622, 1378, 1),
(623, 1379, 1),
(624, 1380, 1),
(625, 1380, 5),
(626, 1381, 7),
(627, 1382, 1),
(628, 1383, 1),
(629, 1383, 3),
(630, 1384, 1),
(631, 1384, 9),
(632, 1385, 1),
(633, 1385, 6),
(634, 1386, 1),
(635, 1387, 7),
(636, 1388, 1),
(637, 1388, 7),
(638, 1389, 1),
(639, 1390, 1),
(640, 1390, 10),
(641, 1391, 7),
(646, 1394, 2),
(647, 1394, 8),
(648, 1395, 7),
(649, 1396, 1),
(650, 1396, 2),
(651, 1396, 8),
(652, 1397, 9),
(653, 1398, 2),
(654, 1398, 7),
(655, 1399, 1),
(656, 1399, 2),
(657, 1399, 5),
(658, 1400, 1),
(659, 1401, 1),
(660, 1402, 9),
(661, 1403, 7),
(662, 1403, 8),
(665, 1405, 2),
(666, 1405, 7),
(667, 1406, 1),
(668, 1406, 2),
(669, 1407, 2),
(670, 1407, 8),
(671, 1408, 1),
(672, 1408, 2),
(673, 1408, 8),
(674, 1409, 7),
(675, 1409, 9),
(676, 1410, 1),
(679, 1413, 1),
(680, 1413, 8);

-- --------------------------------------------------------

--
-- Table structure for table `trackmoods`
--

CREATE TABLE `trackmoods` (
  `TrackMoodID` int(11) NOT NULL,
  `TrackingID` int(11) NOT NULL,
  `MoodID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `trackmoods`
--

INSERT INTO `trackmoods` (`TrackMoodID`, `TrackingID`, `MoodID`) VALUES
(1065, 1376, 2),
(1066, 1377, 1),
(1067, 1378, 3),
(1068, 1379, 2),
(1069, 1380, 4),
(1070, 1381, 1),
(1071, 1382, 5),
(1072, 1383, 4),
(1073, 1384, 3),
(1074, 1385, 1),
(1075, 1386, 3),
(1076, 1387, 2),
(1077, 1388, 2),
(1078, 1389, 1),
(1079, 1390, 2),
(1080, 1391, 1),
(1083, 1394, 1),
(1084, 1395, 1),
(1085, 1396, 2),
(1086, 1397, 1),
(1087, 1398, 2),
(1088, 1399, 3),
(1089, 1400, 4),
(1090, 1401, 3),
(1091, 1402, 2),
(1092, 1403, 1),
(1094, 1405, 1),
(1095, 1406, 3),
(1096, 1407, 2),
(1097, 1408, 2),
(1098, 1409, 1),
(1099, 1410, 5),
(1102, 1413, 2);

-- --------------------------------------------------------

--
-- Table structure for table `tracksleeptime`
--

CREATE TABLE `tracksleeptime` (
  `sleepTimeID` int(11) NOT NULL,
  `sleepTime` varchar(5) NOT NULL,
  `TrackingID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tracksleeptime`
--

INSERT INTO `tracksleeptime` (`sleepTimeID`, `sleepTime`, `TrackingID`) VALUES
(320, '510', 1376),
(321, '480', 1377),
(322, '270', 1378),
(323, '450', 1379),
(324, '180', 1380),
(325, '630', 1381),
(326, '270', 1382),
(327, '210', 1383),
(328, '330', 1384),
(329, '450', 1385),
(330, '540', 1386),
(331, '330', 1387),
(332, '510', 1388),
(333, '480', 1389),
(334, '360', 1390),
(335, '510', 1391),
(338, '360', 1394),
(339, '510', 1395),
(340, '330', 1396),
(341, '540', 1397),
(342, '420', 1398),
(343, '390', 1399),
(344, '210', 1400),
(345, '300', 1401),
(346, '480', 1402),
(347, '450', 1403),
(349, '420', 1405),
(350, '510', 1406),
(351, '390', 1407),
(352, '420', 1408),
(353, '510', 1409),
(354, '570', 1410),
(356, '450', 1413);

-- --------------------------------------------------------

--
-- Table structure for table `trackweather`
--

CREATE TABLE `trackweather` (
  `TrackWeatherID` int(11) NOT NULL,
  `TrackingID` int(11) NOT NULL,
  `WeatherID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `trackweather`
--

INSERT INTO `trackweather` (`TrackWeatherID`, `TrackingID`, `WeatherID`) VALUES
(496, 1376, 3),
(497, 1377, 1),
(498, 1378, 6),
(499, 1379, 1),
(500, 1380, 3),
(501, 1381, 1),
(502, 1382, 5),
(503, 1382, 6),
(504, 1383, 3),
(505, 1384, 2),
(506, 1385, 1),
(507, 1386, 2),
(508, 1387, 1),
(509, 1388, 1),
(510, 1388, 8),
(511, 1390, 2),
(512, 1391, 1),
(515, 1394, 1),
(516, 1395, 2),
(517, 1395, 3),
(518, 1396, 1),
(519, 1396, 2),
(520, 1397, 1),
(521, 1398, 2),
(522, 1399, 3),
(523, 1400, 5),
(524, 1400, 6),
(525, 1402, 1),
(526, 1403, 2),
(527, 1403, 8),
(529, 1405, 1),
(530, 1405, 8),
(531, 1406, 2),
(532, 1407, 1),
(533, 1408, 2),
(534, 1409, 1),
(535, 1410, 4),
(536, 1410, 8),
(538, 1413, 1);

-- --------------------------------------------------------

--
-- Table structure for table `userdata`
--

CREATE TABLE `userdata` (
  `UserDataID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `LastLogin` date DEFAULT NULL,
  `LoginCount` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `userdata`
--

INSERT INTO `userdata` (`UserDataID`, `UserID`, `LastLogin`, `LoginCount`) VALUES
(49, 130, '2024-12-19', 13),
(50, 133, '2024-12-19', 27),
(51, 134, '2024-12-19', 19),
(52, 137, '2024-12-19', 29),
(53, 135, '2024-12-19', 34),
(54, 129, '2024-12-19', 79),
(55, 136, '2024-12-19', 3),
(56, 131, '2024-12-19', 87),
(57, 132, '2024-12-19', 28),
(58, 138, '2024-12-19', 45);

-- --------------------------------------------------------

--
-- Table structure for table `usergoals`
--

CREATE TABLE `usergoals` (
  `UserGoalID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `GoalID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `usergoals`
--

INSERT INTO `usergoals` (`UserGoalID`, `UserID`, `GoalID`) VALUES
(325, 26, 1),
(481, 130, 5),
(482, 130, 8),
(483, 133, 1),
(484, 133, 4);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `UserID` int(11) NOT NULL,
  `FirstName` varchar(50) NOT NULL,
  `LastName` varchar(50) NOT NULL,
  `Username` varchar(30) DEFAULT NULL,
  `Email` varchar(50) NOT NULL,
  `Password` varchar(30) NOT NULL,
  `Role` varchar(1) NOT NULL,
  `Created` date DEFAULT NULL,
  `profileImg` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`UserID`, `FirstName`, `LastName`, `Username`, `Email`, `Password`, `Role`, `Created`, `profileImg`) VALUES
(26, 'Main', 'Admin', 'MainAdmin', 'admin@example.com', 'admin', '1', '2024-11-01', 'admin.jpg'),
(129, 'John', 'Doe', 'johndoe123', 'johndoe@example.com', 'password', '0', '2024-11-11', 'admin.jpg'),
(130, 'Jane', 'Smith', 'janesmith456', 'janesmith@example.com', 'password', '0', '2024-11-16', 'admin.jpg'),
(131, 'Alex', 'Johnson', 'alexjohnson789', 'alexjohnson@example.com', 'password', '0', '2024-11-18', 'admin.jpg'),
(132, 'Sarah', 'Lee', 'sarahlee321', 'sarahlee@example.com', 'password', '0', '2024-12-01', 'admin.jpg'),
(133, 'Mark', 'Brown', 'markbrown101', 'markbrown@example.com', 'password', '0', '2024-12-04', 'admin.jpg'),
(134, 'Emily', 'Davis', 'emilydavis234', 'emilydavis@example.com', 'password', '0', '2024-12-07', 'admin.jpg'),
(135, 'Chris', 'Wilson', 'chriswilson567', 'chriswilson@example.com', 'password', '0', '2024-12-09', 'admin.jpg'),
(136, 'Lily', 'Martinez', 'lilymartinez890', 'lilymartinez@example.com', 'password', '0', '2024-12-11', 'admin.jpg'),
(137, 'Michael', 'White', 'michaelwhite123', 'michaelwhite@example.com', 'password', '0', '2024-12-11', 'admin.jpg'),
(138, 'Olivia', 'Taylor', 'oliviataylor456', 'oliviataylor@example.com', 'password', '0', '2024-12-16', 'admin.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `weather`
--

CREATE TABLE `weather` (
  `WeatherID` int(11) NOT NULL,
  `WeatherName` varchar(30) NOT NULL,
  `WeatherIcon` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `weather`
--

INSERT INTO `weather` (`WeatherID`, `WeatherName`, `WeatherIcon`) VALUES
(1, '晴れ', '<i class=\"fa-solid fa-sun\"></i>'),
(2, '曇り', '<i class=\"fa-solid fa-cloud\"></i>'),
(3, '雨', '<i class=\"fa-solid fa-cloud-rain\"></i>'),
(4, '雪', '<i class=\"fa-solid fa-snowflake\"></i>'),
(5, '雷', '<i class=\"fa-solid fa-cloud-bolt\"></i>'),
(6, '風', '<i class=\"fa-solid fa-wind\"></i>'),
(7, '蒸し暑い', '<i class=\"fa-solid fa-temperature-high\"></i>'),
(8, '寒い', '<i class=\"fa-solid fa-temperature-low\"></i>');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activities`
--
ALTER TABLE `activities`
  ADD PRIMARY KEY (`ActivityID`);

--
-- Indexes for table `blockedwords`
--
ALTER TABLE `blockedwords`
  ADD PRIMARY KEY (`blockedWordID`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`CommentID`),
  ADD KEY `UserID` (`UserID`),
  ADD KEY `PostID` (`PostID`);

--
-- Indexes for table `company`
--
ALTER TABLE `company`
  ADD PRIMARY KEY (`CompanyID`);

--
-- Indexes for table `dailytracking`
--
ALTER TABLE `dailytracking`
  ADD PRIMARY KEY (`TrackingID`),
  ADD KEY `UserID` (`UserID`);

--
-- Indexes for table `feelingloadings`
--
ALTER TABLE `feelingloadings`
  ADD PRIMARY KEY (`FeelingLoadingID`);

--
-- Indexes for table `feelings`
--
ALTER TABLE `feelings`
  ADD PRIMARY KEY (`FeelingID`),
  ADD KEY `FeelingLoadingID` (`FeelingLoadingID`);

--
-- Indexes for table `foods`
--
ALTER TABLE `foods`
  ADD PRIMARY KEY (`FoodID`);

--
-- Indexes for table `goalcategories`
--
ALTER TABLE `goalcategories`
  ADD PRIMARY KEY (`GoalCategoriesID`);

--
-- Indexes for table `goals`
--
ALTER TABLE `goals`
  ADD PRIMARY KEY (`GoalID`),
  ADD KEY `GoalCategoriesID` (`GoalCategoriesID`);

--
-- Indexes for table `locations`
--
ALTER TABLE `locations`
  ADD PRIMARY KEY (`LocationID`);

--
-- Indexes for table `memos`
--
ALTER TABLE `memos`
  ADD PRIMARY KEY (`MemoID`),
  ADD KEY `TrackingID` (`TrackingID`);

--
-- Indexes for table `moods`
--
ALTER TABLE `moods`
  ADD PRIMARY KEY (`MoodID`);

--
-- Indexes for table `postlikes`
--
ALTER TABLE `postlikes`
  ADD PRIMARY KEY (`LikeID`),
  ADD KEY `PostID` (`PostID`),
  ADD KEY `UserID` (`UserID`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`PostID`),
  ADD KEY `MoodID` (`MoodID`),
  ADD KEY `UserID` (`UserID`);

--
-- Indexes for table `requestcheck`
--
ALTER TABLE `requestcheck`
  ADD PRIMARY KEY (`RequestCheckID`),
  ADD KEY `PostID` (`PostID`);

--
-- Indexes for table `trackactivities`
--
ALTER TABLE `trackactivities`
  ADD PRIMARY KEY (`TrackActivityID`),
  ADD KEY `TrackingID` (`TrackingID`),
  ADD KEY `ActivityID` (`ActivityID`);

--
-- Indexes for table `trackcompany`
--
ALTER TABLE `trackcompany`
  ADD PRIMARY KEY (`TrackCompanyID`),
  ADD KEY `TrackingID` (`TrackingID`),
  ADD KEY `CompanyID` (`CompanyID`);

--
-- Indexes for table `trackfeelings`
--
ALTER TABLE `trackfeelings`
  ADD PRIMARY KEY (`TrackFeelingID`),
  ADD KEY `TrackingID` (`TrackingID`),
  ADD KEY `FeelingID` (`FeelingID`);

--
-- Indexes for table `trackfoods`
--
ALTER TABLE `trackfoods`
  ADD PRIMARY KEY (`TrackFoodID`),
  ADD KEY `TrackingID` (`TrackingID`),
  ADD KEY `FoodID` (`FoodID`);

--
-- Indexes for table `trackgoals`
--
ALTER TABLE `trackgoals`
  ADD PRIMARY KEY (`TrackGoalID`),
  ADD KEY `UserGoalID` (`UserGoalID`);

--
-- Indexes for table `tracklocations`
--
ALTER TABLE `tracklocations`
  ADD PRIMARY KEY (`TrackLocationID`),
  ADD KEY `TrackingID` (`TrackingID`),
  ADD KEY `LocationID` (`LocationID`);

--
-- Indexes for table `trackmoods`
--
ALTER TABLE `trackmoods`
  ADD PRIMARY KEY (`TrackMoodID`),
  ADD KEY `TrackingID` (`TrackingID`),
  ADD KEY `MoodID` (`MoodID`);

--
-- Indexes for table `tracksleeptime`
--
ALTER TABLE `tracksleeptime`
  ADD PRIMARY KEY (`sleepTimeID`),
  ADD KEY `TrackingID` (`TrackingID`);

--
-- Indexes for table `trackweather`
--
ALTER TABLE `trackweather`
  ADD PRIMARY KEY (`TrackWeatherID`),
  ADD KEY `TrackingID` (`TrackingID`),
  ADD KEY `WeatherID` (`WeatherID`);

--
-- Indexes for table `userdata`
--
ALTER TABLE `userdata`
  ADD PRIMARY KEY (`UserDataID`),
  ADD KEY `UserID` (`UserID`);

--
-- Indexes for table `usergoals`
--
ALTER TABLE `usergoals`
  ADD PRIMARY KEY (`UserGoalID`),
  ADD KEY `UserID` (`UserID`),
  ADD KEY `GoalID` (`GoalID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`UserID`),
  ADD UNIQUE KEY `Username` (`Username`);

--
-- Indexes for table `weather`
--
ALTER TABLE `weather`
  ADD PRIMARY KEY (`WeatherID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activities`
--
ALTER TABLE `activities`
  MODIFY `ActivityID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=83;

--
-- AUTO_INCREMENT for table `blockedwords`
--
ALTER TABLE `blockedwords`
  MODIFY `blockedWordID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `CommentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=165;

--
-- AUTO_INCREMENT for table `company`
--
ALTER TABLE `company`
  MODIFY `CompanyID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `dailytracking`
--
ALTER TABLE `dailytracking`
  MODIFY `TrackingID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1414;

--
-- AUTO_INCREMENT for table `feelingloadings`
--
ALTER TABLE `feelingloadings`
  MODIFY `FeelingLoadingID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `feelings`
--
ALTER TABLE `feelings`
  MODIFY `FeelingID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT for table `foods`
--
ALTER TABLE `foods`
  MODIFY `FoodID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `goalcategories`
--
ALTER TABLE `goalcategories`
  MODIFY `GoalCategoriesID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `goals`
--
ALTER TABLE `goals`
  MODIFY `GoalID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `locations`
--
ALTER TABLE `locations`
  MODIFY `LocationID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `memos`
--
ALTER TABLE `memos`
  MODIFY `MemoID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `moods`
--
ALTER TABLE `moods`
  MODIFY `MoodID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `postlikes`
--
ALTER TABLE `postlikes`
  MODIFY `LikeID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=225;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `PostID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=474;

--
-- AUTO_INCREMENT for table `requestcheck`
--
ALTER TABLE `requestcheck`
  MODIFY `RequestCheckID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `trackactivities`
--
ALTER TABLE `trackactivities`
  MODIFY `TrackActivityID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1102;

--
-- AUTO_INCREMENT for table `trackcompany`
--
ALTER TABLE `trackcompany`
  MODIFY `TrackCompanyID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=654;

--
-- AUTO_INCREMENT for table `trackfeelings`
--
ALTER TABLE `trackfeelings`
  MODIFY `TrackFeelingID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1198;

--
-- AUTO_INCREMENT for table `trackfoods`
--
ALTER TABLE `trackfoods`
  MODIFY `TrackFoodID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=535;

--
-- AUTO_INCREMENT for table `trackgoals`
--
ALTER TABLE `trackgoals`
  MODIFY `TrackGoalID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=667;

--
-- AUTO_INCREMENT for table `tracklocations`
--
ALTER TABLE `tracklocations`
  MODIFY `TrackLocationID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=681;

--
-- AUTO_INCREMENT for table `trackmoods`
--
ALTER TABLE `trackmoods`
  MODIFY `TrackMoodID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1103;

--
-- AUTO_INCREMENT for table `tracksleeptime`
--
ALTER TABLE `tracksleeptime`
  MODIFY `sleepTimeID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=357;

--
-- AUTO_INCREMENT for table `trackweather`
--
ALTER TABLE `trackweather`
  MODIFY `TrackWeatherID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=539;

--
-- AUTO_INCREMENT for table `userdata`
--
ALTER TABLE `userdata`
  MODIFY `UserDataID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT for table `usergoals`
--
ALTER TABLE `usergoals`
  MODIFY `UserGoalID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=485;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `UserID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=147;

--
-- AUTO_INCREMENT for table `weather`
--
ALTER TABLE `weather`
  MODIFY `WeatherID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`),
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`PostID`) REFERENCES `posts` (`PostID`);

--
-- Constraints for table `dailytracking`
--
ALTER TABLE `dailytracking`
  ADD CONSTRAINT `dailytracking_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`);

--
-- Constraints for table `feelings`
--
ALTER TABLE `feelings`
  ADD CONSTRAINT `feelings_ibfk_1` FOREIGN KEY (`FeelingLoadingID`) REFERENCES `feelingloadings` (`FeelingLoadingID`);

--
-- Constraints for table `goals`
--
ALTER TABLE `goals`
  ADD CONSTRAINT `goals_ibfk_1` FOREIGN KEY (`GoalCategoriesID`) REFERENCES `goalcategories` (`GoalCategoriesID`);

--
-- Constraints for table `memos`
--
ALTER TABLE `memos`
  ADD CONSTRAINT `memos_ibfk_1` FOREIGN KEY (`TrackingID`) REFERENCES `dailytracking` (`TrackingID`);

--
-- Constraints for table `postlikes`
--
ALTER TABLE `postlikes`
  ADD CONSTRAINT `postlikes_ibfk_1` FOREIGN KEY (`PostID`) REFERENCES `posts` (`PostID`),
  ADD CONSTRAINT `postlikes_ibfk_2` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`);

--
-- Constraints for table `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`MoodID`) REFERENCES `moods` (`MoodID`),
  ADD CONSTRAINT `posts_ibfk_2` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`);

--
-- Constraints for table `requestcheck`
--
ALTER TABLE `requestcheck`
  ADD CONSTRAINT `requestcheck_ibfk_1` FOREIGN KEY (`PostID`) REFERENCES `posts` (`PostID`);

--
-- Constraints for table `trackactivities`
--
ALTER TABLE `trackactivities`
  ADD CONSTRAINT `trackactivities_ibfk_1` FOREIGN KEY (`TrackingID`) REFERENCES `dailytracking` (`TrackingID`),
  ADD CONSTRAINT `trackactivities_ibfk_2` FOREIGN KEY (`ActivityID`) REFERENCES `activities` (`ActivityID`);

--
-- Constraints for table `trackcompany`
--
ALTER TABLE `trackcompany`
  ADD CONSTRAINT `trackcompany_ibfk_1` FOREIGN KEY (`TrackingID`) REFERENCES `dailytracking` (`TrackingID`),
  ADD CONSTRAINT `trackcompany_ibfk_2` FOREIGN KEY (`CompanyID`) REFERENCES `company` (`CompanyID`);

--
-- Constraints for table `trackfeelings`
--
ALTER TABLE `trackfeelings`
  ADD CONSTRAINT `trackfeelings_ibfk_1` FOREIGN KEY (`TrackingID`) REFERENCES `dailytracking` (`TrackingID`),
  ADD CONSTRAINT `trackfeelings_ibfk_2` FOREIGN KEY (`FeelingID`) REFERENCES `feelings` (`FeelingID`);

--
-- Constraints for table `trackfoods`
--
ALTER TABLE `trackfoods`
  ADD CONSTRAINT `trackfoods_ibfk_1` FOREIGN KEY (`TrackingID`) REFERENCES `dailytracking` (`TrackingID`),
  ADD CONSTRAINT `trackfoods_ibfk_2` FOREIGN KEY (`FoodID`) REFERENCES `foods` (`FoodID`);

--
-- Constraints for table `trackgoals`
--
ALTER TABLE `trackgoals`
  ADD CONSTRAINT `trackgoals_ibfk_1` FOREIGN KEY (`UserGoalID`) REFERENCES `usergoals` (`UserGoalID`);

--
-- Constraints for table `tracklocations`
--
ALTER TABLE `tracklocations`
  ADD CONSTRAINT `tracklocations_ibfk_1` FOREIGN KEY (`TrackingID`) REFERENCES `dailytracking` (`TrackingID`),
  ADD CONSTRAINT `tracklocations_ibfk_2` FOREIGN KEY (`LocationID`) REFERENCES `locations` (`LocationID`);

--
-- Constraints for table `trackmoods`
--
ALTER TABLE `trackmoods`
  ADD CONSTRAINT `trackmoods_ibfk_1` FOREIGN KEY (`TrackingID`) REFERENCES `dailytracking` (`TrackingID`),
  ADD CONSTRAINT `trackmoods_ibfk_2` FOREIGN KEY (`MoodID`) REFERENCES `moods` (`MoodID`);

--
-- Constraints for table `tracksleeptime`
--
ALTER TABLE `tracksleeptime`
  ADD CONSTRAINT `tracksleeptime_ibfk_1` FOREIGN KEY (`TrackingID`) REFERENCES `dailytracking` (`TrackingID`);

--
-- Constraints for table `trackweather`
--
ALTER TABLE `trackweather`
  ADD CONSTRAINT `trackweather_ibfk_1` FOREIGN KEY (`TrackingID`) REFERENCES `dailytracking` (`TrackingID`),
  ADD CONSTRAINT `trackweather_ibfk_2` FOREIGN KEY (`WeatherID`) REFERENCES `weather` (`WeatherID`);

--
-- Constraints for table `userdata`
--
ALTER TABLE `userdata`
  ADD CONSTRAINT `userdata_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`);

--
-- Constraints for table `usergoals`
--
ALTER TABLE `usergoals`
  ADD CONSTRAINT `usergoals_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`),
  ADD CONSTRAINT `usergoals_ibfk_2` FOREIGN KEY (`GoalID`) REFERENCES `goals` (`GoalID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
