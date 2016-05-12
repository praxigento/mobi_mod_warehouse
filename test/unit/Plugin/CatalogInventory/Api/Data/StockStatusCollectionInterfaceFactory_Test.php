<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Warehouse\Plugin\CatalogInventory\Api\Data;

include_once(__DIR__ . '/../../../../phpunit_bootstrap.php');

class StockStatusCollectionInterfaceFactory_UnitTest extends \Praxigento\Core\Test\BaseMockeryCase
{
    /** @var  StockStatusCollectionInterfaceFactory */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        /** create object to test */
        $this->obj = new StockStatusCollectionInterfaceFactory();
    }

    public function test_beforeCreate()
    {
        /** === Test Data === */
        /** === Setup Mocks === */
        $mQuery = $this->_mock(\Magento\Framework\Db\Query::class);
        $mSelect = $this->_mock(\Magento\Framework\Db\Select::class);
        // $select = $query->getSelectSql();
        $mQuery->shouldReceive('getSelectSql')->once()
            ->andReturn($mSelect);
        // $select->columns(...);
        $mSelect->shouldReceive('columns', 'group');
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