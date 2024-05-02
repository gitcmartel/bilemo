<?php

namespace App\Service;

use Hateoas\Configuration\Annotation as Hateoas;
use JMS\Serializer\Annotation\Groups;

/**
 * 
 * @Hateoas\Relation(
 *     "first",
 *     href = @Hateoas\Route(
 *         "getUsersByClient",
 *         parameters = { "page" = 1, "limit" = "expr(object.getLimit())" }
 *     ), 
 *     exclusion = @Hateoas\Exclusion(groups="getUsers")
 * )
 * 
 * @Hateoas\Relation(
 *     "last",
 *     href = @Hateoas\Route(
 *         "getUsersByClient",
 *         parameters = { "page" = "expr(object.getTotalPages())", "limit" = "expr(object.getLimit())" }
 *     ), 
 *     exclusion = @Hateoas\Exclusion(groups="getUsers")
 * )
 * 
 * @Hateoas\Relation(
 *     "prev",
 *     href = @Hateoas\Route(
 *         "getUsersByClient",
 *         parameters = { "page" = "expr(object.getCurrentPage() - 1)", "limit" = "expr(object.getLimit())" }
 *     ),
 *     exclusion = @Hateoas\Exclusion(groups="getUsers", excludeIf = "expr(object.getCurrentPage() <= 1)")
 * )
 * 
 * @Hateoas\Relation(
 *     "next",
 *     href = @Hateoas\Route(
 *         "getUsersByClient",
 *         parameters = { "page" = "expr(object.getCurrentPage() + 1)", "limit" = "expr(object.getLimit())" }
 *     ),
 *     exclusion = @Hateoas\Exclusion(groups="getUsers", excludeIf = "expr(object.getCurrentPage() >= object.getTotalPages())")
 * )
 * 
 */

class PaginatedResponseUsers extends PaginatedResponse
{
    #[Groups(["getUsers"])]
    protected $items;

    #[Groups(["getUsers"])]
    protected $currentPage;

    #[Groups(["getUsers"])]
    protected $totalPages;

    #[Groups(["getUsers"])]
    protected $limit;
    
    public function getUsers()
    {
        return $this->getItems();
    }
}
