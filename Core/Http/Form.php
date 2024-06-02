<?php

namespace Core\Http;

use Core\Basic\Messages;
use Exception;

/**
 * Core class to interact with requests - models
 */
abstract class Form
{
    /**
     * Configuration of Form behavior
     *
     * @var array
     */
    protected array $options = [
        'allowExtraFields' => false,
        'removeExtraFields' => true
    ];

    /**
     * Form data either form request or manual data
     *
     * @var array
     */
    protected array $data = [];

    /**
     * Form files
     *
     * @var array
     */
    protected array $fields = [];

    /**
     * Ignores some filed from denormalization
     *
     * @var array
     */
    public array $guard = [];

    /**
     * Field validations
     *
     * @var array
     */
    protected array $validations = [];

    public ?Request $request;

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * Sets data and guards them
     *
     * @param array $data
     *
     * @return $this
     */
    public function setData(array $data): static
    {
        /**
         * Applying guard
         */
        foreach ($this->guard as $item) {
            unset($data[$item]);
        }

        $this->data = $data;

        return $this;
    }

    /**
     * Handling form from a request
     *
     * @param Request|null $request
     *
     * @return $this
     */
    public function handle(?Request $request): static
    {
        $this->request = $request;
        $this->setData($request->requests());

        return $this;
    }

    /**
     * Validates with $validation, the fields defined with $fields
     *
     * @return array containing the validation messages
     *
     * @throws Exception
     */
    public function validate(): array
    {
        $data = $this->request->requests();
        $extraFields = array_diff(array_keys($data), $this->fields);
        /**
         * Applying form behavior on extra fields
         */
        if ($this->options['allowExtraFields'] === false && $this->options['removeExtraFields'] === true && count($extraFields) > 0) {
            foreach ($extraFields as $field) {
                unset($data[$field]);
            }
        }

        $messages = [];
        $formMessages = Messages::get('form');
        $validationMessages = Messages::get('validations');
        /**
         * Load form  and validation messages and start validating the fields of data
         */
        foreach ($this->fields as $field) {
            /**
             * Ignore validation if it's not defined for this field
             */
            if (empty($this->validations[$field])) {
                continue;
            }

            $value = $data[$field];

            /**
             * Check for required fields
             */
            $validations = $this->validations[$field];
            if ((!isset($validations['required']) || $validations['required'] === true) && empty($value)) {
                $messages[$field] = sprintf($validationMessages['required'], $formMessages[$field]);
                continue;
            }

            /**
             * Ignore not necessary fields
             */
            if (isset($validations['required']) && $validations['required'] === false && empty($value)) {
                continue;
            }

            /**
             * Applying the validations
             */
            foreach ($validations as $validation => $requirment) {
                /**
                 * Check for required fields
                 */
                if ((!isset($validation['required']) || $validation['required'] === true) && empty($value)) {
                    $messages[$field] = sprintf($validationMessages['required'], $formMessages[$field]);
                    continue;
                }

                /**
                 * Parameters to inject in sprintf
                 */
                $parameters = [
                    $validationMessages[$validation],
                    $formMessages[$field]
                ];

                /**
                 * Validating
                 */
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

    /**
     * Denormalize - convert a form request to provided model
     *
     * @param string $model
     * @return mixed
     *
     * @throws Exception
     */
    public function denormalize(string $model): object
    {
        /**
         * Checks if model class exist
         */
        if (!class_exists($model)) {
            throw new Exception("Model class $model doesn't exists");
        }

        $model = new $model();
        foreach ($this->data as $field => $data) {
            /**
             * Applying guard
             */
            if (in_array($field, $this->guard)) {
                continue;
            }

            $field = ucfirst($field);
            $setMethod = "set$field";
            /**
             * Dynamic call the setters function
             */
            $model->$setMethod($data);
        }

        return $model;
    }
}
