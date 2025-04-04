<?php

return array(
    'plugins' => array(
        'meliscmsnews' => array(
            'plugins' => array(
                'MelisCmsNewsLatestNewsPlugin' => array(
                    'front' => array(
                        'template_path' => array('MelisCmsNews/latestnews'),
                        'id' => 'latestnews',
                        
                        // Site id of News
                        'site_id' => null,
                        
                        // Detail news landing page
                        'pageIdNews' => null, 
                        
                        // optional, filtering
                        'filter' => array(
                            'column' => 'cnews_publish_date',
                            'order' => null,
                            'date_min' => null,
                            'date_max' => null,
                            'limit' => 5,
                            'search' => '',
                        ),
                        
                        // List the files to be automatically included for the correct display of the plugin
                        // To overide a key, just add it again in your site module
                        // To delete an entry, use the keyword "disable" instead of the file path for the same key
                        'files' => array(
                            'css' => array( 
                            ),
                            'js' => array( 
                            ),
                        ),
                    ),
                    'melis' => array(
                        // this is for sectioning the plugins
                        'section' => 'MelisCms',
                        'name' => 'tr_MelisCmsNewsLatestNewsPlugin_Name',
                        'thumbnail' => '/MelisCmsNews/plugins/images/MelisCmsNewsLatestNewsPlugin_thumb.jpg',
                        'description' => 'tr_MelisCmsNewsLatestNewsPlugin_Description',
                        'files' => array(
                            'css' => array(
                            ),
                            'js' => array(
                            ),
                        ),
                        'js_initialization' => array(),
                        'modal_form' => array(
                            'melis_cms_news_list_plugin_template_form' => array(
                                'tab_title' => 'tr_meliscmsnews_plugin_tab_properties',
                                'tab_icon'  => 'fa fa-cog',
                                'tab_form_layout' => 'MelisCmsNews/plugin/modal/modal-template-form',
                                'attributes' => array(
                                    'name' => 'melis_cms_news_list_plugin_template_form',
                                    'id' => 'melis_cms_news_list_plugin_template_form',
                                    'method' => '',
                                    'action' => '',
                                ),
                                'elements' => array(
                                    array(
                                        'spec' => array(
                                            'name' => 'template_path',
                                            'type' => 'MelisEnginePluginTemplateSelect',
                                            'options' => array(
                                                'label' => 'tr_melis_Plugins_Template',
                                                'tooltip' => 'tr_melis_Plugins_Template tooltip',
                                                'empty_option' => 'tr_melis_Plugins_Choose',
                                                'disable_inarray_validator' => true,
                                            ),
                                            'attributes' => array(
                                                'id' => 'id_page_tpl_id',
                                                'class' => 'form-control',
                                                'required' => 'required',
                                            ),
                                        ),
                                    ),
                                    array(
                                        'spec' => array(
                                            'name' => 'site_id',
                                            'type' => 'MelisCmsPluginSiteSelect',
                                            'options' => array(
                                                'label' => 'tr_meliscmsnews_plugin_filter_site',
                                                'tooltip' => 'tr_meliscmsnews_plugin_filter_site tooltip',
                                                'empty_option' => 'tr_melis_Plugins_Choose',
                                                'disable_inarray_validator' => true,
                                            ),
                                            'attributes' => array(
                                                'id' => 'site_id',
                                                'class' => 'form-control',
                                                'required' => 'required',
                                            ),
                                        ),
                                    ),
                                    array(
                                        'spec' => array(
                                            'name' => 'pageIdNews',
                                            'type' => 'MelisText',
                                            'options' => array(
                                                'label' => 'tr_meliscmsnews_plugin_detail_news_page_id',
                                                'tooltip' => 'tr_meliscmsnews_plugin_detail_news_page_id tooltip',
                                            ),
                                            'attributes' => array(
                                                'id' => 'pageIdNews',
                                                'class' => 'melis-input-group-button',
                                                'data-button-icon' => 'fa fa-sitemap',
                                                'data-button-id' => 'meliscms-site-selector',
                                                'placeholder' => 'tr_meliscmsnews_plugin_detail_news_page_id',
                                                'required' => 'required',
                                            ),
                                        ),
                                    ),
                                ),
                                'input_filter' => array(
                                    'template_path' => array(
                                        'name'     => 'template_path',
                                        'required' => true,
                                        'validators' => array(
                                            array(
                                                'name' => 'NotEmpty',
                                                'options' => array(
                                                    'messages' => array(
                                                        \Laminas\Validator\NotEmpty::IS_EMPTY => 'tr_front_template_path_empty',
                                                    ),
                                                ),
                                            ),
                                        ),
                                        'filters'  => array(
                                        ),
                                    ),
                                    'pageIdNews' => array(
                                        'name'     => 'pageIdNews',
                                        'required' => true,
                                        'validators' => array(
                                            array(
                                                'name'    => 'Digits',
                                                'options' => array(
                                                    'messages' => array(
                                                        \Laminas\Validator\Digits::NOT_DIGITS => 'tr_front_common_input_not_digit',
                                                        \Laminas\Validator\Digits::STRING_EMPTY => 'tr_front_common_input_empty',
                                                    ),
                                                ),
                                            ),
                                        ),
                                        'filters'  => array(
                                        ),
                                    ),
                                )
                            ),
                            'melis_cms_news_list_plugin_filter_form' => array(
                                'tab_title' => 'tr_meliscmsnews_plugin_tab_filters',
                                'tab_icon'  => 'fa fa-filter',
                                'tab_form_layout' => 'MelisCmsNews/plugin/modal/modal-filter-form',
                                'attributes' => array(
                                    'name' => 'melis_cms_news_list_plugin_filter_form',
                                    'id' => 'melis_cms_news_list_plugin_filter_form',
                                    'method' => '',
                                    'action' => '',
                                ),
                                'elements' => array(
                                    array(
                                        'spec' => array(
                                            'name' => 'column',
                                            'type' => 'Select',
                                            'options' => array(
                                                'label' => 'tr_meliscmsnews_plugin_filter_by',
                                                'tooltip' => 'tr_meliscmsnews_plugin_filter_by tooltip',
                                                'empty_option' => 'tr_melis_Plugins_Choose',
                                                'disable_inarray_validator' => true,
                                                'value_options' => array(
                                                    'cnews_id'  => 'tr_meliscmsnews_plugin_filter_order_id',
                                                    'cnews_title'  => 'tr_meliscmsnews_plugin_filter_order_column_title',
                                                    'cnews_publish_date' => 'tr_meliscmsnews_plugin_filter_order_column_publish_date',
                                                    'cnews_creation_date'  => 'tr_meliscmsnews_plugin_filter_order_creation_date',
                                                ),
                                            ),
                                            'attributes' => array(
                                                'id' => 'column',
                                                'class' => 'form-control',
                                                'placeholder' => 'tr_meliscmsnews_plugin_filter_col_name',
                                                'required' => 'required',
                                            ),
                                        ),
                                    ),
                                    array(
                                        'spec' => array(
                                            'name' => 'order',
                                            'type' => 'Select',
                                            'options' => array(
                                                'label' => 'tr_meliscmsnews_plugin_filter_order',
                                                'tooltip' => 'tr_meliscmsnews_plugin_filter_order tooltip',
                                                'empty_option' => 'tr_melis_Plugins_Choose',
                                                'disable_inarray_validator' => true,
                                                'value_options' => array(
                                                    'DESC' => 'tr_meliscmsnews_plugin_filter_desc',
                                                    'ASC'  => 'tr_meliscmsnews_plugin_filter_asc'
                                                ),
                                            ),
                                            'attributes' => array(
                                                'id' => 'order',
                                                'class' => 'form-control',
                                            ),
                                        ),
                                    ),
                                    array(
                                        'spec' => array(
                                            'name' => 'limit',
                                            'type' => 'MelisText',
                                            'options' => array(
                                                'label' => 'tr_meliscmsnews_plugin_filter_limit',
                                                'tooltip' => 'tr_meliscmsnews_plugin_filter_limit tooltip',
                                            ),
                                            'attributes' => array(
                                                'id' => 'limit',
                                                'class' => 'form-control',
                                                'placeholder' => 'tr_meliscmsnews_plugin_filter_limit',
                                                'required' => 'required',
                                            ),
                                        ),
                                    ),
                                    array(
                                        'spec' => array(
                                            'name' => 'date_min',
                                            'type' => 'MelisText',
                                            'options' => array(
                                                'label' => 'tr_meliscmsnews_plugin_filter_date_range_from',
                                                'tooltip' => 'tr_meliscmsnews_plugin_filter_date_range_from tooltip',
                                            ),
                                            'attributes' => array(
                                                'id' => 'date_min',
                                                'class' => 'melis-input-group-button',
                                                'data-button-icon' => 'fa fa-eraser',
                                                'data-button-class' => 'meliscore-clear-input',
                                                'data-button-title' => 'tr_meliscore_common_clear',
                                                'placeholder' => 'tr_meliscmsnews_plugin_filter_date_range_from',
                                                'readonly' => 'readonly'
                                            ),
                                        ),
                                    ),
                                    array(
                                        'spec' => array(
                                            'name' => 'date_max',
                                            'type' => 'MelisText',
                                            'options' => array(
                                                'label' => 'tr_meliscmsnews_plugin_filter_date_range_to',
                                                'tooltip' => 'tr_meliscmsnews_plugin_filter_date_range_to tooltip',
                                            ),
                                            'attributes' => array(
                                                'id' => 'date_min',
                                                'class' => 'melis-input-group-button',
                                                'data-button-icon' => 'fa fa-eraser',
                                                'data-button-class' => 'meliscore-clear-input',
                                                'data-button-title' => 'tr_meliscore_common_clear',
                                                'placeholder' => 'tr_meliscmsnews_plugin_filter_date_range_to',
                                                'readonly' => 'readonly'
                                            ),
                                        ),
                                    ),
                                    array(
                                        'spec' => array(
                                            'name' => 'search',
                                            'type' => 'MelisText',
                                            'options' => array(
                                                'label' => 'tr_meliscmsnews_plugin_filter_search_by_default',
                                                'tooltip' => 'tr_meliscmsnews_plugin_filter_search_by_default tooltip',
                                            ),
                                            'attributes' => array(
                                                'id' => 'search',
                                                'class' => 'form-control',
                                                'placeholder' => 'tr_meliscmsnews_plugin_filter_search',
                                            ),
                                        ),
                                    ),
                                ),
                                'input_filter' => array(
                                    'column' => array(
                                        'name'     => 'column',
                                        'required' => true,
                                        'validators' => array(
                                            array(
                                                'name' => 'NotEmpty',
                                                'options' => array(
                                                    'messages' => array(
                                                        \Laminas\Validator\NotEmpty::IS_EMPTY => 'tr_front_common_input_empty',
                                                    ),
                                                ),
                                            ),
                                        ),
                                        'filters'  => array(
                                        ),
                                    ),
                                    'order' => array(
                                        'name'     => 'order',
                                        'required' => false,
                                        'filters'  => array(
                                        ),
                                    ),
                                    'limit' => array(
                                        'name'     => 'limit',
                                        'required' => true,
                                        'validators' => array(
                                            array(
                                                'name'    => 'Digits',
                                                'options' => array(
                                                    'messages' => array(
                                                        \Laminas\Validator\Digits::NOT_DIGITS => 'tr_front_common_input_not_digit',
                                                        \Laminas\Validator\Digits::STRING_EMPTY => 'tr_front_common_input_empty',
                                                    ),
                                                ),
                                            ),
                                        ),
                                        'filters'  => array(
                                        ),
                                    ),
                                )
                            )
                        )
                    ),
                ),
             ),
        ),
     ),
);