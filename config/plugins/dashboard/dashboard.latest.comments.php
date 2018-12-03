<?php
/**
 * Used to add configurations when using MelisCmsComments module's
 * Latest comments Dashboard plugin
 */
return [
    'plugins' => [
        'meliscmscomments' => [
            'dashboard_plugins' => [
                'LatestCommentsPlugin' => [
                    'PostTypes' => [
                        'news' => [
                            'icon' => 'fa fa-list-alt fa-2x',
                        ],
                    ],
                    /** Dashboard Latest News Comments Interface */
                    'interface' => [
                        'dashboard_latest_comments' => [
                            'interface' => [
                                'dashboard_latest_comments_tab_container' => [
                                    'interface' => [
                                        'news_comments_tab_content' => [
                                            'conf' => array(
                                                'id' => 'id_news_comments_tab_content',
                                                'name' => 'tr_news_comments_tab_content',
                                                'melisKey' => 'news_comments_tab_content',
                                            ),
                                            'forward' => array(
                                                'module' => 'MelisCmsNews',
                                                'plugin' => 'DashboardLatestComments',
                                                'function' => 'tabContentAction',
                                            ),
//                                            'interface' => [
//                                    'dashboard_latest_comments_filters' => [
//                                        'conf' => array(
//                                            'id' => 'id_dashboard_latest_comments_filters',
//                                            'name' => 'tr_dashboard_latest_comments_filters',
//                                            'melisKey' => 'dashboard_latest_comments_filters',
//                                        ),
//                                        'forward' => array(
//                                            'module' => 'MelisCmsComments',
//                                            'plugin' => 'LatestCommentsPlugin',
//                                            'function' => 'filtersAction',
//                                        ),
//                                    ],
//                                    'dashboard_latest_comments_list' => [
//                                        'conf' => [
//                                            'id' => 'id_dashboard_latest_comments_list',
//                                            'name' => 'tr_dashboard_latest_comments_list',
//                                            'melisKey' => 'dashboard_latest_comments_list',
//                                        ],
//                                        'forward' => [
//                                            'module' => 'MelisCmsComments',
//                                            'plugin' => 'LatestCommentsPlugin',
//                                            'function' => 'commentsListAction',
//                                        ],
//                                    ],
//                                            ],
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
