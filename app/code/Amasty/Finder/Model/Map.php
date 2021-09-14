<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Finder
 */

namespace Amasty\Finder\Model;

use Amasty\Finder\Api\Data\MapInterface;

class Map extends \Magento\Framework\Model\AbstractModel implements MapInterface
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init(\Amasty\Finder\Model\ResourceModel\Map::class);
    }
    /**
     * @return int
     */
    public function getValueId()
    {
        return $this->_getData(MapInterface::VALUE_ID);
    }

    /**
     * @param int $valueId
     * @return $this
     */
    public function setValueId($valueId)
    {
        $this->setData(MapInterface::VALUE_ID, $valueId);

        return $this;
    }

    /**
     * @return int
     */
    public function getPid()
    {
        return $this->_getData(MapInterface::PID);
    }

    /**
     * @param int $pid
     * @return $this
     */
    public function setPid($pid)
    {
        $this->setData(MapInterface::PID, $pid);

        return $this;
    }

    /**
     * @return string
     */
    public function getSku()
    {
        return $this->_getData(MapInterface::SKU);
    }

    /**
     * @param string $sku
     * @return $this
     */
    public function setSku($sku)
    {
        $this->setData(MapInterface::SKU, $sku);

        return $this;
    }
}
