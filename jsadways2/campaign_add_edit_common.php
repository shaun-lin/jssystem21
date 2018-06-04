<?php
    // 2017-05-19 (Jimmy): create for campaign_add.php and campaign_edit.php

    // 2017-05-19 (Jimmy): fetch all agency data for this page from loading at a query
    $objAgencyContact = CreateObject('AgencyContact');
    $rowsAgencyContact = $objAgencyContact->getAllList();

    $selectorAgencyOptions = [];
    $sql2 = 'SELECT `id`, `name`, `display`, `days` FROM `agency`;';
    $result2 = mysql_query($sql2);
    if (mysql_num_rows($result2) > 0) {
      $jsonDataForCategory1 = [];
      $jsonDataForCategory2 = [];

      while ($agency = mysql_fetch_array($result2)) {
        if (isset($_GET['id']) || $agency['display'] == 1) {
            $data = [
              'value' => $agency['id'], 
              'text' => $agency['name']
            ];

            array_push($selectorAgencyOptions, $data);
        }

        $jsonDataForCategory2[$agency['id']] = [];
        $jsonDataForCategory1[$agency['id']] = [
            'days' => $agency['days'],
            'data' => []
        ];

        if (isset($rowsAgencyContact[$agency['id']]) && count($rowsAgencyContact[$agency['id']])) {
            $idx = 1;
            foreach ($rowsAgencyContact[$agency['id']] as $itemAgencyContact) {
                $data = [
                    "subCategoryId" => $idx,
                    "subCategoryName" => $itemAgencyContact['contact_name']
                ];

                array_push($jsonDataForCategory1[$agency['id']]['data'], $data);

                $jsonDataForCategory2[$agency['id']][$idx] = [
                    'contact1' => $itemAgencyContact['contact_name'],
                    'contact2' => $itemAgencyContact['contact_tel'],
                    'contact3' => $itemAgencyContact['contact_email'],
                    'title' => $itemAgencyContact['contact_title']
                ];

                $idx++;
            }
        }
      }
    }

    // 2017-05-19 (Jimmy): fetch all client data for this page from loading at a query
    $objClientContact = CreateObject('ClientContact');
    $rowsClientContact = $objClientContact->getAllList();

    $selectorClientOptions = ['prepend' => [], 'append' => []];
    $sql2 = 'SELECT `id`, `name`, `name3` FROM `client`;';
    $result2 = mysql_query($sql2);
    if (mysql_num_rows($result2) > 0) {
      $jsonDataForCategory3 = [];
      $jsonDataForCategory4 = [];

      while ($client = mysql_fetch_array($result2)) {
        $tmpData = [
            'value' => $client['id'], 
            'text' => $client['name'] .'【'. $client['name3'] .'】'
        ];

        if (strpos($client['name3'], $_SESSION['name']) === false) {
            array_push($selectorClientOptions['append'], $tmpData);
        } else {
            array_push($selectorClientOptions['prepend'], $tmpData);
        }

        $jsonDataForCategory3[$client['id']] = ['data' => []];
        $jsonDataForCategory4[$client['id']] = [];
        
        if (isset($rowsClientContact[$client['id']]) && count($rowsClientContact[$client['id']])) {
            $idx = 1;

            foreach ($rowsClientContact[$client['id']] as $itemClientContact) {
                $tmpData = [
                    "subCategoryId" => $idx,
                    "subCategoryName" => $itemClientContact['contact_name']
                ];

                $jsonDataForCategory4[$client['id']][$idx] = [
                    'contact1' => $itemClientContact['contact_name'],
                    'contact2' => $itemClientContact['contact_tel'],
                    'contact3' => $itemClientContact['contact_email'],
                    'title' => $itemClientContact['contact_title']
                ];
    
                array_push($jsonDataForCategory3[$client['id']]['data'], $tmpData);

                $idx++;
            }
        }
      }
    }
?>
<script type="text/javascript">
    var jsonScenery = [];
    var jsonHotel = [];

    $(document).ready(function() {
        Page_Init();
    });

    function Page_Init()
    {
        $('#agency').change(function(){
            $('#SelectSubCategory').empty();
            ChangeCategory1();
        });
        $('#SelectSubCategory').change(function(){
            ChangeCategory2();
        });
        $('#client').change(function(){
            $('#SelectSubCategory2').empty();
            ChangeCategory3();
        });
        $('#SelectSubCategory2').change(function(){
            ChangeCategory4();
        });
    }

    function ChangeCategory1()
    {
        //變動第一個下拉選單
        var categoryId = $.trim($('#agency option:selected').val());
        var jsonCategory1Data = <?php echo json_encode($jsonDataForCategory1); unset($jsonDataForCategory1); ?>;
        var jsonData = [];
        if (categoryId == '0') {

        } else {
            if (categoryId in jsonCategory1Data) {
                document.getElementById("pay2").value = jsonCategory1Data[categoryId]['days'];
            }
        }

        if (categoryId.length != 0) {
            $('#SelectSubCategory').append($('<option></option>').val('0').text('----'));
            if (categoryId in jsonCategory1Data && jsonCategory1Data[categoryId]['data'].length) {
                $.each(jsonCategory1Data[categoryId]['data'] , function(i, item){
                    $('#SelectSubCategory').append($('<option></option>').val(item.subCategoryId).text(item.subCategoryName));
                });
            }
        }
    }

    function ChangeCategory2()
    {
        var categoryId = $.trim($('#agency option:selected').val());
        var categoryId2 = $.trim($('#SelectSubCategory option:selected').val());
        var jsonCategory2Data = <?php echo json_encode($jsonDataForCategory2); unset($jsonDataForCategory2); ?>;
        if(categoryId == '0')
        {

        } else {
            if (categoryId in jsonCategory2Data && categoryId2 in jsonCategory2Data[categoryId]) {
                document.getElementById('contact1').value = jsonCategory2Data[categoryId][categoryId2]['contact1'];
                document.getElementById('contact2').value = jsonCategory2Data[categoryId][categoryId2]['contact2'];
                document.getElementById('contact3').value = jsonCategory2Data[categoryId][categoryId2]['contact3'];
                document.getElementById('title').value = jsonCategory2Data[categoryId][categoryId2]['title'];
            }
        }
    }

    function ChangeCategory3()
    {
        //變動第一個下拉選單
        var categoryId = $.trim($('#client option:selected').val());
        var jsonCategory3Data = <?php echo json_encode($jsonDataForCategory3); unset($jsonDataForCategory3); ?>;
        var jsonData = [];
        if(categoryId == '0')
        {

        }

        if(categoryId.length != 0)
        {
            $('#SelectSubCategory2').append($('<option></option>').val('0').text('----'));
            if (categoryId in jsonCategory3Data && jsonCategory3Data[categoryId]['data'].length) {
                $.each(jsonCategory3Data[categoryId]['data'] , function(i, item){
                    $('#SelectSubCategory2').append($('<option></option>').val(item.subCategoryId).text(item.subCategoryName));
                });
            }
        }
    }

    function ChangeCategory4()
    {
        var categoryId = $.trim($('#client option:selected').val());
        var categoryId2 = $.trim($('#SelectSubCategory2 option:selected').val());
        var jsonCategory4Data = <?php echo json_encode($jsonDataForCategory4); unset($jsonDataForCategory4); ?>;
        if(categoryId == '0')
        {

        } else {
            if (categoryId in jsonCategory4Data && categoryId2 in jsonCategory4Data[categoryId]) {
                document.getElementById('contact1').value = jsonCategory4Data[categoryId][categoryId2]['contact1'];
                document.getElementById('contact2').value = jsonCategory4Data[categoryId][categoryId2]['contact2'];
                document.getElementById('contact3').value = jsonCategory4Data[categoryId][categoryId2]['contact3'];
                document.getElementById('title').value = jsonCategory4Data[categoryId][categoryId2]['title'];
            }
        }
    }


    function compare()
    {
        var f = document.getElementById('date1').value,
            e = document.getElementById('date2').value;
        if(Date.parse(f.valueOf()) > Date.parse(e.valueOf()))
        {
            alert('警告！到期日期不能小於起始日期');
            document.getElementById('date2').value=document.getElementById('date1').value;
            return false;
        }

        return true;
    }

    function number(){
        var a = document.getElementById('ctr').value,
            b = document.getElementById('quantity').value;
        var g = (a*b)/100,
            gg = b*0.6;

        document.getElementById('number1').value=g;
        document.getElementById('totalprice').value=gg;
    }
</script>