<?php

return array(

    'plugins' => array(
        'diagnostic' => array(
            'MelisCmsNews' => array(
                // location of your test folder inside the module
                'testFolder' => 'test',
                // moduleTestName is the name of your test folder inside 'testFolder'
                'moduleTestName' => 'MelisCmsNewsTest',
                // this should be properly setup so we can recreate the factory of the database
                'db' => array(
                    // the keys will used as the function name when generated,
                    'getNewsTable' => array(
                        'model' => 'MelisCmsNews\Model\MelisCmsNews',
                        'model_table' => 'MelisCmsNews\Model\Tables\MelisCmsNewsTable',
                        'db_table_name' => 'melis_cms_news',
                    ),
                ),
                // these are the various types of methods that you would like to give payloads for testing
                // you don't have to put all the methods in the test controller,
                // instead, just put the methods that will be needing or requiring the payloads for your test.
                'methods' => array(
                    'testInsertData' => array(
                        'payloads' => array(
                            'dataToInsert' => array(
                                'cnews_status' => 1,
                                'cnews_title' => 'phpunit news test',
                                'cnews_paragraph1' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec tincidunt, lacus quis euismod vehicula, mi tortor fringilla tellus, non pharetra diam quam et orci. Aliquam ac libero at felis laoreet sodales vel sed purus. Vestibulum suscipit sem non nibh dignissim aliquam. Vivamus et lectus mollis, pretium est sed, lobortis ante.',
                                'cnews_paragraph2' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec tincidunt, lacus quis euismod vehicula, mi tortor fringilla tellus, non pharetra diam quam et orci. Aliquam ac libero at felis laoreet sodales vel sed purus. Vestibulum suscipit sem non nibh dignissim aliquam. Vivamus et lectus mollis, pretium est sed, lobortis ante.',
                                'cnews_paragraph3' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec tincidunt, lacus quis euismod vehicula, mi tortor fringilla tellus, non pharetra diam quam et orci. Aliquam ac libero at felis laoreet sodales vel sed purus. Vestibulum suscipit sem non nibh dignissim aliquam. Vivamus et lectus mollis, pretium est sed, lobortis ante.',
                                'cnews_paragraph4' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec tincidunt, lacus quis euismod vehicula, mi tortor fringilla tellus, non pharetra diam quam et orci. Aliquam ac libero at felis laoreet sodales vel sed purus. Vestibulum suscipit sem non nibh dignissim aliquam. Vivamus et lectus mollis, pretium est sed, lobortis ante.',
                                'cnews_image1'  => '/media/tests/testimage1.jpg',
                                'cnews_image2'  => '/media/tests/testimage2.png',
                                'cnews_image3'  => '/media/tests/testimage3.jpg',
                                'cnews_documents1'  => '/media/tests/testimage1.jpg',
                                'cnews_documents2'  => '/media/tests/testimage2.png',
                                'cnews_documents3'  => '/media/tests/testimage3.jpg',
                                'cnews_creation_date' => date('Y-m-d H:i:s'),
                                'cnews_publish_date' => date('Y-m-d H:i:s'),
                            ), 
                        ), 
                    ),
                    'testTableAccessWithPayloadFromConfig' => array(
                        'payloads' => array(
                            'dataToCheck' => array(
                                'column' => 'cnews_title',
                                'value' => 'phpunit news test'
                            ),
                        ),
                    ),
                    'testRemoveData' => array(
                        'payloads' => array(
                            'dataToRemove' => array(
                                'column' => 'cnews_title',
                                'value' => 'phpunit news test'
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),


);

