<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Finder
 */

/**
 * Copyright © 2015 Amasty. All rights reserved.
 */

namespace Amasty\Finder\Model\ResourceModel;

class ImportLog extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Model Initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('amasty_finder_import_file_log', 'file_id');
    }

    /**
     * @param $finderId
     * @return bool
     */
    public function hasIssetReplaceFile($finderId)
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from($this->getMainTable(), "COUNT(*)")
            ->where('finder_id = ?', (int) $finderId)
            ->where('file_name= ?', \Amasty\Finder\Model\Import::REPLACE_CSV);
        return (bool)$connection->fetchOne($select);
    }
}
