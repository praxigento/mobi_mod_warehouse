<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Warehouse\Plugin\CatalogInventory\Api\Data;

include_once(__DIR__ . '/../../../../phpunit_bootstrap.php');

class StockStatusCollectionInterfaceFactory_UnitTest extends \Praxigento\Core\Test\BaseCase\Mockery
{
    /** @var  StockStatusCollectionInterfaceFactory */
    private $obj;
    /** @var  \Mockery\MockInterface */
    private $mToolStockMan;
    /** @var  \Mockery\MockInterface */
    private $mResource;

    protected function setUp()
    {
        parent::setUp();
        /** create mocks */
        $this->mResource = $this->_mockResourceConnection();
        $this->mToolStockMan = $this->_mock(\Praxigento\Warehouse\Tool\IStockManager::class);
        /** create object to test */
        $this->obj = new StockStatusCollectionInterfaceFactory(
            $this->mResource,
            $this->mToolStockMan
        );
    }

    public function test_beforeCreate()
    {
        /** === Test Data === */
        $STOCK_ID = 12;
        /** === Setup Mocks === */
        $mQuery = $this->_mock(\Magento\Framework\Db\Query::class);
        $mSelect = $this->_mockDbSelect(['columns', 'joinLeft', 'where', 'group']);
        // $select = $query->getSelectSql();
        $mQuery->shouldReceive('getSelectSql')->once()
            ->andReturn($mSelect);
        // $stockId = (int)$this->_toolStockManager->getCurrentStockId();
        $this->mToolStockMan
            ->shouldReceive('getCurrentStockId')->once()
            ->andReturn($STOCK_ID);
        /** === Call and asserts  === */
        $data = ['query' => $mQuery];
        $res = $this->obj->beforeCreate(null, $data);

    }

    public function test_constructor()
    {
        /** === Call and asserts  === */
        $this->assertInstanceOf(StockStatusCollectionInterfaceFactory::class, $this->obj);
    }

}