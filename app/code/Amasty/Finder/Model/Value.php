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

use Amasty\Finder\Api\Data\ValueInterface;

class Value extends \Magento\Framework\Model\AbstractModel implements ValueInterface
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init(\Amasty\Finder\Model\ResourceModel\Value::class);
    }

    /**
     * @return int
     */
    public function getValueId()
    {
        return $this->_getData(ValueInterface::VALUE_ID);
    }

    /**
     * @param int $valueId
     * @return $this
     */
    public function setValueId($valueId)
    {
        $this->setData(ValueInterface::VALUE_ID, $valueId);

        return $this;
    }

    /**
     * @return int
     */
    public function getParentId()
    {
        return $this->_getData(ValueInterface::PARENT_ID);
    }

    /**
     * @param int $parentId
     * @return $this
     */
    public function setParentId($parentId)
    {
        $this->setData(ValueInterface::PARENT_ID, $parentId);

        return $this;
    }

    /**
     * @return int
     */
    public function getDropdownId()
    {
        return $this->_getData(ValueInterface::DROPDOWN_ID);
    }

    /**
     * @param int $dropdownId
     * @return $this
     */
    public function setDropdownId($dropdownId)
    {
        $this->setData(ValueInterface::DROPDOWN_ID, $dropdownId);

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->_getData(ValueInterface::NAME);
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->setData(ValueInterface::NAME, $name);

        return $this;
    }
}
