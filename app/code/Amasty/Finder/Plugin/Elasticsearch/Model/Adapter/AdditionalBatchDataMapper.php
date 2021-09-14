<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Finder
 */


namespace Amasty\Finder\Plugin\Elasticsearch\Model\Adapter;

/**
 * Class AdditionalBatchDataMapper
 * @package Amasty\Finder\Plugin\Elasticsearch\Model\Adapter
 */
class AdditionalBatchDataMapper
{
    const FIELD_NAME = 'sku_value';
    const DOCUMENT_FIELD_NAME = 'sku';
    const INDEX_DOCUMENT = 'document';

    /**
     * Prepare index data for using in search engine metadata.
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param $subject
     * @param callable $proceed
     * @param array $documentData
     * @param $storeId
     * @param array $context
     * @return array
     */
    public function aroundMap(
        $subject,
        callable $proceed,
        array $documentData,
        $storeId,
        $context = []
    ) {
        $documentData = $proceed($documentData, $storeId, $context);
        foreach ($documentData as $productId => $document) {
            $value = $document[self::DOCUMENT_FIELD_NAME];
            $document[self::FIELD_NAME] = $value;
            $documentData[$productId] = $document;
        }
        return $documentData;
    }
}
