<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Repo\Entity\Stock;


interface IItem extends \Praxigento\Core\Repo\IEntity
{
    /**
     * Referenced entity to address attributes.
     *
     * @return \Praxigento\Warehouse\Data\Entity\Stock\Item
     */
    public function getRef();
}