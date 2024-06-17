<?php

namespace Camagru\core\middlewares;

use Camagru\core\database\Database;
use Camagru\helpers\Config;

/**
 * Class Validation
 * Handles validation of input data against a set of rules.
 */
class Validation
{
    private $errors = [];

    /**
     * Validate the given data against the provided rules.
     *
     * @param array $data The data to validate.
     * @param array $rules The validation rules.
     * @return $this
     */
    public function validate($data, $rules)
    {
        foreach ($rules as $field => $ruleset) {
            $value = $data[$field] ?? null;
            $rulesArray = explode('|', $ruleset);

            // Check if the field is optional and empty
            if (in_array('optional', $rulesArray) && empty($value)) {
                continue;  // Skip further validation for this field
            }

            foreach ($rulesArray as $rule) {
                $this->applyRule($field, $value, $rule);
            }
        }

        return $this;
    }

    /**
     * Apply a single validation rule to a field.
     *
     * @param string $field The field name.
     * @param mixed $value The field value.
     * @param string $rule The validation rule.
     */
    private function applyRule($field, $value, $rule)
    {
        list($ruleName, $ruleValue) = explode(':', $rule) + [null, null];

        switch ($ruleName) {
            case 'required':
                if (empty($value)) {
                    $this->addError($field, "The {$field} is required.");
                }
                break;
            case 'email':
                if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->addError($field, "The {$field} must be a valid email address.");
                }
                break;
            case 'min':
                if (strlen($value) < $ruleValue) {
                    $this->addError($field, "The {$field} must be at least {$ruleValue} characters.");
                }
                break;
            case 'max':
                if (strlen($value) > $ruleValue) {
                    $this->addError($field, "The {$field} may not be greater than {$ruleValue} characters.");
                }
                break;
            case 'alpha_dash':
                if (!preg_match('/^[a-zA-Z0-9_-]+$/', $value)) {
                    $this->addError($field, "The {$field} may only contain letters, numbers, dashes and underscores.");
                }
                break;
            case 'alpha_num':
                if (!ctype_alnum($value)) {
                    $this->addError($field, "The {$field} may only contain letters and numbers.");
                }
                break;
            case 'unique':
                $this->checkUnique($field, $value, $ruleValue);
                break;
            case 'mimes':
                $this->checkImage($field, $value);
                break;
            case 'size':
                if ($value['size'] > $ruleValue) {
                    $this->addError($field, "The {$field} may not be greater than {$ruleValue} bytes.");
                }
                break;
            case 'exists':
                $this->checkExists($field, $value, $ruleValue);
                break;
            case 'optional':
                break;
        }
    }

    /**
     * Check if a value is unique in the specified table.
     *
     * @param string $field The field name.
     * @param mixed $value The field value.
     * @param string $parameters The table name, id column and id value.
     */
    private function checkUnique($field, $value, $parameters)
    {
        list($table, $idColumn, $id) = array_pad(explode(',', $parameters), 3, null);
        $db = new Database();
        $query = "SELECT COUNT(*) FROM {$table} WHERE {$field} = ?";
        $params = [$value];

        if ($id) {
            $query .= " AND {$idColumn} != ?";
            $params[] = $id;
        }

        $result = $db->query($query, $params);
        if ($result[0]['COUNT(*)'] > 0) {
            $this->addError($field, "The {$field} has already been taken.");
        }
    }

    /**
     * Add an error message for a specific field.
     *
     * @param string $field The field name.
     * @param string $message The error message.
     */
    private function addError($field, $message)
    {
        $this->errors[$field][] = $message;
    }

    /**
     * Check if there are any validation errors.
     *
     * @return bool True if there are errors, false otherwise.
     */
    public function fails()
    {
        return !empty($this->errors);
    }

    /**
     * Get the validation errors as a formatted HTML string.
     *
     * @return string The formatted error messages.
     */
    public function getErrors()
    {
        $errors = '';
        foreach ($this->errors as $field => $messages) {
            $field = htmlspecialchars($field);
            $messages = array_map('htmlspecialchars', $messages);
            $errors .= "<li>{$field}: " . implode('</li><li>', $messages) . '</li>';
        }

        return "<ul>{$errors}</ul>";
    }

    /**
     * Check if a file is a valid image.
     *
     * @param string $field The field name.
     * @param array $value The file data.
     */
    private function checkImage($field, $value)
    {
        $config = Config::get('media');
        $allowedExtensions = array_keys($config['allowed']);
        $image = getimagesize($value['tmp_name']);
        if (!in_array($image['mime'], $allowedExtensions)) {
            $this->addError($field, "The {$field} must be an image.");
        }
    }

    /**
     * Check if a value exists in the specified table.
     *
     * @param string $field The field name.
     * @param mixed $value The field value.
     * @param string $table The table name.
     */
    private function checkExists($field, $value, $table)
    {
        $db = new Database();
        $result = $db->query("SELECT COUNT(*) FROM {$table} WHERE {$field} = ?", [$value]);
        if ($result[0]['COUNT(*)'] == 0) {
            $this->addError($field, "The {$field} does not exist.");
        }
    }
}
