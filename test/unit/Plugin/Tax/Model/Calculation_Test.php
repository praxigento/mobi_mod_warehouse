<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Warehouse\Plugin\Tax\Model;

include_once(__DIR__ . '/../../../phpunit_bootstrap.php');

class Calculation_UnitTest
    extends \Praxigento\Core\Test\BaseCase\Mockery
{
    /** @var  \Mockery\MockInterface */
    protected $mManStock;
    /** @var  \Mockery\MockInterface */
    protected $mRepoWrhs;
    /** @var  \Mockery\MockInterface */
    private $mSubject;
    /** @var  Calculation */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        /** create mocks */
        $this->mSubject = $this->_mock(\Magento\Tax\Model\Calculation::class);
        $this->mManStock = $this->_mock(\Praxigento\Warehouse\Tool\IStockManager::class);
        $this->mRepoWrhs = $this->_mock(\Praxigento\Warehouse\Repo\Entity\Def\Warehouse::class);
        /** create object to test */
        $this->obj = new Calculation(
            $this->mManStock,
            $this->mRepoWrhs
        );
    }

    public function test_afterGetRateRequest()
    {
        /** === Test Data === */
        $STORE_ID = 2;
        $STOCK_ID = 4;
        $COUNTRY_CODE = 'LV';
        $RESULT = $this->_mock(\Magento\Framework\DataObject::class);
        /** === Setup Mocks === */
        // $storeId = $result->getStore();
        $RESULT->shouldReceive('getStore')->once()
            ->andReturn($STORE_ID);
        // $stockId = $this->_manStock->getStockIdByStoreId($storeId);
        $this->mManStock
            ->shouldReceive('getStockIdByStoreId')->once()
            ->andReturn($STOCK_ID);
        // $wrhs = $this->_repoWrhs->getById($stockId);
        $mWrhs = $this->_mock(\Praxigento\Warehouse\Data\Entity\Warehouse::class);
        $this->mRepoWrhs
            ->shouldReceive('getById')->once()
            ->andReturn($mWrhs);
        // $countryCode = $wrhs->getCountryCode();
        $mWrhs->shouldReceive('getCountryCode')->once()
            ->andReturn($COUNTRY_CODE);
        // $result->setCountryId($countryCode);
        $RESULT->shouldReceive('setCountryId')->once()
            ->with($COUNTRY_CODE);
        /** === Call and asserts  === */
        $res = $this->obj->afterGetRateRequest(
            $this->mSubject,
            $RESULT
        );
        $this->assertEquals($RESULT, $res);
    }

    public function test_constructor()
    {
        /** === Call and asserts  === */
        $this->assertInstanceOf(Calculation::class, $this->obj);
    }
}