<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Warehouse\Repo\Entity\Group\Def;

include_once(__DIR__ . '/../../../../phpunit_bootstrap.php');

class Price_UnitTest
    extends \Praxigento\Core\Test\BaseCase\Repo\Entity
{
    /** @var  Price */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        /** create object to test */
        $this->obj = new Price(
            $this->mResource,
            $this->mRepoGeneric
        );
    }

    public function test_constructor()
    {
        /** === Call and asserts  === */
        $this->assertInstanceOf(Price::class, $this->obj);
    }

}