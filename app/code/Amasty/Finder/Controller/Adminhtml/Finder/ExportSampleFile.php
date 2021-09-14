<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Finder
 */


namespace Amasty\Finder\Controller\Adminhtml\Finder;

use Magento\Framework\App\Filesystem\DirectoryList;

class ExportSampleFile extends \Magento\Backend\App\Action
{
    const EXAMPLE_UNIVERSAL = 'universal';
    const EXAMPLE_IMAGE = 'image';

    const AM_CSV_EXAMPLE = 'amasty/finder/am_csv_example.csv';

    /** @var \Magento\Framework\App\Response\Http\FileFactory */
    private $fileFactory;

    /**
     * @var DirectoryList
     */
    private $directoryList;

    /**
     * @var \Magento\Framework\File\Csv
     */
    private $csvWriter;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
        \Magento\Framework\File\Csv $csvWriter
    ) {
        $this->fileFactory = $fileFactory;
        $this->directoryList = $directoryList;
        $this->csvWriter = $csvWriter;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function execute()
    {
        $filePath = $this->directoryList->getPath(DirectoryList::MEDIA) . "/" . self::AM_CSV_EXAMPLE;

        $this->csvWriter
            ->setEnclosure('"')
            ->setDelimiter(',')
            ->saveData($filePath, $this->getFileInfo($this->getRequest()->getParam('type')));

        $file = $this->fileFactory->create(
            self::AM_CSV_EXAMPLE,
            [
                'type'  => "filename",
                'value' => self::AM_CSV_EXAMPLE,
                'rm'    => true,
            ],
            DirectoryList::MEDIA,
            'text/csv',
            null
        );

        return $file;
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

    /**
     * @param string $type
     * @return array
     */
    private function getFileInfo($type)
    {
        if ($type == self::EXAMPLE_UNIVERSAL) {
            $data = [['SKU'], ['SKU1'], ['SKU2'], ['SKU3'], ['...']];
        } else {
            $data = [['option_name', 'image_name'], ['option_name_1', 'image_name_1']];
        }

        return $data;
    }
}
