<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Finder
 */


namespace Amasty\Finder\Model\ResourceModel;

use Amasty\Finder\Api\DropdownRepositoryInterface;
use Amasty\Finder\Api\FinderRepositoryInterface;
use Amasty\Finder\Model\FileValidator;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\File\UploaderFactory;
use Magento\Framework\Filesystem;
use Amasty\Finder\Model\Finder as FinderModel;

class Value extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    const POS_DROPDOWN_ID = 1;
    const POS_VALUE_ID = 2;
    const POS_END_LABEL_FLAG = 6;
    const PARTS_DROPDOWN_KEY = 3;

    /**
     * @var FinderRepositoryInterface
     */
    private $finderRepository;

    /**
     * @var DropdownRepositoryInterface
     */
    private $dropdownRepository;

    /**
     * @var \Magento\Framework\Filesystem\Io\File
     */
    private $file;

    /**
     * @var Filesystem\Driver\File
     */
    private $fileDriver;

    /**
     * @var Filesystem
     */
    protected $fileSystem;

    /**
     * @var UploaderFactory
     */
    protected $uploaderFactory;

    /**
     * @var FileValidator
     */
    private $fileValidator;

    /**
     * @var string
     */
    private $rootDirectory;

    const IMAGES_DIR = '/amasty/finder/images/';

    /**
     * Model Initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('amasty_finder_value', 'value_id');
    }

    public function __construct(
        \Amasty\Finder\Api\FinderRepositoryInterface $finderRepository,
        \Amasty\Finder\Api\DropdownRepositoryInterface $dropdownRepository,
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
        \Magento\Framework\Filesystem\Io\File $file,
        \Magento\Framework\Filesystem\Driver\File $fileDriver,
        Filesystem $fileSystem,
        UploaderFactory $uploaderFactory,
        FileValidator $fileValidator,
        $connectionName = null
    ) {
        $this->finderRepository = $finderRepository;
        $this->dropdownRepository = $dropdownRepository;
        $this->rootDirectory = $directoryList->getPath(DirectoryList::MEDIA);
        $this->file = $file;
        $this->fileDriver = $fileDriver;
        $this->fileSystem = $fileSystem;
        $this->uploaderFactory = $uploaderFactory;
        $this->fileValidator = $fileValidator;
        parent::__construct($context, $connectionName);
    }

    /**
     * @param array $data
     * @return int
     */
    public function saveNewFinder(array $data)
    {
        $connection = $this->getConnection();

        $insertData = [];
        $parentId = 0;
        $deleteData = [];
        foreach ($data as $element => $value) {
            if (strpos($element, 'image_delete_') !== false) {
                $dropdownIdForDelete = substr($element, strripos($element, '_') + 1);
                $deleteData[$dropdownIdForDelete] = $dropdownIdForDelete;
            }

            if (substr($element, 0, self::POS_END_LABEL_FLAG) == 'label_') {
                $ids = explode('_', $element);
                $dropdownId = count($ids) == self::PARTS_DROPDOWN_KEY
                    ? $ids[self::POS_DROPDOWN_ID]
                    : substr($element, self::POS_END_LABEL_FLAG);
                $valueId = isset($ids[self::POS_VALUE_ID]) ? $ids[self::POS_VALUE_ID] : '';

                $insertData[] = [
                    'dropdown_id' => $dropdownId,
                    'name' => $value,
                    'value_id' => $valueId,
                    'delete_image' => isset($deleteData[$dropdownId]) && $dropdownId == $deleteData[$dropdownId]
                        ? $dropdownId
                        : 0
                ];
            }
        }

        foreach ($insertData as $key => $row) {
            $name[$key] = $row['name'];
            $dropdownIds[$key] = $row['dropdown_id'];
        }
        array_multisort($dropdownIds, SORT_ASC, $name, SORT_ASC, $insertData);
        $dropdown = $this->dropdownRepository->getById($insertData[0]['dropdown_id']);
        $finderId = $dropdown->getFinderId();

        foreach ($insertData as $insertElement) {
            $insertDropdownId = isset($insertElement['dropdown_id']) ? $insertElement['dropdown_id'] : '';
            $file = isset($data['files']) ? $data['files']->get($insertDropdownId) : null;
            $resultImagePath = '';
            if (!empty($file)) {
                $result = [];
                if ($insertElement['value_id']) {
                    $result = $this->getCurrentImage($connection->select(), $insertElement) ?: '';
                } else {
                    $image = $this->getExistImage($connection, $insertElement);
                    $this->deleteImageFromDir($image);
                }

                if ($file['name']) {
                    $resultImagePath = $this->uploadImage(
                        $insertDropdownId,
                        $this->getCorrectNameFolder($insertElement['name']),
                        $file['name'],
                        $finderId
                    );
                }

                if ($insertElement['delete_image'] && isset($result['image'])) {
                    $resultImagePath = '';
                    $this->deleteImageFromDir($result['image']);
                } elseif (!$resultImagePath) {
                    $resultImagePath = isset($result['image']) ? $result['image'] : '';
                }
            }

            $connection->insertOnDuplicate($this->getTable('amasty_finder_value'), [
                'parent_id' => $parentId,
                'dropdown_id' => $insertDropdownId,
                'name' => $insertElement['name'],
                'image' => $resultImagePath
            ]);

            $select = $connection->select();
            $select->from($this->getTable('amasty_finder_value'))
                ->where('dropdown_id =?', $insertDropdownId)
                ->where('parent_id =?', $parentId)
                ->where('name =?', $insertElement['name']);
            $result = $this->getConnection()->fetchRow($select);

            $parentId = isset($result['value_id']) ? $result['value_id'] : '';
        }
        $connection->insertOnDuplicate($this->getTable('amasty_finder_map'), [
            'value_id' => $parentId,
            'sku' => $data['sku']
        ]);

        $this->finderRepository->updateLinks();

        return $finderId;
    }

    /**
     * @param $connection
     * @param array $insertElement
     * @return bool|string
     */
    private function getExistImage($connection, $insertElement)
    {
        $select = $connection->select();
        $select->from($this->getTable('amasty_finder_value'))->where('name=?', $insertElement['name']);
        $value = $this->getConnection()->fetchRow($select);

        return isset($value['image']) ? $value['image'] : false;
    }

    /**
     * @param $select
     * @param $insertElement
     * @param $parentId
     * @return array
     */
    public function getCurrentImage($select, $insertElement)
    {
        $dropdownId = isset($insertElement['dropdown_id']) ? $insertElement['dropdown_id'] : '';
        $valueId = isset($insertElement['value_id']) ? $insertElement['value_id'] : '';

        $select->from($this->getTable('amasty_finder_value'))
            ->where('dropdown_id =?', $dropdownId)
            ->where('value_id =?', $valueId);

        return $this->getConnection()->fetchRow($select);
    }

    /**
     * @param $fileId
     * @param $optionName
     * @param $fileName
     * @param $finderId
     * @return string
     */
    public function uploadImage($fileId, $optionName, $fileName, $finderId)
    {
        $mediaDir = $this->fileSystem->getDirectoryWrite(DirectoryList::MEDIA);
        $filePath = $mediaDir->getAbsolutePath(Value::IMAGES_DIR . $optionName . '/');
        $resultImagePath = $filePath . $finderId . substr($fileName, strripos($fileName, '.'));
        try {
            $uploader = $this->uploaderFactory->create(['fileId' => (int) $fileId]);
            $uploader->setFilesDispersion(false)
                ->setFilenamesCaseSensitivity(false)
                ->setAllowRenameFiles(true)
                ->setAllowedExtensions(['jpg', 'png', 'jpeg', 'gif', 'bmp', 'svg'])
                ->save($filePath);
            $uploader->getUploadedFileName();
            $this->fileDriver->rename($filePath . $uploader->getUploadedFileName(), $resultImagePath);
        } catch (\InvalidArgumentException $e) {
            throw $e;
        } catch (\Exception $e) {
            $resultImagePath = $this->file->getDirectoriesList($resultImagePath) ?: '';
        }

        return substr($resultImagePath, strpos($resultImagePath, '/amasty/'));
    }

    /**
     * @param $newId
     * @param $finderId
     * @return string
     */
    public function getSkuById($newId, $finderId)
    {
        $connection = $this->getConnection();
        $selectSql = $connection->select()
            ->from($this->getTable('amasty_finder_map'))->where('value_id = ?', $finderId)->where('id = ?', $newId);
        $result = $connection->fetchRow($selectSql);
        return $result['sku'];
    }

    /**
     * @param $finder
     * @param $file
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function importImages($finder, $file)
    {
        $listErrors = [];
        $connection = $this->getConnection();

        if (empty($file['name'])) {
            return $listErrors;
        }

        $this->fileValidator->validateImageFile($file, $finder);

        //for Mac OS
        // @codingStandardsIgnoreStart
        ini_set('auto_detect_line_endings', '1');
        // @codingStandardsIgnoreEnd

        $file = $this->fileDriver->fileOpen($file['tmp_name'], 'r');

        $dropdowns = $finder->getDropdowns();
        $dropdownIds = [];
        foreach ($dropdowns as $dropdown) {
            $dropdownIds[] = $dropdown->getId();
        }

        while (($line = $this->fileDriver->fileGetCsv($file, Finder::MAX_LINE, ',', '"')) !== false) {
            if (isset($line[1]) && strpos($line[1], '/') === false) {
                $pathInfo = $this->file->getPathInfo($line[1]);
                if (!$pathInfo
                    || !isset($pathInfo['extension'])
                    || !in_array($pathInfo['extension'], ['jpeg', 'jpg', 'png', 'gif'])
                ) {
                    $listErrors[] = __(
                        'Invalid extension for image %1. Please use one from: jpeg, jpg, png, gif',
                        $line[1]
                    );
                    continue;
                }

                try {
                    $imagePath = $this->moveImages($finder, $line);
                    if ($imagePath) {
                        $connection->update(
                            $this->getTable('amasty_finder_value'),
                            ['image' => $imagePath],
                            [
                                'dropdown_id IN (' . implode(",", $dropdownIds) . ')' => 0,
                                'name = ?' => $line[0],
                            ]
                        );
                    }
                } catch (\Exception $ex) {
                    $listErrors[] = $ex->getMessage();
                }
            }
        }

        return $listErrors;
    }

    /**
     * @param string $name
     * @return string
     */
    private function getCorrectNameFolder($name)
    {
        return str_replace('/', '', $name);
    }

    /**
     * @param $finder
     * @param $line
     *
     * @return bool|string
     * @throws LocalizedException
     */
    private function moveImages($finder, $line)
    {
        $optionName = isset($line[0]) ? $line[0] : '';
        $fileName = isset($line[1]) ? $line[1] : '';
        $filePath = $this->rootDirectory . FinderModel::TMP_IMAGE_DIRECTORY . '/' . $fileName;
        if (!$this->file->fileExists($filePath)) {
            throw new LocalizedException(__('File does not exist: %1', $fileName));
        }

        $newPath = $this->rootDirectory . self::IMAGES_DIR . $this->getCorrectNameFolder($optionName);
        $this->file->checkAndCreateFolder($newPath);
        $resultPath = $newPath . '/' . $finder->getId() . substr($fileName, strripos($fileName, '.'));

        if ($this->file->fileExists($filePath)) {
            $this->file->cp($filePath, $resultPath);
        }

        return isset($resultPath) ? substr($resultPath, strpos($resultPath, '/amasty/')) : '';
    }

    /**
     * @param string $imagePath
     */
    public function deleteImageFromDir($imagePath)
    {
        if ($imagePath && $this->file->fileExists($this->rootDirectory . $imagePath)) {
            $this->file->rm($this->rootDirectory . $imagePath);
            $pathToValueDir = $this->rootDirectory . substr($imagePath, 0, strripos($imagePath, '/') + 1);
            if (count($this->file->getDirectoriesList($pathToValueDir)) === 0) {
                $this->file->rmdir($pathToValueDir);
            }
        }
    }
}
