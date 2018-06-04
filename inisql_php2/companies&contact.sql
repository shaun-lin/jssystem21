

drop table if exists `companies`;
drop table if exists `companies_contact`;



CREATE TABLE IF NOT EXISTS `companies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL COMMENT '公司名稱',
  `name2` varchar(30) DEFAULT NULL COMMENT '公司簡稱',
  `eng_name` varchar(50) DEFAULT NULL COMMENT '英文名稱',
  `tax_id` int(20) DEFAULT NULL COMMENT '統編',
  `tel` int(15) DEFAULT NULL COMMENT '電話',
  `fax` int(15) DEFAULT NULL COMMENT '傳真',
  `address` varchar(100) DEFAULT NULL COMMENT '地址',
  `payinfo` varchar(100) DEFAULT NULL COMMENT '匯款資訊',
  `paydays` int(3) DEFAULT NULL COMMENT '付款天數',
  `refund` int(3) DEFAULT NULL COMMENT '退傭%數',
  `update_user` varchar(50) DEFAULT NULL COMMENT '更新者',
  `update_date` int(11) DEFAULT NULL COMMENT '更新時間',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=17 ;


INSERT INTO `companies` (`id`, `name`, `name2`, `eng_name`, `tax_id`, `tel`, `fax`, `address`, `payinfo`, `paydays`, `refund`, `update_user`, `update_date`) VALUES
(1, 'JS', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Jackie', NULL),
(2, '信用卡', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Jackie', NULL),
(3, '威旭數位媒體有限公司', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Jackie', NULL),
(4, '優仕啵有限公司', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Jackie', NULL),
(5, '國眾電腦股份有限公司', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Jackie', NULL),
(6, '生洋網路股份有限公司', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Jackie', NULL),
(7, 'Hitokuse', 'eee', 'jey', 0, 0, 0, 'ddd', 'dddd', 0, 3, 'jimmy', 1525956326),
(8, '台灣連線股份有限公司', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Jackie', NULL),
(9, '邦妮科技有限公司', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Jackie', NULL),
(10, '金寶安科技有限公司', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Jackie', NULL),
(11, '昂奈科技有限公司', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Jackie', NULL),
(12, '新夢想數位科技股份有限公司', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Jackie', NULL),
(13, '統一數網股份有限公司', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Jackie', NULL),
(14, '博英科技有限公司', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Jackie', NULL),
(15, '騰學廣告科技有限公司', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Jackie', NULL),
(16, '博斯比股份有限公司 Inc.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Jackie', NULL);






CREATE TABLE IF NOT EXISTS `companies_contact` (
  `contact_id` int(11) NOT NULL AUTO_INCREMENT,
  `contact_agency` int(11) NOT NULL DEFAULT '0',
  `contact_title` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `contact_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `contact_tel` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `contact_email` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`contact_id`),
  KEY `contact_agency` (`contact_agency`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=948 ;



INSERT INTO `companies_contact` (`contact_id`, `contact_agency`, `contact_title`, `contact_name`, `contact_tel`, `contact_email`) VALUES
(1, 3, '', '柯怡安Anne', '02-2721-9664# 117', 'anne@cheesead.com.tw'),
(2, 3, '', '鄭宛柔Emilie Cheng', '02-2721-9664# 108', 'emilie@cheesead.com.tw'),
(478, 5, '', '楊淑芬 Angela Yang', '(02)2717-5238 ext. 8737', 'Angela.Yang@carat.com'),
(479, 5, '', '趙慧騥Malo Chao', '(02)2717-5238 ext. 8504', 'Malo.Chao@carat.com'),
(480, 5, '', '李筱慧　Naomi Lexe', '(02)2717-5238 ext. 87498', 'Naomi.lee@carat.com'),
(481, 5, '', '林彥均 Cindy Lin', '(02)2717-5238 ext. 8736', 'Cindy.Lin@carat.com'),
(482, 5, '', '朱翊綾 Jessica Chu', '(02)2717-5238 ext. 8345', 'Jessica.Chu@carat.com'),
(483, 5, '', '張雅玲Janice Chang', '(02)2717-5238 ext. 8747', 'Janice.Chang@carat.com'),
(484, 5, '', '詹雅雯 Emma Jan', '(02)2717-5238 ext. 8502', 'emma.jan@carat.com'),
(485, 5, '', '董雅為 Yawei Tung', '(02)2717-5238 ext. 8739', 'Yawei.Tung@carat.com'),
(486, 5, '', '謝旻璟 Mint Hsieh', '(02)2717-5238 ext. 8740', 'Mint.Hsieh@carat.com'),
(487, 5, '', '王振宇 C. Y. Wang', '(02)2717-5238 ext. 8346', 'CY.Wang@carat.com'),
(488, 5, '', '何宜君 Nina Ho', '(02)2717-5238 ext. 8751', 'Nina.Ho@carat.com'),
(489, 5, '', '汪湄 Mei Wang', '02 27173898 ext. 8506', 'Mei.Wang@carat.com'),
(490, 5, '', 'Rainie Wang', '(02)2717-5238 ext. 8507', 'rainie.wang@carat.com'),
(491, 5, '', '徐睿敏 Bella Hsu', '(02)2717-5238 ext. 8203', 'Bellat.Hsu@carat.com|www.carat.com.tw'),
(492, 5, '', '陳芸玫 Vivian Chen', '02 27173898 ext.8505', 'Vivian.Chen@carat.com'),
(493, 5, '', '林芳萍 Cleo Lin', '02 27173898 ext.8611', 'cleo.lin@carat.com'),
(494, 5, '', '高家華 Fallon Kao', '02 27173898 ext.8215', 'Fallon.Kao@carat.com'),
(495, 5, '', '周韋彤 Julie', '0227173839#8624', 'julie.chou@aaamedia.com.tw'),
(496, 5, '', '陳鈺欣 Angela', '2717-5238 #8760', 'Angelas.chen@carat.com'),
(497, 5, '數位購買副總監Associate Digital Buying Director', '史修辭 Shiuci Shih', '2717-5238 #86212', 'Shiuci.Shih@carat.com'),
(498, 5, 'Evens Fan', '范慧如', '27175238 #9768', 'Evens.Fan@dentsuaegis.com'),
(924, 4, '', '李其螢Lydia', '02-2720-6768#596', 'lydia_lee@media-palette.com.tw'),
(925, 4, '', '李政翰Duncan', '02-2720-6768#275', 'duncan_li@media-palette.com.tw'),
(926, 4, '', '劉滋欣Tiffany', '02-2720-6768#272', 'tiffany_liu@media-palette.com.tw'),
(927, 4, '', '林千鈴Jovy', '02-2720-6768#565', 'jovy_lin@media-palette.com.tw'),
(928, 4, '', '李孟凡Gigi', '02-2720-6768#599', 'gigi_lee@media-palette.com.tw'),
(929, 4, '', '王婉君Kerina', '02-2720-6768#596', 'kerina_wang@media-palette.com.tw'),
(930, 4, '', '陳柏全Luk', '02-2720-6768#563', 'luk_chen@media-palette.com.tw'),
(931, 4, '', '王麗麗Li Li', '02-2720-6768#562', 'lili_wang@media-palette.com.tw'),
(932, 4, '', '趙泰瑋Lucas', '02-2720-6768#271', 'Lucas_Chao@media-palette.com.tw'),
(933, 4, '', '韓正儀Ariel', '02-2720-6768#595', 'ariel_han@media-palette.com.tw'),
(934, 4, '', '林瑞津Ray', '02-2720-6768#279', 'Ray.Lin@media-palette.com.tw'),
(935, 4, '', '卓潔妮Jenny Zhou', '02-2720-6768#569', 'jenny_zhuo@media-palette.com.tw'),
(936, 4, '', '倪千涵 Nicole Ni', '02-2720-6768#278', 'nicole_ni@media-palette.com.tw'),
(937, 4, '', '劉本莉 Penli', '02-2720-6768#565', 'penli.liu@media-palette.com.tw'),
(938, 4, '', '熊凱齡 Bear', '02-2720-6768#594', 'bear.Hsiung@media-palette.com.tw'),
(939, 4, '', '賴沛汝 Catrina', '02-2720-6768#599', 'Catrina.Lai@media-palette.com.tw'),
(940, 4, '', 'Kate', '02-2720-6768#279', 'Kate.Lin@media-palette.com.tw'),
(941, 4, '', '羅依婷 I-ting', '02-2720-6768#596', 'Iting.lo@media-palette.com.tw'),
(942, 4, '', '黃柏蒼 Kevin', '02-2720-6768#569', 'kevin.huang@media-palette.com.tw'),
(943, 4, '', '林庭安 Ivy Lin', '+886-2-27206768 ext.281', 'Ivy.Lin@dxglobal.com'),
(944, 4, '', '羅潔敏 Kitman Lo', '02-27206768#592', 'kitman.lo@dxglobal.com'),
(945, 4, '', ' 田晟寧 Louise Tian ', '02-27206768#594', 'louise.tian@dxglobal.com'),
(946, 4, '', '李倩儀 Samantha Li', '02-27206768#283', 'samantha.li@dxglobal.com'),
(947, 4, '', '莊求安 Joann', '02-2720-6768 #278', 'joann.chuang@dxglobal.com');
