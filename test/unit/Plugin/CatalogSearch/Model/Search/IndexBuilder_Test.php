<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Warehouse\Plugin\CatalogSearch\Model\Search;

use Praxigento\Warehouse\Config as Cfg;

include_once(__DIR__ . '/../../../../phpunit_bootstrap.php');

class IndexBuilder_UnitTest
    extends \Praxigento\Core\Test\BaseCase\Mockery
{
    /** @var  \Mockery\MockInterface */
    protected $mManStock;
    /** @var  \Mockery\MockInterface */
    private $mSubject;
    /** @var  IndexBuilder */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        /** create mocks */
        $this->mSubject = $this->_mock(\Magento\CatalogSearch\Model\Search\IndexBuilder::class);
        $this->mManStock = $this->_mock(\Praxigento\Warehouse\Tool\IStockManager::class);
        /** create object to test */
        $this->obj = new IndexBuilder(
            $this->mManStock
        );
    }

    public function test_aroundBuild()
    {
        /** === Test Data === */
        $STORE_ID = 1;
        $STOCK_ID = 2;
        $REQUEST = $this->_mock(\Magento\Framework\Search\RequestInterface::class);
        $RESULT = $this->_mock(\Magento\Framework\DB\Select::class);
        /** === Setup Mocks === */
        $mProceed = function ($dataIn) use ($REQUEST, $RESULT) {
            $this->assertEquals($REQUEST, $dataIn);
            return $RESULT;
        };
        // $from = $result->getPart(\Magento\Framework\DB\Select::FROM);
        $mFrom = [
            IndexBuilder::AS_STOCK_INDEX => []
        ];
        $RESULT->shouldReceive('getPart')->once()
            ->with(\Magento\Framework\DB\Select::FROM)
            ->andReturn($mFrom);
        // $dimensions = $request->getDimensions();
        $mDimensions = $this->_mock(\Magento\Framework\Search\Request\Dimension::class);
        $REQUEST->shouldReceive('getDimensions')->once()
            ->andReturn([$mDimensions]);
        // $storeId = $dimension->getValue();
        $mDimensions->shouldReceive('getValue')->once()
            ->andReturn($STORE_ID);
        // $stockId = (int)$this->_manStock->getStockIdByStoreId($storeId);
        $this->mManStock
            ->shouldReceive('getStockIdByStoreId')->once()
            ->with($STORE_ID)
            ->andReturn($STOCK_ID);
        // $result->where($byStockId);
        $mWhere = IndexBuilder::AS_STOCK_INDEX . '.' . Cfg::E_CATINV_STOCK_STATUS_A_STOCK_ID . '=' . $STOCK_ID;
        $RESULT->shouldReceive('where')->once()
            ->with($mWhere);
        /** === Call and asserts  === */
        $res = $this->obj->aroundBuild(
            $this->mSubject,
            $mProceed,
            $REQUEST
        );
        $this->assertEquals($RESULT, $res);
    }

    public function test_constructor()
    {
        /** === Call and asserts  === */
        $this->assertInstanceOf(IndexBuilder::class, $this->obj);
    }
}