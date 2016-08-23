<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Warehouse\Plugin\CatalogInventory\Model;

include_once(__DIR__ . '/../../../phpunit_bootstrap.php');

if (!function_exists('__')) {
    function __($in)
    {
        $result = new \Magento\Framework\Phrase($in);
        return $result;
    }
}

class StockManagement_UnitTest
    extends \Praxigento\Core\Test\BaseCase\Mockery
{
    /** @var  \Mockery\MockInterface */
    protected $mCallQtyDistributor;
    /** @var  \Mockery\MockInterface */
    protected $mConfigStock;
    /** @var  \Mockery\MockInterface */
    protected $mManStock;
    /** @var  \Mockery\MockInterface */
    protected $mManTrans;
    /** @var  \Mockery\MockInterface */
    protected $mProviderStockRegistry;
    /** @var  \Mockery\MockInterface */
    protected $mResourceStock;
    /** @var  \Mockery\MockInterface */
    protected $mStockState;
    /** @var  \Mockery\MockInterface */
    private $mSubject;
    /** @var  StockManagement */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        /** create mocks */
        $this->mSubject = $this->_mock(\Magento\CatalogInventory\Model\StockManagement::class);
        $this->mResourceStock = $this->_mock(\Magento\CatalogInventory\Model\ResourceModel\Stock::class);
        $this->mProviderStockRegistry = $this->_mock(\Magento\CatalogInventory\Model\Spi\StockRegistryProviderInterface::class);
        $this->mConfigStock = $this->_mock(\Magento\CatalogInventory\Api\StockConfigurationInterface::class);
        $this->mStockState = $this->_mock(\Magento\CatalogInventory\Model\StockState::class);
        $this->mManStock = $this->_mock(\Praxigento\Warehouse\Tool\IStockManager::class);
        $this->mManTrans = $this->_mock(\Praxigento\Core\Transaction\Database\IManager::class);
        $this->mCallQtyDistributor = $this->_mock(\Praxigento\Warehouse\Service\IQtyDistributor::class);
        /** create object to test */
        $this->obj = new StockManagement(
            $this->mResourceStock,
            $this->mProviderStockRegistry,
            $this->mConfigStock,
            $this->mStockState,
            $this->mManStock,
            $this->mManTrans,
            $this->mCallQtyDistributor
        );
    }

    /**
     * @expectedException  \Magento\Framework\Exception\LocalizedException
     */
    public function test_aroundRegisterProductsSale_exception()
    {
        /** === Test Data === */
        $WEBSITE_ID = 0;
        $STOCK_ID = 4;
        $PROD_ID = 32;
        $ITEM_ID = 128;
        $TYPE_ID = 'simple';
        $QTY_STORED = 202;
        $QTY_ORDERED = 2;
        $ITEMS = [
            $PROD_ID => $QTY_ORDERED
        ];
        $LOCKED_ITEM_CONTINUE = [
            'product_id' => $PROD_ID,
            'type_id' => $TYPE_ID
        ];
        $LOCKED_ITEM_EXCEPTION = [
            'product_id' => $PROD_ID,
            'type_id' => $TYPE_ID
        ];
        $LOCKED_ITEMS = [$LOCKED_ITEM_CONTINUE, $LOCKED_ITEM_EXCEPTION];
        /** === Setup Mocks === */
        $mProceed = function () {
        };
        // $stockId = $this->_manStock->getCurrentStockId();
        $this->mManStock
            ->shouldReceive('getCurrentStockId')->once()
            ->andReturn($STOCK_ID);
        // $def = $this->_manTrans->begin();
        $mDef = $this->_mockTransactionDefinition();
        $this->mManTrans
            ->shouldReceive('begin')->once()
            ->andReturn($mDef);
        // $lockedItems = $this->_resourceStock->lockProductsStock(array_keys($items), $stockId);
        $this->mResourceStock
            ->shouldReceive('lockProductsStock')->once()
            ->andReturn($LOCKED_ITEMS);
        //
        // FIRST ITERATION
        // $stockItem = $this->_providerStockRegistry->getStockItem($productId, $stockId);
        $mStockItem = $this->_mock(\Magento\CatalogInventory\Api\Data\StockItemInterface::class);
        $this->mProviderStockRegistry
            ->shouldReceive('getStockItem')->once()
            ->with($PROD_ID, $STOCK_ID)
            ->andReturn($mStockItem);
        // $stockItemId = $stockItem->getItemId();
        $mStockItem->shouldReceive('getItemId')->once()
            ->andReturn($ITEM_ID);
        // $canSubtractQty = $stockItemId && $this->_canSubtractQty($stockItem);
        // protected function _canSubtractQty(StockItemInterface $stockItem)
        // $result = $stockItem->getManageStock() && $this->_configStock->canSubtractQty();
        $mStockItem->shouldReceive('getManageStock')->once()
            ->andReturn(true);
        $this->mConfigStock
            ->shouldReceive('canSubtractQty')->once()
            ->andReturn(false);
        //
        // SECOND ITERATION
        //  (some mocks are defined before)
        //
        // $canSubtractQty = $stockItemId && $this->_canSubtractQty($stockItem);
        // protected function _canSubtractQty(StockItemInterface $stockItem)
        // $result = $stockItem->getManageStock() && $this->_configStock->canSubtractQty();
        $mStockItem->shouldReceive('getManageStock')->once()
            ->andReturn(true);
        $this->mConfigStock
            ->shouldReceive('canSubtractQty')->once()
            ->andReturn(true);
        // if (... || !$this->_configStock->isQty($lockedItemRecord['type_id'])) {
        $this->mConfigStock
            ->shouldReceive('isQty')->once()
            ->with($TYPE_ID)
            ->andReturn(true);
        // if (!$stockItem->hasAdminArea() && ...) {...}
        $mStockItem->shouldReceive('hasAdminArea')->once()
            ->andReturn(false);
        // if (... && !$this->_stockState->checkQty($productId, $orderedQty)) {...}
        $this->mStockState
            ->shouldReceive('checkQty')->once()
            ->andReturn(false);
        // $this->_manTrans->rollback($def);
        $this->mManTrans
            ->shouldReceive('rollback')->once()->with($mDef);
        /** === Call and asserts  === */
        $res = $this->obj->aroundRegisterProductsSale(
            $this->mSubject,
            $mProceed,
            $ITEMS,
            $WEBSITE_ID
        );
        $this->assertTrue(is_array($res));
    }

    public function test_aroundRegisterProductsSale_process()
    {
        /** === Test Data === */
        $WEBSITE_ID = 0;
        $STOCK_ID = 4;
        $PROD_ID = 32;
        $ITEM_ID = 128;
        $TYPE_ID = 'simple';
        $QTY_STORED = 202;
        $QTY_ORDERED = 2;
        $ITEMS = [
            $PROD_ID => $QTY_ORDERED
        ];
        $LOCKED_ITEM = [
            'product_id' => $PROD_ID,
            'type_id' => $TYPE_ID
        ];
        $LOCKED_ITEMS = [$LOCKED_ITEM];
        /** === Setup Mocks === */
        $mProceed = function () {
        };
        // $stockId = $this->_manStock->getCurrentStockId();
        $this->mManStock
            ->shouldReceive('getCurrentStockId')->once()
            ->andReturn($STOCK_ID);
        // $def = $this->_manTrans->begin();
        $mDef = $this->_mockTransactionDefinition();
        $this->mManTrans
            ->shouldReceive('begin')->once()
            ->andReturn($mDef);
        // $lockedItems = $this->_resourceStock->lockProductsStock(array_keys($items), $stockId);
        $this->mResourceStock
            ->shouldReceive('lockProductsStock')->once()
            ->andReturn($LOCKED_ITEMS);
        // $stockItem = $this->_providerStockRegistry->getStockItem($productId, $stockId);
        $mStockItem = $this->_mock(\Magento\CatalogInventory\Api\Data\StockItemInterface::class);
        $this->mProviderStockRegistry
            ->shouldReceive('getStockItem')->once()
            ->with($PROD_ID, $STOCK_ID)
            ->andReturn($mStockItem);
        // $canSubtractQty = $stockItem->getItemId() && $this->_canSubtractQty($stockItem);
        $mStockItem->shouldReceive('getItemId')->once()
            ->andReturn($ITEM_ID);
        //
        // protected function _canSubtractQty(StockItemInterface $stockItem)
        // $result = $stockItem->getManageStock() && $this->_configStock->canSubtractQty();
        $mStockItem->shouldReceive('getManageStock')->once()
            ->andReturn(true);
        $this->mConfigStock
            ->shouldReceive('canSubtractQty')->once()
            ->andReturn(true);
        //
        // if (!$canSubtractQty || !$this->_configStock->isQty($lockedItemRecord['type_id'])) {
        $this->mConfigStock
            ->shouldReceive('isQty')->once()
            ->with($TYPE_ID)
            ->andReturn(true);
        // if (!$stockItem->hasAdminArea() && ...) {...}
        $mStockItem->shouldReceive('hasAdminArea')->once()
            ->andReturn(true);
        // if ($this->_canSubtractQty($stockItem)) {...}
        // protected function _canSubtractQty(...):
        // $result = $stockItem->getManageStock() && $this->_configStock->canSubtractQty();
        $mStockItem->shouldReceive('getManageStock')->once()->andReturn(true);
        $this->mConfigStock->shouldReceive('canSubtractQty')->once()->andReturn(true);
        //
        // $stockItem->setQty($stockItem->getQty() - $orderedQty);
        $mStockItem->shouldReceive('getQty')->once()->andReturn($QTY_STORED);
        $mStockItem->shouldReceive('setQty')->once()->with($QTY_STORED - $QTY_ORDERED);
        // if (!$this->_stockState->verifyStock($productId) || ...)
        $this->mStockState
            ->shouldReceive('verifyStock')->once()
            ->with($PROD_ID)
            ->andReturn(true);
        // if (... || $this->_stockState->verifyNotification($productId))
        $this->mStockState
            ->shouldReceive('verifyNotification')->once()
            ->andReturn(true);
        // END OF LOOPS
        // $this->_resourceStock->correctItemsQty($registeredItems, $stockId, '-');
        $this->mResourceStock
            ->shouldReceive('correctItemsQty')->once();
        // $this->_manTrans->commit($def);
        $this->mManTrans
            ->shouldReceive('commit')->once()
            ->with($mDef);
        /** === Call and asserts  === */
        $res = $this->obj->aroundRegisterProductsSale(
            $this->mSubject,
            $mProceed,
            $ITEMS,
            $WEBSITE_ID
        );
        $this->assertTrue(is_array($res));
    }

    public function test_constructor()
    {
        /** === Call and asserts  === */
        $this->assertInstanceOf(StockManagement::class, $this->obj);
    }

}