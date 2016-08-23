<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Controller\Adminhtml\Lots;

include_once(__DIR__ . '/../../../phpunit_bootstrap.php');

class Index_UnitTest extends \Praxigento\Core\Test\BaseCase\Controller
{

    /** @var  \Mockery\MockInterface */
    protected $mResultPageFactory;
    /** @var  Index */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        /** create mocks */
        $this->mResultPageFactory = $this->_mock(\Magento\Framework\View\Result\PageFactory::class);
        /** create object to test */
        $this->obj = new Index(
            $this->mContext,
            $this->mResultPageFactory
        );
    }


    public function test_constructor()
    {
        /** === Call and asserts  === */
        $this->assertInstanceOf(Index::class, $this->obj);
    }

}