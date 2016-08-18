<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Warehouse\Plugin\CatalogInventory\Model\ResourceModel\Stock;


class Status_UnitTest
    extends \Praxigento\Core\Test\BaseMockeryCase
{
    /** @var \Mockery\MockInterface */
    private $mManStock;
    /** @var \Mockery\MockInterface */
    private $mSubject;
    /** @var Status */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        /** create mocks */
        $this->mSubject = $this->_mock(\Magento\CatalogInventory\Model\ResourceModel\Stock\Status::class);
        $this->mManStock = $this->_mock(\Praxigento\Warehouse\Tool\IStockManager::class);
        /** create object to test */
        $this->obj = new Status(
            $this->mManStock
        );
    }

    public function test_afterAddStockDataToCollection()
    {
        /** === Test Data === */
        $COND = 'condition .stock_id = ';
        $STOCK_ID = 32;
        $RESULT = $this->_mock(\Magento\Catalog\Model\ResourceModel\Product\Collection::class);
        /** === Setup Mocks === */
        // $select = $result->getSelect();
        $mSelect = $this->_mockDbSelect();
        $RESULT->shouldReceive('getSelect')->once()
            ->andReturn($mSelect);
        // $from = $select->getPart('from');
        $mFrom = ['stock_status_index' => ['joinCondition' => $COND . '1']];
        $mSelect->shouldReceive('getPart')->once()
            ->with('from')
            ->andReturn($mFrom);
        // $stockId = $this->_manStock->getCurrentStockId();
        $this->mManStock
            ->shouldReceive('getCurrentStockId')->once()
            ->andReturn($STOCK_ID);
        // $select->setPart('from', $from);
        $mFromFixed = ['stock_status_index' => ['joinCondition' => $COND . $STOCK_ID]];
        $mSelect->shouldReceive('setPart')->once()
            ->with('from', $mFromFixed);
        /** === Call and asserts  === */
        $res = $this->obj->afterAddStockDataToCollection(
            $this->mSubject,
            $RESULT
        );
        $this->assertEquals($RESULT, $res);
    }

    public function test_constructor()
    {
        $this->assertInstanceOf(Status::class, $this->obj);
    }
}