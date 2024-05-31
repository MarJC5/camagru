<?php

namespace Camagru\core\models;

use Camagru\core\database\Database;

abstract class AModel
{
    protected $db;
    protected $table;
    protected $query;
    protected $data;

    public function __construct(?int $id = null)
    {
        $this->db = new Database();
        
        if ($id) {
            $this->query = "SELECT * FROM {$this->table} WHERE id = " . $this->db->quote($id);

            return $this->first();
        } else {
            $this->query = "";
        }

        return $this;
    }

    public static function where($column, $value)
    {
        $instance = new static();
        $instance->query = "SELECT * FROM {$instance->table} WHERE {$column} = " . $instance->db->quote($value);

        return $instance;
    }

    public function andWhere($column, $value)
    {
        $this->query .= " AND {$column} = " . $this->db->quote($value);
        return $this;
    }

    public function get()
    {
        return $this->db->query($this->query);
    }

    public function first()
    {
        $sql = $this->query . " LIMIT 1";
        $result = $this->db->query($sql);
        $this->data = $result ? (object) $result[0] : null;

        if ($this->data) {
            return $this;
        }

        return null;
    }

    public static function all()
    {
        $instance = new static();
        $sql = "SELECT * FROM {$instance->table}";
        return $instance->db->query($sql);
    }

    public function insert($data)
    {
        $columns = implode(', ', array_keys($data));
        $values = implode(', ', array_map([$this->db, 'quote'], array_values($data)));
        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$values})";
        return $this->db->execute($sql);
    }

    public function update($data)
    {
        $set = [];
        foreach ($data as $column => $value) {
            $set[] = "{$column} = " . $this->db->quote($value);
        }
        $sql = "UPDATE {$this->table} SET " . implode(', ', $set) . " WHERE " . $this->query;
        return $this->db->execute($sql);
    }

    public function delete()
    {
        $sql = "DELETE FROM {$this->table} WHERE " . $this->query;
        return $this->db->execute($sql);
    }

    public static function count()
    {
        $instance = new static();
        $sql = "SELECT COUNT(*) FROM {$instance->table}";
        return $instance->db->query($sql)[0]['COUNT(*)'];
    }

    protected function quote($value)
    {
        return $this->db->quote($value);
    }
}