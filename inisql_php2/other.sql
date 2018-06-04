

drop table if exists `cp_detail`;
drop table if exists `lookup`;
drop table if exists `total_seq`;
drop table if exists `media`;
drop table if exists `media_accounting`;
drop table if exists `media_seq`;



CREATE TABLE IF NOT EXISTS `cp_detail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cp_id` int(11) NOT NULL,
  `media_id` int(11) NOT NULL,
  `comp_id` int(11) DEFAULT NULL,
  `item_id` int(11) NOT NULL,
  `mtype_name` varchar(50) DEFAULT NULL,
  `mtype_number` int(11) NOT NULL DEFAULT '0',
  `mtype_id` int(11) NOT NULL,
  `cue` int(1) NOT NULL,
  `item_seq` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `jpc_seq` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `campaign_id` (`cp_id`),
  KEY `status` (`mtype_number`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;



CREATE TABLE IF NOT EXISTS `lookup` (
  `lookup_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '流水號',
  `lookup_type` varchar(100) NOT NULL COMMENT '分類',
  `item` varchar(200) NOT NULL COMMENT '畫面顯示',
  `value` varchar(200) NOT NULL COMMENT '實際值',
  `sort` int(3) DEFAULT NULL COMMENT '排序由小到大',
  PRIMARY KEY (`lookup_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='下拉選單參考表' AUTO_INCREMENT=16 ;



INSERT INTO `lookup` (`lookup_id`, `lookup_type`, `item`, `value`, `sort`) VALUES
(2, 'currency', '台幣', 'TWD', 1),
(3, 'currency', '美元', 'USD', 2),
(4, 'currency', '日幣', 'JPY', 3),
(5, 'currency', '港幣', 'HKD', 4),
(6, 'currency', '歐元', 'EUR', 5),
(7, 'currency', '人民幣', 'CNY', 6),
(8, 'CPA', '名單數', '1', 1),
(9, 'CPA', '讚數', '2', 2),
(10, 'CPA', '回覆次數', '3', 3),
(11, 'CPA', '領取次數', '4', 4),
(12, 'CPA', '轉換次數', '5', 5),
(13, 'CPA', '加入官方帳號', '6', 6),
(14, 'CPA', '評論數', '7', 7),
(15, 'CPA', '其他', '8', 8);



CREATE TABLE IF NOT EXISTS `total_seq` (
  `seq_id` int(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



INSERT INTO `total_seq` (`seq_id`) VALUES
(5001);



CREATE TABLE IF NOT EXISTS `media` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(40) DEFAULT NULL,
  `company` text,
  `costper` varchar(20) DEFAULT NULL,
  `type` int(11) DEFAULT NULL COMMENT '0 CPM , 1 CPC  , 2 CPI , 3 網站廣告 , 4 其他 , 5 廣告素材 , 6 寫手費 , 7 差價 , 8 CPA , 9 CPV , 10 CPT,20 海外CPM,21 海外CPC , 22 海外CPI, 29 海外CPV',
  `type2` varchar(40) DEFAULT NULL,
  `typename` varchar(30) DEFAULT NULL,
  `sortid` int(11) NOT NULL,
  `sortid2` int(11) NOT NULL,
  `profit` int(11) NOT NULL DEFAULT '0' COMMENT '利潤在此設定',
  `display` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `display` (`display`),
  KEY `type` (`type`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=172 ;



  INSERT INTO `media` (`id`, `name`, `company`, `costper`, `type`, `type2`, `typename`, `sortid`, `sortid2`, `profit`, `display`) VALUES
(0, '150', NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 1),
(1, 'ADMOB', NULL, 'CPC', 1, 'SP広告売上', '行動廣告CPC', 1, 17, 20, 1),
(2, 'INMOBI', NULL, 'CPC', 1, 'SP広告売上', '行動廣告CPC', 2, 2, 0, 1),
(3, 'ADLOCUS', NULL, 'CPC', 1, 'SP広告売上', '行動廣告CPC', 3, 3, 0, 1),
(4, 'FACEBOOK(官方)', NULL, 'CPC', 1, 'PC広告売上', '行動廣告CPC', 4, 8, 20, 1),
(5, 'Chartboost', NULL, 'CPI', 2, 'SP広告売上', '行動下載計費CPI', 5, 14, 0, 1),
(6, 'Airpush', NULL, 'CPC', 1, 'SP広告売上', '行動廣告CPC', 6, 4, 20, 1),
(7, '好a果仔隊', NULL, 'CPI', 2, 'SP広告売上', '行動下載計費CPI', 53, 53, 0, 1),
(8, 'Facebook Install ads(官方)', NULL, 'CPI', 2, 'SP広告売上', '行動下載計費CPI', 7, 9, 20, 1),
(9, 'Appdriver', NULL, 'CPI', 2, 'SP APP売上', '行動下載計費CPI', 9, 7, 0, 1),
(10, '愛拿鐵', NULL, 'CPI', 2, 'SP広告売上', '行動下載計費CPI', 10, 12, 10, 1),
(11, '9898', NULL, 'CPI', 2, 'SP広告売上', '行動下載計費CPI', 11, 13, 20, 1),
(12, 'Tapjoy', NULL, 'CPI', 2, 'SP広告売上', '行動下載計費CPI', 12, 6, 20, 1),
(13, 'Babyhome', NULL, 'CPD', 3, 'PC広告売上', '網站廣告', 14, 15, 0, 1),
(14, 'APP01(遊戲)', NULL, 'CPD', 3, 'PC広告売上', '網站廣告', 16, 1, 0, 1),
(15, 'HappyGo', NULL, 'CPD', 3, 'PC広告売上', '網站廣告', 17, 2, 0, 1),
(16, 'Babyhome(置頂)', NULL, 'CPD', 3, 'PC広告売上', '網站廣告', 15, 16, 0, 1),
(17, 'offerMe', NULL, 'CPA', 4, 'PC広告売上', 'PC成效計費CPA', 18, 5, 0, 1),
(18, '廣告素材製作', NULL, '機制費', 5, 'その他売上', '機制費', 19, 19, 0, 1),
(19, '寫手費', NULL, '寫手費', 6, 'PC広告売上', '寫手費', 20, 20, 0, 1),
(20, '差價', NULL, '差價', 7, 'その他売上', '差價', 29, 21, 0, 1),
(21, 'LINE(Pick Up)', NULL, 'CPI', 2, 'SP広告売上', '行動下載計費CPI', 13, 18, 0, 1),
(22, 'APP01(非遊戲)', NULL, 'CPD', 3, 'PC広告売上', '網站廣告', 16, 1, 0, 1),
(23, 'FACEBOOK(信用卡)', NULL, 'CPC', 1, 'PC広告売上', '行動廣告CPC', 5, 8, 20, 1),
(24, 'FACEBOOK(COMHERE)', NULL, 'CPC', 1, 'PC広告売上', '行動廣告CPC', 5, 8, 20, 1),
(25, 'FACEBOOK(卡卡飛)', NULL, 'CPC', 1, 'PC広告売上', '行動廣告CPC', 5, 8, 20, 1),
(26, '好康巴士', NULL, 'CPA', 4, 'PC広告売上', 'PC成效計費CPA', 18, 5, 20, 1),
(27, 'Appier', NULL, 'CPC', 1, 'SP広告売上', '行動廣告CPC', 27, 17, 10, 1),
(28, 'Yahoo', NULL, 'CPM', 0, 'PC広告売上', 'CPM', 19, 5, 20, 1),
(29, 'Monipla', NULL, 'CPA', 8, 'SP広告売上', 'PC成效計費CPA', 18, 5, 61, 1),
(30, 'MYCARD', NULL, 'CPI', 2, 'SP広告売上', '行動下載計費CPI', 30, 7, 0, 1),
(31, 'Yahoo關鍵字', NULL, 'CPC', 1, 'PC広告売上', '行動廣告CPC', 31, 31, 20, 1),
(32, 'Airpush(CPM)', NULL, 'CPM', 0, 'SP広告売上', 'CPM', 6, 4, 20, 1),
(33, 'ClickForce', NULL, 'CPC', 4, 'PC広告売上', 'PC CPC', 32, 32, 10, 1),
(34, 'Facebook Install ads(信用卡)', NULL, 'CPI', 2, 'SP広告売上', '行動下載計費CPI', 8, 9, 20, 1),
(35, '愛免費', NULL, 'CPI', 2, 'SP広告売上', '行動下載計費CPI', 35, 35, 20, 1),
(36, 'LINE(ICOIN)', NULL, 'CPI', 2, 'SP広告売上', '行動下載計費CPI', 13, 18, 0, 0),
(37, 'LINE(Free Coins Video)', NULL, 'CPV', 9, 'SP広告売上', 'CPV', 37, 37, 0, 1),
(38, '113助手', NULL, 'CPI', 2, 'SP広告売上', '行動下載計費CPI', 38, 38, 0, 1),
(39, 'offerme(CPI)', NULL, 'CPI', 2, 'SP広告売上', '行動下載計費CPI', 39, 39, 0, 1),
(40, 'Inmobi(CPM)', NULL, 'CPM', 0, 'SP広告売上', 'CPM', 40, 40, 20, 1),
(41, 'Inmobi(CPV)', NULL, 'CPV', 9, 'SP広告売上', 'CPV', 41, 41, 0, 1),
(42, 'Appia', NULL, 'CPI', 2, 'SP広告売上', '行動下載計費CPI', 42, 42, 20, 1),
(43, 'Ripple', NULL, 'CPI', 2, 'SP広告売上', '行動下載計費CPI', 43, 43, 20, 1),
(44, 'Leadbolt', NULL, 'CPI', 2, 'SP広告売上', '行動下載計費CPI', 44, 44, 20, 1),
(45, 'Airpush(CPV)', NULL, 'CPV', 9, 'SP広告売上', 'CPV', 45, 45, 20, 1),
(46, 'Google關鍵字', NULL, 'CPC', 1, 'PC広告売上', '行動廣告CPC', 46, 46, 20, 1),
(47, '手機簡訊', NULL, '手機簡訊', 4, 'PC広告売上', '手機簡訊', 47, 47, 0, 1),
(48, 'YOUTUBE', NULL, 'CPC', 4, 'PC広告売上', 'PC CPC', 48, 48, 15, 1),
(49, 'HappyGo MMS', NULL, 'HappyGo MMS', 4, 'PC広告売上', 'HappyGo MMS', 49, 49, 0, 1),
(50, 'Network全網聯播-國眾(CPC)', NULL, 'CPC', 1, 'SP広告売上', '行動廣告CPC', 50, 50, 0, 1),
(51, 'Network全網聯播-國眾(CPV)', NULL, 'CPV', 9, 'SP広告売上', 'CPV', 51, 51, 0, 1),
(52, '直接收入', NULL, '差價', 4, 'その他売上', '差價', 52, 52, 0, 1),
(53, 'APP01', NULL, 'CPI', 2, 'SP広告売上', '行動下載計費CPI', 6, 11, 0, 1),
(54, 'LINE(Rank Boost)', NULL, 'CPI', 2, 'SP広告売上', '行動下載計費CPI', 13, 18, 0, 0),
(55, '預約TOP10', NULL, 'CPR', 4, 'SP APP売上', '預約計費CPR', 1, 1, 0, 1),
(56, 'Adsame', NULL, 'CPC', 4, 'PC広告売上', 'PC CPC', 56, 56, 0, 1),
(57, 'KUAD', NULL, 'CPC', 1, 'SP広告売上', '行動廣告CPC', 57, 57, 0, 1),
(58, '電視連續劇', NULL, 'CPM', 0, 'SP広告売上', 'CPM', 58, 58, 0, 1),
(59, 'Network全網聯播-亞普達(CPC)', NULL, 'CPC', 1, 'SP広告売上', '行動廣告CPC', 59, 59, 0, 1),
(60, 'ADPartner', NULL, 'CPC', 4, 'PC広告売上', 'PC CPC', 60, 60, 0, 1),
(61, '風行網', NULL, 'CPV', 9, 'SP広告売上', 'CPV', 61, 61, 20, 1),
(62, 'VPON', NULL, 'CPC', 1, 'SP広告売上', '行動廣告CPC', 62, 62, 0, 1),
(63, 'Mobiforce', NULL, 'CPC', 1, 'SP広告売上', '行動廣告CPC', 63, 63, 0, 1),
(64, '風行網【CPM】', NULL, 'CPM', 0, 'SP広告売上', 'CPM', 64, 64, 0, 1),
(65, 'URAD', NULL, 'CPI', 2, 'SP広告売上', '行動下載計費CPI', 65, 65, 20, 1),
(66, 'URAD【CPA】', NULL, 'CPA', 4, 'PC広告売上', 'PC成效計費CPA', 66, 66, 20, 1),
(67, '熊賺錢', NULL, 'CPI', 2, 'SP広告売上', '行動下載計費CPI', 67, 67, 25, 1),
(68, 'MircoAd', NULL, 'CPM', 0, 'SP広告売上', 'CPM', 68, 68, 20, 1),
(69, 'Yahoo聯播網全站(CPCV)', NULL, 'CPV', 9, 'SP広告売上', 'CPCV', 69, 69, 0, 1),
(70, 'Network全網聯播-生洋(CPC)', NULL, 'CPC', 1, 'SP広告売上', '行動廣告CPC', 70, 70, 0, 1),
(71, 'Youtuber', NULL, 'Youtuber費', 6, 'PC広告売上', 'Youtuber費', 71, 71, 0, 1),
(72, 'Honeyscreen(CPM)', NULL, 'CPM', 0, 'SP広告売上', 'CPM', 72, 72, 0, 1),
(73, 'Honeyscree(CPC)', NULL, 'CPC', 1, 'SP広告売上', '行動廣告CPC', 73, 73, 0, 1),
(74, 'Honeyscreen(任務型)', NULL, 'CPI', 4, 'SP広告売上', '任務型', 74, 74, 0, 1),
(75, 'Yahoo 廣告', NULL, 'CPD', 3, 'PC広告売上', '網站廣告', 75, 75, 10, 1),
(76, '蘋果日報APP', NULL, 'CPM', 0, 'PC広告売上', 'CPM', 76, 76, 10, 1),
(77, '蘋果日報WEB', NULL, 'CPD', 3, 'PC広告売上', '網站廣告', 77, 77, 10, 1),
(78, '蘋果日報手機網站', NULL, 'CPM', 0, 'PC広告売上', 'CPM', 78, 78, 10, 1),
(79, '直接收入', NULL, '翻譯製作', 4, 'その他売上', '翻譯製作', 53, 53, 0, 1),
(80, 'Network全網聯播-七大洋(CPC)', NULL, 'CPC', 1, 'SP広告売上', '行動廣告CPC', 51, 51, 0, 1),
(81, 'SHIRYOUKO STUDIO', NULL, '戶外多媒體廣告', 4, 'SP広告売上', '戶外多媒體廣告', 81, 81, 0, 1),
(82, '直接收入', NULL, '內部人員製作', 4, 'その他売上', '內部人員製作', 54, 54, 0, 1),
(83, 'SHIRYOUKO STUDIO', NULL, '實況主', 4, 'SP広告売上', '實況主', 83, 83, 0, 1),
(84, 'Unicorn', NULL, 'CPI', 2, 'SP広告売上', '行動下載計費CPI', 84, 84, 0, 1),
(85, '巴哈姆特', NULL, 'CPD', 3, 'PC広告売上', '網站廣告', 85, 85, 0, 1),
(86, 'Whoscall(CPM)', NULL, 'CPM', 0, 'SP広告売上', 'CPM', 86, 86, 0, 1),
(87, '雪豹科技(CPM)', NULL, 'CPM', 0, 'SP広告売上', 'CPM', 87, 87, 0, 1),
(88, '雪豹科技', NULL, 'CPC', 1, 'SP広告売上', '行動廣告CPC', 88, 88, 0, 1),
(89, '媽媽經', NULL, 'CPD', 3, 'PC広告売上', '網站廣告', 89, 89, 0, 1),
(90, 'LINE TV', NULL, 'CPM', 0, 'PC広告売上', 'CPM', 90, 90, 0, 1),
(91, 'ADPLAY', NULL, 'CPT', 10, 'SP APP売上', 'CPT', 91, 91, 0, 1),
(92, 'Whoscall(CPI)', NULL, 'CPI', 2, 'SP APP売上', '行動下載計費CPI', 92, 92, 0, 1),
(93, 'Network全網聯播-鴻星數位(CPV)', NULL, 'CPV', 9, 'SP広告売上', 'CPV', 93, 93, 0, 1),
(94, '額外媒體', NULL, '額外媒體', 11, 'その他売上', '額外媒體', 94, 94, 0, 1),
(95, 'LINE TV', NULL, 'CPM', 20, 'PC広告売上', 'CPM', 95, 95, 0, 0),
(96, 'LINE(Free Coins Video)', NULL, 'CPV', 29, 'SP広告売上', 'CPV', 96, 96, 0, 0),
(97, 'LINE(Pick Up)', NULL, 'CPI', 22, 'SP広告売上', '行動下載計費CPI', 97, 97, 0, 0),
(98, 'LINE(ICOIN)', NULL, 'CPI', 22, 'SP広告売上', '行動下載計費CPI', 98, 98, 0, 0),
(99, 'LINE(Rank Boost)', NULL, 'CPI', 22, 'SP広告売上', '行動下載計費CPI', 99, 99, 0, 0),
(100, 'FACEBOOK(官方)', NULL, 'CPC', 21, 'PC広告売上', '行動廣告CPC', 100, 100, 20, 0),
(101, 'Facebook Install ads(官方)', NULL, 'CPI', 22, 'SP広告売上', '行動下載計費CPI', 101, 101, 20, 0),
(102, 'FACEBOOK(官方)', NULL, 'CPM', 0, 'PC広告売上', '行動廣告CPM', 102, 102, 20, 1),
(103, 'FACEBOOK(官方)', NULL, 'CPM', 21, 'PC広告売上', '行動廣告CPM', 103, 103, 20, 0),
(104, 'LINE(standard)', NULL, 'CPI', 2, 'SP広告売上', '行動下載計費CPI', 104, 104, 0, 1),
(105, 'LINE(3DM)', NULL, 'CPI', 2, 'SP広告売上', '行動下載計費CPI', 105, 105, 0, 1),
(106, 'LINE(standard)', NULL, 'CPI', 22, 'SP広告売上', '行動下載計費CPI', 106, 106, 0, 0),
(107, 'LINE(3DM)', NULL, 'CPI', 22, 'SP広告売上', '行動下載計費CPI', 107, 107, 0, 0),
(108, 'Network全網聯播-鴻星數位(CPC)', NULL, 'CPC', 1, 'SP広告売上', '行動廣告CPC', 108, 108, 0, 1),
(109, 'Whoscall', NULL, 'CPC', 1, 'SP広告売上', '行動廣告CPC', 109, 109, 0, 1),
(110, 'LINE Timeline AD', NULL, 'CPM', 0, 'PC広告売上', '行動廣告CPM', 110, 110, 0, 1),
(111, 'Popin Recommend Article ADS', NULL, 'CPC', 1, 'PC広告売上', '行動廣告CPC', 111, 111, 0, 1),
(112, 'POPIN VIDEO ADS 225', NULL, 'CPM', 0, 'SP広告売上', 'CPM', 112, 112, 0, 1),
(113, 'AppLause Interstitial Video Ads 250', NULL, 'CPM', 0, 'SP広告売上', 'CPM', 113, 113, 0, 1),
(114, 'VM5_NativeADs - Card Video 225', NULL, 'CPM', 0, 'SP広告売上', 'CPM', 114, 114, 0, 0),
(115, 'APPLAUSE Video Ads', NULL, 'CPM', 0, 'SP広告売上', 'CPM', 115, 115, 0, 1),
(116, 'LINE Performance CPI Video', NULL, 'CPI', 2, 'SP広告売上', '行動下載計費CPI', 116, 116, 0, 1),
(117, 'SEADS', NULL, 'CPI', 2, 'SP広告売上', '行動下載計費CPI', 117, 117, 0, 1),
(118, 'Tenmax原生廣告', NULL, 'CPC', 1, 'SP広告売上', '行動廣告CPC', 118, 118, 0, 1),
(119, 'APPLAUSE Video Ads(CPV)', NULL, 'CPV', 9, 'SP広告売上', 'CPV', 119, 119, 0, 1),
(120, '小豬出任務', NULL, 'CPI', 2, 'SP APP売上', '行動下載計費CPI', 120, 120, 0, 1),
(121, 'POPIN VIDEO ADS(CPV)', NULL, 'CPV', 9, 'SP広告売上', 'CPV', 121, 121, 0, 1),
(122, 'Unity', NULL, 'CPI', 2, 'SP広告売上', '行動下載計費CPI', 122, 122, 0, 1),
(123, 'Line AD Platform(CPM)', NULL, 'CPM', 0, 'PC広告売上', '行動廣告CPM', 123, 123, 0, 1),
(124, 'Line AD Platform(CPC)', NULL, 'CPC', 1, 'PC広告売上', '行動廣告CPC', 124, 124, 0, 1),
(125, '錢包小豬(任務型)', NULL, '其他', 4, 'SP広告売上', '任務型', 125, 125, 20, 1),
(126, 'APPLOVIN', NULL, 'CPI', 2, 'SP広告売上', '行動下載計費CPI', 126, 126, 0, 1),
(127, 'POPIN 垂直式影音', NULL, 'CPM', 0, 'SP広告売上', 'CPM', 127, 127, 0, 1),
(128, 'Line Today', NULL, 'CPM', 0, 'PC広告売上', '行動廣告CPM', 128, 128, 0, 1),
(129, 'Blue Bee Series (BBS)', NULL, 'CPM', 0, 'PC広告売上', 'CPM', 129, 129, 0, 1),
(130, 'OPENPOINT', NULL, 'CPI', 2, 'SP広告売上', '行動下載計費CPI', 130, 130, 0, 1),
(131, 'Octpass', NULL, 'CPI', 2, 'SP広告売上', '行動下載計費CPI', 131, 131, 0, 1),
(132, '​BotBonnie 邦妮對話式機器人', NULL, '其他', 4, '', 'ChatBot', 132, 132, 0, 1),
(133, 'Facebook代操服務費', NULL, '服務費', 4, 'その他売上', '服務費', 133, 133, 0, 1),
(134, 'FACEBOOK代操(官方)', NULL, 'CPC', 1, 'PC広告売上', '行動廣告CPC', 4, 4, 20, 1),
(135, 'Facebook Install ads代操(官方)', NULL, 'CPI', 2, 'SP広告売上', '行動下載計費CPI', 7, 9, 20, 1),
(136, 'FACEBOOK代操(官方)', NULL, 'CPM', 0, 'PC広告売上', '行動廣告CPM', 102, 102, 20, 1),
(137, 'Party使用費', NULL, '其他', 4, 'SP APP売上', '服務費', 137, 137, 0, 1),
(138, 'Line(企業贊助貼圖)', NULL, '其他', 4, 'SP広告売上', '任務型', 138, 138, 0, 1),
(139, '雪豹科技(CPI)', NULL, 'CPI', 2, 'SP広告売上', '行動下載計費CPI', 139, 139, 0, 1),
(140, 'LINE (CPF)', NULL, '其他', 4, 'SP APP売上', 'CPF', 140, 140, 0, 1),
(141, 'Adways Asia', NULL, 'CPI', 2, 'SP広告売上', '行動下載計費CPI', 141, 141, 0, 1),
(142, 'Google代操服務費', NULL, '服務費', 4, 'その他売上', '服務費', 142, 142, 0, 1),
(143, '直接收入', NULL, 'Oct-pass EC', 4, 'その他売上', 'Oct-pass EC', 143, 143, 0, 1),
(144, 'LINE代操服務費', NULL, '服務費', 4, 'その他売上', '服務費', 144, 144, 0, 1),
(145, 'Smart-C', NULL, 'CPI', 22, 'SP広告売上', '行動下載計費CPI', 145, 145, 0, 1),
(146, 'twitter', NULL, 'CPI', 22, 'SP広告売上', '行動下載計費CPI', 146, 146, 0, 1),
(147, 'Google UAC', NULL, 'CPI', 22, 'SP広告売上', '行動下載計費CPI', 147, 147, 0, 1),
(148, '直接收入', NULL, '日本 EC', 4, 'その他売上', '日本 EC', 148, 148, 0, 1),
(149, 'Blue Bee Series (BBS)', NULL, 'CPC', 1, 'PC広告売上', 'CPC', 149, 149, 0, 1),
(150, 'Blue Bee Series (BBS)', NULL, 'CPV', 9, 'SP広告売上', 'CPV', 150, 150, 0, 1),
(151, 'CPC模組', NULL, 'CPC', 9, 'SP広告売上', 'CPC', 151, 151, 0, 1),
(152, 'CPI模組', NULL, 'CPI', 9, 'SP広告売上', 'CPI', 152, 152, 0, 1),
(153, 'CPM模組', NULL, 'CPM', 9, 'SP広告売上', 'CPM', 153, 153, 0, 1),
(154, 'CPV模組', NULL, 'CPV', 9, 'SP広告売上', 'CPV', 154, 154, 0, 1),
(155, 'CPT模組', NULL, 'CPT', 9, 'SP広告売上', 'CPT', 155, 155, 0, 1),
(156, 'CPS模組', NULL, 'CPS', 9, 'SP広告売上', 'CPS', 156, 156, 0, 1),
(157, '檔期模組', NULL, '檔期', 9, 'SP広告売上', '檔期', 157, 157, 0, 1),
(158, '網站廣告模組', NULL, '網站廣告', 9, 'SP広告売上', '網站廣告', 158, 158, 0, 1),
(159, '【任務型】Line(企業贊助貼圖)', NULL, '【任務型】Line(企業贊助貼圖)', 9, 'SP広告売上', '【任務型】Line(企業贊助貼圖)', 159, 159, 0, 1),
(160, '【其他】手機簡訊', NULL, '【其他】手機簡訊', 9, 'SP広告売上', '【其他】手機簡訊', 160, 160, 0, 1),
(161, '【機制費】廣告素材製作', NULL, '【機制費】廣告素材製作', 9, 'SP広告売上', '【機制費】廣告素材製作', 161, 161, 0, 1),
(162, '寫手費', NULL, '寫手費', 9, 'SP広告売上', '寫手費', 162, 162, 0, 1),
(163, 'Facebook代操服務費', NULL, 'Facebook代操服務費', 9, 'SP広告売上', 'Facebook代操服務費', 163, 163, 0, 1),
(164, 'SHIRYOUKO STUDIO', NULL, 'SHIRYOUKO STUDIO', 9, 'SP広告売上', 'SHIRYOUKO STUDIO', 164, 164, 0, 1),
(165, 'HappyGo MMS', NULL, 'HappyGo MMS', 9, 'SP広告売上', 'HappyGo MMS', 165, 165, 0, 1),
(166, 'Youtuber', NULL, 'Youtuber', 9, 'SP広告売上', 'Youtuber', 166, 166, 0, 1),
(167, '【行動下載計費CPI】LINE(3DM)', NULL, '【行動下載計費CPI】LINE(3DM)', 9, 'SP広告売上', '【行動下載計費CPI】LINE(3DM)', 167, 167, 0, 1),
(168, '預約TOP10', NULL, '預約TOP10', 9, 'SP広告売上', '預約TOP10', 168, 168, 0, 1),
(169, '錢包小豬(任務型)', NULL, '錢包小豬(任務型)', 9, 'SP広告売上', '錢包小豬(任務型)', 169, 169, 0, 1),
(170, 'CPA模組', NULL, 'CPA模組', 9, 'SP広告売上', 'CPA模組', 170, 170, 0, 1),
(171, 'CPE模組', NULL, 'CPE模組', 9, 'SP広告売上', 'CPE模組', 171, 171, 0, 1);



CREATE TABLE IF NOT EXISTS `media_accounting` (
  `accounting_id` int(11) NOT NULL AUTO_INCREMENT,
  `accounting_media_ordinal` int(11) NOT NULL DEFAULT '0',
  `accounting_media_item` int(11) NOT NULL DEFAULT '0',
  `accounting_campaign` int(11) NOT NULL DEFAULT '0',
  `accounting_month` int(11) NOT NULL DEFAULT '0',
  `accounting_revenue` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `accounting_cost` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '實際成本',
  `accounting_profit` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `accounting_gross_margin` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `accounting_comment` text COLLATE utf8_unicode_ci NOT NULL,
  `accounting_modifier` int(11) NOT NULL DEFAULT '0',
  `currency_id` varchar(3) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '幣別縮寫',
  `curr_cost` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT '預估金額',
  `invoice_number` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '發票編號',
  `invoice_date` int(11) DEFAULT NULL COMMENT '發票日期',
  `input_invoice_month` int(11) NOT NULL COMMENT '實際發票輸入日期',
  PRIMARY KEY (`accounting_id`),
  UNIQUE KEY `accounting_unique_id` (`accounting_campaign`,`accounting_media_ordinal`,`accounting_media_item`,`accounting_month`) USING BTREE,
  KEY `accounting_media_ordinal` (`accounting_media_ordinal`),
  KEY `accounting_media_item` (`accounting_media_item`),
  KEY `accounting_campaign` (`accounting_campaign`),
  KEY `accounting_month` (`accounting_month`),
  KEY `accounting_modifier` (`accounting_modifier`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=19 ;



INSERT INTO `media_accounting` (`accounting_id`, `accounting_media_ordinal`, `accounting_media_item`, `accounting_campaign`, `accounting_month`, `accounting_revenue`, `accounting_cost`, `accounting_profit`, `accounting_gross_margin`, `accounting_comment`, `accounting_modifier`, `currency_id`, `curr_cost`, `invoice_number`, `invoice_date`, `input_invoice_month`) VALUES
(1, 1, 7, 1, 201804, '123', '234', '-111', '0', '', 128, 'USD', '0', 'FG55051234', 1526098530, 0),
(2, 1, 5, 1, 201804, '77', '88', '-11', '0', '', 128, 'JPY', '0', 'HW56231552', 1523090000, 0),
(3, 1, 3, 1, 201804, '8888', '888', '8000', '0', '', 128, 'TWD', '0', '63250124', 1523090000, 0),
(4, 76, 2, 4, 201805, '123', '1234', '-1111', '0', '', 128, 'USD', '0', '63254785', 20180204, 201805),
(5, 149, 4, 6, 201805, '516', '333', '183', '0', '', 128, 'EUR', '0', '965845632', 20180203, 201805),
(6, 1, 9, 2, 201805, '0', '100', '-100', '0', '', 128, NULL, '0', NULL, NULL, 0),
(7, 91, 3, 8, 201805, '3568', '2987', '581', '0', '', 128, 'JPY', '0', '88888888', 20180205, 201805),
(8, 145, 4, 8, 201805, '1', '2', '-1', '0', '', 128, 'USD', '0', '1234567', 20180506, 201805),
(9, 75, 2, 12, 201804, '99', '999', '-900', '0', '', 128, 'EUR', '0', '111', 20180206, 0),
(10, 75, 2, 12, 201805, '88', '888', '-800', '0', '', 128, 'TWD', '0', '333', 20180530, 0),
(11, 75, 2, 12, 201806, '123', '123', '0', '0', '', 128, 'TWD', '0', '12345', 20180606, 0),
(12, 75, 2, 12, 201807, '66', '66', '0', '0', '', 128, 'USD', '0', '66', 20180808, 0),
(13, 150, 2, 16, 201807, '66', '66', '0', '0', '', 128, 'USD', '0', '66', 20180808, 0),
(14, 150, 73, 15, 201805, '2000', '20', '1980', '0', '', 128, '', '0', '123456789', 5, 0),
(15, 150, 77, 15, 201805, '2000', '20', '1980', '0', '', 128, '', '0', '12345678', 5, 0),
(16, 156, 2, 30, 201805, '123', '123', '0', '0', '', 128, 'usd', '0', '1243', 20180525, 0),
(17, 158, 16, 33, 201805, '1', '77', '-76', '0', '', 128, 'JPY', '9999', 'EA54048570', 20180606, 201805),
(18, 158, 16, 33, 201806, '6', '7', '-1', '0', '', 128, 'EUR', '666', '965845632', 20180530, 201805);



CREATE TABLE IF NOT EXISTS `media_seq` (
  `seq` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



INSERT INTO `media_seq` (`seq`) VALUES
(170);
