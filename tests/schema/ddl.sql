CREATE TABLE IF NOT EXISTS `jos_content` (
  `content_id` int(11) NOT NULL,
  `type_id` int(11) NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL DEFAULT '',
  `alias` varchar(255) NOT NULL DEFAULT '',
  `body` text,
  `access` int(11) DEFAULT NULL,
  `state` tinyint(4) NOT NULL DEFAULT '0',
  `temporary` int(11) NOT NULL DEFAULT '1',
  `featured` int(11) NOT NULL DEFAULT '0',
  `created_date` datetime DEFAULT '0000-00-00 00:00:00',
  `created_user_id` int(11) DEFAULT NULL,
  `modified_date` datetime DEFAULT '0000-00-00 00:00:00',
  `modified_user_id` int(11) DEFAULT NULL,
  `checked_out_session` varchar(255) NOT NULL DEFAULT '',
  `checked_out_user_id` int(11) DEFAULT NULL,
  `publish_start_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_end_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `likes` int(11) NOT NULL DEFAULT '0',
  `revision` int(11) NOT NULL DEFAULT '0',
  `config` mediumtext NULL,
  `media` text NULL,
  `rules` text NULL,
  PRIMARY KEY (`content_id`)
);

CREATE TABLE IF NOT EXISTS `jos_content_hits` (
  `content_id` int(11) NOT NULL DEFAULT '0',
  `hits` int(11) NOT NULL DEFAULT '0',
  `hit_modified_date` datetime DEFAULT NULL,
  PRIMARY KEY (`content_id`)
);

INSERT INTO `jos_content_hits` (`content_id`, `hits`, `hit_modified_date`) VALUES(1, 0, NULL);
INSERT INTO `jos_content_hits` (`content_id`, `hits`, `hit_modified_date`) VALUES(2, 0, NULL);

CREATE TABLE IF NOT EXISTS `jos_content_likes` (
  `content_id` int(11) NOT NULL DEFAULT '0',
  `user_id` int(11) NOT NULL DEFAULT '0',
  `like_state` tinyint(1) NOT NULL DEFAULT '1',
  `like_modified_date` datetime DEFAULT NULL,
  PRIMARY KEY (`content_id`,`user_id`)
);


CREATE TABLE IF NOT EXISTS `jos_content_types` (
  `type_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL DEFAULT '',
  `alias` varchar(255) NOT NULL DEFAULT '',
  `table` varchar(255) NOT NULL DEFAULT '',
  `rules` text NOT NULL,
  PRIMARY KEY (`type_id`)
);

CREATE TABLE IF NOT EXISTS `jos_general` (
  `id` int(11),
  `content_id` int(11) NOT NULL,
  `field1` varchar(100) NOT NULL,
  `field2` varchar(100) NOT NULL,
  `field3` varchar(100) NOT NULL,
  `field4` varchar(100) DEFAULT NULL,
  `field5` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `jos_users` (
  `id` int(11),
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
  `params` text NULL,
  PRIMARY KEY (`id`)
);

--
-- Table structure for table `jos_viewlevels`
--

CREATE TABLE IF NOT EXISTS `jos_viewlevels` (
  `id` int(10) NOT NULL DEFAULT 1,
  `title` varchar(100) NOT NULL DEFAULT '',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `rules` varchar(5120) NULL,
  PRIMARY KEY (`id`)
);

INSERT INTO `jos_viewlevels` VALUES(2, 'user', 0, '');

--
-- Table structure for table `jos_session`
--

CREATE TABLE `jos_session` (
  `session_id` TEXT NOT NULL DEFAULT '',
  `client_id` INTEGER NOT NULL DEFAULT '0',
  `guest` INTEGER DEFAULT '1',
  `time` TEXT DEFAULT '',
  `data` TEXT DEFAULT NULL,
  `userid` INTEGER DEFAULT '0',
  `username` TEXT DEFAULT '',
  `usertype` TEXT DEFAULT '',
  CONSTRAINT `idx_session` PRIMARY KEY (`session_id`)
);

CREATE INDEX `idx_session_whosonline` ON `jos_session` (`guest`,`usertype`);
CREATE INDEX `idx_session_user` ON `jos_session` (`userid`);
CREATE INDEX `idx_session_time` ON `jos_session` (`time`);
