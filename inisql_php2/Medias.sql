
drop table if exists `medias`;




CREATE TABLE IF NOT EXISTS `medias` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `crop` varchar(10) CHARACTER SET utf8 DEFAULT NULL,
  `creator` varchar(20) CHARACTER SET utf8 DEFAULT NULL,
  `time` int(11) DEFAULT NULL,
  `display` int(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=24 ;



INSERT INTO `medias` (`id`, `name`, `creator`, `time`, `display`) VALUES
(1, 'Facebook', 'Jackie', NULL, 1),
(2, 'Google', 'Jackie', NULL, 1),
(3, 'BlueBeeSeries', 'Jackie', NULL, 1),
(4, 'Yahoo', 'Jackie', NULL, 1),
(5, 'Line', 'Jackie', NULL, 1),
(6, 'BotBonnie邦妮對話式機器人', 'Jackie', NULL, 1),
(7, '寫手', 'Jackie', NULL, 1),
(8, 'AVnight', 'jimmy', 1526277481, 1),
(9, '天天看片', 'Jackie', NULL, 1),
(10, '錢包小豬', 'Jackie', NULL, 1),
(11, '小豬出任務', 'Jackie', NULL, 1),
(12, 'OpenPoint', 'Jackie', NULL, 1),
(13, 'Popin Recommend Article ADS', 'Jackie', NULL, 1),
(14, 'TENMAX', 'Jackie', NULL, 1),
(15, 'Line代操服務費', 'Jackie', NULL, 1),
(16, 'Google代操服務費', 'Jackie', NULL, 1),
(17, 'Facebook代操服務費', 'Jackie', NULL, 1),
(18, '廣告素材製作', 'Jackie', NULL, 1),
(19, 'Adways Asiatest', 'Jackie', 1525956023, 0),
(22, 'Honeyscreen', 'Jackie', NULL, 1),
(23, '直接收入', 'Jackie', NULL, 1);