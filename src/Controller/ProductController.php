<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use JMS\Serializer\SerializerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class ProductController extends AbstractController
{
    private ProductRepository $productRepository;
    private SerializerInterface $serializer;
    private TagAwareCacheInterface $cache;
    /**
     * The class constructor
     * 
     * @param ProductRepository $productRepository The product repository.
     * @param SerializerInterface $serializer The serializer to convert objects to JSON.
     * @param TagAwareCacheInterface $cache The cache to store temporary data.
     */
    public function __construct(ProductRepository $productRepository, SerializerInterface $serializer, TagAwareCacheInterface $cache) 
    {
        $this->productRepository = $productRepository;
        $this->serializer = $serializer;
        $this->cache = $cache;
    }

    /**
     * Endpoint to get the full products list
     * 
     * This method fetch all products from the database, serializes them in JSON format,
     * and caches them to improve performance on subsequent queries.
     * 
     * @param Request $request The current HTTP request. Used to access request data like headers, parameters, and body content.
     * 
     * @return JsonResponse The JSON response containing the list of products.
     */
    #[OA\Response(
        response: 200,
        description: 'Returns the products list',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Product::class))
        )
    )]
    #[OA\Tag(name: 'Products')]
    #[Route('/api/products', name: 'getAllProducts', methods: ['GET'])]
    #[IsGranted('ROLE_USER', message: 'You do not have sufficient rights to obtain the list of products')]
    public function getAllProducts(Request $request): JsonResponse
    {
        $page = $request->get('page', 1);
        $limit = $request->get('limit', 10);

        $idCache = "productsCache-" . $page . '-' . $limit;

        $jsonProductList = $this->cache->get($idCache, function (ItemInterface $item) use ($page, $limit) {
            $item->tag("productsCache");
            $productList = $this->productRepository->findAllWithPagination($page, $limit);
            return $this->serializer->serialize($productList, 'json');
        });
        
        return new JsonResponse($jsonProductList, Response::HTTP_OK, [], true);
    }


    /**
    * Retrieve product details by its ID.
    * 
    * This method retrieves the details of a product identified by its ID.
    * It checks if the user has the necessary permissions to view the product details,
    * and returns a JSON response containing the serialized product information.
    * 
    * 
    * @param Product $product The product entity to retrieve.
    * 
    * @return JsonResponse A JSON response containing the serialized product information.
    */
    #[OA\Response(
        response: 200,
        description: 'Returns a product details',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Product::class))
        )
    )]
    #[OA\Tag(name: 'Products')]
    #[Route('/api/product/{id}', name: 'getProduct', methods: ['GET'])]
    #[IsGranted('ROLE_USER', message: 'You do not have sufficient rights to obtain the product details')]
    public function getProduct(Product $product): JsonResponse
    {
        if ($product === null) {
            return new JsonResponse(null, Response::HTTP_NOT_FOUND);
        }

        $jsonProduct = $this->serializer->serialize($product, 'json');

        return new JsonResponse($jsonProduct, Response::HTTP_OK, [], true);
    }
}
