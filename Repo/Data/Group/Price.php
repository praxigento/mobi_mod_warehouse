<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Repo\Data\Group;

class Price
    extends \Praxigento\Core\App\Repo\Data\Entity\Base
{
    const ATTR_CUST_GROUP_REF = 'cust_group_ref';
    const ATTR_PRICE = 'price';
    const ATTR_STOCK_ITEM_REF = 'stock_item_ref';
    const ENTITY_NAME = 'prxgt_wrhs_group_price';

    /**
     * @return int
     */
    public function getCustomerGroupRef()
    {
        $result = parent::get(self::ATTR_CUST_GROUP_REF);
        return $result;
    }

    /**
     * @return double
     */
    public function getPrice()
    {
        $result = parent::get(self::ATTR_PRICE);
        return $result;
    }

    public static function getPrimaryKeyAttrs()
    {
        return [self::ATTR_STOCK_ITEM_REF, self::ATTR_CUST_GROUP_REF];
    }

    /**
     * @return int
     */
    public function getStockItemRef()
    {
        $result = parent::get(self::ATTR_STOCK_ITEM_REF);
        return $result;
    }

    /**
     * @param int $data
     */
    public function setCustomerGroupRef($data)
    {
        parent::set(self::ATTR_CUST_GROUP_REF, $data);
    }

    /**
     * @param double $data
     */
    public function setPrice($data)
    {
        parent::set(self::ATTR_PRICE, $data);
    }

    /**
     * @param int $data
     */
    public function setStockItemRef($data)
    {
        parent::set(self::ATTR_STOCK_ITEM_REF, $data);
    }
}