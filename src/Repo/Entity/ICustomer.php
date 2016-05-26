<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Repo\Entity;

use Praxigento\Warehouse\Data\Entity\Customer as Entity;

interface ICustomer extends \Praxigento\Core\Repo\IEntity
{

    /**
     * @param array|Entity $data
     * @return Entity
     */
    public function create($data);

    /**
     * @param int $id
     * @return Entity|bool Found instance data or 'false'
     */
    public function getById($id);
}