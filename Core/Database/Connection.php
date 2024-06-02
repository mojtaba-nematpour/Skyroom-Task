<?php

namespace Core\Database;

use Core\Basic\Messages;
use Core\Http\Form;
use PDO;
use PDOException;
use PDOStatement;

class Connection
{
    private PDO $pdo;

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
            die($e->getMessage());
        }
    }

    public function createTable(string $model): static
    {
        $name = $model::Name;
        $schema = $model::Schema;

        $columns = [];
        foreach ($schema as $column => $structure) {
            if (!empty($column)) {
                $column = "`$column`";
            }

            $columns[] = "$column $structure";
        }

        $columns = implode(',', $columns);

        $this->pdo->query("CREATE TABLE IF NOT EXISTS `$name` ($columns);");

        return $this;
    }

    public function removeTable(string $model): static
    {
        $name = $model::Name;
        $this->pdo->query("DROP TABLE `$name`;");

        return $this;
    }

    public function save(string $model, array|Form $form): array|object
    {
        $name = $model::Name;
        $schema = $model::Schema;

        $columns = [];
        $binds = [];
        $values = $form;
        foreach ($schema as $column => $structure) {
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

        foreach ($values as $bind => $value) {
            if ($value instanceof \DateTimeImmutable) {
                $values[$bind] = $value->format("Y-m-d H:i:s");
            }

            $stmt->bindParam(":$bind", $values[$bind]);
        }

        $this->execute($stmt);

        if ($form instanceof Form) {
            $model = $form->denormalize($model);
            $model->setId($this->pdo->lastInsertId());
        } else {
            $model = array_merge($form, ['id' => $this->pdo->lastInsertId()]);
        }

        return $model;
    }

    public function find(string $model, array|Form $form): array|object
    {
        $name = $model::Name;
        $schema = $model::Schema;

        $modelData = $form;
        if ($form instanceof Form) {
            $modelData = $form->getData();
        }

        $wheres = [];
        foreach ($modelData as $column => $value) {
            $equal = "= '$value'";
            if ($value === null) {
                $equal = 'IS NULL';
            }

            $wheres[] = "`$column` $equal";
        }

        $columns = [];
        foreach ($schema as $column => $structure) {
            if (empty($column)) {
                continue;
            }

            $columns[] = "`$column`";
        }

        $columns = implode(',', $columns);
        $wheres = implode(' AND ', $wheres);

        $stmt = $this->pdo->prepare("SELECT $columns FROM `$name` WHERE $wheres;");
        $this->execute($stmt);

        if ($stmt->rowCount() < 0) {
            return [];
        }

        $models = [];
        $results = $stmt->fetchAll();
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

    public function remove(string $model, array $data): void
    {
        $name = $model::Name;

        $wheres = [];
        foreach ($data as $column => $value) {
            $equal = "= '$value'";
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
