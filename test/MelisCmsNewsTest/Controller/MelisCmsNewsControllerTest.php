<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2016 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisCmsNewsTest\Controller;

use MelisCore\ServiceManagerGrabber;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;
class MelisCmsNewsControllerTest extends AbstractHttpControllerTestCase
{
    protected $traceError = false;
    protected $sm;
    protected $method = 'save';

    public function setUp()
    {
        $this->sm  = new ServiceManagerGrabber();
    }

        /**
     * Get getNewsTable table
     * @return mixed
     */
    private function getNewsTable()
    {
        $conf = $this->sm->getPhpUnitTool()->getTable('MelisCmsNews', __METHOD__);
        return $this->sm->getTableMock(new $conf['model'], $conf['model_table'], $conf['db_table_name'], $this->method);
    }


    public function getPayload($method)
    {
        return $this->sm->getPhpUnitTool()->getPayload('MelisCmsNews', $method);
    }

    /**
     * START ADDING YOUR TESTS HERE
     */

    public function testFetchAllData()
    {
        $data = $this->getNewsTable()->fetchAll()->toArray();
        $this->assertNotEmpty($data);
    }

    public function testForceTestToFailWhenFetchingData()
    {
        $data = $this->getNewsTable()->fetchAll()->toArray();
        $this->assertEmpty($data, '<h4>This test is supposed to fail, so just ignore this test.</h4>');
    }
    
    public function testInsertData()
    {
        $payloads = $this->getPayload(__METHOD__);
        $this->method = 'fetchAll';
        $this->getNewsTable()->save($payloads['dataToInsert']);
    }
    
    public function testTableAccessWithPayloadFromConfig()
    {
        $payloads = $this->getPayload(__METHOD__);
        $column =  $payloads['dataToCheck']['column'];
        $value =  $payloads['dataToCheck']['value'];
        $data = $this->getNewsTable()->getEntryByField($column, $value)->current();
        $this->assertNotEmpty($payloads);
        $this->assertNotEmpty($data);
    
    }

    public function testRemoveData()
    {
        $payloads = $this->getPayload(__METHOD__);
        $columns = $payloads['dataToRemove']['column'];
        $value   = $payloads['dataToRemove']['value'];
    
        $this->method = 'fetchAll';
        $this->getNewsTable()->deleteByField($columns, $value);
        $this->assertTrue(true);
    }

}

