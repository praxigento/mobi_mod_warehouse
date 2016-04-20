<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Warehouse\Repo\Entity\Def;

include_once(__DIR__ . '/../../../phpunit_bootstrap.php');

class Warehouse_UnitTest extends \Praxigento\Core\Test\BaseRepoEntityCase
{
    /** @var  Warehouse */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        /* create object to test */
        $this->obj = new Warehouse(
            $this->mResource,
            $this->mRepoGeneric
        );
    }

    public function test_constructor()
    {
        /* === Call and asserts  === */
        $this->assertInstanceOf(Warehouse::class, $this->obj);
    }

}