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

use Magento\Framework\Exception\NoSuchEntityException;

class RunFile extends \Amasty\Finder\Controller\Adminhtml\Finder
{
    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    private $jsonEncoder;

    /**
     * @var \Amasty\Finder\Model\Import
     */
    private $importModel;

    /**
     * RunFile constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Amasty\Finder\Api\ImportLogRepositoryInterface $logRepository
     * @param \Amasty\Finder\Api\ImportHistoryRepositoryInterface $importHistoryRepository
     * @param \Amasty\Finder\Api\ValueRepositoryInterface $valueRepository
     * @param \Amasty\Finder\Api\UniversalRepositoryInterface $universalRepository
     * @param \Amasty\Finder\Api\FinderRepositoryInterface $finderRepository
     * @param \Psr\Log\LoggerInterface $logInterface
     * @param \Amasty\Finder\Model\Import $importModel
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
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
        \Amasty\Finder\Model\Import $importModel,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder
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
     * @param $response
     * @return \Magento\Framework\App\Response\Http
     */
    private function sendResponse($response)
    {
        return $this->getResponse()->setBody($this->jsonEncoder->encode($response));
    }

    /**
     * @return \Magento\Framework\App\Response\Http
     */
    public function execute()
    {
        $message = '';
        try {
            $fileId = (int)$this->getRequest()->getParam('file_id');
            $fileLog = $this->logRepository->getById($fileId);
            if ($fileLog->getIsLocked()) {
                $message = __('File already running');
            }

        } catch (NoSuchEntityException $e) {
            $message = __('File not exists');
        }

        if ($message) {
            return $this->sendResponse([
                'isCompleted' => true,
                'message' => $message,
                'progress' => $fileLog->getProgress(),
            ]);

        }

        $countProcessedRows = 0;
        $this->importModel->runFile($fileLog, $countProcessedRows);

        $data = [];
        $data['isCompleted'] = (bool)$fileLog->getEndedAt();
        if ($data['isCompleted']) {
            if ($countProcessedRows) {
                $data['message'] = __('File imported successfully');
                $data['message'] .= __(' with %1 errors', $fileLog->getCountErrors());
            } else {
                $data['message'] =
                    __(
                        'The file is invalid, please see <a class="show-import-errors" data-url="%1">errors log</a> 
                        for details.',
                        $this->getUrl(
                            '*/finder/errors',
                            [
                                'file_id' => $fileLog->getFileLogHistoryId(),
                                'file_state' => \Amasty\Finder\Helper\Import::FILE_STATE_ARCHIVE
                            ]
                        )
                    );
            }
        } else {
            $data['message'] = __(
                'Imported %1 rows of total %2 rows (%3%)',
                $fileLog->getCountProcessingLines(),
                $fileLog->getCountLines(),
                $fileLog->getProgress()
            );
        }

        $data['progress'] = $fileLog->getProgress();

        return $this->sendResponse($data);
    }
}
