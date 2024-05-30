<?php 

namespace Camagru\models;

use Camagru\database\Database;

abstract class Model
{
    protected $db;
    protected $table;

    public function __construct()
    {
        $this->db = new Database();
    }

    public static function where($column, $value)
    {
        $model = new static();
        return $model->get(['*'], ["{$column} = '{$value}'"]);
    }

    protected static function get($columns = ['*'], $where = [])
    {
        $model = new static();
        $sql = "SELECT " . implode(', ', $columns) . " FROM {$model->table}";
        if (!empty($where)) {
            $sql .= " WHERE " . implode(' AND ', $where);
        }
        return $model->db->query($sql);
    }

    public static function getAll()
    {
        $model = new static();
        return $model->query('SELECT * FROM ' . $model->table);
    }

    public static function getById($id)
    {
        $model = new static();
        return $model->query('SELECT * FROM ' . $model->table . ' WHERE id = ?', [$id], true);
    }

    public static function getBySlug($slug)
    {
        $model = new static();
        return $model->query('SELECT * FROM ' . $model->table . ' WHERE slug = ?', [$slug], true);
    }

    public static function insert($data)
    {
        $model = new static();
        $columns = implode(', ', array_keys($data));
        $values = implode(', ', array_values($data));
        $sql = "INSERT INTO {$model->table} ({$columns}) VALUES ({$values})";
        return $model->db->execute($sql);
    }

    protected function update($data, $where = [])
    {
        $set = [];
        foreach ($data as $column => $value) {
            $set[] = "{$column} = {$value}";
        }
        $sql = "UPDATE {$this->table} SET " . implode(', ', $set);
        if (!empty($where)) {
            $sql .= " WHERE " . implode(' AND ', $where);
        }
        return $this->db->execute($sql);
    }

    public static function delete($where = [])
    {
        $model = new static();
        $sql = "DELETE FROM {$model->table}";
        if (!empty($where)) {
            $sql .= " WHERE " . implode(' AND ', $where);
        }
        return $model->db->execute($sql);
    }

    public static function count($where = [])
    {
        $model = new static();
        $sql = "SELECT COUNT(*) FROM {$model->table}";
        if (!empty($where)) {
            $sql .= " WHERE " . implode(' AND ', $where);
        }
        return $model->db->query($sql);
    }

    public static function exists($where = [])
    {
        $model = new static();
        $sql = "SELECT EXISTS(SELECT 1 FROM {$model->table}";
        if (!empty($where)) {
            $sql .= " WHERE " . implode(' AND ', $where);
        }
        $sql .= ") AS `exists`";
        return $model->db->query($sql);
    }

    protected function query($sql)
    {
        return $this->db->query($sql);
    }

    protected function execute($sql)
    {
        return $this->db->execute($sql);
    }

    public static function last($column = 'id')
    {
        $model = new static();
        $sql = "SELECT MAX({$column}) FROM {$model->table}";
        return $model->db->query($sql);
    }

    public static function first($column = 'id')
    {
        $model = new static();
        $sql = "SELECT MIN({$column}) FROM {$model->table}";
        return $model->db->query($sql);
    }
}