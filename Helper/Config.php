<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Helper;

/**
 * Helper to get configuration parameters related to the module.
 *
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 */
class Config
{

    /** @var \Magento\Framework\App\Config\ScopeConfigInterface */
    protected $scopeConfig;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @return string
     */
    public function getSalesGeneralDeleteCancelled()
    {
        $result = $this->scopeConfig->getValue('praxigento_sales/general/delete_cancelled');
        $result = filter_var($result, FILTER_VALIDATE_BOOLEAN);
        return $result;
    }

}