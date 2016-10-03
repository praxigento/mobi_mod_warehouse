<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Warehouse\Ui\DataProvider;

include_once(__DIR__ . '/../../phpunit_bootstrap.php');

/**
 * @SuppressWarnings(PHPMD.CamelCaseClassName)
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 */
class Lot_UnitTest
    extends \Praxigento\Core\Test\BaseCase\Ui\DataProvider
{
    /** @var  \Mockery\MockInterface */
    private $mRepo;
    /** @var  Lot */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        /** create mocks */
        $this->mRepo = $this->_mock(\Praxigento\Warehouse\Repo\Entity\ILot::class);
        /** create object to test */
        $this->obj = new Lot(
            $this->mUrl,
            $this->mCritAdapter,
            $this->mRepo,
            $this->mReporting,
            $this->mSearchCritBuilder,
            $this->mRequest,
            $this->mFilterBuilder,
            'name'
        );
    }

    public function test_constructor()
    {
        /** === Call and asserts  === */
        $this->assertInstanceOf(Lot::class, $this->obj);
    }

}