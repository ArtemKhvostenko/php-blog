<?php

declare(strict_types=1);

namespace Blog;

use PDO;

class PostMapper
{
    /**
     * @param PDO $connection
     */
    public function __construct(private PDO $connection){}

    /**
     * @param string $urlKey
     * @return Post|false
     */
    public function getByUrlKey(string $urlKey): Post|false
    {
        $statement = $this->connection->prepare('SELECT * FROM post WHERE url_key = :url_key');
        $statement->execute([
            'url_key' => $urlKey
        ]);

        try {
            $result = $statement->fetchObject(Post::class);
        } catch (\PDOException $exception) {
            die($exception->getMessage());
        }

        return $result;
    }
}