<?php

namespace App\Controller;

use App\Entity\Order;
use App\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/order')]
class OrderController extends AbstractController
{
    public function __construct(
        private OrderRepository $orderRepository
    ) {}

    #[Route('/confirmation/{id}', name: 'order_confirmation')]
    public function confirmation(int $id): Response
    {
        if (!$this->isGranted('ROLE_USER')) {
            throw $this->createAccessDeniedException('Você precisa estar logado para visualizar pedidos.');
        }
        
        $order = $this->orderRepository->findOneWithUser($id); // Create this method
        
        if (!$order) {
            throw $this->createNotFoundException('Pedido não encontrado.');
        }

        // Debug line
        if (!$order->getUser()) {
            throw new \RuntimeException('Order #' . $id . ' has no user associated');
        }

        if ($order->getUser() !== $this->getUser() && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Você não tem permissão para visualizar este pedido.');
        }

        return $this->render('order/confirmation.html.twig', [
            'order' => $order
        ]);
    }
}