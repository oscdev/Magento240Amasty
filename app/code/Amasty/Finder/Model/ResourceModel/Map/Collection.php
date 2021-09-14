<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Finder
 */


namespace Amasty\Finder\Model\ResourceModel\Map;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Amasty\Finder\Model\Map::class, \Amasty\Finder\Model\ResourceModel\Map::class);
    }

    /**
     * @param $productId
     * @param $dropdowns
     * @return $this
     */
    public function getDependsValues($productId, $dropdowns)
    {
        $dropdownsCount = count($dropdowns);

        $select = $this->getSelect();

        $select->joinInner(
            ['amfinder_value_0' => $this->getTable('amasty_finder_value')],
            'amfinder_value_0.value_id = main_table.value_id'
        );

        $columns['value_name'] = 'amfinder_value_0.name';
        for ($i = 1; $i < $dropdownsCount; $i++) {
            $select->joinInner(
                ['amfinder_value_' . $i => $this->getTable('amasty_finder_value')],
                'amfinder_value_' . $i . '.value_id = amfinder_value_' . ($i - 1) . '.parent_id'
            );
            $columns['value_name' . $i] = 'amfinder_value_' . $i . '.name';
        }

        $select->where('pid = ?', $productId);
        $select->where('amfinder_value_' . ($i - 1) . '.dropdown_id IN (' . implode(",", $dropdowns) . ')');
        $select->columns($columns);

        return $this;
    }
}
