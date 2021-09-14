<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Finder
 */


namespace Amasty\Finder\Api\Data;

interface ImportLogInterface
{
    /**
     * Constants defined for keys of data array
     */
    const FILE_ID = 'file_id';
    const FILE_NAME = 'file_name';
    const FINDER_ID = 'finder_id';
    const STARTED_AT = 'started_at';
    const UPDATED_AT = 'updated_at';
    const ENDED_AT = 'ended_at';
    const COUNT_LINES = 'count_lines';
    const COUNT_PROCESSING_LINES = 'count_processing_lines';
    const LAST_START_PROCESSING_LINE = 'last_start_processing_line';
    const COUNT_PROCESSING_ROWS = 'count_processing_rows';
    const COUNT_ERRORS = 'count_errors';
    const STATUS = 'status';
    const IS_LOCKED = 'is_locked';

    /**
     * @return int
     */
    public function getFileId();

    /**
     * @param int $fileId
     *
     * @return $this
     */
    public function setFileId($fileId);

    /**
     * @return string
     */
    public function getFileName();

    /**
     * @param string $fileName
     *
     * @return $this
     */
    public function setFileName($fileName);

    /**
     * @return int
     */
    public function getFinderId();

    /**
     * @param int $finderId
     *
     * @return $this
     */
    public function setFinderId($finderId);

    /**
     * @return string|null
     */
    public function getStartedAt();

    /**
     * @param string|null $startedAt
     *
     * @return $this
     */
    public function setStartedAt($startedAt);

    /**
     * @return string|null
     */
    public function getUpdatedAt();

    /**
     * @param string|null $updatedAt
     *
     * @return $this
     */
    public function setUpdatedAt($updatedAt);

    /**
     * @return string|null
     */
    public function getEndedAt();

    /**
     * @param string|null $endedAt
     *
     * @return $this
     */
    public function setEndedAt($endedAt);

    /**
     * @return int
     */
    public function getCountLines();

    /**
     * @param int $countLines
     *
     * @return $this
     */
    public function setCountLines($countLines);

    /**
     * @return int
     */
    public function getCountProcessingLines();

    /**
     * @param int $countProcessingLines
     *
     * @return $this
     */
    public function setCountProcessingLines($countProcessingLines);

    /**
     * @return int
     */
    public function getLastStartProcessingLine();

    /**
     * @param int $lastStartProcessingLine
     *
     * @return $this
     */
    public function setLastStartProcessingLine($lastStartProcessingLine);

    /**
     * @return int
     */
    public function getCountProcessingRows();

    /**
     * @param int $countProcessingRows
     *
     * @return $this
     */
    public function setCountProcessingRows($countProcessingRows);

    /**
     * @return int
     */
    public function getCountErrors();

    /**
     * @param int $countErrors
     *
     * @return $this
     */
    public function setCountErrors($countErrors);

    /**
     * @return int
     */
    public function getStatus();

    /**
     * @param int $status
     *
     * @return $this
     */
    public function setStatus($status);

    /**
     * @return int
     */
    public function getIsLocked();

    /**
     * @param int $isLocked
     *
     * @return \Amasty\Finder\Api\ImportLogInterface
     */
    public function setIsLocked($isLocked);
}
