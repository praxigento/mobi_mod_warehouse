<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Warehouse\Data\Entity;

use Praxigento\Core\Data\Entity\Base as EntityBase;

class Customer extends EntityBase
{
    const ATTR_CUST_REF = 'customer_ref';
    const ATTR_STOCK_REF = 'stock_ref';
    const ENTITY_NAME = 'prxgt_wrhs_customer';

    /**
     * @return int
     */
    public function getCustomerRef()
    {
        $result = parent::getData(self::ATTR_CUST_REF);
        return $result;
    }

    /**
     * @inheritdoc
     */
    public function getEntityName()
    {
        return self::ENTITY_NAME;
    }

    /**
     * @inheritdoc
     */
    public function getPrimaryKeyAttrs()
    {
        return [self::ATTR_CUST_REF];
    }

    /**
     * @return int
     */
    public function getStockRef()
    {
        $result = parent::getData(self::ATTR_STOCK_REF);
        return $result;
    }


    /**
     * @param int $data
     */
    public function setCustomerRef($data)
    {
        parent::setData(self::ATTR_CUST_REF, $data);
    }

    /**
     * @param int $data
     */
    public function setStockRef($data)
    {
        parent::setData(self::ATTR_STOCK_REF, $data);
    }
}