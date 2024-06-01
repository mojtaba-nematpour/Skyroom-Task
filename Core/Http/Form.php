<?php

namespace Core\Http;

use Core\Basic\Messages;
use Exception;

class Form
{
    protected array $options = [
        'allowExtraFields' => false,
        'removeExtraFields' => true
    ];

    protected array $fields = [];

    protected array $guard = [];

    protected array $validations = [];

    protected ?Request $request;

    public function handle(?Request $request): static
    {
        $this->request = $request;

        return $this;
    }

    public function validate(): array
    {
        if ($this->request === null) {
            throw new Exception('Form must be handled first');
        }

        $validationMessages = Messages::get('validations');
        $formMessages = Messages::get('form');

        $messages = [];

        $extraFields = array_diff(array_keys($this->request->requests()), $this->fields);
        if ($this->options['allowExtraFields'] === false && $this->options['removeExtraFields'] === true && count($extraFields) > 0) {
            foreach ($extraFields as $field) {
                $this->request->removeRequest($field);
            }
        }

        foreach ($this->fields as $field) {
            $value = $this->request->requests($field);

            if (empty($this->validations[$field])) {
                continue;
            }

            $validations = $this->validations[$field];
            if ((!isset($validations['required']) || $validations['required'] === true) && empty($value)) {
                $messages[$field] = sprintf($validationMessages['required'], $formMessages[$field]);
                continue;
            }

            if (isset($validations['required']) && $validations['required'] === false && empty($value)) {
                continue;
            }

            foreach ($validations as $validation => $requirment) {
                if ((!isset($validation['required']) || $validation['required'] === true) && empty($value)) {
                    $messages[$field] = sprintf($validationMessages['required'], $formMessages[$field]);
                    continue;
                }

                $parameters = [
                    $validationMessages[$validation],
                    $formMessages[$field]
                ];

                $valid = match ($validation) {
                    'min' => $value >= $requirment && $parameters[] = $requirment,
                    'max' => $value <= $requirment && $parameters[] = $requirment,
                    'minLength' => strlen($value) >= $requirment && $parameters[] = $requirment,
                    'maxLength' => strlen($value) <= $requirment && $parameters[] = $requirment,
                    'email' => (bool)filter_var($value, FILTER_VALIDATE_EMAIL),
                    'equal' => $value === $this->request->requests($requirment),
                    'match' => preg_match("/$requirment/", $value),
                    default => true
                };

                if (!$valid) {
                    $messages[$field] = sprintf($validationMessages[$validation], $formMessages[$field], $requirment);
                }
            }
        }

        return $messages;
    }

    public function denormalize(string $model): object
    {
        if (!class_exists($model)) {
            throw new Exception("Model class $model doesn't exists");
        }

        $model = new $model();
        foreach ($this->fields as $field) {
            if (in_array($field, $this->guard))  {
                continue;
            }

            $field = ucfirst($field);
            $setMethod = "set$field";

            $model->$setMethod($this->request->requests(lcfirst($field)));
        }

        return $model;
    }
}
