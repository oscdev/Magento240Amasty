<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Finder
 */


namespace Amasty\Finder\Block\Adminhtml\Finder\Edit\Tab\Products\Grid;

class Massaction extends \Magento\Backend\Block\Widget\Grid\Massaction
{
    const MASSACTION_LIMIT = 10000;

    /**
     * Get grid ids in JSON format.
     *
     * @return string
     */
    public function getGridIdsJson()
    {
        if (!$this->getUseSelectAll()) {
            return '';
        }
        /** @var \Magento\Framework\Data\Collection $allIdsCollection */
        $allIdsCollection = clone $this->getParentBlock()->getCollection();

        if ($this->getMassactionIdField()) {
            $massActionIdField = $this->getMassactionIdField();
        } else {
            $massActionIdField = $this->getParentBlock()->getMassactionIdField();
        }

        if ($allIdsCollection instanceof AbstractDb) {
            $allIdsCollection->getSelect()->limit(self::MASSACTION_LIMIT);
            $allIdsCollection->clear();
        }

        $allIdsCollection->setPageSize(self::MASSACTION_LIMIT);
        $gridIds = array_map(function ($item) use ($massActionIdField) {
            return $item->getData($massActionIdField);
        }, $allIdsCollection->getItems());

        return !empty($gridIds) ? join(',', $gridIds) : '';
    }
}
