<?php
/**
 * Created by PhpStorm.
 * User: Teka
 * Date: 1/4/2019
 * Time: 7:18 AM
 */

namespace App\Service;


use App\Entity\Product;
use App\Repository\ProductRepository;

final class ProductService
{
    /**
     * @var ProductRepository
     */
    private $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function getProduct(int $productId): ?Product
    {
        return $this->productRepository->find($productId);
    }

    public function getAllProducts(): ?array
    {
        return $this->productRepository->findAll();
    }


    public function getAllProductsCount()
    {
        return $this->productRepository->getAllProductsCount();
    }

}
