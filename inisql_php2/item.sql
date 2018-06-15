
drop table if exists `mtype`;
drop table if exists `items`;

CREATE TABLE IF NOT EXISTS `mtype` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) CHARACTER SET utf8 DEFAULT NULL,
  `creator` varchar(20) CHARACTER SET utf8 DEFAULT NULL,
  `time` int(11) DEFAULT NULL,
  `display` int(1) DEFAULT NULL,
  `dashboard` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;


INSERT INTO `mtype` (`id`, `name`, `creator`, `time`, `display`, `dashboard`) VALUES
(1, 'CPV', 'jimmy', 1527163205, 1, '154'),
(2, 'CPM', 'jimmy', 1527163190, 1, '153'),
(3, 'CPI', 'jimmy', 1527163172, 1, '152'),
(4, 'CPA', 'jimmy', 1527687727, 1, '170'),
(5, 'CPE', 'jimmy', 1527163246, 1, '171'),
(6, 'CPS', 'jimmy', 1527163224, 1, '156'),
(7, 'CPT', 'jimmy', 1527163215, 1, '155'),
(8, 'CPC', 'jimmy', 1527163161, 1, '172'),
(9, '檔期', 'jimmy', 1527163233, 1, '157'),
(10, '網誌廣告', 'jimmy', 1527163275, 1, '158'),
(11, '【任務型】Line(企業贊助貼圖)', 'jimmy', 1527230833, 1, '159'),
(12, '【其他】手機簡訊', 'jimmy', 1527230833, 1, '160'),
(13, '【機制費】廣告素材製作', 'jimmy', 1527230833, 1, '161'),
(14, '寫手費', 'jimmy', 1527230833, 1, '162'),
(15, 'Facebook代操服務費', 'jimmy', 1527230833, 1, '163'),
(16, 'SHIRYOUKO STUDIO', 'jimmy', 1527230833, 1, '164'),
(17, 'HappyGo MMS', 'jimmy', 1527230833, 1, '165'),
(18, 'Youtuber', 'jimmy', 1527230833, 1, '166'),
(19, '【行動下載計費CPI】LINE(3DM)', 'jimmy', 1527230833, 1, '167'),
(20, '預約TOP10', 'jimmy', 1527230833, 1, '168'),
(21, '錢包小豬(任務型)', 'jimmy', 1527230833, 1, '169');



CREATE TABLE IF NOT EXISTS `items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `creator` varchar(20) CHARACTER SET utf8 DEFAULT NULL,
  `time` int(11) DEFAULT NULL,
  `display` int(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;


INSERT INTO `items` (`id`, `name`, `time`, `display`) VALUES (1, '品牌知名度', NULL, 1);
INSERT INTO `items` (`id`, `name`, `time`, `display`) VALUES (2, '觸及人數', NULL, 1);
INSERT INTO `items` (`id`, `name`, `time`, `display`) VALUES (3, '觀看影片', NULL, 1);
INSERT INTO `items` (`id`, `name`, `time`, `display`) VALUES (4, '應用程式安裝', NULL, 1);
INSERT INTO `items` (`id`, `name`, `time`, `display`) VALUES (5, '網站點擊次數', NULL, 1);
INSERT INTO `items` (`id`, `name`, `time`, `display`) VALUES (6, '應用程式互動次數', NULL, 1);
INSERT INTO `items` (`id`, `name`, `time`, `display`) VALUES (7, '開發潛在客戶', NULL, 1);
INSERT INTO `items` (`id`, `name`, `time`, `display`) VALUES (8, '粉絲專頁貼文互動', NULL, 1);
INSERT INTO `items` (`id`, `name`, `time`, `display`) VALUES (9, '粉絲專頁的讚', NULL, 1);
INSERT INTO `items` (`id`, `name`, `time`, `display`) VALUES (10, '活動回覆', NULL, 1);
INSERT INTO `items` (`id`, `name`, `time`, `display`) VALUES (11, '優惠', NULL, 1);
INSERT INTO `items` (`id`, `name`, `time`, `display`) VALUES (12, '發送訊息', NULL, 1);
INSERT INTO `items` (`id`, `name`, `time`, `display`) VALUES (13, '網站轉換次數', NULL, 1);
INSERT INTO `items` (`id`, `name`, `time`, `display`) VALUES (14, '應用程式轉換次數', NULL, 1);
INSERT INTO `items` (`id`, `name`, `time`, `display`) VALUES (15, '目錄銷售', NULL, 1);
INSERT INTO `items` (`id`, `name`, `time`, `display`) VALUES (16, '來店客流量', NULL, 1);
INSERT INTO `items` (`id`, `name`, `time`, `display`) VALUES (17, '離線轉換', NULL, 1);
INSERT INTO `items` (`id`, `name`, `time`, `display`) VALUES (18, '粉絲專頁的讚(讚數)', NULL, 1);
INSERT INTO `items` (`id`, `name`, `time`, `display`) VALUES (19, 'GDN', NULL, 1);
INSERT INTO `items` (`id`, `name`, `time`, `display`) VALUES (20, 'google關鍵字', NULL, 1);
INSERT INTO `items` (`id`, `name`, `time`, `display`) VALUES (21, 'YouTube', NULL, 1);
INSERT INTO `items` (`id`, `name`, `time`, `display`) VALUES (22, 'UAC', NULL, 1);
INSERT INTO `items` (`id`, `name`, `time`, `display`) VALUES (23, '360°環景Banner', NULL, 1);
INSERT INTO `items` (`id`, `name`, `time`, `display`) VALUES (24, '影音廣告', NULL, 1);
INSERT INTO `items` (`id`, `name`, `time`, `display`) VALUES (25, '輪播Banner：Turning Over 翻頁 Slide-in 滑動 Rotation翻轉', NULL, 1);
INSERT INTO `items` (`id`, `name`, `time`, `display`) VALUES (26, '多圖輪播Banner', NULL, 1);
INSERT INTO `items` (`id`, `name`, `time`, `display`) VALUES (27, '三件式目錄型Banner', NULL, 1);
INSERT INTO `items` (`id`, `name`, `time`, `display`) VALUES (28, '客製化 API Banner', NULL, 1);
INSERT INTO `items` (`id`, `name`, `time`, `display`) VALUES (29, '刮刮樂', NULL, 1);
INSERT INTO `items` (`id`, `name`, `time`, `display`) VALUES (30, '活動倒數Banner', NULL, 1);
INSERT INTO `items` (`id`, `name`, `time`, `display`) VALUES (31, 'yahoo關鍵字', NULL, 1);
INSERT INTO `items` (`id`, `name`, `time`, `display`) VALUES (32, 'yahoo原生廣告', NULL, 1);
INSERT INTO `items` (`id`, `name`, `time`, `display`) VALUES (33, 'LINE Points Ads(CPI)', NULL, 1);
INSERT INTO `items` (`id`, `name`, `time`, `display`) VALUES (34, 'LINE Points Ads(3DM)', NULL, 1);
INSERT INTO `items` (`id`, `name`, `time`, `display`) VALUES (35, 'LINE Points Ads(CPE)', NULL, 1);
INSERT INTO `items` (`id`, `name`, `time`, `display`) VALUES (36, 'LINE Points Ads(CPWL)', NULL, 1);
INSERT INTO `items` (`id`, `name`, `time`, `display`) VALUES (37, 'LINE Points Ads (CPA)', NULL, 1);
INSERT INTO `items` (`id`, `name`, `time`, `display`) VALUES (38, 'LINE Points Ads (CPV)', NULL, 1);
INSERT INTO `items` (`id`, `name`, `time`, `display`) VALUES (39, 'LINE Points Ads (BSP)', NULL, 1);
INSERT INTO `items` (`id`, `name`, `time`, `display`) VALUES (40, 'LINE Points Ads (FBV)', NULL, 1);
INSERT INTO `items` (`id`, `name`, `time`, `display`) VALUES (41, 'LINE Points Ads (CPF)', NULL, 1);
INSERT INTO `items` (`id`, `name`, `time`, `display`) VALUES (42, 'LINE Ads Platform (LAP)', NULL, 1);
INSERT INTO `items` (`id`, `name`, `time`, `display`) VALUES (43, 'LINE Ads Platform (First View)', NULL, 1);
INSERT INTO `items` (`id`, `name`, `time`, `display`) VALUES (44, 'LINE企業贊助貼圖', NULL, 1);
INSERT INTO `items` (`id`, `name`, `time`, `display`) VALUES (45, 'LINE TODAY (原生廣告)', NULL, 1);
INSERT INTO `items` (`id`, `name`, `time`, `display`) VALUES (46, 'LINE TODAY (情報快遞)', NULL, 1);
INSERT INTO `items` (`id`, `name`, `time`, `display`) VALUES (47, 'LINE TODAY (方案)', NULL, 1);
INSERT INTO `items` (`id`, `name`, `time`, `display`) VALUES (48, 'LINE官方帳號', NULL, 1);
INSERT INTO `items` (`id`, `name`, `time`, `display`) VALUES (49, 'LINE TV', NULL, 1);
INSERT INTO `items` (`id`, `name`, `time`, `display`) VALUES (50, 'LINE Moretab Expand Ad', NULL, 1);
INSERT INTO `items` (`id`, `name`, `time`, `display`) VALUES (51, 'LINE POINT CODE', NULL, 1);
INSERT INTO `items` (`id`, `name`, `time`, `display`) VALUES (52, '平台月租費(訊息流量費)', NULL, 1);
INSERT INTO `items` (`id`, `name`, `time`, `display`) VALUES (53, '建置費', NULL, 1);
INSERT INTO `items` (`id`, `name`, `time`, `display`) VALUES (54, '程式開發費', NULL, 1);
INSERT INTO `items` (`id`, `name`, `time`, `display`) VALUES (55, '寫手', NULL, 1);
INSERT INTO `items` (`id`, `name`, `time`, `display`) VALUES (56, 'APP下載', NULL, 1);
INSERT INTO `items` (`id`, `name`, `time`, `display`) VALUES (57, '小豬福利社(小豬啦啦隊)', NULL, 1);
INSERT INTO `items` (`id`, `name`, `time`, `display`) VALUES (58, '小豬福利社(口碑培養皿)', NULL, 1);
INSERT INTO `items` (`id`, `name`, `time`, `display`) VALUES (59, '小豬福利社(小豬特派員)', NULL, 1);
INSERT INTO `items` (`id`, `name`, `time`, `display`) VALUES (60, 'VIDEO ADS', NULL, 1);
INSERT INTO `items` (`id`, `name`, `time`, `display`) VALUES (61, 'Recommend Article ADS', NULL, 1);
INSERT INTO `items` (`id`, `name`, `time`, `display`) VALUES (62, '原生廣告', NULL, 1);
INSERT INTO `items` (`id`, `name`, `time`, `display`) VALUES (63, 'LINE代操服務費', NULL, 1);
INSERT INTO `items` (`id`, `name`, `time`, `display`) VALUES (64, 'Google代操服務費', NULL, 1);
INSERT INTO `items` (`id`, `name`, `time`, `display`) VALUES (65, 'Facebook代操服務費', NULL, 1);
INSERT INTO `items` (`id`, `name`, `time`, `display`) VALUES (66, '廣告素材製作', NULL, 1);
INSERT INTO `items` (`id`, `name`, `time`, `display`) VALUES (67, 'unity', NULL, 1);
INSERT INTO `items` (`id`, `name`, `time`, `display`) VALUES (68, 'AppLovin', NULL, 1);
INSERT INTO `items` (`id`, `name`, `time`, `display`) VALUES (69, 'Unicorn', NULL, 1);
INSERT INTO `items` (`id`, `name`, `time`, `display`) VALUES (70, '差價', NULL, 1);
INSERT INTO `items` (`id`, `name`, `time`, `display`) VALUES (71, '翻譯製作', NULL, 1);
INSERT INTO `items` (`id`, `name`, `time`, `display`) VALUES (72, '內部人員製作', NULL, 1);
INSERT INTO `items` (`id`, `name`, `time`, `display`) VALUES (73, '日本EC', NULL, 1);
INSERT INTO `items` (`id`, `name`, `time`, `display`) VALUES (74, '分潤收入', NULL, 1);
INSERT INTO `items` (`id`, `name`, `time`, `display`) VALUES (75, 'Oct-pass EC', NULL, 1);
INSERT INTO `items` (`id`, `name`, `time`, `display`) VALUES (76, 'Octpass', NULL, 1);
INSERT INTO `items` (`id`, `name`, `time`, `display`) VALUES (77, '貼文分享', NULL, 1);
INSERT INTO `items` (`id`, `name`, `time`, `display`) VALUES (78, '心得撰寫', NULL, 1);
INSERT INTO `items` (`id`, `name`, `time`, `display`) VALUES (79, '成果網CPS', NULL, 1);
INSERT INTO `items` (`id`, `name`, `time`, `display`) VALUES (80, '代墊服務費', NULL, 1);
INSERT INTO `items` (`id`, `name`, `time`, `display`) VALUES (81, '戶外多媒體廣告', NULL, 1);
INSERT INTO `items` (`id`, `name`, `time`, `display`) VALUES (82, '實況主', NULL, 1);
INSERT INTO `items` (`id`, `name`, `time`, `display`) VALUES (83, '五星評論', NULL, 1);
INSERT INTO `items` (`id`, `name`, `time`, `display`) VALUES (84, '下載', NULL, 1);
INSERT INTO `items` (`id`, `name`, `time`, `display`) VALUES (85, '手機簡訊', NULL, 1);
INSERT INTO `items` (`id`, `name`, `time`, `display`) VALUES (86, 'Party使用費', NULL, 1);
INSERT INTO `items` (`id`, `name`, `time`, `display`) VALUES (87, '網紅', NULL, 1);
INSERT INTO `items` (`id`, `name`, `time`, `display`) VALUES (88, '聯播網', NULL, 1);
INSERT INTO `items` (`id`, `name`, `time`, `display`) VALUES (89, '預約登錄', NULL, 1);
INSERT INTO `items` (`id`, `name`, `time`, `display`) VALUES (90, '影片觀看', NULL, 1);
INSERT INTO `items` (`id`, `name`, `time`, `display`) VALUES (91, 'MMS', NULL, 1);
INSERT INTO `items` (`id`, `name`, `time`, `display`) VALUES (92, 'Mobile Site', NULL, 1);
INSERT INTO `items` (`id`, `name`, `time`, `display`) VALUES (93, 'APP', NULL, 1);
INSERT INTO `items` (`id`, `name`, `time`, `display`) VALUES (94, '廣編稿', NULL, 1);