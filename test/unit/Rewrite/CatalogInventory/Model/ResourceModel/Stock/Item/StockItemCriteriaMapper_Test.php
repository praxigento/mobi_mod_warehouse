<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Warehouse\Repo\Agg\Def;

namespace Praxigento\Warehouse\Rewrite\CatalogInventory\Model\ResourceModel\Stock\Item;

include_once(__DIR__ . '/../../../../../../phpunit_bootstrap.php');

class StockItemCriteriaMapper_UnitTest extends \Praxigento\Core\Test\BaseMockeryCase
{
    /** @var  \Mockery\MockInterface */
    private $mLogger;
    /** @var  \Mockery\MockInterface */
    private $mFetchStrategy;
    /** @var  \Mockery\MockInterface */
    private $mObjectFactory;
    /** @var  \Mockery\MockInterface */
    private $mMapperFactory;
    /** @var  \Mockery\MockInterface */
    private $mStoreManager;
    /** @var  \Mockery\MockInterface */
    private $mSelect;

    /** @var  StockItemCriteriaMapper */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        /** create mocks */
        $this->mLogger = $this->_mockLogger();
        $this->mFetchStrategy = $this->_mock(\Magento\Framework\Data\Collection\Db\FetchStrategyInterface::class);
        $this->mObjectFactory = $this->_mock(\Magento\Framework\Data\ObjectFactory::class);
        $this->mMapperFactory = $this->_mock(\Magento\Framework\DB\MapperFactory::class);
        $this->mStoreManager = $this->_mock(\Magento\Store\Model\StoreManagerInterface::class);
        $this->mSelect = $this->_mock(\Magento\Framework\DB\Select::class);
        /** create object to test */
        $this->obj = new StockItemCriteriaMapper(
            $this->mLogger,
            $this->mFetchStrategy,
            $this->mObjectFactory,
            $this->mMapperFactory,
            $this->mStoreManager,
            $this->mSelect
        );
    }

    public function test_constructor()
    {
        /** === Call and asserts  === */
        $this->assertInstanceOf(StockItemCriteriaMapper::class, $this->obj);
    }

}