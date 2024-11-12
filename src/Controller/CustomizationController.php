<?php
namespace App\Controller;

use App\Entity\Customization;
use App\Entity\Product;
use App\Entity\CartItem;
use App\Form\CustomizationType;
use App\Repository\CartRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;

class CustomizationController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private CartRepository $cartRepository,
        private ProductRepository $productRepository
    ) {}

    #[Route('/product/{id}/customize', name: 'product_customize')]
    public function customize(Request $request, int $id): Response
    {
        // Check security manually
        if (!$this->isGranted('ROLE_USER')) {
            throw $this->createAccessDeniedException('You need to be logged in to access this page.');
        }
        
        // Get the product using the repository
        $product = $this->productRepository->find($id);
        
        if (!$product) {
            throw $this->createNotFoundException('Product not found');
        }
        
        $customization = new Customization();
        $customization->setProduct($product);
        $customization->setPriceIncrement('0.00');
        
        $form = $this->createForm(CustomizationType::class, $customization);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Calculate price increment based on selections
            $priceIncrement = 0.00;
            
            // Add price for toppings
            switch ($customization->getTopping()) {
                case 'chocolate':
                case 'cream_cheese':
                    $priceIncrement += 3.00;
                    break;
                case 'whipped_cream':
                    $priceIncrement += 2.00;
                    break;
            }
            
            // Add price for decorations
            switch ($customization->getDecoration()) {
                case 'fruits':
                    $priceIncrement += 4.00;
                    break;
                case 'sprinkles':
                case 'confetti':
                    $priceIncrement += 2.00;
                    break;
            }
            
            $customization->setPriceIncrement(number_format($priceIncrement, 2, '.', ''));
            
            // Add to cart
            $cart = $this->cartRepository->findOneOrCreateByUser($this->getUser());
            
            $cartItem = new CartItem();
            $cartItem->setProduct($product);
            $cartItem->setCustomization($customization);
            $cartItem->setQuantity(1);
            $cartItem->setPrice((string) $product->getPrice());
            
            $cart->addItem($cartItem);
            
            $this->entityManager->persist($customization);
            $this->entityManager->persist($cartItem);
            $this->entityManager->persist($cart);
            $this->entityManager->flush();
            
            $this->addFlash('success', 'Cupcake personalizado adicionado ao carrinho!');
            return $this->redirectToRoute('cart_index');
        }

        return $this->render('customization/customize.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }
}