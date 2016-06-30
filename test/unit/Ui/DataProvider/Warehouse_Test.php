<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Warehouse\Ui\DataProvider;

include_once(__DIR__ . '/../../phpunit_bootstrap.php');

class Warehouse_UnitTest extends \Praxigento\Core\Test\BaseMockeryCase
{
    /** @var  \Mockery\MockInterface */
    private $mCriteriaAdapter;
    /** @var  \Mockery\MockInterface */
    private $mFilterBuilder;
    /** @var  \Mockery\MockInterface */
    private $mRepo;
    /** @var  \Mockery\MockInterface */
    private $mReporting;
    /** @var  \Mockery\MockInterface */
    private $mRequest;
    /** @var  \Mockery\MockInterface */
    private $mSearchCriteriaBuilder;
    /** @var  \Mockery\MockInterface */
    private $mUrl;
    /** @var  Warehouse */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        /** create mocks */
        $this->mUrl = $this->_mock(\Magento\Framework\UrlInterface::class);
        $this->mCriteriaAdapter = $this->_mock(\Praxigento\Core\Repo\Query\Criteria\IAdapter::class);
        $this->mRepo = $this->_mock(\Praxigento\Warehouse\Repo\Agg\IWarehouse::class);
        $this->mReporting = $this->_mock(\Magento\Framework\View\Element\UiComponent\DataProvider\Reporting::class);
        $this->mSearchCriteriaBuilder = $this->_mock(\Magento\Framework\Api\Search\SearchCriteriaBuilder::class);
        $this->mRequest = $this->_mock(\Magento\Framework\App\RequestInterface::class);
        $this->mFilterBuilder = $this->_mock(\Magento\Framework\Api\FilterBuilder::class);
        /** setup mocks for constructor */
        $this->mUrl
            ->shouldReceive('getRouteUrl')->once();
        /** create object to test */
        $this->obj = new Warehouse(
            $this->mUrl,
            $this->mCriteriaAdapter,
            $this->mRepo,
            $this->mReporting,
            $this->mSearchCriteriaBuilder,
            $this->mRequest,
            $this->mFilterBuilder,
            'name'
        );
    }


    public function test_constructor()
    {
        /** === Call and asserts  === */
        $this->assertInstanceOf(Warehouse::class, $this->obj);
    }

}