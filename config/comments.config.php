<?php
/**
 * Configurations for extending the MelisCmsComments module
 * to have commenting functionality inside news posts
 */
return array(
    'plugins' => array(
        'meliscmsnews' => array(
            'ressources' => array(
                'js' => array(),
                'css' => array(),
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
            ),
            'interface' => array(
                'meliscmsnews' => array(
                    'conf' => array(
                        'comment_type' => 'NEWS'
                    ),
                    'interface' => array(
                        'meliscmsnews_page' => array(
                            'interface' => array(
                                'meliscmsnews_content' => array(
                                    'interface' => array(
                                        'meliscmsnews_content_tabs_properties' => array(
                                            'interface' => array(
                                                'meliscmsnews_content_tabs_properties_details' => array(
                                                    'interface' => array(
                                                        'meliscmsnews_content_tabs_properties_details_main' => array(
                                                            'interface' => array(
                                                                'meliscmsnews_content_tabs_properties_details_left' => array(
                                                                    'interface' => array(
                                                                        'meliscms_comments_validate_comments_news' => array(
                                                                            'conf' => array(
                                                                                'id'   => 'id_meliscms_comments_validate_comments',
                                                                                'name' => 'tr_meliscms_comments_validate_comments',
                                                                                'melisKey' => 'meliscms_comments_validate_comments',
                                                                                'postType' => 'news',
                                                                            ),
                                                                            'forward' => array(
                                                                                'module' => 'MelisCmsComments',
                                                                                'controller' => 'MelisCmsCommentsTab',
                                                                                'action' => 'comments-validate-comments',
                                                                                'jscallback' => '',
                                                                                'jsdatas' => array()
                                                                            ),
                                                                        ),
                                                                    ),
                                                                ),
                                                            ),
                                                        ),
                                                    ),
                                                ),
                                            ),
                                        ),
                                        'meliscmsnews_content_tabs_comments' => array(
                                            'conf' => array(
                                                'id' => 'id_meliscmsnews_content_tabs_comments',
                                                'melisKey' => 'meliscmsnews_content_tabs_comments',
                                                'name' => 'tr_meliscms_comments_module_name',
                                                'icon' => 'glyphicons comments',
                                            ),
                                            'forward' => array(
                                                'module' => 'MelisCmsComments',
                                                'controller' => 'MelisCmsCommentsTab',
                                                'action' => 'tab-container',
                                            ),
                                            'interface' => array(
                                                'meliscmsnews_comments_content_container' => array(
                                                    'conf' => array(
                                                        'id' => 'id_meliscmsnews_comments_content_container',
                                                        'melisKey' => 'meliscmsnews_comments_content_container',
                                                    ),
                                                    'forward' => array(
                                                        'module' => 'MelisCmsComments',
                                                        'controller' => 'MelisCmsCommentsTab',
                                                        'action' => 'content-container',
                                                    ),
                                                    'interface' => array(
                                                        // Add comment button
                                                        'meliscmscomments_add_comment' => array(
                                                            'conf' => array(
                                                                'id' => 'id_meliscmscomments_add_comment',
                                                                'postType' => 'news',
                                                                'melisKey' => 'meliscmscomments_add_comment',
                                                                'name' => 'tr_meliscmscomments_add_comment',
                                                            ),
                                                            'forward' => array(
                                                                'module' => 'MelisCmsComments',
                                                                'controller' => 'MelisCmsCommentsTab',
                                                                'action' => 'comments-add-btn',
                                                            ),
                                                        ),
                                                        'meliscmsnews_comments_table' => array(
                                                            'conf' => array(
                                                                'id' => 'id_meliscmsnews_comments_table',
                                                                'melisKey' => 'meliscmsnews_comments_table',
                                                                'name' => 'tr_meliscms_comments_table',
                                                            ),
                                                            'forward' => array(
                                                                'module' => 'MelisCmsComments',
                                                                'controller' => 'MelisCmsCommentsTab',
                                                                'action' => 'comments-table',
                                                            ),
                                                        ),
                                                    ),
                                                ),

                                                // Modal: Add or Edit comments
                                                'meliscmscomments_plugin_props_modal_container' => array(
                                                    'conf' => array(
                                                        'id'   => 'id_meliscmscomments_plugin_props_modal_container',
                                                        'name' => 'tr_meliscmscomments_plugin_props_modal_container',
                                                        'melisKey' => 'meliscmscomments_plugin_props_modal_container',
                                                    ),
                                                    'forward' => array(
                                                        'module' => 'MelisCmsComments',
                                                        'controller' => 'MelisCmsCommentsTab',
                                                        'action' => 'comments-modal-container',
                                                    ),
                                                    'interface' => array(
                                                        'meliscmscomments_plugin_props_modal_content' => array(
                                                            'conf' => array(
                                                                'id' => 'id_meliscmscomments_plugin_props_modal_content',
                                                                'name' => 'tr_meliscmscomments_plugin_props_modal_content',
                                                                'melisKey' => 'meliscmscomments_plugin_props_modal_content',
                                                            ),
                                                            'forward' => array(
                                                                'module' => 'MelisCmsComments',
                                                                'controller' => 'MelisCmsCommentsTab',
                                                                'action' => 'comments-modal-content',
                                                            ),
                                                        ),
                                                    ),
                                                ),
                                            ),
                                        ),
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
);