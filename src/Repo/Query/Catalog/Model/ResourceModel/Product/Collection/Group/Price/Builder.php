<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Repo\Query\Catalog\Model\ResourceModel\Product\Collection\Group\Price;

use Praxigento\Warehouse\Config as Cfg;
use Praxigento\Warehouse\Repo\Entity\Data\Group\Price as EGroupPrice;

/**
 * Add warehouse group price data to input query.
 */
class Builder
    implements \Praxigento\Core\Repo\Query\IBuilder2
{
    /* Tables aliases */
    const AS_CATALOGINVENTORY_STOCK_ITEM = 'cisi';
    const AS_WRHS_GROUP_PRICE = 'prxgtWgp';

    /** Columns aliases */
    const A_PRICE = 'prxgt_wrhs_group_price';

    /** @var \Praxigento\Warehouse\Tool\IStockManager */
    private $manStock;
    /** @var \Magento\Backend\Model\Session\Quote */
    private $modQuoteSession;
    /** @var \Magento\Customer\Model\Session */
    private $modSession;
    /** @var \Magento\Framework\App\ResourceConnection */
    private $resource;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Backend\Model\Session\Quote $modQuoteSession,
        \Magento\Customer\Model\Session $modSession,
        \Praxigento\Warehouse\Tool\IStockManager $manStock
    ) {
        $this->resource = $resource;
        $this->modQuoteSession = $modQuoteSession;
        $this->modSession = $modSession;
        $this->manStock = $manStock;
    }


    public function build(\Magento\Framework\DB\Select $source = null)
    {
        $query = $source;
        /* aliases for tables in query */
        $from = $query->getPart('from');
        $asMain = key($from);
        $asInvItem = self::AS_CATALOGINVENTORY_STOCK_ITEM;
        $asPrice = self::AS_WRHS_GROUP_PRICE;

        /* query parameters */
        $storeId = $this->modQuoteSession->getStoreId();
        $stockId = $this->manStock->getStockIdByStoreId($storeId);
        $custGroupId = $this->modSession->getCustomerGroupId();
        $quote = $this->modQuoteSession->getQuote();
        $quoteCustGroupId = $quote->getCustomerGroupId();
        if ($quoteCustGroupId) {
            $custGroupId = $quoteCustGroupId;
        }
        /* INNER JOIN cataloginventory_stock_item */
        $tbl = $this->resource->getTableName(Cfg::ENTITY_MAGE_CATALOGINVENTORY_STOCK_ITEM);
        $as = $asInvItem;
        $on = $asInvItem . '.' . Cfg::E_CATINV_STOCK_ITEM_A_PROD_ID . '=' . $asMain . '.' . Cfg::E_PRODUCT_A_ENTITY_ID;
        $on .= " AND ($asInvItem." . Cfg::E_CATINV_STOCK_ITEM_A_STOCK_ID . '=' . (int)$stockId . ')';
        $cols = [];
        $query->join([$as => $tbl], $on, $cols);

        /* LEFT JOIN prxgt_wrhs_group_price */
        $tbl = $this->resource->getTableName(EGroupPrice::ENTITY_NAME);
        $as = $asPrice;
        $on = $asPrice . '.' . EGroupPrice::ATTR_STOCK_ITEM_REF . '=' . $asInvItem . '.' . Cfg::E_CATINV_STOCK_ITEM_A_ITEM_ID;
        $on .= " AND ($asPrice." . EGroupPrice::ATTR_CUST_GROUP_REF . '=' . (int)$custGroupId . ')';
        $cols = [self::A_PRICE => EGroupPrice::ATTR_PRICE];
        $query->join([$as => $tbl], $on, $cols);

        /* result */
        return $query;
    }
}