<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Warehouse\Controller\Adminhtml;

abstract class Base
    extends \Magento\Backend\App\Action
{
    /** @var  string */
    protected $_aclResource;
    /** @var  string */
    protected $_activeMenu;
    /** @var  string */
    protected $_breadcrumbLabel;
    /** @var  string */
    protected $_breadcrumbTitle;
    /** @var  string */
    protected $_pageTitle;
    /** @var \Magento\Framework\View\Result\PageFactory */
    protected $_resultPageFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        $aclResource,
        $activeMenu,
        $breadcrumbLabel,
        $breadcrumbTitle,
        $pageTitle
    ) {
        parent::__construct($context);
        $this->_resultPageFactory = $resultPageFactory;
        $this->_aclResource = $aclResource;
        $this->_activeMenu = $activeMenu;
        $this->_breadcrumbLabel = $breadcrumbLabel;
        $this->_breadcrumbTitle = $breadcrumbTitle;
        $this->_pageTitle = $pageTitle;
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        $result = $this->_authorization->isAllowed($this->_aclResource);
        return $result;
    }

    /** @inheritdoc */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu($this->_activeMenu);
        $this->_addBreadcrumb(__($this->_breadcrumbLabel), __($this->_breadcrumbTitle));
        $resultPage->getConfig()->getTitle()->prepend(__($this->_pageTitle));
        return $resultPage;
    }
}