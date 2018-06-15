<?php
    //防止sql注入
	function quotes($content){
	    if(!get_magic_quotes_gpc())
		{
			if (is_array($content)) {
				foreach ($content as $key=>$value) {
					$content[$key] = addslashes($value);
				}
			} else {
				$content = addslashes($content);
			}
		} 
		return $content;
	}

	if (!function_exists('startsWith')) {
		function startsWith($haystack, $needle)
		{
			$length = strlen($needle);
			return (substr($haystack, 0, $length) === $needle);
		}
	}

	function loadMedia($mediaId=0)
	{
		// 2017-05-08 (Jimmy): loading media data to cache
		global $mediaCache;

		if (empty($mediaCache)) {
			$rowsMedia = [];
			$sqlMedia = "SELECT * FROM media WHERE id > 0;";
			$resultMedia = mysql_query($sqlMedia);
			while ($itemMedia = mysql_fetch_array($resultMedia)) {
				$rowsMedia[$itemMedia['id']] = $itemMedia;
			}

			$mediaCache = $rowsMedia;
		}

		if (is_numeric($mediaId) && $mediaId > 0) {
			return isset($mediaCache[$mediaId]) ? $mediaCache[$mediaId] : [];
		}

		return $mediaCache;
	}

	function loadAgency($agencyId=0, $fields='*')
	{
		// 2017-05-08 (Jimmy): loading agency data to cache
		global $agencyCache;

		if (empty($agencyCache)) {
			$rowsAgency = [];
			$sqlAgency = "SELECT ". $fields ." FROM agency;";
			$resultAgency = mysql_query($sqlAgency);
			while ($itemAgency = mysql_fetch_array($resultAgency)) {
				$rowsAgency[$itemAgency['id']] = $itemAgency;
			}

			$agencyCache = $rowsAgency;
		}

		if (is_numeric($agencyId) && $agencyId > 0) {
			return isset($agencyCache[$agencyId]) ? $agencyCache[$agencyId] : [];
		}

		return $agencyCache;
	}

	function loadClient($clientId=0, $fields='*')
	{
		// 2017-05-18 (Jimmy): loading client data to cache
		global $clientCache;

		if (empty($clientCache)) {
			$rowsClient = [];
			$sqlClient = "SELECT ". $fields ." FROM client;";
			$resultClient = mysql_query($sqlClient);
			while ($itemClient = mysql_fetch_array($resultClient)) {
				$rowsClient[$itemClient['id']] = $itemClient;
			}

			$clientCache = $rowsClient;
		}

		if (is_numeric($clientId) && $clientId > 0) {
			return isset($clientCache[$clientId]) ? $clientCache[$clientId] : [];
		}

		return $clientCache;
	}

	if (!function_exists('IsId')) {
		function IsId($id=0)
		{
			return is_numeric($id) && $id > 0;
		}
	}

	function AddMediaMapping($mediaFile='', $campaignId=0, $mediaId=0)
	{
		$mediaType = is_numeric($mediaFile) ? $mediaFile : basename($mediaFile, '_add2.php');
		$mediaOrdinal = preg_match('/^media\d+$/', $mediaType) ? str_replace('media', '', $mediaType) : (is_numeric($mediaFile) ? $mediaFile : 0);

		if (IsId($mediaOrdinal) && IsId($campaignId) && IsId($mediaId)) {
			$sqlTotal = sprintf("SELECT COUNT(*) as `total` FROM media_map 
								WHERE `map_media_ordinal` = %d 
								AND `map_campaign` = %d 
								AND `map_media_id` = %d ;", $mediaOrdinal, $campaignId, $mediaId);

			if (is_object($GLOBALS['app']->db)) {
				$GLOBALS['app']->db->query($sqlTotal);
				$item = $GLOBALS['app']->db->next_record();

				if (empty($item['total'])) {
					$sqlInsert = sprintf("INSERT INTO `media_map` (`map_id`, `map_campaign`, `map_media_ordinal`, `map_media_id`) VALUES (NULL, %d, %d, %d);", $campaignId, $mediaOrdinal, $mediaId);
					$GLOBALS['app']->db->query($sqlInsert);

					return true;
				}
			} else {
				$result = mysql_query($sqlTotal);
				$item = mysql_fetch_array($result);

				if (empty($item['total'])) {
					$sqlInsert = sprintf("INSERT INTO `media_map` (`map_id`, `map_campaign`, `map_media_ordinal`, `map_media_id`) VALUES (NULL, %d, %d, %d);", $campaignId, $mediaOrdinal, $mediaId);
					mysql_query($sqlInsert);

					return true;
				}
			}
		}

		return false;
	}

	function RemoveMediaMapping($mediaOrdinal=0, $campaignId=0, $mediaId=0)
	{
		if (IsId($mediaOrdinal) && IsId($campaignId) && IsId($mediaId)) {
			$sqlRemove = sprintf("DELETE FROM `media_map` 
									WHERE `map_media_ordinal` = %d 
									AND `map_campaign` = %d 
									AND `map_media_id` = %d ;", $mediaOrdinal, $campaignId, $mediaId);
									
			if (is_object($GLOBALS['app']->db)) {
				$GLOBALS['app']->db->query($sqlRemove);
			} else {
				mysql_query($sqlRemove);
			}

			return true;
		}

		return false;
	}

	function GetCampaignByMediaOrdinal($ordinal=0)
	{
		$campaignId = [];

		if (!is_array($ordinal)) {
			$ordinal = [$ordinal];
		}

		foreach ($ordinal as $order => $id) {
			if (!IsId($id)) {
				unset($ordinal[$order]);
			}
		}

		if (count($ordinal)) {
			$sql = 'SELECT `map_campaign`, `map_media_ordinal` FROM `media_map` WHERE `map_media_ordinal` IN ('. implode(', ', $ordinal) .') GROUP BY `map_campaign`;';
			$result = mysql_query($sql);

			if (mysql_num_rows($result) > 0) {
				while ($item = mysql_fetch_array($result)) {
					$campaignId[$item['map_media_ordinal']][] = $item['map_campaign'];
				}
			}
		}

		return $campaignId;
	}

	if (!function_exists('GetUsedMediaOrdinal')) {
		function GetUsedMediaOrdinal($condition=0, $type='id', $specificMediaOrdinal=0)
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

			$sql = 'SELECT `map_media_ordinal` FROM media_map WHERE map_campaign IN ('. $condition .') GROUP BY `map_media_ordinal`;';
			$result = mysql_query($sql);

			if (mysql_num_rows($result) > 0) {
				$ordinal = [];

				while ($item = mysql_fetch_array($result)) {
					array_push($ordinal, $item['map_media_ordinal']);
				}

				return $ordinal;
			}

			return [];
		}
	}

	//Jackie 2018/05/31自動流水編碼 對應table is total_seq;
	function autoSerialNumber()
	{
	    try{
	        include("db.inc.php");
	        $sqlRead="select seq_id from total_seq";
	        $result=mysql_query($sqlRead);
	        $count=mysql_fetch_assoc($result);

	        $count2=$count['seq_id']+1;
	        $sqlUpdate="update total_seq set seq_id ='".$count2."'";
	        mysql_query($sqlUpdate);

			return $count['seq_id'];
	    }catch(Exception $e){
	        throw $e;
	    }
	}

	//ken,2018/6/13,改寫底層的function.inc.php的GetMedia函數,避開讀取media table,改讀取cp_detail
	function GetMediaNew($cp_Id,$media_Id,$mtype_number,$cue=1)
	{
		try{
	        include("db.inc.php");
	        $sqlRead="SELECT t.name, c.mtype_name as costper, m.typename
						FROM cp_detail c
						left join items t on t.id = c.item_id
						left join media m on m.sortid = c.mtype_number
						WHERE c.cp_id=".$campaignId.
						" and c.media_id=".$media_id.
						" and c.mtype_number=".$mtype_number.
						" and c.cue=".$cue;
	        $result=mysql_query($sqlRead);

			return $result;
	    }catch(Exception $e){
	        throw $e;
	    }
	}

		