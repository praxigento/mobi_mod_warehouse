<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Data\Agg;

include_once(__DIR__ . '/../../phpunit_bootstrap.php');

class Warehouse_UnitTest extends \Praxigento\Core\Test\BaseCase\Mockery
{
    /** @var  Warehouse */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        $this->obj = new Warehouse();
    }


    public function test_accessors()
    {
        /** === Test Data === */
        $CODE = 'code';
        $CUR = 'currency';
        $ID = 'id';
        $NOTE = 'note';
        $WEBSITE_ID = 'wsid';
        /** === Call and asserts  === */
        $this->obj->setCode($CODE);
        $this->obj->setCurrency($CUR);
        $this->obj->setId($ID);
        $this->obj->setNote($NOTE);
        $this->obj->setWebsiteId($WEBSITE_ID);
        $this->assertEquals($CODE, $this->obj->getCode());
        $this->assertEquals($CUR, $this->obj->getCurrency());
        $this->assertEquals($ID, $this->obj->getId());
        $this->assertEquals($NOTE, $this->obj->getNote());
        $this->assertEquals($WEBSITE_ID, $this->obj->getWebsiteId());
    }
}