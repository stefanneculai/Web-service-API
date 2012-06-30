CREATE TABLE IF NOT EXISTS `ws_content` (
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

INSERT INTO `ws_content` (`content_id`, `type_id`, `title`, `alias`, `body`, `access`, `state`, `temporary`, `featured`, `created_date`, `created_user_id`, `modified_date`, `modified_user_id`, `checked_out_session`, `checked_out_user_id`, `publish_start_date`, `publish_end_date`, `likes`, `revision`, `config`, `media`, `rules`) VALUES(1, 1, 'General', 'general', 'this is a test', 2, 1, 0, 0, '2011-01-01 00:00:01', 1, '2011-01-01 00:00:01', 1, '', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 0, '{"target":"","image":""}', '', '{"core.create":{"10":0,"12":0},"core.delete":[],"core.edit":[],"core.edit.state":[]}');
INSERT INTO `ws_content` (`content_id`, `type_id`, `title`, `alias`, `body`, `access`, `state`, `temporary`, `featured`, `created_date`, `created_user_id`, `modified_date`, `modified_user_id`, `checked_out_session`, `checked_out_user_id`, `publish_start_date`, `publish_end_date`, `likes`, `revision`, `config`, `media`, `rules`) VALUES(2, 1, 'General', 'general', 'this is a test', 2, 1, 0, 0, '2012-01-01 00:00:01', 1, '2011-01-01 00:00:01', 1, '', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 0, '{"target":"","image":""}', '', '{"core.create":{"10":0,"12":0},"core.delete":[],"core.edit":[],"core.edit.state":[]}');

CREATE TABLE IF NOT EXISTS `ws_content_hits` (
  `content_id` int(11) NOT NULL DEFAULT '0',
  `hits` int(11) NOT NULL DEFAULT '0',
  `hit_modified_date` datetime DEFAULT NULL,
  PRIMARY KEY (`content_id`)
);

INSERT INTO `ws_content_hits` (`content_id`, `hits`, `hit_modified_date`) VALUES(1, 0, NULL);
INSERT INTO `ws_content_hits` (`content_id`, `hits`, `hit_modified_date`) VALUES(2, 0, NULL);

CREATE TABLE IF NOT EXISTS `ws_content_likes` (
  `content_id` int(11) NOT NULL DEFAULT '0',
  `user_id` int(11) NOT NULL DEFAULT '0',
  `like_state` tinyint(1) NOT NULL DEFAULT '1',
  `like_modified_date` datetime DEFAULT NULL,
  PRIMARY KEY (`content_id`,`user_id`)
);


CREATE TABLE IF NOT EXISTS `ws_content_types` (
  `type_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL DEFAULT '',
  `alias` varchar(255) NOT NULL DEFAULT '',
  `table` varchar(255) NOT NULL DEFAULT '',
  `rules` text NOT NULL,
  PRIMARY KEY (`type_id`)
);

INSERT INTO `ws_content_types` (`type_id`, `title`, `alias`, `table`, `rules`) VALUES(1, 'General', 'general', '#__general', '');

CREATE TABLE IF NOT EXISTS `ws_general` (
  `id` int(11),
  `content_id` int(11) NOT NULL,
  `field1` varchar(100) NOT NULL,
  `field2` varchar(100) NOT NULL,
  `field3` varchar(100) NOT NULL,
  `field4` varchar(100) DEFAULT NULL,
  `field5` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
);

INSERT INTO `ws_general` (`id`, `content_id`, `field1`, `field2`, `field3`, `field4`, `field5`) VALUES(1, 1, 'field1 test', 'field2 test', 'field3 test', 'field4 test', 'field5 test');
INSERT INTO `ws_general` (`id`, `content_id`, `field1`, `field2`, `field3`, `field4`, `field5`) VALUES(2, 2, 'f1', 'f2', 'f3', 'f4', 'f5');

CREATE TABLE IF NOT EXISTS `ws_users` (
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
  `params` text NOT NULL,
  PRIMARY KEY (`id`)
);

INSERT INTO `ws_users` (`id`, `name`, `username`, `email`, `password`, `usertype`, `block`, `sendEmail`, `registerDate`, `lastvisitDate`, `activation`, `params`) VALUES(1, 'test', 'test', 'test@aaa.com', 'rarata', '', 0, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', '');

--
-- Table structure for table `ws_viewlevels`
--

CREATE TABLE IF NOT EXISTS `ws_viewlevels` (
  `id` int(10) NOT NULL DEFAULT 1,
  `title` varchar(100) NOT NULL DEFAULT '',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `rules` varchar(5120) NULL,
  PRIMARY KEY (`id`)
);

INSERT INTO `ws_viewlevels` VALUES(2, 'user', 0, '');
