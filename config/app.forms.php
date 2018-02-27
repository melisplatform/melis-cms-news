<?php
return array(
    'plugins' => array(
        'MelisCmsNews' => array(
            'conf' => array(
                // user rights exclusions
                'rightsDisplay' => 'none',
            ),
            'forms' => array(
                'meliscmsnews_properties_form' => array(
                    'attributes' => array(
                        'name' => 'newsLetterForm',
                        'id' => 'newsLetterForm',
                        'method' => 'POST',
                        'action' => '',
                    ),
                    'hydrator'  => 'Zend\Stdlib\Hydrator\ArraySerializable',
                    'elements' => array(
                        array(
                            'spec' => array(
                                'name' => 'cnews_publish_date',
                                'type' => 'DateField',
                                'options' => array(
                                    'label' => 'tr_meliscmsnews_form_publish',
                                    'tooltip' => 'tr_meliscmsnews_form_publish tooltip',
                                ),
                                'attributes' => array(
                                    'dateId' => 'newsPublishDate',
                                ),
                            ),
                        ),
                        array(
                            'spec' => array(
                                'name' => 'cnews_unpublish_date',
                                'type' => 'DateField',
                                'options' => array(
                                    'label' => 'tr_meliscmsnews_form_unpublish',
                                    'tooltip' => 'tr_meliscmsnews_form_unpublish tooltip',
                                ),
                                'attributes' => array(
                                    'dateId' => 'newsUnpublishDate',
                                ),
                            ),
                        ),
                        array(
                            'spec' => array(
                                'name' => 'cnews_id',
                                'type' => 'hidden',
                            ),
                        ),
                        array(
                            'spec' => array(
                                'name' => 'cnews_creation_date',
                                'type' => 'hidden',
                            ),
                        ),
                    ),
                    'input_filter' => array(
                    ),
                ),
                'meliscmsnews_file_form' => array(
                    'attributes' => array(
                        'name' => 'newsFileForm',
                        'id' => 'newsFileForm',
                        'enctype' => "multipart/form-data",
                        'method' => 'POST',
                        'action' => '',
                    ),
                    'hydrator'  => 'Zend\Stdlib\Hydrator\ArraySerializable',
                    'elements' => array(                        
                        array(
                            'spec' => array(
                                'name' => 'cnews_document',
                                'type' => 'file',
                                'options' => array(
                                    'label' => 'tr_meliscmsnews_save_upload_file',  
                                ),
                                'attributes' => array(
                                    'id' => 'cnews_document',
                                    'value' => '',
                                    'class' => 'filestyle',
                                    'label' => 'Upload',
                                    'required' => 'required',
                                    'onchange' => 'newsImagePreview(".imgDocThumbnail", this);',
                                ),
                            ),
                        ),
                        array(
                            'spec' => array(
                                'name' => 'type',
                                'type' => 'hidden',
                            ),
                        ),
                        array(
                            'spec' => array(
                                'name' => 'cnews_id',
                                'type' => 'hidden',
                            ),
                        ),
                        array(
                            'spec' => array(
                                'name' => 'column',
                                'type' => 'hidden',
                            ),
                        ),
                    ),
                    'input_filter' => array(
                    ),
                ),
                'meliscmsnews_site_select_form' => array(
                    'attributes' => array(
                        'name' => 'newsSiteSelectForm',
                        'id' => 'newsSiteSelectForm',
                        'method' => 'POST',
                        'action' => '',
                    ),
                    'hydrator'  => 'Zend\Stdlib\Hydrator\ArraySerializable',
                    'elements' => array(
                        array(
                            'spec' => array(
                                'name' => 'cnews_site_id',
                                'type' => 'MelisCoreSiteSelect',
                                'options' => array(
                                    'label' => 'tr_meliscms_tool_templates_tpl_site_id',
                                    'tooltip' => 'tr_meliscmsnews_tpl_site_id tooltip',
                                    'empty_option' => 'tr_meliscmsliderdetails_common_label_choose',
                                    'disable_inarray_validator' => true,
                                ),
                                'attributes' => array(
                                    'id' => 'id_cnews_site_id',
                                    'value' => '',
                                ),
                            ),
                        ),
                    ),
                    'input_filter' => array(
                        'cnews_site_id' => array(
                            'name' => 'cnews_site_id',
                            'required' => false,
                            'validators' => array(),
                            'filters' => array(),
                        ),
                    ),
                ),
                'meliscmsnews_site_title_subtitle_form' => array(
                    'attributes' => array(
                        'name' => 'newsSiteTitleSubtitleForm',
                        'id' => 'newsSiteTitleSubtitleForm',
                        'method' => 'POST',
                        'action' => '',
                    ),
                    'hydrator'  => 'Zend\Stdlib\Hydrator\ArraySerializable',
                    'elements' => array(
                        array(
                            'spec' => array(
                                'name' => 'cnews_title',
                                'type' => 'MelisText',
                                'options' => array(
                                    'label' => 'tr_meliscmsnews_list_col_title',
                                    'tooltip' => 'tr_meliscmsnews_list_col_title tooltip',
                                ),
                                'attributes' => array(
                                    'id' => 'cnews_title',
                                    'required' => false,
                                ),
                            ),
                        ),
                        array(
                            'spec' => array(
                                'name' => 'cnews_subtitle',
                                'type' => 'MelisText',
                                'options' => array(
                                    'label' => 'tr_meliscmsnews_form_subtitle',
                                    'tooltip' => 'tr_meliscmsnews_form_subtitle tooltip',
                                ),
                                'attributes' => array(
                                    'id' => 'cnews_subtitle',
                                ),
                            ),
                        ),
                        array(
                            'spec' => array(
                                'name' => 'cnews_paragraph1',
                                'type' => 'TextArea',
                                 'options' => array(
                                    'label' => 'tr_meliscmsnews_paragraph1',
                                    'tooltip' => 'tr_meliscmsnews_paragraph1 tooltip',
                                ),
                                
                                'attributes' => array(
                                    'id' => 'cnews_paragraph1',
                                    'value' => '',
                                    'class' => 'form-control editme',
                                    'style' => 'max-width:100%',
                                    'rows' => '4',
                                ),
                            ),
                        ),
                        array(
                            'spec' => array(
                                'name' => 'cnews_paragraph2',
                                'type' => 'TextArea',
                                 'options' => array(
                                    'label' => 'tr_meliscmsnews_paragraph2',
                                    'tooltip' => 'tr_meliscmsnews_paragraph2 tooltip',
                                ),
                                
                                'attributes' => array(
                                    'id' => 'cnews_paragraph2',
                                    'value' => '',
                                    'class' => 'form-control editme',
                                    'style' => 'max-width:100%',
                                    'rows' => '4',
                                ),
                            ),
                        ),
                        array(
                            'spec' => array(
                                'name' => 'cnews_paragraph3',
                                'type' => 'TextArea',
                                 'options' => array(
                                    'label' => 'tr_meliscmsnews_paragraph3',
                                    'tooltip' => 'tr_meliscmsnews_paragraph3 tooltip',
                                ),
                                
                                'attributes' => array(
                                    'id' => 'cnews_paragraph3',
                                    'value' => '',
                                    'class' => 'form-control editme',
                                    'style' => 'max-width:100%',
                                    'rows' => '4',
                                ),
                            ),
                        ),
                        array(
                            'spec' => array(
                                'name' => 'cnews_paragraph4',
                                'type' => 'TextArea',
                                 'options' => array(
                                    'label' => 'tr_meliscmsnews_paragraph4',
                                    'tooltip' => 'tr_meliscmsnews_paragraph4 tooltip',
                                ),
                                
                                'attributes' => array(
                                    'id' => 'cnews_paragraph4',
                                    'value' => '',
                                    'class' => 'form-control editme',
                                    'style' => 'max-width:100%',
                                    'rows' => '4',
                                ),
                            ),
                        ),
                    ),
                     'input_filter' => array(
                        'cnews_title' => array(
                            'name'     => 'cnews_title',
                            'required' => false,
                            'validators' => array(
                                array(
                                    'name'    => 'StringLength',
                                    'options' => array(
                                        'encoding' => 'UTF-8',
                                        'max'      => 255,
                                        'messages' => array(
                                            \Zend\Validator\StringLength::TOO_LONG => 'tr_meliscmsnews_form_error_long_255',
                                        ),
                                    ),
                                ),
                                array(
                                    'name' => 'NotEmpty',
                                    'options' => array(
                                        'messages' => array(
                                            \Zend\Validator\NotEmpty::IS_EMPTY => 'tr_meliscmsnews_form_error_empty',
                                        ),
                                    ),
                                ),
                            ),
                            'filters'  => array(
                                array('name' => 'StripTags'),
                                array('name' => 'StringTrim'),
                            ),
                        ),
                        'cnews_subtitle' => array(
                            'name'     => 'cnews_subtitle',
                            'required' => false,
                            'validators' => array(
                                array(
                                    'name'    => 'StringLength',
                                    'options' => array(
                                        'encoding' => 'UTF-8',
                                        'max'      => 255,
                                        'messages' => array(
                                            \Zend\Validator\StringLength::TOO_LONG => 'tr_meliscmsnews_form_error_long_255',
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
                ),
            ),            
        ),
    ),
);
