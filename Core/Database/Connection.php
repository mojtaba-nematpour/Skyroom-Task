<?php

namespace Core\Database;

use Core\Basic\Messages;
use Core\Http\Form;
use Exception;
use PDO;
use PDOException;
use PDOStatement;

/**
 * Core class to interact with database
 */
class Connection
{
    /**
     * PDO connection object
     *
     * @var PDO
     */
    private PDO $pdo;

    /**
     * Initiates the PDO connection with given config and info
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        try {
            $this->pdo = new PDO(
                $config['connection'] . ';dbname=' . $config['name'],
                $config['username'],
                $config['password'],
                $config['options']
            );

        } catch (PDOException $e) {
            die(json_encode(['error' => $e->getMessage()]));
        }
    }

    /**
     * Executes the statements and catches the exceptions from PDO
     *
     * @param PDOStatement $stmt
     *
     * @return void
     */
    private function execute(PDOStatement $stmt): void
    {
        try {
            $stmt->execute();
        } catch (PDOException $e) {
            $message = ['error' => $e->getMessage()];
            if ($e->getCode() === '23000') {
                $message = ['error' => Messages::get('pdo')[23000]];
            }

            die(json_encode($message));
        }
    }

    /**
     * Creates a model table based on give Schema(Model::Schema)
     *
     * @param string $model model class(Model::class)
     *
     * @return $this
     */
    public function createTable(string $model): static
    {
        $name = $model::Name;
        $schema = $model::Schema;

        $columns = [];
        /**
         * To string the columns and structures
         */
        foreach ($schema as $column => $structure) {
            if (!empty($column)) {
                $column = "`$column`";
            }

            $columns[] = "$column $structure";
        }

        $columns = implode(',', $columns);

        $stmt = $this->pdo->prepare("CREATE TABLE IF NOT EXISTS `$name` ($columns);");
        $this->execute($stmt);

        return $this;
    }

    /**
     * Just drops a table
     *
     * @param string $model model class(Model::class)
     *
     * @return $this
     */
    public function removeTable(string $model): static
    {
        $name = $model::Name;

        $stmt = $this->pdo->prepare("DROP TABLE `$name`;");
        $this->execute($stmt);

        return $this;
    }

    /**
     * Saves a model from a Form::class or an array and inserted into Model::class table
     *
     * @param string $model model class(Model::class)
     * @param array|Form $form it can be either a Form::class or an array to  save based on the provided model
     *
     * @return array|object denormalized output if supported otherwise an array
     *
     * @throws Exception
     */
    public function save(string $model, array|Form $form): array|object
    {
        $name = $model::Name;
        $schema = $model::Schema;

        $columns = [];
        $binds = [];
        $values = $form;
        /**
         * To string the columns and bind columns
         */
        foreach ($schema as $column => $structure) {
            /**
             * Ignore PRIMARY key and ID fields
             */
            if (empty($column) || str_contains($structure, 'AUTO')) {
                continue;
            }

            $columns[] = "`$column`";
            $binds[] = ":$column";
        }

        if ($form instanceof Form) {
            $values = $form->getData();
        }

        $columns = implode(',', $columns);
        $binds = implode(',', $binds);

        $stmt = $this->pdo->prepare("INSERT INTO `$name` ($columns) VALUES ($binds)");

        /**
         * Bind parameters value
         */
        foreach ($values as $bind => $value) {
            /**
             * To string the DateTimeImmutable columns
             */
            if ($value instanceof \DateTimeImmutable) {
                $values[$bind] = $value->format("Y-m-d H:i:s");
            }

            $stmt->bindParam(":$bind", $values[$bind]);
        }

        $this->execute($stmt);

        /**
         * Denormalize if it's a FormRequest otherwise an array
         */
        if ($form instanceof Form) {
            $model = $form->denormalize($model);
            $model->setId($this->pdo->lastInsertId());
        } else {
            $model = array_merge($form, ['id' => $this->pdo->lastInsertId()]);
        }

        return $model;
    }

    /**
     * Finds and selects a model from a Form::class or an array used as WHERE clause
     *
     * @param string $model model class(Model::class)
     * @param array|Form $form it can be either a Form::class or an array to  save based on the provided model
     *
     * @return array|object denormalized output if supported otherwise an array
     *
     * @throws Exception
     */
    public function find(string $model, array|Form $form = []): array|object
    {
        $name = $model::Name;
        $schema = $model::Schema;

        $modelData = $form;
        if ($form instanceof Form) {
            $modelData = $form->getData();
        }

        $wheres = [];
        /**
         * To string the where clauses
         */
        foreach ($modelData as $column => $value) {
            $equal = "= '$value'";
            /**
             * Make it null check-able
             */
            if ($value === null) {
                $equal = 'IS NULL';
            }

            $wheres[] = "`$column` $equal";
        }

        $columns = [];
        /**
         * To string the columns
         */
        foreach ($schema as $column => $structure) {
            if (empty($column)) {
                continue;
            }

            $columns[] = "`$column`";
        }

        $columns = implode(',', $columns);
        $wheres = implode(' AND ', $wheres);

        if (!empty($wheres)) {
            $wheres = "WHERE $wheres";
        }

        $stmt = $this->pdo->prepare("SELECT $columns FROM `$name` $wheres;");
        $this->execute($stmt);

        /**
         * On finding nothing
         */
        if ($stmt->rowCount() < 0) {
            return [];
        }

        $models = [];
        /**
         * TODO::Needs a pagination
         */
        $results = $stmt->fetchAll();
        /**
         * Denormalize if it's a FormRequest otherwise an array
         */
        foreach ($results as $result) {
            if ($form instanceof Form) {
                $form->setData($result);

                $models[] = $form->denormalize($model);
                continue;
            }

            $models[] = $result;
        }

        return $models;
    }

    /**
     * Removes a model from an array used as WHERE clause
     *
     * @param string $model model class(Model::class)
     * @param array $data used as WHERE clause
     *
     * @return void
     *
     */
    public function remove(string $model, array $data): void
    {
        $name = $model::Name;

        $wheres = [];
        /**
         * To string the where clauses
         */
        foreach ($data as $column => $value) {
            $equal = "= '$value'";
            /**
             * Make it null check-able
             */
            if ($value === null) {
                $equal = 'IS NULL';
            }

            $wheres[] = "`$column` $equal";
        }

        $wheres = implode(' AND ', $wheres);

        $stmt = $this->pdo->prepare("DELETE FROM `$name` WHERE $wheres;");
        $this->execute($stmt);
    }
}
