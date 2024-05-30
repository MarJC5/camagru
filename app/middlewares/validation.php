<?php

namespace Camagru\middlewares;

use Camagru\database\Database;

class Validation
{
    private $errors = [];

    public function validate($data, $rules)
    {
        foreach ($rules as $field => $ruleset) {
            $value = $data[$field] ?? null;

            foreach (explode('|', $ruleset) as $rule) {
                $this->applyRule($field, $value, $rule);
            }
        }

        return $this;
    }

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
            case 'image':
                $this->checkImage($field, $value);
                break;
            case 'exists':
                $this->checkExists($field, $value, $ruleValue);
                break;
        }
    }

    private function checkUnique($field, $value, $table)
    {
        $db = new Database();
        $result = $db->query("SELECT COUNT(*) FROM {$table} WHERE {$field} = ?", [$value]);
        if ($result[0][0] > 0) {
            $this->addError($field, "The {$field} has already been taken.");
        }
    }

    private function addError($field, $message)
    {
        $this->errors[$field][] = $message;
    }

    public function fails()
    {
        return !empty($this->errors);
    }

    public function getErrors()
    {
        return $this->errors;
    }

    private function checkImage($field, $value)
    {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $image = getimagesize($value['tmp_name']);
        if (!in_array($image['mime'], $allowedTypes)) {
            $this->addError($field, "The {$field} must be an image.");
        }
    }

    private function checkExists($field, $value, $table)
    {
        $db = new Database();
        $result = $db->query("SELECT COUNT(*) FROM {$table} WHERE {$field} = ?", [$value]);
        if ($result[0][0] == 0) {
            $this->addError($field, "The {$field} does not exist.");
        }
    }
}
