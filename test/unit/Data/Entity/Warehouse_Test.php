<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Data\Entity;

include_once(__DIR__ . '/../../phpunit_bootstrap.php');

class Warehouse_UnitTest extends \Praxigento\Core\Lib\Test\BaseMockeryCase
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
        /* === Test Data === */
        $CODE = 'code';
        $CUR = 'currency';
        $ID = 'id';
        $NOTE = 'note';
        $STOCK_REF = 'stock';
        /* === Call and asserts  === */
        $this->obj->setCode($CODE);
        $this->obj->setCurrency($CUR);
        $this->obj->setNote($NOTE);
        $this->obj->setStockRef($STOCK_REF);
        $this->assertEquals($CODE, $this->obj->getCode());
        $this->assertEquals($CUR, $this->obj->getCurrency());
        $this->assertEquals($NOTE, $this->obj->getNote());
        $this->assertEquals($STOCK_REF, $this->obj->getStockRef());
    }
}