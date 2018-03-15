<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Plugin\Sales\Block\Adminhtml\Order\Create\Search\Grid\Renderer;

use Magento\Catalog\Api\Data\ProductAttributeInterface as EProdAttr;
use Praxigento\Warehouse\Api\Data\Catalog\Product as AWrhsProd;

/**
 * Replace product price by warehouse group price before rendering on new order creation in adminhtml.
 */
class Product
{
    /** @var \Magento\Backend\Model\Session\Quote */
    private $session;
    /** @var \Praxigento\Warehouse\Helper\PriceLoader */
    private $hlpPriceLoader;

    public function __construct(
        \Magento\Backend\Model\Session\Quote $session,
        \Praxigento\Warehouse\Helper\PriceLoader $hlpPriceLoader
    ) {
        $this->session = $session;
        $this->hlpPriceLoader = $hlpPriceLoader;
    }

    public function beforeRender(
        \Magento\Sales\Block\Adminhtml\Order\Create\Search\Grid\Renderer\Product $subject,
        \Magento\Framework\DataObject $row
    )
    {
        assert($row instanceof \Magento\Catalog\Model\Product);
        /* replace regular price & special price with warehouse price & warehouse group price */
        $prodId = $row->getId();
        $storeId = $row->getStoreId();
        $quote = $this->session->getQuote();
        $groupId = $quote->getCustomerGroupId();
        list($priceWrhs, $priceWrhsGroup) = $this->hlpPriceLoader->load($prodId, $storeId, $groupId);
        $row->setData(AWrhsProd::A_PRICE_WRHS, $priceWrhs);
        $row->setData(AWrhsProd::A_PRICE_WRHS_GROUP, $priceWrhsGroup);
        /* use warehouse price instead of regular price */
        if ($priceWrhs > 0) {
            $row->setData(EProdAttr::CODE_PRICE, $priceWrhs);
        }
        /* use warehouse group price as special price */
        if ($priceWrhsGroup > 0) {
            $row->setData(EProdAttr::CODE_SPECIAL_PRICE, $priceWrhsGroup);
        }
        $result = [$row];
        return $result;
    }
}