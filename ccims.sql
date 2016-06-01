-- phpMyAdmin SQL Dump
-- version 3.2.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 07, 2013 at 03:28 PM
-- Server version: 5.1.44
-- PHP Version: 5.3.1

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `ccims`
--
CREATE DATABASE `ccims` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `ccims`;

-- --------------------------------------------------------

--
-- Table structure for table `ccims_users`
--

CREATE TABLE IF NOT EXISTS `ccims_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) CHARACTER SET utf8 COLLATE utf8_roman_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8 COLLATE utf8_roman_ci NOT NULL,
  `first_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_roman_ci NOT NULL,
  `last_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_roman_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8 COLLATE utf8_roman_ci DEFAULT NULL,
  `favorite_movie` varchar(255) CHARACTER SET ucs2 COLLATE ucs2_roman_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `ccims_users`
--

INSERT INTO `ccims_users` (`id`, `username`, `password`, `first_name`, `last_name`, `email`, `favorite_movie`) VALUES
(1, 'clay', 'clay123', 'Clay', 'Leeds', 'clay@apache.org', 'Blazing Saddles'),
(2, 'allen', 'allen123', 'Allen', 'Ury', 'aury@cci.edu', 'Ferris Beuller''s Day Off'),
(3, 'chad', 'chad123', 'Chad', 'Marciniak', 'cmarciniak@cci.edu', 'Space Balls'),
(4, 'michael', 'michael123', 'Michael', 'Glenn', 'mglenn@cci.edu', 'Star Wars'),
(5, 'john', 'john123', 'John', 'Craig', 'jcraig@cci.edu', 'Start Trek Generations'),
(6, 'joe', 'joe123', 'Joe', 'Brock', 'jbrock@cci.edu', 'The Avengers');
