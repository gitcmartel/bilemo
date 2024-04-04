<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class ProductController extends AbstractController
{
    /**
     * Endpoint to get the full products list
     * 
     * This method fetch all products from the database, serializes them in JSON format,
     * and caches them to improve performance on subsequent queries.
     * 
     * @param ProductRepository $productRepository The product repository.
     * @param SerializerInterface $serializer The serializer to convert objects to JSON.
     * @param TagAwareCacheInterface $cache The cache to store temporary data.
     * 
     * @return JsonResponse The JSON response containing the list of products.
     */
    #[Route('/api/products', name: 'getAllProducts', methods: ['GET'])]
    #[IsGranted('ROLE_USER', message: 'You do not have sufficient rights to obtain the list of products')]
    public function getAllProducts(ProductRepository $productRepository, SerializerInterface $serializer, TagAwareCacheInterface $cache): JsonResponse
    {
        $idCache = "getAllProducts";

        $jsonProductList = $cache->get($idCache, function (ItemInterface $item) use ($productRepository, $serializer) {
            $item->tag("productsCache");
            $productList = $productRepository->findAll();
            return $serializer->serialize($productList, 'json');
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
    * @param Product $product The product entity to retrieve.
    * @param ProductRepository $productRepository The repository for product entities.
    * @param SerializerInterface $serializer The serializer used to serialize the product object into JSON.
    * 
    * @return JsonResponse A JSON response containing the serialized product information.
    */
    #[Route('/api/product/{id}', name: 'getProduct', methods: ['GET'])]
    #[IsGranted('ROLE_USER', message: 'You do not have sufficient rights to obtain the product details')]
    public function getProduct(Product $product, ProductRepository $productRepository, SerializerInterface $serializer): JsonResponse
    {
        if ($product === null) {
            return new JsonResponse(null, Response::HTTP_NOT_FOUND);
        }

        $jsonProduct = $serializer->serialize($product, 'json');

        return new JsonResponse($jsonProduct, Response::HTTP_OK, [], true);
    }
}
