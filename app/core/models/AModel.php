<?php

namespace Camagru\core\models;

use Camagru\core\database\Database;
use Camagru\helpers\Logger;

/**
 * Class AModel
 * Base model class providing common functionalities for all models.
 */
abstract class AModel
{
    protected $db;
    protected $table;
    protected $query;
    protected $data;
    protected $fillable = [];
    protected $hidden = [];

    /**
     * AModel constructor.
     *
     * @param int|null $id The ID of the model to load.
     */
    public function __construct(?int $id = null)
    {
        $this->db = new Database();

        if ($id) {
            $this->query = "SELECT * FROM {$this->table} WHERE id = " . $this->db->quote($id);
            return $this->first();
        } else {
            $this->query = "";

            return null;
        }

        return $this;
    }

    /**
     * Get the ID of the model.
     *
     * @return int
     */
    public function id()
    {
        return $this->data->id ?? null;
    }

    /**
     * Get the created_at timestamp.
     *
     * @return string
     */
    public function created_at()
    {
        return $this->data->created_at ?? null;
    }

    /**
     * Get the updated_at timestamp.
     *
     * @return string
     */
    public function updated_at()
    {
        return $this->data->updated_at ?? null;
    }

    /**
     * Save the model to the database.
     *
     * @return bool
     */
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

    /**
     * Get a random record(s) from the database.
     * 
     * @param int $limit The number of records to return.
     * @return array An array of model instances.
     */
    public static function random($limit = 3)
    {
        $instance = new static();
        $sql = "SELECT * FROM {$instance->table} ORDER BY RAND() LIMIT {$limit}";
        
        $results = $instance->db->query($sql);
        return array_map(function ($item) {
            $model = new static();
            $model->data = (object) $item;
            return $model;
        }, $results);
    }

    /**
     * Map a callback function to each item in the collection.
     *
     * @param callable $callback The callback function.
     * @return array The resulting collection.
     */
    public function map($callback)
    {
        $data = $this->get();
        $data = array_map(function ($item) {
            $instance = new static();
            $instance->data = (object) $item;
            return $instance;
        }, $data);

        return array_map($callback, $data);
    }

    /**
     * Paginate the results.
     *
     * @param int $offset The offset of the first result.
     * @param int $limit The maximum number of results to return.
     * @param array $filter Optional filter criteria.
     * @return array The paginated results.
     */
    public static function paginate($offset, $limit, $filter = [])
    {
        $instance = new static();
        $sql = "";
        if ($filter) {
            $sql = "SELECT * FROM {$instance->table} WHERE {$filter['key']} = {$filter['value']} LIMIT {$limit} OFFSET {$offset}";
        } else {
            $sql = "SELECT * FROM {$instance->table} LIMIT {$limit} OFFSET {$offset}";
        }

        return $instance->db->query($sql);
    }

    /**
     * Find a model by its ID.
     *
     * @param int $id The ID of the model.
     * @return static|null The found model, or null if not found.
     */
    public static function find($id)
    {
        $instance = new static();
        $instance->query = "SELECT * FROM {$instance->table} WHERE id = " . $instance->db->quote($id);

        return $instance->first();
    }

    /**
     * Get models that match a specific condition.
     *
     * @param string $column The column to match.
     * @param mixed $value The value to match.
     * @return static
     */
    public static function where($column, $value)
    {
        $instance = new static();
        $instance->query = "SELECT * FROM {$instance->table} WHERE {$column} = " . $instance->db->quote($value);

        return $instance;
    }

    /**
     * Add an additional condition to the query.
     *
     * @param string $column The column to match.
     * @param mixed $value The value to match.
     * @return $this
     */
    public function andWhere($column, $value)
    {
        $this->query .= " AND {$column} = " . $this->db->quote($value);
        return $this;
    }

    /**
     * Get the results of the query.
     *
     * @return array The results of the query.
     */
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

    /**
     * Get the first result of the query.
     *
     * @return static|null The first result, or null if not found.
     */
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

    /**
     * Get all models.
     *
     * @return array All models.
     */
    public static function all()
    {
        $instance = new static();
        $sql = "SELECT * FROM {$instance->table}";
        return $instance->db->query($sql);
    }

    /**
     * Insert a new model into the database.
     *
     * @param array $data The data to insert.
     * @return bool True if the insert was successful, false otherwise.
     */
    public function insert($data)
    {
        $columns = implode(', ', array_keys($data));
        $values = implode(', ', array_map([$this->db, 'quote'], array_values($data)));
        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$values})";
        return $this->db->execute($sql);
    }

    /**
     * Update an existing model in the database.
     *
     * @param array $data The data to update.
     * @return bool True if the update was successful, false otherwise.
     */
    public function update($data)
    {
        $set = [];
        foreach ($data as $key => $value) {
            $set[] = "{$key} = " . $this->db->quote($value);
        }

        $set = implode(', ', $set);
        $sql = "UPDATE {$this->table} SET {$set} WHERE id = " . $this->id();

        $update = $this->db->execute($sql);

        // Update the data property
        if ($update) {
            foreach ($data as $key => $value) {
                $this->data->$key = $value;
            }
        } else {
            Logger::log("Failed to update {$this->table} with id {$this->id()}");
        }

        return $update;
    }

    /**
     * Delete the model from the database.
     *
     * @return bool True if the delete was successful, false otherwise.
     */
    public function delete()
    {
        $sql = "DELETE FROM {$this->table} WHERE id = " . $this->id();
        return $this->db->execute($sql);
    }

    /**
     * Convert the model data to an array.
     *
     * @return array The model data as an array.
     */
    public function toArray()
    {
        return (array) $this->data ?? [];
    }

    /**
     * Count the number of models in the database.
     *
     * @param string|null $column Optional column to match.
     * @param mixed|null $value Optional value to match.
     * @return int The number of models.
     */
    public static function count($column = null, $value = null)
    {
        $instance = new static();
        $sql = "SELECT COUNT(*) FROM {$instance->table}";
        if ($column && $value) {
            $sql .= " WHERE {$column} = " . $instance->db->quote($value);
        }
        return $instance->db->query($sql)[0]['COUNT(*)'];
    }

    /**
     * Quote a value for use in a query.
     *
     * @param mixed $value The value to quote.
     * @return string The quoted value.
     */
    protected function quote($value)
    {
        return $this->db->quote($value);
    }
}
