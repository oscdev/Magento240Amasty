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

use Amasty\Finder\Api\FinderRepositoryInterface;
use Amasty\Finder\Api\ValueRepositoryInterface;
use Amasty\Finder\Helper\Import as ImportHelper;
use Amasty\Finder\Api\Data\DropdownInterface;

class Dropdown extends \Magento\Framework\Model\AbstractModel implements DropdownInterface
{
    /**
     * @var ValueRepositoryInterface
     */
    private $valueRepository;

    /**
     * @var FinderRepositoryInterface
     */
    private $finderRepository;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init(\Amasty\Finder\Model\ResourceModel\Dropdown::class);
    }

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Amasty\Finder\Api\ValueRepositoryInterface $valueRepository,
        \Amasty\Finder\Api\FinderRepositoryInterface $finderRepository,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->valueRepository = $valueRepository;
        $this->finderRepository = $finderRepository;
        $this->storeManager = $storeManager;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * @param $parentId
     * @param int $selected
     * @param bool $loadAfterCheck
     * @return array
     */
    public function getOptions($parentId, $selected = 0, $loadAfterCheck = false)
    {
        $url = rtrim($this->storeManager->getStore()->getBaseUrl(
            \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
        ), '/');
        $options = [];
        $optionsCollection = $this->getCollectionOptions($parentId);
        foreach ($optionsCollection as $option) {
            $isSelected = $selected == $option->getValueId() || ($loadAfterCheck && $optionsCollection->getSize() == 1);
            $name = $option->getName();
            $options[] =[
                'value' => $option->getValueId(),
                'label' => is_numeric($name) ? $name : __($name),
                'selected' => $isSelected,
                'image' => $option->getImage() ? $url . $option->getImage() : ''
            ];
        }

        return $options;
    }

    /**
     * @param $parentId
     * @return ResourceModel\Value\Collection
     */
    private function getCollectionOptions($parentId)
    {
        $collection = $this->valueRepository->getByParentAndDropdownIds($parentId, $this->getId());

        if (!$this->getPos()) {
            $collection->addFieldToFilter('dropdown_id', $this->getId());
        }

        $collection->getSelect()->order($this->getOrder());

        return $collection;
    }

    /**
     * @return string|\Zend_Db_Expr
     */
    private function getOrder()
    {
        $order = '';
        switch ($this->getSort()) {
            case ImportHelper::SORT_STRING_ASC:
                $order = 'name ASC';
                break;
            case ImportHelper::SORT_STRING_DESC:
                $order = 'name DESC';
                break;
            case ImportHelper::SORT_NUM_ASC:
                $order = new \Zend_Db_Expr('CAST(`name` AS DECIMAL(10,2)) ASC');
                break;
            case ImportHelper::SORT_NUM_DESC:
                $order = new \Zend_Db_Expr('CAST(`name` AS DECIMAL(10,2)) DESC');
                break;
        }

        return $order;
    }

    /**
     * @return \Amasty\Finder\Api\Data\FinderInterface
     */
    public function getFinder()
    {
        return $this->finderRepository->getById($this->getFinderId());
    }

    /**
     * @return int
     */
    public function getDropdownId()
    {
        return $this->_getData(DropdownInterface::DROPDOWN_ID);
    }

    /**
     * @param int $dropdownId
     * @return $this
     */
    public function setDropdownId($dropdownId)
    {
        $this->setData(DropdownInterface::DROPDOWN_ID, $dropdownId);

        return $this;
    }

    /**
     * @return int
     */
    public function getFinderId()
    {
        return $this->_getData(DropdownInterface::FINDER_ID);
    }

    /**
     * @param int $finderId
     * @return $this
     */
    public function setFinderId($finderId)
    {
        $this->setData(DropdownInterface::FINDER_ID, $finderId);

        return $this;
    }

    /**
     * @return int
     */
    public function getPos()
    {
        return $this->_getData(DropdownInterface::POS);
    }

    /**
     * @param int $pos
     * @return $this
     */
    public function setPos($pos)
    {
        $this->setData(DropdownInterface::POS, $pos);

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->_getData(DropdownInterface::NAME);
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->setData(DropdownInterface::NAME, $name);

        return $this;
    }

    /**
     * @return int
     */
    public function getSort()
    {
        return $this->_getData(DropdownInterface::SORT);
    }

    /**
     * @param int $sort
     * @return $this
     */
    public function setSort($sort)
    {
        $this->setData(DropdownInterface::SORT, $sort);

        return $this;
    }

    /**
     * @return int
     */
    public function getRange()
    {
        return $this->_getData(DropdownInterface::RANGE);
    }

    /**
     * @param int $range
     * @return $this
     */
    public function setRange($range)
    {
        $this->setData(DropdownInterface::RANGE, $range);

        return $this;
    }

    /**
     * @return int
     */
    public function getDisplayType()
    {
        return $this->_getData(DropdownInterface::DISPLAY_TYPE);
    }

    /**
     * @param int $displayType
     * @return $this
     */
    public function setDisplayType($displayType)
    {
        $this->setData(DropdownInterface::DISPLAY_TYPE, $displayType);

        return $this;
    }

    /**
     * @param \Amasty\Finder\Model\Dropdown $dropdown
     * @return bool
     */
    public function isHidden(\Amasty\Finder\Model\Dropdown $dropdown, $finder)
    {
        //it's not the first dropdown && value is not selected
        return ($dropdown->getPos() && !$finder->getSavedValue($dropdown->getId()));
    }
}
