<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Lib\Repo\Entity\Def;

use Magento\Framework\ObjectManagerInterface;
use Praxigento\Core\Repo\IBasic as IBasicRepo;
use Praxigento\Warehouse\Data\Entity\Lot as EntityLot;
use Praxigento\Warehouse\Lib\Repo\Entity\ILot;


class Lot implements ILot
{
    const AS_LOT = 'pwl';
    /** @var  \Magento\Framework\DB\Adapter\AdapterInterface */
    protected $_conn;
    /** @var  IObjectManager */
    protected $_manObj;
    /** @var IBasicRepo */
    protected $_repoBasic;
    /** @var \Magento\Framework\App\ResourceConnection */
    protected $_resource;

    public function __construct(
        ObjectManagerInterface $manObj,
        \Magento\Framework\App\ResourceConnection $resource,
        IBasicRepo $repoBasic
    ) {
        $this->_manObj = $manObj;
        $this->_resource = $resource;
        $this->_conn = $resource->getConnection();
        $this->_repoBasic = $repoBasic;
    }

    protected function _initQueryRead()
    {
        $result = $this->_conn->select();
        /* aliases and tables */
        $asLot = self::AS_LOT;
        $tblLot = [$asLot => $this->_conn->getTableName(EntityLot::ENTITY_NAME)];
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
        $data = $this->_conn->fetchRow($query, ['id' => $id]);
        if ($data) {
            $result = $this->_initResultRead($data);
        }
        return $result;
    }

}