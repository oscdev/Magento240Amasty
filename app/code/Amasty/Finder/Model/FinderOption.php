<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Finder
 */


namespace Amasty\Finder\Model;

use Amasty\Finder\Api\Data\FinderOptionInterface;

class FinderOption extends \Magento\Framework\Model\AbstractModel implements FinderOptionInterface
{
    /**
     * @return int
     */
    public function getDropdownId()
    {
        return $this->_getData(FinderOptionInterface::ID);
    }

    /**
     * @param int $id
     * @return $this
     */
    public function setDropdownId($id)
    {
        $this->setData(FinderOptionInterface::ID, $id);

        return $this;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->_getData(FinderOptionInterface::VALUE);
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setValue($value)
    {
        $this->setData(FinderOptionInterface::VALUE, $value);

        return $this;
    }
}
