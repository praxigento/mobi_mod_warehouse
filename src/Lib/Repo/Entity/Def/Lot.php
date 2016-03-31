<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Lib\Repo\Entity\Def;

use Praxigento\Core\Lib\Context as Ctx;
use Praxigento\Core\Lib\Context\IObjectManager;
use Praxigento\Core\Lib\Context\ObjectManagerFactory;
use Praxigento\Core\Lib\Repo\IBasic as IBasicRepo;
use Praxigento\Warehouse\Lib\Data\Entity\Lot as EntityLot;
use Praxigento\Warehouse\Lib\Repo\Entity\ILot;


class Lot implements ILot
{
    const AS_LOT = 'pwl';
    /** @var IBasicRepo */
    protected $_repoBasic;
    /** @var  IObjectManager */
    protected $_manObj;

    public function __construct(
        ObjectManagerFactory $factObjMan,
        IBasicRepo $repoBasic
    ) {
        $this->_manObj = $factObjMan->create();
        $this->_repoBasic = $repoBasic;
    }

    protected function _initQueryRead()
    {
        $dba = $this->_repoBasic->getDba();
        $conn = $dba->getDefaultConnection();
        $result = $conn->select();
        /* aliases and tables */
        $asLot = self::AS_LOT;
        $tblLot = [$asLot => $dba->getTableName(EntityLot::ENTITY_NAME)];
        /* SELECT FROM prxgt_odoo_lot */
        $cols = [
            EntityLot::ATTR_ID,
            EntityLot::ATTR_CODE,
            EntityLot::ATTR_EXP_DATE
        ];
        $result->from($tblLot, $cols);
        return $result;
    }

    protected function _initResultRead($data)
    {
        /** @var  $result EntityLot */
        $result = $this->_manObj->create(EntityLot::class);
        $result->setData($data);
        return $result;
    }

    public function create($data)
    {
        // TODO: Implement create() method.
    }

    public function getById($id)
    {
        /** @var  $result EntityLot */
        $result = null;
        $query = $this->_initQueryRead();
        $query->where(self::AS_LOT . '.' . EntityLot::ATTR_ID . '=:id');
        $sql = (string)$query;
        $dba = $this->_repoBasic->getDba();
        $conn = $dba->getDefaultConnection();
        $data = $conn->fetchRow($query, ['id' => $id]);
        if ($data) {
            $result = $this->_initResultRead($data);
        }
        return $result;
    }

}