<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Service\Warehouse\Response;

use Praxigento\Core\Service\Base\Response as BaseResponse;

class Create extends BaseResponse implements ICreate
{
    public function getId()
    {
        $result = parent::getId();
        return $result;
    }

    public function setId($data)
    {
        parent::setID($data);
    }

}