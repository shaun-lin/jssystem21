
drop table if exists `medias`;


CREATE TABLE IF NOT EXISTS `medias` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `crop` varchar(10) CHARACTER SET utf8 DEFAULT NULL,
  `creator` varchar(20) CHARACTER SET utf8 DEFAULT NULL,
  `time` int(11) DEFAULT NULL,
  `display` int(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;


INSERT INTO `medias` (`id`, `name`, `time`, `display`) VALUES (1, 'Facebook', NULL, 1);
INSERT INTO `medias` (`id`, `name`, `time`, `display`) VALUES (2, 'Google', NULL, 1);
INSERT INTO `medias` (`id`, `name`, `time`, `display`) VALUES (3, 'BlueBeeSeries', NULL, 1);
INSERT INTO `medias` (`id`, `name`, `time`, `display`) VALUES (4, 'Yahoo', NULL, 1);
INSERT INTO `medias` (`id`, `name`, `time`, `display`) VALUES (5, 'LINE', NULL, 1);
INSERT INTO `medias` (`id`, `name`, `time`, `display`) VALUES (6, 'BotBonnie 邦妮對話式機器人', NULL, 1);
INSERT INTO `medias` (`id`, `name`, `time`, `display`) VALUES (7, '寫手', NULL, 1);
INSERT INTO `medias` (`id`, `name`, `time`, `display`) VALUES (8, 'AVnight', NULL, 1);
INSERT INTO `medias` (`id`, `name`, `time`, `display`) VALUES (9, '天天看片', NULL, 1);
INSERT INTO `medias` (`id`, `name`, `time`, `display`) VALUES (10, '錢包小豬', NULL, 1);
INSERT INTO `medias` (`id`, `name`, `time`, `display`) VALUES (11, '小豬出任務', NULL, 1);
INSERT INTO `medias` (`id`, `name`, `time`, `display`) VALUES (12, 'OPENPOINT', NULL, 1);
INSERT INTO `medias` (`id`, `name`, `time`, `display`) VALUES (13, 'Popin Recommend Article ADS', NULL, 1);
INSERT INTO `medias` (`id`, `name`, `time`, `display`) VALUES (14, 'TENMAX', NULL, 1);
INSERT INTO `medias` (`id`, `name`, `time`, `display`) VALUES (15, 'LINE代操服務費', NULL, 1);
INSERT INTO `medias` (`id`, `name`, `time`, `display`) VALUES (16, 'Google代操服務費', NULL, 1);
INSERT INTO `medias` (`id`, `name`, `time`, `display`) VALUES (17, 'Facebook代操服務費', NULL, 1);
INSERT INTO `medias` (`id`, `name`, `time`, `display`) VALUES (18, '廣告素材製作', NULL, 1);
INSERT INTO `medias` (`id`, `name`, `time`, `display`) VALUES (19, 'Adways connect', NULL, 1);
INSERT INTO `medias` (`id`, `name`, `time`, `display`) VALUES (20, 'Honeyscreen', NULL, 1);
INSERT INTO `medias` (`id`, `name`, `time`, `display`) VALUES (21, '直接收入', NULL, 1);
INSERT INTO `medias` (`id`, `name`, `time`, `display`) VALUES (22, 'Octpass', NULL, 1);
INSERT INTO `medias` (`id`, `name`, `time`, `display`) VALUES (23, '113助手', NULL, 1);
INSERT INTO `medias` (`id`, `name`, `time`, `display`) VALUES (24, 'MyCard', NULL, 1);
INSERT INTO `medias` (`id`, `name`, `time`, `display`) VALUES (25, '點書PointBook', NULL, 1);
INSERT INTO `medias` (`id`, `name`, `time`, `display`) VALUES (26, 'CHANET服務費', NULL, 1);
INSERT INTO `medias` (`id`, `name`, `time`, `display`) VALUES (27, '樂一番代墊服務費', NULL, 1);
INSERT INTO `medias` (`id`, `name`, `time`, `display`) VALUES (28, '詩涼子SHIRYOUKO STUDIO', NULL, 1);
INSERT INTO `medias` (`id`, `name`, `time`, `display`) VALUES (29, '五星評論', NULL, 1);
INSERT INTO `medias` (`id`, `name`, `time`, `display`) VALUES (30, '巴哈姆特', NULL, 1);
INSERT INTO `medias` (`id`, `name`, `time`, `display`) VALUES (31, '雪豹科技', NULL, 1);
INSERT INTO `medias` (`id`, `name`, `time`, `display`) VALUES (32, '手機簡訊', NULL, 1);
INSERT INTO `medias` (`id`, `name`, `time`, `display`) VALUES (33, 'Partytrack', NULL, 1);
INSERT INTO `medias` (`id`, `name`, `time`, `display`) VALUES (34, 'CAULY TAIWAN', NULL, 1);
INSERT INTO `medias` (`id`, `name`, `time`, `display`) VALUES (35, 'HappyGo', NULL, 1);
INSERT INTO `medias` (`id`, `name`, `time`, `display`) VALUES (36, '蘋果日報', NULL, 1);
INSERT INTO `medias` (`id`, `name`, `time`, `display`) VALUES (37, '東森新聞雲', NULL, 1);
INSERT INTO `medias` (`id`, `name`, `time`, `display`) VALUES (38, 'ELLE', NULL, 1);
INSERT INTO `medias` (`id`, `name`, `time`, `display`) VALUES (39, 'NOWnews今日新聞', NULL, 1);
INSERT INTO `medias` (`id`, `name`, `time`, `display`) VALUES (40, '妞新聞', NULL, 1);
INSERT INTO `medias` (`id`, `name`, `time`, `display`) VALUES (41, '聯合報UDN', NULL, 1);
INSERT INTO `medias` (`id`, `name`, `time`, `display`) VALUES (42, '自由時報', NULL, 1);
INSERT INTO `medias` (`id`, `name`, `time`, `display`) VALUES (43, '商業周刊', NULL, 1);
INSERT INTO `medias` (`id`, `name`, `time`, `display`) VALUES (44, '愛奇藝PPS', NULL, 1);
INSERT INTO `medias` (`id`, `name`, `time`, `display`) VALUES (45, '三立新聞網', NULL, 1);