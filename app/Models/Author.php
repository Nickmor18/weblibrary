<?php

namespace App\Models;

class Author extends Model
{
    protected $table = "authors";

    /**
     * Добавление пользователя при отсутствии
     *
     * @param $name
     * @return bool|object|null
     */
    public function addIfNotExist($name){
        if (!$this->getByName($name))
            return $this->insertData(["name" => $name]);

        return false;
    }

    /**
     * Получение автора по имени
     *
     * @param $name
     * @return false|mixed|object
     */
    public function getByName($name){
        $query = $this->db->query("SELECT * FROM {$this->table} WHERE name = '{$name}'");
        return $query->fetchObject();
    }

    /**
     * Получение авторов по id книги
     *
     * @param $bookId
     * @return array
     */
    public function getByBookId($bookId): array
    {
        $query = $this->db->query("SELECT * FROM book_authors WHERE book_id = {$bookId}");
        $arAuthor = [];

        foreach ($query->fetchAll() as $bookAuthor) {
            $arAuthor[] = $this->getItem($bookAuthor['author_id']);
        }

        return $arAuthor;
    }
}