<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Repo\Entity;

use Praxigento\Warehouse\Data\Entity\Warehouse as Entity;

interface IWarehouse extends \Praxigento\Core\Repo\IEntity
{

    /**
     * @param array|Entity $data
     * @return Entity
     */
    public function create($data);

    /**
     * Generic method to get data from repository.
     *
     * @param null $where
     * @param null $order
     * @param null $limit
     * @param null $offset
     * @param null $columns
     * @param null $group
     * @param null $having
     * @return Entity[] Found data or empty array if no data found.
     */
    public function get(
        $where = null,
        $order = null,
        $limit = null,
        $offset = null,
        $columns = null,
        $group = null,
        $having = null
    );

    /**
     * Get the data instance by ID (ID can be an array for complex primary keys).
     *
     * @param int $id
     * @return Entity|bool Found instance data or 'false'
     */
    public function getById($id);
}