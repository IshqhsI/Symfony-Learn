<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\BookFormType;
use App\Service\BookService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class BookController extends AbstractController
{
    private $em;
    private BookService $bookService;

    public function __construct(EntityManagerInterface $entityManager, BookService $bookService)
    {
        $this->em = $entityManager;
        $this->bookService = $bookService;
    }
    #[Route('/books', name: 'app_book')]
    public function index(): Response
    {
        $books = $this->bookService->getAllBooks();
        return $this->render('book/index.html.twig', [
            'books' => $books,
            'title' => 'IshqStore | Bookstore Catalog'
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

            $newBook = $request->request->all()['book_form'];
            $file = $request->files->all()['book_form']['imagePath'];
            $this->bookService->createBook($newBook, $file);

            $this->addFlash('success', 'Book created successfully!');
            return $this->redirectToRoute('app_book');
        }

        return $this->render('book/create.html.twig', [
            'form' => $form->createView(),
            'title' => 'IshqStore | Create Book'
        ]);
    }

    #[Route('/edit-book/{id}', name: 'edit_book')]
    public function editBook(Request $request, int $id): Response
    {
        // Setup Entity
        $book = $this->em->getRepository(Book::class)->find($id);

        // Check if book exists
        if(!$book){
            return $this->redirectToRoute('app_book');
        }

        // Setup Form
        $form = $this->createForm(BookFormType::class, $book);
        $form->handleRequest($request);
        
        // Process Form
        if($form->isSubmitted() && $form->isValid()){

            $newBook = $request->request->all()['book_form'];
            $file = $request->files->all()['book_form']['imagePath'];
            $this->bookService->editBook($book, $newBook, $file);

            $this->addFlash('success', 'Book updated successfully!');
            return $this->redirectToRoute('app_book');
        }

        return $this->render('book/edit.html.twig', [
            'form' => $form->createView(),
            'title' => 'IshqStore | Edit Book',
            'book' => $book
        ]);
    }

    #[Route('/delete-book/{id}', name: 'delete_book')]
    public function deleteBook(int $id): Response
    {
        $book = $this->em->getRepository(Book::class)->find($id);

        if(!$book){
            return $this->redirectToRoute('app_book');
        }

        $this->em->remove($book);
        $this->em->flush();
        $this->addFlash('success', 'Book deleted successfully!');
        
        return $this->redirectToRoute('app_book');
    }

    public function createData($form){
        $newBook = $form->getData();
        $imageFile = $form->get('imagePath')->getData();

        // Upload Image
        if($imageFile){
            $newImageName = uniqid() . '.' . $imageFile->guessExtension();

            try {
                $imageFile->move($this->getParameter('kernel.project_dir') . '/public/uploads', $newImageName);
            } catch (FileException $e) {
                return new Response($e->getMessage());
            }

            $newBook->setImagePath('/uploads/' . $newImageName);
        } 

        // Persist
        $this->em->persist($newBook);
        $this->em->flush();
    }

    public function editData($book, $form){
        $newBook = $form->getData();
        $imageFile = $form->get('imagePath')->getData();

        // Check if there is a new image
        if($imageFile){
            $newImageName = uniqid() . '.' . $imageFile->guessExtension();

            try {
                $imageFile->move($this->getParameter('kernel.project_dir') . '/public/uploads', $newImageName);
            } catch (FileException $e) {
                return new Response($e->getMessage());
            }

            $newBook->setImagePath('/uploads/' . $newImageName);
        } else {
            $newBook->setImagePath($book->getImagePath());
        }

        // Persist
        $this->em->persist($newBook);
        $this->em->flush();
    }

    #[Route('/', name: 'app_home')]
    public function home(): Response
    {
        return $this->redirectToRoute('app_book');
    }
}
