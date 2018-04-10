<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Warehouse\Repo\Data;

class Quote
    extends \Praxigento\Core\App\Repo\Data\Entity\Base
{
    const A_QUOTE_REF = 'quote_ref';
    const A_STOCK_REF = 'stock_ref';
    const ENTITY_NAME = 'prxgt_wrhs_quote';

    public static function getPrimaryKeyAttrs()
    {
        return [self::A_QUOTE_REF];
    }

    /** @return int */
    public function getQuoteRef()
    {
        $result = parent::get(self::A_QUOTE_REF);
        return $result;
    }

    /** @return int */
    public function getStockRef()
    {
        $result = parent::get(self::A_STOCK_REF);
        return $result;
    }

    /** @param int $data */
    public function setQuoteRef($data)
    {
        parent::set(self::A_QUOTE_REF, $data);
    }

    /** @param int $data */
    public function setStockRef($data)
    {
        parent::set(self::A_STOCK_REF, $data);
    }
}