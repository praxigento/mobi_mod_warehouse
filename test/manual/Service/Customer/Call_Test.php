<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Warehouse\Service\Customer;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\ObjectManagerInterface;
use Praxigento\Warehouse\Service\ICustomer;

include_once(__DIR__ . '/../../phpunit_bootstrap.php');

class Call_ManualTest extends \Praxigento\Core\Test\BaseIntegrationTest
{
    /** @var  ObjectManagerInterface */
    private $manObj;
    /** @var  ICustomer */
    private $obj;

    protected function setUp()
    {
        $this->manObj = \Magento\Framework\App\ObjectManager::getInstance();
        $this->obj = $this->manObj->create(ICustomer::class);
    }

    public function test_getCurrentStock()
    {
        /** === Test Data === */
        $CUST_ID = 2;
        /** === Call and asserts  === */
        $req = new Request\GetCurrentStock();
        $req->setCustomerId($CUST_ID);
        $res = $this->obj->getCurrentStock($req);
        $this->assertNotNull($res);
    }
}