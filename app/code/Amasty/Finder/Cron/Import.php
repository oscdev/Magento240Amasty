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

class Import
{
    /**
     * @var \Amasty\Finder\Model\Import
     */
    private $importModel;

    /**
     * Import constructor.
     * @param \Amasty\Finder\Model\Import $importModel
     */
    public function __construct(
        \Amasty\Finder\Model\Import $importModel
    ) {
        $this->importModel = $importModel;
    }

    public function execute()
    {
        $this->importModel->runAll();
    }
}
