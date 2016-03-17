<?php
/**
 * Empty class to get stub for tests
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Warehouse\Setup;

use Praxigento\Core\Lib\Context;

include_once(__DIR__ . '/../phpunit_bootstrap.php');

class InstallData_UnitTest extends \Praxigento\Core\Lib\Test\BaseTestCase {

    public function test_constructor() {
        $obj = new InstallData();
        $this->assertInstanceOf(\Praxigento\Warehouse\Setup\InstallData::class, $obj);
    }

    public function test_smth() {
        $obj = new \Praxigento\Warehouse\Api\Data\Def\Warehouse();
        $obj->setData('asd');
    }
}