
drop table if exists `cp_detail`;
drop table if exists `lookup`;
drop table if exists `total_seq`;
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
(15, 'CPA', '其他', '8', 8),
(17, 'mtype', '172', 'CPC_add', 1),
(18, 'mtype', '152', 'CPI_add', 2),
(19, 'mtype', '153', 'CPM_add', 3),
(20, 'mtype', '154', 'CPV_add', 4),
(21, 'mtype', '155', 'CPT_add', 5),
(22, 'mtype', '156', 'CPS_add', 6),
(23, 'mtype', '157', 'Schedule_add', 7),
(24, 'mtype', '158', 'WebADV_add', 8),
(25, 'mtype', '159', 'LineCorpMap_add', 9),
(26, 'mtype', '160', 'MMS_add', 10),
(27, 'mtype', '161', 'Creative_add', 11),
(28, 'mtype', '162', 'Handwriting_edit', 12),
(29, 'mtype', '163', 'FBFee_add', 13),
(30, 'mtype', '164', 'ShiryoukoStudio_add', 14),
(31, 'mtype', '165', 'HappyGoMMS_add', 15),
(32, 'mtype', '166', 'Youtuber_add', 16),
(33, 'mtype', '167', 'LINE3DM_add', 17),
(34, 'mtype', '168', 'TOPTen_add', 18),
(35, 'mtype', '169', 'Mission_add', 19),
(36, 'mtype', '170', 'CPA_add', 20),
(37, 'mtype', '171', 'CPE_add', 21),
(38, 'mtypeedit', '172', 'CPC_edit', 1),
(39, 'mtypeedit', '152', 'CPI_edit', 2),
(40, 'mtypeedit', '153', 'CPM_edit', 3),
(41, 'mtypeedit', '154', 'CPV_edit', 4),
(42, 'mtypeedit', '155', 'CPT_edit', 5),
(43, 'mtypeedit', '156', 'CPS_edit', 6),
(44, 'mtypeedit', '157', 'Schedule_edit', 7),
(45, 'mtypeedit', '158', 'WebADV_edit', 8),
(46, 'mtypeedit', '159', 'LineCorpMap_edit', 9),
(47, 'mtypeedit', '160', 'MMS_edit', 10),
(48, 'mtypeedit', '161', 'Creative_edit', 11),
(49, 'mtypeedit', '162', 'Handwriting_add', 12),
(50, 'mtypeedit', '163', 'FBFee_edit', 13),
(51, 'mtypeedit', '164', 'ShiryoukoStudio_edit', 14),
(52, 'mtypeedit', '165', 'HappyGoMMS_edit', 15),
(53, 'mtypeedit', '166', 'Youtuber_edit', 16),
(54, 'mtypeedit', '167', 'LINE3DM_edit', 17),
(55, 'mtypeedit', '168', 'TOPTen_edit', 18),
(56, 'mtypeedit', '169', 'Mission_edit', 19),
(57, 'mtypeedit', '171', 'CPE_edit', 21),
(58, 'mtypeedit', '170', 'CPA_edit', 20);



CREATE TABLE IF NOT EXISTS `total_seq` (
  `seq_id` int(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `total_seq` (`seq_id`) VALUES
(5001);





INSERT INTO `media` (`id`, `name`, `company`, `costper`, `type`, `type2`, `typename`, `sortid`, `sortid2`, `profit`, `display`) VALUES

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
(172, 'CPC模組', NULL, 'CPC', 9, 'SP広告売上', 'CPC', 172, 172, 0, 1);




CREATE TABLE IF NOT EXISTS `media_seq` (
  `seq` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `media_seq` (`seq`) VALUES
(170);


alter table `media_accounting` add column `curr_cost` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT '預估金額';
alter table `media_accounting` add column `invoice_number` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '發票編號';
alter table `media_accounting` add column `invoice_date` int(11) unsigned DEFAULT NULL COMMENT '發票日期';
alter table `media_accounting` add column ` input_invoice_month` int(11) unsigned DEFAULT NULL;