<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2020
 */

namespace Praxigento\Warehouse\Model\Config\Source\Warehouse;

class Currency
    implements \Magento\Framework\Data\OptionSourceInterface
{
    /** @var array */
    private $cache;
    /** @var \Praxigento\Warehouse\Repo\Dao\Warehouse */
    private $daoWrhs;

    public function __construct(
        \Praxigento\Warehouse\Repo\Dao\Warehouse $daoWrhs
    ) {
        $this->daoWrhs = $daoWrhs;
    }

    public function toOptionArray()
    {
        if (is_null($this->cache)) {
            $all = $this->daoWrhs->get();
            $this->cache = [];
            $processed = [];
            /** @var \Praxigento\Warehouse\Repo\Data\Warehouse $one */
            foreach ($all as $one) {
                $currency = $one->getCurrency();
                if (!\in_array($currency, $processed)) {
                    $processed[] = $currency;
                    $this->cache[] = ['value' => $currency, 'label' => $currency];
                }
            }
            usort($this->cache, function ($a, $b) {
                return $a['label'] > $b['label'];
            });
        }
        return $this->cache;
    }
}
