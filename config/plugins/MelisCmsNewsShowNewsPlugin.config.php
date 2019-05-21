<?php

return [
    'plugins' => [
        'meliscmsnews' => [
            'plugins' => [
                'MelisCmsNewsShowNewsPlugin' => [
                    'front' => [
                        'template_path' => ['MelisCmsNews/shownews'],
                        'id' => 'shownews',
                        // News Id
                        'newsId' => null,
                    ],
                    'melis' => [
                        'section' => 'MelisCms',
                        'name' => 'tr_MelisCmsNewsShowNewsPlugin_Name',
                        'thumbnail' => '/MelisCmsNews/plugins/images/MelisCmsNewsShowNewsPlugin_thumb.jpg',
                        'description' => 'tr_MelisCmsNewsShowNewsPlugin_Description',
                        // List the files to be automatically included for the correct display of the plugin
                        // To overide a key, just add it again in your site module
                        // To delete an entry, use the keyword "disable" instead of the file path for the same key
                        'files' => [
                            'css' => [],
                            'js' => [],
                        ],
                        'js_initialization' => [],
                        'modal_form' => [
                            'melis_cms_news_list_plugin_template_form' => [
                                'tab_title' => 'tr_meliscmsnews_plugin_tab_properties',
                                'tab_icon' => 'fa fa-cog',
                                'tab_form_layout' => 'MelisCmsNews/plugin/modal/modal-template-form',
                                'attributes' => [
                                    'name' => 'melis_cms_news_list_plugin_template_form',
                                    'id' => 'melis_cms_news_list_plugin_template_form',
                                    'method' => '',
                                    'action' => '',
                                ],
                                'elements' => [
                                    [
                                        'spec' => [
                                            'name' => 'template_path',
                                            'type' => 'MelisEnginePluginTemplateSelect',
                                            'options' => [
                                                'label' => 'tr_melis_Plugins_Template',
                                                'tooltip' => 'tr_melis_Plugins_Template tooltip',
                                                'empty_option' => 'tr_melis_Plugins_Choose',
                                                'disable_inarray_validator' => true,
                                            ],
                                            'attributes' => [
                                                'id' => 'id_page_tpl_id',
                                                'class' => 'form-control',
                                                'required' => 'required',
                                            ],
                                        ],
                                    ],
                                    [
                                        'spec' => [
                                            'name' => 'newsId',
                                            'type' => 'Select',
                                            'options' => [
                                                'label' => 'tr_meliscmsnews_plugin_select_news_by_default',
                                                'tooltip' => 'tr_meliscmsnews_plugin_select_news_by_default tooltip',
                                                'empty_option' => 'tr_melis_Plugins_Choose',
                                                'disable_inarray_validator' => true,
                                                'open_tool' => [
                                                    'tool_name' => 'tr_meliscmsnews_list_header_title',
                                                    'tooltip' => 'tr_meliscmsnews_list_header_title edit',
                                                    'tool_icon' => 'fa-list-alt',
                                                    'tool_id' => 'id_meliscmsnews_left_menu',
                                                    'tool_meliskey' => 'meliscmsnews_left_menu',
                                                ],
                                            ],
                                            'attributes' => [
                                                'id' => 'news_id',
                                                'class' => 'form-control',
                                            ],
                                        ],
                                    ],
                                ],
                                'input_filter' => [
                                    'template_path' => [
                                        'name' => 'template_path',
                                        'required' => true,
                                        'validators' => [
                                            [
                                                'name' => 'NotEmpty',
                                                'options' => [
                                                    'messages' => [
                                                        \Zend\Validator\NotEmpty::IS_EMPTY => 'tr_front_template_path_empty',
                                                    ],
                                                ],
                                            ],
                                        ],
                                        'filters' => [],
                                    ],
                                    'newsId' => [
                                        'name' => 'newsId',
                                        'required' => false,
                                        'validators' => [
                                            [
                                                'name' => 'Digits',
                                                'options' => [
                                                    'messages' => [
                                                        \Zend\Validator\Digits::NOT_DIGITS => 'tr_front_common_input_not_digit',
                                                        \Zend\Validator\Digits::STRING_EMPTY => 'tr_front_common_input_empty',
                                                    ],
                                                ],
                                            ],
                                        ],
                                        'filters' => [],
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