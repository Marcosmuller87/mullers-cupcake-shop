<?php

namespace App\Controller;

use App\Entity\Order;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/orders')]
class AdminOrderController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private OrderRepository $orderRepository
    ) {}

    #[Route('', name: 'admin_order_index')]
    public function index(): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }
        
        $orders = $this->orderRepository->findAllWithUsers();  // Create this method too
        return $this->render('admin/order/index.html.twig', [
            'orders' => $orders
        ]);
    }

    #[Route('/{id}', name: 'admin_order_show', methods: ['GET'])]
    public function show(int $id): Response  // Change parameter
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }
        
        $order = $this->orderRepository->findOneWithUser($id);
        if (!$order) {
            throw $this->createNotFoundException();
        }
        
        return $this->render('admin/order/show.html.twig', [
            'order' => $order
        ]);
    }

    #[Route('/{id}/update-status', name: 'admin_order_update_status', methods: ['POST'])]
    public function updateStatus(int $id, Request $request): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }
        
        $order = $this->orderRepository->findOneWithUser($id);
        if (!$order) {
            throw $this->createNotFoundException();
        }
        
        $newStatus = $request->request->get('status');
        if (in_array($newStatus, ['pending', 'processing', 'completed', 'cancelled'])) {
            $order->setStatus($newStatus);
            $this->entityManager->flush();
            
            $this->addFlash('success', 'Status do pedido atualizado com sucesso!');
        }
        
        return $this->redirectToRoute('admin_order_show', ['id' => $order->getId()]);
    }
}