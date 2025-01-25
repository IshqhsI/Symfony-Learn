<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\BookFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class BookController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }
    #[Route('/books', name: 'app_book')]
    public function index(): Response
    {
        return $this->render('book/index.html.twig', [
            'controller_name' => 'BookController',
        ]);
    }

    #[Route('/create-book', name: 'create_book') ]
    public function createBook(Request $request): Response
    {
        // Setup Entity
        $book = new Book();

        // Setup Form
        $form = $this->createForm(BookFormType::class, $book);
        $form->handleRequest($request);

        // Process Form
        if ($form->isSubmitted() && $form->isValid()) {

            $newBook = $form->getData();
            $imageFile = $form->get('imagePath')->getData();

            if($imageFile){
                $newImageName = uniqid() . '.' . $imageFile->guessExtension();
                
                try {
                    $imageFile->move($this->getParameter('kernel.project_dir') . '/public/uploads', $newImageName);
                } catch (FileException $e) {
                    return new Response($e->getMessage());
                }
                
                $newBook->setImagePath('/uploads/' . $newImageName);
            } 

            $this->em->persist($newBook);
            $this->em->flush();

            $this->addFlash('success', 'Book created successfully!');
            return $this->redirectToRoute('app_book');
        }

        return $this->render('book/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
