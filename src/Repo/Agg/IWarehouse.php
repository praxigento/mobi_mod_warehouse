<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Warehouse\Repo\Agg;

use Praxigento\Warehouse\Data\Agg\Warehouse as AggWarehouse;

interface IWarehouse
    extends \Praxigento\Core\Repo\ICrud
{
    const AS_STOCK = 'cs';
    const AS_WRHS = 'pww';

    /**
     * @param array|AggWarehouse $data
     * @return AggWarehouse
     */
    public function create($data);

    /**
     * @param int $id
     * @return AggWarehouse|null
     */
    public function getById($id);
}