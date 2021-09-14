<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Finder
 */

/**
 * Copyright © 2016 Amasty. All rights reserved.
 */

namespace Amasty\Finder\Block\Adminhtml\Finder\Edit\Tab;

class AbstractGrid extends \Magento\Backend\Block\Widget\Grid
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    private $coreRegistry;

    /**
     * @var \Amasty\Finder\Model\ResourceModel\ImportLog\Collection
     */
    protected $importCollection;

    /**
     * @var \Amasty\Finder\Model\Import
     */
    protected $importModel;

    /**
     * @var \Amasty\Finder\Model\ResourceModel\Value\Collection
     */
    protected $productCollection;

    /**
     * @var \Amasty\Finder\Model\ResourceModel\Universal\Collection
     */
    protected $universalCollection;

    /**
     * @var \Amasty\Finder\Model\ResourceModel\ImportHistory\Collection
     */
    protected $importHistoryCollection;

    /**
     * AbstractGrid constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Framework\Registry $registry
     * @param \Amasty\Finder\Model\ResourceModel\ImportLog\Collection $importCollection
     * @param \Amasty\Finder\Model\ResourceModel\Value\Collection $productCollection
     * @param \Amasty\Finder\Model\ResourceModel\Universal\Collection $universalCollection
     * @param \Amasty\Finder\Model\ResourceModel\ImportHistory\Collection $importHistoryCollection
     * @param \Amasty\Finder\Model\Import $importModel
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Framework\Registry $registry,
        \Amasty\Finder\Model\ResourceModel\ImportLog\Collection $importCollection,
        \Amasty\Finder\Model\ResourceModel\Value\Collection $productCollection,
        \Amasty\Finder\Model\ResourceModel\Universal\Collection $universalCollection,
        \Amasty\Finder\Model\ResourceModel\ImportHistory\Collection $importHistoryCollection,
        \Amasty\Finder\Model\Import $importModel,
        array $data = []
    ) {
        $this->coreRegistry = $registry;
        $this->importCollection = $importCollection;
        $this->productCollection = $productCollection;
        $this->universalCollection = $universalCollection;
        $this->importHistoryCollection = $importHistoryCollection;
        $this->importModel = $importModel;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return \Amasty\Finder\Model\Finder
     */
    protected function getFinder()
    {
        $finder = $this->coreRegistry->registry('current_amasty_finder_finder');
        return $finder;
    }
}
