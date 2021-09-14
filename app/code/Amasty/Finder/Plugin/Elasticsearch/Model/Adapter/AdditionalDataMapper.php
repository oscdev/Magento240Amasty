<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Finder
 */


namespace Amasty\Finder\Plugin\Elasticsearch\Model\Adapter;

/**
 * Class AdditionalDataMapper
 * @package Amasty\Finder\Plugin\Elasticsearch\Model\Adapter
 */
class AdditionalDataMapper
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
     * @param $productId
     * @param array $indexData
     * @param $storeId
     * @param array $context
     * @return array
     */
    public function aroundMap(
        $subject,
        callable $proceed,
        $productId,
        array $indexData,
        $storeId,
        $context = []
    ) {
        $document = $proceed($productId, $indexData, $storeId, $context);

        $value = isset($document[self::DOCUMENT_FIELD_NAME])
            ? $document[self::INDEX_DOCUMENT][self::DOCUMENT_FIELD_NAME] : $indexData[self::FIELD_NAME];
        $document[self::FIELD_NAME] = $value;

        return $document;
    }
}
