<?php

$datedate = '04/21~04/21';
$cpm225_datedate = '04/21~04/2104/21~04/2104/21~04/2104/22~04/22';
echo gettype(strpos($cpm225_datedate,$datedate)).'<br>';
if(strpos($cpm225_datedate,$datedate)){
	echo 1;

}else{
	echo 2;
}



$str1 = $cpm225_datedate;
$str2 = $datedate;

// $str1 = 'http://test/test.php?var=123';
// $str2 = '/test/test.php';
if (false !== ($rst = strpos($str1, $str2))) {
    echo 'find : '.$rst; // 印出 find : 6
} else {
    echo 'not find'; // 若不存在, 則印出 not find
}
?>