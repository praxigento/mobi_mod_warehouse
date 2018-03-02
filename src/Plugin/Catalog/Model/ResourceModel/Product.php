<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Plugin\Catalog\Model\ResourceModel;

use Magento\Catalog\Api\Data\ProductAttributeInterface as EProdAttr;
use Praxigento\Warehouse\Config as Cfg;
use Praxigento\Warehouse\Repo\Entity\Data\Group\Price as EGroupPrice;
use Praxigento\Warehouse\Repo\Entity\Data\Stock\Item as EWrhsStockItem;

/**
 * MOBI-784, MOBI-1175: replace retail & special prices with warehouse & group prices on Catalog Product loading.
 */
class Product
{
    /** @var \Praxigento\Warehouse\Api\Helper\Stock */
    private $hlpStock;
    /** @var \Magento\Customer\Api\GroupManagementInterface */
    private $manGroup;
    /** @var \Magento\Customer\Model\Session */
    private $modSession;
    /** @var \Magento\Framework\App\ResourceConnection */
    private $resource;
    /** @var \Magento\Framework\Config\ScopeInterface */
    private $scope;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\Config\ScopeInterface $scope,
        \Magento\Customer\Api\GroupManagementInterface $manGroup,
        \Magento\Customer\Model\Session $modSession,
        \Praxigento\Warehouse\Api\Helper\Stock $hlpStock
    ) {
        $this->resource = $resource;
        $this->scope = $scope;
        $this->manGroup = $manGroup;
        $this->modSession = $modSession;
        $this->hlpStock = $hlpStock;
    }


    /**
     * Replace product price & special price attributes for $object by warehouse/group price.
     *
     * @param \Magento\Catalog\Model\ResourceModel\Product $subject
     * @param \Closure $proceed
     * @param $object
     * @param $entityId
     * @param array $attributes
     * @return \Magento\Catalog\Model\ResourceModel\Product
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function aroundLoad(
        \Magento\Catalog\Model\ResourceModel\Product $subject,
        \Closure $proceed,
        $object,
        $entityId,
        $attributes = []
    ) {
        /* load data */
        $result = $proceed($object, $entityId, $attributes);
        /* then replace retail & special prices by warehouse & group prices */
        if ($entityId) {
            list($priceWrhs, $priceGroup) = $this->getWrhsPrices($entityId);
            if ($priceWrhs) {
                $object->setData(EProdAttr::CODE_PRICE, $priceWrhs);
            }
            if ($priceGroup) {
                $object->setData(EProdAttr::CODE_SPECIAL_PRICE, $priceGroup);
            }
        }
        return $result;
    }

    /**
     * Build query to get warehouse & group prices using product ID.
     *
     * @param $prodId
     * @return array [$priceWrhs, $priceGroup]
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getWrhsPrices($prodId)
    {
        /* detect running mode (front/back) */
        $scope = $this->scope->getCurrentScope();
        if ($scope != \Magento\Framework\App\Area::AREA_FRONTEND) {
            /* backend mode */
            $stockId = $this->hlpStock->getDefaultStockId();
            $custGroup = $this->manGroup->getDefaultGroup();
            $custGroupId = $custGroup->getId();
        } else {
            /* frontend mode */
            $stockId = $this->hlpStock->getCurrentStockId();
            $custGroupId = $this->modSession->getCustomerGroupId();
        }
        /* build query */
        $conn = $this->resource->getConnection();
        $query = $conn->select();
        /* aliases for tables and attributes */
        $asStockItem = 'cisi';
        $asWrhsItem = 'wsi';
        $asWrhsGroup = 'wgp';
        $aWrhsPrice = 'wrhsPrice';
        $aGroupPrice = 'groupPrice';

        /* FROM cataloginventory_stock_item */
        $tbl = $this->resource->getTableName(Cfg::ENTITY_MAGE_CATALOGINVENTORY_STOCK_ITEM);
        $as = $asStockItem;
        $cols = [];
        $query->from([$as => $tbl], $cols);

        /* LEFT JOIN prxgt_wrhs_stock_item */
        $tbl = $this->resource->getTableName(EWrhsStockItem::ENTITY_NAME);
        $as = $asWrhsItem;
        $cols = [
            $aWrhsPrice => EWrhsStockItem::ATTR_PRICE
        ];
        $cond = $as . '.' . EWrhsStockItem::ATTR_STOCK_ITEM_REF . '='
            . $asStockItem . '.' . Cfg::E_CATINV_STOCK_ITEM_A_ITEM_ID;
        $query->joinLeft([$as => $tbl], $cond, $cols);

        /* LEFT JOIN prxgt_wrhs_group_price */
        $tbl = $this->resource->getTableName(EGroupPrice::ENTITY_NAME);
        $as = $asWrhsGroup;
        $cols = [
            $aGroupPrice => EGroupPrice::ATTR_PRICE
        ];
        $cond = $as . '.' . EGroupPrice::ATTR_STOCK_ITEM_REF . '='
            . $asStockItem . '.' . Cfg::E_CATINV_STOCK_ITEM_A_ITEM_ID;
        $query->joinLeft([$as => $tbl], $cond, $cols);

        /* query tuning */
        $byProdId = "$asStockItem." . Cfg::E_CATINV_STOCK_ITEM_A_PROD_ID . "=" . (int)$prodId;
        $byStockId = "$asStockItem." . Cfg::E_CATINV_STOCK_ITEM_A_STOCK_ID . "=" . (int)$stockId;
        $byGroupId = "$asWrhsGroup." . EGroupPrice::ATTR_CUST_GROUP_REF . "=" . (int)$custGroupId;
        $query->where("($byProdId) AND ($byStockId) AND ($byGroupId)");

        /* execute query */
        $conn = $query->getConnection();
        $rs = $conn->fetchRow($query);
        $result = [null, null];
        if ($rs) {
            $priceWrhs = $rs[$aWrhsPrice];
            $priceGroup = $rs[$aGroupPrice];
            $result = [$priceWrhs, $priceGroup];
        }
        return $result;
    }
}