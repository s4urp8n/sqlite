<?php

namespace Zver {

    class SQLite
    {

        protected $connection = null;

        public function connect($databasePath, $login = null, $password = null, $persistent = false)
        {

            /**
             * Create file if it not exists
             */
            clearstatcache(true);
            if (!file_exists($databasePath)) {
                Common::createDirectoryIfNotExists(dirname($databasePath));
                touch($databasePath);
            }

            /**
             * Try to connect
             */
            if (is_null($this->connection)) {

                $pdo = new \PDO('sqlite:' . $databasePath, $login, $password, [
                    \PDO::ATTR_PERSISTENT => $persistent,
                    \PDO::ATTR_ERRMODE    => \PDO::ERRMODE_EXCEPTION,
                ]);

                if (!is_resource($pdo)) {
                    throw new \Exception("Cant connect to db " . $databasePath);
                }
            }

            $this->connection = $pdo;

            return $this->connection;
        }

        public function disconnect()
        {
            if (!is_null($this->connection)) {
                $this->connection = null;
            }
        }

        public function execute($query)
        {

        }

    }
}