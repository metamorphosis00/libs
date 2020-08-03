<?php

class DB
{
    const DB_HOST = '127.0.0.1';
    const DB_PORT = '3306';
    const DB_NAME = 'database';
    const DB_USER = 'root';
    const DB_PASS = '';

    private $pdo;

    public function __construct()
    {
        try {
            $dsn = sprintf("mysql:host=%s;dbname=%s;port=%s;charset=utf8",
                self::DB_HOST, self::DB_NAME, self::DB_PORT);
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ];
            $this->pdo = new PDO($dsn, self::DB_USER, self::DB_PASS, $options);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    public function query($sql, array $params = [])
    {
        $result = [];
        echo $sql.PHP_EOL;
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            if ($stmt) {
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        return $result;
    }

    public function first($sql, array $params)
    {
        $result = $this->query($sql, $params);
        if (!empty($result)) {
            return $result[0];
        }
        return $result;
    }

    public function all($table)
    {
        return $this->query('SELECT * FROM ' . $table);
    }

    public function insert($table, array $columns, array $values)
    {
        if (count($columns) != count($values)) {
            throw new Exception('Count passed column names not equals to values');
        }
        $sql = 'INSERT INTO ' . $table . '(' . implode(',', $columns) . ')';
        $sql .= ' VALUES (?' . str_repeat(',?', count($columns) - 1) . ')';
        return $this->query($sql, array_values($values));
    }

    public function update($table, array $columns, array $values, $id)
    {
        if (count($columns) != count($values)) {
            throw new Exception('Count passed column names not equals to values');
        }
        $sql = 'UPDATE '.$table.' SET ';
        $data = array_combine($columns, $values);
        $i = 0;

        foreach ($data as $column => $value) {
            if ($i > 0) {
                $sql .= ',';
            }
            $sql .= $column . '=?';
            $i++;
        }

        $sql .= ' WHERE id='.$id;

        return $this->query($sql, array_values($values));
    }

    public function count($table, array $columns, array $values)
    {
        if (count($columns) != count($values)) {
            throw new Exception('Count passed column names not equals to values');
        }
        $sql = 'SELECT * FROM ' . $table . ' WHERE ';

        $data = array_combine($columns, $values);
        $i = 0;

        foreach ($data as $column => $value) {
            if ($i > 0) {
                $sql .= ' AND ';
            }
            $sql .= $column . '=?';
            $i++;
        }

        return count($this->query($sql, array_values($values)));
    }
}
