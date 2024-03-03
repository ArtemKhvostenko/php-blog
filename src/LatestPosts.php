<?php
declare(strict_types=1);

namespace Blog;

use PDO;
use PDOException;

class LatestPosts
{
    /**
     * @param PDO $connection
     */
    public function __construct(private PDO $connection){}

    /**
     * @param int $limit
     * @return array|null
     */
    public function get(int $limit = 5): ?array
    {
        $statement  = $this->connection->prepare('SELECT * FROM post ORDER BY created_at DESC LIMIT ' . $limit);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_CLASS, Post::class);
    }
}