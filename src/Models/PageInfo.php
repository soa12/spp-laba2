<?php
/**
 * Created by PhpStorm.
 * User: sabyrzhan
 * Date: 01.11.18
 * Time: 23:26
 */

namespace ImageUploadingService\Models;

/**
 * Class PageInfo
 * @package ImageUploadingService\Models
 */
class PageInfo
{

    /**
     * @var int
     */
    public $currentPage;

    /**
     * @var int
     */
    public $pageSize;

    /**
     * @var int
     */
    public $totalRecords;

    /**
     * @var float
     */
    private $totalPages;

    /**
     * @var int
     */
    private $shift;

    /**
     * PageInfo constructor.
     * @param array $urlParams
     * @param int $totalRecords
     */
    public function __construct($urlParams, $totalRecords)
    {
        if (empty($urlParams['page'])) {
            $this->currentPage = 1;
        } else {
            $this->currentPage = (int)$urlParams['page'];
        }

        if (!empty($urlParams['recordsShow'])
            && $urlParams['recordsShow'] > 0
        ) {
            $this->pageSize = (int)$urlParams['recordsShow'];
        } else {
            $this->pageSize = 5;
        }

        $this->totalRecords = $totalRecords;
        $this->totalPages = ceil($this->totalRecords / $this->pageSize);
        $this->shift = ($this->currentPage - 1) * $this->pageSize;
    }

    /**
     * @param $property
     * @return float|int
     */
    public function __get($property)
    {
        if ($property == 'totalPages') {
            return $this->$property = ceil($this->totalRecords / $this->pageSize);
        } else if ($property == 'shift') {
            return $this->$property = ($this->currentPage - 1) * $this->pageSize;
        } else {
            return NULL;
        }
    }

}
