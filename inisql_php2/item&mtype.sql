
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
(3, 'CPM', 'jimmy', 1527163190, 1, '153'),
(4, 'CPI', 'jimmy', 1527163172, 1, '152'),
(6, 'CPA', 'jimmy', 1527687727, 1, '160'),
(7, 'CPE', 'jimmy', 1527163246, 1, '151'),
(8, 'CPS', 'jimmy', 1527163224, 1, '156'),
(11, 'CPT', 'jimmy', 1527163215, 1, '155'),
(13, 'CPC', 'jimmy', 1527163161, 1, '151'),
(19, '檔期', 'jimmy', 1527163233, 1, '157'),
(22, '網誌廣告', 'jimmy', 1527163275, 1, '158'),
(24, '【任務型】Line(企業贊助貼圖)', 'jimmy', 1527230833, 1, '159'),
(25, '【其他】手機簡訊', 'jimmy', 1527230833, 1, '160'),
(26, '【機制費】廣告素材製作', 'jimmy', 1527230833, 1, '161'),
(27, '寫手費', 'jimmy', 1527230833, 1, '162'),
(28, 'Facebook代操服務費', 'jimmy', 1527230833, 1, '163'),
(29, 'SHIRYOUKO STUDIO', 'jimmy', 1527230833, 1, '164'),
(30, 'HappyGo MMS', 'jimmy', 1527230833, 1, '165'),
(31, 'Youtuber', 'jimmy', 1527230833, 1, '166'),
(32, '【行動下載計費CPI】LINE(3DM)', 'jimmy', 1527230833, 1, '167'),
(33, '預約TOP10', 'jimmy', 1527230833, 1, '168'),
(34, '錢包小豬(任務型)', 'jimmy', 1527230833, 1, '169');



CREATE TABLE IF NOT EXISTS `items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `creator` varchar(20) CHARACTER SET utf8 DEFAULT NULL,
  `time` int(11) DEFAULT NULL,
  `display` int(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=70 ;



INSERT INTO `items` (`id`, `name`, `creator`, `time`, `display`) VALUES
(1, '品牌知名度', 'Jackie', NULL, 1),
(2, '觸及人數', 'Jackie', NULL, 1),
(3, '觀看影片', 'Jackie', NULL, 1),
(4, '應用程式安裝', 'Jackie', NULL, 1),
(5, '網站點擊次數', 'Jackie', NULL, 1),
(6, '應用程式互動次數', 'Jackie', NULL, 1),
(7, '開發潛在客戶', 'Jackie', NULL, 1),
(8, '粉絲專頁貼文互動', 'Jackie', NULL, 1),
(9, '粉絲專頁的讚', 'Jackie', NULL, 1),
(10, '活動回覆', 'Jackie', NULL, 1),
(11, '優惠', 'Jackie', NULL, 1),
(12, '發送訊息', 'Jackie', NULL, 1),
(13, '網站轉換次數', 'Jackie', NULL, 1),
(14, '應用程式轉換次數', 'Jackie', NULL, 1),
(15, '目錄銷售', 'Jackie', NULL, 1),
(16, '來店客流量', 'Jackie', NULL, 1),
(17, '離線轉換', 'Jackie', NULL, 1),
(18, 'GDN', 'Jackie', NULL, 1),
(19, '關鍵字', 'Jackie', NULL, 1),
(20, 'YouTube', 'Jackie', NULL, 1),
(21, 'UAC', 'Jackie', NULL, 1),
(22, '360°環景Banner', 'jimmy', 1526283500, 0),
(23, '影音廣告', 'Jackie', NULL, 1),
(24, '輪播Banner', 'Jackie', NULL, 1),
(25, 'Turning Over翻頁', 'Jackie', NULL, 1),
(26, 'Slide-in滑動', 'Jackie', NULL, 1),
(27, 'Rotation翻轉', 'Jackie', NULL, 1),
(28, '多圖輪播Banner', 'Jackie', NULL, 1),
(29, '三件式目錄型Banner', 'Jackie', NULL, 1),
(30, '客製化 API Banner', 'Jackie', NULL, 1),
(31, '刮刮樂', 'Jackie', NULL, 1),
(32, '活動倒數Banner', 'Jackie', NULL, 1),
(33, '原生廣告', 'Jackie', NULL, 1),
(34, 'Line Points Ads(CPI)', 'Jackie', NULL, 1),
(35, 'Line Points Ads(3DM)', 'Jackie', NULL, 1),
(36, 'Line Points Ads(CPE)', 'Jackie', NULL, 1),
(37, 'Line Points Ads(CPWL)', 'Jackie', NULL, 1),
(38, 'Line Points Ads(CPA)', 'Jackie', NULL, 1),
(39, 'Line Points Ads(CPV)', 'Jackie', NULL, 1),
(40, 'Line Points Ads(BSP)', 'Jackie', NULL, 1),
(41, 'Line Points Ads(FBV)', 'Jackie', NULL, 1),
(42, 'Line Points Ads(CPF)', 'jimmy', 1526365715, 1),
(43, 'Line Ads Platform(LAP)', 'Jackie', NULL, 1),
(44, 'LINE Ads Platform (First View)', 'Jackie', NULL, 1),
(45, 'Line企業贊助貼圖', 'Jackie', NULL, 1),
(46, 'LINE TODAY(原生廣告)', 'Jackie', NULL, 1),
(47, 'LINE TODAY(情報快遞)', 'Jackie', NULL, 1),
(48, 'LINE TODAY(方案)', 'Jackie', NULL, 1),
(49, 'LINE官方帳號', 'Jackie', NULL, 1),
(50, 'LINE TV', 'Jackie', NULL, 1),
(51, 'LINE Moretab Expand Ad', 'Jackie', NULL, 1),
(52, 'LINE POINT CODE', 'Jackie', NULL, 1),
(53, '平台月租費(訊息流量費)', 'Jackie', NULL, 1),
(54, '建置費', 'Jackie', NULL, 1),
(55, '程式開發費', 'Jackie', NULL, 1),
(56, '寫手', 'Jackie', NULL, 1),
(57, 'APP下載', 'Jackie', NULL, 1),
(58, '小豬福利社(小豬啦啦隊)', 'Jackie', NULL, 1),
(59, '小豬福利社(口碑培養皿)', 'Jackie', NULL, 1),
(60, '小豬福利社(小豬特派員)', 'Jackie', NULL, 1),
(61, 'VIDEO ADS', 'Jackie', NULL, 1),
(62, 'Recommend Article ADS', 'Jackie', NULL, 1),
(63, 'unity', 'Jackie', NULL, 1),
(64, 'AppLovin', 'jimmy', 1526365704, 0),
(65, 'Unicorn', 'Jackie', NULL, 1),
(66, '差價', 'Jackie', NULL, 1),
(67, '翻譯製作', 'Jackie', NULL, 1),
(68, '內部人員製作', 'Jackie', NULL, 1),
(69, '日本EC', 'Jackie', NULL, 1);

