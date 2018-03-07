<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Warehouse\Plugin\CatalogInventory\Ui\DataProvider\Product;


/**
 * Replace WHERE clause by HAVING clause (used for grouped values).
 */
class AddQuantityFilterToCollection
{
    protected $_regCond = [];
    /** @var \Praxigento\Warehouse\Repo\Modifier\Product\Grid */
    protected $_repoModifierProductGFrid;

    public function __construct(
        \Praxigento\Warehouse\Repo\Modifier\Product\Grid $repoModifierProductGFrid
    ) {
        $this->_repoModifierProductGFrid = $repoModifierProductGFrid;
    }

    /**
     * Replace WHERE-filtering by HAVING-filtering.
     *
     * @param \Magento\CatalogInventory\Ui\DataProvider\Product\AddQuantityFilterToCollection $subject
     * @param \Closure $proceed
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
     * @param $field
     * @param null $condition
     */
    public function aroundAddFilter(
        \Magento\CatalogInventory\Ui\DataProvider\Product\AddQuantityFilterToCollection $subject,
        \Closure $proceed,
        \Magento\Catalog\Model\ResourceModel\Product\Collection $collection,
        $field,
        $condition = null
    ) {
        /* skip identical conditions () */
        $regKey = print_r($condition, true);
        if (!isset($this->_regCond[$regKey])) {
            $conn = $collection->getConnection();
            $select = $collection->getSelect();
            $equation = $this->_repoModifierProductGFrid->getEquationQty();
            $prepared = $conn->prepareSqlCondition($equation, $condition);
            $select->having($prepared);
            $this->_regCond[$regKey] = true;
        }
        return;
    }
}