<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Repo\Query\Catalog\Model\ResourceModel\Product\Collection\GetSelectCountSql;


/**
 * Fix Select Count SQL for products collection.
 */
class Builder
    implements \Praxigento\Core\Repo\Query\IBuilder2
{


    public function build(\Magento\Framework\DB\Select $source = null)
    {
        $query = $source;
        /* MOBI-736: remove group clause to get total count in adminhtml grid */
        $query->reset(\Zend_Db_Select::GROUP);
        return $query;
    }
}