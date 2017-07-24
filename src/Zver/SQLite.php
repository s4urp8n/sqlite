<?php

namespace Zver {

    class SQLite
    {

        protected $connection = null;

        public static function connect($databasePath, $login = null, $password = null, $persistent = false)
        {

            /**
             * Create file if it not exists
             */
            clearstatcache(true);

            if (!file_exists($databasePath)) {
                Common::createDirectoryIfNotExists(dirname($databasePath));
                touch($databasePath);
            }

            return new static($databasePath, $login, $password, $persistent);
        }

        public function __construct($databasePath, $login, $password, $persistent)
        {
            $pdo = new \PDO('sqlite:' . $databasePath, $login, $password, [
                \PDO::ATTR_PERSISTENT => $persistent,
                \PDO::ATTR_ERRMODE    => \PDO::ERRMODE_EXCEPTION,
            ]);

            $this->connection = $pdo;
        }

        public function insertInTransaction($table, array $values, $ignore = false)
        {

            $this->execute("PRAGMA synchronous=OFF");
            $this->execute("PRAGMA count_changes=OFF");
            $this->execute("PRAGMA journal_mode=NORMAL");
            $this->execute("PRAGMA temp_store=MEMORY");

            $this->connection->beginTransaction();

            foreach ($values as $value) {
                $this->insert($table, $value, $ignore);
            }

            $this->connection->commit();

        }

        public function fetch($query)
        {
            return $this->connection->query($query)
                                    ->fetchAll(\PDO::FETCH_ASSOC);
        }

        public function insert($table, array $values, $ignore = false)
        {

            $insertFields = $insertValues = [];

            foreach ($values as $key => $value) {

                $insertFields[] = $this->escape($key);
                $insertValues[] = $this->escape($value);

            }

            $query = sprintf(
                'insert' . ($ignore ? ' or ignore ' : ' ') . 'into `' . $table . '` (%s) values (%s)',
                implode(',', $insertFields),
                implode(',', $insertValues)
            );

            return $this->execute($query);

        }

        public function __destruct()
        {
            $this->disconnect();
        }

        public function disconnect()
        {
            if (!is_null($this->connection)) {
                $this->connection = null;
            }
        }

        public function execute($query)
        {
            return $this->connection->exec($query);
        }

        public function escape($string)
        {
            return $this->connection->quote($string);
        }

    }
}