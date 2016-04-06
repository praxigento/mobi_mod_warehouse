<?php
/**
 * Empty class to get stub for tests
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Warehouse\Setup;



include_once(__DIR__ . '/../phpunit_bootstrap.php');

class InstallSchema_UnitTest extends \Praxigento\Core\Lib\Test\BaseMockeryCase {

    public static function tearDownAfterClass() {
        Context::reset();
    }

    public function test_constructor() {
        $obj = new InstallSchema();
        $this->assertInstanceOf(\Praxigento\Warehouse\Setup\InstallSchema::class, $obj);
    }

    public function test_install() {
        /** === Test Data === */
        /** === Mocks === */
        // parameters for install(...)
        $mockSetup = $this
            ->getMockBuilder('Magento\Framework\Setup\SchemaSetupInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $mockMageCtx = $this
            ->getMockBuilder('Magento\Framework\Setup\ModuleContextInterface')
            ->disableOriginalConstructor()
            ->getMock();

        // $setup->startSetup();
        $mockSetup
            ->expects($this->once())
            ->method('startSetup');
        // $obm = \Magento\Framework\App\ObjectManager::getInstance();
        $mockCtx = $this
            ->getMockBuilder('Praxigento\Core\Lib\Context')
            ->disableOriginalConstructor()
            ->getMock();
        $mockObm = $this
            ->getMockBuilder('Praxigento\Core\Lib\Context\IObjectManager')
            ->disableOriginalConstructor()
            ->getMock();
        $mockCtx
            ->expects($this->any())
            ->method('getObjectManager')
            ->willReturn($mockObm);
        // $moduleSchema = $obm->get($this->_classSchema);
        $mockCoreSchema = $this
            ->getMockBuilder('Praxigento\Warehouse\Lib\Setup\Schema')
            ->disableOriginalConstructor()
            ->getMock();
        $mockObm
            ->expects($this->once())
            ->method('get')
            ->with('Praxigento\Warehouse\Lib\Setup\Schema')
            ->willReturn($mockCoreSchema);
        // $moduleSchema->setup();
        $mockSetup
            ->expects($this->once())
            ->method('endSetup');
        // Setup mocks to MOBI context
        Context::set($mockCtx);
        /** === Test itself === */
        $obj = new InstallSchema();
        $obj->install($mockSetup, $mockMageCtx);
        $this->assertInstanceOf(\Praxigento\Warehouse\Setup\InstallSchema::class, $obj);
    }
}