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

use Amasty\Finder\Api\Data\DropdownInterface;

class Save extends \Amasty\Finder\Controller\Adminhtml\Finder
{
    /**
     * Dispatch request
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        if ($this->getRequest()->getPostValue()) {
            try {
                $model = $this->finderRepository->getFinderModel();
                $data = $this->getRequest()->getPostValue();
                $inputFilter = new \Zend_Filter_Input([], [], $data);
                $data = $inputFilter->getUnescaped();
                $finderId = $this->getRequest()->getParam('id');
                if ($finderId) {
                    $model = $this->finderRepository->getById($finderId);
                    if ($finderId != $model->getId()) {
                        throw new \Magento\Framework\Exception\LocalizedException(__('The wrong item is specified.'));
                    }
                }
                if (isset($data['categories'])) {
                    $data['categories'] = ',' . implode(',', $data['categories']) . ',';
                } else {
                    $data['categories'] = '';
                }
                $model->addData($data);
                $this->session->setPageData($model->getData());
                $model->save();

                if ($finderId) {
                    foreach ($model->getDropdowns() as $dropdown) {
                        $prefix = 'dropdown_' . $dropdown->getId();
                        $dropdown->addData([
                            DropdownInterface::NAME => $model->getData($prefix . '_name'),
                            DropdownInterface::SORT => $model->getData($prefix . '_sort'),
                            DropdownInterface::RANGE => $model->getData($prefix . '_range'),
                            DropdownInterface::DISPLAY_TYPE => $model->getData($prefix . '_display_type')
                        ]);
                        $dropdown->save();
                    }
                } else {
                    for ($i = 0; $i < $model->getCnt(); ++$i) {
                        /** @var $dropdown \Amasty\Finder\Model\Dropdown */
                        $dropdown = $this->dropdownRepository->getDropdownModel();
                        $dropdown->setPos($i);
                        $dropdown->setFinderId($model->getId());
                        $this->dropdownRepository->save($dropdown);
                    }
                }

                $this->messageManager->addSuccess(__('You saved the item.'));
                $this->session->setPageData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('amasty_finder/*/edit', ['id' => $model->getId()]);
                    return;
                }
                $this->_redirect('amasty_finder/*/');
                return;
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $finderId = (int)$this->getRequest()->getParam('id');
                if (!empty($finderId)) {
                    $this->_redirect('amasty_finder/*/edit', ['id' => $finderId]);
                } else {
                    $this->_redirect('amasty_finder/*/new');
                }
                return;
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $this->messageManager->addErrorMessage(
                    __('Something went wrong while saving the item data. Please review the error log.')
                );
                $this->logInterface->critical($e);
                $this->session->setPageData($data);
                $this->_redirect('amasty_finder/*/edit', ['id' => $this->getRequest()->getParam('id')]);
                return;
            }
        }
        $this->_redirect('amasty_finder/*/');
    }
}
