<?php 

return array(
    'plugins' => array(
        'meliscore' => array(
            'datas' => array(),
            'interface' => array(
                'meliscore_leftmenu' => array(
                    'interface' => array(
        			    'meliscore_toolstree' =>  array(
        			    	'interface' => array(
								'meliscms_tools_section' => array(
                                    'conf' => array(
                                        'id' => 'id_meliscms_tools_section',
                                        'name' => 'tr_meliscms_meliscms',
                                        'icon' => 'fa-newspaper-o',
                                        'rights_checkbox_disable' => true,
                                    ),
        			    			'interface' => array(
		        			    		'meliscmsnews_left' => array(
		        			    			'conf' => array(
		        			    				'type' => '/meliscmsnews/interface/meliscmsnews_list/interface/meliscmsnews_left_menu',
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
		'meliscore_dashboard' => array(),
        'meliscmsnews' => array(
            'conf' => array(
                'id' => '',
                'name' => 'tr_MelisCmsNews_manager',
                'rightsDisplay' => 'none',
                'files' => array(
                    'minUploadSize' => 1,
                    'maxUploadSize' => 10500000,
                    'imagesPath' => '/media/news/',
                ),
                'images_conf' => array(
                    'max' => 3,
                    'name' => 'cnews_image',
                ),
                'documents_conf' => array(
                    'max' => 3,
                    'name' => 'cnews_documents',
                ),
                'paragraphs_conf' => array(
                    'max' => 4,
                    'name' => 'cnews_paragraph',
                ),
            ),
            'ressources' => array(
                'js' => array(
                    '/MelisCmsNews/js/tools/news.tool.js',
                    '/MelisCmsNews/assets/switch/bootstrap-switch.js',
                ),
                
                'css' => array(
                    '/MelisCmsNews/css/news.css',
                ),
            ),
            'datas' => array(
                
            ),
            'interface' => array(
                'meliscmsnews_list' => array(

                    'conf' => array(
                        'name' => 'tr_meliscmsnews_list',
                        'rightsDisplay' => 'referencesonly',
                    ),
                    'interface' => array(
                        'meliscmsnews_left_menu' => array(
                            'conf' => array(
                                'id' => 'id_meliscmsnews_left_menu',
                                'name' => 'tr_meliscmsnews_list_header_title',
                                'melisKey' => 'meliscmsnews_left_menu',
                                'icon' => 'fa-list-alt',
                                'rights_checkbox_disable' => true,
                                'follow_regular_rendering' => false,
                            ),
                            'forward' => array(
                                'module' => 'MelisCmsNews',
                                'controller' => 'MelisCmsNewsList',
                                'action' => 'render-news-list-page',
                                'jscallback' => '',
                                'jsdatas' => array()
                            ),
                            'interface' => array(
                                'meliscmsnews_list_header' => array(
                                    'conf' => array(
                                        'id' => 'id_meliscmsnews_list_header',
                                        'melisKey' => 'meliscmsnews_list_header',
                                        'name' => 'tr_meliscmsnews_list_header',
                                    ),
                                    'forward' => array(
                                        'module' => 'MelisCmsNews',
                                        'controller' => 'MelisCmsNewsList',
                                        'action' => 'render-news-list-header',
                                    ),
                                    'interface' => array(
                                        'meliscmsnews_list_header_left' => array(
                                            'conf' => array(
                                                'id' => 'id_meliscmsnews_list_header_left',
                                                'melisKey' => 'meliscmsnews_list_header_left',
                                                'name' => 'tr_meliscmsnews_list_header_left',
                                            ),
                                            'forward' => array(
                                                'module' => 'MelisCmsNews',
                                                'controller' => 'MelisCmsNewsList',
                                                'action' => 'render-news-list-header-left',
                                            ),
                                            'interface' => array(
                                                'meliscmsnews_list_header_title' => array(
                                                    'conf' => array(
                                                        'id' => 'id_meliscmsnews_list_header_title',
                                                        'melisKey' => 'meliscmsnews_list_header_title',
                                                        'name' => 'tr_meliscmsnews_list_header_title',
                                                    ),
                                                    'forward' => array(
                                                        'module' => 'MelisCmsNews',
                                                        'controller' => 'MelisCmsNewsList',
                                                        'action' => 'render-news-list-header-title',
                                                    ),
                                                ),
                                            ),
                                        ),
                                        'meliscmsnews_list_header_right' => array(
                                            'conf' => array(
                                                'id' => 'id_meliscmsnews_list_header_right',
                                                'melisKey' => 'meliscmsnews_list_header_right',
                                                'name' => 'tr_meliscmsnews_list_header_right',
                                            ),
                                            'forward' => array(
                                                'module' => 'MelisCmsNews',
                                                'controller' => 'MelisCmsNewsList',
                                                'action' => 'render-news-list-header-right',
                                            ),
                                            'interface' => array(
                                                'meliscmsnews_list_header_right_add' => array(
                                                    'conf' => array(
                                                        'id' => 'id_meliscmsnews_list_header_right_add',
                                                        'melisKey' => 'meliscmsnews_list_header_right_add',
                                                        'name' => 'tr_meliscmsnews_list_header_right_add',
                                                    ),
                                                    'forward' => array(
                                                        'module' => 'MelisCmsNews',
                                                        'controller' => 'MelisCmsNewsList',
                                                        'action' => 'render-news-list-header-right-add',
                                                    ),
                                                ),
                                            ),
                                        ),                                        
                                    ),
                                ),
                                'meliscmsnews_list_content' => array(
                                    'conf' => array(
                                        'id' => 'id_meliscmsnews_list_content',
                                        'melisKey' => 'meliscmsnews_list_content',
                                        'name' => 'tr_meliscmsnews_list_content',
                                    ),
                                    'forward' => array(
                                        'module' => 'MelisCmsNews',
                                        'controller' => 'MelisCmsNewsList',
                                        'action' => 'render-news-list-content',
                                    ),
                                    'interface' => array(
                                        'meliscmsnews_list_content_table' => array(
                                            'conf' => array(
                                                'id' => 'id_meliscmsnews_list_content_table',
                                                'melisKey' => 'meliscmsnews_list_content_table',
                                                'name' => 'tr-meliscmsnews_list_content_table',
                                            ),
                                            'forward' => array(
                                                'module' => 'MelisCmsNews',
                                                'controller' => 'MelisCmsNewsList',
                                                'action' => 'render-news-list-content-table',
                                            ),
                                        ),
                                    ),
                                ),
                            ),
                        ),
                    ),                    
                ),
                'meliscmsnews' => array(
                    'interface' => array(
                        'meliscmsnews_page' => array(
                            'conf' => array(
                                'id' => 'id_meliscmsnews_page',
                                'melisKey' => 'meliscmsnews_page',
                                'name' => 'tr_meliscmsnews_list_header_title',
                            ),
                            'forward' => array(
                                'module' => 'MelisCmsNews',
                                'controller' => 'MelisCmsNews',
                                'action' => 'render-news-page',
                            ),
                            'interface' => array(
                                'meliscmsnews_header' => array(
                                    'conf' => array(
                                        'id' => 'id_meliscmsnews_header',
                                        'melisKey' => 'meliscmsnews_header',
                                        'name' => 'tr_meliscmsnews_header',
                                    ),
                                    'forward' => array(
                                        'module' => 'MelisCmsNews',
                                        'controller' => 'MelisCmsNews',
                                        'action' => 'render-news-header',
                                    ),
                                    'interface' => array(
                                        'meliscmsnews_header_left' => array(
                                            'conf' => array(
                                                'id' => 'id_meliscmsnews_header_left',
                                                'melisKey' => 'meliscmsnews_header_left',
                                                'name' => 'tr_meliscmsnews_header_left',
                                            ),
                                            'forward' => array(
                                                'module' => 'MelisCmsNews',
                                                'controller' => 'MelisCmsNews',
                                                'action' => 'render-news-header-left',
                                            ),
                                            'interface' => array(
                                                'meliscmsnews_header_title' => array(
                                                    'conf' => array(
                                                        'id' => 'id_meliscmsnews_header_title',
                                                        'melisKey' => 'meliscmsnews_header_title',
                                                        'name' => 'tr_meliscmsnews_header_title',
                                                    ),
                                                    'forward' => array(
                                                        'module' => 'MelisCmsNews',
                                                        'controller' => 'MelisCmsNews',
                                                        'action' => 'render-news-header-title',
                                                    ),
                                                ),
                                            ),
                                        ),
                                        'meliscmsnews_header_right' => array(
                                            'conf' => array(
                                                'id' => 'id_meliscmsnews_header_right',
                                                'melisKey' => 'meliscmsnews_header_right',
                                                'name' => 'tr_meliscmsnews_header_right',
                                            ),
                                            'forward' => array(
                                                'module' => 'MelisCmsNews',
                                                'controller' => 'MelisCmsNews',
                                                'action' => 'render-news-header-right',
                                            ),
                                            'interface' => array(
                                                'meliscmsnews_header_right_save' => array(
                                                    'conf' => array(
                                                        'id' => 'id_meliscmsnews_header_right_save',
                                                        'melisKey' => 'meliscmsnews_header_right_save',
                                                        'name' => 'tr_meliscmsnews_header_right_save',
                                                    ),
                                                    'forward' => array(
                                                        'module' => 'MelisCmsNews',
                                                        'controller' => 'MelisCmsNews',
                                                        'action' => 'render-news-header-right-save',
                                                    ),
                                                ),
                                            ),
                                        ),
                                    ),
                                ),
                                'meliscmsnews_content' => array(
                                    'conf' => array(
                                        'id' => 'id_meliscmsnews_content',
                                        'melisKey' => 'meliscmsnews_content',
                                        'name' => 'tr_meliscmsnews_content',
                                    ),
                                    'forward' => array(
                                        'module' => 'MelisCmsNews',
                                        'controller' => 'MelisCmsNews',
                                        'action' => 'render-news-page-content',
                                    ),
                                    'interface' => array(
                                        'meliscmsnews_content_tabs_properties' => array(
                                            'conf' => array(
                                                'id' => 'id_meliscmsnews_content_tabs_properties',
                                                'melisKey' => 'meliscmsnews_content_tabs_properties',
                                                'name' => 'tr_meliscmsnews_content_tabs_properties',
                                                'icon' => 'glyphicons tag',
                                            ),
                                            'forward' => array(
                                                'module' => 'MelisCmsNews',
                                                'controller' => 'MelisCmsNews',
                                                'action' => 'render-news-page-tabs-main',
                                            ),
                                            'interface' => array(
                                                'meliscmsnews_content_tabs_properties_header' => array(
                                                    'conf' => array(
                                                        'id' => 'id_meliscmsnews_content_tabs_properties_header',
                                                        'melisKey' => 'meliscmsnews_content_tabs_properties_header',
                                                        'name' => 'tr_meliscmsnews_content_tabs_properties_header',
                                                    ),
                                                    'forward' => array(
                                                        'module' => 'MelisCmsNews',
                                                        'controller' => 'MelisCmsNews',
                                                        'action' => 'render-news-tabs-content-header',
                                                    ),
                                                    'interface' => array(
                                                        'meliscmsnews_content_tabs_properties_header_left' => array(
                                                            'conf' => array(
                                                                'id' => 'id_meliscmsnews_content_tabs_properties_header_left',
                                                                'melisKey' => 'meliscmsnews_content_tabs_properties_header_left',
                                                                'name' => 'tr_meliscmsnews_content_tabs_properties_header_left',
                                                            ),
                                                            'forward' => array(
                                                                'module' => 'MelisCmsNews',
                                                                'controller' => 'MelisCmsNews',
                                                                'action' => 'render-news-tabs-content-header-left',
                                                            ),
                                                            'interface' => array(
                                                                'meliscmsnews_content_tabs_properties_header_title' => array(
                                                                    'conf' => array(
                                                                        'id' => 'id_meliscmsnews_content_tabs_properties_header_title',
                                                                        'melisKey' => 'meliscmsnews_content_tabs_properties_header_title',
                                                                        'name' => 'tr_meliscmsnews_content_tabs_properties',
                                                                    ),
                                                                    'forward' => array(
                                                                        'module' => 'MelisCmsNews',
                                                                        'controller' => 'MelisCmsNews',
                                                                        'action' => 'render-news-tabs-content-header-title',
                                                                    ),
                                                                ),
                                                            ),
                                                        ),
                                                        'meliscmsnews_content_tabs_properties_header_right' => array(
                                                            'conf' => array(
                                                                'id' => 'id_meliscmsnews_content_tabs_properties_header_right',
                                                                'melisKey' => 'meliscmsnews_content_tabs_properties_header_right',
                                                                'name' => 'tr_meliscmsnews_content_tabs_properties_header_right',
                                                            ),
                                                            'forward' => array(
                                                                'module' => 'MelisCmsNews',
                                                                'controller' => 'MelisCmsNews',
                                                                'action' => 'render-news-tabs-content-header-left',
                                                            ),
                                                            'interface' => array(
                                                                'meliscmsnews_content_tabs_properties_header_status' => array(
                                                                    'conf' => array(
                                                                        'id' => 'id_meliscmsnews_content_tabs_properties_header_status',
                                                                        'melisKey' => 'meliscmsnews_content_tabs_properties_header_status',
                                                                        'name' => 'tr_meliscmsnews_content_tabs_properties_header_status',
                                                                    ),
                                                                    'forward' => array(
                                                                        'module' => 'MelisCmsNews',
                                                                        'controller' => 'MelisCmsNews',
                                                                        'action' => 'render-news-tabs-content-header-status',
                                                                    ),
                                                                    'interface' => array(
                                                                    ),
                                                                ),
                                                            ),
                                                        ),
                                                    ),
                                                ),
                                                'meliscmsnews_content_tabs_properties_details' => array(
                                                    'conf' => array(
                                                        'id' => 'id_meliscmsnews_content_tabs_properties_details',
                                                        'melisKey' => 'meliscmsnews_content_tabs_properties_details',
                                                        'name' => 'tr-meliscmsnews_content_tabs_properties_details',
                                                    ),
                                                    'forward' => array(
                                                        'module' => 'MelisCmsNews',
                                                        'controller' => 'MelisCmsNews',
                                                        'action' => 'render-news-tabs-content-details',
                                                    ),
                                                    'interface' => array(
                                                        'meliscmsnews_content_tabs_properties_details_main' => array(
                                                            'conf' => array(
                                                                'id' => 'id_meliscmsnews_content_tabs_properties_details_main',
                                                                'melisKey' => 'meliscmsnews_content_tabs_properties_details_main',
                                                                'name' => 'tr_meliscmsnews_content_tabs_properties_details_main',
                                                            ),
                                                            'forward' => array(
                                                                'module' => 'MelisCmsNews',
                                                                'controller' => 'MelisCmsNews',
                                                                'action' => 'render-news-tabs-properties-details-main',
                                                            ),
                                                            'interface' => array(
                                                                'meliscmsnews_content_tabs_properties_details_left' => array(
                                                                    'conf' => array(
                                                                        'id' => 'id_meliscmsnews_content_tabs_properties_details_left',
                                                                        'melisKey' => 'meliscmsnews_content_tabs_properties_details_left',
                                                                        'name' => 'tr_meliscmsnews_content_tabs_properties_details_left',
                                                                    ),
                                                                    'forward' => array(
                                                                        'module' => 'MelisCmsNews',
                                                                        'controller' => 'MelisCmsNews',
                                                                        'action' => 'render-news-tabs-properties-details-left',
                                                                    ),
                                                                    'interface' => array(
                                                                        'meliscmsnews_content_tabs_properties_details_left_properties' => array(
                                                                            'conf' => array(
                                                                                'id' => 'id_meliscmsnews_content_tabs_properties_details_left_properties',
                                                                                'melisKey' => 'meliscmsnews_content_tabs_properties_details_left_properties',
                                                                                'name' => 'tr_meliscmsnews_content_tabs_properties_details_left_properties',
                                                                            ),
                                                                            'forward' => array(
                                                                                'module' => 'MelisCmsNews',
                                                                                'controller' => 'MelisCmsNews',
                                                                                'action' => 'render-news-tabs-properties-details-left-properties',
                                                                            ),
                                                                        ),                                                                        
                                                                    ),
                                                                ),
                                                                'meliscmsnews_content_tabs_properties_details_right' => array(
                                                                    'conf' => array(
                                                                        'id' => 'id_meliscmsnews_content_tabs_properties_details_right',
                                                                        'melisKey' => 'meliscmsnews_content_tabs_properties_details_right',
                                                                        'name' => 'tr_meliscmsnews_content_tabs_properties_details_right',
                                                                    ),
                                                                    'forward' => array(
                                                                        'module' => 'MelisCmsNews',
                                                                        'controller' => 'MelisCmsNews',
                                                                        'action' => 'render-news-tabs-properties-details-right',
                                                                    ),
                                                                    'interface' => array(
                                                                        'meliscmsnews_content_tabs_properties_details_left_sites' => array(
                                                                            'conf' => array(
                                                                                'id' => 'id_meliscmsnews_content_tabs_properties_details_left_sites',
                                                                                'melisKey' => 'meliscmsnews_content_tabs_properties_details_left_sites',
                                                                                'name' => 'tr_meliscmsnews_content_tabs_properties_details_left_sites',
                                                                            ),
                                                                            'forward' => array(
                                                                                'module' => 'MelisCmsNews',
                                                                                'controller' => 'MelisCmsNews',
                                                                                'action' => 'render-news-tabs-properties-details-left-sites',
                                                                            ),
                                                                        ),
                                                                        'meliscmsnews_content_tabs_properties_details_left_sliders' => array(
                                                                            'conf' => array(
                                                                                'type' => 'MelisCmsSlider/interface/meliscmsslider_select_slider',
                                                                            )
                                                                        ),
                                                                    ),
                                                                ),
                                                            ),
                                                        ),
                                                    ),
                                                ),
                                            ),
                                        ),
                                        'meliscmsnews_content_tabs_medias' => array(
                                            'conf' => array(
                                                'id' => 'id_meliscmsnews_content_tabs_medias',
                                                'melisKey' => 'meliscmsnews_content_tabs_medias',
                                                'name' => 'tr_meliscmsnews_content_tabs_medias',
                                                'icon' => 'glyphicons picture',
                                            ),
                                            'forward' => array(
                                                'module' => 'MelisCmsNews',
                                                'controller' => 'MelisCmsNews',
                                                'action' => 'render-news-page-tabs-main',
                                            ),
                                            'interface' => array(
                                                'meliscmsnews_content_tabs_medias_header' => array(
                                                    'conf' => array(
                                                        'id' => 'id_meliscmsnews_content_tabs_medias_header',
                                                        'melisKey' => 'meliscmsnews_content_tabs_medias_header',
                                                    ),
                                                    'forward' => array(
                                                        'module' => 'MelisCmsNews',
                                                        'controller' => 'MelisCmsNews',
                                                        'action' => 'render-news-tabs-content-header',
                                                    ),
                                                    'interface' => array(
                                                        'meliscmsnews_content_tabs_medias_header_left' => array(
                                                            'conf' => array(
                                                                'id' => 'id_meliscmsnews_content_tabs_medias_header_left',
                                                                'melisKey' => 'meliscmsnews_content_tabs_medias_header_left',
                                                            ),
                                                            'forward' => array(
                                                                'module' => 'MelisCmsNews',
                                                                'controller' => 'MelisCmsNews',
                                                                'action' => 'render-news-tabs-content-header-left',
                                                            ),
                                                            'interface' => array(
                                                                'meliscmsnews_content_tabs_medias_header_title' => array(
                                                                    'conf' => array(
                                                                        'id' => 'id_meliscmsnews_content_tabs_medias_header_title',
                                                                        'melisKey' => 'meliscmsnews_content_tabs_medias_header_title',
                                                                        'name' => 'tr_meliscmsnews_content_tabs_medias',
                                                                    ),
                                                                    'forward' => array(
                                                                        'module' => 'MelisCmsNews',
                                                                        'controller' => 'MelisCmsNews',
                                                                        'action' => 'render-news-tabs-content-header-title',
                                                                    ),
                                                                ),
                                                            ),
                                                        ),
                                                        'meliscmsnews_content_tabs_medias_header_right' => array(
                                                            'conf' => array(
                                                                'id' => 'id_meliscmsnews_content_tabs_medias_header_right',
                                                                'melisKey' => 'meliscmsnews_content_tabs_medias_header_right',
                                                            ),
                                                            'forward' => array(
                                                                'module' => 'MelisCmsNews',
                                                                'controller' => 'MelisCmsNews',
                                                                'action' => 'render-news-tabs-content-header-left',
                                                            ),
                                                            'interface' => array(
                                                                'meliscmsnews_content_tabs_medias_details' => array(
                                                                    'conf' => array(
                                                                        'id' => 'id_meliscmsnews_content_tabs_medias_details',
                                                                        'melisKey' => 'meliscmsnews_content_tabs_medias_details',
                                                                    ),
                                                                    'forward' => array(
                                                                        'module' => 'MelisCmsNews',
                                                                        'controller' => 'MelisCmsNews',
                                                                        'action' => 'render-news-tabs-content-details',
                                                                    ),
                                                                    'interface' => array(
                                                                        
                                                                    ),
                                                                ),
                                                            ),
                                                        ),
                                                    ),
                                                ),
                                                'meliscmsnews_content_tabs_medias_details_main' => array(
                                                    'conf' => array(
                                                        'id' => 'id_meliscmsnews_content_tabs_medias_details_main',
                                                        'melisKey' => 'meliscmsnews_content_tabs_medias_details_main',
                                                    ),
                                                    'forward' => array(
                                                        'module' => 'MelisCmsNews',
                                                        'controller' => 'MelisCmsNews',
                                                        'action' => 'render-news-tabs-properties-details-main',
                                                    ),
                                                    'interface' => array(
                                                        'meliscmsnews_content_tabs_medias_details_left' => array(
                                                            'conf' => array(
                                                                'id' => 'id_meliscmsnews_content_tabs_medias_details_left',
                                                                'melisKey' => 'meliscmsnews_content_tabs_medias_details_left',
                                                            ),
                                                            'forward' => array(
                                                                'module' => 'MelisCmsNews',
                                                                'controller' => 'MelisCmsNews',
                                                                'action' => 'render-news-tabs-properties-details-left',
                                                            ),
                                                            'interface' => array(
                                                                'meliscmsnews_content_tabs_properties_details_left_images' => array(
                                                                    'conf' => array(
                                                                        'id' => 'id_meliscmsnews_content_tabs_properties_details_left_images',
                                                                        'melisKey' => 'meliscmsnews_content_tabs_properties_details_left_images',
                                                                        'name' => 'tr_meliscmsnews_content_tabs_properties_details_left_images',
                                                                    ),
                                                                    'forward' => array(
                                                                        'module' => 'MelisCmsNews',
                                                                        'controller' => 'MelisCmsNews',
                                                                        'action' => 'render-news-tabs-properties-details-left-images',
                                                                    ),
                                                                ),
                                                            ),
                                                        ),
                                                        'meliscmsnews_content_tabs_medias_details_right' => array(
                                                            'conf' => array(
                                                                'id' => 'id_meliscmsnews_content_tabs_medias_details_right',
                                                                'melisKey' => 'meliscmsnews_content_tabs_medias_details_right',
                                                            ),
                                                            'forward' => array(
                                                                'module' => 'MelisCmsNews',
                                                                'controller' => 'MelisCmsNews',
                                                                'action' => 'render-news-tabs-properties-details-right',
                                                            ),
                                                            'interface' => array(
                                                                'meliscmsnews_content_tabs_properties_details_left_documents' => array(
                                                                    'conf' => array(
                                                                        'id' => 'id_meliscmsnews_content_tabs_properties_details_left_documents',
                                                                        'melisKey' => 'meliscmsnews_content_tabs_properties_details_left_documents',
                                                                        'name' => 'tr_meliscmsnews_content_tabs_properties_details_left_documents',
                                                                    ),
                                                                    'forward' => array(
                                                                        'module' => 'MelisCmsNews',
                                                                        'controller' => 'MelisCmsNews',
                                                                        'action' => 'render-news-tabs-properties-details-left-documents',
                                                                    ),
                                                                ),
                                                            ),
                                                        ),
                                                    ),
                                                ),
                                            ),
                                        ),
                                        'meliscmsnews_content_tabs_texts' => array(
                                            'conf' => array(
                                                'id' => 'id_meliscmsnews_content_tabs_texts',
                                                'melisKey' => 'meliscmsnews_content_tabs_texts',
                                                'name' => 'tr_meliscmsnews_content_tabs_texts',
                                                'icon' => 'glyphicons pencil',
                                            ),
                                            'forward' => array(
                                                'module' => 'MelisCmsNews',
                                                'controller' => 'MelisCmsNews',
                                                'action' => 'render-news-page-tabs-main',
                                            ),
                                            'interface' => array(
                                                'meliscmsnews_content_tabs_texts_header' => array(
                                                    'conf' => array(
                                                        'id' => 'id_meliscmsnews_content_tabs_texts_header',
                                                        'melisKey' => 'meliscmsnews_content_tabs_texts_header',
                                                    ),
                                                    'forward' => array(
                                                        'module' => 'MelisCmsNews',
                                                        'controller' => 'MelisCmsNews',
                                                        'action' => 'render-news-tabs-content-header',
                                                    ),
                                                    'interface' => array(
                                                        'meliscmsnews_content_tabs_texts_header_left' => array(
                                                            'conf' => array(
                                                                'id' => 'id_meliscmsnews_content_tabs_texts_header_left',
                                                                'melisKey' => 'meliscmsnews_content_tabs_texts_header_left',
                                                            ),
                                                            'forward' => array(
                                                                'module' => 'MelisCmsNews',
                                                                'controller' => 'MelisCmsNews',
                                                                'action' => 'render-news-tabs-content-header-left',
                                                            ),
                                                            'interface' => array(
                                                                'meliscmsnews_content_tabs_texts_header_title' => array(
                                                                    'conf' => array(
                                                                        'id' => 'id_meliscmsnews_content_tabs_texts_header_title',
                                                                        'melisKey' => 'meliscmsnews_content_tabs_texts_header_title',
                                                                        'name' => 'tr_meliscmsnews_content_tabs_texts',
                                                                    ),
                                                                    'forward' => array(
                                                                        'module' => 'MelisCmsNews',
                                                                        'controller' => 'MelisCmsNews',
                                                                        'action' => 'render-news-tabs-content-header-title',
                                                                    ),
                                                                ),
                                                            ),
                                                        ),
                                                        'meliscmsnews_content_tabs_texts_header_right' => array(
                                                            'conf' => array(
                                                                'id' => 'id_meliscmsnews_content_tabs_texts_header_right',
                                                                'melisKey' => 'meliscmsnews_content_tabs_texts_header_right',
                                                            ),
                                                            'forward' => array(
                                                                'module' => 'MelisCmsNews',
                                                                'controller' => 'MelisCmsNews',
                                                                'action' => 'render-news-tabs-content-header-left',
                                                            ),
                                                            'interface' => array(
                                                            ),
                                                        ),
                                                    ),
                                                ),
                                                'meliscmsnews_content_tabs_medias_details_main' => array(
                                                    'conf' => array(
                                                        'id' => 'id_meliscmsnews_content_tabs_medias_details_main',
                                                        'melisKey' => 'meliscmsnews_content_tabs_medias_details_main',
                                                    ),
                                                    'forward' => array(
                                                        'module' => 'MelisCmsNews',
                                                        'controller' => 'MelisCmsNews',
                                                        'action' => 'render-news-tabs-properties-details-main',
                                                    ),
                                                    'interface' => array(
                                                        'meliscmsnews_content_tabs_properties_details_right_paragraphs' => array(
                                                            'conf' => array(
                                                                'id' => 'id_meliscmsnews_content_tabs_properties_details_right_paragraphs',
                                                                'melisKey' => 'meliscmsnews_content_tabs_properties_details_right_paragraphs',
                                                                'name' => 'tr_meliscmsnews_content_tabs_properties_details_right_paragraphs',
                                                            ),
                                                            'forward' => array(
                                                                'module' => 'MelisCmsNews',
                                                                'controller' => 'MelisCmsNews',
                                                                'action' => 'render-news-tabs-properties-details-right-paragraphs',
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
                'meliscmsnews_modal' => array(
                    'conf' => array(
                        'id' => 'id_meliscmsnews_modal',
                        'name' => 'tr_meliscmsnews_modal',
                        'melisKey' => 'meliscmsnews_modal',
                    ),
                    'forward' => array(
                        'module' => 'MelisCmsNews',
                        'controller' => 'MelisCmsNews',
                        'action' => 'render-modal',
                    ),
                    'interface' => array(
                        'meliscmsnews_modal_documents_form' => array(
                            'conf' => array(
                                'id' => 'id_meliscmsnews_modal_documents_form',
                                'name' => 'tr_meliscmsnews_add_file',
                                'melisKey' => 'meliscmsnews_modal_documents_form',
                            ),
                            'forward' => array(
                                'module' => 'MelisCmsNews',
                                'controller' => 'MelisCmsNews',
                                'action' => 'render-modal-form',
                            ),
                        ),
                        'meliscmsnews_modal_documents_form_image' => array(
                            'conf' => array(
                                'id' => 'id_meliscmsnews_modal_documents_form_image',
                                'name' => 'tr_meliscmsnews_modal_documents_form_image',
                                'melisKey' => 'meliscmsnews_modal_documents_form_image',
                            ),
                            'forward' => array(
                                'module' => 'MelisCmsNews',
                                'controller' => 'MelisCmsNews',
                                'action' => 'render-modal-form',
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
);