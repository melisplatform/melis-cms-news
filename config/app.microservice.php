<?php 

return array(
	'plugins' => array(
		'microservice' => array(
			//Module Name
			'MelisCmsNews' => array( 

				/**
				 * This service retrieves  a list of news
				 * MelisCmsNewsService.php
				 */
				'MelisCmsNewsService' => array(

					'getNewsList' => array(
						'attributes' => array(
							'name' => 'microservice_form',
							'id'   => 'microservice_form',
							'method' => 'POST',
							'action' => $_SERVER['REQUEST_URI']
						),
						'hydrator' => '\Zend\Stdlib\Hydrator\ArraySerializable',
						'elements' => array(
							array(
								'spec' => array(
									'name' => 'status',
									'type' => 'Text',
									'options' => array(
										'label' => 'status',
									),
									'attributes' => array(
										'id' => 'status',
										'value' => '',
										'class' => '',
										'placeholder' => '1',
										'data-type' => 'bool'
									),
								),
							),
                            array(
                                'spec' => array(
                                    'name' => 'langId',
                                    'type' => 'Text',
                                    'options' => array(
                                        'label' => 'langId',
                                    ),
                                    'attributes' => array(
                                        'id' => 'langId',
                                        'value' => '',
                                        'class' => '',
                                        'placeholder' => '1',
                                        'data-type' => 'int'
                                    ),
                                ),
                            ),
							array(
								'spec' => array(
									'name' => 'dateMin',
									'type' => 'Text',
									'options' => array(
										'label' => 'dateMin',
									),
									'attributes' => array(
										'id' => 'dateMin',
										'value' => '',
										'class' => '',
										'placeholder' => '2017-04-18',
										'data-type' => 'date'
									),
								),
							),
							array(
								'spec' => array(
									'name' => 'dateMax',
									'type' => 'Text',
									'options' => array(
										'label' => 'dateMax',
									),
									'attributes' => array(
										'id' => 'dateMax',
										'value' => '',
										'class' => '',
										'placeholder' => '2017-04-18',
										'data-type' => 'date'
									),
								),
							),
							array(
								'spec' => array(
									'name' => 'publishDateMin',
									'type' => 'Text',
									'options' => array(
										'label' => 'publishDateMin',
									),
									'attributes' => array(
										'id' => 'publishDateMin',
										'value' => '',
										'class' => '',
										'placeholder' => '2017-04-18',
										'data-type' => 'date'
									),
								),
							),
							array(
								'spec' => array(
									'name' => 'publishDateMax',
									'type' => 'Text',
									'options' => array(
										'label' => 'publishDateMax',
									),
									'attributes' => array(
										'id' => 'publishDateMax',
										'value' => '',
										'class' => '',
										'placeholder' => '2017-04-18',
										'data-type' => 'date'
									),
								),
							),
							array(
								'spec' => array(
									'name' => 'unpublishFilter',
									'type' => 'Text',
									'options' => array(
										'label' => 'unpublishFilter',
									),
									'attributes' => array(
										'id' => 'unpublishFilter',
										'value' => '',
										'class' => '',
										'placeholder' => 'false',
										'data-type'  => 'bool'
									),
								),
							),
							array(
								'spec' => array(
									'name' => 'start',
									'type' => 'Text',
									'options' => array(
										'label' => 'start',
									),
									'attributes' => array(
										'id' => 'start',
										'value' => '',
										'class' => '',
										'placeholder' => '0',
										'data-type' => 'int'
									),
								),
							),
							array(
								'spec' => array(
									'name' => 'limit',
									'type' => 'Text',
									'options' => array(
										'label' => 'limit',
									),
									'attributes' => array(
										'id' => 'limit',
										'value' => '',
										'class' => '',
										'placeholder' => '5',
										'data-type' => 'int'
									),
								),
							),
							array(
								'spec' => array(
									'name' => 'orderColumn',
									'type' => 'Text',
									'options' => array(
										'label' => 'orderColumn',
									),
									'attributes' => array(
										'id' => 'orderColumn',
										'value' => '',
										'class' => '',
										'placeholder' => 'cnews_id',
										'data-type' => 'string'
									),
								),
							),
							array(
								'spec' => array(
									'name' => 'order',
									'type' => 'Text',
									'options' => array(
										'label' => 'order',
									),
									'attributes' => array(
										'id' => 'order',
										'value' => '',
										'class' => '',
										'placeholder' => 'ASC',
										'data-type' => 'string'
									),
								),
							),
							array(
								'spec' => array(
									'name' => 'siteId',
									'type' => 'Text',
									'options' => array(
										'label' => 'siteId',
									),
									'attributes' => array(
										'id' => 'siteId',
										'value' => '',
										'class' => '',
										'placeholder' => '1',
										'data-type' => 'int'
									),
								),
							),
							array(
								'spec' => array(
									'name' => 'search',
									'type' => 'Text',
									'options' => array(
										'label' => 'search',
									),
									'attributes' => array(
										'id' => 'search',
										'value' => '',
										'class' => '',
										'placeholder' => 'fashion',
										'data-type' => 'string'
									),
								),
							),
						),
						'input_filter' => array(
							'status' => array(
								'name' => 'status',
								'required' => false,
								'validators' => array(
									array(
										'name' => 'NotEmpty',
										'options' => array(
											'message' => array(
												\Zend\Validator\NotEmpty::IS_EMPTY => 'Please enter status'
											),
										),
									),
								),
								'filters' => array(
									array('name' => 'StripTags'),
									array('name' => 'StringTrim')
								),
							),
							'dateMin' => array(
								'name' => 'dateMin',
								'required' => false,
								'validators' => array(
									array(
										'name' => 'NotEmpty',
										'options' => array(
											'message' => array(
												\Zend\Validator\NotEmpty::IS_EMPTY => 'Please enter minimum date'
											),
										),
									),
								),
								'filters' => array(
									array('name' => 'StripTags'),
									array('name' => 'StringTrim')
								),
							),
							'dateMax' => array(
								'name' => 'dateMax',
								'required' => false,
								'validators' => array(
									array(
										'name' => 'NotEmpty',
										'options' => array(
											'message' => array(
												\Zend\Validator\NotEmpty::IS_EMPTY => 'Please enter maximum date'
											),
										),
									),
								),
								'filters' => array(
									array('name' => 'StripTags'),
									array('name' => 'StringTrim')
								),
							),
							'publishDateMin' => array(
								'name' => 'publishDateMin',
								'required' => false,
								'validators' => array(
									array(
										'name' => 'NotEmpty',
										'options' => array(
											'message' => array(
												\Zend\Validator\NotEmpty::IS_EMPTY => 'Please enter minimum publish date'
											),
										),
									),
								),
								'filters' => array(
									array('name' => 'StripTags'),
									array('name' => 'StringTrim')
								),
							),
							'publishDateMax' => array(
								'name' => 'publishDateMax',
								'required' => false,
								'validators' => array(
									array(
										'name' => 'NotEmpty',
										'options' => array(
											'message' => array(
												\Zend\Validator\NotEmpty::IS_EMPTY => 'Please enter maximum publish date'
											),
										),
									),
								),
								'filters' => array(
									array('name' => 'StripTags'),
									array('name' => 'StringTrim')
								),
							),
							'unpublishFilter' => array(
								'name' => 'unpublishFilter',
								'required' => false,
								'validators' => array(
									array(
										'name' => 'NotEmpty',
										'options' => array(
											'message' => array(
												\Zend\Validator\NotEmpty::IS_EMPTY => 'Please enter unpublish Filter'
											),
										),
									),
								),
								'filters' => array(
									array('name' => 'StripTags'),
									array('name' => 'StringTrim')
								),
							),
							'start' => array(
								'name' => 'start',
								'required' => false,
								'validators' => array(
									array(
										'name' => 'NotEmpty',
										'options' => array(
											'message' => array(
												\Zend\Validator\NotEmpty::IS_EMPTY => 'Please enter start'
											),
										),
									),
								),
								'filters' => array(
									array('name' => 'StripTags'),
									array('name' => 'StringTrim')
								),
							),
							'limit' => array(
								'name' => 'limit',
								'required' => false,
								'validators' => array(
									array(
										'name' => 'NotEmpty',
										'options' => array(
											'message' => array(
												\Zend\Validator\NotEmpty::IS_EMPTY => 'Please enter limit'
											),
										),
									),
								),
								'filters' => array(
									array('name' => 'StripTags'),
									array('name' => 'StringTrim')
								),
							),
							'orderColumn' => array(
								'name' => 'orderColumn',
								'required' => false,
								'validators' => array(
									array(
										'name' => 'NotEmpty',
										'options' => array(
											'message' => array(
												\Zend\Validator\NotEmpty::IS_EMPTY => 'Please enter column to order'
											),
										),
									),
								),
								'filters' => array(
									array('name' => 'StripTags'),
									array('name' => 'StringTrim')
								),
							),
							'order' => array(
								'name' => 'order',
								'required' => false,
								'validators' => array(
									array(
										'name' => 'NotEmpty',
										'options' => array(
											'message' => array(
												\Zend\Validator\NotEmpty::IS_EMPTY => 'Please enter order direction'
											),
										),
									),
								),
								'filters' => array(
									array('name' => 'StripTags'),
									array('name' => 'StringTrim')
								),
							),
							'siteId' => array(
								'name' => 'siteId',
								'required' => false,
								'validators' => array(
									array(
										'name' => 'IsInt',
										'options' => array(
											'message' => array(
												\Zend\I18n\Validator\IsInt::INVALID => 'siteId must be an integer'
											),
										),
									),
								),
								'filters' => array(
									array('name' => 'StripTags'),
									array('name' => 'StringTrim')
								),
							),
							'search' => array(
								'name' => 'search',
								'required' => false,
								'validators' => array(
									array(
										'name' => 'NotEmpty',
										'options' => array(
											'message' => array(
												\Zend\Validator\NotEmpty::IS_EMPTY => 'Search'
											),
										),
									),
								),
								'filters' => array(
									array('name' => 'StripTags'),
									array('name' => 'StringTrim')
								),
							),
						),
					),
					'getNewsById' => array(
						'attributes' => array(
							'name' => 'microservice_form',
							'id'   => 'microservice_form',
							'method' => 'POST',
							'action' => $_SERVER['REQUEST_URI']
						),
						'hydrator' => '\Zend\Stdlib\Hydrator\ArraySerializable',
						'elements' => array(
							array(
								'spec' => array(
									'name' => 'newsId',
									'type' => 'Text',
									'options' => array(
										'label' => 'newsId',
									),
									'attributes' => array(
										'id' => 'newsId',
										'value' => '',
										'class' => '',
										'placeholder' => '1',
										'data-type' => 'int'
									),
								),
							),
						),
						'input_filter' => array(
							'newsId' => array(
								'name' => 'newsId',
								'required' => true,
								'validators' => array(
									array(
										'name' => 'IsInt',
										'options' => array(
											'message' => array(
												\Zend\I18n\Validator\IsInt::INVALID => 'newsId must be an integer'
											),
										),
									),
								),
								'filters' => array(
									array('name' => 'StripTags'),
									array('name' => 'StringTrim')
								),
							),
						),
					),
				),
			),
		),
	),
);