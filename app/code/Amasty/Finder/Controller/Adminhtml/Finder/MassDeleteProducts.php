<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Finder
 */


namespace Amasty\Finder\Controller\Adminhtml\Finder;

class MassDeleteProducts extends \Amasty\Finder\Controller\Adminhtml\Finder
{
    use \Amasty\Finder\MyTrait\FinderController;

    public function execute()
    {
        $finder = $this->_initFinder();
        $finderId = $finder->getId();
        $ids = $this->getRequest()->getParam('value_ids');
        if ($ids) {
            try {
                $this->valueRepository->deleteByIds($ids, $finder);

                $this->messageManager->addSuccess(__('You have deleted the product(s).'));
                $this->_redirect(
                    'amasty_finder/finder/edit',
                    ['id' => $finderId, '_fragment' => 'amasty_finder_finder_edit_tabs_products_section_content']
                );
                return;
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addError(
                    __('We can\'t delete the product(s) right now. Please review the log and try again.')
                );
                $this->logInterface->critical($e);
                $this->_redirect(
                    'amasty_finder/finder/edit',
                    ['id' => $finderId, '_fragment' => 'amasty_finder_finder_edit_tabs_products_section_content']
                );
                return;
            }
        }
        $this->messageManager->addError(__('We can\'t find the product(s) to delete.'));
        $this->_redirect(
            'amasty_finder/finder/edit',
            ['id' => $finderId, '_fragment' => 'amasty_finder_finder_edit_tabs_products_section_content']
        );
    }
}
