-- phpMyAdmin SQL Dump
-- version 4.9.5deb2
-- https://www.phpmyadmin.net/
--
-- Хост: localhost:3306
-- Время создания: Янв 12 2022 г., 08:23
-- Версия сервера: 8.0.26-0ubuntu0.20.04.2
-- Версия PHP: 7.4.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `gasable_test6`
--

-- --------------------------------------------------------

--
-- Структура таблицы `schedule_notifications`
--

CREATE TABLE `schedule_notifications` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `order_status` int DEFAULT NULL,
  `country_id` int NOT NULL,
  `total_note` int NOT NULL DEFAULT '0',
  `minimum_orders` int NOT NULL DEFAULT '0',
  `text` text NOT NULL,
  `insert_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_date` timestamp NULL DEFAULT NULL,
  `status` int NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Дамп данных таблицы `schedule_notifications`
--

INSERT INTO `schedule_notifications` (`id`, `name`, `start_date`, `end_date`, `order_status`, `country_id`, `total_note`, `minimum_orders`, `text`, `insert_date`, `update_date`, `status`) VALUES
(1, 'nikolay', '2022-01-11 10:18:00', '2022-01-15 11:45:00', 2, 17, 2, 3, 'lorem ipsum', '2022-01-12 07:17:33', NULL, 1);

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `schedule_notifications`
--
ALTER TABLE `schedule_notifications`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `schedule_notifications`
--
ALTER TABLE `schedule_notifications`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
