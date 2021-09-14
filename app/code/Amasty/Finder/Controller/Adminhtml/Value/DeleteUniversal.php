<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Finder
 */

/**
 * Copyright © 2015 Amasty. All rights reserved.
 */
namespace Amasty\Finder\Controller\Adminhtml\Value;

class DeleteUniversal extends \Amasty\Finder\Controller\Adminhtml\Value
{
    /**
     * Dispatch request
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        $universalId = $this->getRequest()->getParam('id');
        $finderId = $this->getRequest()->getParam('finder_id');

        if ($universalId) {
            try {
                $this->universalRepository->deleteById($universalId);

                $this->messageManager->addSuccess(__('You deleted the item.'));
                $this->_redirect('amasty_finder/finder/edit', [
                    'id' => $finderId,
                    '_fragment' => 'amasty_finder_finder_edit_tabs_universal_section_content'
                ]);
                return;
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addError(
                    __('We can\'t delete item right now. Please review the log and try again.')
                );
                $this->logger->critical($e);
                $this->_redirect('amasty_finder/*/');
                return;
            }
        }
        $this->messageManager->addError(__('We can\'t find a item to delete.'));
        $this->_redirect('amasty_finder/*/');
    }
}
