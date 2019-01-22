<?php
/**
 * Configurations for extending the MelisCmsComments module
 * to have commenting functionality inside news posts
 */
return [
    'plugins' => [
        'meliscmsnews' => [
            'ressources' => [
                'js' => [],
                'css' => [],
                /**
                 * the "build" configuration compiles all assets into one file to make
                 * lesser requests
                 */
                'build' => [
                    // lists of assets that will be loaded in the layout
                    'css' => [
                        '/MelisCmsComments/build/css/bundle.css',
                    ],
                    'js' => [
                        '/MelisCmsComments/build/js/bundle.js',
                    ]
                ]
            ],
            'interface' => [
                'meliscmsnews' => [
                    'conf' => [
                        'comment_type' => 'NEWS'
                    ],
                    'interface' => [
                        'meliscmsnews_page' => [
                            'interface' => [
                                'meliscmsnews_content' => [
                                    'interface' => [
                                        'meliscmsnews_content_tabs_properties' => [
                                            'interface' => [
                                                'meliscmsnews_content_tabs_properties_details' => [
                                                    'interface' => [
                                                        'meliscmsnews_content_tabs_properties_details_main' => [
                                                            'interface' => [
                                                                'meliscmsnews_content_tabs_properties_details_left' => [
                                                                    'interface' => [
                                                                        'meliscms_comments_validate_comments_news' => [
                                                                            'conf' => [
                                                                                'id' => 'id_meliscms_comments_validate_comments',
                                                                                'name' => 'tr_meliscms_comments_validate_comments',
                                                                                'melisKey' => 'meliscms_comments_validate_comments',
                                                                                'postType' => 'news',
                                                                            ],
                                                                            'forward' => [
                                                                                'module' => 'MelisCmsComments',
                                                                                'controller' => 'MelisCmsCommentsTab',
                                                                                'action' => 'comments-validate-comments',
                                                                                'jscallback' => '',
                                                                                'jsdatas' => []
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
                                        'meliscmsnews_content_tabs_comments' => [
                                            'conf' => [
                                                'id' => 'id_meliscmsnews_content_tabs_comments',
                                                'melisKey' => 'meliscmsnews_content_tabs_comments',
                                                'name' => 'tr_meliscms_comments_module_name',
                                                'icon' => 'glyphicons comments',
                                            ],
                                            'forward' => [
                                                'module' => 'MelisCmsComments',
                                                'controller' => 'MelisCmsCommentsTab',
                                                'action' => 'tab-container',
                                            ],
                                            'interface' => [
                                                'meliscmsnews_comments_content_container' => [
                                                    'conf' => [
                                                        'id' => 'id_meliscmsnews_comments_content_container',
                                                        'melisKey' => 'meliscmsnews_comments_content_container',
                                                    ],
                                                    'forward' => [
                                                        'module' => 'MelisCmsComments',
                                                        'controller' => 'MelisCmsCommentsTab',
                                                        'action' => 'content-container',
                                                    ],
                                                    'interface' => [
                                                        // Add comment button
                                                        'meliscmscomments_add_comment' => [
                                                            'conf' => [
                                                                'id' => 'id_meliscmscomments_add_comment',
                                                                'postType' => 'news',
                                                                'melisKey' => 'meliscmscomments_add_comment',
                                                                'name' => 'tr_meliscmscomments_add_comment',
                                                            ],
                                                            'forward' => [
                                                                'module' => 'MelisCmsComments',
                                                                'controller' => 'MelisCmsCommentsTab',
                                                                'action' => 'comments-add-btn',
                                                            ],
                                                        ],
                                                        'meliscmsnews_comments_table' => [
                                                            'conf' => [
                                                                'id' => 'id_meliscmsnews_comments_table',
                                                                'melisKey' => 'meliscmsnews_comments_table',
                                                                'name' => 'tr_meliscms_comments_table',
                                                            ],
                                                            'forward' => [
                                                                'module' => 'MelisCmsComments',
                                                                'controller' => 'MelisCmsCommentsTab',
                                                                'action' => 'comments-table',
                                                            ],
                                                        ],
                                                    ],
                                                ],

                                                // Modal: Add or Edit comments
                                                'meliscmscomments_plugin_props_modal_container' => [
                                                    'conf' => [
                                                        'id' => 'id_meliscmscomments_plugin_props_modal_container',
                                                        'name' => 'tr_meliscmscomments_plugin_props_modal_container',
                                                        'melisKey' => 'meliscmscomments_plugin_props_modal_container',
                                                    ],
                                                    'forward' => [
                                                        'module' => 'MelisCmsComments',
                                                        'controller' => 'MelisCmsCommentsTab',
                                                        'action' => 'comments-modal-container',
                                                    ],
                                                    'interface' => [
                                                        'meliscmscomments_plugin_props_modal_content' => [
                                                            'conf' => [
                                                                'id' => 'id_meliscmscomments_plugin_props_modal_content',
                                                                'name' => 'tr_meliscmscomments_plugin_props_modal_content',
                                                                'melisKey' => 'meliscmscomments_plugin_props_modal_content',
                                                            ],
                                                            'forward' => [
                                                                'module' => 'MelisCmsComments',
                                                                'controller' => 'MelisCmsCommentsTab',
                                                                'action' => 'comments-modal-content',
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
                    ],
                ],
            ],
        ],
    ],
];