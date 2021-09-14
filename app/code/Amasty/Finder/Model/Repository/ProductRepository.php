<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Finder
 */


namespace Amasty\Finder\Model\Repository;

use Amasty\Finder\Api\ProductRepositoryInterface;

class ProductRepository implements ProductRepositoryInterface
{
    /**
     * @var \Amasty\Finder\Api\DropdownRepositoryInterface
     */
    private $dropdownRepository;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var \Amasty\Finder\Api\FinderRepositoryInterface
     */
    private $finderRepository;

    /**
     * @var \Amasty\Finder\Helper\Config
     */
    private $configHelper;

    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collectionFactory,
        \Amasty\Finder\Api\DropdownRepositoryInterface $dropdownRepository,
        \Amasty\Finder\Api\FinderRepositoryInterface $finderRepository,
        \Amasty\Finder\Helper\Config $configHelper
    ) {
        $this->dropdownRepository = $dropdownRepository;
        $this->collectionFactory = $collectionFactory;
        $this->finderRepository = $finderRepository;
        $this->configHelper = $configHelper;
    }

    /**
     * @param \Amasty\Finder\Api\Data\FinderOptionInterface[] $finderOptions
     *
     * @return \Magento\Catalog\Api\Data\ProductInterface[]
     */
    public function getProductsByFinderValues($finderOptions)
    {
        $collection = $this->collectionFactory->create();
        if (isset($finderOptions[0])) {
            $dropdown = $this->dropdownRepository->getById($finderOptions[0]->getDropdownId());
            /** @var \Amasty\Finder\Model\Finder $finder */
            $finder = $dropdown->getFinder();
            $countEmptyDropdowns = $finder->getCnt() - count($finderOptions);
            $lastOption = end($finderOptions);
            $isUniversal = (bool)$this->configHelper->getConfigValue('advanced/universal');
            $isUniversalLast = (bool)$this->configHelper->getConfigValue('advanced/universal_last');

            $this->finderRepository->addConditionToProductCollection(
                $collection,
                $lastOption->getValue(),
                $countEmptyDropdowns,
                $finder->getId(),
                $isUniversal,
                $isUniversalLast
            );
        }

        return $collection->getItems();
    }
}
