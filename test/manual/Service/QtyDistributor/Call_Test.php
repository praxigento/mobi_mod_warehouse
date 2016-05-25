<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Warehouse\Service\QtyDistributor;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\ObjectManagerInterface;
use Praxigento\Warehouse\Service\IQtyDistributor;

include_once(__DIR__ . '/../../phpunit_bootstrap.php');

class Call_ManualTest extends \Praxigento\Core\Test\BaseIntegrationTest
{
    /** @var  ObjectManagerInterface */
    private $manObj;
    /** @var  IQtyDistributor */
    private $obj;

    protected function setUp()
    {
        $this->manObj = \Magento\Framework\App\ObjectManager::getInstance();
        $this->obj = $this->manObj->create(IQtyDistributor::class);
    }

    public function test_registerForSaleItem()
    {
        /** === Test Data === */
        $ITEM_ID = 1;
        $PROD_ID = 1;
        $STOCK_ID = 1;
        $QTY = 8;
        /** === Call and asserts  === */
        $req = new Request\RegisterForSaleItem();
        $req->setItemId($ITEM_ID);
        $req->setProductId($PROD_ID);
        $req->setStockId($STOCK_ID);
        $req->setQuantity($QTY);
        $res = $this->obj->registerForSaleItem($req);
        $this->assertNotNull($res);
    }
}