<?php
/**
 * Disable original "Quantity" filter in the grid.
 *
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Plugin\CatalogInventory\Ui\DataProvider\Product;


use Praxigento\Warehouse\Repo\Modifier\Product\Grid;

class AddQuantityFilterToCollection
{
    protected $_regCond = [];

    /**
     * @param Subject $subject
     * @param \Closure $proceed
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
            $equation = Grid::EQ_QTY;
            $prepared = $collection->getConnection()->prepareSqlCondition($equation, $condition);
            $collection->getSelect()->having($prepared);
            $this->_regCond[$regKey] = true;
        }
        return;
    }
}