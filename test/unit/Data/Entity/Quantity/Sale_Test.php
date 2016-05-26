<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Data\Entity\Quantity;

include_once(__DIR__ . '/../../../phpunit_bootstrap.php');

class Sale_UnitTest extends \Praxigento\Core\Test\BaseMockeryCase
{
    /** @var  Sale */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        $this->obj = new Sale();
    }

    public function test_accessors()
    {
        /** === Test Data === */
        $LOT_REF = 'lot ref';
        $SALE_ITEM_REF = 'sale item ref';
        $TOTAL = 'total';
        /** === Call and asserts  === */
        $this->obj->setLotRef($LOT_REF);
        $this->obj->setSaleItemRef($SALE_ITEM_REF);
        $this->obj->setTotal($TOTAL);
        $this->assertEquals($LOT_REF, $this->obj->getLotRef());
        $this->assertEquals($SALE_ITEM_REF, $this->obj->getSaleItemRef());
        $this->assertEquals($TOTAL, $this->obj->getTotal());
    }
}