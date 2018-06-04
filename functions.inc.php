<?php

function IncludeFunctions($module='')
{
    $module = lcfirst(trim($module));

    $fnFile = __DIR__ ."/{$module}/functions.inc.php";
    $fnFile2 = __DIR__ ."/{$module}/{$module}.inc.php";
    $fnFile3 = __DIR__ ."/includes/{$module}.inc.php";

    if (file_exists($fnFile)) {
        require_once $fnFile;
    } else if (file_exists($fnFile2)) {
        require_once $fnFile2;
    } else if (file_exists($fnFile3)) {
        require_once $fnFile3;
    }
}

function History($message='', $prefix='')
{
    $path = $GLOBALS['env']['log_dir'] .'/'. (empty($prefix) ? '' : "{$prefix}-") . date('Ymd') .'.log';
    $message = sprintf('[%s] ', date('Y-m-d H:i:s')) . $message ."\n";
    error_log($message, 3, $path);
}

function CreateObject($name='')
{
    $name = ucfirst($name);

    $module = $GLOBALS['env']['module_dir'] .'/'. $name .'.php';
    if (file_exists($module)) {
        require_once $module;

        $args = func_get_args();
        
        if (count($args) == 1) {
            return new $name();
        } elseif (count($args) > 1) {
            $code = '$obj = new ' . $name . '(';
            foreach ($args as $n => $arg) {
                if ($n) {
                    $code .= ($n > 1 ? ',' : '') . '$args[' . $n . ']';
                }
            }
            $code .= ');';
            eval($code);
//echo $code;
            return $obj;
        }
    }

    return null;
}

function CreateCrondObject()
{
    if (!property_exists($GLOBALS['app'], 'crond') || !is_object($GLOBALS['app']->crond)) {
        $GLOBALS['app']->crond = CreateObject('Crond');
    }

    return $GLOBALS['app']->crond;
}

function CreateNohupObject()
{
    // if (!property_exists($GLOBALS['app'], 'nohup') || !is_object($GLOBALS['app']->nohup)) {
    //     $GLOBALS['app']->nohup = CreateObject('Nohup');
    // }

    // return $GLOBALS['app']->nohup;
}

function IsId($id=0, $type='number')
{
    if ($type == 'number') {
        return (!empty($id) && is_numeric($id) && $id > 0);
    } else {
        return true;
    }
}

function IsDateNumber($input=0)
{
    return IsId($input, 'number');
}

function StartsWith($haystack, $needle)
{
    $length = strlen($needle);
    return (substr($haystack, 0, $length) === $needle);
}

function EndsWith($haystack, $needle)
{
    $length = strlen($needle);
    if ($length == 0) {
        return true;
    }

    return (substr($haystack, -$length) === $needle);
}

function IsDateString($datetime='0000-00-00 00:00:00')
{
    $patternYYYYmmdd = '/^\d{4}[\-\/]{1}(1[0-2]{1}|0[1-9]{1}|[1-9]{1})[\-\/]{1}(3[0-1]{1}|2[0-9]{1}|1[0-9]{1}|0[1-9]{1}|[1-9]{1})$/';
    $patternYYYmmddHHiiss = '/^\d{4}[\-\/]{1}(1[0-2]{1}|0[1-9]{1}|[1-9]{1})[\-\/]{1}(3[0-1]{1}|2[0-9]{1}|1[0-9]{1}|0[1-9]{1}|[1-9]{1})\s{1}(2[0-3]{1}|1[0-9]{1}|0[0-9]{1}|[0-9]{1}):(5[0-9]{1}|4[0-9]{1}|3[0-9]{1}|2[0-9]{1}|1[0-9]{1}|0[0-9]{1}|[0-9]{1}):(5[0-9]{1}|4[0-9]{1}|3[0-9]{1}|2[0-9]{1}|1[0-9]{1}|0[0-9]{1}|[0-9]{1})$/';
    
    preg_match($patternYYYYmmdd, $datetime, $matchYYYYmmdd);
    preg_match($patternYYYmmddHHiiss, $datetime, $matchYYYYmmddHHiiss);

    return $datetime && strtotime($datetime) > 0 && ($matchYYYYmmdd || $matchYYYYmmddHHiiss);
}

function GetVar($name='', $default=null, $type='any')
{   
    if (strtolower($type) == 'post') {
        return isset($_POST[$name]) ? $_POST[$name] : $default;
    } else if (strtolower($type) == 'get') {
        return isset($_GET[$name]) ? $_GET[$name] : $default;
    }

    return isset($_POST[$name]) ? $_POST[$name] : (isset($_GET[$name]) ? $_GET[$name] : $default);
}

function IsPermitted($app='', $redirect=null, $acl='read', $ignoreSuperVisor=false, $specificUser=0)
{
    if (isset($_REQUEST['accesskey'])) {
        if (!property_exists($GLOBALS['app'], 'crypto') || !is_object($GLOBALS['app']->crypto)) {
            $GLOBALS['app']->crypto = CreateObject('Crypto');
        }

        $postAccesskey = isset($_POST['accesskey']) ? $GLOBALS['app']->crypto->decrypt($_POST['accesskey']) : '';
        $getAccesskey = isset($_GET['accesskey']) ? $GLOBALS['app']->crypto->decrypt(urlencode($_GET['accesskey'])) : '';

        if (in_array('supervisor', [$postAccesskey, $getAccesskey])) {
            return true;
        } else if (IsLocalAddress()) {
            return true;
        } else if (in_array(GetRemoteIpAddress(), [$postAccesskey, $getAccesskey])) {
            return true;
        } else if (in_array($_SERVER['HTTP_USER_AGENT'], [$postAccesskey, $getAccesskey])) {
            return true;
        } else if (isset($_REQUEST['force'])) {
            return true;
        }

        header("HTTP/1.0 404 Not Found");
        echo 'not auth';
        die();
    }

    $userId = empty($specificUser) || !IsId($specificUser) ? $GLOBALS['app']->userid : $specificUser;

    if (empty($userId)) {
        if ($redirect === null) {
            header('Location: ../index.php');
        } else {
            header("Location: $redirect");
        }
        
        die();
    }

    if (!property_exists($GLOBALS['app'], 'mrbsUsers') || !is_object($GLOBALS['app']->mrbsUsers)) {
        $GLOBALS['app']->mrbsUsers = CreateObject('MrbsUsers');
    }

    $deptId = $GLOBALS['app']->departmentid;
    if ($userId != $GLOBALS['app']->userid) {
        $GLOBALS['app']->mrbsUsers->load($userId);
        $deptId = $GLOBALS['app']->mrbsUsers->getVar('departmentid');
    }

    $granted = $GLOBALS['app']->permission->isPermitted($userId, $app, $acl, $ignoreSuperVisor);
    if (empty($granted)) {
        $granted = $GLOBALS['app']->permission->isPermitted(($deptId * -1), $app, $acl, $ignoreSuperVisor);
    }
    if (empty($granted)) {
        $granted = $GLOBALS['app']->permission->isPermitted('all', $app, $acl, $ignoreSuperVisor) || $GLOBALS['app']->permission->isPermitted(0, $app, $acl, $ignoreSuperVisor);
    }

    if (empty($granted) && !empty($redirect)) {
        header('Location: '. $redirect);
        die();
    }

    return $granted;
}

function GetRemoteIpAddress()
{
    if (empty($REMOTE_ADDR)) {
        if (!empty($_SERVER) && isset($_SERVER['REMOTE_ADDR'])) {
            $REMOTE_ADDR = $_SERVER['REMOTE_ADDR'];
        } elseif (!empty($_ENV) && isset($_ENV['REMOTE_ADDR'])) {
            $REMOTE_ADDR = $_ENV['REMOTE_ADDR'];
        } elseif (@getenv('REMOTE_ADDR')) {
            $REMOTE_ADDR = getenv('REMOTE_ADDR');
        }
    }
    if (empty($HTTP_X_FORWARDED_FOR)) {
        if (!empty($_SERVER) && isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $HTTP_X_FORWARDED_FOR = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (!empty($_ENV) && isset($_ENV['HTTP_X_FORWARDED_FOR'])) {
            $HTTP_X_FORWARDED_FOR = $_ENV['HTTP_X_FORWARDED_FOR'];
        } elseif (@getenv('HTTP_X_FORWARDED_FOR')) {
            $HTTP_X_FORWARDED_FOR = getenv('HTTP_X_FORWARDED_FOR');
        }
    }
    if (empty($HTTP_X_FORWARDED)) {
        if (!empty($_SERVER) && isset($_SERVER['HTTP_X_FORWARDED'])) {
            $HTTP_X_FORWARDED = $_SERVER['HTTP_X_FORWARDED'];
        } elseif (!empty($_ENV) && isset($_ENV['HTTP_X_FORWARDED'])) {
            $HTTP_X_FORWARDED = $_ENV['HTTP_X_FORWARDED'];
        } elseif (@getenv('HTTP_X_FORWARDED')) {
            $HTTP_X_FORWARDED = getenv('HTTP_X_FORWARDED');
        }
    }
    if (empty($HTTP_FORWARDED_FOR)) {
        if (!empty($_SERVER) && isset($_SERVER['HTTP_FORWARDED_FOR'])) {
            $HTTP_FORWARDED_FOR = $_SERVER['HTTP_FORWARDED_FOR'];
        } elseif (!empty($_ENV) && isset($_ENV['HTTP_FORWARDED_FOR'])) {
            $HTTP_FORWARDED_FOR = $_ENV['HTTP_FORWARDED_FOR'];
        } elseif (@getenv('HTTP_FORWARDED_FOR')) {
            $HTTP_FORWARDED_FOR = getenv('HTTP_FORWARDED_FOR');
        }
    }
    if (empty($HTTP_FORWARDED)) {
        if (!empty($_SERVER) && isset($_SERVER['HTTP_FORWARDED'])) {
            $HTTP_FORWARDED = $_SERVER['HTTP_FORWARDED'];
        } elseif (!empty($_ENV) && isset($_ENV['HTTP_FORWARDED'])) {
            $HTTP_FORWARDED = $_ENV['HTTP_FORWARDED'];
        } elseif (@getenv('HTTP_FORWARDED')) {
            $HTTP_FORWARDED = getenv('HTTP_FORWARDED');
        }
    }
    if (empty($HTTP_VIA)) {
        if (!empty($_SERVER) && isset($_SERVER['HTTP_VIA'])) {
            $HTTP_VIA = $_SERVER['HTTP_VIA'];
        } elseif (!empty($_ENV) && isset($_ENV['HTTP_VIA'])) {
            $HTTP_VIA = $_ENV['HTTP_VIA'];
        } elseif (@getenv('HTTP_VIA')) {
            $HTTP_VIA = getenv('HTTP_VIA');
        }
    }
    if (empty($HTTP_X_COMING_FROM)) {
        if (!empty($_SERVER) && isset($_SERVER['HTTP_X_COMING_FROM'])) {
            $HTTP_X_COMING_FROM = $_SERVER['HTTP_X_COMING_FROM'];
        } elseif (!empty($_ENV) && isset($_ENV['HTTP_X_COMING_FROM'])) {
            $HTTP_X_COMING_FROM = $_ENV['HTTP_X_COMING_FROM'];
        } elseif (@getenv('HTTP_X_COMING_FROM')) {
            $HTTP_X_COMING_FROM = getenv('HTTP_X_COMING_FROM');
        }
    }
    if (empty($HTTP_COMING_FROM)) {
        if (!empty($_SERVER) && isset($_SERVER['HTTP_COMING_FROM'])) {
            $HTTP_COMING_FROM = $_SERVER['HTTP_COMING_FROM'];
        } elseif (!empty($_ENV) && isset($_ENV['HTTP_COMING_FROM'])) {
            $HTTP_COMING_FROM = $_ENV['HTTP_COMING_FROM'];
        } elseif (@getenv('HTTP_COMING_FROM')) {
            $HTTP_COMING_FROM = getenv('HTTP_COMING_FROM');
        }
    }

    if (!empty($REMOTE_ADDR)) {
        $direct_ip = $REMOTE_ADDR;
    }

    $proxy_ip     = '';
    if (!empty($HTTP_X_FORWARDED_FOR)) {
        $proxy_ip = $HTTP_X_FORWARDED_FOR;
    } elseif (!empty($HTTP_X_FORWARDED)) {
        $proxy_ip = $HTTP_X_FORWARDED;
    } elseif (!empty($HTTP_FORWARDED_FOR)) {
        $proxy_ip = $HTTP_FORWARDED_FOR;
    } elseif (!empty($HTTP_FORWARDED)) {
        $proxy_ip = $HTTP_FORWARDED;
    } elseif (!empty($HTTP_VIA)) {
        $proxy_ip = $HTTP_VIA;
    } elseif (!empty($HTTP_X_COMING_FROM)) {
        $proxy_ip = $HTTP_X_COMING_FROM;
    } elseif (!empty($HTTP_COMING_FROM)) {
        $proxy_ip = $HTTP_COMING_FROM;
    }

    if (empty($proxy_ip)) {
        return $direct_ip;
    } else {
        $is_ip = preg_match('|^([0-9]{1,3}\.){3,3}[0-9]{1,3}|', $proxy_ip, $regs);
        if ($is_ip && (count($regs) > 0)) {
            return $regs[0];
        } else {
            return false;
        }
    }
}

function IsLocalAddress()
{
    return in_array(GetRemoteIpAddress(), ['127.0.0.1', '::1']);
}

function AppendContentToQueue($name='', $data=[], $appendTag='', $executeTime=null)
{
    if (IsUnix()) {
        $crond =& CreateCrondObject();
        $crond->appendData($name, $data, $appendTag, $executeTime);
    } else {
        $newCreated = false;
        
        $queueFile = __DIR__ .'/queue/'. $name;
    
        if (!file_exists($queueFile)) {
            if ($executeTime && is_numeric($executeTime)) {
                $data['time'] = $executeTime;
            }
    
            touch($queueFile);
            $queueWritter = fopen($queueFile, 'w');
            fwrite($queueWritter, json_encode($data));
            fclose($queueWritter);
            
            $newCreated = true;
        }
    
        $queueReader = fopen($queueFile, 'r');
        $queueData = fread($queueReader, filesize($queueFile));
        fclose($queueReader);
    
        if ($queueData) {
            $queueData = json_decode($queueData, true);
        }
    
        if ($executeTime && is_numeric($executeTime)) {
            $queueData['time'] = $executeTime;
        }
    
        if (!$newCreated && array_key_exists($appendTag, $queueData)) {
            $queueData[$appendTag] .= $data[$appendTag];
        }
    
        $queueWritter = fopen($queueFile, 'w');
        fwrite($queueWritter, json_encode($queueData));
        fclose($queueWritter);
    
        $message = "queue name: {$name}, data: \n". json_encode($data);
        History($message, 'Queue-Append-'. $name);
    }
}

function AddMailToQueue($to, $recipient, $content, $subject, $sender, $cc=[], $reply=[], $onlyReturnData=false)
{
    $queueData = [
        'to' => $to,
        'recipient' => $recipient,
        'subject' => $subject,
        'content' => $content,
        'sender' => $sender,
        'cc' => $cc,
        'reply' => $reply
    ];

    if ($onlyReturnData === false) {
        $message = "Subject: {$subject}";
        AddTaskToQueue('send-mail', $queueData, 'Mail-Record', $message);
    }

    return $queueData;
}

function AddTaskToQueue($task='', $data=[], $historyPrefix='', $customMessage='')
{
    if ($historyPrefix !== false) {
        $message = empty($customMessage) ? ('Add Queue, task: '. $task) : $customMessage;
        $message .=  ", data:\n". json_encode($data);
        History($message, $historyPrefix);
    }

    $md5RndOrigin = microtime(true) . chr(rand(48, 122)) . rand(1, 99999) . chr(rand(48, 122));
    $md5RndCode = substr(md5($md5RndOrigin), 0, 16);
    $queueName = date('Y-m-d') .'.'. $md5RndCode;
    $queueFile = __DIR__ .'/queue/'. $queueName;
	
    $queueData = $data;
    $queueData['type'] = $task;

    switch ($task) {
        case 'mail':
        case 'send-mail':
        case 'append-timecard':
        case 'sync-punch':
        case 'update-punch-status':
        case 'thumb-file':
        case 'blogger-exception':
        case 'lock-receipt-effective':
            break;
        default:
            return null;
    }

    touch($queueFile);
    $queueWritter = fopen($queueFile, 'w');
    fwrite($queueWritter, json_encode($queueData));
    fclose($queueWritter);

    if (IsUnix() && empty($GLOBALS['env']['development'])) {
        $nohup =& CreateNohupObject();
        $nohup->exec("{$GLOBALS['env']['portal_url']}/queue.php", ['id' => $queueName]);
    }

    return true;
}

function ExecuteLocalAdapter($path='', $postData=[])
{
    $url = $GLOBALS['env']['portal_url'] .'/'. $path;
    $postData['accesskey'] = 'UJa629slGhu3ijW3nrXC3x1Tot%2BlmXFVROmHGQkT%2BRI%3D';

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);

    print_r($response);
    curl_close($ch);

    return true;
}

function ShowMessageAndRedirect($message='', $path='', $alert=true)
{
    $refer = empty($path) ? 'home.php' : $path;

    $twig = CreateObject('Twig', __DIR__ .'/public/template', 'message.html', [
        'flag' => $GLOBALS['env']['flag'],
        'alert' => $alert,
        'refer' => $refer,
        'message' => $message
    ]);

    echo $twig->display();
    exit;
}

function ShowMessageAndClose($message='')
{
    $html = '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
            <script>
                alert("'. $message .'");
                close();
            </script>';
    die($html);
}

function FileReset($input=[])
{
    $attachment = [];

    foreach ($input['name'] as $order => $name) {
        if (!empty($name) && !empty($input['size'][$order]) && empty($input['error'][$order])) {
            $attachment[] = [
                'name' => $name,
                'type' => $input['type'][$order],
                'tmp_name' => $input['tmp_name'][$order],
                'error' => $input['error'][$order],
                'size' => $input['size'][$order]
            ];
        }
    }

    return $attachment;
}

function DownloadFile($filename='', $filepath='', $method='attachment')
{
    if ($filename && file_exists($filepath)) {
        while (@ob_end_clean());

        if(ini_get('zlib.output_compression')) {
            ini_set('zlib.output_compression', 'Off');
        }
        
        header("Pragma: public");

        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Cache-Control: private",false);
        header( "Content-disposition: $method; filename=\"".addslashes($filename)."\"; filename*=utf-8''".rawurlencode($filename));

        header("Content-Transfer-Encoding: binary");
        header("Content-length: " . filesize($filepath));
        ob_clean();
        flush();
        readfile($filepath);

        die();
    }
}

function GetCurrentUserId()
{
    return $GLOBALS['app']->userid;
}

function RedirectLink($path='')
{
    if ($path) {
        header("Location: $path");
        exit;
    }
}

function SqlQuote($string='', $like=false)
{
    if ($like) {
        $string = "%{$string}%";
    }
    
    return $GLOBALS['app']->db->quote($string);
}

function GenSqlFromArray($array=[], $tableName='', $type='insert', $where=[])
{
    $statement = '';

    if ($tableName) {
        if ($type == 'insert') {
            $fieldsName = [];
	        $fieldsVar = [];

            foreach ($array as $dataName => $dataVar) {
                $fieldsName[] = "`{$dataName}`";
                $fieldsVar[] = $GLOBALS['app']->db->quote($dataVar);
            }

            if (count($fieldsName) && count($fieldsName) == count($fieldsVar)) {
                $statement = "INSERT INTO `{$tableName}` (". implode(', ', $fieldsName) .") VALUES (". implode(', ', $fieldsVar) .");";
            }
        } else if ($type == 'update') {
            $fieldsStatement = [];

            foreach ($array as $dataName => $dataVar) {
                if (StartsWith($dataVar, '`') && EndsWith($dataVar, '`')) {
                    $fieldsStatement[] = "`{$dataName}` = `{$$dataVar}`";
                } else {
                    $fieldsStatement[] = "`{$dataName}` = ". $GLOBALS['app']->db->quote($dataVar);
                }
            }

            if (count($fieldsStatement)) {
                $statement = "UPDATE `{$tableName}` SET ". implode(', ', $fieldsStatement);

                $conditions = [];

                if (count($where)) {
                    foreach ($where as $dataName => $dataVar) {
                        $conditions[] = "`{$dataName}` = ". $GLOBALS['app']->db->quote($dataVar);
                    }
                }

                if (count($conditions)) {
                    $statement .= " WHERE ". implode(' AND ', $conditions);
                }
            }
        } else if ($type == 'select') {
            $where = empty($where) ? '*' : $where;
            $statement = "SELECT {$where} FROM `{$tableName}` WHERE 1=1 ";
            
            $conditions = [];

            if (count($array)) {
                foreach ($array as $dataName => $dataVar) {
                    $conditions[] = "`{$dataName}` = ". $GLOBALS['app']->db->quote($dataVar);
                }
            }

            if (count($conditions)) {
                $statement .= " AND ". implode(' AND ', $conditions);
            }
        }
    }

    return $statement;
}

function PrintJsonData($data=[], $jsonType=false)
{
    if (!is_array($data)) {
        $data = [$data];
    }

    if ($jsonType) {
        header('Content-Type: application/json; charset=utf-8');
    }

    echo json_encode($data);
    exit;
}

function IsHoliday($date='')
{
    $isHoliday = null;
    $SundayIdentify = 0;
    $SaturdayIdentify = 6;

    if (IsDateString($date)) {
        $stamp = strtotime($date);
        $year = (int)date('Y', $stamp);
        $month = (int)date('m', $stamp);
        $day = (int)date('d', $stamp);
        $week = (int)date('w', $stamp);

        if (!property_exists($GLOBALS['app'], 'holidays') || !is_object($GLOBALS['app']->holidays)) {
            $GLOBALS['app']->holidays = CreateObject('Holidays');
        }

        $rowsHoliday = $GLOBALS['app']->holidays->getDataAssocDay($year, $month);
        $rowsShift = $GLOBALS['app']->holidays->getShiftDataAssocDay($year, $month);

        $isHoliday = isset($rowsHoliday[$day]) || (($SundayIdentify == $week || $SaturdayIdentify == $week) && !isset($rowsShift[$day]));
    }
    
    return $isHoliday;
}

function GetUsedMediaOrdinal($condition=0, $type='id', $specificMediaOrdinal=0, $nativeStatement=false)
{
    if ($type == 'id') {
        if (IsId($condition)) {
            $condition = [$condition];
        }

        if (is_array($condition)) {
            foreach ($condition as $order => $id) {
                if (!IsId($id)) {
                    unset($condition[$order]);
                }
            }
        }

        if (count($condition)) {
            $condition = implode(',', $condition);
        } else {
            return null;
        }
    } else if ($type == 'sql') {

    } else {
        return null;
    }

    if (IsId($specificMediaOrdinal)) {
        $specificMediaOrdinal = [$specificMediaOrdinal];
    }

    if (is_array($specificMediaOrdinal)) {
        foreach ($specificMediaOrdinal as $idx => $ordinal) {
            if (!IsId($ordinal)) {
                unset($specificMediaOrdinal[$idx]);
            }
        }
    }

    $ordinal = [];
    $sql = 'SELECT `map_media_ordinal` FROM media_map WHERE map_campaign IN ('. $condition .')';
    if (is_array($specificMediaOrdinal) && count($specificMediaOrdinal)) {
        $sql .= " AND `map_media_ordinal` IN (". implode(', ', $specificMediaOrdinal).")";
    }
    $sql .= " GROUP BY `map_media_ordinal`;";
    if ($nativeStatement) {
        $result = mysql_query($sql);

        if (mysql_num_rows($result) > 0) {
            while ($item = mysql_fetch_array($result)) {
                array_push($ordinal, $item['map_media_ordinal']);
            }
        }
    } else {
        $db = clone($GLOBALS['app']->db);
        $db->query($sql);

        while ($item = $db->next_record()) {
            array_push($ordinal, $item['map_media_ordinal']);
        }
    
        unset($db);
    }
    
    return $ordinal;
}

function PrintHelloMessage()
{
    $db = clone($GLOBALS['app']->db);

    $sql = sprintf('SELECT * FROM  message WHERE id = %d;', rand(1, 369));
    $db->query($sql);
    $item = $db->next_record();

    unset($db);

    $title = $_SESSION['sex'] == '男' ? '哥' : '姊';

    echo '<a class="nameLink">Hi! '. $_SESSION['name'] . $title .'~~ '. $item['message'] .'</a>';
}

function GetChineseWeekNumber($week=0)
{
    switch ($week) {
        case 0:
            return '日';
        case 1:
            return '一';
        case 2:
            return '二';
        case 3:
            return '三';
        case 4:
            return '四';
        case 5:
            return '五';
        case 6:
            return '六';
        case 7:
            return '日';
    }

    return '';
}

function GetValidDate(&$specificYear, &$specificMonth, &$specificDay, &$specificStamp)
{
    $specificDate = sprintf('%04d-%02d-%02d', $specificYear, $specificMonth, $specificDay);
    $specificStamp = IsDateString($specificDate) ? strtotime($specificDate) : null;

    if ($specificStamp === null) {
        $specificStamp = time();
    }

    $specificYear = date('Y', $specificStamp);
    $specificMonth = date('m', $specificStamp);
    $specificDay = date('d', $specificStamp);

    return date('Y-m-d', $specificStamp);
}

function AddMonthToDate($range=0, $date=0, $returnStamp=false)
{
    $originStamp = IsDateString($date) ? strtotime($date) : (is_numeric($date) && $date > 0 ? $date : time());
    $originDate = date('Y-m-d H:i:s', $originStamp);
    $originYear = date('Y', $originStamp);
    $originMonth = date('m', $originStamp);
    $originDay = date('d', $originStamp);
    $originHour = date('H', $originStamp);
    $originMinute = date('i', $originStamp);
    $originSecond = date('s', $originStamp);

    $newDay = $originDay;

    $newStamp = 0;
    if ($range && is_numeric($range)) {
        $newYear = $originYear;
        $newMonth = $originMonth + $range;

        if ($newMonth > 12) {
            if ($newMonth % 12) {
                $newYear = $originYear + ((int)($newMonth / 12));
                $newMonth = $newMonth % 12;
            } else {
                $newYear = $originYear + ((int)($newMonth / 12)) - 1;
                $newMonth = $newMonth % 12;
                $newMonth = empty($newMonth) ? $originMonth : $newMonth;    
            }
        } else if ($newMonth < 1) {
            $newMonth = $originMonth - (abs($range) % 12);
            $newMonth += $newMonth <= 0 ? 12 : 0;
            $newYear = $originYear - 1 - ((int)(abs($range) / 12));
        } else if ($newMonth > 0) {
            $newYear = $originYear;
        }
        
        $newMontLastDay = date('t', strtotime("{$newYear}-{$newMonth}-1"));

        if ($newDay > $newMontLastDay) {
            $newDay = $newMontLastDay;
        }
    }
    
    if (empty((int)$originHour) && empty((int)$originMinute) && empty((int)$originSecond)) {
        $string = sprintf('%04d-%02d-%02d', $newYear, $newMonth, $newDay);
    } else {
        $string = sprintf('%04d-%02d-%02d %02d:%02d:%02d', $newYear, $newMonth, $newDay, $originHour, $originMinute, $originSecond);
    }

    return $returnStamp === true ?  strtotime($string) : $string;
}

function GetMedia($mediaId=0)
{
    global $mediaCache;

    if (empty($mediaCache)) {
        $db = clone($GLOBALS['app']->db);

        $rowsMedia = [];

        $db->query("SELECT * FROM media WHERE id > 0;");
        while ($itemMedia = $db->next_record()) {
            $rowsMedia[$itemMedia['id']] = $itemMedia;
        }

        $mediaCache = $rowsMedia;
    }

    if (is_numeric($mediaId) && $mediaId > 0) {
        return isset($mediaCache[$mediaId]) ? $mediaCache[$mediaId] : [];
    }

    return [];
}

function GetUsersInfo($perm='')
{
    if (!property_exists($GLOBALS['app'], 'mrbsUsers') || !is_object($GLOBALS['app']->mrbsUsers)) {
        $GLOBALS['app']->mrbsUsers = CreateObject('MrbsUsers');
    }

    if (isset($GLOBALS['app']->permission->data[$perm]) && is_array($GLOBALS['app']->permission->data[$perm]) && $GLOBALS['app']->permission->data[$perm]) {
        return $GLOBALS['app']->mrbsUsers->searchAll([
            sprintf("`id` IN (%s)", implode(', ', array_keys($GLOBALS['app']->permission->data[$perm])))
        ], '', '', '', '', 'id, name, username, email');
    }

    return [];
}

function CreateNativeDBConnector()
{
    ini_set('memory_limit', '-1');
    ini_set('max_execution_time', "600");
    date_default_timezone_set('Asia/Taipei');

    if (empty(mysql_connect($GLOBALS['env']['db_host'], $GLOBALS['env']['db_user'], $GLOBALS['env']['db_password']))) {
        die('Could not connect: ' . mysql_error());
    }

    mysql_query("SET NAMES 'UTF8'");
	mysql_select_db($GLOBALS['env']['db_name']);
}

function ChangeDateYmdTomdY($date='', $separator='-')
{
    if (IsDateString($date)) {
        $stamp = strtotime($date);
        return date("m{$separator}d{$separator}Y", $stamp);
    }
    
    return '';
}

function GetFileExtension($name='')
{
    return pathinfo($name, PATHINFO_EXTENSION);
}

function IsUnix()
{
    $serverOS = strtoupper(PHP_OS);
    return strpos($serverOS, 'LINUX') !== false || strpos($serverOS, 'DARWIN') !== false;
}

if (!function_exists('IsWindowsBrowser')) {
    function IsWindowsBrowser()
    {
        return strpos(strtolower($_SERVER['HTTP_USER_AGENT']), "/windows ") !== false;
    }
}

function RemoveEmoji($string='')
{
    if (is_string($string)) {
        // Match Emoticons
        $regex_emoticons = '/[\x{1F600}-\x{1F64F}]/u';
        $clear_string = preg_replace($regex_emoticons, '', $string);

        // Match Miscellaneous Symbols and Pictographs
        $regex_symbols = '/[\x{1F300}-\x{1F5FF}]/u';
        $clear_string = preg_replace($regex_symbols, '', $clear_string);

        // Match Transport And Map Symbols
        $regex_transport = '/[\x{1F680}-\x{1F6FF}]/u';
        $clear_string = preg_replace($regex_transport, '', $clear_string);

        // Match Miscellaneous Symbols
        $regex_misc = '/[\x{2600}-\x{26FF}]/u';
        $clear_string = preg_replace($regex_misc, '', $clear_string);

        // Match Dingbats
        $regex_dingbats = '/[\x{2700}-\x{27BF}]/u';
        $clear_string = preg_replace($regex_dingbats, '', $clear_string);

        return $clear_string;
    }

    return $string;
}

function StrPosWithArray($haystack, $needles=[])
{
    if (is_array($needles) && count($needles)) {
        foreach ($needles as $nle) {
            if (strpos($haystack, $nle) === false) {
                continue;
            }

            return true;
        }
    }

    return false;
}

function DisplaySlogan()
{
    $objMessage = CreateObject('Message');

    $title = $_SESSION['nickname'];
    if (empty($_SESSION['nickname'])) {
        $title = ucfirst($_SESSION['name']) . ($_SESSION['sex'] == '男' ? '哥' : '姊');
    }
    
    $sloganHTML = '<nav><div class="name"><ul class="navigation"><li><a>Hi '. $title .' ~~&nbsp;&nbsp;'. $objMessage->getRandomMessage() .'</a></li></ul></div></nav>';
    echo $sloganHTML;

    unset($objMessage);
}

function isWindowsPlatform()
{
    $userAgent = strtolower($_SERVER['HTTP_USER_AGENT']);
    return strpos($userAgent, "windows ") !== false;
}
//Jackie 2018/05/31自動流水編碼 對應table is total_seq;
//Jackie 2018/05/31自動流水編碼 對應table is total_seq;
//Jackie 2018/05/31自動流水編碼 對應table is total_seq;
function autoSerialNumber()
{
    try{
        include("include/db.inc.php");
        //Jackie 2018/05/31自動流水編碼 對應table is total_seq;
        //Jackie 2018/05/31自動流水編碼 對應table is total_seq;
        //Jackie 2018/05/31自動流水編碼 對應table is total_seq;
        $sqlread="select seq_id from total_seq";
        //Jackie 2018/05/31自動流水編碼 對應table is total_seq;
        //Jackie 2018/05/31自動流水編碼 對應table is total_seq;
        //Jackie 2018/05/31自動流水編碼 對應table is total_seq;
        $result=mysql_query($sqlread);
        //Jackie 2018/05/31自動流水編碼 對應table is total_seq;
        //Jackie 2018/05/31自動流水編碼 對應table is total_seq;
        //Jackie 2018/05/31自動流水編碼 對應table is total_seq;
        $count=mysql_fetch_assoc($result);
        //Jackie 2018/05/31自動流水編碼 對應table is total_seq;
        //Jackie 2018/05/31自動流水編碼 對應table is total_seq;
        //Jackie 2018/05/31自動流水編碼 對應table is total_seq;
        $count2=$count['seq_id']+1;
        //Jackie 2018/05/31自動流水編碼 對應table is total_seq;
        //Jackie 2018/05/31自動流水編碼 對應table is total_seq;
        //Jackie 2018/05/31自動流水編碼 對應table is total_seq;
        $sqlupdate="update total_seq set seq_id ='".$count2."' where seq_id='".$count['seq_id']."'";
        //Jackie 2018/05/31自動流水編碼 對應table is total_seq;
        //Jackie 2018/05/31自動流水編碼 對應table is total_seq;
        //Jackie 2018/05/31自動流水編碼 對應table is total_seq;
        mysql_query($sqlupdate);
        //Jackie 2018/05/31自動流水編碼 對應table is total_seq;
        //Jackie 2018/05/31自動流水編碼 對應table is total_seq;
        //Jackie 2018/05/31自動流水編碼 對應table is total_seq;
		return $count['seq_id'];
    }catch(Exception $e)
    {
        throw $e;
    }
}