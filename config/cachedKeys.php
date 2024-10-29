<?php
return [
	'divisions' => [
		'dhaka' => [
			'label' => 'Dhaka',
			'value' => 'dhaka,'
		],
		'chattogram' => [
			'label' => 'Chattogram',
			'value' => 'chattogram,'
		],
	],
	'districts' => [
		'dhaka' => [
			'label' => 'Dhaka',
			'value' => 'dhaka',
			'visible_if' => '[["business_information.business_information.registered_division.value","dhaka"]]',
		],
		'gazipur' => [
			'label' => 'Gazipur',
			'value' => 'gazipur',
			'visible_if' => '[["{wildcard}.value","dhaka"]]',
		],
		'chattogram' => [
			'label' => 'Chattogram',
			'value' => 'chattogram',
			'visible_if' => '[["business_information.business_information.registered_division.value","chattogram"]]',
		],
		'cumilla' => [
			'label' => 'Cumilla',
			'value' => 'cumilla',
			'visible_if' => '[["business_information.business_information.registered_division.value","chattogram"]]',
		],
	],
	'thanas' => [
		'dhaka.dhaka.khilkhet' => [
			'label' => 'Khilkhet',
			'value' => 'dhaka.dhaka.khilkhet',
			'visible_if' => '[["business_information.business_information.registered_district.value","dhaka"]]',
		],
		'dhaka.dhaka.banani' => [
			'label' => 'Banani',
			'value' => 'dhaka.dhaka.banani',
			'visible_if' => '[["business_information.business_information.registered_district.value","dhaka"]]',
		],
		'dhaka.dhaka.gulshan' => [
			'label' => 'Gulshan',
			'value' => 'dhaka.dhaka.gulshan',
			'visible_if' => '[["business_information.business_information.registered_district.value","dhaka"]]',
		],
		'dhaka.gazipur.joydevpur' => [
			'label' => 'জয়দেবপুর',
			'value' => 'dhaka.gazipur.joydevpur',
			'visible_if' => '[["business_information.business_information.registered_district.value","gazipur"]]',
		],
		'dhaka.gazipur.tongi' => [
			'label' => 'টংগী',
			'value' => 'dhaka.gazipur.tongi',
			'visible_if' => '[["business_information.business_information.registered_district.value","gazipur"]]',
		],
		'chattogram.chattogram.karnafuli' => [
			'label' => 'Karnafuli',
			'value' => 'chattogram.chattogram.karnafuli',
			'visible_if' => '[["business_information.business_information.registered_district.value","chattogram"]]',
		],
		'chattogram.chattogram.kotwali' => [
			'label' => 'Kotwali',
			'value' => 'chattogram.chattogram.kotwali',
			'visible_if' => '[["business_information.business_information.registered_district.value","chattogram"]]',
		],
		'chattogram.cumilla.kotwali_model_thana' => [
			'label' => 'কোতোয়ালী মডেল থানা',
			'value' => 'chattogram.cumilla.kotwali_model_thana',
			'visible_if' => '[["business_information.business_information.registered_district.value","cumilla"]]',
		],
		'chattogram.cumilla.sadar_dakkhin_model_thana' => [
			'label' => 'সদর দক্ষিন মডেল থানা',
			'value' => 'chattogram.cumilla.sadar_dakkhin_model_thana',
			'visible_if' => '[["business_information.business_information.registered_district.value","cumilla"]]',
		],
		'chattogram.cumilla.choddogram' => [
			'label' => 'চৌদ্দগ্রাম থানা',
			'value' => 'chattogram.cumilla.choddogram',
			'visible_if' => '[["business_information.business_information.registered_district.value","cumilla"]]',
		],
	],
];
