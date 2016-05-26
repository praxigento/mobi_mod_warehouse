<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Warehouse\Repo\Entity\Quantity\Def;

include_once(__DIR__ . '/../../../../phpunit_bootstrap.php');

class Item_UnitTest extends \Praxigento\Core\Test\BaseRepoEntityCase
{
    /** @var  Sale */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        /** create object to test */
        $this->obj = new Sale(
            $this->mResource,
            $this->mRepoGeneric
        );
    }

    public function test_constructor()
    {
        /** === Call and asserts  === */
        $this->assertInstanceOf(Sale::class, $this->obj);
    }

}