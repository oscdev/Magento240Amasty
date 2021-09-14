<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Finder
 */

/**
 * Copyright © 2015 Amasty. All rights reserved.
 */
namespace Amasty\Finder\Cron;

class ImportLogHistoryCleaner
{
    /**
     * @var \Amasty\Finder\Helper\Config
     */
    private $configHelper;

    /**
     * @var \Amasty\Finder\Api\ImportHistoryRepositoryInterface
     */
    private $historyRepository;

    /**
     * ImportLogHistoryCleaner constructor.
     * @param \Amasty\Finder\Helper\Config $configHelper
     * @param \Amasty\Finder\Api\ImportHistoryRepositoryInterface $historyRepository
     */
    public function __construct(
        \Amasty\Finder\Helper\Config $configHelper,
        \Amasty\Finder\Api\ImportHistoryRepositoryInterface $historyRepository
    ) {
        $this->configHelper = $configHelper;
        $this->historyRepository = $historyRepository;
    }

    public function execute()
    {
        $lifetime = $this->configHelper->getConfigValue('import/archive_lifetime');
        $date = strftime('%Y-%m-%d %H:%M:%S', strtotime("-{$lifetime} days"));

        $this->historyRepository->clearLogHistory($date);
    }
}
