<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Finder
 */

/**
 * Copyright © 2015 Amasty. All rights reserved.
 */

namespace Amasty\Finder\Controller\Adminhtml\Finder;

class DeleteHistory extends \Amasty\Finder\Controller\Adminhtml\Finder
{
    public function execute()
    {
        $fileId = $this->getRequest()->getParam('file_id');
        if ($fileId) {
            try {
                $fileLog = $this->importHistoryRepository->getById($fileId);
                $this->importHistoryRepository->delete($fileLog);
                $this->messageManager->addSuccess(__('You deleted the item.'));
                $finderId = $fileLog->getFinderId();
                $this->_redirect(
                    'amasty_finder/finder/edit',
                    ['id' => $finderId, '_fragment' => 'amasty_finder_finder_edit_tabs_import_history_section_content']
                );
                return;
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addError(
                    __('We can\'t delete item right now. Please review the log and try again.')
                );
                $this->logInterface->critical($e);
                $this->_redirect('amasty_finder/*/');
                return;
            }
        }
        $this->messageManager->addError(__('We can\'t find a item to delete.'));
        $this->_redirect('amasty_finder/*/');
    }
}
