<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Warehouse\Service\Customer\Sub;

use Praxigento\Warehouse\Data\Entity\Warehouse;

include_once(__DIR__ . '/../../../phpunit_bootstrap.php');

class Repo_UnitTest extends \Praxigento\Core\Test\BaseCase\Mockery
{

    /** @var  \Mockery\MockInterface */
    private $mRepoWrhs;
    /** @var  Repo */
    private $obj;

    public function setUp()
    {
        parent::setUp();
        /** create mocks */
        $this->mRepoWrhs = $this->_mock(\Praxigento\Warehouse\Repo\Entity\IWarehouse::class);
        /** create object to test */
        $this->obj = new Repo(
            $this->mRepoWrhs
        );
    }

    public function test_constructor()
    {
        /** === Call and asserts  === */
        $this->assertInstanceOf(Repo::class, $this->obj);
    }

    public function test_getStockId()
    {
        /** === Test Data === */
        $STOCK_ID = 54;
        $WRHS = [
            [Warehouse::ATTR_STOCK_REF => $STOCK_ID]
        ];
        /** === Setup Mocks === */
        // $all = $this->_repoWrhs->get();
        $this->mRepoWrhs
            ->shouldReceive('get')->once()
            ->andReturn($WRHS);
        /** === Call and asserts  === */
        $this->obj->getStockId();
    }

}