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

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\NoSuchEntityException;

class Errors extends \Amasty\Finder\Controller\Adminhtml\Finder
{
    /**
     * @return \Magento\Framework\View\Result\Layout|void
     */
    public function execute()
    {
        $fileId = (int)$this->getRequest()->getParam('file_id');
        $fileState = $this->getRequest()->getParam('file_state');
        try {
            if ($fileState == \Amasty\Finder\Helper\Import::FILE_STATE_PROCESSING) {
                $model = $this->logRepository->getById($fileId);
            } else {
                $model = $this->importHistoryRepository->getById($fileId);
            }
        } catch (NoSuchEntityException $e) {
            $this->messageManager->addError(__('Record does not exist.'));
            $this->_redirect('amasty_finder/finder/');
            return;
        }
        $this->coreRegistry->register('amfinder_importFile', $model);

        /** @var \Magento\Framework\View\Result\Layout $resultLayout */
        $resultLayout = $this->resultFactory->create(ResultFactory::TYPE_LAYOUT);
        return $resultLayout;
    }
}
