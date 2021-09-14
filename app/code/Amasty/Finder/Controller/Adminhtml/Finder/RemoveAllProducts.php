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

class RemoveAllProducts extends \Amasty\Finder\Controller\Adminhtml\Finder
{
    use \Amasty\Finder\MyTrait\FinderController;

    public function execute()
    {
        $finder = $this->_initFinder();
        try {
            $this->valueRepository->deleteOldData($finder);

            $this->messageManager->addSuccess(__('You deleted all products in the finder.'));
            $this->_redirect('amasty_finder/finder/edit', ['id' => $finder->getId()]);
            return;
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addError(
                __('We can\'t delete products right now. Please review the log and try again.') . $e->getMessage()
            );
            $this->logInterface->critical($e);
            $this->_redirect('amasty_finder/finder/edit', ['id' => $finder->getId()]);
            return;
        }
        $this->messageManager->addError(__('We can\'t find a products to delete.'));
        $this->_redirect('amasty_finder/finder/edit', ['id' => $finder->getId()]);
    }
}
