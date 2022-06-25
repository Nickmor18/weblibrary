<?php

namespace App\Models;

class Book extends Model
{
    protected $table = "books";

    /**
     * Добавление книги если ее нет в БД
     *
     * @param array $book
     * @param array $authors
     * @return mixed
     */
    public function addIfNotExist(array $book, array $authors)
    {
        $needBook = $this->getByUid($book['uid']);
        if (!$needBook) {
            $needBook = $this->insertData([
                "uid" => $book['uid'],
                "title" => $book['title'],
                "description" => empty($book['description']) ? "" : $book['description']
            ]);
            $this->setAuthorForBook($needBook->id, $authors);
        }

        return $needBook->id;
    }

    /**
     * Получение книги по Uid
     *
     * @param $uid
     * @return mixed
     */
    public function getByUid($uid): mixed
    {
        $query = $this->db->query("SELECT * FROM {$this->table} WHERE uid = '{$uid}'");
        return $query->fetchObject();
    }

    /**
     * Привязка авторов к книге
     *
     * @param $bookId
     * @param array $authorIds
     * @return void
     */
    private function setAuthorForBook($bookId, array $authorIds): void
    {
        foreach ($authorIds as $authorId) {
            $this->insertData([
                "author_id" => $authorId,
                "book_id" => $bookId,
            ],
                "book_authors");
        }
    }

    /**
     * Добавление книги в общий список польвателя
     *
     * @param $user
     * @param $bookId
     * @return mixed
     */
    public function addBookToUserList($user, $bookId)
    {
        $needUserBook = $this->checkUserBook($user->id, $bookId);
        if (!$needUserBook) {
            $userBook = $this->insertData([
                "user_id" => $user->id,
                "book_id" => $bookId,
            ], "user_book");
        }

        return $needUserBook->id;
    }

    /**
     * Проверка книги у пользователя
     *
     * @param $userId
     * @param $bookId
     * @return mixed
     */
    public function checkUserBook($userId, $bookId): mixed
    {
        $query = $this->db->query("SELECT * FROM user_book WHERE user_id = {$userId} AND book_id = {$bookId}");
        return $query->fetchObject();
    }

    /**
     * Получение всех книг польвателя по $userId
     *
     * @param $userId
     * @return array
     */
    public function getUserBooks($userId)
    {
        $query = $this->db->query("SELECT * FROM user_book WHERE user_id = {$userId}");
        $arUserBooks = [];

        foreach ($query->fetchAll() as $userBook) {
            $arUserBooks[] = $this->getItem($userBook['book_id']);
        }

        return $arUserBooks;
    }

    /**
     * Кол-во книг у пользвоателей
     *
     * @param $bookId
     * @return integer
     */
    public function getUserBookCountById($bookId)
    {
        $query = $this->db->query("SELECT count(*) as kol FROM user_book WHERE book_id = {$bookId}");
        $result = $query->fetchObject();

        return intval($result->kol);
    }

    /**
     *  Получение всех избранных книг польвателя по $userId
     *
     * @param $userId
     * @return array
     */
    public function getUserFavoriveBooks($userId)
    {
        $query = $this->db->query("SELECT * FROM user_book WHERE user_id = {$userId} AND favorite = 1");
        $arUserBooks = [];

        foreach ($query->fetchAll() as $userBook) {
            $arUserBooks[] = $this->getItem($userBook['book_id']);
        }

        return $arUserBooks;
    }

    /**
     * Добавление книги пользователя в список избранного
     *
     * @param $bookId
     * @param $userId
     * @return bool
     */
    public function setFavoriteBook($bookId, $userId): bool
    {
        $sql = "UPDATE user_book 
            SET favorite = 1
            WHERE user_id = :user_id AND book_id = :book_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":user_id", $userId);
        $stmt->bindValue(":book_id", $bookId);

        return $stmt->execute();
    }

    /**
     * Удаление книги пользователя
     *
     * @param $bookId
     * @param $userId
     * @return bool
     */
    public function deleteBookForUser($bookId, $userId): bool
    {
        if ($this->getUserBookCountById($bookId) <= 1)
            return $this->deleteBook($bookId);

        return $this->deleteUserBook();
    }

    public function deleteBook($bookId): bool
    {
        $sql = "DELETE FROM books 
            WHERE id = :book_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":book_id", $bookId);
        return $stmt->execute();
    }

    public function deleteUserBook($bookId, $userId): bool
    {
        $sql = "DELETE FROM user_book 
            WHERE user_id = :user_id AND book_id = :book_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":user_id", $userId);
        $stmt->bindValue(":book_id", $bookId);

        return $stmt->execute();
    }

}