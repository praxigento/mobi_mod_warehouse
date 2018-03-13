<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Repo\Query\Catalog\Model\ResourceModel\Product\Collection\Group\Price;

use Praxigento\Warehouse\Config as Cfg;
use Praxigento\Warehouse\Repo\Entity\Data\Group\Price as EGroupPrice;

/**
 * Add warehouse group price data to input query.
 * @deprecated use on-place SQL modifications
 */
class Builder
    implements \Praxigento\Core\App\Repo\Query\IBuilder
{
    /** Tables aliases */
    const AS_CATALOGINVENTORY_STOCK_ITEM = 'cisi';
    const AS_WRHS_GROUP_PRICE = 'prxgtWgp';
    /** Columns aliases */
    const A_PRICE = \Praxigento\Warehouse\Plugin\Catalog\Model\Product\Type\Price::A_PRICE_WRHS_GROUP;

    /** @var \Magento\Framework\Config\ScopeInterface */
    private $cfgScope;
    /** @var \Praxigento\Warehouse\Api\Helper\Stock */
    private $hlpStock;
    /** @var \Magento\Customer\Api\GroupManagementInterface */
    private $manGroup;
    /** @var \Magento\Customer\Model\Session */
    private $modSession;
    /** @var \Magento\Framework\App\ResourceConnection */
    private $resource;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\Config\ScopeInterface $cfgScope,
        \Magento\Customer\Api\GroupManagementInterface $manGroup,
        \Magento\Customer\Model\Session $modSession,
        \Praxigento\Warehouse\Api\Helper\Stock $hlpStock
    ) {
        $this->resource = $resource;
        $this->cfgScope = $cfgScope;
        $this->manGroup = $manGroup;
        $this->modSession = $modSession;
        $this->hlpStock = $hlpStock;
    }


    public function build(\Magento\Framework\DB\Select $source = null)
    {
        $query = $source;
        /* aliases for tables in query */
        $from = $query->getPart(\Magento\Framework\DB\Select::FROM);
        $asMain = key($from);
        $asInvItem = self::AS_CATALOGINVENTORY_STOCK_ITEM;
        $asPrice = self::AS_WRHS_GROUP_PRICE;

        /* detect running mode (front/back) */
        $scope = $this->cfgScope->getCurrentScope();
        if ($scope != \Magento\Framework\App\Area::AREA_FRONTEND) {
            /* backend mode */
            $stockId = $this->hlpStock->getDefaultStockId();
            $custGroup = $this->manGroup->getDefaultGroup();
            $custGroupId = $custGroup->getId();
            $mJoin = 'joinLeft';
        } else {
            /* frontend mode */
            $stockId = $this->hlpStock->getCurrentStockId();
            $custGroupId = $this->modSession->getCustomerGroupId();
            $mJoin = 'joinInner';
        }
        /* INNER/LEFT JOIN cataloginventory_stock_item */
        $tbl = $this->resource->getTableName(Cfg::ENTITY_MAGE_CATALOGINVENTORY_STOCK_ITEM);
        $as = $asInvItem;
        $on = $asInvItem . '.' . Cfg::E_CATINV_STOCK_ITEM_A_PROD_ID . '=' . $asMain . '.' . Cfg::E_PRODUCT_A_ENTITY_ID;
        $on .= " AND ($asInvItem." . Cfg::E_CATINV_STOCK_ITEM_A_STOCK_ID . '=' . (int)$stockId . ')';
        $cols = [];
        $query->{$mJoin}([$as => $tbl], $on, $cols);

        /* INNER/LEFT JOIN prxgt_wrhs_group_price */
        $tbl = $this->resource->getTableName(EGroupPrice::ENTITY_NAME);
        $as = $asPrice;
        $on = $asPrice . '.' . EGroupPrice::ATTR_STOCK_ITEM_REF . '=' . $asInvItem . '.' . Cfg::E_CATINV_STOCK_ITEM_A_ITEM_ID;
        $on .= " AND ($asPrice." . EGroupPrice::ATTR_CUST_GROUP_REF . '=' . (int)$custGroupId . ')';
        $cols = [self::A_PRICE => EGroupPrice::ATTR_PRICE];
        $query->{$mJoin}([$as => $tbl], $on, $cols);

        /* result */
        return $query;
    }
}