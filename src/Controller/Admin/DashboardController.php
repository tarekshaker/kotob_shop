<?php
/**
 * Created by PhpStorm.
 * User: Teka
 * Date: 12/31/2018
 * Time: 3:08 AM
 */

namespace App\Controller\Admin;
use App\Entity\Order;
use App\Entity\User;
use App\Service\OrderService;
use App\Service\ProductService;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use App\Entity\Product;

class DashboardController extends AbstractController {

    /**
     * @var ProductService
     */
    private $productService;

    /**
     * @var OrderService
     */
    private $orderService;

    /**
     * @var UserService
     */
    private $userService;

    public function __construct(OrderService $orderService,ProductService $productService,UserService $userService)
    {
        $this->orderService = $orderService;
        $this->productService = $productService;
        $this->userService = $userService;
    }

    /**
     * @Route("/admin")
     * @Method({"GET"})
     */
    public function index(){

        $products = $this->productService->getAllProductsCount();
        $users = $this->userService->getAllUsersCount();
        $allOrders= $this->orderService->getAllOrdersCount();
        $draftOrders = $this->orderService->getDraftOrdersCount();
        $pendingOrders = $this->orderService->getPendingOrdersCount();
        $confirmedOrders = $this->orderService->getConfirmedOrdersCount();

        return $this->render('admin/dashboard.html.twig', array(
            'products' => $products,
            'users'=>$users,
            'allOrders'=>$allOrders,
            'draftOrders'=>$draftOrders,
            'pendingOrders'=>$pendingOrders,
            'confirmedOrders'=>$confirmedOrders)

        );
    }
}