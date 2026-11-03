-- Tables the BGA framework creates for every game (subset used by Altered)
-- Hidden auto-increment key: unordered scans return players in insertion (seat) order, as on BGA
CREATE TABLE `player` (
  `player_row` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `player_id` int(10) unsigned NOT NULL,
  `player_no` int(10) NOT NULL DEFAULT 0,
  `player_name` varchar(32) NOT NULL,
  `player_avatar` varchar(10) NOT NULL DEFAULT '',
  `player_color` varchar(6) NOT NULL,
  `player_canal` varchar(32) NOT NULL DEFAULT '',
  `player_score` int(10) NOT NULL DEFAULT 0,
  `player_score_aux` int(10) NOT NULL DEFAULT 0,
  `player_zombie` tinyint(1) NOT NULL DEFAULT 0,
  `player_eliminated` tinyint(1) NOT NULL DEFAULT 0,
  `player_is_multiactive` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`player_row`),
  UNIQUE KEY (`player_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE `global` (
  `global_id` int(10) unsigned NOT NULL,
  `global_value` int(10) DEFAULT NULL,
  PRIMARY KEY (`global_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
INSERT INTO `global` VALUES (1, 1), (2, 0), (3, 0), (10, 0);
CREATE TABLE `stats` (
  `stats_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `stats_type` smallint(5) unsigned NOT NULL,
  `stats_player_id` int(10) unsigned DEFAULT NULL,
  `stats_value` float NOT NULL,
  PRIMARY KEY (`stats_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE `gamelog` (
  `gamelog_packet_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `gamelog_move_id` int(10) unsigned DEFAULT NULL,
  `gamelog_notification` mediumtext,
  PRIMARY KEY (`gamelog_packet_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
