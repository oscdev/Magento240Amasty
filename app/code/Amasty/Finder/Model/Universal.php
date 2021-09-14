<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Finder
 */

/**
 * Copyright © 2015 Amasty. All rights reserved.
 */

namespace Amasty\Finder\Model;

use Amasty\Finder\Api\Data\UniversalInterface;

class Universal extends \Magento\Framework\Model\AbstractModel implements UniversalInterface
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init(\Amasty\Finder\Model\ResourceModel\Universal::class);
    }

    /**
     * @return int
     */
    public function getUniversalId()
    {
        return $this->_getData(UniversalInterface::UNIVERSAL_ID);
    }

    /**
     * @param int $universalId
     * @return $this
     */
    public function setUniversalId($universalId)
    {
        $this->setData(UniversalInterface::UNIVERSAL_ID, $universalId);

        return $this;
    }

    /**
     * @return int
     */
    public function getFinderId()
    {
        return $this->_getData(UniversalInterface::FINDER_ID);
    }

    /**
     * @param int $finderId
     * @return $this
     */
    public function setFinderId($finderId)
    {
        $this->setData(UniversalInterface::FINDER_ID, $finderId);

        return $this;
    }

    /**
     * @return string
     */
    public function getSku()
    {
        return $this->_getData(UniversalInterface::SKU);
    }

    /**
     * @param string $sku
     * @return $this
     */
    public function setSku($sku)
    {
        $this->setData(UniversalInterface::SKU, $sku);

        return $this;
    }

    /**
     * @return int
     */
    public function getPid()
    {
        return $this->_getData(UniversalInterface::PID);
    }

    /**
     * @param int $pid
     * @return $this
     */
    public function setPid($pid)
    {
        $this->setData(UniversalInterface::PID, $pid);

        return $this;
    }
}
