<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Database configuration for MySQL (XAMPP compatible)
$db['default'] = array(
    'dsn'   => '',
    'hostname' => 'localhost',
    'username' => 'root',
    'password' => '',
    'database' => 'vehicleflow',
    'dbdriver' => 'mysqli',
    'dbprefix' => '',
    'pconnect' => FALSE,
    'db_debug' => TRUE,
    'cache_on' => FALSE,
    'cachedir' => '',
    'char_set' => 'utf8mb4',
    'dbcollat' => 'utf8mb4_general_ci',
    'swap_pre' => '',
    'encrypt' => FALSE,
    'compress' => FALSE,
    'stricton' => FALSE,
    'failover' => array(),
    'save_queries' => TRUE
);

// Simple database connection class
class Database {
    private $connection;
    
    public function __construct($config = null) {
        if ($config === null) {
            global $db;
            $config = $db['default'];
        }
        
        $this->connection = new mysqli(
            $config['hostname'],
            $config['username'],
            $config['password'],
            $config['database']
        );
        
        if ($this->connection->connect_error) {
            die('Connection failed: ' . $this->connection->connect_error);
        }
        
        $this->connection->set_charset($config['char_set']);
    }
    
    public function query($sql, $params = []) {
        if (!empty($params)) {
            $stmt = $this->connection->prepare($sql);
            if ($stmt) {
                $types = str_repeat('s', count($params));
                $stmt->bind_param($types, ...$params);
                $stmt->execute();
                $result = $stmt->get_result();
                $stmt->close();
                return $result;
            }
        }
        
        return $this->connection->query($sql);
    }
    
    public function fetch_array($result) {
        return $result->fetch_assoc();
    }
    
    public function fetch_all($result) {
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public function num_rows($result) {
        return $result->num_rows;
    }
    
    public function insert_id() {
        return $this->connection->insert_id;
    }
    
    public function affected_rows() {
        return $this->connection->affected_rows;
    }
    
    public function escape_string($string) {
        return $this->connection->real_escape_string($string);
    }
    
    public function close() {
        $this->connection->close();
    }
}

// Global database instance
$GLOBALS['db_instance'] = new Database();