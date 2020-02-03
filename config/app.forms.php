<?php
return [
    'plugins' => [
        'MelisCmsNews' => [
            'conf' => [
                // user rights exclusions
                'rightsDisplay' => 'none',
            ],
            'forms' => [
                'meliscmsnews_properties_form' => [
                    'attributes' => [
                        'name' => 'newsLetterForm',
                        'id' => 'newsLetterForm',
                        'method' => 'POST',
                        'action' => '',
                    ],
                    'hydrator' => 'Zend\Stdlib\Hydrator\ArraySerializable',
                    'elements' => [
                        [
                            'spec' => [
                                'name' => 'cnews_publish_date',
                                'type' => 'DateField',
                                'options' => [
                                    'label' => 'tr_meliscmsnews_form_publish',
                                    'tooltip' => 'tr_meliscmsnews_form_publish tooltip',
                                    'class' => 'd-flex flex-row justify-content-between',
                                ],
                                'attributes' => [
                                    'dateId' => 'newsPublishDate',
                                ],
                            ],
                        ],
                        [
                            'spec' => [
                                'name' => 'cnews_unpublish_date',
                                'type' => 'DateField',
                                'options' => [
                                    'label' => 'tr_meliscmsnews_form_unpublish',
                                    'tooltip' => 'tr_meliscmsnews_form_unpublish tooltip',
                                    'class' => 'd-flex flex-row justify-content-between',
                                ],
                                'attributes' => [
                                    'dateId' => 'newsUnpublishDate',
                                ],
                            ],
                        ],
                        [
                            'spec' => [
                                'name' => 'cnews_id',
                                'type' => 'hidden',
                            ],
                        ],
                        [
                            'spec' => [
                                'name' => 'cnews_creation_date',
                                'type' => 'hidden',
                            ],
                        ],
                    ],
                    'input_filter' => [
                    ],
                ],
                'meliscmsnews_file_form' => [
                    'attributes' => [
                        'name' => 'newsFileForm',
                        'id' => 'newsFileForm',
                        'enctype' => "multipart/form-data",
                        'method' => 'POST',
                        'action' => '',
                    ],
                    'hydrator' => 'Zend\Stdlib\Hydrator\ArraySerializable',
                    'elements' => [
                        [
                            'spec' => [
                                'name' => 'cnews_document',
                                'type' => 'file',
                                'options' => [
                                    'label' => 'tr_meliscmsnews_save_upload_file',
                                ],
                                'attributes' => [
                                    'id' => 'cnews_document',
                                    'accept' => ".gif,.jpg,.jpeg,.png",
                                    'value' => '',
                                    'class' => 'filestyle',
                                    'label' => 'Upload',
                                    'required' => 'required',
                                    'onchange' => 'newsImagePreview(".imgDocThumbnail", this);',
                                ],
                            ],
                        ],
                        [
                            'spec' => [
                                'name' => 'type',
                                'type' => 'hidden',
                            ],
                        ],
                        [
                            'spec' => [
                                'name' => 'cnews_id',
                                'type' => 'hidden',
                            ],
                        ],
                        [
                            'spec' => [
                                'name' => 'column',
                                'type' => 'hidden',
                            ],
                        ],
                    ],
                    'input_filter' => [
                    ],
                ],
                'meliscmsnews_site_select_form' => [
                    'attributes' => [
                        'name' => 'newsSiteSelectForm',
                        'id' => 'newsSiteSelectForm',
                        'method' => 'POST',
                        'action' => '',
                    ],
                    'hydrator' => 'Zend\Stdlib\Hydrator\ArraySerializable',
                    'elements' => [
                        [
                            'spec' => [
                                'name' => 'cnews_site_id',
                                'type' => 'MelisCoreSiteSelect',
                                'options' => [
                                    'label' => 'tr_meliscms_tool_news_site_id',
                                    'tooltip' => 'tr_meliscmsnews_tpl_site_id tooltip',
                                    'empty_option' => 'tr_meliscmsliderdetails_common_label_choose',
                                    'disable_inarray_validator' => true,
                                ],
                                'attributes' => [
                                    'id' => 'id_cnews_site_id',
                                    'value' => '',
                                ],
                            ],
                        ],
                    ],
                    'input_filter' => [
                        'cnews_site_id' => [
                            'name' => 'cnews_site_id',
                            'required' => true,
                            'validators' => [
                                [
                                    'name' => 'NotEmpty',
                                    'options' => [
                                        'messages' => [
                                            \Zend\Validator\NotEmpty::IS_EMPTY => 'tr_meliscmsnews_empty_site',
                                        ],
                                    ],
                                ],
                            ],
                            'filters' => [],
                        ],
                    ],
                ],
                'meliscmsnews_site_title_subtitle_form' => [
                    'attributes' => [
                        'name' => 'newsSiteTitleSubtitleForm',
                        'id' => 'newsSiteTitleSubtitleForm',
                        'method' => 'POST',
                        'action' => '',
                    ],
                    'hydrator' => 'Zend\Stdlib\Hydrator\ArraySerializable',
                    'elements' => [
                        [
                            'spec' => [
                                'name' => 'cnews_title',
                                'type' => 'MelisText',
                                'options' => [
                                    'label' => 'tr_meliscmsnews_list_col_title',
                                    'tooltip' => 'tr_meliscmsnews_list_col_title tooltip',
                                ],
                                'attributes' => [
                                    'id' => 'cnews_title',
                                    'required' => false,
                                ],
                            ],
                        ],
                        [
                            'spec' => [
                                'name' => 'cnews_subtitle',
                                'type' => 'MelisText',
                                'options' => [
                                    'label' => 'tr_meliscmsnews_form_subtitle',
                                    'tooltip' => 'tr_meliscmsnews_form_subtitle tooltip',
                                ],
                                'attributes' => [
                                    'id' => 'cnews_subtitle',
                                ],
                            ],
                        ],
                        [
                            'spec' => [
                                'name' => 'cnews_paragraph1',
                                'type' => 'TextArea',
                                'options' => [
                                    'label' => 'tr_meliscmsnews_paragraph1',
                                    'tooltip' => 'tr_meliscmsnews_paragraph1 tooltip',
                                ],

                                'attributes' => [
                                    'id' => 'cnews_paragraph1',
                                    'value' => '',
                                    'class' => 'form-control editme',
                                    'style' => 'max-width:100%',
                                    'rows' => '4',
                                ],
                            ],
                        ],
                        [
                            'spec' => [
                                'name' => 'cnews_paragraph2',
                                'type' => 'TextArea',
                                'options' => [
                                    'label' => 'tr_meliscmsnews_paragraph2',
                                    'tooltip' => 'tr_meliscmsnews_paragraph2 tooltip',
                                ],

                                'attributes' => [
                                    'id' => 'cnews_paragraph2',
                                    'value' => '',
                                    'class' => 'form-control editme',
                                    'style' => 'max-width:100%',
                                    'rows' => '4',
                                ],
                            ],
                        ],
                        [
                            'spec' => [
                                'name' => 'cnews_paragraph3',
                                'type' => 'TextArea',
                                'options' => [
                                    'label' => 'tr_meliscmsnews_paragraph3',
                                    'tooltip' => 'tr_meliscmsnews_paragraph3 tooltip',
                                ],

                                'attributes' => [
                                    'id' => 'cnews_paragraph3',
                                    'value' => '',
                                    'class' => 'form-control editme',
                                    'style' => 'max-width:100%',
                                    'rows' => '4',
                                ],
                            ],
                        ],
                        [
                            'spec' => [
                                'name' => 'cnews_paragraph4',
                                'type' => 'TextArea',
                                'options' => [
                                    'label' => 'tr_meliscmsnews_paragraph4',
                                    'tooltip' => 'tr_meliscmsnews_paragraph4 tooltip',
                                ],

                                'attributes' => [
                                    'id' => 'cnews_paragraph4',
                                    'value' => '',
                                    'class' => 'form-control editme',
                                    'style' => 'max-width:100%',
                                    'rows' => '4',
                                ],
                            ],
                        ],
                    ],
                    'input_filter' => [
                        'cnews_title' => [
                            'name' => 'cnews_title',
                            'required' => false,
                            'validators' => [
                                [
                                    'name' => 'StringLength',
                                    'options' => [
                                        'encoding' => 'UTF-8',
                                        'max' => 255,
                                        'messages' => [
                                            \Zend\Validator\StringLength::TOO_LONG => 'tr_meliscmsnews_form_error_long_255',
                                        ],
                                    ],
                                ],
                                [
                                    'name' => 'NotEmpty',
                                    'options' => [
                                        'messages' => [
                                            \Zend\Validator\NotEmpty::IS_EMPTY => 'tr_meliscmsnews_form_error_empty',
                                        ],
                                    ],
                                ],
                            ],
                            'filters' => [
                                ['name' => 'StripTags'],
                                ['name' => 'StringTrim'],
                            ],
                        ],
                        'cnews_subtitle' => [
                            'name' => 'cnews_subtitle',
                            'required' => false,
                            'validators' => [
                                [
                                    'name' => 'StringLength',
                                    'options' => [
                                        'encoding' => 'UTF-8',
                                        'max' => 255,
                                        'messages' => [
                                            \Zend\Validator\StringLength::TOO_LONG => 'tr_meliscmsnews_form_error_long_255',
                                        ],
                                    ],
                                ],
                            ],
                            'filters' => [
                                ['name' => 'StripTags'],
                                ['name' => 'StringTrim'],
                            ],
                        ],
                    ],
                ],
                'meliscmsnews_page_detail_selector' => [
                    'attributes' => [
                        'name' => 'newsDetailsPageSelectorForm',
                        'id' => 'newsDetailsPageSelectorForm',
                        'method' => 'POST',
                        'action' => '',
                    ],
                    'hydrator' => 'Zend\Stdlib\Hydrator\ArraySerializable',
                    'elements' => [
                        [
                            'spec' => [
                                'name' => 'page-id',
                                'type' => 'select',
                                'options' => [
                                    'empty_option' => 'tr_meliscmsnews_preview_page_select',
                                    'disable_inarray_validator' => true,
                                ],
                                'attributes' => [
                                    'id' => 'page-id',
                                    'class' => 'mcnews-page-details',
                                    'value' => '',
                                    'data-news-id' => '',
                                    'data-name-space' => '',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
];
