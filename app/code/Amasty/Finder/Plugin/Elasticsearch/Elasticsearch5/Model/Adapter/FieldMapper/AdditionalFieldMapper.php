<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Finder
 */


namespace Amasty\Finder\Plugin\Elasticsearch\Elasticsearch5\Model\Adapter\FieldMapper;

class AdditionalFieldMapper
{
    const FIELD_NAME_SKU_VALUE = 'sku_value';
    const ATTRIBUTE_TYPE_KEYWORD = 'keyword';
    const ATTRIBUTE_TYPE_TEXT = 'text';
    const FIELD_NAME_SKU = 'sku';

    /**
     * @param mixed $subject
     * @param array $result
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetAllAttributesTypes($subject, array $result)
    {
        $result[self::FIELD_NAME_SKU_VALUE] = ['type' => self::ATTRIBUTE_TYPE_KEYWORD];
        $result[self::FIELD_NAME_SKU] = ['type' => self::ATTRIBUTE_TYPE_TEXT, "fielddata" => true];
        return $result;
    }
}
