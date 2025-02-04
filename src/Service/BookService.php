<?php 

namespace App\Service;

use App\Entity\Book;
use App\Repository\BookRepository;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class BookService{

    private BookRepository $bookRepository;

    public function __construct(BookRepository $bookRepository){
        $this->bookRepository = $bookRepository;
    }

    public function getAllBooks(){
        return $this->bookRepository->findAll();
    }

    public function createBook($newBook, ?UploadedFile $uploadedFile = null){

        $book = new Book();

        $book->setTitle($newBook['title']);
        $book->setAuthor($newBook['author']);
        $book->setPrice($newBook['price']);
        $book->setDescription($newBook['description']);

        if($uploadedFile){
            $book->setImagePath($this->uploadImage($uploadedFile));
        }

        $this->bookRepository->save($book, true);
        return $book;
    }

    public function editBook($oldBook, $newBook,?UploadedFile $uploadedFile = null){

        $book = $oldBook;

        $book->setTitle($newBook['title']);
        $book->setAuthor($newBook['author']);
        $book->setPrice($newBook['price']);
        $book->setDescription($newBook['description']);

        if ($uploadedFile) {
            $book->setImagePath($this->uploadImage($uploadedFile));
        } else {
            $book->setImagePath($oldBook->getImagePath());
        }

        $this->bookRepository->save($book, true);
        return $book;

    }

    public function uploadImage(UploadedFile $uploadedFile){
        $fileName = "uploads/" . uniqid() . '.' . $uploadedFile->guessExtension();
        $uploadedFile->move('uploads', $fileName);
        return $fileName;
    }

}

