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

use Amasty\Finder\Model\Import as ImportModel;

class Upload extends \Amasty\Finder\Controller\Adminhtml\Finder
{
    use \Amasty\Finder\MyTrait\FinderController;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    private $jsonEncoder;

    /**
     * @var \Amasty\Finder\Model\Import
     */
    private $importModel;

    /**
     * Upload constructor.
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
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Amasty\Finder\Model\Import $importModel
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
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        ImportModel $importModel
    ) {
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
        $this->importModel = $importModel;
        $this->jsonEncoder = $jsonEncoder;
    }

    /**
     * @return \Magento\Framework\App\Response\Http
     */
    public function execute()
    {
        $finder = $this->_initFinder();
        $isDeleteExistingData = $this->getRequest()->getParam('delete_existing_data');
        $newFileName = $isDeleteExistingData ? ImportModel::REPLACE_CSV : null;
        $error = null;
        $content = "";

        try {
            $fileName = $this->importModel->upload('file', $finder->getId(), $newFileName);
            $content = __('The file has been uploaded');
            if ($fileName == ImportModel::REPLACE_CSV) {
                $content .= __(', all other files in the queue have been removed');
            }
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

        $response = ['status' => 'ok'];
        if ($error !== null) {
            $response = ["error" => $error];
        } else {
            $this->messageManager->addSuccess($content);
        }
        $response = $this->jsonEncoder->encode($response);
        return $this->getResponse()->setBody($response);
    }
}
