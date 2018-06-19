<?php
//jackie 2018/06/07
//KEN說要加一下
ini_set('memory_limit', '256M');
require_once dirname(dirname(__DIR__)) .'/autoload.php';
require_once dirname(dirname(__DIR__)).'/jsadways2/include/db.inc.php';
include_once(dirname(__DIR__)).'/excel/Classes/PHPExcel/IOFactory.php';


//如果沒選擇檔案，跳個訊息給他
if ($_FILES["file"]["error"] > 0){
$_FILES["file"]["error"];
    if($_FILES["file"]["error"]==4)
    ShowMessageAndRedirect('請選擇檔案','/erp/jsadways2/sap_excel.php' , false);
}
//有抓到檔案就把資訊撈一撈
else{
$_FILES["file"]["name"]."<br/>";
$_FILES["file"]["type"]."<br/>";
$_FILES["file"]["size"] / 1024;
$_FILES["file"]["tmp_name"];
$extension=pathinfo($_FILES['file']['name'],PATHINFO_EXTENSION);
}
//如果附檔名不是xls就跳個訊息給他
if($extension!="xls")
{
    ShowMessageAndRedirect('檔案類型錯誤，僅容許xls檔','/erp/jsadways2/sap_excel.php' , false);
}

if (move_uploaded_file($_FILES["file"]["tmp_name"],"../EXCEL.xls")){
} else {
    exit("Sorry, there was an error uploading your file." . EOL);
}
//因為是excel2003版本，所以crateReader放Excel5，如果是Excel2007就放Excel2007即可
$reader = PHPExcel_IOFactory::createReader('Excel5');
//讀取xls檔案
$PHPExcel = $reader->load(dirname(__DIR__)."/EXCEL.xls");
$sheet = $PHPExcel->getSheet(0); // 讀取第一個工作表
$highestRow = $sheet->getHighestRow(); //count total row
//阿給的範本檔前幾行都標題，所以從第四行取起
for ($row = 4; $row <= $highestRow; $row++) {
    //定義兩個變數來接值，因為流水號在56列，總公司給的編號在57列，所以直接寫死
        $val = $sheet->getCellByColumnAndRow(0, $row)->getValue();
        $val2 = $sheet->getCellByColumnAndRow(4, $row)->getValue();
        if($val2!=""){
            if($val!=""){
                //抓到值之後直接寫sql命令
        $sqlupdate[]="update `cp_detail` set `jpc_seq`=".$val2." where `item_seq`=".$val.";";
            }
        }
}
//寫完之後棄掉相同的，然後重新排序，接著for迴圈做新增
for($num=0; $num<=count(array_values(array_unique($sqlupdate)))-1;$num++)
{
   mysql_query(array_values(array_unique($sqlupdate))[$num]);
}








ShowMessageAndRedirect('更新成功','/erp/jsadways2/sap_excel.php' , false);