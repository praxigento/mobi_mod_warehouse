<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Plugin\Catalog\Model\ResourceModel;

use Praxigento\Warehouse\Config as Cfg;
use Praxigento\Warehouse\Repo\Query\Catalog\Model\ResourceModel\Product\Collection\Group\Price\Builder as QBGroupPrice;

/**
 * MOBI-784: replace "price" attribute on Catalog Product loading,
 */
class Product
{

    /**
     * @var \Praxigento\Warehouse\Api\Helper\Stock
     */
    protected $hlpStock;
    /**
     * @var \Praxigento\Warehouse\Repo\Query\Catalog\Model\ResourceModel\Product\Collection\Group\Price\Builder
     */
    protected $qbGroupPrice;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resource;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Praxigento\Warehouse\Repo\Query\Catalog\Model\ResourceModel\Product\Collection\Group\Price\Builder $qbGroupPrice
    ) {
        $this->resource = $resource;
        $this->qbGroupPrice = $qbGroupPrice;
    }


    /**
     * Replace product price attribute for $object by warehouse/group price.
     *
     * @param \Magento\Catalog\Model\ResourceModel\Product $subject
     * @param \Closure $proceed
     * @param $object
     * @param $entityId
     * @param array $attributes
     * @return \Magento\Catalog\Model\ResourceModel\Product
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
        /* then replace retail price by warehouse/group price */
        $price = $object->getPrice();
        if (!is_null($price)) {
            /* create base query to get product */
            $conn = $this->resource->getConnection();
            $baseQuery = $conn->select();
            $tbl = $this->resource->getTableName(Cfg::ENTITY_MAGE_PRODUCT);
            $cols = []; // no columns in result set
            $baseQuery->from($tbl, $cols);
            $bindProdId = 'prodId';
            $where = $tbl . '.' . Cfg::E_PRODUCT_A_ENTITY_ID . "=:$bindProdId";
            $baseQuery->where($where);
            /* build query to get additional warehouse/group prces */
            $query = $this->qbGroupPrice->build($baseQuery);
            $bind = [$bindProdId => $entityId];
            $row = $conn->fetchRow($query, $bind);
            if (is_array($row) && isset($row[QBGroupPrice::A_PRICE])) {
                $priceWrhsGroup = $row[QBGroupPrice::A_PRICE];
                $object->setPrice($priceWrhsGroup);
            }
        }
        return $result;
    }

}