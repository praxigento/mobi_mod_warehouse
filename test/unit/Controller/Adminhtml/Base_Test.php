<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Controller\Adminhtml;

include_once(__DIR__ . '/../../phpunit_bootstrap.php');

/**
 * @SuppressWarnings(PHPMD.CamelCaseClassName)
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 */
class Base_UnitTest
    extends \Praxigento\Core\Test\BaseCase\Controller
{

    const ACL_RESOURCE = 'acl_resource';
    const ACTIVE_MENU = 'active_menu';
    const BREADCRUMB_LABEL = 'bc label';
    const BREADCRUMB_TITLE = 'bc title';
    const PAGE_TITLE = 'page title';

    /** @var  TestedBase */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        /** create object to test */
        $this->obj = new TestedBase(
            $this->mContext,
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
        /** === Setup Mocks === */
        // $resultPage = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_PAGE);
        $mResultPage = $this->_mock(\Magento\Backend\Model\View\Result\Page::class);
        $this->mCtxResultFactory
            ->shouldReceive('create')->once()
            ->with(\Magento\Framework\Controller\ResultFactory::TYPE_PAGE)
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
        $mIsAllowed = true;
        /** === Setup Mocks === */
        $this->mCtxAuthorization
            ->shouldReceive('isAllowed')->once()
            ->with(\Magento\Backend\App\AbstractAction::ADMIN_RESOURCE)
            ->andReturn(true);
        $this->mCtxAuthorization
            ->shouldReceive('isAllowed')->once()
            ->with(self::ACL_RESOURCE)
            ->andReturn($mIsAllowed);
        /** === Call and asserts  === */
        $this->obj->isAllowed();
    }

}

/**
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
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