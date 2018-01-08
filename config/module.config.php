<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2015 Melis Technology (http://www.melistechnology.com)
 *
 */

return array(
    'router' => array(
        'routes' => array(
            'melis-backoffice' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '/melis[/]',
                ),
                'child_routes' => array(
                    'application-meliscmsnews' => array(
                        'type'    => 'Literal',
                        'options' => array(
                            'route'    => 'MelisCmsNews',
                            'defaults' => array(
                                '__NAMESPACE__' => 'MelisCmsNews\Controller',
                                'controller'    => 'CustomEdition',
                                'action'        => 'renderCustomTableEditor',
                            ),
                        ),
                        'may_terminate' => true,
                        'child_routes' => array(
                            'default' => array(
                                'type'    => 'Segment',
                                'options' => array(
                                    'route'    => '/[:controller[/:action]]',
                                    'constraints' => array(
                                        'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                        'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                                    ),
                                    'defaults' => array(
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
    'translator' => array(
    	'locale' => 'en_EN',
	),
    'service_manager' => array(
        'invokables' => array(
            
        ),
        'aliases' => array(
            'translator' => 'MvcTranslator',
            'MelisCmsNewsTable' => 'MelisCmsNews\Model\Tables\MelisCmsNewsTable',
        ),
        'factories' => array(
            //services
            'MelisCmsNewsService' => 'MelisCmsNews\Service\Factory\MelisCmsNewsServiceFactory',
            
            //tables
            'MelisCmsNews\Model\Tables\MelisCmsNewsTable' => 'MelisCmsNews\Model\Tables\Factory\MelisCmsNewsTableFactory',
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'MelisCmsNews\Controller\MelisCmsNewsList' => 'MelisCmsNews\Controller\MelisCmsNewsListController',
            'MelisCmsNews\Controller\MelisCmsNews' => 'MelisCmsNews\Controller\MelisCmsNewsController',
            'MelisCmsNews\Controller\MelisSetup' => 'MelisCmsNews\Controller\MelisSetupController',
            ),
    ),
    'controller_plugins' => array(
        'invokables' => array(
            'MelisCmsNewsLatestNewsPlugin' => 'MelisCmsNews\Controller\Plugin\MelisCmsNewsLatestNewsPlugin',
            'MelisCmsNewsListNewsPlugin' => 'MelisCmsNews\Controller\Plugin\MelisCmsNewsListNewsPlugin',
            'MelisCmsNewsShowNewsPlugin' => 'MelisCmsNews\Controller\Plugin\MelisCmsNewsShowNewsPlugin',
        )
    ),

    'form_elements' => array(
        'factories' => array(
            'MelisCmsNewsSelect' => 'MelisCmsNews\Form\Factory\MelisCmsNewsSelectFactory',
            'MelisCmsSiteNewsSelect' => 'MelisCmsNews\Form\Factory\MelisCmsSiteNewsSelectFactory',
        ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'template_map' => array(
            'MelisCmsNews/latestnews'                           => __DIR__ . '/../view/melis-cms-news/plugins/latestnews.phtml',
            'MelisCmsNews/listnews'                             => __DIR__ . '/../view/melis-cms-news/plugins/listnews.phtml',
            'MelisCmsNews/shownews'                             => __DIR__ . '/../view/melis-cms-news/plugins/shownews.phtml',
            'MelisCmsNews/list-paginator'                       => __DIR__ . '/../view/melis-cms-news/plugins/list-paginator.phtml',
            'MelisCmsNews/plugin/modal/modal-filter-form'       => __DIR__ . '/../view/melis-cms-news/plugins/modal-filter-form.phtml',
            'MelisCmsNews/plugin/modal/modal-pagination-form'   => __DIR__ . '/../view/melis-cms-news/plugins/modal-pagination-form.phtml',
            'MelisCmsNews/plugin/modal/modal-template-form'     => __DIR__ . '/../view/melis-cms-news/plugins/modal-template-form.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
        'strategies' => array(
            'ViewJsonStrategy',
        ),
    ),
);
