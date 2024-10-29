<?php

return [
    'draft_start' => 'trade_license',
    'qc_portal_key' => env('QC_PORTAL_KEY'),

    'cached_keys' => [
        'divisions' => [
            'dhaka' => [
                'label' => 'Dhaka',
                'value' => 'dhaka',
                'districts' => [
                    'dhaka' => [
                        'label' => 'Dhaka',
                        'value' => 'dhaka',
                        'thanas' => [
                            'khilkhet' => [
                                'label' => 'Khilkhet',
                                'value' => 'khilkhet'
                            ],
                            'banani' => [
                                'label' => 'Banani',
                                'value' => 'banani',
                            ],
                            'gulshan' => [
                                'label' => 'Gulshan',
                                'value' => 'gulshan',
                            ]
                        ]
                    ],
                    'gazipur' => [
                        'label' => 'Gazipur',
                        'value' => 'gazipur',
                        'thanas' => [
                            'joydevpur' => [
                                'label' => 'জয়দেবপুর',
                                'value' => 'joydevpur',
                            ],
                            'tongi' => [
                                'label' => 'টংগী',
                                'value' => 'tongi',
                            ],
                        ]
                    ]
                ]
            ],
            'chattogram' => [
                'label' => 'Chattogram',
                'value' => 'chattogram',
                'districts' => [
                    'chattogram' => [
                        'label' => 'Chattogram',
                        'value' => 'chattogram',
                        'thanas' => [
                            'karnafuli' => [
                                'label' => 'Karnafuli',
                                'value' => 'karnafuli',
                            ],
                            'kotwali' => [
                                'label' => 'Kotwali',
                                'value' => 'kotwali',
                            ]
                        ]
                    ],
                    'cumilla' => [
                        'label' => 'Cumilla',
                        'value' => 'cumilla',
                        'thanas' => [
                            'kotwali_model_thana' => [
                                'label' => 'কোতোয়ালী মডেল থানা',
                                'value' => 'kotwali_model_thana',
                            ],
                            'sadar_dakkhin_model_thana' => [
                                'lable' => 'সদর দক্ষিন মডেল থানা',
                                'value' => 'sadar_dakkhin_model_thana',
                            ],
                            'choddogram' => [
                                'label' => 'চৌদ্দগ্রাম থানা',
                                'value' => 'choddogram',
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ]
];
