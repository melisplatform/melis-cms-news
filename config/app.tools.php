<?php

return array(
    'plugins' => array(
        'meliscmsnews' => array(
            'tools' => array(
                'meliscmsnews_list_table' => array(
                    'table' => array(
                        'target' => '#newsList',
                        'ajaxUrl' => 'melis/MelisCmsNews/MelisCmsNewsList/renderNewsListData',
                        'dataFunction' => '',
                        'ajaxCallback' => '',
                        'filters' => array(
                            'left' => array(
                                'news-list-news-filter-limit' => array(
                                    'module' => 'MelisCmsNews',
                                    'controller' => 'MelisCmsNewsList',
                                    'action' => 'render-news-list-content-filter-limit'
                                ),
                            ),
                        
                            'center' => array(
                                'news-list-news-filter-search' => array(
                                    'module' => 'MelisCmsNews',
                                    'controller' => 'MelisCmsNewsList',
                                    'action' => 'render-news-list-content-filter-search'
                                ),
                            ),
                        
                            'right' => array(
                                'news-list-news-filter-refresh' => array(
                                    'module' => 'MelisCmsNews',
                                    'controller' => 'MelisCmsNewsList',
                                    'action' => 'render-news-list-content-filter-refresh'
                                ),
                            ),
                        ),
                        
                        'columns' => array(
                            'cnews_id' => array(
                                'text' => 'tr_meliscmsnews_list_col_id',
                                'css' => array('width' => '10%', 'padding-right' => '0'),
                                'sortable' => true,
                            ),
                            'cnews_status' => array(
                                'text' => 'tr_meliscmsnews_list_col_status',
                                'css' => array('width' => '10%', 'padding-right' => '0'),
                                'sortable' => true,
                            ),
                            'cnews_title' => array(
                                'text' => 'tr_meliscmsnews_list_col_title',
                                'css' => array('width' => '25%', 'padding-right' => '0'),
                                'sortable' => true,
                            ),
                            'cnews_creation_date' => array(
                                'text' => 'tr_meliscmsnews_list_col_date',
                                'css' => array('width' => '25%', 'padding-right' => '0'),
                                'sortable' => true,
                            ),
                        ),
                        
                        'searchables' => array(),
                        'actionButtons' => array(
                            'info' => array(
                                'module' => 'MelisCmsNews',
                                'controller' => 'MelisCmsNewsList',
                                'action' => 'render-news-list-content-action-info'
                            ),                            
                            'delete' => array(
                                'module' => 'MelisCmsNews',
                                'controller' => 'MelisCmsNewsList',
                                'action' => 'render-news-list-content-action-delete'
                            ),
                        ),                        
                    ),
                ),
            ),
        ),
    ),
);
