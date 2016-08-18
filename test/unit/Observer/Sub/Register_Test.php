<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Warehouse\Observer\Sub;

include_once(__DIR__ . '/../../phpunit_bootstrap.php');

class Register_UnitTest
    extends \Praxigento\Core\Test\BaseMockeryCase
{
    /** @var  \Mockery\MockInterface */
    private $mCallQtyDistributor;
    /** @var  \Mockery\MockInterface */
    private $mManObj;
    /** @var  \Mockery\MockInterface */
    private $mManStock;
    /** @var  Register */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        /** create mocks */
        $this->mManObj = $this->_mockObjectManager();
        $this->mManStock = $this->_mock(\Praxigento\Warehouse\Tool\IStockManager::class);
        $this->mCallQtyDistributor = $this->_mock(\Praxigento\Warehouse\Service\IQtyDistributor::class);
        /** setup mocks for constructor */
        /** create object to test */
        $this->obj = new Register(
            $this->mManObj,
            $this->mManStock,
            $this->mCallQtyDistributor
        );
    }

    public function test_constructor()
    {
        /** === Call and asserts  === */
        $this->assertInstanceOf(Register::class, $this->obj);
    }

    public function test_splitQty()
    {
        /** === Test Data === */
        $STORE_ID = 2;
        $STOCK_ID = 4;
        $PROD_ID = 32;
        $ITEM_ID = 64;
        $QTY = 128;
        $ORDER = $this->_mock(\Magento\Sales\Api\Data\OrderInterface::class);
        /** === Setup Mocks === */
        // $storeId = $order->getStoreId();
        $ORDER->shouldReceive('getStoreId')->once()
            ->andReturn($STORE_ID);
        // $stockId = $this->_manStock->getStockIdByStoreId($storeId);
        $this->mManStock
            ->shouldReceive('getStockIdByStoreId')->once()
            ->andReturn($STOCK_ID);
        // $items = $order->getItems();
        $mItem = $this->_mock(\Magento\Sales\Api\Data\OrderItemInterface::class);
        $ORDER->shouldReceive('getItems')->once()
            ->andReturn([$mItem]);
        //
        // FIRST ITERATION
        //
        // $prodId = $item->getProductId();
        $mItem->shouldReceive('getProductId')->once()
            ->andReturn($PROD_ID);
        // $itemId = $item->getItemId();
        $mItem->shouldReceive('getItemId')->once()
            ->andReturn($ITEM_ID);
        // $qty = $item->getQtyOrdered();
        $mItem->shouldReceive('getQtyOrdered')->once()
            ->andReturn($QTY);
        // $itemData = $this->_manObj->create(\Praxigento\Warehouse\Service\QtyDistributor\Data\Item::class);
        $mItemData = $this->_mock(\Praxigento\Warehouse\Service\QtyDistributor\Data\Item::class);
        $this->mManObj
            ->shouldReceive('create')->once()
            ->andReturn($mItemData);
        // $itemData->setItemId($itemId);
        $mItemData->shouldReceive('setItemId')->once()->with($ITEM_ID);
        // $itemData->setProductId($prodId);
        $mItemData->shouldReceive('setProductId')->once()->with($PROD_ID);
        // $itemData->setQuantity($qty);
        $mItemData->shouldReceive('setQuantity')->once()->with($QTY);
        // $itemData->setStockId($stockId);
        $mItemData->shouldReceive('setStockId')->once()->with($STOCK_ID);
        //
        // $reqSale = $this->_manObj->create(\Praxigento\Warehouse\Service\QtyDistributor\Request\RegisterSale::class);
        $mReqSale = $this->_mock(\Praxigento\Warehouse\Service\QtyDistributor\Request\RegisterSale::class);
        $this->mManObj
            ->shouldReceive('create')->once()
            ->andReturn($mReqSale);
        // $reqSale->setSaleItems($itemsData);
        $mReqSale->shouldReceive('setSaleItems')->once();
        // $this->_callQtyDistributor->registerSale($reqSale);
        $this->mCallQtyDistributor
            ->shouldReceive('registerSale')->once()->with($mReqSale);
        /** === Call and asserts  === */
        $res = $this->obj->splitQty($ORDER);
    }

}