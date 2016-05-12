<?php
/**
 * Disable original "Quantity" filter in the grid.
 *
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Plugin\CatalogInventory\Ui\DataProvider\Product;

use Magento\CatalogInventory\Ui\DataProvider\Product\AddQuantityFilterToCollection as Subject;

class AddQuantityFilterToCollection
{

    /**
     * @param Subject $subject
     * @param \Closure $proceed
     */
    public function aroundAddFilter(Subject $subject, \Closure $proceed)
    {
        return;
    }
}