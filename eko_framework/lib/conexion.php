<?php

class dbConexion
{
    public ?mysqli $link = null;
    public string $dbase;
    private static ?dbConexion $instance = null;
    private bool $transaction = false;

    public function switchDB(string $dbname): void
    {
        self::$instance?->link?->close();
        self::$instance = new dbConexion($dbname);
    }

    public static function singleton(string|false $basedatos = false): dbConexion
    {
		
        if (!isset(self::$instance)) {
			//throw new Exception("Aqui2");
            self::$instance = new dbConexion($basedatos);
			//throw new Exception("Aqui");
        } elseif (!self::$instance->transaction) {
            self::$instance->link?->close();
            self::$instance = new dbConexion($basedatos);
        }

        return self::$instance;
    }

    public function startTransaction(): void
    {
        $this->transaction = true;
        $this->link->autocommit(false);
        $this->link->begin_transaction();
    }

    public function commit(): void
    {
        $this->link->commit();
        $this->link->autocommit(true);
        $this->transaction = false;
    }

    public function rollback(): void
    {
        $this->link->rollback();
        $this->link->autocommit(true);
        $this->transaction = false;
    }

    public function __construct(string|false $basedatos = false)
    {
		
        $this->transaction = false;
        $this->dbase = $basedatos ?: DB_NAME;
	
         if (function_exists('mysqli_report')) {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    }
		//throw new Exception("Aqui3");
        try {
            $this->link = new mysqli(DB_HOST, DB_USER, DB_PASS, $this->dbase);
            $this->link->set_charset('utf8mb4');
        } catch (mysqli_sql_exception $e) {
            throw new Exception("Error de Conexión: " . $e->getMessage());
        }
    }

    public function __destruct()
    {
        // $this->link?->close();
    }

    public function useMaster(): void
    {
        if (!$this->link->select_db(DB_MASTER)) {
            throw new Exception($this->link->error);
        }
    }

    // Método auxiliar para ejecutar queries
    public function query(string $sql): mysqli_result|bool
    {
        return $this->link->query($sql);
    }
}
