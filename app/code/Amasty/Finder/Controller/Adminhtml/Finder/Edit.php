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

use Magento\Framework\App\ResponseInterface;

class Edit extends \Amasty\Finder\Controller\Adminhtml\Finder
{

    /**
     * Dispatch request
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        $finderId = $this->getRequest()->getParam('id');
        $model = $this->finderRepository->getFinderModel();

        if ($finderId) {
            $model = $this->finderRepository->getById($finderId);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This item no longer exists.'));
                $this->_redirect('amasty_finder/*');
                return;
            }
        }
        // set entered data if was error when we do save
        $data = $this->session->getPageData(true);
        if (!empty($data)) {
            $model->addData($data);
        }
        $this->coreRegistry->register('current_amasty_finder_finder', $model);
        $this->_initAction();
        if ($model->getId()) {
            $title = __('Edit Parts Finder `%1`', $model->getName());
        } else {
            $title = __("Add new Parts Finder");
        }
        $this->_view->getPage()->getConfig()->getTitle()->prepend($title);

        $this->_view->renderLayout();
    }
}
