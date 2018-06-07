<?php
	// 2018/5/12 ken chien,對內的SAP成本表(匯出excel)
	//$_GET['search3']=年
	//$_GET['search4']=月

	ini_set('memory_limit', '256M');
	require_once dirname(dirname(__DIR__)) .'/autoload.php';

	$db = clone($GLOBALS['app']->db);

	IncludeFunctions('jsadways');

	IncludeFunctions('excel');
	$objPHPExcel = CreateExcelFile();
	$sh = &$objPHPExcel->getActiveSheet();
	
	
	//設定sheet欄位基本屬性+寬度
	$sh->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
	$objPHPExcel->getDefaultStyle()->getFont()->setName('新細明體');
	$objPHPExcel->getDefaultStyle()->getFont()->setSize(12);
	$sh->getDefaultColumnDimension()->setWidth(12);//設定欄位寬度
	$sh->getColumnDimension('A')->setWidth(12);
	$sh->getColumnDimension('B')->setWidth(8);
	$sh->getColumnDimension('C')->setWidth(12);
	$sh->getColumnDimension('D')->setWidth(12);
	$sh->getColumnDimension('E')->setWidth(12);
	$sh->getColumnDimension('F')->setWidth(12);
	$sh->getColumnDimension('G')->setWidth(14);
	$sh->getColumnDimension('H')->setWidth(8);
	$sh->getColumnDimension('I')->setWidth(12);
	$sh->getColumnDimension('J')->setWidth(14);
	$sh->getColumnDimension('K')->setWidth(22);
	$sh->getColumnDimension('L')->setWidth(16);
	$sh->getColumnDimension('M')->setWidth(22);
	$sh->getColumnDimension('N')->setWidth(22);
	$sh->getColumnDimension('O')->setWidth(14);
	$sh->getColumnDimension('P')->setWidth(14);
	$sh->getColumnDimension('Q')->setWidth(14);
	$sh->getColumnDimension('R')->setWidth(24);
	$sh->getColumnDimension('S')->setWidth(12);
	$sh->getColumnDimension('T')->setWidth(16);
	$sh->getColumnDimension('U')->setWidth(22);
	$sh->getColumnDimension('V')->setWidth(22);
	$sh->getColumnDimension('W')->setWidth(30);
	$sh->getColumnDimension('X')->setWidth(12);
	$sh->getColumnDimension('Y')->setWidth(12);
	$sh->getColumnDimension('Z')->setWidth(16);
	$sh->getColumnDimension('AA')->setWidth(16);
	$sh->getColumnDimension('AB')->setWidth(12);
	$sh->getColumnDimension('AC')->setWidth(30);
	$sh->getColumnDimension('AD')->setWidth(32);
	$sh->getColumnDimension('AE')->setWidth(12);
	$sh->getColumnDimension('AF')->setWidth(12);
	$sh->getColumnDimension('AG')->setWidth(12);
	$sh->getColumnDimension('AH')->setWidth(12);
	$sh->getColumnDimension('AI')->setWidth(12);
	$sh->getColumnDimension('AJ')->setWidth(14);
	$sh->getColumnDimension('AK')->setWidth(14);
	$sh->getColumnDimension('AL')->setWidth(18);
	$sh->getColumnDimension('AM')->setWidth(12);
	$sh->getColumnDimension('AN')->setWidth(14);
	$sh->getColumnDimension('AO')->setWidth(18);
	$sh->getColumnDimension('AP')->setWidth(18);
	$sh->getColumnDimension('AQ')->setWidth(14);
	$sh->getColumnDimension('AR')->setWidth(14);
	$sh->getColumnDimension('AS')->setWidth(14);
	$sh->getColumnDimension('AT')->setWidth(14);
	$sh->getColumnDimension('AU')->setWidth(16);
	$sh->getColumnDimension('AV')->setWidth(12);
	$sh->getColumnDimension('AW')->setWidth(16);
	$sh->getColumnDimension('AX')->setWidth(10);
	$sh->getColumnDimension('AY')->setWidth(10);
	$sh->getColumnDimension('AZ')->setWidth(14);
	$sh->getColumnDimension('BA')->setWidth(22);
	$sh->getColumnDimension('BB')->setWidth(20);
	$sh->getColumnDimension('BC')->setWidth(16);
	$sh->getColumnDimension('BD')->setWidth(10);
	$sh->getColumnDimension('BE')->setWidth(16);
	$sh->getColumnDimension('BF')->setWidth(16);

	$sh->setTitle("所有媒體");//設定標籤名稱

	$cellNum=1;
	$cellPos = array("A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z","AA","AB","AC","AD","AE","AF","AG","AH","AI","AJ","AK","AL","AM","AN","AO","AP","AQ","AR","AS","AT","AU","AV","AW","AX","AY","AZ","BA","BB","BC","BD","BE","BF","BG","BH","BI","BJ","BK","BL","BM","BN","BO","BP","BQ","BR","BS","BT","BU","BV","BW","BX","BY","BZ");

	//ken,title畫格子+置中
	//foreach ($cellPos as $title){
	//	SetExcellCellBorder($sh, [$title.$cellNum => 'all']);
	//	SetExcelCellCenter($sh, [$title.$cellNum]);
	//}


	//寫入第1排欄位名稱,共56欄位(A~BD)+2
	$pos = 0;
	$cellNum=1;
	$cellTitle1 = array("転記フラグ","元帳","会社コード","税自動計算","伝票日付","転記日付","伝票タイプ","通貨","換算レート","参照伝票番号","伝票ヘッダーテキスト","会社名","会社名は英語で","取引パートナー","転記キー","勘定科目","特殊G/L","固定資産取引タイプ","統制勘定","取引通貨金額","ローカル通貨金額","グループ通貨金額","グローバル会社通貨金額","税コード","税タイプ","原価センター","利益センター","事業領域","取引パートナーの事業領域","取引パートナーの利益センター","支払条件","サイト","支払凍結","支払参照","支払方法","支払基準日","手形発行日","WBSエレメント","内部指図","製品コード","財務計画項目","スタート日付","参照１","参照２","参照３","中央銀行","ベンダー国家","取引銀行","資産価値日","数量","数量単位","セグメント","参照（ソートキー）","明細テキスト","取引タイプ","取引先","編號","SAP編號");
	foreach ($cellTitle1 as $titleName1){
	　	&$sh->setCellValue($cellPos[$pos++].$cellNum, $titleName1);
	}


	//寫入第2排欄位名稱,共56欄位+2
	$pos = 0;
	$cellNum=2;
	$cellTitle2 = array("过账标识","分类账","公司代码","税自动计算","凭证日期","记账日期","凭证类型","币种","换算汇率","参考凭证号码","凭证头文本","公司简称","公司英文名","贸易伙伴","记账码","会计科目","特殊G/L","固定资产事务类型","统驭科目","凭证货币金额","本位币金额","集团货币金额","全球公司金额","税码","税种","成本中心","利润中心","业务范围","贸易伙伴业务范围","贸易伙伴利润中心","付款条件","场所","支付冻结","支付参考","支付方法","支付基准日","票据发行日","WBS元素","活动ID","产品代码","财务计划项目","开始日期","参考1","参考2","参考3","中央银行","供货国家","开户银行","资产价值日","数量","数量单位","段","参考（分配）","明细文本","事务类型","交易方","編號","SAP編號");
	foreach ($cellTitle2 as $titleName2){
	　	&$sh->setCellValue($cellPos[$pos++].$cellNum, $titleName2);
	}

	//ken,這樣寫最好,不會有問題,foreach裡面用&還是有點問題
	//寫入第3排欄位名稱,共56欄位+2
	$pos = 0;
	$cellNum=3;
	$data = [];
	$data[] = [
	    'Posting Control',
		'Ledger',
		'Compay Code',
		'Calculate tax automatically',
		'Document Date',
		'Posting Date',
		'Document Type',
		'Currency',
		'Exchange rate',
		'Reference',
		'Doc.Header Text',
		'Company Abbreviation Name',
		'Company name in English',
		'trading partner',
		'Posting key',
		'Account No.',
		'Special G/L Indicator',
		'Asset Transaction Type',
		'Reconciliation account',
		'Amount in document currency',
		'Amount in Local Currency',
		'Group currency',
		'Global company currency',
		'Tax Code',
		'Tax Type',
		'Cost Center',
		'Profit Center',
		'Business area',
		'Trading partner\'s business area',
		'Partner Profit Center',
		'Payment Term',
		'site',
		'Payment Block',
		'Payment Reference',
		'Payment Method',
		'Due date',
		'Note issue date',
		'WBS Element',
		'Order Number',
		'Product Code',
		'Financial planning item',
		'Starting date',
		'Reference 1',
		'Reference 2',
		'Reference 3',
		'Central bank',
		'Supplying countries',
		'House Bank',
		'Asset value date',
		'Quantity',
		'数量単位',
		'Segment',
		'Reference (Assignment)',
		'Item Text',
		'取引タイプ',
		'取引先',
		'編號',
		'SAP編號'];
	SetExcellCellFromArray($objPHPExcel, $data,0,$cellNum);



	//開始準備填入資料
	$cellNum=4;
	$firstRow = true;
	$mark = "X";//$mark=过账标识,如每筆匯出資料多複製一行 例 一二行為相同資料 但第一筆資料此欄打X
	$markNumber = 31;//$markNumber=记账码,此項須建立在 (Posting Control 描述成立)第一筆為31 第二筆為40

	CreateNativeDBConnector();

	$dateYearMonth = sprintf('%04d%02d', GetVar('search3'), GetVar('search4'));//轉換成201805
	
	//ken,根據accounting_month=查詢條件,則每一筆都轉成輸出兩筆預估成本
	$sql=sprintf("SELECT acc.accounting_id,acc.accounting_cost,acc.currency_id,acc.curr_cost,acc.invoice_number,acc.invoice_date,
		com.name as com_name,com.name2 as com_name2,com.eng_name as com_eng_name,com.tax_id as com_tax_id,
		r.numberid,o.item_seq,o.jpc_seq
		FROM media_accounting acc 
		left join receipt r on r.receipt_number = acc.invoice_number COLLATE utf8_unicode_ci 
		left join cp_detail o on o.cue='2' and o.cp_id = acc.accounting_campaign and o.mtype_number = acc.accounting_media_ordinal and o.mtype_id = acc.accounting_media_item  
		left join companies com on com.id = o.comp_id
		where acc.accounting_month = '%d'",$dateYearMonth);

	$dsAcc=mysql_query($sql); 
	$checkRowCount = mysql_num_rows($dsAcc);

	if($checkRowCount>0){
		while($drAcc=mysql_fetch_array($dsAcc)){
			for ($writeCount=0; $writeCount < 2; $writeCount++) { 
				$invoice_date = ($drAcc['invoice_date'] == null ? '' : $drAcc['invoice_date']);//date('Ymd',$drAcc['invoice_date'])
				$currency_id = ($drAcc['currency_id'] == null ? '' : $drAcc['currency_id']);

				$com_name = ($drAcc['com_name'] == null ? '' : $drAcc['com_name']);
				$com_name2 = ($drAcc['com_name2'] == null ? '' : $drAcc['com_name2']);
				$com_eng_name = ($drAcc['com_eng_name'] == null ? '' : $drAcc['com_eng_name']);
				$com_tax_id = ($drAcc['com_tax_id'] == null ? '' : 'CD'.$drAcc['com_tax_id']);

				$amt = ($currency_id=='TWD' || $currency_id=='' ? $drAcc['curr_cost'] : '');//預估的凭证货币金额
				$forAmt = ($currency_id=='TWD' || $currency_id=='' ? '' : $drAcc['curr_cost']);//預估的本位币金额

				//$paydate=支付基准日,media_accounting.invoice_date的下個月1號往後加90天
				//$now_month = date("Y-m",$drAcc['invoice_date']).'-01';
				//$now_month_math = strtotime($now_month);
				$now_month_math = strtotime($dr['invoice_date']);
				$nextMonthFirstDay = date("Y-m",strtotime("+1 month", $now_month_math))."-01";
				$paydate = date("Ymd", strtotime ($nextMonthFirstDay ."+90 days"));

				$number_id = ($drAcc['numberid'] == null ? '' : $drAcc['numberid']);

				$sh->setCellValue("A".$cellNum,$mark);//过账标识,如每筆匯出資料多複製一行 例 一二行為相同資料 但第一筆資料此欄打X
				$sh->setCellValue("B".$cellNum,$drAcc['accounting_id']);//分类账,空白//ken,test
				$sh->setCellValue("C".$cellNum,"C03");//公司代码,固定值 C03
				$sh->setCellValue("D".$cellNum,"");//税自动计算,空白
				$sh->setCellValue("E".$cellNum,"");//凭证日期,media_accounting.invoice_date

				$sh->setCellValue("F".$cellNum,"");//记账日期,media_accounting.invoice_date
				$sh->setCellValue("G".$cellNum,"KJ");//凭证类型,固定值:KJ
				$sh->setCellValue("H".$cellNum,$currency_id);//币种,media_accounting.currency_id
				$sh->setCellValue("I".$cellNum,"");//换算汇率,空白
				$sh->setCellValue("J".$cellNum,"");//参考凭证号码,空白

				$sh->setCellValue("K".$cellNum,$com_name);//凭证头文本,companies.name
				$sh->setCellValue("L".$cellNum,$com_name2);//公司简称,companies.name2
				$sh->setCellValue("M".$cellNum,$com_eng_name);//公司英文名,companies.eng_name
				$sh->setCellValue("N".$cellNum,"");//贸易伙伴,空白
				$sh->setCellValue("O".$cellNum,$markNumber);//记账码,此項須建立在 (Posting Control 描述成立)第一筆為31 第二筆為40

				$sh->setCellValue("P".$cellNum,$com_tax_id);//会计科目,CD + companies.tax_id
				$sh->setCellValue("Q".$cellNum,"");//特殊G/L,空白
				$sh->setCellValue("R".$cellNum,"");//固定资产事务类型,空白
				$sh->setCellValue("S".$cellNum,"");//统驭科目,空白
				$sh->setCellValue("T".$cellNum,$amt);//凭证货币金额,if media_accounting.currency_id = TWD,then curr_cost else null

				$sh->setCellValue("U".$cellNum,$forAmt);//本位币金额,if media_accounting.currency_id = TWD,then null else curr_cost
				$sh->setCellValue("V".$cellNum,"");//集团货币金额,空白
				$sh->setCellValue("W".$cellNum,"");//全球公司金额,空白
				$sh->setCellValue("X".$cellNum,"");//税码,空白
				$sh->setCellValue("Y".$cellNum,"");//税种,空白

				$sh->setCellValue("Z".$cellNum,"");//成本中心,空白
				$sh->setCellValue("AA".$cellNum,"");//利润中心,空白
				$sh->setCellValue("AB".$cellNum,"");//业务范围,空白
				$sh->setCellValue("AC".$cellNum,"");//贸易伙伴业务范围,空白
				$sh->setCellValue("AD".$cellNum,"");//贸易伙伴利润中心,空白

				$sh->setCellValue("AE".$cellNum,"X900");//付款条件,固定：X900
				$sh->setCellValue("AF".$cellNum,"");//场所,空白
				$sh->setCellValue("AG".$cellNum,"");//支付冻结,空白
				$sh->setCellValue("AH".$cellNum,"");//支付参考,空白
				$sh->setCellValue("AI".$cellNum,"");//支付方法,空白

				$sh->setCellValue("AJ".$cellNum,"");//支付基准日,空白
				$sh->setCellValue("AK".$cellNum,"");//票据发行日,空白
				$sh->setCellValue("AL".$cellNum,"");//WBS元素,空白
				$sh->setCellValue("AM".$cellNum,"");//活动ID,空白
				$sh->setCellValue("AN".$cellNum,"");//产品代码,空白

				$sh->setCellValue("AO".$cellNum,"");//财务计划项目,空白
				$sh->setCellValue("AP".$cellNum,"");//开始日期,空白
				$sh->setCellValue("AQ".$cellNum,"");//参考1,空白
				$sh->setCellValue("AR".$cellNum,"");//参考2,空白
				$sh->setCellValue("AS".$cellNum,"");//参考3,空白

				$sh->setCellValue("AT".$cellNum,"");//中央银行,空白
				$sh->setCellValue("AU".$cellNum,"");//供货国家,空白
				$sh->setCellValue("AV".$cellNum,"");//开户银行,空白
				$sh->setCellValue("AW".$cellNum,"");//资产价值日,空白
				$sh->setCellValue("AX".$cellNum,"");//数量,空白

				$sh->setCellValue("AY".$cellNum,"");//数量单位,空白
				$sh->setCellValue("AZ".$cellNum,"");//段,空白
				$sh->setCellValue("BA".$cellNum,"");//参考（分配）,空白
				$sh->setCellValue("BB".$cellNum,$number_id);//明细文本,receipt.numberid 委刊編號
				$sh->setCellValue("BC".$cellNum,"");//事务类型,空白

				$sh->setCellValue("BD".$cellNum,"");//交易方,空白
				$sh->setCellValue("BE".$cellNum,$drAcc['item_seq']);//編號,item_seq
				$sh->setCellValue("BF".$cellNum,$drAcc['jpc_seq']);//SAP編號,jpc_seq

				//同一筆row要寫兩筆,這邊要做切換第二筆
				if($firstRow){
					$firstRow = false;
					$mark = "";
					$markNumber = 40;
				}
				$cellNum++;
			}//for ($writeCount=0; $writeCount < 2; $writeCount++) { 


			//準備換下一行
			$firstRow = true;
			$mark = "X";
			$markNumber = 31;
		}//while($drAcc=mysql_fetch_array($dsAcc)){
	}//if($drAccCount>0){


	$firstRow = true;
	$mark = "X";//$mark=过账标识,如每筆匯出資料多複製一行 例 一二行為相同資料 但第一筆資料此欄打X
	$markNumber = 31;//$markNumber=记账码,此項須建立在 (Posting Control 描述成立)第一筆為31 第二筆為40

	//ken,如果有輸入發票,則再輸入兩筆實際成本+發票資訊
	$sqlInvoice=sprintf("SELECT acc.accounting_id,acc.accounting_cost,acc.currency_id,acc.curr_cost,
		acc.invoice_number,acc.invoice_date,
		com.name as com_name,com.name2 as com_name2,com.eng_name as com_eng_name,com.tax_id as com_tax_id,
		r.numberid,o.item_seq,o.jpc_seq
		FROM media_accounting acc 
		left join receipt r on r.receipt_number = acc.invoice_number COLLATE utf8_unicode_ci 
		left join cp_detail o on o.cue='2' and o.cp_id = acc.accounting_campaign and o.mtype_number = acc.accounting_media_ordinal and o.mtype_id = acc.accounting_media_item 
		left join companies com on com.id = o.comp_id 
		where acc.input_invoice_month = '%d' 
		order by item_seq,invoice_date",$dateYearMonth);

	$dsInvoice=mysql_query($sqlInvoice); 
	$checkRowCount = mysql_num_rows($dsInvoice);

	if($checkRowCount>0){
		while($dr = mysql_fetch_array($dsInvoice)){
			for ($writeCount=0; $writeCount < 2; $writeCount++) { 

				$invoice_date = ($dr['invoice_date'] == null ? '' : $dr['invoice_date']);//date('Ymd',$dr['invoice_date'])
				$currency_id = ($dr['currency_id'] == null ? '' : $dr['currency_id']);

				$com_name = ($dr['com_name'] == null ? '' : $dr['com_name']);
				$com_name2 = ($dr['com_name2'] == null ? '' : $dr['com_name2']);
				$com_eng_name = ($dr['com_eng_name'] == null ? '' : $dr['com_eng_name']);
				$com_tax_id = ($dr['com_tax_id'] == null ? '' : 'CD'.$dr['com_tax_id']);

				$amt = $dr['accounting_cost'];//實際的發票金額,凭证货币金额
								

				//$paydate=支付基准日,media_accounting.invoice_date的下個月1號往後加90天
				//$now_month = date("Y-m",$dr['invoice_date']).'-01';
				//$now_month_math = strtotime($now_month);
				$now_month_math = strtotime($dr['invoice_date']);
				$nextMonthFirstDay = date("Y-m",strtotime("+1 month", $now_month_math))."-01";
				$paydate = date("Ymd", strtotime($nextMonthFirstDay ."+90 days"));

				$invoice_number = ($dr['invoice_number'] == null ? '' : $dr['invoice_number']);
				$number_id = ($dr['numberid'] == null ? '' : $dr['numberid']);

				$sh->setCellValue("A".$cellNum,$mark);//过账标识,如每筆匯出資料多複製一行 例 一二行為相同資料 但第一筆資料此欄打X
				$sh->setCellValue("B".$cellNum,$dr['accounting_id']);//分类账,空白//ken,test
				$sh->setCellValue("C".$cellNum,"C03");//公司代码,固定值 C03
				$sh->setCellValue("D".$cellNum,"");//税自动计算,空白
				$sh->setCellValue("E".$cellNum,$invoice_date);//凭证日期,media_accounting.invoice_date

				$sh->setCellValue("F".$cellNum,$invoice_date);//记账日期,media_accounting.invoice_date
				$sh->setCellValue("G".$cellNum,"KJ");//凭证类型,固定值:KJ
				$sh->setCellValue("H".$cellNum,$currency_id);//币种,media_accounting.currency_id
				$sh->setCellValue("I".$cellNum,"");//换算汇率,空白
				$sh->setCellValue("J".$cellNum,"");//参考凭证号码,空白

				$sh->setCellValue("K".$cellNum,$com_name);//凭证头文本,companies.name
				$sh->setCellValue("L".$cellNum,$com_name2);//公司简称,companies.name2
				$sh->setCellValue("M".$cellNum,$com_eng_name);//公司英文名,companies.eng_name
				$sh->setCellValue("N".$cellNum,"");//贸易伙伴,空白
				$sh->setCellValue("O".$cellNum,$markNumber);//记账码,此項須建立在 (Posting Control 描述成立)第一筆為31 第二筆為40

				$sh->setCellValue("P".$cellNum,$com_tax_id);//会计科目,CD + companies.tax_id
				$sh->setCellValue("Q".$cellNum,"");//特殊G/L,空白
				$sh->setCellValue("R".$cellNum,"");//固定资产事务类型,空白
				$sh->setCellValue("S".$cellNum,"");//统驭科目,空白
				$sh->setCellValue("T".$cellNum,$amt);//凭证货币金额

				$sh->setCellValue("U".$cellNum,"");//本位币金额
				$sh->setCellValue("V".$cellNum,"");//集团货币金额,空白
				$sh->setCellValue("W".$cellNum,"");//全球公司金额,空白
				$sh->setCellValue("X".$cellNum,"");//税码,空白
				$sh->setCellValue("Y".$cellNum,"");//税种,空白

				$sh->setCellValue("Z".$cellNum,"");//成本中心,空白
				$sh->setCellValue("AA".$cellNum,"");//利润中心,空白
				$sh->setCellValue("AB".$cellNum,"");//业务范围,空白
				$sh->setCellValue("AC".$cellNum,"");//贸易伙伴业务范围,空白
				$sh->setCellValue("AD".$cellNum,"");//贸易伙伴利润中心,空白

				$sh->setCellValue("AE".$cellNum,"X900");//付款条件,固定：X900
				$sh->setCellValue("AF".$cellNum,"");//场所,空白
				$sh->setCellValue("AG".$cellNum,"");//支付冻结,空白
				$sh->setCellValue("AH".$cellNum,"");//支付参考,空白
				$sh->setCellValue("AI".$cellNum,"");//支付方法,空白

				$sh->setCellValue("AJ".$cellNum,$paydate);//支付基准日,media_accounting.invoice_date的下個月1號往後加90天
				$sh->setCellValue("AK".$cellNum,"");//票据发行日,空白
				$sh->setCellValue("AL".$cellNum,"");//WBS元素,空白
				$sh->setCellValue("AM".$cellNum,"");//活动ID,空白
				$sh->setCellValue("AN".$cellNum,"");//产品代码,空白

				$sh->setCellValue("AO".$cellNum,"");//财务计划项目,空白
				$sh->setCellValue("AP".$cellNum,"");//开始日期,空白
				$sh->setCellValue("AQ".$cellNum,"");//参考1,空白
				$sh->setCellValue("AR".$cellNum,"");//参考2,空白
				$sh->setCellValue("AS".$cellNum,$invoice_number);//参考3,media_accounting.invoice_number

				$sh->setCellValue("AT".$cellNum,"");//中央银行,空白
				$sh->setCellValue("AU".$cellNum,"");//供货国家,空白
				$sh->setCellValue("AV".$cellNum,"");//开户银行,空白
				$sh->setCellValue("AW".$cellNum,"");//资产价值日,空白
				$sh->setCellValue("AX".$cellNum,"");//数量,空白

				$sh->setCellValue("AY".$cellNum,"");//数量单位,空白
				$sh->setCellValue("AZ".$cellNum,"");//段,空白
				$sh->setCellValue("BA".$cellNum,"");//参考（分配）,空白
				$sh->setCellValue("BB".$cellNum,$number_id);//明细文本,receipt.numberid 委刊編號
				$sh->setCellValue("BC".$cellNum,"");//事务类型,空白

				$sh->setCellValue("BD".$cellNum,"");//交易方,空白
				$sh->setCellValue("BE".$cellNum,$dr['item_seq']);//編號,item_seq
				$sh->setCellValue("BF".$cellNum,$dr['jpc_seq']);//SAP編號,jpc_seq

				//同一筆row要寫兩筆,這邊要做切換第二筆
				if($firstRow){
					$firstRow = false;
					$mark = "";
					$markNumber = 40;
				}
				$cellNum++;
			}//for ($writeCount=0; $writeCount < 2; $writeCount++) { 


			//準備換下一行
			$firstRow = true;
			$mark = "X";
			$markNumber = 31;
		}//while($dr = mysql_fetch_array($dsInvoice)){
	}//if($drAccCount>0){



	//ken,畫底色沒成功,有空再測試
	$sh->getStyle('A1:BF3')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
	$sh->getStyle('A1:BF3')->getFill()->getStartColor()->setRGB('ccffcc'); 
	
	$sh->getStyle('A1:BF3')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);//靠上

	//ken,最後準備輸出excel,取個檔名
	//$objPHPExcel->setActiveSheetIndex(0);//ken,沒用到
	$xlsFilename = sprintf('%d年%d月SAP成本表', $_GET['search3'], $_GET['search4']);//預設副檔名為xlsx
	SendExcellFile($objPHPExcel, $xlsFilename);
