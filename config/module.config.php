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
    'service_manager' => array(
        'invokables' => array(
            
        ),
        'aliases' => array(
            //services
            'MelisCmsNewsService'       => \MelisCmsNews\Service\MelisCmsNewsService::class,
            'MelisCmsNewsSeoService'    => \MelisCmsNews\Service\MelisCmsNewsSeoService::class,
            //tables
            'MelisCmsNewsTable'         => \MelisCmsNews\Model\Tables\MelisCmsNewsTable::class,
            'MelisCmsNewsTextsTable'    => \MelisCmsNews\Model\Tables\MelisCmsNewsTextsTable::class,
            'MelisCmsNewsSeoTable'      => \MelisCmsNews\Model\Tables\MelisCmsNewsSeoTable::class,
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'MelisCmsNews\Controller\MelisCmsNewsList' => \MelisCmsNews\Controller\MelisCmsNewsListController::class,
            'MelisCmsNews\Controller\MelisCmsNews' => \MelisCmsNews\Controller\MelisCmsNewsController::class,
        ),
    ),
    'controller_plugins' => array(
        'invokables' => array(
            'MelisCmsNewsLatestNewsPlugin' => 'MelisCmsNews\Controller\Plugin\MelisCmsNewsLatestNewsPlugin',
            'MelisCmsNewsListNewsPlugin' => 'MelisCmsNews\Controller\Plugin\MelisCmsNewsListNewsPlugin',
            'MelisCmsNewsShowNewsPlugin' => 'MelisCmsNews\Controller\Plugin\MelisCmsNewsShowNewsPlugin',

            /** Extending MelisCmsComments module */
            'DashboardLatestComments' => 'MelisCmsNews\Controller\Plugin\Dashboard\DashboardLatestComments'
        )
    ),

    'form_elements' => array(
        'factories' => array(
            'MelisCmsNewsSelect' => 'MelisCmsNews\Form\Factory\MelisCmsNewsSelectFactory',
            'MelisCmsNewsBOSelect' => 'MelisCmsNews\Form\Factory\MelisCmsNewsBOSelectFactory',
        ),
    ),
    'view_helpers' => [
        'aliases' => [
            'MelisCmsNewsListPlugin' => \MelisCmsNews\View\Helper\MelisCmsNewsListHelper::class,
            'MelisCmsNewsLatestPlugin' => \MelisCmsNews\View\Helper\MelisCmsNewsLatestHelper::class,
            'MelisCmsNewsShowNewsPlugin' => \MelisCmsNews\View\Helper\MelisCmsNewsShowNewsHelper::class
        ]
    ],
    'view_manager' => array(
        'doctype'                  => 'HTML5',
        'template_map' => array(
            'MelisCmsNews/latestnews'                           => __DIR__ . '/../view/melis-cms-news/plugins/latestnews.phtml',
            'MelisCmsNews/listnews'                             => __DIR__ . '/../view/melis-cms-news/plugins/listnews.phtml',
            'MelisCmsNews/shownews'                             => __DIR__ . '/../view/melis-cms-news/plugins/shownews.phtml',
            'MelisCmsNews/list-paginator'                       => __DIR__ . '/../view/melis-cms-news/plugins/list-paginator.phtml',
            'MelisCmsNews/plugin/modal/modal-filter-form'       => __DIR__ . '/../view/melis-cms-news/plugins/modal-filter-form.phtml',
            'MelisCmsNews/plugin/modal/modal-pagination-form'   => __DIR__ . '/../view/melis-cms-news/plugins/modal-pagination-form.phtml',
            'MelisCmsNews/plugin/modal/modal-template-form'     => __DIR__ . '/../view/melis-cms-news/plugins/modal-template-form.phtml',

            /** Extending MelisCmsComments module */
            'MelisCmsNews/plugins/dashboard/latest-comments/tab-content' => __DIR__ . '/../view/melis-cms-news/plugins/dashboard/latest-comments/tab-content.phtml',
            'MelisCmsNews/plugins/dashboard/latest-comments/filters' => __DIR__ . '/../view/melis-cms-news/plugins/dashboard/latest-comments/filters.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
        'strategies' => array(
            'ViewJsonStrategy',
        ),
    ),
);
