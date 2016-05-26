<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Data\Entity;

include_once(__DIR__ . '/../../phpunit_bootstrap.php');

class Customer_UnitTest extends \Praxigento\Core\Test\BaseMockeryCase
{
    /** @var  Customer */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        $this->obj = new Customer();
    }

    public function test_accessors()
    {
        /** === Test Data === */
        $CUST_REF = 'cust ref';
        $STOCK_REF = 'stock ref';
        /** === Call and asserts  === */
        $this->obj->setCustomerRef($CUST_REF);
        $this->obj->setStockRef($STOCK_REF);
        $this->assertEquals($CUST_REF, $this->obj->getCustomerRef());
        $this->assertEquals($STOCK_REF, $this->obj->getStockRef());
    }
}