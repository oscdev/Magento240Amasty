<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Finder
 */

/**
 * Copyright © 2015 Amasty. All rights reserved.
 */

namespace Amasty\Finder\Controller\Adminhtml;

use Amasty\Finder\Api\ImportHistoryRepositoryInterface;
use Amasty\Finder\Api\ImportLogRepositoryInterface;

abstract class Finder extends \Magento\Backend\App\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /** @var \Magento\Backend\Model\View\Result\ForwardFactory */
    private $resultForwardFactory;

    /** @var \Magento\Framework\View\Result\PageFactory */
    protected $resultPageFactory;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logInterface;

    /**
     * @var ImportLogRepositoryInterface
     */
    protected $logRepository;

    /**
     * @var ImportHistoryRepositoryInterface
     */
    protected $importHistoryRepository;

    /**
     * @var \Amasty\Finder\Api\UniversalRepositoryInterface
     */
    protected $universalRepository;

    /**
     * @var \Amasty\Finder\Api\ValueRepositoryInterface
     */
    protected $valueRepository;

    /**
     * @var \Amasty\Finder\Api\FinderRepositoryInterface
     */
    protected $finderRepository;

    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $session;

    /**
     * @var \Amasty\Finder\Api\DropdownRepositoryInterface
     */
    protected $dropdownRepository;

    /**
     * Finder constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param ImportLogRepositoryInterface $logRepository
     * @param ImportHistoryRepositoryInterface $importHistoryRepository
     * @param \Amasty\Finder\Api\ValueRepositoryInterface $valueRepository
     * @param \Amasty\Finder\Api\UniversalRepositoryInterface $universalRepository
     * @param \Amasty\Finder\Api\FinderRepositoryInterface $finderRepository
     * @param \Amasty\Finder\Api\DropdownRepositoryInterface $dropdownRepository
     * @param \Psr\Log\LoggerInterface $logInterface
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
        \Psr\Log\LoggerInterface $logInterface
    ) {
        $this->coreRegistry = $coreRegistry;
        parent::__construct($context);
        $this->resultForwardFactory = $resultForwardFactory;
        $this->resultPageFactory = $resultPageFactory;
        $this->logInterface = $logInterface;
        $this->logRepository = $logRepository;
        $this->importHistoryRepository = $importHistoryRepository;
        $this->universalRepository = $universalRepository;
        $this->valueRepository = $valueRepository;
        $this->finderRepository = $finderRepository;
        $this->session = $context->getSession();
        $this->dropdownRepository = $dropdownRepository;
    }

    /**
     * Initiate action
     *
     * @return $this
     */
    protected function _initAction()
    {
        $this->_view->loadLayout();
        $this->_setActiveMenu('Amasty_Finder::finder')->_addBreadcrumb(__('Finder'), __('Finder'));
        return $this;
    }

    /**
     * Determine if authorized to perform group actions.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Amasty_Finder::finder');
    }
}
