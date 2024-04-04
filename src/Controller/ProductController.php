<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class ProductController extends AbstractController
{

    #[Route('/api/products', name: 'products', methods: ['GET'])]
    #[IsGranted('ROLE_USER', message: 'Vous \'avez pas les droits suffisants pour obtenir la liste des produits')]
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


    #[Route('/api/product/{id}', name: 'product', methods: ['GET'])]
    #[IsGranted('ROLE_USER', message: 'Vous \'avez pas les droits suffisants pour obtenir le détail d\'un produit')]
    public function getProduct(Product $product, ProductRepository $productRepository, SerializerInterface $serializer): JsonResponse
    {
        if ($product === null) {
            return new JsonResponse(null, Response::HTTP_NOT_FOUND);
        }

        $jsonProduct = $serializer->serialize($product, 'json');

        return new JsonResponse($jsonProduct, Response::HTTP_OK, [], true);
    }
}
