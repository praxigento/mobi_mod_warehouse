<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Block\Adminhtml\Catalog\Warehouse;


class Grid extends \Magento\Backend\Block\Widget\Grid\Container
{
    protected function _construct()
    {
        $this->_blockGroup = 'Advanced_Affiliate';
        $this->_controller = 'adminhtml_affiliate';
        $this->_headerText = __('Affiliate');
        $this->_addButtonLabel = __('Add New Affiliate');
        parent::_construct();
        $this->buttonList->add(
            'affiliate_apply',
            [
                'label' => __('Affiliate'),
                'onclick' => "location.href='" . $this->getUrl('affiliate/*/applyAffiliate') . "'",
                'class' => 'apply'
            ]
        );
    }
}