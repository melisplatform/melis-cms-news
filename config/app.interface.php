<?php

return [
    'plugins' => [
        'meliscore' => [
            'datas' => [],
            'interface' => [
                'meliscore_leftmenu' => [
                    'interface' => [
                        'meliscms_toolstree_section' =>  [
                            'interface' => [
                                'meliscms_news_tool_section' => [
                                    'conf' => [
                                        'id' => 'id_meliscms_news_tool_section',
                                        'name' => 'tr_meliscmsnews_list_header_title',
                                        'icon' => 'fa-newspaper-o',
                                        'rights_checkbox_disable' => true,
                                        'melisKey' => 'meliscms_news_tool_section'
                                    ],
                                    'interface' => [
                                        'meliscmsnews_left' => [
                                            'conf' => [
                                                'type' => '/meliscmsnews/interface/meliscmsnews_list/interface/meliscmsnews_left_menu',
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
//        'meliscore_dashboard' => [],
        'meliscmsnews' => [
            'conf' => [
                'id' => '',
                'name' => 'tr_MelisCmsNews_manager',
                'rightsDisplay' => 'none',
                'files' => [
                    'minUploadSize' => 1,
                    'maxUploadSize' => 10500000,
                    'imagesPath' => '/media/news/',
                ],
                'images_conf' => [
                    'max' => 3,
                    'name' => 'cnews_image',
                ],
                'documents_conf' => [
                    'max' => 3,
                    'name' => 'cnews_documents',
                ],
                'paragraphs_conf' => [
                    'max' => 4,
                    'name' => 'cnews_paragraph',
                ],
            ],
            'ressources' => [
                'js' => [
                    '/MelisCmsNews/js/tools/news.tool.js',
                    '/MelisCmsNews/assets/switch/bootstrap-switch.js',
                    '/MelisCmsNews/assets/iframeResizer/iframeResizer.min.js'
                ],

                'css' => [
                    '/MelisCmsNews/css/news.css',
                ],
                /**
                 * the "build" configuration compiles all assets into one file to make
                 * lesser requests
                 */
                'build' => [
                    // lists of assets that will be loaded in the layout
                    'css' => [
                        '/MelisCmsNews/build/css/bundle.css',

                    ],
                    'js' => [
                        '/MelisCmsNews/build/js/bundle.js',
                    ]
                ]
            ],
            'datas' => [

            ],
            'interface' => [
                'meliscmsnews_list' => [

                    'conf' => [
                        'name' => 'tr_meliscmsnews_list',
                        'rightsDisplay' => 'referencesonly',
                    ],
                    'interface' => [
                        'meliscmsnews_left_menu' => [
                            'conf' => [
                                'id' => 'id_meliscmsnews_left_menu',
                                'name' => 'tr_meliscmsnews_list_header_title',
                                'melisKey' => 'meliscmsnews_left_menu',
                                'icon' => 'fa-list-alt',
                                'rights_checkbox_disable' => true,
                                'follow_regular_rendering' => false,
                            ],
                            'forward' => [
                                'module' => 'MelisCmsNews',
                                'controller' => 'MelisCmsNewsList',
                                'action' => 'render-news-list-page',
                                'jscallback' => '',
                                'jsdatas' => []
                            ],
                            'interface' => [
                                'meliscmsnews_list_header' => [
                                    'conf' => [
                                        'id' => 'id_meliscmsnews_list_header',
                                        'melisKey' => 'meliscmsnews_list_header',
                                        'name' => 'tr_meliscmsnews_list_header',
                                    ],
                                    'forward' => [
                                        'module' => 'MelisCmsNews',
                                        'controller' => 'MelisCmsNewsList',
                                        'action' => 'render-news-list-header',
                                    ],
                                    'interface' => [
                                        'meliscmsnews_list_header_left' => [
                                            'conf' => [
                                                'id' => 'id_meliscmsnews_list_header_left',
                                                'melisKey' => 'meliscmsnews_list_header_left',
                                                'name' => 'tr_meliscmsnews_list_header_left',
                                            ],
                                            'forward' => [
                                                'module' => 'MelisCmsNews',
                                                'controller' => 'MelisCmsNewsList',
                                                'action' => 'render-news-list-header-left',
                                            ],
                                            'interface' => [
                                                'meliscmsnews_list_header_title' => [
                                                    'conf' => [
                                                        'id' => 'id_meliscmsnews_list_header_title',
                                                        'melisKey' => 'meliscmsnews_list_header_title',
                                                        'name' => 'tr_meliscmsnews_list_header_title',
                                                    ],
                                                    'forward' => [
                                                        'module' => 'MelisCmsNews',
                                                        'controller' => 'MelisCmsNewsList',
                                                        'action' => 'render-news-list-header-title',
                                                    ],
                                                ],
                                            ],
                                        ],
                                        'meliscmsnews_list_header_right' => [
                                            'conf' => [
                                                'id' => 'id_meliscmsnews_list_header_right',
                                                'melisKey' => 'meliscmsnews_list_header_right',
                                                'name' => 'tr_meliscmsnews_list_header_right',
                                            ],
                                            'forward' => [
                                                'module' => 'MelisCmsNews',
                                                'controller' => 'MelisCmsNewsList',
                                                'action' => 'render-news-list-header-right',
                                            ],
                                            'interface' => [
                                                'meliscmsnews_list_header_right_add' => [
                                                    'conf' => [
                                                        'id' => 'id_meliscmsnews_list_header_right_add',
                                                        'melisKey' => 'meliscmsnews_list_header_right_add',
                                                        'name' => 'tr_meliscmsnews_list_header_right_add',
                                                    ],
                                                    'forward' => [
                                                        'module' => 'MelisCmsNews',
                                                        'controller' => 'MelisCmsNewsList',
                                                        'action' => 'render-news-list-header-right-add',
                                                    ],
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                                'meliscmsnews_list_content' => [
                                    'conf' => [
                                        'id' => 'id_meliscmsnews_list_content',
                                        'melisKey' => 'meliscmsnews_list_content',
                                        'name' => 'tr_meliscmsnews_list_content',
                                    ],
                                    'forward' => [
                                        'module' => 'MelisCmsNews',
                                        'controller' => 'MelisCmsNewsList',
                                        'action' => 'render-news-list-content',
                                    ],
                                    'interface' => [
                                        'meliscmsnews_list_content_table' => [
                                            'conf' => [
                                                'id' => 'id_meliscmsnews_list_content_table',
                                                'melisKey' => 'meliscmsnews_list_content_table',
                                                'name' => 'tr_meliscmsnews_list_content_table',
                                            ],
                                            'forward' => [
                                                'module' => 'MelisCmsNews',
                                                'controller' => 'MelisCmsNewsList',
                                                'action' => 'render-news-list-content-table',
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                'meliscmsnews' => [
                    'interface' => [
                        'meliscmsnews_page' => [
                            'conf' => [
                                'id' => 'id_meliscmsnews_page',
                                'melisKey' => 'meliscmsnews_page',
                                'name' => 'tr_meliscmsnews_list_header_title',
                            ],
                            'forward' => [
                                'module' => 'MelisCmsNews',
                                'controller' => 'MelisCmsNews',
                                'action' => 'render-news-page',
                            ],
                            'interface' => [
                                'meliscmsnews_header' => [
                                    'conf' => [
                                        'id' => 'id_meliscmsnews_header',
                                        'melisKey' => 'meliscmsnews_header',
                                        'name' => 'tr_meliscmsnews_header',
                                    ],
                                    'forward' => [
                                        'module' => 'MelisCmsNews',
                                        'controller' => 'MelisCmsNews',
                                        'action' => 'render-news-header',
                                    ],
                                    'interface' => [
                                        'meliscmsnews_header_left' => [
                                            'conf' => [
                                                'id' => 'id_meliscmsnews_header_left',
                                                'melisKey' => 'meliscmsnews_header_left',
                                                'name' => 'tr_meliscmsnews_header_left',
                                            ],
                                            'forward' => [
                                                'module' => 'MelisCmsNews',
                                                'controller' => 'MelisCmsNews',
                                                'action' => 'render-news-header-left',
                                            ],
                                            'interface' => [
                                                'meliscmsnews_header_title' => [
                                                    'conf' => [
                                                        'id' => 'id_meliscmsnews_header_title',
                                                        'melisKey' => 'meliscmsnews_header_title',
                                                        'name' => 'tr_meliscmsnews_header_title',
                                                    ],
                                                    'forward' => [
                                                        'module' => 'MelisCmsNews',
                                                        'controller' => 'MelisCmsNews',
                                                        'action' => 'render-news-header-title',
                                                    ],
                                                ],
                                            ],
                                        ],
                                        'meliscmsnews_header_right' => [
                                            'conf' => [
                                                'id' => 'id_meliscmsnews_header_right',
                                                'melisKey' => 'meliscmsnews_header_right',
                                                'name' => 'tr_meliscmsnews_header_right',
                                            ],
                                            'forward' => [
                                                'module' => 'MelisCmsNews',
                                                'controller' => 'MelisCmsNews',
                                                'action' => 'render-news-header-right',
                                            ],
                                            'interface' => [
                                                'meliscmsnews_header_right_save' => [
                                                    'conf' => [
                                                        'id' => 'id_meliscmsnews_header_right_save',
                                                        'melisKey' => 'meliscmsnews_header_right_save',
                                                        'name' => 'tr_meliscmsnews_header_right_save',
                                                    ],
                                                    'forward' => [
                                                        'module' => 'MelisCmsNews',
                                                        'controller' => 'MelisCmsNews',
                                                        'action' => 'render-news-header-right-save',
                                                    ],
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                                'meliscmsnews_content' => [
                                    'conf' => [
                                        'id' => 'id_meliscmsnews_content',
                                        'melisKey' => 'meliscmsnews_content',
                                        'name' => 'tr_meliscmsnews_content',
                                    ],
                                    'forward' => [
                                        'module' => 'MelisCmsNews',
                                        'controller' => 'MelisCmsNews',
                                        'action' => 'render-news-page-content',
                                    ],
                                    'interface' => [
                                        'meliscmsnews_content_tabs_properties' => [
                                            'conf' => [
                                                'id' => 'id_meliscmsnews_content_tabs_properties',
                                                'melisKey' => 'meliscmsnews_content_tabs_properties',
                                                'name' => 'tr_meliscmsnews_content_tabs_properties',
                                                'icon' => 'glyphicons tag',
                                            ],
                                            'forward' => [
                                                'module' => 'MelisCmsNews',
                                                'controller' => 'MelisCmsNews',
                                                'action' => 'render-news-page-tabs-main',
                                            ],
                                            'interface' => [
                                                'meliscmsnews_content_tabs_properties_header' => [
                                                    'conf' => [
                                                        'id' => 'id_meliscmsnews_content_tabs_properties_header',
                                                        'melisKey' => 'meliscmsnews_content_tabs_properties_header',
                                                        'name' => 'tr_meliscmsnews_content_tabs_properties_header',
                                                    ],
                                                    'forward' => [
                                                        'module' => 'MelisCmsNews',
                                                        'controller' => 'MelisCmsNews',
                                                        'action' => 'render-news-tabs-content-header',
                                                    ],
                                                    'interface' => [
                                                        'meliscmsnews_content_tabs_properties_header_left' => [
                                                            'conf' => [
                                                                'id' => 'id_meliscmsnews_content_tabs_properties_header_left',
                                                                'melisKey' => 'meliscmsnews_content_tabs_properties_header_left',
                                                                'name' => 'tr_meliscmsnews_content_tabs_properties_header_left',
                                                            ],
                                                            'forward' => [
                                                                'module' => 'MelisCmsNews',
                                                                'controller' => 'MelisCmsNews',
                                                                'action' => 'render-news-tabs-content-header-left',
                                                            ],
                                                            'interface' => [
                                                                'meliscmsnews_content_tabs_properties_header_title' => [
                                                                    'conf' => [
                                                                        'id' => 'id_meliscmsnews_content_tabs_properties_header_title',
                                                                        'melisKey' => 'meliscmsnews_content_tabs_properties_header_title',
                                                                        'name' => 'tr_meliscmsnews_content_tabs_properties',
                                                                    ],
                                                                    'forward' => [
                                                                        'module' => 'MelisCmsNews',
                                                                        'controller' => 'MelisCmsNews',
                                                                        'action' => 'render-news-tabs-content-header-title',
                                                                    ],
                                                                ],
                                                            ],
                                                        ],
                                                        'meliscmsnews_content_tabs_properties_header_right' => [
                                                            'conf' => [
                                                                'id' => 'id_meliscmsnews_content_tabs_properties_header_right',
                                                                'melisKey' => 'meliscmsnews_content_tabs_properties_header_right',
                                                                'name' => 'tr_meliscmsnews_content_tabs_properties_header_right',
                                                            ],
                                                            'forward' => [
                                                                'module' => 'MelisCmsNews',
                                                                'controller' => 'MelisCmsNews',
                                                                'action' => 'render-news-tabs-content-header-left',
                                                            ],
                                                            'interface' => [
                                                                'meliscmsnews_content_tabs_properties_header_status' => [
                                                                    'conf' => [
                                                                        'id' => 'id_meliscmsnews_content_tabs_properties_header_status',
                                                                        'melisKey' => 'meliscmsnews_content_tabs_properties_header_status',
                                                                        'name' => 'tr_meliscmsnews_content_tabs_properties_header_status',
                                                                    ],
                                                                    'forward' => [
                                                                        'module' => 'MelisCmsNews',
                                                                        'controller' => 'MelisCmsNews',
                                                                        'action' => 'render-news-tabs-content-header-status',
                                                                    ],
                                                                    'interface' => [
                                                                    ],
                                                                ],
                                                                'meliscmsnews_content_tabs_properties_header_workflow' => [
                                                                    'conf' => [
                                                                        'id' => 'id_meliscmsnews_content_tabs_properties_header_workflow',
                                                                        'melisKey' => 'meliscmsnews_content_tabs_properties_header_workflow',
                                                                        'name' => '',
                                                                    ],
                                                                    'forward' => [
                                                                        'module' => 'MelisCmsNews',
                                                                        'controller' => 'MelisCmsNews',
                                                                        'action' => 'render-news-tabs-content-header-workflow',
                                                                    ],
                                                                    'interface' => [],
                                                                ],
                                                            ],
                                                        ],
                                                    ],
                                                ],
                                                'meliscmsnews_content_tabs_properties_details' => [
                                                    'conf' => [
                                                        'id' => 'id_meliscmsnews_content_tabs_properties_details',
                                                        'melisKey' => 'meliscmsnews_content_tabs_properties_details',
                                                        'name' => 'tr-meliscmsnews_content_tabs_properties_details',
                                                    ],
                                                    'forward' => [
                                                        'module' => 'MelisCmsNews',
                                                        'controller' => 'MelisCmsNews',
                                                        'action' => 'render-news-tabs-content-details',
                                                    ],
                                                    'interface' => [
                                                        'meliscmsnews_content_tabs_properties_details_main' => [
                                                            'conf' => [
                                                                'id' => 'id_meliscmsnews_content_tabs_properties_details_main',
                                                                'melisKey' => 'meliscmsnews_content_tabs_properties_details_main',
                                                                'name' => 'tr_meliscmsnews_content_tabs_properties_details_main',
                                                            ],
                                                            'forward' => [
                                                                'module' => 'MelisCmsNews',
                                                                'controller' => 'MelisCmsNews',
                                                                'action' => 'render-news-tabs-properties-details-main',
                                                            ],
                                                            'interface' => [
                                                                'meliscmsnews_content_tabs_properties_details_left' => [
                                                                    'conf' => [
                                                                        'id' => 'id_meliscmsnews_content_tabs_properties_details_left',
                                                                        'melisKey' => 'meliscmsnews_content_tabs_properties_details_left',
                                                                        'name' => 'tr_meliscmsnews_content_tabs_properties_details_left',
                                                                    ],
                                                                    'forward' => [
                                                                        'module' => 'MelisCmsNews',
                                                                        'controller' => 'MelisCmsNews',
                                                                        'action' => 'render-news-tabs-properties-details-left',
                                                                    ],
                                                                    'interface' => [
                                                                        'meliscmsnews_content_tabs_properties_details_left_properties' => [
                                                                            'conf' => [
                                                                                'id' => 'id_meliscmsnews_content_tabs_properties_details_left_properties',
                                                                                'melisKey' => 'meliscmsnews_content_tabs_properties_details_left_properties',
                                                                                'name' => 'tr_meliscmsnews_content_tabs_properties_details_left_properties',
                                                                            ],
                                                                            'forward' => [
                                                                                'module' => 'MelisCmsNews',
                                                                                'controller' => 'MelisCmsNews',
                                                                                'action' => 'render-news-tabs-properties-details-left-properties',
                                                                            ],
                                                                        ],
                                                                    ],
                                                                ],
                                                                'meliscmsnews_content_tabs_properties_details_right' => [
                                                                    'conf' => [
                                                                        'id' => 'id_meliscmsnews_content_tabs_properties_details_right',
                                                                        'melisKey' => 'meliscmsnews_content_tabs_properties_details_right',
                                                                        'name' => 'tr_meliscmsnews_content_tabs_properties_details_right',
                                                                    ],
                                                                    'forward' => [
                                                                        'module' => 'MelisCmsNews',
                                                                        'controller' => 'MelisCmsNews',
                                                                        'action' => 'render-news-tabs-properties-details-right',
                                                                    ],
                                                                    'interface' => [
                                                                        'meliscmsnews_content_tabs_properties_details_left_sites' => [
                                                                            'conf' => [
                                                                                'id' => 'id_meliscmsnews_content_tabs_properties_details_left_sites',
                                                                                'melisKey' => 'meliscmsnews_content_tabs_properties_details_left_sites',
                                                                                'name' => 'tr_meliscmsnews_content_tabs_properties_details_left_sites',
                                                                            ],
                                                                            'forward' => [
                                                                                'module' => 'MelisCmsNews',
                                                                                'controller' => 'MelisCmsNews',
                                                                                'action' => 'render-news-tabs-properties-details-left-sites',
                                                                            ],
                                                                        ],
                                                                        'meliscmsnews_content_tabs_properties_details_left_sliders' => [
                                                                            'conf' => [
                                                                                'type' => 'MelisCmsSlider/interface/meliscmsslider_select_slider',
                                                                            ]
                                                                        ],
                                                                    ],
                                                                ],
                                                            ],
                                                        ],
                                                    ],
                                                ],
                                            ],
                                        ],
                                        'meliscmsnews_content_tabs_medias' => [
                                            'conf' => [
                                                'id' => 'id_meliscmsnews_content_tabs_medias',
                                                'melisKey' => 'meliscmsnews_content_tabs_medias',
                                                'name' => 'tr_meliscmsnews_content_tabs_medias',
                                                'icon' => 'glyphicons picture',
                                            ],
                                            'forward' => [
                                                'module' => 'MelisCmsNews',
                                                'controller' => 'MelisCmsNews',
                                                'action' => 'render-news-page-tabs-main',
                                            ],
                                            'interface' => [
                                                'meliscmsnews_content_tabs_medias_header' => [
                                                    'conf' => [
                                                        'id' => 'id_meliscmsnews_content_tabs_medias_header',
                                                        'melisKey' => 'meliscmsnews_content_tabs_medias_header',
                                                    ],
                                                    'forward' => [
                                                        'module' => 'MelisCmsNews',
                                                        'controller' => 'MelisCmsNews',
                                                        'action' => 'render-news-tabs-content-header',
                                                    ],
                                                    'interface' => [
                                                        'meliscmsnews_content_tabs_medias_header_left' => [
                                                            'conf' => [
                                                                'id' => 'id_meliscmsnews_content_tabs_medias_header_left',
                                                                'melisKey' => 'meliscmsnews_content_tabs_medias_header_left',
                                                            ],
                                                            'forward' => [
                                                                'module' => 'MelisCmsNews',
                                                                'controller' => 'MelisCmsNews',
                                                                'action' => 'render-news-tabs-content-header-left',
                                                            ],
                                                            'interface' => [
                                                                'meliscmsnews_content_tabs_medias_header_title' => [
                                                                    'conf' => [
                                                                        'id' => 'id_meliscmsnews_content_tabs_medias_header_title',
                                                                        'melisKey' => 'meliscmsnews_content_tabs_medias_header_title',
                                                                        'name' => 'tr_meliscmsnews_content_tabs_medias',
                                                                    ],
                                                                    'forward' => [
                                                                        'module' => 'MelisCmsNews',
                                                                        'controller' => 'MelisCmsNews',
                                                                        'action' => 'render-news-tabs-content-header-title',
                                                                    ],
                                                                ],
                                                            ],
                                                        ],
                                                        'meliscmsnews_content_tabs_medias_header_right' => [
                                                            'conf' => [
                                                                'id' => 'id_meliscmsnews_content_tabs_medias_header_right',
                                                                'melisKey' => 'meliscmsnews_content_tabs_medias_header_right',
                                                            ],
                                                            'forward' => [
                                                                'module' => 'MelisCmsNews',
                                                                'controller' => 'MelisCmsNews',
                                                                'action' => 'render-news-tabs-content-header-left',
                                                            ],
                                                            'interface' => [
                                                                'meliscmsnews_content_tabs_medias_details' => [
                                                                    'conf' => [
                                                                        'id' => 'id_meliscmsnews_content_tabs_medias_details',
                                                                        'melisKey' => 'meliscmsnews_content_tabs_medias_details',
                                                                    ],
                                                                    'forward' => [
                                                                        'module' => 'MelisCmsNews',
                                                                        'controller' => 'MelisCmsNews',
                                                                        'action' => 'render-news-tabs-content-details',
                                                                    ],
                                                                    'interface' => [

                                                                    ],
                                                                ],
                                                            ],
                                                        ],
                                                    ],
                                                ],
                                                'meliscmsnews_content_tabs_medias_details_main' => [
                                                    'conf' => [
                                                        'id' => 'id_meliscmsnews_content_tabs_medias_details_main',
                                                        'melisKey' => 'meliscmsnews_content_tabs_medias_details_main',
                                                    ],
                                                    'forward' => [
                                                        'module' => 'MelisCmsNews',
                                                        'controller' => 'MelisCmsNews',
                                                        'action' => 'render-news-tabs-properties-details-main',
                                                    ],
                                                    'interface' => [
                                                        'meliscmsnews_content_tabs_medias_details_left' => [
                                                            'conf' => [
                                                                'id' => 'id_meliscmsnews_content_tabs_medias_details_left',
                                                                'melisKey' => 'meliscmsnews_content_tabs_medias_details_left',
                                                            ],
                                                            'forward' => [
                                                                'module' => 'MelisCmsNews',
                                                                'controller' => 'MelisCmsNews',
                                                                'action' => 'render-news-tabs-properties-details-left',
                                                            ],
                                                            'interface' => [
                                                                'meliscmsnews_content_tabs_properties_details_left_images' => [
                                                                    'conf' => [
                                                                        'id' => 'id_meliscmsnews_content_tabs_properties_details_left_images',
                                                                        'melisKey' => 'meliscmsnews_content_tabs_properties_details_left_images',
                                                                        'name' => 'tr_meliscmsnews_content_tabs_properties_details_left_images',
                                                                    ],
                                                                    'forward' => [
                                                                        'module' => 'MelisCmsNews',
                                                                        'controller' => 'MelisCmsNews',
                                                                        'action' => 'render-news-tabs-properties-details-left-images',
                                                                    ],
                                                                ],
                                                            ],
                                                        ],
                                                        'meliscmsnews_content_tabs_medias_details_right' => [
                                                            'conf' => [
                                                                'id' => 'id_meliscmsnews_content_tabs_medias_details_right',
                                                                'melisKey' => 'meliscmsnews_content_tabs_medias_details_right',
                                                            ],
                                                            'forward' => [
                                                                'module' => 'MelisCmsNews',
                                                                'controller' => 'MelisCmsNews',
                                                                'action' => 'render-news-tabs-properties-details-right',
                                                            ],
                                                            'interface' => [
                                                                'meliscmsnews_content_tabs_properties_details_left_documents' => [
                                                                    'conf' => [
                                                                        'id' => 'id_meliscmsnews_content_tabs_properties_details_left_documents',
                                                                        'melisKey' => 'meliscmsnews_content_tabs_properties_details_left_documents',
                                                                        'name' => 'tr_meliscmsnews_content_tabs_properties_details_left_documents',
                                                                    ],
                                                                    'forward' => [
                                                                        'module' => 'MelisCmsNews',
                                                                        'controller' => 'MelisCmsNews',
                                                                        'action' => 'render-news-tabs-properties-details-left-documents',
                                                                    ],
                                                                ],
                                                            ],
                                                        ],
                                                    ],
                                                ],
                                            ],
                                        ],
                                        'meliscmsnews_content_tabs_texts' => [
                                            'conf' => [
                                                'id' => 'id_meliscmsnews_content_tabs_texts',
                                                'melisKey' => 'meliscmsnews_content_tabs_texts',
                                                'name' => 'tr_meliscmsnews_content_tabs_texts',
                                                'icon' => 'glyphicons pencil',
                                            ],
                                            'forward' => [
                                                'module' => 'MelisCmsNews',
                                                'controller' => 'MelisCmsNews',
                                                'action' => 'render-news-page-tabs-main',
                                            ],
                                            'interface' => [
                                                'meliscmsnews_content_tabs_texts_header' => [
                                                    'conf' => [
                                                        'id' => 'id_meliscmsnews_content_tabs_texts_header',
                                                        'melisKey' => 'meliscmsnews_content_tabs_texts_header',
                                                    ],
                                                    'forward' => [
                                                        'module' => 'MelisCmsNews',
                                                        'controller' => 'MelisCmsNews',
                                                        'action' => 'render-news-tabs-content-header',
                                                    ],
                                                    'interface' => [
                                                        'meliscmsnews_content_tabs_texts_header_left' => [
                                                            'conf' => [
                                                                'id' => 'id_meliscmsnews_content_tabs_texts_header_left',
                                                                'melisKey' => 'meliscmsnews_content_tabs_texts_header_left',
                                                            ],
                                                            'forward' => [
                                                                'module' => 'MelisCmsNews',
                                                                'controller' => 'MelisCmsNews',
                                                                'action' => 'render-news-tabs-content-header-left',
                                                            ],
                                                            'interface' => [
                                                                'meliscmsnews_content_tabs_texts_header_title' => [
                                                                    'conf' => [
                                                                        'id' => 'id_meliscmsnews_content_tabs_texts_header_title',
                                                                        'melisKey' => 'meliscmsnews_content_tabs_texts_header_title',
                                                                        'name' => 'tr_meliscmsnews_content_tabs_texts',
                                                                    ],
                                                                    'forward' => [
                                                                        'module' => 'MelisCmsNews',
                                                                        'controller' => 'MelisCmsNews',
                                                                        'action' => 'render-news-tabs-content-header-title',
                                                                    ],
                                                                ],
                                                            ],
                                                        ],
                                                        'meliscmsnews_content_tabs_texts_header_right' => [
                                                            'conf' => [
                                                                'id' => 'id_meliscmsnews_content_tabs_texts_header_right',
                                                                'melisKey' => 'meliscmsnews_content_tabs_texts_header_right',
                                                            ],
                                                            'forward' => [
                                                                'module' => 'MelisCmsNews',
                                                                'controller' => 'MelisCmsNews',
                                                                'action' => 'render-news-tabs-content-header-left',
                                                            ],
                                                            'interface' => [
                                                            ],
                                                        ],
                                                    ],
                                                ],
                                                'meliscmsnews_content_tabs_medias_details_main' => [
                                                    'conf' => [
                                                        'id' => 'id_meliscmsnews_content_tabs_medias_details_main',
                                                        'melisKey' => 'meliscmsnews_content_tabs_medias_details_main',
                                                    ],
                                                    'forward' => [
                                                        'module' => 'MelisCmsNews',
                                                        'controller' => 'MelisCmsNews',
                                                        'action' => 'render-news-tabs-properties-details-main',
                                                    ],
                                                    'interface' => [
                                                        'meliscmsnews_content_tabs_properties_details_right_paragraphs' => [
                                                            'conf' => [
                                                                'id' => 'id_meliscmsnews_content_tabs_properties_details_right_paragraphs',
                                                                'melisKey' => 'meliscmsnews_content_tabs_properties_details_right_paragraphs',
                                                                'name' => 'tr_meliscmsnews_content_tabs_properties_details_right_paragraphs',
                                                            ],
                                                            'forward' => [
                                                                'module' => 'MelisCmsNews',
                                                                'controller' => 'MelisCmsNews',
                                                                'action' => 'render-news-tabs-properties-details-right-paragraphs',
                                                            ],
                                                        ],
                                                    ],
                                                ],
                                            ],
                                        ],
                                        'meliscmsnews_content_tabs_seo' => [
                                            'conf' => [
                                                'id' => 'id_meliscmsnews_content_tabs_seo',
                                                'name' => 'tr_meliscmsnews_content_tabs_seo',
                                                'melisKey' => 'meliscmsnews_content_tabs_seo',
                                                'icon' => 'glyphicons search',
                                            ],
                                            'forward' => [
                                                'module' => 'MelisCmsNews',
                                                'controller' => 'MelisCmsNews',
                                                'action' => 'render-news-page-tabs-main',
                                                'jscallback' => '',
                                                'jsdatas' => array()
                                            ],
                                            'interface' => [
                                                'meliscmsnews_content_tabs_seo_header' => [
                                                    'conf' => [
                                                        'id' => 'id_meliscmsnews_content_tabs_seo_header',
                                                        'melisKey' => 'meliscmsnews_content_tabs_seo_header',
                                                    ],
                                                    'forward' => [
                                                        'module' => 'MelisCmsNews',
                                                        'controller' => 'MelisCmsNews',
                                                        'action' => 'render-news-tabs-content-header',
                                                    ],
                                                    'interface' => [
                                                        'meliscmsnews_content_tabs_seo_header_left' => [
                                                            'conf' => [
                                                                'id' => 'id_meliscmsnews_content_tabs_seo_header_left',
                                                                'melisKey' => 'meliscmsnews_content_tabs_seo_header_left',
                                                            ],
                                                            'forward' => [
                                                                'module' => 'MelisCmsNews',
                                                                'controller' => 'MelisCmsNews',
                                                                'action' => 'render-news-tabs-content-header-left',
                                                            ],
                                                            'interface' => [
                                                                'meliscmsnews_content_tabs_seo_header_title' => [
                                                                    'conf' => [
                                                                        'id' => 'id_meliscmsnews_content_tabs_seo_header_title',
                                                                        'melisKey' => 'meliscmsnews_content_tabs_seo_header_title',
                                                                        'name' => 'tr_meliscmsnews_content_tabs_seo',
                                                                    ],
                                                                    'forward' => [
                                                                        'module' => 'MelisCmsNews',
                                                                        'controller' => 'MelisCmsNews',
                                                                        'action' => 'render-news-tabs-content-header-title',
                                                                    ],
                                                                ],
                                                            ],
                                                        ],
                                                        'meliscmsnews_content_tabs_seo_header_right' => [
                                                            'conf' => [
                                                                'id' => 'id_meliscmsnews_content_tabs_seo_header_right',
                                                                'melisKey' => 'meliscmsnews_content_tabs_seo_header_right',
                                                            ],
                                                            'forward' => [
                                                                'module' => 'MelisCmsNews',
                                                                'controller' => 'MelisCmsNews',
                                                                'action' => 'render-news-tabs-content-header-left',
                                                            ],
                                                            'interface' => [
                                                            ],
                                                        ],
                                                    ],
                                                ],
                                                'meliscmsnews_content_tabs_seo_details_main' => [
                                                    'conf' => [
                                                        'id' => 'id_meliscmsnews_content_tabs_seo_details_main',
                                                        'melisKey' => 'meliscmsnews_content_tabs_seo_details_main',
                                                    ],
                                                    'forward' => [
                                                        'module' => 'MelisCmsNews',
                                                        'controller' => 'MelisCmsNews',
                                                        'action' => 'render-news-tabs-content-seo-details-main',
                                                    ],
                                                    'interface' => [
                                                        'meliscmsnews_content_tabs_seo_details' => [
                                                            'conf' => [
                                                                'id' => 'id_meliscmsnews_content_tabs_seo_details',
                                                                'melisKey' => 'meliscmsnews_content_tabs_seo_details',
                                                                'name' => 'tr_meliscmsnews_content_tabs_seo_details',
                                                            ],
                                                            'forward' => [
                                                                'module' => 'MelisCmsNews',
                                                                'controller' => 'MelisCmsNews',
                                                                'action' => 'render-news-tabs-seo-details',
                                                            ],
                                                        ],
                                                    ],
                                                ],
                                            ],
                                        ],
                                        'meliscmsnews_content_tabs_comments' => [],
                                        /*for the workflow*/
                                        'meliscmsnews_workflow_comments' => [
                                            'conf' => [
                                                'id' => 'id_meliscmsnews_workflow_tabs_comments',
                                                'name' => 'tr_meliscmsnews_workflow_tab_comments',
                                                'melisKey' => 'meliscmsnews_workflow_comments',
                                                'icon' => 'glyphicons history',
                                            ],
                                            'forward' => [
                                                'module' => 'MelisCmsNews',
                                                'controller' => 'MelisCmsNewsWorkflowComments',
                                                'action' => 'render-news-workflow-comments',
                                                'jscallback' => '',
                                                'jsdatas' => [],
                                            ],
                                            'interface' => [
                                                'meliscmsnews_news_comments_add' => [
                                                    'conf' => [
                                                        'id' => 'id_meliscmsnews_center_page_tabs_comments_add',
                                                        'name' => 'tr_meliscmsnews_page_tab_comments_Add comment',
                                                        'melisKey' => 'meliscmsnews_page_comments_add',
                                                    ],
                                                    'forward' => [
                                                        'module' => 'MelisCmsNews',
                                                        'controller' => 'MelisCmsNewsWorkflowComments',
                                                        'action' => 'render-news-workflow-comments-add',
                                                        'jscallback' => '',
                                                        'jsdatas' => [],
                                                    ],
                                                ],
                                                'meliscmsnews_workflow_comments_timeline' => [
                                                    'conf' => [
                                                        'id' => 'id_meliscmsnews_center_workflow_tabs_comments_timeline',
                                                        'name' => 'tr_meliscmsnews_workflow_tab_comments_Timeline',
                                                        'melisKey' => 'meliscmsnews_workflow_comments_timeline',
                                                    ],
                                                    'forward' => [
                                                        'module' => 'MelisCmsNews',
                                                        'controller' => 'MelisCmsNewsWorkflowComments',
                                                        'action' => 'render-news-workflow-comments-timeline',
                                                        'jscallback' => '',
                                                        'jsdatas' => [],
                                                    ],
                                                ],
                                            ],
                                        ],

                                        /** Start of news preview */
                                        'meliscmsnews_tabs_preview' => [
                                            'conf' => [
                                                'id' => 'id_meliscmsnews_tabs_preview',
                                                'melisKey' => 'meliscmsnews_tabs_preview',
                                                'name' => 'tr_meliscmsnews_preview_tab_name',
                                                'icon' => 'glyphicons imac',
                                            ],
                                            'forward' => [
                                                'module' => 'MelisCmsNews',
                                                'controller' => 'MelisCmsNews',
                                                'action' => 'preview-tab-container',
                                            ],
                                            'interface' => [
                                                'meliscmsnews_tabs_preview_content_container' => [
                                                    'conf' => [
                                                        'id' => 'id_meliscmsnews_tabs_preview_content_container',
                                                        'melisKey' => 'meliscmsnews_tabs_preview_content_container',
                                                    ],
                                                    'forward' => [
                                                        'module' => 'MelisCmsNews',
                                                        'controller' => 'MelisCmsNews',
                                                        'action' => 'preview-tab-content-container',
                                                    ],
                                                    'interface' => [
                                                        'meliscmsnews_tabs_preview_content' => [
                                                            'conf' => [
                                                                'id' => 'id_meliscmsnews_tabs_preview_content',
                                                                'melisKey' => 'meliscmsnews_tabs_preview_content',
                                                            ],
                                                            'forward' => [
                                                                'module' => 'MelisCmsNews',
                                                                'controller' => 'MelisCmsNews',
                                                                'action' => 'preview-tab-content',
                                                            ],
                                                            'interface' => [
                                                                'meliscmsnews_tabs_preview_iframe' => [
                                                                    'conf' => [
                                                                        'id' => 'id_meliscmsnews_tabs_preview_iframe',
                                                                        'melisKey' => 'meliscmsnews_tabs_preview_iframe',
                                                                    ],
                                                                    'forward' => [
                                                                        'module' => 'MelisCmsNews',
                                                                        'controller' => 'MelisCmsNews',
                                                                        'action' => 'preview-tab-iframe',
                                                                    ],
                                                                ]
                                                            ],
                                                        ],
                                                    ],
                                                ],
                                            ],
                                        ],
                                        /** End of news preview */
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                'meliscmsnews_modal' => [
                    'conf' => [
                        'id' => 'id_meliscmsnews_modal',
                        'name' => 'tr_meliscmsnews_modal',
                        'melisKey' => 'meliscmsnews_modal',
                    ],
                    'forward' => [
                        'module' => 'MelisCmsNews',
                        'controller' => 'MelisCmsNews',
                        'action' => 'render-modal',
                    ],
                    'interface' => [
                        'meliscmsnews_modal_documents_form' => [
                            'conf' => [
                                'id' => 'id_meliscmsnews_modal_documents_form',
                                'name' => 'tr_meliscmsnews_add_file',
                                'melisKey' => 'meliscmsnews_modal_documents_form',
                            ],
                            'forward' => [
                                'module' => 'MelisCmsNews',
                                'controller' => 'MelisCmsNews',
                                'action' => 'render-modal-form',
                            ],
                        ],
                        'meliscmsnews_modal_documents_form_image' => [
                            'conf' => [
                                'id' => 'id_meliscmsnews_modal_documents_form_image',
                                'name' => 'tr_meliscmsnews_modal_documents_form_image',
                                'melisKey' => 'meliscmsnews_modal_documents_form_image',
                            ],
                            'forward' => [
                                'module' => 'MelisCmsNews',
                                'controller' => 'MelisCmsNews',
                                'action' => 'render-modal-form',
                            ],
                        ],
                    ],
                ],
                'meliscmsnews_workflow_modal_content' => [
                    'conf' => [
                        'id' => 'id_meliscmsnews_workflow_modal_content',
                        'name' => 'tr_meliscmsnews_workflow_modal_content',
                        'melisKey' => 'meliscmsnews_workflow_modal_content',
                    ],
                    'forward' => [
                        'module' => 'MelisCmsNews',
                        'controller' => 'MelisCmsNewsWorkflow',
                        'action' => 'render-workflow-modal-content',
                        'jscallback' => '',
                        'jsdatas' => [],
                    ],
                    'interface' => [
                        'meliscmsnews_workflow_modal_content_actions' => [
                            'conf' => [
                                'id' => 'id_meliscmsnews_workflow_modal_content_actions',
                                'name' => 'tr_meliscmsnews_workflow_modal_content_actions',
                                'melisKey' => 'meliscmsnews_workflow_modal_content_actions',
                            ],
                            'forward' => [
                                'module' => 'MelisCmsNews',
                                'controller' => 'MelisCmsNewsWorkflow',
                                'action' => 'render-workflow-modal-content-actions',
                                'jscallback' => '',
                                'jsdatas' => [],
                            ],
                            'interface' => [
                                'meliscmsnews_workflow_modal_content_actions_ask' => [
                                    'conf' => [
                                        'id' => 'id_meliscmsnews_workflow_modal_content_actions_ask',
                                        'name' => 'tr_meliscmsnews_workflow_modal_content_actions_ask',
                                        'melisKey' => 'meliscmsnews_workflow_modal_content_actions_ask',
                                    ],
                                    'forward' => [
                                        'module' => 'MelisCmsNews',
                                        'controller' => 'MelisCmsNewsWorkflow',
                                        'action' => 'render-workflow-modal-content-actions-ask',
                                        'jscallback' => '',
                                        'jsdatas' => [],
                                    ],
                                ],
                            ],
                        ],
                        'meliscmsnews_workflow_modal_content_actions_zones' => [
                            'interface' => [
                                'meliscmsnews_workflow_modal_content_actions_zones_ask' => [
                                    'conf' => [
                                        'id' => 'id_meliscmsnews_workflow_modal_content_actions_zones_ask',
                                        'name' => 'tr_meliscmsnews_workflow_modal_content_actions_zones_ask',
                                        'melisKey' => 'meliscmsnews_workflow_modal_content_actions_zones_ask',
                                    ],
                                    'forward' => [
                                        'module' => 'MelisCmsNews',
                                        'controller' => 'MelisCmsNewsWorkflow',
                                        'action' => 'render-workflow-modal-content-actions-zone-ask',
                                        'jscallback' => '',
                                        'jsdatas' => [],
                                    ],
                                ],
                            ],
                        ],
                        'meliscmsnews_workflow_modal_content_historic' => [
                            'conf' => [
                                'id' => 'id_meliscmsnews_workflow_modal_content_historic',
                                'name' => 'tr_meliscmsnews_workflow_modal_content_historic',
                                'melisKey' => 'meliscmsnews_workflow_modal_content_historic',
                            ],
                            'forward' => [
                                'module' => 'MelisCmsNews',
                                'controller' => 'MelisCmsNewsWorkflow',
                                'action' => 'render-workflow-modal-content-historic',
                                'jscallback' => '',
                                'jsdatas' => [],
                            ],
                            'interface' => [
                                'meliscmsnews_workflow_modal_content_actions_validate' => [
                                    'conf' => [
                                        'id' => 'id_meliscmsnews_workflow_modal_content_actions_validate',
                                        'name' => 'tr_meliscmsnews_workflow_modal_content_actions_validate',
                                        'melisKey' => 'meliscmsnews_workflow_modal_content_actions_validate',
                                    ],
                                    'forward' => [
                                        'module' => 'MelisCmsNews',
                                        'controller' => 'MelisCmsNewsWorkflow',
                                        'action' => 'render-workflow-modal-content-actions-validate',
                                        'jscallback' => '',
                                        'jsdatas' => [],
                                    ],
                                ],
                                'meliscmsnews_workflow_modal_content_actions_refuse' => [
                                    'conf' => [
                                        'id' => 'id_meliscmsnews_workflow_modal_content_actions_refuse',
                                        'name' => 'tr_meliscmsnews_workflow_modal_content_actions_refuse',
                                        'melisKey' => 'meliscmsnews_workflow_modal_content_actions_refuse',
                                    ],
                                    'forward' => [
                                        'module' => 'MelisCmsNews',
                                        'controller' => 'MelisCmsNewsWorkflow',
                                        'action' => 'render-workflow-modal-content-actions-refuse',
                                        'jscallback' => '',
                                        'jsdatas' => [],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                /*for the news workflow dashboard plugin*/
                'meliscmsnews_dashboard_workflow_comment_modal' => [
                    'conf' => [
                        'id' => 'id_meliscmsnews_dashboard_workflow_comment_modal',
                        'name' => 'tr_meliscmsnews_dashboard_workflow_comment_modal',
                        'melisKey' => 'meliscmsnews_dashboard_workflow_comment_modal',
                    ],
                    'forward' => [
                        'module' => 'MelisCmsNews',
                        'controller' => 'MelisCmsNewsWorkflow',
                        'action' => 'workflowCommentModal',
                        'jscallback' => '',
                        'jsdatas' => [],
                    ],
                    'interface' => [
                        'meliscmsnews_dashboard_workflow_comment_modal_content' => array(
                            'conf' => array(
                                'id' => 'id_meliscmsnews_dashboard_workflow_comment_modal_content',
                                'name' => 'tr_meliscmsnews_dashboard_workflow_comment_modal_content',
                                'melisKey' => 'meliscmsnews_dashboard_workflow_comment_modal_content',
                            ),
                            'forward' => array(
                                'module' => 'MelisCmsNews',
                                'controller' => 'MelisCmsNewsWorkflow',
                                'action' => 'workflowCommentModalContent',
                            ),
                        )
                    ],
                ],
            ],
        ],
    ],
];
