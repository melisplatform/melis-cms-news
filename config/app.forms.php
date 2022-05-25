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
                    'hydrator' => 'Laminas\Hydrator\ArraySerializableHydrator',
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
                    'hydrator' => 'Laminas\Hydrator\ArraySerializableHydrator',
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
                    'hydrator' => 'Laminas\Hydrator\ArraySerializableHydrator',
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
                                            \Laminas\Validator\NotEmpty::IS_EMPTY => 'tr_meliscmsnews_empty_site',
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
                    'hydrator' => 'Laminas\Hydrator\ArraySerializableHydrator',
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
                                'type' => 'Textarea',
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
                                'type' => 'Textarea',
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
                                'type' => 'Textarea',
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
                                'type' => 'Textarea',
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
                        [
                            'spec' => [
                                'name' => 'cnews_paragraph5',
                                'type' => 'Textarea',
                                'options' => [
                                    'label' => 'tr_meliscmsnews_paragraph5',
                                    'tooltip' => 'tr_meliscmsnews_paragraph5 tooltip',
                                ],
                                'attributes' => [
                                    'id' => 'cnews_paragraph5',
                                    'value' => '',
                                    'class' => 'form-control editme',
                                    'style' => 'max-width:100%',
                                    'rows' => '4',
                                ],
                            ],
                        ],
                        [
                            'spec' => [
                                'name' => 'cnews_paragraph6',
                                'type' => 'Textarea',
                                'options' => [
                                    'label' => 'tr_meliscmsnews_paragraph6',
                                    'tooltip' => 'tr_meliscmsnews_paragraph6 tooltip',
                                ],
                                'attributes' => [
                                    'id' => 'cnews_paragraph6',
                                    'value' => '',
                                    'class' => 'form-control editme',
                                    'style' => 'max-width:100%',
                                    'rows' => '4',
                                ],
                            ],
                        ],
                        [
                            'spec' => [
                                'name' => 'cnews_paragraph7',
                                'type' => 'Textarea',
                                'options' => [
                                    'label' => 'tr_meliscmsnews_paragraph7',
                                    'tooltip' => 'tr_meliscmsnews_paragraph7 tooltip',
                                ],
                                'attributes' => [
                                    'id' => 'cnews_paragraph7',
                                    'value' => '',
                                    'class' => 'form-control editme',
                                    'style' => 'max-width:100%',
                                    'rows' => '4',
                                ],
                            ],
                        ],
                        [
                            'spec' => [
                                'name' => 'cnews_paragraph8',
                                'type' => 'Textarea',
                                'options' => [
                                    'label' => 'tr_meliscmsnews_paragraph8',
                                    'tooltip' => 'tr_meliscmsnews_paragraph8 tooltip',
                                ],
                                'attributes' => [
                                    'id' => 'cnews_paragraph8',
                                    'value' => '',
                                    'class' => 'form-control editme',
                                    'style' => 'max-width:100%',
                                    'rows' => '4',
                                ],
                            ],
                        ],
                        [
                            'spec' => [
                                'name' => 'cnews_paragraph9',
                                'type' => 'Textarea',
                                'options' => [
                                    'label' => 'tr_meliscmsnews_paragraph9',
                                    'tooltip' => 'tr_meliscmsnews_paragraph9 tooltip',
                                ],
                                'attributes' => [
                                    'id' => 'cnews_paragraph9',
                                    'value' => '',
                                    'class' => 'form-control editme',
                                    'style' => 'max-width:100%',
                                    'rows' => '4',
                                ],
                            ],
                        ],
                        [
                            'spec' => [
                                'name' => 'cnews_paragraph10',
                                'type' => 'Textarea',
                                'options' => [
                                    'label' => 'tr_meliscmsnews_paragraph10',
                                    'tooltip' => 'tr_meliscmsnews_paragraph10 tooltip',
                                ],
                                'attributes' => [
                                    'id' => 'cnews_paragraph10',
                                    'value' => '',
                                    'class' => 'form-control editme',
                                    'style' => 'max-width:100%',
                                    'rows' => '4',
                                ],
                            ],
                        ],
                        [
                            'spec' => [
                                'name' => 'cnews_paragraph_order',
                                'type' => 'hidden',                             
                                'attributes' => [
                                    'id' => 'cnews_paragraph_order',
                                    'required' => false,
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
                                            \Laminas\Validator\StringLength::TOO_LONG => 'tr_meliscmsnews_form_error_long_255',
                                        ],
                                    ],
                                ],
                                [
                                    'name' => 'NotEmpty',
                                    'options' => [
                                        'messages' => [
                                            \Laminas\Validator\NotEmpty::IS_EMPTY => 'tr_meliscmsnews_form_error_empty',
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
                                            \Laminas\Validator\StringLength::TOO_LONG => 'tr_meliscmsnews_form_error_long_255',
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
                    'hydrator' => 'Laminas\Hydrator\ArraySerializableHydrator',
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
               'meliscmsnews_seo_form' => [
                    'attributes' => [
                        'name' => 'newsSeoForm',
                        'id' => 'newsSeoForm',
                        'method' => 'POST',
                        'action' => '',
                    ],
                    'hydrator' => 'Laminas\Hydrator\ArraySerializableHydrator',
                    'elements' => array( 
                        array(
                            'spec' => array(
                                'name' => 'cnews_seo_id',
                                'type' => 'hidden'                                
                            ),
                        ),    
                        array(
                            'spec' => array(
                                'name' => 'cnews_id',
                                'type' => 'hidden'                                
                            ),
                        ), 
                        array(
                            'spec' => array(
                                'name' => 'cnews_seo_lang_id',
                                'type' => 'hidden'                                
                            ),
                        ),
                        array(
                            'spec' => array(
                                'name' => 'cnews_seo_meta_title',
                                'type' => 'MelisText',
                                'options' => array(
                                    'label' => 'tr_meliscmsnews_page_tab_seo_form_Meta Title',
                                    'tooltip' => 'tr_meliscmsnews_page_tab_seo_form_Meta Title tooltip',
                                ),
                                'attributes' => array(
                                    'id' => 'cnews_seo_meta_title',
                                    'value' => '',
                                ),
                            ),
                        ),
                        array(
                            'spec' => array(
                                'name' => 'cnews_seo_meta_description',
                                'type' => 'Textarea',
                                'options' => array(
                                    'label' => 'tr_meliscmsnews_page_tab_seo_form_Meta Description',
                                    'tooltip' => 'tr_meliscmsnews_page_tab_seo_form_Meta Description tooltip',
                                ),
                                'attributes' => array(
                                    'id' => 'cnews_seo_meta_description',
                                    'value' => '',
                                    'rows' => 5,
                                    'class' => 'melis-seo-desc form-control'
                                ),
                            ),
                        ),
                        array(
                            'spec' => array(
                                'name' => 'cnews_seo_url',
                                'type' => 'MelisText',
                                'options' => array(
                                    'label' => 'tr_meliscmsnews_page_tab_seo_form_Url',
                                    'tooltip' => 'tr_meliscmsnews_page_tab_seo_form_Url tooltip',
                                ),
                                'attributes' => array(
                                    'id' => 'cnews_seo_url',
                                    'value' => '',
                                ),
                            ),
                        ),
                        array(
                            'spec' => array(
                                'name' => 'cnews_seo_url_redirect',
                                'type' => 'MelisText',
                                'options' => array(
                                    'label' => 'tr_meliscmsnews_page_tab_seo_form_Url Redirect',
                                    'tooltip' => 'tr_meliscmsnews_page_tab_seo_form_Url Redirect tooltip',
                                ),
                                'attributes' => array(
                                    'id' => 'cnews_seo_url_redirect',
                                    'value' => '',
                                ),
                            ),
                        ),
                        array(
                            'spec' => array(
                                'name' => 'cnews_seo_url_301',
                                'type' => 'MelisText',
                                'options' => array(
                                    'label' => 'tr_meliscmsnews_page_tab_seo_form_Url 301',
                                    'tooltip' => 'tr_meliscmsnews_page_tab_seo_form_Url 301 tooltip',
                                ),
                                'attributes' => array(
                                    'id' => 'cnews_seo_url_301',
                                    'value' => '',
                                ),
                            ),
                        ),
                        array(
                            'spec' => array(
                                'name' => 'cnews_seo_canonical',
                                'type' => 'MelisText',
                                'options' => array(
                                    'label' => 'tr_meliscmsnews_page_tab_seo_form_canonical',
                                    'tooltip' => 'tr_meliscmsnews_page_tab_seo_form_canonical tooltip',
                                ),
                                'attributes' => array(
                                    'id' => 'cnews_seo_canonical',
                                    'value' => '',
                                ),
                            ),
                        ),                                            
                    ),
                    'input_filter' => array( 
                        'cnews_seo_meta_title' => array(
                            'name'     => 'cnews_seo_meta_title',
                            'required' => false,
                            'validators' => array(
                                array(
                                    'name'    => 'StringLength',
                                    'options' => array(
                                        'encoding' => 'UTF-8',
                                        'max'      => 65,
                                        'messages' => array(
                                            \Laminas\Validator\StringLength::TOO_LONG => 'tr_meliscmsnews_pageseo_form_page_title_long',
                                        ),
                                    ),
                                ),
                            ),
                            'filters'  => array(
                                array('name' => 'StripTags'),
                                array('name' => 'StringTrim'),
                            ),
                        ),
                        'cnews_seo_meta_description' => array(
                            'name'     => 'cnews_seo_meta_description',
                            'required' => false,
                            'validators' => array(
                                array(
                                    'name'    => 'StringLength',
                                    'options' => array(
                                        'encoding' => 'UTF-8',
                                        'max'      => 255,
                                        'messages' => array(
                                            \Laminas\Validator\StringLength::TOO_LONG => 'tr_meliscmsnews_pageseo_form_page_desc_long',
                                        ),
                                    ),
                                ),
                            ),
                            'filters'  => array(
                                array('name' => 'StripTags'),
                                array('name' => 'StringTrim'),
                            ),
                        ),
                        'cnews_seo_canonical' => array(
                            'name'     => 'cnews_seo_canonical',
                            'required' => false,
                            'validators' => array(
                                array(
                                    'name'    => 'StringLength',
                                    'options' => array(
                                        'encoding' => 'UTF-8',
                                        'max'      => 255,
                                        'messages' => array(
                                            \Laminas\Validator\StringLength::TOO_LONG => 'tr_meliscmsnews_pageseo_form_page_desc_long',
                                        ),
                                    ),
                                ),
                            ),
                            'filters'  => array(
                                array('name' => 'StripTags'),
                                array('name' => 'StringTrim'),
                            ),
                        ),
                        'cnews_seo_url' => array(
                            'name'     => 'cnews_seo_url',
                            'required' => false,
                            'validators' => array(
                                array(
                                    'name'    => 'StringLength',
                                    'options' => array(
                                        'encoding' => 'UTF-8',
                                        'max'      => 255,
                                        'messages' => array(
                                            \Laminas\Validator\StringLength::TOO_LONG => 'tr_meliscmsnews_pageseo_form_page_url_too_long',
                                        ),
                                    ),
                                ),
                            ),
                            'filters'  => array(
                                array('name' => 'StripTags'),
                                array('name' => 'StringTrim'),
                            ),
                        ),
                        'cnews_seo_url_redirect' => array(
                            'name'     => 'cnews_seo_url_redirect',
                            'required' => false,
                            'validators' => array(
                                array(
                                    'name'    => 'StringLength',
                                    'options' => array(
                                        'encoding' => 'UTF-8',
                                        'max'      => 255,
                                        'messages' => array(
                                            \Laminas\Validator\StringLength::TOO_LONG => 'tr_meliscmsnews_pageseo_form_page_url_too_long',
                                        ),
                                    ),
                                ),
                            ),
                            'filters'  => array(
                                array('name' => 'StripTags'),
                                array('name' => 'StringTrim'),
                            ),
                        ),
                        'cnews_seo_url_301' => array(
                            'name'     => 'cnews_seo_url_301',
                            'required' => false,
                            'validators' => array(
                                array(
                                    'name'    => 'StringLength',
                                    'options' => array(
                                        'encoding' => 'UTF-8',
                                        'max'      => 255,
                                        'messages' => array(
                                            \Laminas\Validator\StringLength::TOO_LONG => 'tr_meliscmsnews_pageseo_form_page_url_too_long',
                                        ),
                                    ),
                                ),
                            ),
                            'filters'  => array(
                                array('name' => 'StripTags'),
                                array('name' => 'StringTrim'),
                            ),
                        ),
                    ),
                ],
            ],
        ],
    ],
];
