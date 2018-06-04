<?php

$Select_str = "var TypeItems =
	[
		{
			'TypeItemId': 'GAME APP',
			'TypeItemName': 'GAME APP'
		},
		{
			'TypeItemId': '非GAME APP',
			'TypeItemName': '非GAME APP'
		},
		{
			'TypeItemId': '網頁廣告',
			'TypeItemName': '網頁廣告'
		}
	];

	$('#SelectType').empty().append($('<option></option>').val('').text('------'));

	$.each(TypeItems, function (i, item)
	{
		$('#SelectType').append($('<option></option>').val(item.TypeItemId).text(item.TypeItemName));
	});

	$('#SelectType').prop('required',true);


	var SystemItems =
	[
		{
			'TypeItemId': 'iOS',
			'TypeItemName': 'iOS'
		},
		{
			'TypeItemId': 'Android',
			'TypeItemName': 'Android'
		},
		{
			'TypeItemId': '其他',
			'TypeItemName': '其他'
		}
	];

	$('#SelectSystem').empty().append($('<option></option>').val('').text('------'));

	$.each(SystemItems, function (i, item)
	{
		$('#SelectSystem').append($('<option></option>').val(item.TypeItemId).text(item.TypeItemName));
	});

	$('#SelectSystem').prop('required',true);";

?>