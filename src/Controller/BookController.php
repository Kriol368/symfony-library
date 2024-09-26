<?php

namespace App\Controller;

use App\Entity\Book;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class BookController extends AbstractController
{

    private $books = [
        1 => ["title" => "The Silent Forest", "genre" => "Fantasy", "year" => 2010, "pages" => 320],
        2 => ["title" => "Mystery of the Night", "genre" => "Mystery", "year" => 2015, "pages" => 410],
        3 => ["title" => "Galactic Traveler", "genre" => "Science Fiction", "year" => 2018, "pages" => 285],
        4 => ["title" => "Love and War", "genre" => "Romance", "year" => 2020, "pages" => 380],
        5 => ["title" => "Secrets of the Mind", "genre" => "Thriller", "year" => 2016, "pages" => 270]
    ];



    /**
     * @Route("/book", name="index")
     */
    public function index(): Response
    {
        return $this->render('/book/index.html.twig', []);

    }

    /**
     * @Route ("/book/insert", name="insert_book")
     */
    public function insert(ManagerRegistry $doctrine){
        $entityManager = $doctrine->getManager();
        foreach($this->books as $b) {
            $book = new Book();
            $book->setTitle($b ["title"]);
            $book->setGenre($b ["genre"]);
            $book->setPages($b ["pages"]);
            $book->setYear($b ["year"]);
            $entityManager->persist($book);
        }
        try {
            $entityManager->flush();
            return new Response("Books inserted");
        }catch (\Exception $e){
            return new Response("Error inserting books");
        }
    }

    /**
     * @Route("/book/find/{title}", name="find_book")
     */
    public function find(ManagerRegistry $doctrine, $title): Response {
        $repository = $doctrine->getRepository(Book::class);
        $books = $repository->findByTitle($title);

        return $this->render('book/book_list.html.twig', [
            'books' => $books
        ]);
    }

    /**
     * @Route("/book/update/{id}/{title}", name="modificar_contacto")
     */
    public function update(ManagerRegistry $doctrine, $id, $title): Response {
        $entityManager = $doctrine->getManager();
        $repository = $doctrine->getRepository(Book::class);
        $book = $repository->find($id);

        if ($book) {
            $book->setTitle($title);
            try {
                $entityManager->flush();
                return $this->render('book/book_list.html.twig', [
                    'book' => $book
                ]);
            } catch (\Exception $e) {
                return new Response("Error updating book");
            }
        } else {
            return $this->render('book/book_list.html.twig', [
                'book' => null
            ]);
        }
    }

    /**
     * @Route("/book/delete/{id}", name="eliminar_contacto")
     */
    public function delete(ManagerRegistry $doctrine, $id): Response
    {
        $entityManager = $doctrine->getManager();
        $repository = $doctrine->getRepository(Book::class);
        $book = $repository->find($id);

        if ($book) {
            try {
                $entityManager->remove($book);
                $entityManager->flush();
                return new Response("Book deleted successfully");
            } catch (\Exception $e) {
                return new Response("Error deleting book");
            }
        } else {
            return $this->render('book_list.html.twig', [
                'book' => null
            ]);
        }
    }

}
