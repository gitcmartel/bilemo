<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Attribute\Route;

class ProductController extends AbstractController
{
    #[Route('/api/products', name: 'products', methods: ['GET'])]
    public function getAllProducts(ProductRepository $productRepository, SerializerInterface $serializer): JsonResponse
    {
        $productList = $productRepository->findAll();
        $jsonProductList = $serializer->serialize($productList, 'json');
        
        return new JsonResponse($jsonProductList, Response::HTTP_OK, [], true);
    }

    #[Route('/api/product/{id}', name: 'product', methods: ['GET'])]
    public function getProduct(Product $product, ProductRepository $productRepository, SerializerInterface $serializer): JsonResponse
    {
        if ($product === null) {
            return new JsonResponse(null, Response::HTTP_NOT_FOUND);
        }
        
        $jsonProduct = $serializer->serialize($product, 'json');

        return new JsonResponse($jsonProduct, Response::HTTP_OK, [], true);
    }
}
