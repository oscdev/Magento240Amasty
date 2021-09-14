<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Finder
 */


/**
 * Copyright © 2015 Amasty. All rights reserved.
 */
namespace Amasty\Finder\Model\Import;

abstract class ImportLogAbstract extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @var \Amasty\Finder\Api\ImportErrorsRepositoryInterface
     */
    protected $errorsRepository;

    /**
     * @var \Amasty\Finder\Api\ImportHistoryRepositoryInterface
     */
    protected $historyRepository;

    /**
     * @var \Amasty\Finder\Helper\Import
     */
    protected $helper;

    /**
     * ImportLogAbstract constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Amasty\Finder\Api\ImportErrorsRepositoryInterface $errorsRepository
     * @param \Amasty\Finder\Api\ImportHistoryRepositoryInterface $historyRepository
     * @param \Amasty\Finder\Helper\Import $helper
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Amasty\Finder\Api\ImportErrorsRepositoryInterface $errorsRepository,
        \Amasty\Finder\Api\ImportHistoryRepositoryInterface $historyRepository,
        \Amasty\Finder\Helper\Import $helper,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->errorsRepository = $errorsRepository;
        $this->historyRepository = $historyRepository;
        $this->helper = $helper;
    }

    /**
     * @return string
     */
    abstract public function getFileState();

    /**
     * @return string
     */
    abstract public function getFieldInErrorLog();

    /**
     * @return \Amasty\Finder\Model\ResourceModel\ImportErrors\Collection
     */
    public function getErrorsCollection()
    {
        return $this->errorsRepository->getErrorsCollection($this->getFieldInErrorLog(), $this->getId());
    }
}
