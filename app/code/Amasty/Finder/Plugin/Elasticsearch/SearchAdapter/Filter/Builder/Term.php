<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Finder
 */

namespace Amasty\Finder\Plugin\Elasticsearch\SearchAdapter\Filter\Builder;

use Magento\Framework\Search\Request\FilterInterface as RequestFilterInterface;

class Term
{
    const SKU_FIELD = 'sku';

    /**
     * @var \Magento\Elasticsearch\Model\Adapter\FieldMapper\FieldMapperResolve
     */
    protected $fieldMapper = null;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        if(class_exists('\Magento\Elasticsearch\Model\Adapter\FieldMapper\FieldMapperResolver')) {
            $this->fieldMapper = $objectManager
                ->create('\Magento\Elasticsearch\Model\Adapter\FieldMapper\FieldMapperResolver');
        }
    }

    /**
     * @param $subject
     * @param callable $proceed
     * @param RequestFilterInterface $filter
     * @return array
     */
    public function aroundBuildFilter(
        $subject,
        callable $proceed,
        RequestFilterInterface $filter
    ){
        if (in_array($this->fieldMapper->getFieldName($filter->getField()), [self::SKU_FIELD])
            && !is_null($this->fieldMapper)
        ) {
            $filterQuery = [];
            if ($filter->getValue()) {
                $operator = is_array($filter->getValue()) ? 'terms' : 'term';
                $filterQuery []= [
                    $operator => [
                        $this->fieldMapper->getFieldName($filter->getField()) . '_value' => $filter->getValue(),
                    ],
                ];
            }
            return $filterQuery;
        }
        return $proceed($filter);
    }
}
