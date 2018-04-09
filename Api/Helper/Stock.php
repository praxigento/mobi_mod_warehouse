<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Api\Helper;

/**
 * Resolve current stock ID (MOBI-311). These helper should be implemented in concrete project.
 */
interface  Stock
{
    /**
     * Convert $amount according to current $payment details (store currency to payment currency).
     *
     * @param \Magento\Payment\Model\InfoInterface $payment
     * @param float $amount
     * @return float
     */
    public function convertPaymentAmount($payment, $amount);

    /**
     * @return int
     */
    public function getCurrentStockId();

    /**
     * ID of the default stock to process regular prices in adminhtml (one stock only in the grid).
     * @return int
     */
    public function getDefaultStockId();

    /**
     * Currency code for store view.
     *
     * @param $storeId
     * @return string
     */
    public function getStockCurrencyByStoreId($storeId);

    /**
     * @param int $storeId
     * @return int
     */
    public function getStockIdByStoreId($storeId);
}