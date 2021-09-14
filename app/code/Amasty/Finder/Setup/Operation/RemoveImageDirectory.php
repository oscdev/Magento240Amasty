<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Finder
 */


namespace Amasty\Finder\Setup\Operation;

class RemoveImageDirectory
{
    /** @var \Amasty\Base\Helper\Deploy */
    private $deployHelper;

    public function __construct(\Amasty\Base\Helper\Deploy $deploy)
    {
        $this->deployHelper = $deploy;
    }

    /**
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     * @throws \Zend_Db_Exception
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function execute(\Magento\Framework\Setup\SchemaSetupInterface $setup)
    {
        $setup->getConnection()->dropColumn($setup->getTable('amasty_finder_finder'), 'images_directory');

        $this->deployHelper->deployFolder(dirname(__DIR__) . '/../pub');
    }
}
