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

class ImportErrors extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Model Initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('amasty_finder_import_file_log_errors', 'error_id');
    }

    /**
     * @param $fileId
     * @param $historyFileId
     */
    public function archiveErrorHistory($fileId, $historyFileId)
    {
        $adapter = $this->getConnection();
        $adapter->update(
            $this->getMainTable(),
            ['import_file_log_id' => null, 'import_file_log_history_id' => $historyFileId],
            'import_file_log_id = ' . $fileId
        );
    }
}
