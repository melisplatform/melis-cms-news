<?php 
return [
    'plugins' => [
        'meliscore' => [
            'emails' => [
                'WF_DEMAND' => [
                    'email_name' => 'Work Flow Demand',
                    'layout' => 'melis-core/view/layout/layoutEmail.phtml',
                    'headers' => [
                        'from' => 'noreply@melistechnology.com',
                        'from_name' => 'Melis Technology',
                        'replyTo' => '',
                        'tags' => 'USER_TO,USER_FROM,TYPE,DETAILS',
                    ],
                    'contents' => [
                        'en_EN' => [
                            'subject' => 'tr_meliscmsnews_workflow_email_demand_Subject',
                            'html' => 'tr_meliscmsnews_workflow_email_demand_html_Content',
                            'text' => '',
                        ],
                        'fr_FR' => [
                            'subject' => 'tr_meliscmsnews_workflow_email_demand_Subject',
                            'html' => 'tr_meliscmsnews_workflow_email_demand_html_Content',
                            'text' => '',
                        ],
                    ],
                ],
                'WF_VALIDATED' => [
                    'email_name' => 'Work Flow Validated',
                    'layout' => 'melis-core/view/layout/layoutEmail.phtml',
                    'headers' => [
                        'from' => 'noreply@melistechnology.com',
                        'from_name' => 'Melis Technology',
                        'replyTo' => '',
                        'tags' => 'USER_TO,USER_FROM,TYPE,DETAILS',
                    ],
                    'contents' => [
                        'en_EN' => [
                            'subject' => 'tr_meliscmsnews_workflow_email_validated_Subject',
                            'html' => 'tr_meliscmsnews_workflow_email_validated_Content',
                            'text' => '',
                        ],
                        'fr_FR' => [
                            'subject' => 'tr_meliscmsnews_workflow_email_validated_Subject',
                            'html' => 'tr_meliscmsnews_workflow_email_validated_Content',
                            'text' => '',
                        ],
                    ],
                ],
                'WF_REFUSED' => [
                    'email_name' => 'Work Flow Refused',
                    'layout' => 'melis-core/view/layout/layoutEmail.phtml',
                    'headers' => [
                        'from' => 'noreply@melistechnology.com',
                        'from_name' => 'Melis Technology',
                        'replyTo' => '',
                        'tags' => 'USER_TO,USER_FROM,TYPE,DETAILS',
                    ],
                    'contents' => [
                        'en_EN' => [
                            'subject' => 'tr_meliscmsnews_workflow_email_refused_Subject',
                            'html' => 'tr_meliscmsnews_workflow_email_refused_Content',
                            'text' => '',
                        ],
                        'fr_FR' => [
                            'subject' => 'tr_meliscmsnews_workflow_email_refused_Subject',
                            'html' => 'tr_meliscmsnews_workflow_email_refused_Content',
                            'text' => '',
                        ],
                    ],
                ],
            ]
        ]
    ]
];