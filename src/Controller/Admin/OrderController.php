<?php
/**
 * Created by PhpStorm.
 * User: Teka
 * Date: 12/31/2018
 * Time: 2:48 AM
 */


namespace App\Controller\Admin;

use App\Entity\Product;
use App\Form\ProductFormType;
use App\Service\OrderService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;


class OrderController extends AbstractController
{

    /**
     * @var OrderService
     */
    private $orderService;

    /**
     * ProductController constructor.
     */
    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * @Route("/admin/orders" ,name="list_orders",methods="GET")
     */
    public function getOrdersList()
    {
        $orders = $this->orderService->getAllOrders();
        return $this->render('admin/orders/list.html.twig', array('orders' => $orders));
    }


    /**
     * @Route("/admin/orders/confirm_order/{id}",name="confirm_order", methods="PUT")
     */
    public function confirm_order(Request $request, $id): Response
    {
        $order = $this->orderService->getOrder($id);
        $order=$this->orderService->confirmOrder($order);

        if($order){
            $this->addFlash('success', 'The order confirmed successfully');
        }else{
            $this->addFlash('error', 'This order is not in pending status');
        }

        $response = new Response();
        $response->send();

    }


    /**
     * @Route("/admin/orders/show_order_list/{id}",name="show_order_list", methods="GET")
     */
    public function show_order(Request $request, $id): Response
    {

        $orderList = $this->orderService->getOrder($id);
        return $this->render('admin/orders/show.html.twig', array('orderList' => $orderList));


    }
}