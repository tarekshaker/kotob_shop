<?php
/**
 * Created by PhpStorm.
 * User: Teka
 * Date: 1/15/2019
 * Time: 1:03 AM
 */

namespace App\Controller\Api;

use App\Service\OrderService;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Response;

final class OrderController extends AbstractFOSRestController
{

    /**
     * @var OrderService
     */
    private $orderService;

    /**
     * OrderController constructor.
     * @param OrderService $orderService
     */
    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Add product to cart
     * @Rest\Post("/order/{order_id}/submit")
     */
    public function submitOrderAction(int $order_id, Request $request): View
    {

        $order = $this->orderService->getOrder($order_id);
        $submitOrder = $this->orderService->submitOrder($order);

        if ($submitOrder) {
            // In case our POST was a success we need to return a 200 HTTP OK response with the success message
            $response = View::create(array("success" => "Order#" . $order_id . " status changed to pending successfully"), Response::HTTP_OK);

        } else {
            // In case our POST was a success we need to return a 200 HTTP OK response with the error message
            $response = View::create(array("error" => "This order is not in draft status"), Response::HTTP_OK);
        }

        return $response;
    }
}