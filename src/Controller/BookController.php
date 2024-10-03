<?php

namespace App\Controller;

use App\Entity\Author;
use App\Entity\Book;
use App\Form\BookType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
     * @Route("/book/new  ", name="new_book")
     */
    public function new(ManagerRegistry $doctrine, Request $request) {
        $book = new Book();

        $form = $this->createForm(BookType::class, $book);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $book = $form->getData();
            $entityManager = $doctrine->getManager();
            $entityManager->persist($book);
            $entityManager->flush();
            return $this->redirectToRoute('find_book', ["title" => $book->getTitle()]);
        }

        return $this->render('book/new.html.twig', array(
            'form' => $form->createView()
        ));
    }


    /**
     * @Route("/book/edit/{id}", name="edit_book", requirements={"id"="\d+"})
     */
    public function edit(ManagerRegistry $doctrine, Request $request, $id) {
        $repository = $doctrine->getRepository(Book::class);
        $book = $repository->find($id);

        $form = $this->createForm(BookType::class, $book);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $book = $form->getData();
            $entityManager = $doctrine->getManager();
            $entityManager->persist($book);
            $entityManager->flush();
        }

        return $this->render('book/new.html.twig', array(
            'form' => $form->createView()
        ));
    }





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

    /**
     * @Route("/book/insertWithAuthor", name="insert_with_author_book")
     */
    public function insertWithAuthor(ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        $author = new Author();

        $author->setName("Manolo");
        $book = new Book();

        $book->setTitle("The Shadows of Dusk");
        $book->setGenre("Mystery");
        $book->setYear(2012);
        $book->setPages(384);
        $book->setAuthor($author);

        $entityManager->persist($author);
        $entityManager->persist($book);

        $entityManager->flush();
        return $this->render('book/book_data.html.twig', [
            'book' => $book
        ]);
    }

    /**
     * @Route("/book/insertWithoutAuthor", name="insert_without_author_book")
     */
    public function insertWithoutAuthor(ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        $repository = $doctrine->getRepository(Author::class);

        $author = $repository->findOneBy(["name" => "Manolo"]);

        $book = new Book();

        $book->setTitle("Whispers of the Forgotten Shore");
        $book->setGenre("Historical Fiction");
        $book->setYear(2017);
        $book->setPages(312);
        $book->setAuthor($author);;

        $entityManager->persist($book);

        $entityManager->flush();
        return $this->render('book/book_data.html.twig', [
            'book' => $book
        ]);
    }


}
