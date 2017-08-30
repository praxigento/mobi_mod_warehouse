<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Repo\Entity;

class Warehouse extends \Praxigento\Core\Repo\Def\Entity
{
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Praxigento\Core\Repo\IGeneric $repoGeneric
    ) {
        parent::__construct($resource, $repoGeneric, \Praxigento\Warehouse\Repo\Entity\Data\Warehouse::class);
    }

    /**
     * @param array|\Praxigento\Warehouse\Repo\Entity\Data\Warehouse $data
     * @return \Praxigento\Warehouse\Repo\Entity\Data\Warehouse
     */
    public function create($data)
    {
        $result = parent::create($data);
        return $result;
    }

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
     * @return \Praxigento\Warehouse\Repo\Entity\Data\Warehouse[] Found data or empty array if no data found.
     */
    public function get(
        $where = null,
        $order = null,
        $limit = null,
        $offset = null,
        $columns = null,
        $group = null,
        $having = null
    )
    {
        $result = parent::get($where, $order, $limit, $offset, $columns, $group, $having);
        return $result;
    }

    /**
     * Get the data instance by ID.
     *
     * @param int $id
     * @return \Praxigento\Warehouse\Repo\Entity\Data\Warehouse|bool Found instance data or 'false'
     */
    public function getById($id)
    {
        $result = parent::getById($id);
        return $result;
    }
}