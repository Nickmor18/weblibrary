<?php

namespace App\Controllers;

use App\Models\Author;
use App\Models\Book;

class LibraryController extends BaseController
{

    /**
     * Список всех книг
     *
     * @return template
     * @throws \App\Exceptions\LibraryException
     */
    public function index()
    {
        $user = $this->getUserIfExist();
        $config = $this->getConfig();

        $book = new Book($this->di['db']);
        $userBooks = $book->getUserBooks($user->id);


        $params = [
            'url' => $config['url'],
            'user' => $user,
            'books' => $userBooks
        ];
        return $this->view('Library/list', $params);
    }

    /**
     * Поиск/добавление книги
     *
     * @return template
     * @throws \App\Exceptions\LibraryException
     */
    public function search()
    {
        $user = $this->getUserIfExist();
        $config = $this->getConfig();

        $params = [
            'url' => $config['url'],
            'user' => $user
        ];

        return $this->view('Library/search', $params);
    }

    /**
     * Список избранного
     *
     * @return template
     * @throws \App\Exceptions\LibraryException
     */
    public function favorite()
    {

        $user = $this->getUserIfExist();
        $config = $this->getConfig();

        $book = new Book($this->di['db']);
        $userBooks = $book->getUserFavoriveBooks($user->id);

        $params = [
            'url' => $config['url'],
            'user' => $user,
            'books' => $userBooks
        ];
        return $this->view('Library/favorite', $params);
    }

    /**
     * Пусть все зарегестированные польтели могут видеть все книги
     *
     * @return void
     * @throws \App\Exceptions\LibraryException
     */
    public function info()
    {
        $request = $this->di['request'];
        $data = $request->query->all();

        $user = $this->getUserIfExist();
        $config = $this->getConfig();

        // Пусть все зарегестированные польтели могут видеть все книги
        $bookObj = new Book($this->di['db']);
        $book = $bookObj->getItem($data['id']);

        $authorObj = new Author($this->di['db']);
        $bookAuthors = $authorObj->getByBookId($data['id']);

        $params = [
            'url' => $config['url'],
            'user' => $user,
            'book' => $book,
            'authors' => $bookAuthors
        ];
        return $this->view('Library/info', $params);
    }

    /**
     * Добавление книги для пользователя
     *
     * @return void
     */
    public function addbook()
    {
        $request = $this->di['request'];
        $requestBody = json_decode(file_get_contents('php://input'), true);

        //немного валидации. недостаточно, некоторые спецефические книги не добавятся
        $requestMethod = $request->server->get('REQUEST_METHOD');
        if ($requestMethod != 'POST')
            abort404();
        $user = $this->getUserIfExist();
        if (empty($requestBody['title'])) {
            $arResult['error'] = [
                "code" => 400,
                "message" => "Ошибка добавления",
            ];
            echo(json_encode($arResult, JSON_UNESCAPED_UNICODE));
        }

        //информация о авторах
        $arAuthorIds = [];
        if (!empty($requestBody['authors'])) {
            $author = new Author($this->di['db']);
            foreach ($requestBody['authors'] as $authorName) {
                $newAuthor = $author->addIfNotExist($authorName);
                $newAuthorObject = !empty($newAuthor) ? $newAuthor : $author->getByName($authorName);
                $arAuthorIds[] = $newAuthorObject->id;
            }
        }

        //добавим книгу
        $book = new Book($this->di['db']);
        $needBookId = $book->addIfNotExist($requestBody, $arAuthorIds);
        $book->addBookToUserList($user, $needBookId);

        $arResult['success'] = [
            "code" => 200,
            "message" => "Книга успешно добавлена",
        ];
        echo(json_encode($arResult, JSON_UNESCAPED_UNICODE));
    }

    /**
     * Книгу в избранное
     *
     * @return void
     */
    public function addFavoriteBook()
    {
        $user = $this->getUserIfExist();
        $requestBody = json_decode(file_get_contents('php://input'), true);

        if (empty($requestBody['bookId'])){
            $arResult['error']['message'] = "Ошибка добавления книги в избранное";
            echo json_encode($arResult, JSON_UNESCAPED_UNICODE);
            return;
        }

        $book = new Book($this->di['db']);
        if ($book->setFavoriteBook($requestBody['bookId'], $user->id)){
            $arResult['success']['message'] = "Книга добавлена в избранное";
        } else {
            $arResult['error']['message'] = "Книга добавлена в избранное";
        }

        echo(json_encode($arResult, JSON_UNESCAPED_UNICODE));
    }

    public function deleteBook()
    {
        $user = $this->getUserIfExist();
        $requestBody = json_decode(file_get_contents('php://input'), true);


        if (empty($requestBody['bookId'])){
            $arResult['error']['message'] = "Ошибка удаления книги";
            echo json_encode($arResult, JSON_UNESCAPED_UNICODE);
            return;
        }

        $book = new Book($this->di['db']);
        if ($book->deleteBookForUser($requestBody['bookId'], $user->id)){
            $arResult['success']['message'] = "Книга удалена из Ващего списка";
        } else {
            $arResult['error']['message'] = "Ошибка удаления книги";
        }

        echo(json_encode($arResult, JSON_UNESCAPED_UNICODE));
    }


}