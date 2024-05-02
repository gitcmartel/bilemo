<?php

namespace App\Service;

/**
 * This class represents a paginated response containing a list of elements,
 * information about the current page, total pages and limit
 * elements per page.
 */

class PaginatedResponse
{
    protected $items;

    protected $currentPage;

    protected $totalPages;

    protected $limit;

    /**
     * CLass Constructor
     * 
     * @param array $items       Current page elements list
     * @param int   $currentPage Current page number
     * @param int   $totalPages  Total number of pages
     * @param int   $limit       Limit of elements per page
     */
    public function __construct($items, $currentPage, $totalPages, $limit)
    {
        $this->items = $items;
        $this->currentPage = $currentPage;
        $this->totalPages = $totalPages;
        $this->limit = $limit;
    }
    
    public function getItems()
    {
        return $this->items;
    }

    public function getCurrentPage()
    {
        return $this->currentPage;
    }

    public function getTotalPages()
    {
        return $this->totalPages;
    }

    public function getLimit()
    {
        return $this->limit;
    }
}
