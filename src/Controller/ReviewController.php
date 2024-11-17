<?php

namespace App\Controller;

use App\Entity\Review;
use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Repository\ReviewRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/review')]
class ReviewController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ProductRepository $productRepository,
        private ReviewRepository $reviewRepository
    ) {}

    #[Route('/add/{id}', name: 'review_add', methods: ['POST'])]
    public function addReview(Request $request, int $id): Response
    {
        if (!$this->isGranted('ROLE_USER')) {
            throw $this->createAccessDeniedException('Você precisa estar logado para avaliar produtos.');
        }

        $product = $this->productRepository->find($id);
        
        if (!$product) {
            throw $this->createNotFoundException('Produto não encontrado.');
        }

        $rating = $request->request->getInt('rating');
        $comment = $request->request->get('comment');

        if ($rating < 1 || $rating > 5) {
            $this->addFlash('error', 'A avaliação deve ser entre 1 e 5 estrelas.');
            return $this->redirectToRoute('product_show', ['id' => $id]);
        }

        // Check for existing review
        $existingReview = $this->reviewRepository->findOneBy([
            'product' => $product,
            'user' => $this->getUser()
        ]);

        if ($existingReview) {
            // Update existing review
            $existingReview->setRating($rating);
            $existingReview->setComment($comment);
            $existingReview->setCreatedAt(new \DateTimeImmutable());
            
            $this->addFlash('success', 'Sua avaliação foi atualizada com sucesso!');
        } else {
            // Create new review
            $review = new Review();
            $review->setProduct($product);
            $review->setUser($this->getUser());
            $review->setRating($rating);
            $review->setComment($comment);
            $review->setCreatedAt(new \DateTimeImmutable());
            
            $this->entityManager->persist($review);
            $this->addFlash('success', 'Avaliação adicionada com sucesso!');
        }

        $this->entityManager->flush();
        return $this->redirectToRoute('product_show', ['id' => $id]);
    }
}