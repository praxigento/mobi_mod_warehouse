<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Warehouse\Plugin\Magento\Payment\Model\Method;

/**
 * Convert payment amounts from store currency into payment currency.
 */
class Adapter
{
    /** @var \Praxigento\Warehouse\Api\Helper\Stock */
    private $hlpStock;

    public function __construct(
        \Praxigento\Warehouse\Api\Helper\Stock $hlpStock
    ) {
        $this->hlpStock = $hlpStock;
    }


    public function beforeAuthorize(
        \Magento\Payment\Model\Method\Adapter $subject,
        \Magento\Payment\Model\InfoInterface $payment,
        $amount
    ) {
        $converted = $this->hlpStock->convertPaymentAmount($payment, $amount);
        return [$payment, $converted];
    }

    public function beforeCapture(
        \Magento\Payment\Model\Method\Adapter $subject,
        \Magento\Payment\Model\InfoInterface $payment,
        $amount
    ) {
        $converted = $this->hlpStock->convertPaymentAmount($payment, $amount);
        return [$payment, $converted];
    }

    public function beforeOrder(
        \Magento\Payment\Model\Method\Adapter $subject,
        \Magento\Payment\Model\InfoInterface $payment,
        $amount
    ) {
        $converted = $this->hlpStock->convertPaymentAmount($payment, $amount);
        return [$payment, $converted];
    }

    public function beforeRefund(
        \Magento\Payment\Model\Method\Adapter $subject,
        \Magento\Payment\Model\InfoInterface $payment,
        $amount
    ) {
        $converted = $this->hlpStock->convertPaymentAmount($payment, $amount);
        return [$payment, $converted];
    }

}