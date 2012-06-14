-- phpMyAdmin SQL Dump
-- version 3.3.9
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 14, 2012 at 06:46 AM
-- Server version: 5.1.53
-- PHP Version: 5.3.4

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `ws`
--

-- --------------------------------------------------------

--
-- Table structure for table `ws_content`
--

CREATE TABLE IF NOT EXISTS `ws_content` (
  `content_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type_id` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL DEFAULT '',
  `alias` varchar(255) NOT NULL DEFAULT '',
  `body` text NOT NULL,
  `access` int(10) unsigned DEFAULT NULL,
  `state` tinyint(4) NOT NULL DEFAULT '0',
  `temporary` tinyint(4) unsigned NOT NULL DEFAULT '1',
  `featured` tinyint(4) unsigned NOT NULL DEFAULT '0',
  `created_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_user_id` int(10) unsigned DEFAULT NULL,
  `modified_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_user_id` int(10) unsigned DEFAULT NULL,
  `checked_out_session` varchar(255) NOT NULL DEFAULT '',
  `checked_out_user_id` int(10) unsigned DEFAULT NULL,
  `publish_start_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_end_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `likes` int(10) unsigned NOT NULL DEFAULT '0',
  `revision` int(10) unsigned NOT NULL DEFAULT '0',
  `config` mediumtext NOT NULL,
  `media` text NOT NULL,
  `rules` text NOT NULL,
  PRIMARY KEY (`content_id`),
  KEY `type_id` (`type_id`),
  KEY `idx_visibility` (`type_id`,`state`,`access`,`publish_start_date`,`publish_end_date`),
  KEY `idx_visibility_created` (`type_id`,`state`,`access`,`publish_start_date`,`publish_end_date`,`created_date`),
  KEY `idx_visibility_modified` (`type_id`,`state`,`access`,`publish_start_date`,`publish_end_date`,`modified_date`),
  KEY `idx_visibility_likes` (`type_id`,`state`,`access`,`publish_start_date`,`publish_end_date`,`likes`),
  KEY `modified_user_id` (`modified_user_id`),
  KEY `checked_out_user_id` (`checked_out_user_id`),
  KEY `created_user_id` (`created_user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=15 ;

--
-- Dumping data for table `ws_content`
--

INSERT INTO `ws_content` (`content_id`, `type_id`, `title`, `alias`, `body`, `access`, `state`, `temporary`, `featured`, `created_date`, `created_user_id`, `modified_date`, `modified_user_id`, `checked_out_session`, `checked_out_user_id`, `publish_start_date`, `publish_end_date`, `likes`, `revision`, `config`, `media`, `rules`) VALUES
(1, 1, 'General', 'general', 'this is a test', 1, 1, 0, 0, '2011-01-01 00:00:01', 1, '2011-01-01 00:00:01', 1, '', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 0, '{"target":"","image":""}', '', '{"core.create":{"10":0,"12":0},"core.delete":[],"core.edit":[],"core.edit.state":[]}'),
(2, 1, '', '', '', NULL, 1, 1, 0, '2012-06-10 14:03:38', NULL, '0000-00-00 00:00:00', NULL, '', NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 0, '', '', ''),
(3, 1, '', '', '', NULL, 1, 1, 0, '2012-06-10 14:03:59', NULL, '0000-00-00 00:00:00', NULL, '', NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 0, '', '', ''),
(4, 1, '', '', '', NULL, 1, 1, 0, '2012-06-10 14:04:15', NULL, '0000-00-00 00:00:00', NULL, '', NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 0, '', '', ''),
(5, 1, '', '', '', NULL, 1, 1, 0, '2012-06-10 14:04:59', NULL, '2012-06-12 16:43:18', NULL, '9evl268564t50agpguopjdm8p3', NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 0, '', '', ''),
(6, 1, '', '', '', NULL, 1, 1, 0, '2012-06-10 14:05:17', NULL, '0000-00-00 00:00:00', NULL, '', NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 0, '', '', ''),
(7, 1, '', '', '', NULL, 1, 1, 0, '2012-06-10 14:05:36', NULL, '0000-00-00 00:00:00', NULL, '', NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 0, '', '', ''),
(12, 1, '', '', '', NULL, 1, 1, 0, '2012-06-10 14:07:06', NULL, '0000-00-00 00:00:00', NULL, '', NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 0, '', '', ''),
(13, 1, '', '', '', NULL, 1, 1, 0, '2012-06-10 14:07:25', NULL, '0000-00-00 00:00:00', NULL, '', NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 0, '', '', ''),
(14, 1, '', '', '', NULL, 1, 1, 0, '2012-06-10 14:07:35', NULL, '0000-00-00 00:00:00', NULL, '', NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 0, '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `ws_content_hits`
--

CREATE TABLE IF NOT EXISTS `ws_content_hits` (
  `content_id` int(10) unsigned NOT NULL DEFAULT '0',
  `hits` int(10) unsigned NOT NULL DEFAULT '0',
  `hit_modified_date` datetime DEFAULT NULL COMMENT 'The time that the content was last hit.',
  PRIMARY KEY (`content_id`),
  KEY `idx_hits` (`hits`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `ws_content_hits`
--

INSERT INTO `ws_content_hits` (`content_id`, `hits`, `hit_modified_date`) VALUES
(1, 0, NULL),
(2, 0, NULL),
(3, 0, NULL),
(4, 0, NULL),
(5, 0, NULL),
(6, 0, NULL),
(7, 0, NULL),
(8, 0, NULL),
(9, 0, NULL),
(10, 0, NULL),
(11, 0, NULL),
(12, 0, NULL),
(13, 0, NULL),
(14, 0, NULL),
(15, 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `ws_content_likes`
--

CREATE TABLE IF NOT EXISTS `ws_content_likes` (
  `content_id` int(10) unsigned NOT NULL DEFAULT '0',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `like_state` tinyint(1) NOT NULL DEFAULT '1' COMMENT '+1 if the user likes the content, -1 if the user explicitly dislikes the content.',
  `like_modified_date` datetime DEFAULT NULL COMMENT 'The time that the like was updated',
  PRIMARY KEY (`content_id`,`user_id`),
  KEY `member_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `ws_content_likes`
--


-- --------------------------------------------------------

--
-- Table structure for table `ws_content_types`
--

CREATE TABLE IF NOT EXISTS `ws_content_types` (
  `type_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `alias` varchar(255) NOT NULL DEFAULT '',
  `table` varchar(255) NOT NULL DEFAULT '',
  `rules` text NOT NULL,
  PRIMARY KEY (`type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `ws_content_types`
--

INSERT INTO `ws_content_types` (`type_id`, `title`, `alias`, `table`, `rules`) VALUES
(1, 'General', 'general', '#__general', '');

-- --------------------------------------------------------

--
-- Table structure for table `ws_general`
--

CREATE TABLE IF NOT EXISTS `ws_general` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content_id` int(11) NOT NULL,
  `field1` varchar(100) NOT NULL,
  `field2` varchar(100) NOT NULL,
  `field3` varchar(100) NOT NULL,
  `field4` varchar(100) DEFAULT NULL,
  `field5` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=16 ;

--
-- Dumping data for table `ws_general`
--

INSERT INTO `ws_general` (`id`, `content_id`, `field1`, `field2`, `field3`, `field4`, `field5`) VALUES
(1, 1, 'field1 test', 'field2 test', 'field3 test', 'field4 test', 'field5 test'),
(2, 2, 'field1', 'field2', 'field3', NULL, NULL),
(3, 3, 'field1', 'field2', 'field3', NULL, NULL),
(4, 4, 'field1', 'field2', 'field3', NULL, NULL),
(5, 5, 'super tare', 'field2', 'field3', NULL, NULL),
(6, 6, 'field1', 'field2', 'field3', NULL, NULL),
(7, 7, 'field1', 'field2', 'field3', NULL, NULL),
(8, 8, 'field1', 'field2', 'field3', NULL, NULL),
(9, 9, 'field1', 'field2', 'field3', NULL, NULL),
(10, 10, 'field1', 'field2', 'field3', NULL, NULL),
(11, 11, 'field1', 'field2', 'field3', NULL, NULL),
(12, 12, 'field1', 'field2', 'field3', NULL, NULL),
(13, 13, 'field1', 'field2', 'field3', NULL, NULL),
(14, 14, 'field1', 'field2', 'field3', NULL, NULL),
(15, 15, 'field1', 'field2', 'field3', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `ws_users`
--

CREATE TABLE IF NOT EXISTS `ws_users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `username` varchar(150) NOT NULL DEFAULT '',
  `email` varchar(100) NOT NULL DEFAULT '',
  `password` varchar(100) NOT NULL DEFAULT '',
  `usertype` varchar(25) NOT NULL DEFAULT '',
  `block` tinyint(4) NOT NULL DEFAULT '0',
  `sendEmail` tinyint(4) DEFAULT '0',
  `registerDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `lastvisitDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `activation` varchar(100) NOT NULL DEFAULT '',
  `params` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `usertype` (`usertype`),
  KEY `idx_name` (`name`),
  KEY `idx_block` (`block`),
  KEY `username` (`username`),
  KEY `email` (`email`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `ws_users`
--

INSERT INTO `ws_users` (`id`, `name`, `username`, `email`, `password`, `usertype`, `block`, `sendEmail`, `registerDate`, `lastvisitDate`, `activation`, `params`) VALUES
(1, 'test', 'test', 'test@aaa.com', 'rarata', '', 0, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', '');
