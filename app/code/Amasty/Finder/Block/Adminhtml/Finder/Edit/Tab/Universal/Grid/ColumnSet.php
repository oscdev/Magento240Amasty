<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Finder
 */


namespace Amasty\Finder\Block\Adminhtml\Finder\Edit\Tab\Universal\Grid;

class ColumnSet extends \Magento\Backend\Block\Widget\Grid\ColumnSet
{
    /**
     * Core registry
     *
     * @var \Amasty\Finder\Model\Finder $finder
     */
    private $finder;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Backend\Model\Widget\Grid\Row\UrlGeneratorFactory $generatorFactory,
        \Magento\Backend\Model\Widget\Grid\SubTotals $subtotals,
        \Magento\Backend\Model\Widget\Grid\Totals $totals,
        \Magento\Framework\Registry $registry,
        array $data
    ) {
        /** @var \Amasty\Finder\Model\Finder $finder */
        $this->finder = $registry->registry('current_amasty_finder_finder');
        parent::__construct($context, $generatorFactory, $subtotals, $totals, $data);
    }
}
