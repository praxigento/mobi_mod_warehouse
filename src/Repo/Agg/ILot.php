<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Repo\Agg;


interface ILot
{

    public function create($data);

    public function getById($id);
}