<?php

namespace App\Controller;

use App\Entity\Author;
use App\Entity\Book;
use App\Form\BookType;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookController extends AbstractController
{
    private function checkUserLoggedIn()
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('index');
        }
    }

    /**
     * @Route("/book/list", name="book_list")
     */
    public function list(BookRepository $bookRepository): Response
    {
        $this->checkUserLoggedIn();
        $books = $bookRepository->findAll();

        return $this->render('book/index.html.twig', [
            'books' => $books,
        ]);
    }

    /**
     * @Route("/", name="index")
     */
    public function index(BookRepository $bookRepository): Response
    {
        $this->checkUserLoggedIn();

        return $this->render('book/index.html.twig', [
            'books' => $bookRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="book_new")
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->checkUserLoggedIn();

        $book = new Book();
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($book);
            $entityManager->flush();

            return $this->redirectToRoute('index');
        }

        return $this->render('book/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="book_show")
     */
    public function show(Book $book): Response
    {
        $this->checkUserLoggedIn();

        return $this->render('book/show.html.twig', [
            'book' => $book,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="book_edit")
     */
    public function edit(Request $request, Book $book, EntityManagerInterface $entityManager): Response
    {
        $this->checkUserLoggedIn();

        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('index');
        }

        return $this->render('book/edit.html.twig', [
            'book' => $book,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/book/{id}/delete", name="book_delete", methods={"POST"})
     */
    public function delete(Request $request, Book $book, EntityManagerInterface $entityManager): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('index');
        }

        if ($this->isCsrfTokenValid('delete' . $book->getId(), $request->request->get('_token'))) {
            $entityManager->remove($book);
            $entityManager->flush();
        }

        return $this->redirectToRoute('book_index');
    }



    /**
     * @Route("/book/insert", name="insert_book")
     */
    public function insert(ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        // Aquí iría la lógica para insertar libros.

        return new Response("Books inserted");
    }

    /**
     * @Route("/book/find/{title}", name="find_book")
     */
    public function find(ManagerRegistry $doctrine, string $title): Response {
        $repository = $doctrine->getRepository(Book::class);
        $books = $repository->findBy(['title' => $title]);

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
     * @Route("/book/insertWithAuthor", name="insert_with_author_book")
     */
    public function insertWithAuthor(ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        // Aquí iría la lógica para insertar un libro con autor.

        return $this->render('book/book_data.html.twig', [
            'book' => null // Cambia esto según la lógica
        ]);
    }

    /**
     * @Route("/book/insertWithoutAuthor", name="insert_without_author_book")
     */
    public function insertWithoutAuthor(ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        // Aquí iría la lógica para insertar un libro sin autor.

        return $this->render('book/book_data.html.twig', [
            'book' => null // Cambia esto según la lógica
        ]);
    }

}

