<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Finder
 */


namespace Amasty\Finder\Setup\Operation;

class MoveToAdvanced
{
    /**
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     * @throws \Zend_Db_Exception
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function execute(\Magento\Framework\Setup\SchemaSetupInterface $setup)
    {
        $connection = $setup->getConnection();
        $tableName = $setup->getTable('core_config_data');
        $changedSettings = [
            '"amfinder/general/universal"',
            '"amfinder/general/universal_last"',
            '"amfinder/general/auto_submit"',
            '"amfinder/general/clear_other_conditions"',
            '"amfinder/general/redirect_single_product"',
            '"amfinder/general/is_chosen_enable"',
            '"amfinder/general/show_active_finder_options"',
            '"amfinder/general/name_finder_options_tab"',
        ];

        $whereSettings = '';
        foreach ($changedSettings as $setting) {
            $whereSettings .= '"' . $setting . '",';
        }

        $select = $setup->getConnection()->select()
            ->from($tableName, ['config_id','path'])
            ->where('path IN (' . implode(',', $changedSettings) . ')');

        $settings = $connection->fetchPairs($select);

        foreach ($settings as $key => $value) {
            $value = str_replace('general', 'advanced', $value);
            $connection->update($tableName, ['path' => $value], ['config_id = ?' => $key]);
        }
    }
}
