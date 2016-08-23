<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Data\Entity;

include_once(__DIR__ . '/../../phpunit_bootstrap.php');

class Quantity_UnitTest extends \Praxigento\Core\Test\BaseCase\Mockery
{
    /** @var  Quantity */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        $this->obj = new Quantity();
    }

    public function test_accessors()
    {
        /** === Test Data === */
        $LOT_REF = 'lot ref';
        $STOCK_ITEM_REF = 'stock item ref';
        $TOTAL = 'total';
        /** === Call and asserts  === */
        $this->obj->setLotRef($LOT_REF);
        $this->obj->setStockItemRef($STOCK_ITEM_REF);
        $this->obj->setTotal($TOTAL);
        $this->assertEquals($LOT_REF, $this->obj->getLotRef());
        $this->assertEquals($STOCK_ITEM_REF, $this->obj->getStockItemRef());
        $this->assertEquals($TOTAL, $this->obj->getTotal());
    }
}