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

abstract class Value extends \Magento\Backend\App\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /** @var \Amasty\Finder\Model\Finder */
    protected $model;

    /**
     * @var \Amasty\Finder\Api\FinderRepositoryInterface
     */
    protected $finderRepository;

    /**
     * @var \Amasty\Finder\Api\ValueRepositoryInterface
     */
    protected $valueRepository;

    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $session;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \Amasty\Finder\Api\UniversalRepositoryInterface
     */
    protected $universalRepository;

    /**
     * Value constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Amasty\Finder\Api\FinderRepositoryInterface $finderRepository
     * @param \Amasty\Finder\Api\ValueRepositoryInterface $valueRepository
     * @param \Amasty\Finder\Api\UniversalRepositoryInterface $universalRepository
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Amasty\Finder\Api\FinderRepositoryInterface $finderRepository,
        \Amasty\Finder\Api\ValueRepositoryInterface $valueRepository,
        \Amasty\Finder\Api\UniversalRepositoryInterface $universalRepository,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->finderRepository = $finderRepository;
        $this->valueRepository = $valueRepository;
        $this->session = $context->getSession();
        $this->logger = $logger;
        $this->universalRepository = $universalRepository;
        parent::__construct($context);
    }

    /**
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

    protected function _initModel()
    {
        $finderId = $this->getRequest()->getParam('finder_id');
        $this->model = $this->finderRepository->getById($finderId);
        if (!$this->model->getId()) {
            $this->_redirect('amasty_finder/finder/');
            return;
        }
        $this->coreRegistry->register('current_amasty_finder_finder', $this->model);
    }
}
