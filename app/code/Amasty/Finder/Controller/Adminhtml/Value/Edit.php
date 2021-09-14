<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Finder
 */


namespace Amasty\Finder\Controller\Adminhtml\Value;

use Magento\Framework\Exception\NoSuchEntityException;

class Edit extends \Amasty\Finder\Controller\Adminhtml\Value
{
    const CURRENT_AMASTY_FINDER_VALUE = 'current_amasty_finder_value';

    /**
     * Dispatch request
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        $this->_initModel();

        $newId = $this->getRequest()->getParam('id');
        $setterId = $this->model->newSetterId($newId);
        $model = $this->valueRepository->getValueModel();

        if ($setterId) {
            try {
                $model = $this->valueRepository->getById($setterId);
            } catch (NoSuchEntityException $e) {
                $this->messageManager->addError(__('Record does not exist'));
                $this->_redirect('amasty_finder/finder/edit', ['id' => $this->model->getId()]);
                return;
            }
        }
        // set entered data if was error when we do save
        $data = $this->session->getPageData(true);
        if (!empty($data)) {
            $model->addData($data);
        }
        $model->setFinder($this->model);
        $this->coreRegistry->register(self::CURRENT_AMASTY_FINDER_VALUE, $model);
        $this->_initAction();

        if ($model->getId()) {
            $title = __('Edit Product');
        } else {
            $title = __("Add new Product");
        }
        $this->_view->getPage()->getConfig()->getTitle()->prepend($title);

        $this->_view->renderLayout();
    }
}
