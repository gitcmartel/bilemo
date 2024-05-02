<?php

namespace App\Service;



class PaginatedResponse
{
    protected $items;

    protected $currentPage;

    protected $totalPages;

    protected $limit;

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
