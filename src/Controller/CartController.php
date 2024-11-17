<?php
namespace App\Controller;

use App\Entity\Cart;
use App\Entity\CartItem;
use App\Entity\Product;
use App\Repository\CartRepository;
use App\Repository\CartItemRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

#[Route('/cart')]
class CartController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private CartRepository $cartRepository,
        private CartItemRepository $cartItemRepository
    ) {}

    private function checkUserAccess(): void
    {
        // Check if user is logged in
        if (!$this->isGranted('ROLE_USER')) {
            throw $this->createAccessDeniedException('Você precisa estar logado para acessar o carrinho.');
        }

        // Check if user has ROLE_ADMIN but not ROLE_USER
        if ($this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_USER')) {
            throw $this->createAccessDeniedException('Administradores não têm acesso ao carrinho de compras.');
        }
    }

    #[Route('', name: 'cart_index', methods: ['GET'])]
    public function index(): Response
    {
        $this->checkUserAccess();

        $cart = $this->cartRepository->findOneOrCreateByUser($this->getUser());
        return $this->render('cart/index.html.twig', [
            'cart' => $cart,
        ]);
    }

    #[Route('/add/{id}', name: 'cart_add', methods: ['POST'])]
    public function addToCart(Request $request, Product $product): Response
    {
        $this->checkUserAccess();

        $cart = $this->cartRepository->findOneOrCreateByUser($this->getUser());
        
        $existingItem = null;
        foreach ($cart->getItems() as $item) {
            if ($item->getProduct()->getId() === $product->getId() && $item->getCustomization() === null) {
                $existingItem = $item;
                break;
            }
        }

        if ($existingItem) {
            $existingItem->setQuantity($existingItem->getQuantity() + 1);
        } else {
            $cartItem = new CartItem();
            $cartItem->setProduct($product);
            $cartItem->setPrice((string) $product->getPrice());
            $cart->addItem($cartItem);
        }

        $this->entityManager->persist($cart);
        $this->entityManager->flush();

        $this->addFlash('success', 'Produto adicionado ao carrinho com sucesso!');
        return $this->redirectToRoute('product_index');
    }

    #[Route('/remove/{id}', name: 'cart_remove_item', methods: ['POST'])]
    public function removeItem(int $id): Response
    {
        $this->checkUserAccess();

        $cartItem = $this->cartItemRepository->find($id);
        
        if (!$cartItem) {
            throw $this->createNotFoundException('Item não encontrado.');
        }

        $cart = $cartItem->getCart();
        
        if ($cart->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Você não pode modificar este carrinho.');
        }

        $cart->removeItem($cartItem);
        $this->entityManager->remove($cartItem);
        $this->entityManager->flush();

        $this->addFlash('success', 'Item removido do carrinho com sucesso!');
        return $this->redirectToRoute('cart_index');
    }

    #[Route('/update/{id}', name: 'cart_update_quantity', methods: ['POST'])]
    public function updateQuantity(Request $request, int $id): Response
    {
        $this->checkUserAccess();

        $cartItem = $this->cartItemRepository->find($id);
        
        if (!$cartItem) {
            throw $this->createNotFoundException('Item não encontrado.');
        }

        $cart = $cartItem->getCart();
        
        if ($cart->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Você não pode modificar este carrinho.');
        }

        $quantity = $request->request->getInt('quantity', 1);
        if ($quantity < 1) {
            $quantity = 1;
        }

        $cartItem->setQuantity($quantity);
        $cart->updateTotal();
        
        $this->entityManager->flush();

        $this->addFlash('success', 'Quantidade atualizada com sucesso!');
        return $this->redirectToRoute('cart_index');
    }

    #[Route('/checkout', name: 'cart_checkout', methods: ['POST'])]
    public function checkout(): Response
    {
        $this->checkUserAccess();

        $cart = $this->cartRepository->findOneOrCreateByUser($this->getUser());
        
        if ($cart->getItems()->isEmpty()) {
            $this->addFlash('error', 'Seu carrinho está vazio!');
            return $this->redirectToRoute('cart_index');
        }

        try {
            $order = $cart->convertToOrder();
            
            $this->entityManager->persist($order);
            $this->entityManager->remove($cart);
            $this->entityManager->flush();

            return $this->redirectToRoute('order_confirmation', [
                'id' => $order->getId()
            ]);
        } catch (\Exception $e) {
            $this->addFlash('error', 'Ocorreu um erro ao processar seu pedido. Por favor, tente novamente.');
            return $this->redirectToRoute('cart_index');
        }
    }

    #[Route('/clear', name: 'cart_clear', methods: ['POST'])]
    public function clearCart(): Response
    {
        $this->checkUserAccess();

        $cart = $this->cartRepository->findOneOrCreateByUser($this->getUser());
        
        $this->entityManager->remove($cart);
        $this->entityManager->flush();

        $this->addFlash('success', 'Carrinho esvaziado com sucesso!');
        return $this->redirectToRoute('product_index');
    }
}