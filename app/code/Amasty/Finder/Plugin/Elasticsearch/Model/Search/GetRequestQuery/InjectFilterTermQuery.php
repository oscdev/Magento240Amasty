<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Finder
 */


namespace Amasty\Finder\Plugin\Elasticsearch\Model\Search\GetRequestQuery;

use Magento\Framework\Search\Request\QueryInterface;

class InjectFilterTermQuery
{
    const SKU_FIELD = 'sku';

    /**
     * @inheritdoc
     */
    public function aroundExecute(
        $subject,
        callable $proceed,
        array $elasticQuery,
        QueryInterface $request,
        $conditionType
    ) {
        /** @var \Magento\Framework\Search\Request\Filter\Term $filter */
        $filter = $request->getReference();
        if ($filter->getValue() && $filter->getField() == self::SKU_FIELD) {
            if (!isset($elasticQuery['bool'][$conditionType])) {
                $elasticQuery['bool'][$conditionType] = [];
            }

            $filterType = is_array($filter->getValue()) ? 'terms' : 'term';
            $elasticQuery['bool'][$conditionType][] = [
                $filterType => [
                    $filter->getField() . '_value' => $filter->getValue()
                ]
            ];
            return $elasticQuery;
        }
        return $proceed($elasticQuery, $request, $conditionType);

    }
}
