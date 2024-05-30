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

    protected function get($tableName, $columns = ['*'], $where = [])
    {
        $sql = "SELECT " . implode(', ', $columns) . " FROM {$tableName}";
        if (!empty($where)) {
            $sql .= " WHERE " . implode(' AND ', $where);
        }
        return $this->db->query($sql);
    }

    public function getAll()
    {
        return $this->query('SELECT * FROM ' . $this->table);
    }

    public function getById($id)
    {
        return $this->query('SELECT * FROM ' . $this->table . ' WHERE id = ?', [$id], true);
    }

    public function getBySlug($slug)
    {
        return $this->query('SELECT * FROM ' . $this->table . ' WHERE slug = ?', [$slug], true);
    }

    protected function insert($tableName, $data)
    {
        $columns = implode(', ', array_keys($data));
        $values = implode(', ', array_values($data));
        $sql = "INSERT INTO {$tableName} ({$columns}) VALUES ({$values})";
        return $this->db->execute($sql);
    }

    protected function update($tableName, $data, $where = [])
    {
        $set = [];
        foreach ($data as $column => $value) {
            $set[] = "{$column} = {$value}";
        }
        $sql = "UPDATE {$tableName} SET " . implode(', ', $set);
        if (!empty($where)) {
            $sql .= " WHERE " . implode(' AND ', $where);
        }
        return $this->db->execute($sql);
    }

    protected function delete($tableName, $where = [])
    {
        $sql = "DELETE FROM {$tableName}";
        if (!empty($where)) {
            $sql .= " WHERE " . implode(' AND ', $where);
        }
        return $this->db->execute($sql);
    }

    protected function count($tableName, $where = [])
    {
        $sql = "SELECT COUNT(*) FROM {$tableName}";
        if (!empty($where)) {
            $sql .= " WHERE " . implode(' AND ', $where);
        }
        return $this->db->query($sql);
    }

    protected function exists($tableName, $where = [])
    {
        $sql = "SELECT EXISTS(SELECT 1 FROM {$tableName}";
        if (!empty($where)) {
            $sql .= " WHERE " . implode(' AND ', $where);
        }
        $sql .= ") AS `exists`";
        return $this->db->query($sql);
    }

    protected function query($sql)
    {
        return $this->db->query($sql);
    }

    protected function execute($sql)
    {
        return $this->db->execute($sql);
    }

    protected function last($tableName, $column = 'id')
    {
        $sql = "SELECT MAX({$column}) FROM {$tableName}";
        return $this->db->query($sql);
    }

    protected function first($tableName, $column = 'id')
    {
        $sql = "SELECT MIN({$column}) FROM {$tableName}";
        return $this->db->query($sql);
    }
}