<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Finder
 */


namespace Amasty\Finder\Model\Finder;

use Magento\Catalog\Model\ResourceModel\Product\Collection;

class SearchCriteriaBuilder
{
    /**
     * @var array
     */
    private $aggregation = [];

    /**
     * @var \Amasty\Finder\Helper\Config
     */
    private $configHelper;

    public function __construct(
        \Amasty\Finder\Helper\Config $configHelper
    ) {
        $this->configHelper = $configHelper;
    }

    /**
     * @param Collection $collection
     * @param string $filter
     * @param mixed $filterCondition
     * @return $this
     */
    public function addCollectionFilter(Collection $collection, $filter, $filterCondition)
    {
        $entityIds = [];
        if (isset($this->aggregation[$filter])) {
            if (!$result = array_intersect($this->aggregation[$filter], (array)$filterCondition)) {
                $result = [null];
            }
            $this->aggregation[$filter] = $result;
        } else {
            $this->aggregation[$filter] = (array)$filterCondition;
        }

        foreach ($this->aggregation[$filter] as $key => $value) {
            $key = $this->configHelper->isMysqlEngine() ? 'key_' . $key : $key;
            $entityIds[$key] = $value;
        }

        $collection->addFieldToFilter($filter, $entityIds);

        return $this;
    }
}
