<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Service\QtyDistributor\Request;

use Praxigento\Warehouse\Service\QtyDistributor\Data\Item;

/**
 * @method Item[] getSaleItems()
 * @method void setSaleItems(Item[] $data)
 */
class RegisterSale extends \Praxigento\Core\Service\Base\Request
{
}