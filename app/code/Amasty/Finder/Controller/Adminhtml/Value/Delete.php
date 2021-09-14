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

use Magento\Framework\Exception\LocalizedException;

class Delete extends \Amasty\Finder\Controller\Adminhtml\Value
{
    /**
     * Dispatch request
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        $setterId = $this->getRequest()->getParam('id');
        if ($setterId) {
            try {
                $this->_initModel();
                $this->valueRepository->deleteById($setterId, $this->model);
                $this->messageManager->addSuccess(__('You have deleted the item.'));
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(
                    __('We can\'t delete item right now. Please review the log and try again.')
                );
                $this->logger->critical($e);
            }
        } else {
            $this->messageManager->addErrorMessage(__('We can\'t find a item to delete.'));
        }
        $this->_redirect('amasty_finder/finder/edit', ['id' => $this->model->getId()]);
    }
}
