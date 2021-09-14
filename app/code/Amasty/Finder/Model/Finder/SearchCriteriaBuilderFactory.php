<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Finder
 */


namespace Amasty\Finder\Model\Finder;

/**
 * Class SearchCriteriaBuilderFactory
 * @package Amasty\Finder\Model\Finder
 */
class SearchCriteriaBuilderFactory
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * @return SearchCriteriaBuilder
     * @throws \UnexpectedValueException
     */
    public function create()
    {
        return $this->_objectManager->create(SearchCriteriaBuilder::class);
    }

    /**
     * @return SearchCriteriaBuilder
     * @throws \UnexpectedValueException
     */
    public function get()
    {
        return $this->_objectManager->get(SearchCriteriaBuilder::class);
    }
}
