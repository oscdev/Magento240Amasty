<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Finder
 */


namespace Amasty\Finder\Plugin\Elasticsearch\Model\Adapter;

/**
 * Class AdditionalFieldMapper
 * @package Amasty\Shopby\Plugin\Elasticsearch\Model\Adapter
 */
class AdditionalFieldMapper
{
    const ES_DATA_TYPE_STRING = 'string';
    const FIELD_NAME = 'sku_value';

    /**
     * @param mixed $subject
     * @param array $result
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetAllAttributesTypes($subject, array $result)
    {
        $result[self::FIELD_NAME] = ['type' => self::ES_DATA_TYPE_STRING, "index" => "not_analyzed"];
        return $result;
    }
}
