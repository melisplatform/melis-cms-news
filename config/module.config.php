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
            ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'template_map' => array(
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
        'strategies' => array(
            'ViewJsonStrategy',
        ),
    ),
    'asset_manager' => array(
        'resolver_configs' => array(
            'aliases' => array(
                'MelisCmsNews/' => __DIR__ . '/../public/',
            ),
        ),
    ),
);
