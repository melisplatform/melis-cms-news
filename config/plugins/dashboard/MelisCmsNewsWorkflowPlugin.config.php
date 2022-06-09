<?php 
    return [
        'plugins' => [
            'meliscore' => [
                'interface' => [
                    'melis_dashboardplugin' => [
                        'conf' => [
                            'dashboard_plugin' => true
                        ],
                        'interface' => [
                            'melisdashboardplugin_section' => [
                                'interface' => [
                                    'MelisCmsNewsWorkflowPlugin' => [
                                        'conf' => [
                                            'type' => '/meliscmsnews/interface/MelisCmsNewsWorkflowPlugin'
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
            ],

            'meliscmsnews' => [
                'ressources' => [
                    'css' => [
                    ],
                    'js' => [
                    ]
                ],
                'interface' => [
                    'MelisCmsNewsWorkflowPlugin' => [
                        'conf' => [
                            'name' => 'MelisCmsNewsWorkflowPlugin',
                            'melisKey' => 'MelisCmsNewsWorkflowPlugin',
                            'dashboard_plugin' => true
                        ],
                        'datas' => [
                            'plugin_id' => 'newsWorkFlow',
                            'name' => 'tr_meliscmsnews_dashboard_Workflow',
                            'description' => 'tr_meliscmsnews_dashboard_Workflow description',
                            'icon' => 'fa fa-users',
                            'thumbnail' => '/MelisCmsNews/plugins/images/MelisCmsNewsWorkflowPlugin.jpg',
                            'page_icon' => 'file',
                            'users_icon' => 'parents',
                            'jscallback' => '',
                            'max_lines' => 8,
                            'height' => 4,
                            'width' => 6,
                            'x-axis' => 0,
                            'y-axis' => 0,
                            'limit' => 8,
                            /*
                            *if set this plugin will belong to a specific marketplace section,
                            *  - available section for dashboard plugins as of 2019-05-16
                            *    - MelisCore
                            *    - MelisCms
                            *    - MelisMarketing
                            *    - MelisSite
                            *    - MelisCommerce
                            *    - Others
                            *    - CustomProjects
                            * if not or the section is not correct it will go directly to ( Others ] section
                            */
                            'section' => 'MelisCms',
                        ],
                        'forward' => [
                            'module' => 'MelisCmsNews',
                            'plugin' => 'MelisCmsNewsWorkflowPlugin',
                            'function' => 'workflow',
                            'jscallback' => '',
                            'jsdatas' => []
                        ],
                    ],
                    'meliscmsnews_dashboard_workflow_comment_modal' => [
                        'conf' => [
                            'id' => 'id_meliscmsnews_dashboard_workflow_comment_modal',
                            'name' => 'tr_meliscmsnews_dashboard_workflow_comment_modal',
                            'melisKey' => 'meliscmsnews_dashboard_workflow_comment_modal',
                        ],
                        'forward' => [
                            'module' => 'MelisCmsNews',
                            'plugin' => 'MelisCmsNewsWorkflowPlugin',
                            'function' => 'workflowCommentModal',
                        ],
                    ],
                ],
            ],
        ],
    ];