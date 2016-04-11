<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Warehouse\Repo\Entity\Def;

use Praxigento\Core\Repo\Def\Entity as BaseEntityRepo;
use Praxigento\Core\Repo\IBasic as IRepoBasic;
use Praxigento\Warehouse\Data\Entity\Quantity as Entity;
use Praxigento\Warehouse\Repo\Entity\IQuantity as IEntityRepo;

class Quantity extends BaseEntityRepo implements IEntityRepo
{
    public function __construct(IRepoBasic $repoBasic)
    {
        parent::__construct($repoBasic, new Entity());
    }

}