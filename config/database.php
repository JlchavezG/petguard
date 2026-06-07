<?php
/**
 * ============================================================
 * PETGUARD - Configuración de Base de Datos
 * ============================================================
 * Conexión PDO segura con patrón Singleton
 * Proyecto de Ingeniería de Software · 9no Semestre 2026
 * ============================================================
 */

// Evitar acceso directo al archivo
if (!defined('PETGUARD_APP')) {
    http_response_code(403);
    exit('Acceso no permitido.');
}

class Database {
    
    // ============================================================
    // CONFIGURACIÓN DE CONEXIÓN
    // ============================================================
    private $host     = 'localhost';
    private $db_name  = 'petguard_db';
    private $username = 'root';
    private $password = '';  // Cambia esto en producción
    private $charset  = 'utf8mb4';
    
    // Instancia única (Singleton)
    private static $instance = null;
    private $pdo = null;
    
    // ============================================================
    // CONSTRUCTOR PRIVADO (Patrón Singleton)
    // ============================================================
    private function __construct() {
        try {
            // DSN (Data Source Name) con charset
            $dsn = "mysql:host={$this->host};dbname={$this->db_name};charset={$this->charset}";
            
            // Opciones PDO para seguridad y rendimiento
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,  // Lanzar excepciones en errores
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,         // Retornar arrays asociativos
                PDO::ATTR_EMULATE_PREPARES   => false,                    // Prepared statements reales
                PDO::ATTR_PERSISTENT         => false,                    // No usar conexiones persistentes
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$this->charset}"
            ];
            
            // Crear instancia PDO
            $this->pdo = new PDO($dsn, $this->username, $this->password, $options);
            
        } catch (PDOException $e) {
            // En desarrollo: mostrar error detallado
            // En producción: loguear error y mostrar mensaje genérico
            if (defined('APP_ENV') && APP_ENV === 'development') {
                die("Error de conexión: " . $e->getMessage());
            } else {
                error_log("Error de conexión a BD: " . $e->getMessage());
                die("Error de conexión a la base de datos. Inténtalo más tarde.");
            }
        }
    }
    
    // ============================================================
    // OBTENER INSTANCIA ÚNICA
    // ============================================================
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }
    
    // ============================================================
    // OBTENER CONEXIÓN PDO
    // ============================================================
    public function getConnection() {
        return $this->pdo;
    }
    
    // ============================================================
    // PREVENIR CLONACIÓN Y SERIALIZACIÓN
    // ============================================================
    private function __clone() {}
    public function __wakeup() {
        throw new Exception("No se puede deserializar un Singleton");
    }
}

/**
 * Función helper para obtener la conexión rápidamente
 * Uso: $db = getDB();
 */
function getDB() {
    return Database::getInstance()->getConnection();
}