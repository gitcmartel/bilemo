<?php

namespace App\Service;

use Hateoas\Configuration\Annotation as Hateoas;

/**
 * 
 * @Hateoas\Relation(
 *     "first",
 *     href = @Hateoas\Route(
 *         "getAllProducts",
 *         parameters = { "page" = 1, "limit" = "expr(object.getLimit())" }
 *     )
 * )
 * 
 * @Hateoas\Relation(
 *     "last",
 *     href = @Hateoas\Route(
 *         "getAllProducts",
 *         parameters = { "page" = "expr(object.getTotalPages())", "limit" = "expr(object.getLimit())" }
 *     )
 * )
 * 
 * @Hateoas\Relation(
 *     "prev",
 *     href = @Hateoas\Route(
 *         "getAllProducts",
 *         parameters = { "page" = "expr(object.getCurrentPage() - 1)", "limit" = "expr(object.getLimit())" }
 *     ),
 *     exclusion = @Hateoas\Exclusion(excludeIf = "expr(object.getCurrentPage() <= 1)")
 * )
 * 
 * @Hateoas\Relation(
 *     "next",
 *     href = @Hateoas\Route(
 *         "getAllProducts",
 *         parameters = { "page" = "expr(object.getCurrentPage() + 1)", "limit" = "expr(object.getLimit())" }
 *     ),
 *     exclusion = @Hateoas\Exclusion(excludeIf = "expr(object.getCurrentPage() >= object.getTotalPages())")
 * )
 * 
 */

  /**
  * This class extends the PaginatedResponse class and adds HATEOAS relationships
  * for product pagination.
  */
class PaginatedResponseProducts extends PaginatedResponse
{
    public function getProducts()
    {
        return $this->getItems();
    }
}
