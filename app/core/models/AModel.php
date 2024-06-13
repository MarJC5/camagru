<?php

namespace Camagru\core\models;

use Camagru\core\database\Database;
use Camagru\helpers\Logger;

abstract class AModel
{
    protected $db;
    protected $table;
    protected $query;
    protected $data;
    protected $fillable = [];
    protected $hidden = [];

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

    public function id()
    {
        return $this->data->id;
    }

    public function created_at()
    {
        return $this->data->created_at;
    }

    public function updated_at()
    {
        return $this->data->updated_at;
    }

    public function save()
    {
        $data = (array) $this->data;
        $data = array_intersect_key($data, array_flip($this->fillable));

        if ($this->id()) {
            return $this->update($data);
        } else {
            return $this->insert($data);
        }
    }

    public function map($callback)
    {
        $data = $this->get();
        // convert all to self object
        $data = array_map(function ($item) {
            $instance = new static();
            $instance->data = (object) $item;
            return $instance;
        }, $data);

        return array_map($callback, $data);
    }

    public static function paginate($offset, $limit, $filter = []) {
        $instance = new static();
        $sql = "";
        if ($filter) {
            $sql = "SELECT * FROM {$instance->table} WHERE {$filter['key']} = {$filter['value']} LIMIT {$limit} OFFSET {$offset}";
        } else {
            $sql = "SELECT * FROM {$instance->table} LIMIT {$limit} OFFSET {$offset}";
        }
        
        return $instance->db->query($sql);
    }

    public static function find($id)
    {
        $instance = new static();
        $instance->query = "SELECT * FROM {$instance->table} WHERE id = " . $instance->db->quote($id);

        return $instance->first();
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
        $data = $this->db->query($this->query);

        // Hide hidden columns
        foreach ($data as $key => $value) {
            foreach ($this->hidden as $hidden) {
                unset($data[$key][$hidden]);
            }
        }

        return $data;
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
        foreach ($data as $key => $value) {
            $set[] = "{$key} = " . $this->db->quote($value);
        }

        $set = implode(', ', $set);
        $sql = "UPDATE {$this->table} SET {$set} WHERE id = " . $this->id();

        $update = $this->db->execute($sql);

        // update the data property
        if ($update) {
            foreach ($data as $key => $value) {
                $this->data->$key = $value;
            }
        } else {
            Logger::log("Failed to update {$this->table} with id {$this->id()}");
        }

        return $update;
    }

    public function delete()
    {
        $sql = "DELETE FROM {$this->table} WHERE id = " . $this->id();
        return $this->db->execute($sql);
    }

    public function toArray()
    {
        return (array) $this->data;
    }

    public static function count($column = null, $value = null)
    {
        $instance = new static();
        $sql = "SELECT COUNT(*) FROM {$instance->table}";
        if ($column && $value) {
            $sql .= " WHERE {$column} = " . $instance->db->quote($value);
        }
        return $instance->db->query($sql)[0]['COUNT(*)'];
    }

    protected function quote($value)
    {
        return $this->db->quote($value);
    }
}