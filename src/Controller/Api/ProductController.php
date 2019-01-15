<?php
/**
 * Created by PhpStorm.
 * User: Teka
 * Date: 12/31/2018
 * Time: 2:48 AM
 */


namespace App\Controller\Api;

use App\Entity\Order;
use App\Entity\Product;


use App\Service\OrderService;
use App\Service\ProductService;
use App\Service\UserService;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Knp\Component\Pager\Paginator;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


final class ProductController extends Controller
{

    /**
     * @var ProductService
     */
    private $productService;

    /**
     * @var UserService
     */
    private $userService;


    /**
     * @var Paginator
     */
    private $paginator;

    /**
     * @var OrderService
     */
    private $orderService;

    /**
     * ProductController constructor.
     * @param ProductService $productService
     * @param PaginatorInterface $paginator
     * @param UserService $userService
     * @param OrderService $orderService
     */
    public function __construct(ProductService $productService, PaginatorInterface $paginator, UserService $userService, OrderService $orderService)
    {
        $this->productService = $productService;
        $this->paginator = $paginator;
        $this->userService = $userService;
        $this->orderService = $orderService;
    }

    /**
     * Get all products (Paginated)
     * @Rest\Get("/product")
     */
    public function getProductsAction(Request $request): View
    {
        //Use doctrine query to handle pagination
        $em = $product = $this->getDoctrine()->getManager();
        $dql = "SELECT p FROM App\Entity\Product p";
        $query = $em->createQuery($dql);

        $paginator = $this->get('knp_paginator');
        $products = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            10/*limit per page*/
        );
        // In case our GET was a success we need to return a 200 HTTP OK response with the collection of product object
        return View::create($products, Response::HTTP_OK);
    }

    /**
     * Add product to cart
     * @Rest\Post("/product/{product_id}/add-to-cart")
     */
    public function addToCartAction(int $product_id, Request $request): View
    {
        $product = $this->productService->getProduct($product_id);
        if ($product == null) {
            $response = View::create(array("error" => "This product is not found"), Response::HTTP_OK);
        } else {
            $user = $this->userService->getCurrentUser();

            $checkUserDraftOrder = $this->orderService->checkUserDraftOrder($user);

            if ($checkUserDraftOrder == null) {
                $order = $this->orderService->addOrder($product, $user);
            } else {
                $order = $checkUserDraftOrder;
                $order_product = $this->orderService->addOrderProduct($order, $product);
            }

            // In case our POST was a success we need to return a 200 HTTP OK response with success message
            $response = View::create(array("success" => $product->getTitle() . " added to cart successfully"), Response::HTTP_OK);

        }
        return $response;
    }


}