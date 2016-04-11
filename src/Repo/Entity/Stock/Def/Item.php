<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Repo\Entity\Stock\Def;

use Praxigento\Core\Repo\Def\Entity as BaseEntityRepo;
use Praxigento\Core\Repo\IBasic as IRepoBasic;
use Praxigento\Warehouse\Data\Entity\Stock\Item as Entity;
use Praxigento\Warehouse\Repo\Entity\Stock\IItem as IEntityRepo;

class Item extends BaseEntityRepo implements IEntityRepo
{
    public function __construct(IRepoBasic $repoBasic)
    {
        parent::__construct($repoBasic, new Entity());
    }

}