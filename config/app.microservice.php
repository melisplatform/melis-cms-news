<?php 

return [
	'plugins' => [
		'microservice' => [
			//Module Name
			'MelisCmsNews' => [ 

				/**
				 * This service retrieves  a list of news
				 * MelisCmsNewsService.php
				 */
				'MelisCmsNewsService' => [

					'getNewsList' => [
						'attributes' => [
							'name' => 'microservice_form',
							'id'   => 'microservice_form',
							'method' => 'POST',
							'action' => $_SERVER['REQUEST_URI']
						],
						'hydrator' => '\Laminas\Hydrator\ArraySerializable',
						'elements' => [
							[
								'spec' => [
									'name' => 'status',
									'type' => 'Text',
									'options' => [
										'label' => 'status',
									],
									'attributes' => [
										'id' => 'status',
										'value' => '',
										'class' => '',
										'placeholder' => '1',
										'data-type' => 'bool'
									],
								],
							],
                            [
                                'spec' => [
                                    'name' => 'langId',
                                    'type' => 'Text',
                                    'options' => [
                                        'label' => 'langId',
                                    ],
                                    'attributes' => [
                                        'id' => 'langId',
                                        'value' => '',
                                        'class' => '',
                                        'placeholder' => '1',
                                        'data-type' => 'int'
                                    ],
                                ],
                            ],
							[
								'spec' => [
									'name' => 'dateMin',
									'type' => 'Text',
									'options' => [
										'label' => 'dateMin',
									],
									'attributes' => [
										'id' => 'dateMin',
										'value' => '',
										'class' => '',
										'placeholder' => '2017-04-18',
										'data-type' => 'date'
									],
								],
							],
							[
								'spec' => [
									'name' => 'dateMax',
									'type' => 'Text',
									'options' => [
										'label' => 'dateMax',
									],
									'attributes' => [
										'id' => 'dateMax',
										'value' => '',
										'class' => '',
										'placeholder' => '2017-04-18',
										'data-type' => 'date'
									],
								],
							],
							[
								'spec' => [
									'name' => 'publishDateMin',
									'type' => 'Text',
									'options' => [
										'label' => 'publishDateMin',
									],
									'attributes' => [
										'id' => 'publishDateMin',
										'value' => '',
										'class' => '',
										'placeholder' => '2017-04-18',
										'data-type' => 'date'
									],
								],
							],
							[
								'spec' => [
									'name' => 'publishDateMax',
									'type' => 'Text',
									'options' => [
										'label' => 'publishDateMax',
									],
									'attributes' => [
										'id' => 'publishDateMax',
										'value' => '',
										'class' => '',
										'placeholder' => '2017-04-18',
										'data-type' => 'date'
									],
								],
							],
							[
								'spec' => [
									'name' => 'unpublishFilter',
									'type' => 'Text',
									'options' => [
										'label' => 'unpublishFilter',
									],
									'attributes' => [
										'id' => 'unpublishFilter',
										'value' => '',
										'class' => '',
										'placeholder' => 'false',
										'data-type'  => 'bool'
									],
								],
							],
							[
								'spec' => [
									'name' => 'start',
									'type' => 'Text',
									'options' => [
										'label' => 'start',
									],
									'attributes' => [
										'id' => 'start',
										'value' => '',
										'class' => '',
										'placeholder' => '0',
										'data-type' => 'int'
									],
								],
							],
							[
								'spec' => [
									'name' => 'limit',
									'type' => 'Text',
									'options' => [
										'label' => 'limit',
									],
									'attributes' => [
										'id' => 'limit',
										'value' => '',
										'class' => '',
										'placeholder' => '5',
										'data-type' => 'int'
									],
								],
							],
							[
								'spec' => [
									'name' => 'orderColumn',
									'type' => 'Text',
									'options' => [
										'label' => 'orderColumn',
									],
									'attributes' => [
										'id' => 'orderColumn',
										'value' => '',
										'class' => '',
										'placeholder' => 'cnews_id',
										'data-type' => 'string'
									],
								],
							],
							[
								'spec' => [
									'name' => 'order',
									'type' => 'Text',
									'options' => [
										'label' => 'order',
									],
									'attributes' => [
										'id' => 'order',
										'value' => '',
										'class' => '',
										'placeholder' => 'ASC',
										'data-type' => 'string'
									],
								],
							],
							[
								'spec' => [
									'name' => 'siteId',
									'type' => 'Text',
									'options' => [
										'label' => 'siteId',
									],
									'attributes' => [
										'id' => 'siteId',
										'value' => '',
										'class' => '',
										'placeholder' => '1',
										'data-type' => 'int'
									],
								],
							],
							[
								'spec' => [
									'name' => 'search',
									'type' => 'Text',
									'options' => [
										'label' => 'search',
									],
									'attributes' => [
										'id' => 'search',
										'value' => '',
										'class' => '',
										'placeholder' => 'fashion',
										'data-type' => 'string'
									],
								],
							],
						],
						'input_filter' => [
							'status' => [
								'name' => 'status',
								'required' => false,
								'validators' => [
									[
										'name' => 'NotEmpty',
										'options' => [
											'message' => [
												\Laminas\Validator\NotEmpty::IS_EMPTY => 'Please enter status'
											],
										],
									],
								],
								'filters' => [
									['name' => 'StripTags'],
									['name' => 'StringTrim']
								],
							],
							'dateMin' => [
								'name' => 'dateMin',
								'required' => false,
								'validators' => [
									[
										'name' => 'NotEmpty',
										'options' => [
											'message' => [
												\Laminas\Validator\NotEmpty::IS_EMPTY => 'Please enter minimum date'
											],
										],
									],
								],
								'filters' => [
									['name' => 'StripTags'],
									['name' => 'StringTrim']
								],
							],
							'dateMax' => [
								'name' => 'dateMax',
								'required' => false,
								'validators' => [
									[
										'name' => 'NotEmpty',
										'options' => [
											'message' => [
												\Laminas\Validator\NotEmpty::IS_EMPTY => 'Please enter maximum date'
											],
										],
									],
								],
								'filters' => [
									['name' => 'StripTags'],
									['name' => 'StringTrim']
								],
							],
							'publishDateMin' => [
								'name' => 'publishDateMin',
								'required' => false,
								'validators' => [
									[
										'name' => 'NotEmpty',
										'options' => [
											'message' => [
												\Laminas\Validator\NotEmpty::IS_EMPTY => 'Please enter minimum publish date'
											],
										],
									],
								],
								'filters' => [
									['name' => 'StripTags'],
									['name' => 'StringTrim']
								],
							],
							'publishDateMax' => [
								'name' => 'publishDateMax',
								'required' => false,
								'validators' => [
									[
										'name' => 'NotEmpty',
										'options' => [
											'message' => [
												\Laminas\Validator\NotEmpty::IS_EMPTY => 'Please enter maximum publish date'
											],
										],
									],
								],
								'filters' => [
									['name' => 'StripTags'],
									['name' => 'StringTrim']
								],
							],
							'unpublishFilter' => [
								'name' => 'unpublishFilter',
								'required' => false,
								'validators' => [
									[
										'name' => 'NotEmpty',
										'options' => [
											'message' => [
												\Laminas\Validator\NotEmpty::IS_EMPTY => 'Please enter unpublish Filter'
											],
										],
									],
								],
								'filters' => [
									['name' => 'StripTags'],
									['name' => 'StringTrim']
								],
							],
							'start' => [
								'name' => 'start',
								'required' => false,
								'validators' => [
									[
										'name' => 'NotEmpty',
										'options' => [
											'message' => [
												\Laminas\Validator\NotEmpty::IS_EMPTY => 'Please enter start'
											],
										],
									],
								],
								'filters' => [
									['name' => 'StripTags'],
									['name' => 'StringTrim']
								],
							],
							'limit' => [
								'name' => 'limit',
								'required' => false,
								'validators' => [
									[
										'name' => 'NotEmpty',
										'options' => [
											'message' => [
												\Laminas\Validator\NotEmpty::IS_EMPTY => 'Please enter limit'
											],
										],
									],
								],
								'filters' => [
									['name' => 'StripTags'],
									['name' => 'StringTrim']
								],
							],
							'orderColumn' => [
								'name' => 'orderColumn',
								'required' => false,
								'validators' => [
									[
										'name' => 'NotEmpty',
										'options' => [
											'message' => [
												\Laminas\Validator\NotEmpty::IS_EMPTY => 'Please enter column to order'
											],
										],
									],
								],
								'filters' => [
									['name' => 'StripTags'],
									['name' => 'StringTrim']
								],
							],
							'order' => [
								'name' => 'order',
								'required' => false,
								'validators' => [
									[
										'name' => 'NotEmpty',
										'options' => [
											'message' => [
												\Laminas\Validator\NotEmpty::IS_EMPTY => 'Please enter order direction'
											],
										],
									],
								],
								'filters' => [
									['name' => 'StripTags'],
									['name' => 'StringTrim']
								],
							],
							'siteId' => [
								'name' => 'siteId',
								'required' => false,
								'validators' => [
									[
										'name' => 'IsInt',
										'options' => [
											'message' => [
												\Laminas\I18n\Validator\IsInt::INVALID => 'siteId must be an integer'
											],
										],
									],
								],
								'filters' => [
									['name' => 'StripTags'],
									['name' => 'StringTrim']
								],
							],
							'search' => [
								'name' => 'search',
								'required' => false,
								'validators' => [
									[
										'name' => 'NotEmpty',
										'options' => [
											'message' => [
												\Laminas\Validator\NotEmpty::IS_EMPTY => 'Search'
											],
										],
									],
								],
								'filters' => [
									['name' => 'StripTags'],
									['name' => 'StringTrim']
								],
							],
						],
					],
					'getNewsById' => [
						'attributes' => [
							'name' => 'microservice_form',
							'id'   => 'microservice_form',
							'method' => 'POST',
							'action' => $_SERVER['REQUEST_URI']
						],
						'hydrator' => 'Laminas\Hydrator\ArraySerializable',
						'elements' => [
							[
                                'spec' => [
                                    'name' => 'newsId',
                                    'type' => 'Text',
                                    'options' => [
                                        'label' => 'newsId',
                                    ],
                                    'attributes' => [
                                        'id' => 'newsId',
                                        'value' => '',
                                        'class' => '',
                                        'placeholder' => '1',
                                        'data-type' => 'int'
                                    ],
                                ],
                            ],
                            [
                                'spec' => [
                                    'name' => 'langId',
                                    'type' => 'Text',
                                    'options' => [
                                        'label' => 'langId',
                                    ],
                                    'attributes' => [
                                        'id' => 'langId',
                                        'value' => '',
                                        'class' => '',
                                        'placeholder' => '1',
                                        'data-type' => 'int'
                                    ],
                                ],
                            ],
						],
						'input_filter' => [
							'newsId' => [
								'name' => 'newsId',
								'required' => true,
								'validators' => [
									[
										'name' => 'IsInt',
										'options' => [
											'message' => [
												\Laminas\I18n\Validator\IsInt::INVALID => 'newsId must be an integer'
											],
										],
									],
								],
								'filters' => [
									['name' => 'StripTags'],
									['name' => 'StringTrim']
								],
							],
                            'langId' => [
                                'name' => 'langId',
                                'required' => false,
                                'validators' => [
                                    [
                                        'name' => 'IsInt',
                                        'options' => [
                                            'message' => [
                                                \Laminas\I18n\Validator\IsInt::INVALID => 'langId must be an integer'
                                            ],
                                        ],
                                    ],
                                ],
                                'filters' => [
                                    ['name' => 'StripTags'],
                                    ['name' => 'StringTrim']
                                ],
                            ],
						],
					],
				],
			],
		],
	],
];
