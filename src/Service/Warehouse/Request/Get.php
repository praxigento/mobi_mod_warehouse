<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Warehouse\Service\Warehouse\Request;

use Praxigento\Core\Service\Base\Request;

class Get extends Request implements IGet
{

    public function getId()
    {
        $result = parent::getId();
        return $result;
    }

    public function setId($data)
    {
        parent::setId($data);
    }
}