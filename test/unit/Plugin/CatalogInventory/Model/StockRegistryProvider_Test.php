<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Warehouse\Plugin\CatalogInventory\Model;

include_once(__DIR__ . '/../../../phpunit_bootstrap.php');

class StockRegistryProvider_UnitTest
    extends \Praxigento\Core\Test\BaseCase\Mockery
{

    /** @var  \Mockery\MockInterface */
    protected $mFactoryStockItem;
    /** @var  \Mockery\MockInterface */
    protected $mFactoryStockItemCrit;
    /** @var  \Mockery\MockInterface */
    protected $mRepoStockItem;
    /** @var  \Mockery\MockInterface */
    protected $mStorageStockRegistry;
    /** @var  \Mockery\MockInterface */
    private $mSubject;
    /** @var  \Mockery\MockInterface */
    protected $mToolStockManager;
    /** @var  StockRegistryProvider */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        /** create mocks */
        $this->mSubject = $this->_mock(\Magento\CatalogInventory\Model\StockRegistryProvider::class);
        $this->mStorageStockRegistry = $this->_mock(\Magento\CatalogInventory\Model\StockRegistryStorage::class);
        $this->mFactoryStockItem = $this->_mock(\Magento\CatalogInventory\Api\Data\StockItemInterfaceFactory::class);
        $this->mFactoryStockItemCrit = $this->_mock(\Magento\CatalogInventory\Api\StockItemCriteriaInterfaceFactory::class);
        $this->mRepoStockItem = $this->_mock(\Magento\CatalogInventory\Api\StockItemRepositoryInterface::class);
        $this->mToolStockManager = $this->_mock(\Praxigento\Warehouse\Tool\IStockManager::class);
        /** create object to test */
        $this->obj = new StockRegistryProvider(
            $this->mStorageStockRegistry,
            $this->mFactoryStockItem,
            $this->mFactoryStockItemCrit,
            $this->mRepoStockItem,
            $this->mToolStockManager
        );
    }

    public function test_aroundGetStockItem_update()
    {
        /** === Test Data === */
        $PROD_ID = 4;
        $SCOPE_ID = 2;
        $STOCK_ID = 16;
        $ITEM_ID = 32;
        /** === Setup Mocks === */
        $mProceed = function () {
        };
        // $result = $this->_storageStockRegistry->getStockItem($productId, $scopeId);
        $this->mStorageStockRegistry
            ->shouldReceive('getStockItem')->once()
            ->with($PROD_ID, $SCOPE_ID)
            ->andReturn(null);
        // $criteria = $this->_factoryStockItemCrit->create();
        $mCriteria = $this->_mock(\Magento\CatalogInventory\Api\StockItemCriteriaInterface::class);
        $this->mFactoryStockItemCrit
            ->shouldReceive('create')->once()
            ->andReturn($mCriteria);
        // $criteria->setProductsFilter($productId);
        $mCriteria->shouldReceive('setProductsFilter')->once()
            ->with($PROD_ID);
        // $stockId = $this->_toolStockManager->getCurrentStockId();
        $this->mToolStockManager
            ->shouldReceive('getCurrentStockId')->once()
            ->andReturn($STOCK_ID);
        // $criteria->setStockFilter($stockId);
        $mCriteria->shouldReceive('setStockFilter')->once()
            ->with($STOCK_ID);
        // $collection = $this->_repoStockItem->getList($criteria);
        $mCollection = $this->_mock(\Magento\CatalogInventory\Api\Data\StockItemCollectionInterface::class);
        $this->mRepoStockItem
            ->shouldReceive('getList')->once()
            ->with($mCriteria)
            ->andReturn($mCollection);
        // $result = current($collection->getItems());
        $mResult = $this->_mock(\Magento\CatalogInventory\Api\Data\StockItemInterface::class);
        $mCollection->shouldReceive('getItems')->once()
            ->andReturn([$mResult]);
        // if ($result && $result->getItemId()) {...}
        $mResult->shouldReceive('getItemId')->once()
            ->andReturn($ITEM_ID);
        // $this->_storageStockRegistry->setStockItem($productId, $scopeId, $result);
        $this->mStorageStockRegistry
            ->shouldReceive('setStockItem')->once()
            ->with($PROD_ID, $SCOPE_ID, $mResult);
        /** === Call and asserts  === */
        $res = $this->obj->aroundGetStockItem(
            $this->mSubject,
            $mProceed,
            $PROD_ID,
            $SCOPE_ID
        );
        $this->assertEquals($mResult, $res);
    }

    public function test_aroundGetStockItem_create()
    {
        /** === Test Data === */
        $PROD_ID = 4;
        $SCOPE_ID = 2;
        $STOCK_ID = 16;
        $ITEM_ID = 32;
        /** === Setup Mocks === */
        $mProceed = function () {
        };
        // $result = $this->_storageStockRegistry->getStockItem($productId, $scopeId);
        $this->mStorageStockRegistry
            ->shouldReceive('getStockItem')->once()
            ->with($PROD_ID, $SCOPE_ID)
            ->andReturn(null);
        // $criteria = $this->_factoryStockItemCrit->create();
        $mCriteria = $this->_mock(\Magento\CatalogInventory\Api\StockItemCriteriaInterface::class);
        $this->mFactoryStockItemCrit
            ->shouldReceive('create')->once()
            ->andReturn($mCriteria);
        // $criteria->setProductsFilter($productId);
        $mCriteria->shouldReceive('setProductsFilter')->once()
            ->with($PROD_ID);
        // $stockId = $this->_toolStockManager->getCurrentStockId();
        $this->mToolStockManager
            ->shouldReceive('getCurrentStockId')->once()
            ->andReturn($STOCK_ID);
        // $criteria->setStockFilter($stockId);
        $mCriteria->shouldReceive('setStockFilter')->once()
            ->with($STOCK_ID);
        // $collection = $this->_repoStockItem->getList($criteria);
        $mCollection = $this->_mock(\Magento\CatalogInventory\Api\Data\StockItemCollectionInterface::class);
        $this->mRepoStockItem
            ->shouldReceive('getList')->once()
            ->with($mCriteria)
            ->andReturn($mCollection);
        // $result = current($collection->getItems());
        $mCollection->shouldReceive('getItems')->once()
            ->andReturn([]);
        // $result = $this->_factoryStockItem->create();
        $mResult = $this->_mock(\Magento\CatalogInventory\Api\Data\StockItemInterface::class);
        $this->mFactoryStockItem
            ->shouldReceive('create')->once()
            ->andReturn($mResult);
        /** === Call and asserts  === */
        $res = $this->obj->aroundGetStockItem(
            $this->mSubject,
            $mProceed,
            $PROD_ID,
            $SCOPE_ID
        );
        $this->assertEquals($mResult, $res);
    }

    public function test_constructor()
    {
        /** === Call and asserts  === */
        $this->assertInstanceOf(StockRegistryProvider::class, $this->obj);
    }
}