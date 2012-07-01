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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


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
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_content_id` FOREIGN KEY (`content_id`)
  REFERENCES `ws_content`(`content_id`)
  ON DELETE CASCADE
) DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

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
