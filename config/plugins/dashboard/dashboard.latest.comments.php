<?php
/**
 * Used to add configurations when using MelisCmsComments module's
 * Latest comments Dashboard plugin
 */
return [
    'plugins' => [
        'meliscmscomments' => [
            'dashboard_plugins' => [
                'LatestCommentsPlugin' => [
                    'PostTypes' => [
                        'news' => [
                            'icon' => 'fa fa-list-alt fa-2x',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
