<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Finder
 */


namespace Amasty\Finder\Controller\Adminhtml\Finder;

class ImportImages extends \Amasty\Finder\Controller\Adminhtml\Finder
{
    /**
     * Dispatch request
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        try {
            $model = $this->finderRepository->getFinderModel();
            $finderId = $this->getRequest()->getParam('id');
            if ($finderId) {
                $model = $this->finderRepository->getById($finderId);
                if ($finderId != $model->getId()) {
                    throw new \Magento\Framework\Exception\LocalizedException(__('The wrong item is specified.'));
                }
            }

            $errorMesages = $model->importImages($this->getRequest()->getFiles('importimages_file'));
            if ($errorMesages) {
                $errorMesages = implode(', ', $errorMesages);
                $this->messageManager->addErrorMessage($errorMesages);
            }


            $this->_redirect('amasty_finder/*/edit', ['id' => $finderId]);
            return;
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $finderId = (int)$this->getRequest()->getParam('id');
            if (!empty($finderId)) {
                $this->_redirect('amasty_finder/*/edit', ['id' => $finderId]);
            } else {
                $this->_redirect('amasty_finder/*/new');
            }
            return;
        }
    }
}
