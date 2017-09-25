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
										'label' => 'Status',
									),
									'attributes' => array(
										'id' => 'status',
										'value' => '',
										'class' => '',
										'placeholder' => 'Enter Status',
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
										'placeholder' => 'Enter dateMin',
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
										'placeholder' => 'Enter dateMax',
									),
								),
							),
							array(
								'spec' => array(
									'name' => 'puhlishDateMin',
									'type' => 'Text',
									'options' => array(
										'label' => 'puhlishDateMin',
									),
									'attributes' => array(
										'id' => 'puhlishDateMin',
										'value' => '',
										'class' => '',
										'placeholder' => 'Enter publish`DateMin',
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
										'placeholder' => 'Enter publishDateMax',
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
										'placeholder' => 'Enter unpublishFilter',
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
										'placeholder' => 'Enter start',
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
										'placeholder' => 'Enter limit',
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
										'placeholder' => 'Enter Order Column',
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
										'placeholder' => 'Enter order',
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
										'placeholder' => 'Enter Site Id',
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
										'placeholder' => 'search',
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
										'label' => 'News Id',
									),
									'attributes' => array(
										'id' => 'newsId',
										'value' => '',
										'class' => '',
										'placeholder' => 'Enter news Id',
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
												\Zend\I18n\Validator\IsInt::INVALID => 'News Id must be an integer'
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