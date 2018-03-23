<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Repo\Data\Group;

class Price
    extends \Praxigento\Core\App\Repo\Data\Entity\Base
{
    const A_CUST_GROUP_REF = 'cust_group_ref';
    const A_PRICE = 'price';
    const A_STOCK_ITEM_REF = 'stock_item_ref';
    const ENTITY_NAME = 'prxgt_wrhs_group_price';

    /**
     * @return int
     */
    public function getCustomerGroupRef()
    {
        $result = parent::get(self::A_CUST_GROUP_REF);
        return $result;
    }

    /**
     * @return double
     */
    public function getPrice()
    {
        $result = parent::get(self::A_PRICE);
        return $result;
    }

    public static function getPrimaryKeyAttrs()
    {
        return [self::A_STOCK_ITEM_REF, self::A_CUST_GROUP_REF];
    }

    /**
     * @return int
     */
    public function getStockItemRef()
    {
        $result = parent::get(self::A_STOCK_ITEM_REF);
        return $result;
    }

    /**
     * @param int $data
     */
    public function setCustomerGroupRef($data)
    {
        parent::set(self::A_CUST_GROUP_REF, $data);
    }

    /**
     * @param double $data
     */
    public function setPrice($data)
    {
        parent::set(self::A_PRICE, $data);
    }

    /**
     * @param int $data
     */
    public function setStockItemRef($data)
    {
        parent::set(self::A_STOCK_ITEM_REF, $data);
    }
}