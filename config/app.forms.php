<?php
return array(
    'plugins' => array(
        'MelisCmsNews' => array(
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
                                'name' => 'cnews_title',
                                'type' => 'MelisText',
                                'options' => array(
                                    'label' => 'tr_meliscmsnews_list_col_title',
                                ),
                                'attributes' => array(
                                    'id' => 'cnews_title',
                                ),
                            ),
                        ),
                        array(
                            'spec' => array(
                                'name' => 'cnews_subtitle',
                                'type' => 'MelisText',
                                'options' => array(
                                    'label' => 'tr_meliscmsnews_form_subtitle',
                                ),
                                'attributes' => array(
                                    'id' => 'cnews_subtitle',
                                ),
                            ),
                        ),
                        array(
                            'spec' => array(
                                'name' => 'cnews_publish_date',
                                'type' => 'DateField',
                                'options' => array(),
                                'attributes' => array(
                                    'dateId' => 'newsPublishDate',
                                    'dateLabel' => 'tr_meliscmsnews_form_publish'
                                ),
                            ),
                        ),
                        array(
                            'spec' => array(
                                'name' => 'cnews_id',
                                'type' => 'hidden',
                            ),
                        ),
                    ),
                    'input_filter' => array(
                        'cnews_id' => array(
                            'name'     => 'cnews_id',
                            'required' => false,
                            'validators' => array(
                                array(
                                    'name'    => 'IsInt',
                                    'options' => array(
                                        'messages' => array(
                                            \Zend\I18n\Validator\IsInt::NOT_INT => 'tr_meliscms_tool_platform_not_digit',
                                            \Zend\I18n\Validator\IsInt::INVALID => 'tr_meliscms_tool_platform_not_digit',
                                        )
                                    )
                                ),
                            ),
                            'filters' => array(
                                array('name' => 'StripTags'),
                                array('name' => 'StringTrim'),
                            ),
                        ),
                        'cnews_title' => array(
                            'name'     => 'cnews_title',
                            'required' => true,
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
                                'attributes' => array(
                                    'id' => 'cnews_document',
                                    'value' => '',
                                    'class' => 'filestyle',
                                    'label' => 'Upload',
                                    'required' => 'required',
                                    'onchange' => 'newsImagePreview(".imgDocThumbnail", this);',
                                ),
//                                 'options' => array(
//                                     'label' => 'tr_meliscmsnews_input_file_doc1',
//                                 ),
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
                'meliscmsnews_paragraph_form' => array(
                    'attributes' => array(
                        'name' => 'newsParagraphForm',
                        'id' => 'newsParagraphForm',
                        'method' => 'POST',
                        'action' => '',
                    ),
                    'hydrator'  => 'Zend\Stdlib\Hydrator\ArraySerializable',
                    'elements' => array(
                        array(
                            'spec' => array(
                                'name' => 'paragraph',
                                'type' => 'TextArea',
                                
                                'attributes' => array(
                                    'id' => 'cnews_paragraph',
                                    'value' => '',
                                    'class' => 'form-control editme',
                                    'style' => 'max-width:100%',
                                    'rows' => '4',
                                ),
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
                        'paragraph' => array(
                            'name'     => 'paragraph',
                            'required' => false,
                            'validators' => array(
                            ),
                            'filters'  => array(
                                array('name' => 'StringTrim'),
                            ),
                        ),
                    ),
                ),
            ),            
        ),
    ),
);
