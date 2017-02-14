<?php

return array(
    'plugins' => array(
        'meliscmsnews' => array(
            'plugins' => array(
                'MelisCmsNewsListNewsPlugin' => array(
                    'front' => array(
                        'template_path' => 'MelisCmsNews/listnews',
                        'id' => 'listnews',
                        
                        // optional, if found will add a pagination object
                        'pagination' => array(
                            'current' => 1,
                            'nbPerPage' => 9,
                            'nbPageBeforeAfter' => 2
                        ),
                        
                        // optional, filtering
                        'filter' => array(
                            'column' => 'cnews_publish_date',
                            'order' => 'DESC',
                            'date_min' => null,
                            'date_max' => null,
                            'search' => '',
                        ),
                    ),
                    'melis' => array(
                    ),
                ),
                'MelisCmsNewsShowNewsPlugin' => array(
                    'front' => array(
                        'template_path' => 'MelisCmsNews/shownews',
                        'id' => 'shownews',
                        'newsId' => 1,
                    ),
                    'melis' => array(
                    ),
                ),
                'MelisCmsNewsLatestNewsPlugin' => array(
                    'front' => array(
                        'template_path' => 'MelisCmsNews/latestnews',
                        'id' => 'latestnews',
                        
                        // optional, filtering
                        'filter' => array(
                            'column' => 'cnews_publish_date',
                            'order' => 'DESC',
                            'date_min' => null,
                            'date_max' => null,
                            'limit' => 5,
                        ),
                    ),
                    'melis' => array(
                    ),
                ),
             ),
        ),
     ),
);