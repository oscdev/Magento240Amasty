<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Finder
 */

/**
 * Copyright © 2015 Amasty. All rights reserved.
 */

namespace Amasty\Finder\Block\Adminhtml\Finder\Edit\Tab\Import;

class ErrorsGrid extends \Magento\Backend\Block\Widget\Grid
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    private $coreRegistry;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Framework\Registry $registry,
        array $data
    ) {
        $this->coreRegistry = $registry;
        parent::__construct($context, $backendHelper, $data);

        /** @var \Amasty\Finder\Model\ResourceModel\ImportErrors\Collection $collection */
        $collection = $this->coreRegistry->registry('amfinder_importFile')->getErrorsCollection();
        $this->setCollection($collection);
    }
}
