<?php

/**
 * @class Model
 * Clase base para los modelos
 * $debug: true para desarrollo (muestra detalle del error con query)
 *         false para producción (solo nombre del modelo y tipo de consulta)
 */
class Model
{
    public $id = 0;
    public $name = 'Model';
    private $debug = true;

    public $select = "*";
    public $primaryKey = 'id';
    public $useTable = '';
    public $camposAfiltrar = [];
    public $hasOne = [];
    public $orderBy = [];
    public $singleton = false;
    public $registroNuevo = false;

    public function __construct(?array $params = null)
    {
        if (defined('SQL_DEBUG')) {
            $this->debug = SQL_DEBUG !== '0';
        }
    }

    public function jsDateToMysql($jsDate)
    {
        [$dia, $mes, $year] = explode("/", $jsDate);
        [$year, $time] = array_pad(explode(" ", $year), 2, '');

        $convertida = "$year-$mes-$dia";

        if ($time !== '') {
            [$hora, $minuto, $segundo] = explode(':', $time);
            $convertida .= " $hora:$minuto:$segundo";
        }

        return $convertida;
    }

    public function startTransaction()
    {
        $conexion = dbConexion::singleton();
        $conexion->startTransaction();
    }

    public function EscComillas($texto)
    {
        $conexion = dbConexion::singleton();
        return $conexion->link->real_escape_string($texto);
    }

    public function execute(string $query, ?string $dbName = null)
    {
        $conexion = dbConexion::singleton($dbName);
        $link = $conexion->link;

        try {
            $res = $link->query($query);
            if ($res === false) {
                throw new mysqli_sql_exception($link->error, $link->errno);
            }
        } catch (mysqli_sql_exception $e) {
            $this->handleQueryError('query', $query, $e);
        }
    }

    public function query(string $query, ?string $dbName = null)
    {
        $conexion = dbConexion::singleton($dbName);
        $link = $conexion->link;

        try {
            $res = $link->query($query);
            if ($res === false) {
                throw new mysqli_sql_exception($link->error, $link->errno);
            }
        } catch (mysqli_sql_exception $e) {
            $this->handleQueryError('query', $query, $e);
        }

        $result = [];
        while ($row = $res->fetch_assoc()) {
            $result[] = $row;
        }
        $res->free();

        return $result;
    }

    public function select(string $query, ?string $dbName = null)
    {
        return $this->query($query, $dbName);
    }

    public function insert(string $query, ?string $dbName = null)
    {
        $conexion = dbConexion::singleton($dbName);
        $link = $conexion->link;

        try {
            $res = $link->query($query);
            if ($res === false) {
                throw new mysqli_sql_exception($link->error, $link->errno);
            }
        } catch (mysqli_sql_exception $e) {
            if ($e->getCode() === 1062) {
                generaLog('insert_' . $this->name, $e->getMessage() . ":" . $query);
                throw new Exception("El registro no puede duplicarse");
            }
            $this->handleQueryError('insert', $query, $e);
        }

        return $link->insert_id;
    }

    public function update(string $query, ?string $dbName = null)
    {
        $conexion = dbConexion::singleton($dbName);
        $link = $conexion->link;

        try {
            $res = $link->query($query);
            if ($res === false) {
                throw new mysqli_sql_exception($link->error, $link->errno);
            }
        } catch (mysqli_sql_exception $e) {
            if ($e->getCode() === 1062) {
                generaLog('update_' . $this->name, $e->getMessage() . ":" . $query);
                throw new Exception("El registro no puede duplicarse");
            }
            $this->handleQueryError('update', $query, $e);
        }

        return true;
    }

    public function delete($Id)
    {
        $conexion = dbConexion::singleton();
        $link = $conexion->link;

        $Id = $link->real_escape_string((string)$Id);
        $query = "DELETE FROM {$this->useTable} WHERE {$this->primaryKey} = '{$Id}'";

        try {
            $result = $link->query($query);
            if ($result === false) {
                throw new mysqli_sql_exception($link->error, $link->errno);
            }
        } catch (mysqli_sql_exception $e) {
            $this->handleQueryError('DELETE', $query, $e);
        }

        return $Id;
    }

    public function queryDelete(string $query, ?string $dbName = null)
    {
        $conexion = dbConexion::singleton($dbName);
        $link = $conexion->link;

        try {
            $res = $link->query($query);
            if ($res === false) {
                throw new mysqli_sql_exception($link->error, $link->errno);
            }
        } catch (mysqli_sql_exception $e) {
            $this->handleQueryError('queryDelete', $query, $e);
        }

        return true;
    }

    /**
     * Maneja errores de consulta de forma centralizada
     */
    private function handleQueryError(string $operation, string $query, mysqli_sql_exception $e)
    {
        generaLog($operation . '_' . $this->name, $e->getMessage() . ":" . $query);

        if ($this->debug) {
            throw new Exception("Debug: {$this->name}->{$e->getMessage()} $query");
        } else {
            throw new Exception("{$this->name}: Error al realizar la consulta, consulte con el administrador del sistema");
        }
    }

    /**
     * Filtra texto para búsqueda en campos definidos
     */
    public function filtroToSQL(
        string $filtro,
        array $filtros = [],
        bool $usarAlias = false,
        string $where = ''
    ) {
        $conexion = dbConexion::singleton();
        $link = $conexion->link;
        $tableAlias = $this->name;

        if (!empty($filtro)) {
            $filtroArray = explode(" ", $filtro);
            $condiciones = "";

            foreach ($this->camposAfiltrar as $campo) {
                $condicion = "";
                foreach ($filtroArray as $text) {
                    if (strlen($text) > 0) {
                        $text = $link->real_escape_string($text);
                        $fieldRef = $usarAlias ? "$tableAlias.$campo" : $campo;
                        $condicion .= "$fieldRef LIKE '%$text%' AND ";
                    }
                }

                if (strlen($condicion) > 0) {
                    $condicion = substr($condicion, 0, -4);
                    $condicion = "(" . $condicion . ") OR ";
                    $condiciones .= $condicion;
                }
            }

            if (strlen($condiciones) > 0) {
                $condiciones = substr($condiciones, 0, -3);
                $where = "WHERE ($condiciones)";
            }
        }

        // Filtros adicionales
        $condiciones = "";
        foreach ($filtros as $filtroItem) {
            if (count($filtroItem) === 1 && isset($filtroItem['filtro'])) {
                $condiciones .= $filtroItem['filtro'] . " AND ";
            } else {
                $campo = $filtroItem['campo'];
                $condicion = $filtroItem['condicion'];
                $valor = $link->real_escape_string($filtroItem['valor']);
                $condiciones .= "$campo $condicion '$valor' AND ";
            }
        }

        if (strlen($condiciones) > 0) {
            $condiciones = substr($condiciones, 0, -4);
            $where = empty($where)
                ? "WHERE $condiciones"
                : "$where AND $condiciones";
        }

        return $where;
    }

    /**
     * Búsqueda paginada
     */
    public function readAll(
        $start = 0,
        $limit = 0,
        $filtro = '',
        $params = [],
        $usarAlias = false
    ) {
        $filtros = $params['filtros'] ?? [];
        $filtroSql = $this->filtroToSQL($filtro, $filtros, $usarAlias);
        $tableAlias = $this->name;

        // Contar registros
        $query = "SELECT COUNT({$this->primaryKey}) as totalrows FROM {$this->useTable} AS $tableAlias $filtroSql";
        $resultado = $this->query($query);
        $totalRows = (int)$resultado[0]['totalrows'];

        // Construir SELECT
        $select = $this->buildSelectClause($params, $tableAlias);

        // Construir LEFT JOINs
        [$leftJoin, $selectExtra] = $this->buildJoinClause($params, $tableAlias);
        $select .= $selectExtra;

        $orderBy = $this->gerOrderBy();

        $query = "SELECT $select 
                  FROM {$this->useTable} AS $tableAlias
                  $leftJoin
                  $filtroSql
                  $orderBy
                  LIMIT $start, $limit";

        $resArr = $this->query($query);

        return [
            'success' => true,
            'data' => $resArr,
            'totalRows' => $totalRows
        ];
    }

    public function gerOrderBy()
    {
        if (!isset($this->orderBy) || empty($this->orderBy)) {
            return '';
        }

        $order = '';
        foreach ($this->orderBy as $orderEl) {
            foreach ($orderEl as $column => $orden) {
                $order .= "$column $orden,";
            }
        }

        if (strlen($order) > 0) {
            $order = substr($order, 0, -1);
            return 'ORDER BY ' . $order;
        }

        return '';
    }

    public function constructSelect($selectParams, $tableAlias)
    {
        $select = '';

        foreach ($selectParams as $regSelect) {
            if (is_array($regSelect) && count($regSelect) === 1) {
                $campo = key($regSelect);
                $alias = $regSelect[$campo];
                $select .= "$tableAlias.$campo AS $alias,";
            } elseif (is_array($regSelect) && count($regSelect) === 2) {
                $campo = $regSelect[1];
                $select .= "$campo,";
            } elseif (is_string($regSelect)) {
                $select .= "$tableAlias.$regSelect,";
            } else {
                throw new Exception("constructSelect error");
            }
        }

        return substr($select, 0, -1);
    }

    /**
     * Obtiene un registro por ID
     */
    public function getById($IDValue, $params = [])
    {
        $tableAlias = $this->name;
        $select = $this->buildSelectClause($params, $tableAlias);
        [$leftJoin, $selectExtra] = $this->buildJoinClause($params, $tableAlias);
        $select .= $selectExtra;

        $conexion = dbConexion::singleton();
        $IDValue = $conexion->link->real_escape_string((string)$IDValue);

        $query = "SELECT $select 
                  FROM {$this->useTable} AS $tableAlias
                  $leftJoin
                  WHERE $tableAlias.{$this->primaryKey} = '$IDValue'";

        $arrRes = $this->select($query);

        if (empty($arrRes)) {
            throw new Exception("El elemento buscado no existe en la base de datos");
        }

        return [$this->name => $arrRes[0]];
    }

    /**
     * Guarda un registro (INSERT o UPDATE)
     */
    public function save($params, $log = true)
    {
        if (empty($params)) {
            throw new Exception("No se recibieron los datos a guardar");
        }

        $conexion = dbConexion::singleton();
        $link = $conexion->link;

        $IDUsu = $_SESSION['Auth']['User']['IDUsu'] ?? 0;
        $registroNuevo = empty($params[$this->primaryKey]);

        if ($registroNuevo) {
            $query = "INSERT INTO {$this->useTable} SET ";
            if ($log) {
                $query .= "AddUsuario = $IDUsu, AddFecha = NOW(), ";
            }
        } else {
            $query = "UPDATE {$this->useTable} SET ";
            if ($log) {
                $query .= "ModUsuario = $IDUsu, ModFecha = NOW(), ";
            }
        }

        foreach ($params as $key => $value) {
            $key = $link->real_escape_string($key);
            if (is_null($value)) {
                $query .= "$key = NULL, ";
            } else {
                $value = $link->real_escape_string((string)$value);
                $query .= "$key = '$value', ";
            }
        }

        $query = rtrim($query, ', ');

        if (!$registroNuevo) {
            $pk = $link->real_escape_string((string)$params[$this->primaryKey]);
            $query .= " WHERE {$this->primaryKey} = '$pk'";
        }

        $this->insert($query);

        $id = $registroNuevo ? $link->insert_id : $params[$this->primaryKey];
        $this->id = (int)$id;
        $this->registroNuevo = $registroNuevo;

        return $this->getById($IDValue, $params = []);
    }

    /**
     * Construye la cláusula SELECT
     */
    private function buildSelectClause($params, $tableAlias)
    {
        $selectParams = $params['select'] ?? $this->select ?? '*';

        if (is_string($selectParams)) {
            return $selectParams;
        }

        if (is_array($selectParams)) {
            return $this->constructSelect($selectParams, $tableAlias);
        }

        return '*';
    }

    /**
     * Construye LEFT JOINs y campos adicionales
     */
    private function buildJoinClause($params, $tableAlias)
    {
        $hasOne = $params['hasOne'] ?? $this->hasOne ?? [];
        $leftJoin = '';
        $selectExtra = '';

        foreach ($hasOne as $relation) {
            if (isset($relation['tabla'], $relation['alias'], $relation['pk'], $relation['fk'])) {
                $tabla = $relation['tabla'];
                $alias = $relation['alias'];
                $fk = $relation['fk'];
                $pk = $relation['pk'];
                $leftJoin .= " LEFT JOIN $tabla AS $alias ON $alias.$pk = $tableAlias.$fk ";
            }

            if (isset($relation['select'])) {
                $selectExtra .= "," . $this->constructSelect($relation['select'], $relation['alias']);
            }
        }

        return [$leftJoin, $selectExtra];
    }
}
