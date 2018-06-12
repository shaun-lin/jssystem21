<!-- left menu starts -->
<?php

    require_once dirname(dirname(__DIR__)) .'/classes/Permission.php';
    
    $objPermission = new Permission();    
    $listCampaignGroup = $objPermission->getData('backend_campaign_list_group', $_SESSION['userid']);

    $isHKPlatform = strpos($_SERVER['REQUEST_URI'], '/jsadways2hk/') !== false || isset($_REQUEST['HK']) || isset($_REQUEST['hk']);
    $isFFPlatform = strpos($_SERVER['REQUEST_URI'], '/jsadways2ff/') !== false || isset($_REQUEST['FF']) || isset($_REQUEST['ff']);
    $isJSPlatform = !$isHKPlatform && !$isFFPlatform;

?>
<div class="span2 main-menu-span">
	<div class="well nav-collapse sidebar-nav">
		<ul class="nav nav-tabs nav-stacked main-menu">
            <li class="nav-header hidden-tablet">新增案件</li>
            <li><a class="ajax-link" href="campaign_add.php"><i class="icon-edit"></i><span class="hidden-tablet"> 新增個人案件</span></a></li>
            <li><a class="ajax-link" href="campaign_list.php"><i class="icon-folder-open"></i><span class="hidden-tablet"> 個人案件列表</span></a></li>
            <li class="nav-header hidden-tablet">3.0新增功能</li>
            <li><a class="ajax-link" href="medias_list.php"><i class="icon-folder-open"></i><span class="hidden-tablet">媒體維護作業</span></a></li>
            <li><a class="ajax-link" href="companies_list.php"><i class="icon-folder-open"></i><span class="hidden-tablet">公司維護作業</span></a></li>
            <li><a class="ajax-link" href="item_list.php"><i class="icon-folder-open"></i><span class="hidden-tablet">品項維護作業</span></a></li>
            <li><a class="ajax-link" href="mtype_list.php"><i class="icon-folder-open"></i><span class="hidden-tablet">模板維護作業</span></a></li>
            <li><a class="ajax-link" href="campaign_report.php"><i class="icon-folder-open"></i><span class="hidden-tablet">SAP成本表</span></a></li>
            <li><a class="ajax-link" href="sap_excel.php"><i class="icon-folder-open"></i><span class="hidden-tablet">總公司編號匯入</span></a></li>
            
            <? if ($listCampaignGroup) : ?>
                <li><a class="ajax-link" href="campaign_listall.php?group"><i class="icon-folder-open"></i><span class="hidden-tablet"> 組員案件列表</span></a></li>
            <? endif; ?>

      		<? if ($_SESSION['usergroup'] == 1) : ?>
       		    <li class="nav-header hidden-tablet"></li>
            <? endif; ?>

			<? if ($_SESSION['usergroup'] == 2 && $_SESSION['name'] != 'nana') : ?>
                <li class="nav-header hidden-tablet">業務部</li>
                <li><a class="ajax-link" href="campaign_list.php?status=1"><i class="icon-folder-open"></i><span class="hidden-tablet"> 個人案件列表-未送審(<span class="badge-num status11"><i class="fa fa-spin fa-circle-o-notch"></i></span>)</span></a></li>
                <li><a class="ajax-link" href="campaign_list.php?status=2"><i class="icon-folder-open"></i><span class="hidden-tablet"> 個人案件列表-送審中(<span class="badge-num status22"><i class="fa fa-spin fa-circle-o-notch"></i></span>)</span></a></li>
                <li><a class="ajax-link" href="campaign_list.php?status=3"><i class="icon-folder-open"></i><span class="hidden-tablet"> 個人案件列表-執行中(<span class="badge-num status33"><i class="fa fa-spin fa-circle-o-notch"></i></span>)</span></a></li>
                <li><a class="ajax-link" href="campaign_list.php?status=5"><i class="icon-folder-open"></i><span class="hidden-tablet"> 個人案件列表-暫停(<span class="badge-num status55"><i class="fa fa-spin fa-circle-o-notch"></i></span>)</span></a></li>
                <li><a class="ajax-link" href="campaign_list.php?status=7"><i class="icon-folder-open"></i><span class="hidden-tablet"> 個人案件列表-異常(<span class="badge-num status77"><i class="fa fa-spin fa-circle-o-notch"></i></span>)</span></a></li>
                <li><a class="ajax-link" href="campaign_list.php?status=4"><i class="icon-folder-open"></i><span class="hidden-tablet"> 個人案件列表-結案(<span class="badge-num status44"><i class="fa fa-spin fa-circle-o-notch"></i></span>)</span></a></li>
                <li><a class="ajax-link" href="campaign_listall4.php?"><i class="icon-folder-open"></i><span class="hidden-tablet">月報表</span></a></li>
                <li><a class="ajax-link" href="appdriver_list.php"><i class="icon-eye-open"></i><span class="hidden-tablet"> Appdriver</span></a></li>
            <? endif; ?>

            <? if ($_SESSION['usergroup'] == 3 || $_SESSION['name'] == 'nana') : ?>
                <li class="nav-header hidden-tablet">專案管理部門</li>
                <li><a class="ajax-link" href="campaign_listall.php"><i class="icon-folder-open"></i><span class="hidden-tablet"> 全部案件列表</span></a></li>
                <li><a class="ajax-link" href="campaign_listall.php?status=2"><i class="icon-folder-open"></i><span class="hidden-tablet"> 送審中案件列表(<span class="badge-num status2"><i class="fa fa-spin fa-circle-o-notch"></i></span>)</span></a></li>
                <li><a class="ajax-link" href="campaign_listall.php?status=3"><i class="icon-folder-open"></i><span class="hidden-tablet"> 執行中案件列表(<span class="badge-num status3"><i class="fa fa-spin fa-circle-o-notch"></i></span>)</span></a></li>
                <li><a class="ajax-link" href="campaign_listall.php?status=5"><i class="icon-ban-circle"></i><span class="hidden-tablet"> 暫停案件列表(<span class="badge-num status5"><i class="fa fa-spin fa-circle-o-notch"></i></span>)</span></a></li>
                <li><a class="ajax-link" href="campaign_listall.php?status=7"><i class="icon-ban-circle"></i><span class="hidden-tablet"> 異常案件列表(<span class="badge-num status7"><i class="fa fa-spin fa-circle-o-notch"></i></span>)</span></a></li>
                <li><a class="ajax-link" href="campaign_listall.php?status=4"><i class="icon-folder-open"></i><span class="hidden-tablet"> 結案案件列表(<span class="badge-num status4"><i class="fa fa-spin fa-circle-o-notch"></i></span>)</span></a></li>
                <li><a class="ajax-link" href="campaign_listall.php?status=9"><i class="icon-ban-circle"></i><span class="hidden-tablet"> 待審作廢案件列表(<span class="badge-num status9"><i class="fa fa-spin fa-circle-o-notch"></i></span>)</span></a></li>
                <li><a class="ajax-link" href="campaign_listall.php?status=8"><i class="icon-ban-circle"></i><span class="hidden-tablet"> 作廢案件列表(<span class="badge-num status6"><i class="fa fa-spin fa-circle-o-notch"></i></span>)</span></a></li>
                <li><a class="ajax-link" href="campaign_listall.php?status=90"><i class="icon-folder-open"></i><span class="hidden-tablet"> 外匯調整案件列表(<span class="badge-num status8"><i class="fa fa-spin fa-circle-o-notch"></i></span>)</span></a></li>
                <li><a class="ajax-link" href="campaign_listall3.php"><i class="icon-folder-open"></i><span class="hidden-tablet"> 月報表</span></a></li>
                <li><a class="ajax-link" href="campaign_bloger.php"><i class="icon-folder-open"></i><span class="hidden-tablet"> 寫手出帳表</span></a></li>
                <li><a class="ajax-link" href="blogger_bank_list.php"><i class="icon-folder-open"></i><span class="hidden-tablet"> 寫手銀行資訊表</span></a></li>
            <? endif; ?>
                  
            <? if ($_SESSION['name'] == 'nana') : ?>
                <li><a class="ajax-link" href="receipt_list.php?status=0"><i class="icon-folder-open"></i><span class="hidden-tablet"> 【一】開發票需求列表(<span class="badge-num receipt0"><i class="fa fa-spin fa-circle-o-notch"></i></span>)</span></a></li>
            <? endif; ?>

			<? if ($_SESSION['usergroup'] == 4) : ?>
                <li class="nav-header hidden-tablet">財務部</li>
                <li><a class="ajax-link" href="receipt_list.php?status=0"><i class="icon-folder-open"></i><span class="hidden-tablet"> 【一】開發票需求列表(<span class="badge-num receipt0"><i class="fa fa-spin fa-circle-o-notch"></i></span>)</span></a></li>
                <li><a class="ajax-link" href="receipt_list.php?status=1"><i class="icon-folder-open"></i><span class="hidden-tablet"> 【一】已開發票列表(<span class="badge-num receipt1"><i class="fa fa-spin fa-circle-o-notch"></i></span>)</span></a></li>
                <li><a class="ajax-link" href="receipt_list.php?status=2"><i class="icon-folder-open"></i><span class="hidden-tablet"> 【一】作廢發票列表(<span class="badge-num receipt2"><i class="fa fa-spin fa-circle-o-notch"></i></span>)</span></a></li>
                <li><a class="ajax-link" href="receipt_list.php?status=3"><i class="icon-folder-open"></i><span class="hidden-tablet"> 【一】折讓發票列表(<span class="badge-num receipt3"><i class="fa fa-spin fa-circle-o-notch"></i></span>)</span></a></li>
                <li><a class="ajax-link" href="receipt_newnew.php"><i class="icon-folder-open"></i><span class="hidden-tablet"> 【二】輸入進項發票</span></a></li>
                <li><a class="ajax-link" href="receipt_list2.php"><i class="icon-folder-open"></i><span class="hidden-tablet"> 【二】進項發票列表</span></a></li>
                <li><a class="ajax-link" href="receipt_newnewnew3.php"><i class="icon-folder-open"></i><span class="hidden-tablet"> 【三】輸入收到款項</span></a></li>
                <li><a class="ajax-link" href="receipt_list3.php"><i class="icon-folder-open"></i><span class="hidden-tablet"> 【三】款項列表</span></a></li>
                <li><a class="ajax-link" href="campaign_listall.php?status=3&is_receipt=0"><i class="icon-folder-open"></i><span class="hidden-tablet"> 未勾選已開發票列表(<span class="badge-num status3_is_receipt"><i class="fa fa-spin fa-circle-o-notch"></i></span>)</span></a></li>
                <li><a class="ajax-link" href="campaign_listall_no_receipt.php?no_receipt=0&year=<?php echo date("Y",time()); ?>&month=<?php echo date("m",time());?>&day=01"><i class="icon-folder-open"></i><span class="hidden-tablet"> 未開發票列表</span></a></li>
                <li><a class="ajax-link" href="campaign_listall.php?status=7"><i class="icon-ban-circle"></i><span class="hidden-tablet"> 異常案件列表(<span class="badge-num status7"><i class="fa fa-spin fa-circle-o-notch"></i></span>)</span></a></li>
                <li><a class="ajax-link" href="campaign_listall.php?status=8"><i class="icon-ban-circle"></i><span class="hidden-tablet"> 作廢案件列表(<span class="badge-num status6"><i class="fa fa-spin fa-circle-o-notch"></i></span>)</span></a></li>
                <li><a class="ajax-link" href="campaign_listall.php?status=90"><i class="icon-folder-open"></i><span class="hidden-tablet"> 外匯調整案件列表(<span class="badge-num status8"><i class="fa fa-spin fa-circle-o-notch"></i></span>)</span></a></li>
                <li><a class="ajax-link" href="campaign_listall3.php"><i class="icon-folder-open"></i><span class="hidden-tablet"> 月報表</span></a></li>
                <li><a class="ajax-link" href="campaign_bloger.php"><i class="icon-folder-open"></i><span class="hidden-tablet"> 寫手出帳表</span></a></li>
                <li><a class="ajax-link" href="blogger_bank_list.php"><i class="icon-folder-open"></i><span class="hidden-tablet"> 寫手銀行資訊表</span></a></li>
            <? endif; ?>

            <? if ($_SESSION['usergroup'] == 5) : ?>
                <li class="nav-header hidden-tablet">主管</li>
                <li><a class="ajax-link" href="campaign_listall.php"><i class="icon-folder-open"></i><span class="hidden-tablet"> 全部案件列表</span></a></li>
                <li><a class="ajax-link" href="campaign_listall.php?status=2"><i class="icon-folder-open"></i><span class="hidden-tablet"> 送審中案件列表(<span class="badge-num status2"><i class="fa fa-spin fa-circle-o-notch"></i></span>)</span></a></li>
                <li><a class="ajax-link" href="campaign_listall.php?status=3"><i class="icon-folder-open"></i><span class="hidden-tablet"> 執行中案件列表(<span class="badge-num status3"><i class="fa fa-spin fa-circle-o-notch"></i></span>)</span></a></li>
                <li><a class="ajax-link" href="campaign_listall.php?status=5"><i class="icon-ban-circle"></i><span class="hidden-tablet"> 暫停案件列表(<span class="badge-num status5"><i class="fa fa-spin fa-circle-o-notch"></i></span>)</span></a></li>
                <li><a class="ajax-link" href="campaign_listall.php?status=7"><i class="icon-ban-circle"></i><span class="hidden-tablet"> 異常案件列表(<span class="badge-num status7"><i class="fa fa-spin fa-circle-o-notch"></i></span>)</span></a></li>
                <li><a class="ajax-link" href="campaign_listall.php?status=4"><i class="icon-folder-open"></i><span class="hidden-tablet"> 結案案件列表(<span class="badge-num status4"><i class="fa fa-spin fa-circle-o-notch"></i></span>)</span></a></li>
                <li><a class="ajax-link" href="campaign_listall.php?status=8"><i class="icon-ban-circle"></i><span class="hidden-tablet"> 作廢案件列表(<span class="badge-num status6"><i class="fa fa-spin fa-circle-o-notch"></i></span>)</span></a></li>
                <li><a class="ajax-link" href="campaign_listall3.php"><i class="icon-folder-open"></i><span class="hidden-tablet"> 月報表</span></a></li>
                <li><a class="ajax-link" href="campaign_bloger.php"><i class="icon-folder-open"></i><span class="hidden-tablet"> 寫手出帳表</span></a></li>
            <? endif; ?>

             <? if ($_SESSION['usergroup'] == 6) : ?>
                <li class="nav-header hidden-tablet">管理者</li>
                <li><a class="ajax-link" href="receipt_list.php?status=0"><i class="icon-folder-open"></i><span class="hidden-tablet"> 【一】開發票需求列表(<span class="badge-num receipt0"><i class="fa fa-spin fa-circle-o-notch"></i></span>)</span></a></li>
                <li><a class="ajax-link" href="receipt_list.php?status=1"><i class="icon-folder-open"></i><span class="hidden-tablet"> 【一】已開發票列表(<span class="badge-num receipt1"><i class="fa fa-spin fa-circle-o-notch"></i></span>)</span></a></li>
                <li><a class="ajax-link" href="receipt_list.php?status=2"><i class="icon-folder-open"></i><span class="hidden-tablet"> 【一】作廢發票列表(<span class="badge-num receipt2"><i class="fa fa-spin fa-circle-o-notch"></i></span>)</span></a></li>
                <li><a class="ajax-link" href="receipt_newnew.php"><i class="icon-folder-open"></i><span class="hidden-tablet"> 【二】輸入進項發票</span></a></li>
                <li><a class="ajax-link" href="receipt_list2.php"><i class="icon-folder-open"></i><span class="hidden-tablet"> 【二】進項發票列表</span></a></li>
                <li><a class="ajax-link" href="receipt_newnewnew3.php"><i class="icon-folder-open"></i><span class="hidden-tablet"> 【三】輸入收到款項</span></a></li>
                <li><a class="ajax-link" href="receipt_list3.php"><i class="icon-folder-open"></i><span class="hidden-tablet"> 【三】款項列表</span></a></li>
                <li><a class="ajax-link" href="campaign_listall.php?status=3&is_receipt=0"><i class="icon-folder-open"></i><span class="hidden-tablet"> 未勾選已開發票列表(<span class="badge-num status8"><i class="fa fa-spin fa-circle-o-notch"></i></span>)</span></a></li>
                <li><a class="ajax-link" href="campaign_listall_no_receipt.php?no_receipt=0&year=<?php echo date("Y",time()); ?>&month=<?php echo date("m",time());?>&day=01"><i class="icon-folder-open"></i><span class="hidden-tablet"> 未開發票列表</span></a></li>
                <li><a class="ajax-link" href="campaign_list.php?status=1"><i class="icon-folder-open"></i><span class="hidden-tablet"> 個人案件列表-未送審(<span class="badge-num status11"><i class="fa fa-spin fa-circle-o-notch"></i></span>)</span></a></li>
                <li><a class="ajax-link" href="campaign_list.php?status=2"><i class="icon-folder-open"></i><span class="hidden-tablet"> 個人案件列表-送審中(<span class="badge-num status22"><i class="fa fa-spin fa-circle-o-notch"></i></span>)</span></a></li>
                <li><a class="ajax-link" href="campaign_list.php?status=3"><i class="icon-folder-open"></i><span class="hidden-tablet"> 個人案件列表-執行中(<span class="badge-num status33"><i class="fa fa-spin fa-circle-o-notch"></i></span>)</span></a></li>
                <li><a class="ajax-link" href="campaign_list.php?status=5"><i class="icon-folder-open"></i><span class="hidden-tablet"> 個人案件列表-暫停(<span class="badge-num status55"><i class="fa fa-spin fa-circle-o-notch"></i></span>)</span></a></li>
                <li><a class="ajax-link" href="campaign_list.php?status=7"><i class="icon-folder-open"></i><span class="hidden-tablet"> 個人案件列表-異常(<span class="badge-num status77"><i class="fa fa-spin fa-circle-o-notch"></i></span>)</span></a></li>
                <li><a class="ajax-link" href="campaign_list.php?status=4"><i class="icon-folder-open"></i><span class="hidden-tablet"> 個人案件列表-結案(<span class="badge-num status44"><i class="fa fa-spin fa-circle-o-notch"></i></span>)</span></a></li>
                <li><a class="ajax-link" href="campaign_listall.php"><i class="icon-folder-open"></i><span class="hidden-tablet"> 全部案件列表</span></a></li>
                <li><a class="ajax-link" href="campaign_listall.php?status=2"><i class="icon-folder-open"></i><span class="hidden-tablet"> 送審中案件列表(<span class="badge-num status2"><i class="fa fa-spin fa-circle-o-notch"></i></span>)</span></a></li>
                <li><a class="ajax-link" href="campaign_listall.php?status=3"><i class="icon-folder-open"></i><span class="hidden-tablet"> 執行中案件列表(<span class="badge-num status3"><i class="fa fa-spin fa-circle-o-notch"></i></span>)</span></a></li>
                <li><a class="ajax-link" href="campaign_listall.php?status=5"><i class="icon-ban-circle"></i><span class="hidden-tablet"> 暫停案件列表(<span class="badge-num status5"><i class="fa fa-spin fa-circle-o-notch"></i></span>)</span></a></li>
                <li><a class="ajax-link" href="campaign_listall.php?status=7"><i class="icon-ban-circle"></i><span class="hidden-tablet"> 異常案件列表(<span class="badge-num status7"><i class="fa fa-spin fa-circle-o-notch"></i></span>)</span></a></li>
                <li><a class="ajax-link" href="campaign_listall.php?status=90"><i class="icon-folder-open"></i><span class="hidden-tablet"> 外匯調整案件列表(<span class="badge-num status8"><i class="fa fa-spin fa-circle-o-notch"></i></span>)</span></a></li>
                <li><a class="ajax-link" href="campaign_listall.php?status=4"><i class="icon-folder-open"></i><span class="hidden-tablet"> 結案案件列表(<span class="badge-num status4"><i class="fa fa-spin fa-circle-o-notch"></i></span>)</span></a></li>
                <li><a class="ajax-link" href="campaign_listall.php?status=9"><i class="icon-ban-circle"></i><span class="hidden-tablet"> 待審作廢案件列表(<span class="badge-num status9"><i class="fa fa-spin fa-circle-o-notch"></i></span>)</span></a></li>
                <li><a class="ajax-link" href="campaign_listall.php?status=8"><i class="icon-ban-circle"></i><span class="hidden-tablet"> 作廢案件列表(<span class="badge-num status6"><i class="fa fa-spin fa-circle-o-notch"></i></span>)</span></a></li>
                <li><a class="ajax-link" href="campaign_listall3.php"><i class="icon-folder-open"></i><span class="hidden-tablet"> 月報表</span></a></li>
                <? if ($isJSPlatform) : ?>
                    <li><a class="ajax-link" href="campaign_bloger.php"><i class="icon-folder-open"></i><span class="hidden-tablet"> 寫手出帳表</span></a></li>
                    <li><a class="ajax-link" href="blogger_bank_list.php"><i class="icon-folder-open"></i><span class="hidden-tablet"> 寫手銀行資訊表</span></a></li>
                <? endif; ?>
            <? endif; ?>

            <li class="nav-header hidden-tablet">資料查詢</li>
            <li><a class="ajax-link" href="client_list.php"><i class="icon-eye-open"></i><span class="hidden-tablet"> 廣告主列表</span></a></li>
            <li><a class="ajax-link" href="agency_list.php"><i class="icon-eye-open"></i><span class="hidden-tablet"> 代理商列表</span></a></li>
            <? if ($isJSPlatform) : ?>
                <li><a class="ajax-link" href="blogger_list.php"><i class="icon-eye-open"></i><span class="hidden-tablet"> 寫手列表</span></a></li>
            <? endif; ?>

            <li><a class="ajax-link" href="everyday_list.php"><i class="icon-eye-open"></i><span class="hidden-tablet"> 今日回簽列表</span></a></li>
            <li><a class="ajax-link" href="media_list.php"><i class="icon-eye-open"></i><span class="hidden-tablet"> 媒體規格表</span></a></li>
            <li><a class="ajax-link" href="campaign_listall2.php"><i class="icon-eye-open"></i><span class="hidden-tablet"> 競品資料區</span></a></li>
          
            <? if ($isJSPlatform || $isHKPlatform) : ?>
                <li><a class="ajax-link" href="appdriver_list.php"><i class="icon-eye-open"></i><span class="hidden-tablet"> Appdriver</span></a></li>
                <li><a class="ajax-link" href="line_list.php"><i class="icon-eye-open"></i><span class="hidden-tablet"> LINE</span></a></li>
            <? endif; ?>

            <? if ($isJSPlatform) : ?>
                <li><a class="ajax-link" href="tapjoy_list.php"><i class="icon-eye-open"></i><span class="hidden-tablet"> Tapjoy</span></a></li>
            <? endif; ?>

            <? if ($isJSPlatform || $isHKPlatform) : ?>
                <? if ($_SESSION['usergroup'] == 6 || in_array($_SESSION['userid'], [16, 17, 19])) : ?>
                    <li><a class="ajax-link" href="crm2.php"><i class="icon-eye-open"></i><span class="hidden-tablet"> CRM</span></a></li>
                <? endif; ?>
            <? endif; ?>

            <li><a class="ajax-link" href="published.php">
                <i class="icon-eye-open"></i>
                <span class="hidden-tablet"> 上刊畫面</span>
            </a></li>
		</ul>
	</div>
</div>
<!-- left menu ends -->