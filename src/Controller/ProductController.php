<?php
namespace App\Controller;

use App\Entity\Product;
use App\Entity\Review;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use App\Service\FileUploader;

class ProductController extends AbstractController
{
    private $entityManager;
    private $productRepository;
    private $fileUploader;

    public function __construct(
        EntityManagerInterface $entityManager,
        ProductRepository $productRepository,
        FileUploader $fileUploader
    ) {
        $this->entityManager = $entityManager;
        $this->productRepository = $productRepository;
        $this->fileUploader = $fileUploader;
    }

    #[Route(path: '/', name: 'default')]
    public function redirectToLogin(): Response
    {
        return $this->redirectToRoute('app_login');
    }

    #[Route('/product', name: 'product_index')]
    public function index(): Response
    {
        $products = $this->isGranted('ROLE_ADMIN') 
            ? $this->productRepository->findAllIncludingDeleted()
            : $this->productRepository->findAllActive();
            
        return $this->render('product/index.html.twig', [
            'products' => $products,
        ]);
    }

    #[Route('/product/new', name: 'product_new')]
    public function new(Request $request): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Você precisa ser um administrador para acessar esta página.');
        }

        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Clear any existing success messages
            $request->getSession()->remove('success');
            
            $imageFile = $form->get('imageFile')->getData();

            if ($imageFile) {
                try {
                    $imageFileName = $this->fileUploader->upload($imageFile);
                    $product->setImage($imageFileName);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erro ao fazer upload da imagem');
                }
            }

            $this->entityManager->persist($product);
            $this->entityManager->flush();
        
            $this->addFlash('success', 'Produto adicionado com sucesso!');
            return $this->redirectToRoute('product_index');
        }

        return $this->render('product/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/product/{id}/edit', name: 'product_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, int $id): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Você precisa ser um administrador para acessar esta página.');
        }

        $product = $this->productRepository->find($id);
    
        if (!$product) {
            throw $this->createNotFoundException('Produto não encontrado');
        }

        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Clear any existing success messages
            $request->getSession()->remove('success');
            
            $imageFile = $form->get('imageFile')->getData();

            if ($imageFile) {
                try {
                    if ($product->getImage()) {
                        $this->fileUploader->remove($product->getImage());
                    }

                    $imageFileName = $this->fileUploader->upload($imageFile);
                    $product->setImage($imageFileName);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erro ao fazer upload da imagem');
                }
            }

            $this->entityManager->flush();
        
            $this->addFlash('success', 'Produto atualizado com sucesso!');
            return $this->redirectToRoute('product_index');
        }

        return $this->render('product/edit.html.twig', [
            'form' => $form->createView(),
            'product' => $product,
        ]);
    }

    #[Route('/product/{id}/delete', name: 'product_delete', methods: ['POST'])]
    public function delete(Request $request, int $id): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        $product = $this->productRepository->find($id);
        if (!$product) {
            throw $this->createNotFoundException();
        }

        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            try {
                // Instead of removing, we'll soft delete
                $product->setIsActive(false);
                $product->setDeletedAt(new \DateTime());
                
                $this->entityManager->flush();
                
                $this->addFlash('success', 'Produto removido com sucesso!');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Ocorreu um erro ao tentar remover o produto.');
            }
        }

        return $this->redirectToRoute('product_index');
    }

    #[Route('/product/{id}/show', name: 'product_show', methods: ['GET'])]
    public function show(int $id): Response
    {
        $product = $this->productRepository->find($id);
        
        if (!$product) {
            throw $this->createNotFoundException('Produto não encontrado');
        }

        $reviews = $this->entityManager
            ->getRepository(Review::class)
            ->findBy(['product' => $product], ['createdAt' => 'DESC']);

        return $this->render('product/show.html.twig', [
            'product' => $product,
            'reviews' => $reviews
        ]);
    }
}