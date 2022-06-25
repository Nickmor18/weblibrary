<?php

namespace App\Models;


use Symfony\Component\HttpFoundation\Request;

abstract class Model
{
    protected $db;
    protected $table;
    public string $error;

    public function __construct(\PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Вывод любых данных
     * @return array
     */
    public function getData()
    {
        $query = $this->db->query("SELECT * from {$this->table}");

        return $query->fetchAll();
    }

    public function getItem(int $id)
    {
        $query = $this->db->query("SELECT * from {$this->table} 
                                            WHERE id = $id");
        return $query->fetchObject();
    }

    /**
     * Кастомная валидация, добавить все что нужно
     *
     * @param Request $request
     * @return bool
     */
    public function validate(Request $request): bool
    {
        $validated = true;
        $uniqueFields = self::getUniqueFields();

        if (count($uniqueFields) > 0) {
            foreach ($uniqueFields as $field) {

                if (!$request->get($field)) {
                    $this->error = "Поле $field не указано";
                    return false;
                }

                if (!$this->isUnique($field, $request->get($field))) {
                    $validated = false;
                    $this->error = "Поле $field должно быть уникально";
                }
            }
        }
        return $validated;
    }

    public function isUnique($field, $fieldValue)
    {
        $query = $this->db->query("SELECT COUNT(*) from {$this->table} WHERE $field = '{$fieldValue}'");
        $df = intval($query->fetchColumn());

        return $df == 0;
    }

    public static function getUniqueFields()
    {
        return static::getUnique();
    }

    /**
     * Добавление любых данных
     * @param array $params
     * @return bool|object
     */
    public function insertData(array $params, $insertTable = null)
    {
        $table = is_null($insertTable) ? $this->table : $insertTable;
        $values = [];
        $out = "";
        $i = 0;
        foreach ($params as $key => $value) {
            $values[] = $value;
            $out .= ($i) ? ",?" : "?";
            $i++;
        }

        $sqlPrepare = "INSERT INTO {$table}(" . implode(",",
                array_keys($params)) . ")
                             VALUES($out)";

        $statement = $this->db->prepare($sqlPrepare);
        $status = $statement->execute($values);

        if ($status) {
            $id = $this->db->lastInsertId();
            return $this->getItem($id);
        }
        return $status;
    }

}