-- phpMyAdmin SQL Dump
-- version 4.0.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Sep 24, 2014 at 10:19 AM
-- Server version: 5.6.12-log
-- PHP Version: 5.4.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `facebookscraper`
--
CREATE DATABASE IF NOT EXISTS `facebookscraper` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
USE `facebookscraper`;

-- --------------------------------------------------------

--
-- Table structure for table `actions`
--

CREATE TABLE IF NOT EXISTS `actions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `post_id` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `objectid` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `facebookid` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `page` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(50) CHARACTER SET latin1 NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE IF NOT EXISTS `posts` (
  `id` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `page` varchar(100) CHARACTER SET latin1 NOT NULL,
  `message` text COLLATE utf8_unicode_ci NOT NULL,
  `picture` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `link` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `icon` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `status_type` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `objectid` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `fb_created_time` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `fb_updated_time` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `scrapes`
--

CREATE TABLE IF NOT EXISTS `scrapes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page` varchar(100) CHARACTER SET latin1 NOT NULL,
  `total_posts` int(11) NOT NULL,
  `total_likes` int(11) NOT NULL,
  `total_comments` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `facebookid` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `total_likes` int(11) DEFAULT '0',
  `total_comments` int(11) DEFAULT '0',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
