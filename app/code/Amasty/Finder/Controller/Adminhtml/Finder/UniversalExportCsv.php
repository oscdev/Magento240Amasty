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

use Magento\Framework\App\Filesystem\DirectoryList;

class UniversalExportCsv extends \Amasty\Finder\Controller\Adminhtml\Finder
{
    use \Amasty\Finder\MyTrait\FinderController;

    /** @var \Magento\Framework\App\Response\Http\FileFactory */
    private $fileFactory;

    /**
     * UniversalExportCsv constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Amasty\Finder\Api\ImportLogRepositoryInterface $logRepository
     * @param \Amasty\Finder\Api\ImportHistoryRepositoryInterface $importHistoryRepository
     * @param \Amasty\Finder\Api\ValueRepositoryInterface $valueRepository
     * @param \Amasty\Finder\Api\UniversalRepositoryInterface $universalRepository
     * @param \Amasty\Finder\Api\FinderRepositoryInterface $finderRepository
     * @param \Amasty\Finder\Api\DropdownRepositoryInterface $dropdownRepository
     * @param \Psr\Log\LoggerInterface $logInterface
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Amasty\Finder\Api\ImportLogRepositoryInterface $logRepository,
        \Amasty\Finder\Api\ImportHistoryRepositoryInterface $importHistoryRepository,
        \Amasty\Finder\Api\ValueRepositoryInterface $valueRepository,
        \Amasty\Finder\Api\UniversalRepositoryInterface $universalRepository,
        \Amasty\Finder\Api\FinderRepositoryInterface $finderRepository,
        \Amasty\Finder\Api\DropdownRepositoryInterface $dropdownRepository,
        \Psr\Log\LoggerInterface $logInterface,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory
    ) {
        $this->fileFactory = $fileFactory;
        parent::__construct(
            $context,
            $coreRegistry,
            $resultForwardFactory,
            $resultPageFactory,
            $logRepository,
            $importHistoryRepository,
            $valueRepository,
            $universalRepository,
            $finderRepository,
            $dropdownRepository,
            $logInterface
        );
    }

    /**
     * Export customer grid to CSV format
     *
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function execute()
    {
        $model = $this->_initFinder();

        $this->_view->loadLayout();
        $fileName = 'finder_' . $model->getId() . '_universal_products.csv';

        /** @var \Magento\Backend\Block\Widget\Grid\ExportInterface $exportBlock */
        $exportBlock = $this->_view->getLayout()->getChildBlock(
            'adminhtml.amasty.finder.finder.universal.grid',
            'grid.export'
        );
        return $this->fileFactory->create($fileName, $exportBlock->getCsvFile(), DirectoryList::VAR_DIR);
    }
}
