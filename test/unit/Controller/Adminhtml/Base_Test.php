<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Controller\Adminhtml;

include_once(__DIR__ . '/../../phpunit_bootstrap.php');

class Base_UnitTest extends \Praxigento\Core\Test\BaseControllerCase
{

    const ACL_RESOURCE = 'acl_resource';
    const ACTIVE_MENU = 'active_menu';
    const BREADCRUMB_LABEL = 'bc label';
    const BREADCRUMB_TITLE = 'bc title';
    const PAGE_TITLE = 'page title';

    /** @var  \Mockery\MockInterface */
    protected $mResultPageFactory;
    /** @var  TestedBase */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        /** create mocks */
        $this->mResultPageFactory = $this->_mock(\Magento\Framework\View\Result\PageFactory::class);
        /** create object to test */
        $this->obj = new TestedBase(
            $this->mContext,
            $this->mResultPageFactory,
            self::ACL_RESOURCE,
            self::ACTIVE_MENU,
            self::BREADCRUMB_LABEL,
            self::BREADCRUMB_TITLE,
            self::PAGE_TITLE
        );
    }


    public function test_constructor()
    {
        /** === Call and asserts  === */
        $this->assertInstanceOf(Base::class, $this->obj);
    }

    public function test_execute()
    {
        /** === Test Data === */
        $IS_ALLOWED = true;
        /** === Setup Mocks === */
        // $resultPage = $this->_resultPageFactory->create();
        $mResultPage = $this->_mock(\Magento\Backend\Model\View\Result\Page::class);
        $this->mResultPageFactory
            ->shouldReceive('create')->once()
            ->andReturn($mResultPage);
        // $resultPage->setActiveMenu($this->_activeMenu);
        $mResultPage->shouldReceive('setActiveMenu')->once();
        // $resultPage->getConfig()->getTitle()->prepend(__($this->_pageTitle));
        $mTitle = $this->_mock(\Magento\Framework\View\Page\Title::class);
        $mConfig = $this->_mock(\Magento\Framework\View\Page\Config::class);
        $mResultPage->shouldReceive('getConfig')->once()
            ->andReturn($mConfig);
        $mConfig->shouldReceive('getTitle')->once()
            ->andReturn($mTitle);
        $mTitle->shouldReceive('prepend')->once();
        /** === Call and asserts  === */
        $this->obj->execute();
    }

    public function test_isAllowed()
    {
        /** === Test Data === */
        $IS_ALLOWED = true;
        /** === Setup Mocks === */
        $this->mCtxAuthorization
            ->shouldReceive('isAllowed')->once()
            ->with(self::ACL_RESOURCE)
            ->andReturn($IS_ALLOWED);
        /** === Call and asserts  === */
        $this->obj->isAllowed();
    }

}

class TestedBase extends Base
{
    protected function _addBreadcrumb($label, $title, $link = null)
    {
        // stub for protected method
    }

    public function isAllowed()
    {
        return parent::_isAllowed();
    }

}