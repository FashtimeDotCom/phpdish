<?php

namespace PHPDish\Bundle\PostBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BookController extends Controller
{
    use ManagerTrait;

    use \PHPDish\Bundle\UserBundle\Controller\ManagerTrait;

    /**
     * 查看书籍
     *
     * @Route("/books/{slug}", name="book_view", requirements={"slug": "[\w-]+"})
     * @param string $slug
     * @param Request $request
     * @return Response
     */
    public function viewAction($slug, Request $request)
    {
        $book = $this->getBookManager()->findBook($slug);
        $character = $this->getBookManager()->findCharacter(1);
        return $this->render('PHPDishWebBundle:Book:view.html.twig', [
            'book' => $book,
            'character' => $character
        ]);
    }

    /**
     * 查看书籍具体章节
     *
     * @Route("/books/{slug}/characters/{characterId}", name="book_character_view", requirements={"slug": "[\w-]+", "characterId": "\d+"})
     * @param string $slug
     * @param int $characterId
     * @param Request $request
     * @return Response
     */
    public function viewCharacterAction($slug, $characterId, Request $request)
    {
        $book = $this->getBookManager()->findBook($slug);
        $character = $this->getBookManager()->findCharacter($characterId);
        return $this->render('PHPDishWebBundle:Book:view.html.twig', [
            'book' => $book,
            'character' => $character
        ]);
    }

    /**
     * 查看用户的书籍
     *
     * @Route("/users/{username}/books", name="user_books")
     *
     * @param string  $username
     * @param Request $request
     *
     * @return Response
     */
    public function getUserBooksAction($username, Request $request)
    {
        $user = $this->getUserManager()->findUserByName($username);
        $books = $this->getBookManager()->findUserBooks($user);
        dump($books);
        return $this->render('PHPDishWebBundle:Book:user_books.html.twig', [
            'user' => $user,
            'books' => $books
        ]);
    }
}
