<?php

namespace Core\Database;

use PDO;

class QueryBuilder
{
    public function __construct(protected PDO $pdo)
    {
    }
}
