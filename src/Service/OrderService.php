<?php
/**
 * Created by PhpStorm.
 * User: Teka
 * Date: 1/4/2019
 * Time: 7:18 AM
 */

namespace App\Service;


use App\Entity\Order;
use App\Entity\Product;
use App\Entity\User;
use App\Repository\OrderRepository;

final class OrderService
{
    /**
     * @var OrderRepository
     */
    private $orderRepository;

    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    public function getOrder(int $orderId): ?Order
    {
        return $this->orderRepository->find($orderId);
    }

    public function getAllOrders(): ?array
    {
        return $this->orderRepository->findAll();
    }

    public function checkUserDraftOrder(User $user): ?Order
    {
        $checkUserOrder = $this->orderRepository->findDraftOrderByUser($user);
        return $checkUserOrder;
    }

    public function addOrder(Product $product, User $user): ?Order
    {
        $order = new Order();
        $order->setUserId($user);
        $order->setTotalPrice($product->getPrice());
        $order->setCreatedAt(new \DateTime());
        $order->setStatus('draft');
        $order->setProducts(array('product' => $product));
        $this->orderRepository->save($order);
        return $order;
    }

    public function addOrderProduct(Order $order, Product $product): ?Order
    {
        $order->addProduct($product);
        $oldPrice = $order->getTotalPrice();
        $totalPrice = $oldPrice + $product->getPrice();
        $order->setTotalPrice($totalPrice);
        $this->orderRepository->save($order);
        return $order;
    }

    public function submitOrder(Order $order)
    {
        if ($order->getStatus() == 'draft') {
            $order->setStatus("pending");
            $this->orderRepository->save($order);
            return $order;
        } else {
            return false;
        }
    }

    public function confirmOrder(Order $order): ?Order
    {
        if ($order->getStatus() == 'pending') {
            $order->setStatus("confirmed");
            $this->orderRepository->save($order);
            return $order;
        } else {
            return false;
        }

    }

    public function getDraftOrdersCount()
    {
        return $this->orderRepository->getDraftOrdersCount();
    }

    public function getPendingOrdersCount()
    {
        return $this->orderRepository->getPendingOrdersCount();
    }

    public function getAllOrdersCount()
    {
        return $this->orderRepository->getAllOrdersCount();
    }

    public function getConfirmedOrdersCount()
    {
        return $this->orderRepository->getConfirmedOrdersCount();
    }


    public function getOrderList(Order $order): ?array
    {
        $orderList[] = $order->getProducts($order);

        return $orderList;
    }

}
