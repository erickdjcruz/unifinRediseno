<?php


/*
 * Your installation or use of this SugarCRM file is subject to the applicable
 * terms available at
 * http://support.sugarcrm.com/Resources/Master_Subscription_Agreements/.
 * If you do not agree to all of the applicable terms or do not have the
 * authority to bind the entity as an authorized representative, then do not
 * install or use this SugarCRM file.
 *
 * Copyright (C) SugarCRM Inc. All rights reserved.
 */


/**
 * This class handles the Data base functionality for the application using
 * IBM DB2.
 *
 * Note: we are only supporting LUW 9.7 and higher at this moment.
 */
class IBMDB2Manager extends DBManager
{
    /**+
     * @see DBManager::$dbType
     */
    public $dbType = 'ibm_db2';
    public $variant = 'ibm_db2';
    public $dbName = 'IBM_DB2';
    public $label = 'LBL_IBM_DB2';

    /**+
     * @var array
     */
    protected $maxNameLengths = [
        'table' => 128,
        'column' => 128,
        'index' => 128,
        'alias' => 128,
    ];

    /**
     * {@inheritDoc}
     *
     * Mapping recommendation derived from MySQL to DB2 guidelines
     * @link http://www.ibm.com/developerworks/data/library/techarticle/dm-0807patel/index.html
     */
    protected $type_map = [
        'clob' => 'clob(65535)',
        'blob' => 'blob(65535)',
        'bool' => 'smallint',
        'char' => 'char',
        'currency' => 'decimal(26,6)',
        'date' => 'date',
        'datetimecombo' => 'timestamp(0)',
        'datetime' => 'timestamp(0)',

        // Using Oracle numeric precision and scale as DB2 does not support decimal without it
        'decimal' => 'decimal(20,2)',
        'decimal2' => 'decimal(30,6)',

        'decimal_tpl' => 'decimal(%d, %d)',
        'double' => 'double',
        'encrypt' => 'varchar',
        'enum' => 'varchar',
        'file' => 'varchar',
        'float' => 'double',
        'html' => 'clob(65535)',
        'id' => 'char(36)',
        'int' => 'integer',
        'long' => 'bigint',
        'longblob' => 'blob(2000000000)',
        'longhtml' => 'clob(2000000000)',
        'longtext' => 'clob(2000000000)',
        'multienum' => 'clob(65535)',
        'relate' => 'varchar',
        'short' => 'smallint',
        'text' => 'clob(65535)',
        'time' => 'time',
        'tinyint' => 'smallint',
        'uint' => 'bigint',
        'ulong' => 'decimal(20,0)',
        'url' => 'varchar',
        'varchar' => 'varchar',
    ];

    /**
     * Integer fields' min and max values
     * @var array
     */
    protected $type_range = [
        'int' => ['min_value' => -2147483648, 'max_value' => 2147483647],
        'uint' => ['min_value' => -9223372036854775808, 'max_value' => 9223372036854775807],
        'ulong' => ['min_value' => -99999999999999999999, 'max_value' => 99999999999999999999],//decimal(20,0)
        'long' => ['min_value' => -9223372036854775808, 'max_value' => 9223372036854775807],
        'short' => ['min_value' => -32768, 'max_value' => 32767],
        'tinyint' => ['min_value' => -32767, 'max_value' => 32767],
    ];

    /**
     * Field's max size
     * @var array
     */
    protected $max_size = [
        'text' => 65535,
        'html' => 65535,
    ];

    /**+
     * @var array
     */
    protected $capabilities = [
        'affected_rows' => true,
        'auto_increment_sequence' => true, // Opted to use DB2 sequences instead of identity columns because of the restriction of only 1 identity per table
        'limit_subquery' => false, // DB2 doesn't support OPTIMIZE FOR n ROWS in sub query
        'recursive_query' => true,

        /* Do not consider DB2 order stability as we have experienced issues
         * that this is not something we can rely on. By disabling this flag
         * sugar will add an additional id column in the ORDER BY clause to
         * to ensure stability of the results during result paging. Although
         * this below behavior is the default, leaving this capability flag
         * in here as a reference as in previous versions we used to rely on
         * DB2 order stability without altering the ORDER BY clause.
         */
        'order_stability' => false,
    ];

    /**
     * Schema in which all the DB2 objects live.
     * Is only used for management operations for now and set to the DB2 user id.
     * Could potentially become a configuration option when creating the database.
     * @var string
     */
    public $schema = '';

    protected $ignoreErrors = false;
    /**
     * @var string
     */
    public $lastsql;

    /**~
     * Parses and runs queries
     *
     * @param string $sql SQL Statement to execute
     * @param bool $dieOnError True if we want to call die if the query returns errors
     * @param string $msg Message to log if error occurs
     * @param bool $suppress Flag to suppress all error output unless in debug logging mode.
     * @param bool $keepResult True if we want to push this result into the $lastResult array.
     * @return resource result set
     */
    public function query($sql, $dieOnError = false, $msg = '', $suppress = false, $keepResult = false)
    {
        if (is_array($sql)) {
            return $this->queryArray($sql, $dieOnError, $msg, $suppress);
        }
        parent::countQuery($sql);
        $this->logger->info('Query: ' . $sql);
        $this->checkConnection();
        $db = $this->getDatabase();
        $result = false;

        $stmt = $suppress ? @db2_prepare($db, $sql) : db2_prepare($db, $sql);

        if ($stmt) {
            $sp_msg = null;
            if ($this->bindPreparedSqlParams($sql, $suppress, $stmt, $sp_msg)) {
                $this->query_time = microtime(true);
                $rc = $suppress ? @db2_execute($stmt) : db2_execute($stmt);
                $this->query_time = microtime(true) - $this->query_time;
                $this->logger->info('Query Execution Time: ' . $this->query_time);

                if (!$rc) {
                    $this->logger->error("Query Failed: $sql");
                    $stmt = false; // Making sure we don't use the statement resource for error reporting
                } else {
                    $result = $stmt;
                    if (isset($sp_msg) && $sp_msg != '') {
                        $this->logger->info("Return message from stored procedure call '$sql': $sp_msg");
                    }

                    $this->dump_slow_queries($sql);
                }
            } else {
                $this->logger->error("Failed to bind parameter for query : $sql");
            }
        }

        if ($keepResult) {
            $this->lastResult = $result;
        }

        if ($this->checkError($msg . ' Query Failed: ' . $sql, $dieOnError)) {
            return false;
        }
        $matches = [];
        if (preg_match('/^\W*alter\W+table\W+(\w+)/mi', $sql, $matches)) {
            if ($this->tableExists($matches[1])) {
                $this->reorgTable($matches[1]);
            }
        }
        return $result;
    }


    /**
     * Inspects the SQL statement to deduce if binding parameters is necessary and if so
     * also binds the parameters. Currently only a stored procedure message is supported.
     * @param $sql
     * @param $suppress
     * @param $stmt
     * @param $sp_msg
     * @return bool         false if binding failed, true if binding succeeded or wasn't necessary
     */
    protected function bindPreparedSqlParams($sql, $suppress, $stmt, &$sp_msg)
    {

        if (preg_match('/^CALL.+,\s*\?/i', $sql)) {
            // 20110519 Frank Steegmans: Note at the time of this implementation we are not using stored procedures
            // anywhere except for creating full text indexes in add_drop_contraint. Furthermore
            // we are also not using parameterized prepared queries. If either one of these assumptions
            // changes this code needs to be revisited.
            try {
                $sp_msg = null;
                $this->commit(); // XXX TODO: DIRTY HACK to work around auto-commit off problem. I.e. TS index creation will hang if tables hasn't been committed yet.
                // HENCE THIS COMMIT IS ONLY INTENDED FOR THE CREATION OF TS INDEXES. This should be moved into its execution objects in phase 3
                $proceed = ($suppress) ? @db2_bind_param($stmt, 1, 'sp_msg', DB2_PARAM_OUT) :
                    db2_bind_param($stmt, 1, 'sp_msg', DB2_PARAM_OUT);
                return $proceed;
            } catch (Exception $e) {
                $this->logger->error(
                    "IBMDB2Manager.query caught exception when running db2_bind_param for: $sql -> "
                    . $e->getMessage()
                );
                throw $e;
            }
        }
        return true;
    }


    /**~
     * Checks for db2_stmt_error in the given resource
     *
     * @param resource $obj
     * @return bool Was there an error?
     */
    protected function checkDB2STMTerror($obj)
    {
        if (!$obj) {
            return true;
        }

        $err = db2_stmt_error($obj);
        if ($err != false
            && $err != '01003') { // NULL result in aggregate bug 47612
            $this->logger->alert('DB2 Statement error: ' . var_export($err, true));
            return true;
        }
        return false;
    }


    /**~
     * Disconnects from the database
     *
     * Also handles any cleanup needed
     */
    public function disconnect()
    {
        $this->logger->debug('Calling IBMDB2::disconnect()');
        if (!empty($this->database)) {
            $this->commit();    // Commit any pending changes as most of our code assumes auto commits
            $this->freeResult();
            db2_close($this->database);
            $this->database = null;
        }

        parent::disconnect();
    }

    /**+
     * @see DBManager::freeDbResult()
     */
    protected function freeDbResult($dbResult)
    {
        if (is_resource($dbResult)) {
            db2_free_result($dbResult);
        }
    }

    /**+
     * @see DBManager::limitQuery()
     * NOTE that DB2 supports this on my LUW Express-C version but there may be issues
     * prior to 9.7.2. Hence depending on the versions we are supporting we may need
     * to add code for backward compatibility.
     * If we need to support this on platforms that don't support the LIMIT functionality,
     * see here: http://www.channeldb2.com/profiles/blogs/porting-limit-and-offset
     */
    public function limitQuery($sql, $start, $count, $dieOnError = false, $msg = '', $execute = true)
    {
        $start = (int)$start;
        $count = (int)$count;

        $this->logger->debug('IBM DB2 Limit Query:' . $sql . ' Start: ' . $start . ' count: ' . $count);

        if ($start <= 0) {
            $start = ''; // Not specifying a 0 start helps the DB2 optimizer create a better plan
        } else {
            $start .= ',';
        }

        $sql = "SELECT * FROM ($sql) LIMIT $start $count OPTIMIZE FOR $count ROWS";
        $this->lastsql = $sql;

        if (!empty($GLOBALS['sugar_config']['check_query'])) {
            $this->checkQuery($sql);
        }
        if (!$execute) {
            return $sql;
        }

        return $this->query($sql, $dieOnError, $msg);
    }

    /**~
     * Get list of DB column definitions
     *
     * More info can be found here:
     * http://publib.boulder.ibm.com/infocenter/db2luw/v9/index.jsp?topic=/com.ibm.db2.udb.admin.doc/doc/r0001047.htm
     */
    public function get_columns($tablename)
    {
        // Sanity check for getting columns
        if (empty($tablename)) {
            $this->logger->error(__METHOD__ . ' called with an empty tablename argument');
            return [];
        }

        $query = 'SELECT COLNAME, TYPENAME, LENGTH, SCALE, DEFAULT, NULLS, GENERATED
FROM SYSCAT.COLUMNS
WHERE TABSCHEMA = ?
    AND TABNAME = ?';

        $stmt = $this->getConnection()
            ->executeQuery($query, [
                strtoupper($this->schema),
                strtoupper($tablename),
            ]);

        $columns = [];
        while (($row = $stmt->fetchAssociative())) {
            $name = strtolower($row['colname']);
            $columns[$name]['name'] = $name;
            $columns[$name]['type'] = strtolower($row['typename']);

            switch ($columns[$name]['type']) {
                case 'timestamp':
                case 'date':
                case 'xml':
                case 'blob':
                case 'clob':
                case 'dbclob':
                    break;
                case 'decimal':
                    $columns[$name]['len'] = $row['length'] . ',' . $row['scale'];
                    break;
                case 'character':
                    $columns[$name]['type'] = 'char';
                    // char needs length
                    // no break
                default:
                    $columns[$name]['len'] = $row['length'];
            }
            if (!empty($row['default'])) {
                $matches = [];
                if (preg_match("/^'(.*)'$/i", $row['default'], $matches) && $matches[1] != '') {
                    $columns[$name]['default'] = $matches[1];
                }
            }
            // TODO add logic to make this generated when there is a sequence being used
            if ($row['generated'] == 'A' || $row['generated'] == 'D') {
                $columns[$name]['auto_increment'] = '1';
            }
            if ($row['nulls'] == 'N') {
                $columns[$name]['required'] = 'true';
            }
            if (isset($columns[$name]['required']) && $columns[$name]['required'] == '') {
                unset($columns[$name]['required']);
            }
        }
        return $columns;
    }


    /**+
     * @see DBManager::getFieldsArray()
     */
    public function getFieldsArray($result, $make_lower_case = false)
    {
        if (!isset($result) || empty($result)) {
            return 0;
        }

        $field_array = [];
        $count = db2_num_fields($result);
        for ($i = 0; $i < $count; $i++) {
            $meta = db2_field_name($result, $i);
            if (!$meta) {
                return [];
            }
            $field_array[] = $make_lower_case ? strtolower($meta) : $meta;
        }
        return $field_array;
    }

    /**+
     * Get number of rows affected by last operation
     * @see DBManager::getAffectedRowCount()
     */
    public function getAffectedRowCount($result)
    {
        return db2_num_rows($result);
    }

    /**~
     * Fetches the next row from the result set
     *
     * @param resource $result result set
     * @return array
     */
    protected function db2FetchRow($result)
    {
        $row = db2_fetch_assoc($result);
        if (!$row) {
            return false;
        }
        if ($this->checkDB2STMTerror($result) == false) {
            // make the column keys as lower case
            $row = array_change_key_case($row, CASE_LOWER);
        } else {
            return false;
        }

        return $row;
    }

    /**~
     * @see DBManager::fetchRow()
     */
    public function fetchRow($result)
    {
        if (empty($result)) {
            return false;
        }

        return $this->db2FetchRow($result);
    }

    /**+
     * @param  $namepattern     LIKE Style pattern to match the table name
     * @return array|bool       returns false if no match found and an array with the matching list of names
     */
    private function getTablesArrayByName($namepattern)
    {
        if ($db = $this->getDatabase()) {
            $tables = [];
            $result = db2_tables($db, '', '%', strtoupper($namepattern), 'TABLE');
            if (!empty($result)) {
                while ($row = $this->fetchByAssoc($result)) {
                    if (preg_match('/^sys/i', $row['table_schem']) == 0) { // Since we don't know the default schema name
                        $tables[] = strtolower($row['table_name']);       // we filter out all the tables coming from system schemas
                    }
                }
                return $tables;
            }
        }

        return false; // no database available
    }

    /**+
     * @see DBManager::getTablesArray()
     */
    public function getTablesArray()
    {
        $this->logger->debug('Fetching table list');
        return $this->getTablesArrayByName('%');
    }

    /**~
     * @see DBManager::version()
     * NOTE DBMS_VER may not be adequate to uniquely identify the database system for DB2
     * I.e. as per the discussion with the IBM folks, there DB2 version for different operating
     * systems can be inherently different. Hence we may need to add an implementation indicator
     * to the version. E.g. DBMS_NAME
     */
    public function version()
    {
        if (!isset(static::$version)) {
            $dbInfo = db2_server_info($this->getDatabase());
            if (!empty($dbInfo->DBMS_VER)) {
                static::$version = $dbInfo->DBMS_VER;
            } else {
                return false;
            }
        }
        return static::$version;
    }

    /**
     * Check DB version
     * @see DBManager::canInstall()
     */
    public function canInstall()
    {
        $db_version = $this->version();
        if (!$db_version) {
            return ['ERR_DB_VERSION_FAILURE'];
        }
        if (version_compare($db_version, '9.7.4') < 0) {
            return ['ERR_DB_IBM_DB2_VERSION', $db_version];
        }
        return true;
    }

    /**
     * Create conversion function to convert 10M blob to clob
     */
    public function createConversionFunctions()
    {
        $functionQuery = "
        CREATE OR REPLACE PROCEDURE Blob2Clob (IN tableName VARCHAR (64), IN sourceColumn VARCHAR (64), IN tmpColumn VARCHAR (64))
            LANGUAGE SQL
            BEGIN
                DECLARE SQLSTATE CHAR(5);

                DECLARE sourceBlob BLOB (41943040);
                DECLARE destClob CLOB (41943040) DEFAULT '';

                DECLARE destOffset INTEGER DEFAULT 1;
                DECLARE sourceOffset INTEGER DEFAULT 1;
                DECLARE langContext INTEGER DEFAULT 0;
                DECLARE warningCode INTEGER DEFAULT 0;

                DECLARE selectQuery VARCHAR(512);
                DECLARE updateQuery VARCHAR(512);

                DECLARE selectStatement STATEMENT;
                DECLARE updateStatement STATEMENT;

                DECLARE updateCursor CURSOR FOR selectStatement;

                SET selectQuery = 'SELECT ' || sourceColumn || ', ' || tmpColumn || ' FROM ' || tableName ||' FOR UPDATE OF ' || tmpColumn;
                SET updateQuery = 'UPDATE ' || tableName || ' SET ' || tmpColumn || ' = ? WHERE CURRENT OF updateCursor';

                PREPARE selectStatement FROM selectQuery;

                OPEN updateCursor;

                FETCH FROM updateCursor INTO sourceBlob, destClob;

                WHILE (SQLSTATE = '00000') DO

                    SET destOffset = 1;
                    SET sourceOffset = 1;

                    IF LENGTH(sourceBlob) > 0 THEN
                        CALL DBMS_LOB.CONVERTTOCLOB(destClob,
                                                    sourceBlob,
                                                    dbms_lob.lobmaxsize,
                                                    destOffset,
                                                    sourceOffset,
                                                    dbms_lob.default_csid,
                                                    langContext,
                                                    warningCode);

                        PREPARE updateStatement FROM updateQuery;
                        EXECUTE updateStatement USING destClob;
                    END IF;

                    FETCH FROM updateCursor INTO sourceBlob, destClob;

                END WHILE;

                CLOSE updateCursor;

            END";
        $this->query($functionQuery);
    }

    public function preInstall()
    {
        $this->createConversionFunctions();
    }

    /**+
     * @see DBManager::tableExists()
     */
    public function tableExists($tableName)
    {
        $this->logger->debug("tableExists: $tableName");
        return (bool)$this->getTablesArrayByName($tableName);
    }

    /**
     * @param string $table
     * @param string $schema
     * @param string $type
     * @return bool
     */
    protected function tableExistsExtended($table, $schema = '%', $type = 'TABLE')
    {
        $table = db2_tables($this->database, null, $schema, $table, $type);
        $table = $this->fetchByAssoc($table);
        return !empty($table);
    }

    /**+
     * Get tables like expression
     * @param $like string
     * @return array
     */
    public function tablesLike($like)
    {
        $this->logger->debug("tablesLike: $like");
        return $this->getTablesArrayByName($like);
    }

    /**+
     * @see DBManager::quote()
     */
    public function quote($string)
    {
        if (is_array($string)) {
            return $this->arrayQuote($string);
        }
        return str_replace("'", "''", $this->quoteInternal($string));
    }

    /**~
     * @see DBManager::connect()
     */
    public function connect(array $configOptions = null, $dieOnError = false)
    {
        global $sugar_config;

        if (is_null($configOptions)) {
            $configOptions = $sugar_config['dbconfig'];
        }


        if ($this->getOption('persistent')) {
            $persistConnection = true;
        } else {
            $persistConnection = false;
        }

        // Creating the connection string dynamically so that we can accommodate all scenarios
        // Starting with user and password as we always need these.
        $dsn = 'UID=' . $configOptions['db_user_name'] . ';PWD=' . $configOptions['db_password'] . ';';
        $this->schema = strtoupper($configOptions['db_user_name']); // Converting to upper since DB2 expects it that way

        if (isset($configOptions['db_name']) && $configOptions['db_name'] != '') {
            $dsn = $dsn . 'DATABASE=' . $configOptions['db_name'] . ';';
        }

        if (!isset($configOptions['db_host_name']) || $configOptions['db_host_name'] == '') {
            $configOptions['db_host_name'] = 'localhost';   // Connect to localhost by default
        }
        $dsn = $dsn . 'HOSTNAME=' . $configOptions['db_host_name'] . ';';

        if (!isset($configOptions['db_protocol']) || $configOptions['db_protocol'] == '') {
            $configOptions['db_protocol'] = 'TCPIP';   // Use TCPIP as default protocol
        }
        $dsn = $dsn . 'PROTOCOL=' . $configOptions['db_protocol'] . ';';

        if (!isset($configOptions['db_port']) || $configOptions['db_port'] == '') {
            $configOptions['db_port'] = '50000';   // Use 50000 as the default port
        }
        $dsn = $dsn . 'PORT=' . $configOptions['db_port'] . ';';

        if (!isset($configOptions['db_options'])) {
            $configOptions['db_options'] = [];
        }

        if ($persistConnection) {
            $this->database = db2_pconnect($dsn, '', '', $configOptions['db_options']);
            if (!$this->database) {
                $this->logger->alert(self::class . ': Persistent connection specified, but failed. Error: '
                    . db2_conn_error() . ': ' . db2_conn_errormsg());
            }
        }

        if (!$this->database) {
            $this->database = db2_connect($dsn, '', '', $configOptions['db_options']);
            if ($this->database && $persistConnection) {
                $_SESSION['administrator_error'] = '<b>Severe Performance Degradation: Persistent Database Connections '
                    . "not working.  Please set \$sugar_config['dbconfigoption']['persistent'] to false "
                    . 'in your config.php file</b>';
            }
            if (!$this->database) {
                $this->logger->alert(
                    self::class . ': Could not connect to Database with non-persistent connection. Error '
                    . db2_conn_error() . ': ' . db2_conn_errormsg()
                );
            }
        }

        // Skipping check for statement errors as there is a bug in the DB2 driver
        // http://pecl.php.net/bugs/bug.php?id=22854
        // TODO take this skip out when the DB2 bug is fixed
        $this->ignoreErrors = true;
        if (!$this->checkError('Could Not Connect:', $dieOnError) && $this->database != false) {
            $this->logger->info('connected to db');

            if (db2_autocommit($this->database, DB2_AUTOCOMMIT_OFF)) {
                $this->logger->info('turned autocommit off');
            } else {
                $this->logger->error('failed to turn autocommit off!');
            }
            static::$version = null;
        }
        $this->ignoreErrors = false;
        $this->logger->info('Connect:' . $this->database);

        return !empty($this->database);
    }

    protected $date_formats = [
        '%Y-%m-%d' => 'YYYY-MM-DD',
        '%Y-%m' => 'YYYY-MM',
        '%Y' => 'YYYY',
        '%x' => 'IYYY',
        '%v' => 'IW',
    ];


    /**~
     * @see DBManager::convert()
     *
     * TODO revisit this for other versions of DB2
     *
     * The following is provided for your convenience should you wish to learn more about
     * Converting a string to a date in DB2.
     * For a list of the actual third party software used in this Sugar product,
     * please visit http://support.sugarcrm.com/06_Customer_Center/11_Third_Party_Software/.
     *
     * http://stackoverflow.com/questions/4852139/converting-a-string-to-a-date-in-db2
     */
    public function convert($string, $type, array $additional_parameters = [])
    {
        if (!empty($additional_parameters)) {
            $additional_parameters_string = ',' . implode(',', $additional_parameters);
        } else {
            $additional_parameters_string = '';
        }
        $all_parameters = $additional_parameters;
        if (is_array($string)) {
            $all_parameters = array_merge($string, $all_parameters);
        } elseif (!is_null($string)) {
            array_unshift($all_parameters, $string);
        }

        switch (strtolower($type ?? '')) {
            case 'date':
                return "to_date($string, 'YYYY-MM-DD')";
            case 'time':
                return "to_date($string, 'HH24:MI:SS')";
            case 'timestamp':
            case 'datetime':
                return "to_date($string, 'YYYY-MM-DD HH24:MI:SS'$additional_parameters_string)";
            case 'today':
                return 'CURRENT_DATE';
            case 'left':
                return "LTRIM($string$additional_parameters_string)";
            case 'date_format':
                if (!empty($additional_parameters[0]) && $additional_parameters[0][0] == "'") {
                    $additional_parameters[0] = trim($additional_parameters[0], "'");
                }
                if (!empty($additional_parameters) && isset($this->date_formats[$additional_parameters[0]])) {
                    $format = $this->date_formats[$additional_parameters[0]];
                    return "TO_CHAR($string, '$format')";
                } else {
                    return "TO_CHAR($string, 'YYYY-MM-DD')";
                }
                // date format need time format too
                // no break
            case 'time_format':
                if (empty($additional_parameters_string)) {
                    $additional_parameters_string = ",'HH24:MI:SS'";
                }
                return "TO_CHAR($string" . $additional_parameters_string . ')';
            case 'ifnull':
                if (empty($additional_parameters_string)) {
                    $additional_parameters_string = ",''";
                }
                return "NVL($string$additional_parameters_string)";
            case 'concat':
                return implode('||', $all_parameters);
            case 'text2char':
                return "cast($string as VARCHAR(32000))";
            case 'quarter':
                return "TO_CHAR($string, 'Q')";
            case 'length':
                return "LENGTH($string)";
            case 'month':
                return "TO_CHAR($string, 'MM')";
            case 'add_date':
                switch (strtolower($additional_parameters[1])) {
                    case 'quarter':
                        $additional_parameters[0] .= '*3';
                        // break missing intentionally
                        // no break
                    case 'month':
                        return "ADD_MONTHS($string, {$additional_parameters[0]})";
                    case 'week':
                        $additional_parameters[0] .= '*7';
                        // break missing intentionally
                        // no break
                    case 'day':
                        return "($string + $additional_parameters[0] DAYS)";
                    case 'year':
                        return "ADD_MONTHS($string, {$additional_parameters[0]}*12)";
                }
                break;
            case 'add_time':
                return "$string + {$additional_parameters[0]}/24 + {$additional_parameters[1]}/1440";
            case 'add_tz_offset':
                $getUserUTCOffset = $GLOBALS['timedate']->getUserUTCOffset();
                $operation = $getUserUTCOffset < 0 ? '-' : '+';
                return $string . ' ' . $operation . ' ' . abs($getUserUTCOffset) . ' minutes';
            case 'avg':
                return "avg($string)";
            case 'substr':
                return "substr($string, " . implode(', ', $additional_parameters) . ')';
            case 'round':
                return "round($string, " . implode(', ', $additional_parameters) . ')';
        }

        return $string;
    }

    /**
     * {@inheritDoc}
     */
    public function fromConvert($string, $type)
    {
        if (is_null($string)) {
            return '';
        }
        switch ($type) {
            case 'id':
            case 'char':
                return rtrim($string, ' ');

            case 'time':
                if (strlen($string) >= 19) {
                    return substr($string, 11, 8);
                }

                if (strlen($string) > 8) {
                    return substr($string, 0, 8);
                }
        }

        return $string;
    }

    /**+
     * @see DBManager::createTableSQLParams()
     */
    public function createTableSQLParams($tablename, $fieldDefs, $indices)
    {
        $columns = $this->columnSQLRep($fieldDefs, false, $tablename);
        if (empty($columns)) {
            return false;
        }

        $sql = "CREATE TABLE $tablename ($columns)";
        $this->logger->info('IBMDB2Manager.createTableSQLParams: ' . $sql);
        return $sql;
    }


    /**~
     * @see DBManager::oneColumnSQLRep()
     */
    protected function oneColumnSQLRep($fieldDef, $ignoreRequired = false, $table = '', $return_as_array = false, $action = null)
    {
        if (isset($fieldDef['name'])) {
            if (stristr($this->getFieldType($fieldDef), 'decimal') && isset($fieldDef['len'])) {
                // PHP8 compares non-numeric strings with numbers as strings, so cast to int explicitly
                if ((int)$fieldDef['len'] > 31) {
                    $fieldDef['len'] = 31; // DB2 max precision is 31 for LUW, may be different for other OSs
                }
            }
        }
        //May need to add primary key and sequence stuff here
        $ref = parent::oneColumnSQLRep($fieldDef, $ignoreRequired, $table, true, $action);

        $matches = [];
        if (!empty($fieldDef['len']) &&
            preg_match('/^decimal(\((?P<len>\d*),*(?P<prec>\d*)\))?$/i', $ref['colType'], $matches)) {
            $numspec = [$fieldDef['len']]; // We are ignoring the length if it existed since we have one that comes from the vardefs
            if (!empty($fieldDef['precision']) && !strpos($fieldDef['len'], ',')) {
                $numspec [] = $fieldDef['precision']; // Use the vardef precision if it exists and wasn't specified in the length
            }
            $ref['colType'] = 'decimal(' . implode(',', $numspec) . ')';
        }

        if (!empty($ref['default'])
            && in_array($ref['colBaseType'], ['integer', 'smallint', 'bigint', 'double', 'decimal'])) {
            $ref['default'] = str_replace(["'", '"'], '', $ref['default']); // Stripping quotes
        }

        if ($return_as_array) {
            return $ref;
        } else {
            if ($ref['required'] == 'NULL') {
                // DB2 doesn't have NULL definition, only NOT NULL
                $ref['required'] = ''; // ONLY important when statement is rendered
            }
            return "{$ref['name']} {$ref['colType']} {$ref['default']} {$ref['required']} {$ref['auto_increment']}";
        }
    }

    protected function alterTableSQL($tablename, $columnspecs)
    {
        return "ALTER TABLE $tablename $columnspecs";
    }

    protected function alterTableColumnSQL($action, $columnspec)
    {
        return "$action COLUMN $columnspec";
    }

    /**
     * Generate sets of SQL Queries to convert Blob field to Clob field
     * @param string $tablename Name of the table
     * @param array $oldColumn Old column definition
     * @param array $newColumn New column definition
     * @param bool $ignoreRequired
     * @return array
     */
    protected function alterBlobToClob($tablename, $oldColumn, $newColumn, $ignoreRequired)
    {
        $newColumn['name'] = 'tmp_' . random_int(0, mt_getrandmax());
        $sql = [];
        $sql[] = $this->alterTableSQL(
            $tablename,
            $this->changeOneColumnSQL($tablename, $newColumn, 'ADD', $ignoreRequired)
        );
        $sql[] = "CALL Blob2Clob('" . $tablename . "','" . $oldColumn['name'] . "','" . $newColumn['name'] . "')";
        $sql[] = $this->alterTableSQL(
            $tablename,
            $this->changeOneColumnSQL($tablename, $oldColumn, 'DROP', $ignoreRequired)
        );
        $sql[] = $this->renameColumnSQL($tablename, $newColumn['name'], $oldColumn['name']);
        return $sql;
    }

    /**
     * Generates a sequence of SQL statements to accomplish the required column alterations
     *
     * @param string $tableName
     * @param array $def
     * @param bool $ignoreRequired
     * @return array
     */
    protected function alterOneColumnSQL($tableName, $def, $ignoreRequired = false): array
    {
        if (!is_array($def) || !is_string($tableName)) {
            return [];
        }

        $sql = [];
        $tableName = $this->getValidDBName($tableName);
        $fieldName = $this->getValidDBName($def['name']);
        $cols = $this->get_columns($tableName);

        if ($this->shouldConvertBlobToClob($cols, $def)) {
            return $this->alterBlobToClob($tableName, $cols[$def['name']], $def, $ignoreRequired);
        }

        // Early return if changing types between VARCHAR and CLOB
        if ($this->shouldChangeTypeBetweenVarcharAndClob($cols, $def)) {
            return $this->prepareVarcharClobAlterationSQL($tableName, $fieldName, $def);
        }

        // Prepare for column alteration based on the definition
        $req = $this->oneColumnSQLRep($def, $ignoreRequired, $tableName, true);
        $alter = $this->alterTableSQL($tableName, $this->alterTableColumnSQL('ALTER', $req['name']));

        // Conditional alterations based on column properties
        $sql = array_merge($sql, $this->prepareConditionalAlterations($req, $cols, $alter));

        return $sql;
    }

    private function shouldChangeTypeBetweenVarcharAndClob(array $cols, array $def): bool
    {
        $textTypes = $this->getTextTypes();
        $oldType = $cols[$def['name']]['type'] ?? null;
        $oldDbType = $cols[$def['name']]['dbType'] ?? null;
        $isOldColumnText = in_array($oldDbType, $textTypes) || in_array($oldType, $textTypes);

        $newDbType = $def['dbType'] ?? null;
        $newType = $def['type'] ?? null;
        $isNewColumnText = in_array($newDbType, $textTypes) || in_array($newType, $textTypes);

        // if text type changes to non-text and vice versa
        return $isOldColumnText !== $isNewColumnText;
    }
    private function shouldConvertBlobToClob(array $cols, array $def): bool
    {
        $fromType = $cols[$def['name']]['dbType'] ?? $cols[$def['name']]['type'] ?? '';
        $fromTypeInternal = substr($this->type_map[$fromType] ?? '', 0, 4);

        $toType = $def['dbType'] ?? $def['type'] ?? '';
        $toTypeInternal = substr($this->type_map[$toType] ?? '', 0, 4);

        return $fromTypeInternal === 'blob' && $toTypeInternal === 'clob';
    }

    private function getTextTypes(): array
    {
        $textTypes = [];
        foreach ($this->type_map as $type => $dbType) {
            // didn't use str_starts_with to be safe as we still support PHP 7.4
            if (substr($dbType, 0, 4) === 'clob' || substr($dbType, 0, 4) === 'blob') {
                $textTypes[] = $type;
            }
        }

        return $textTypes;
    }

    private function prepareVarcharClobAlterationSQL($tableName, $fieldName, $def): array
    {
        $len = is_numeric($def['len'] ?? null) ? ' (' . (int)$def['len'] . ')' : '';
        $type = $def['dbType'] ?? $def['type'];
        if (!isset($this->type_map[$type])) {
            $this->logger->error("Cannot prepare SQL for field {$fieldName} with invalid type: " . $type);
            return [];
        }
        $dbType = $this->type_map[$type];

        return [
            "ALTER TABLE $tableName ADD COLUMN {$fieldName}_tmp {$dbType}$len",
            "UPDATE $tableName SET {$fieldName}_tmp = {$fieldName}",
            "ALTER TABLE $tableName DROP COLUMN {$fieldName}",
            "ALTER TABLE $tableName RENAME COLUMN {$fieldName}_tmp TO {$fieldName}",
        ];
    }

    private function prepareConditionalAlterations($req, $cols, $alter)
    {
        $sql = [];

        // Adjust nullability
        if ($req['required'] === 'NULL') {
            $sql[] = "$alter DROP NOT NULL";
        } elseif ($req['required'] === 'NOT NULL') {
            $sql[] = "$alter SET NOT NULL";
        }

        // Adjust data type
        $sql[] = "$alter SET DATA TYPE {$req['colType']}";

        // Adjust default value
        if (strlen($req['default']) > 0) {
            $sql[] = "$alter SET {$req['default']}";
        } elseif (isset($cols[$req['name']]['default']) && $cols[$req['name']]['default'] !== '') {
            $sql[] = "$alter DROP DEFAULT";
        }

        return $sql;
    }

    /**+
     *
     * Generates the column specific SQL statement to accomplish the change action.
     * This can be used as part of an ALTER TABLE statement for the ADD and DROP or
     * is a standalone sequence of SQL statement for the MODIFY action.
     *
     * @param string $tablename
     * @param array $def Column definition
     * @param string $action Change Action
     * @param bool $ignoreRequired
     * @return  string                       Returns the SQL required to change this one column
     */
    protected function changeOneColumnSQL($tablename, $def, $action, $ignoreRequired = false)
    {
        switch ($action) {
            case 'ADD':
                $ref = $this->oneColumnSQLRep($def, $ignoreRequired, $tablename, true);
                if ($ref['required'] == 'NULL' // DB2 doesn't have NULL definition, only NOT NULL
                    || ($ref['required'] == 'NOT NULL' && $ref['default'] == '')) { // Make it nullable if no default value provided
                    $ref['required'] = '';
                }
                $sql = $this->alterTableColumnSQL($action, "{$ref['name']} {$ref['colType']} {$ref['default']} {$ref['required']} {$ref['auto_increment']}");
                break;
            case 'DROP':
                $sql = $this->alterTableColumnSQL($action, $def['name']);
                $this->reorgQueueAddTable($tablename); // Column DROP operations require TABLE REORGS
                break;
            case 'MODIFY':
                $sql = $this->alterOneColumnSQL($tablename, $def, $ignoreRequired);
                $this->reorgQueueAddTable($tablename); // Some modification (DROP IS NULL, etc.) require TABLE REORGS, so just to be sure adding table to queue for reorg
                break;
            default:
                $sql = null;
                $this->logger->alert("IBMDB2Manager.changeOneColumnSQL unknown change action '$action'"
                    . " for table '$tablename'");
                break;
        }
        return $sql;
    }

    /**
     * {@inheritDoc}
     */
    protected function changeColumnSQL($tablename, $fieldDefs, $action, $ignoreRequired = false)
    {
        $action = strtoupper($action);
        $columns = [];
        if ($this->isFieldArray($fieldDefs)) {
            foreach ($fieldDefs as $def) {
                $columns[] = $this->changeOneColumnSQL($tablename, $def, $action, $ignoreRequired);
            }
        } else {
            $columns[] = $this->changeOneColumnSQL($tablename, $fieldDefs, $action, $ignoreRequired);
        }

        if ($action == 'MODIFY') {
            $sql = call_user_func_array('array_merge', $columns); // Modify returns an array of SQL statements
        } else {
            $sql = $this->alterTableSQL($tablename, implode(' ', $columns));
        }

        return $sql;
    }


    /**+
     * Returns the next value for an auto increment
     *
     * @param string $table tablename
     * @param string $field_name
     * @return string
     */
    public function getAutoIncrement($table, $field_name)
    {
        $currval = $this->getOne("SELECT max($field_name) currval FROM $table");
        if (!empty($currval)) {
            return $currval + 1;
        }
        return '';
    }

    /**+
     * Returns the sql for the next value in a sequence
     *
     * @param string $table tablename
     * @param string $field_name
     * @return string
     */
    public function getAutoIncrementSQL($table, $field_name)
    {
        $seqName = $this->_getSequenceName($table, $field_name, true);
        return "NEXTVAL FOR $seqName";
    }


    /**~
     * Generate an DB2 SEQUENCE name similar to Oracle.
     *
     * @param string $table
     * @param string $field_name
     * @param boolean $upper_case
     * @return string
     */
    // @codingStandardsIgnoreLine PSR2.Methods.MethodDeclaration.Underscore
    protected function _getSequenceName($table, $field_name, $upper_case = true)
    {
        $sequence_name = $this->getValidDBName($table . '_' . $field_name . '_seq', true, 'index');
        if ($upper_case) {
            $sequence_name = strtoupper($sequence_name);
        }
        return $sequence_name;
    }

    /**+
     * @see DBManager::setAutoIncrement()
     */
    protected function setAutoIncrement($table, $field_name, array $platformOptions = [], $action = null)
    {
        $this->deleteAutoIncrement($table, $field_name);
        if (!empty($platformOptions['cache'])) {
            $seqCache = sprintf('CACHE %d', $platformOptions['cache']);
        } else {
            $seqCache = 'NO CACHE';
        }
        $seqName = $this->_getSequenceName($table, $field_name, true);
        $this->query("CREATE SEQUENCE $seqName START WITH 0 INCREMENT BY 1 NO MAXVALUE NO CYCLE $seqCache");
        $this->query("SELECT NEXTVAL FOR $seqName  FROM SYSIBM.SYSDUMMY1"); // Making sure we initialize the sequence so that getAutoIncrement behaves as expected
        return '';
    }

    /**+
     * Sets the next auto-increment value of a column to a specific value.
     *
     * @param string $table tablename
     * @param string $field_name
     */
    public function setAutoIncrementStart($table, $field_name, $start_value)
    {
        $sequence_name = $this->_getSequenceName($table, $field_name, true);
        if ($this->_findSequence($sequence_name)) {
            $this->query("ALTER SEQUENCE $sequence_name RESTART WITH $start_value");
            return true;
        } else {
            return false;
        }
    }

    /**+
     * @see DBManager::deleteAutoIncrement()
     */
    public function deleteAutoIncrement($table, $field_name)
    {
        $sequence_name = $this->_getSequenceName($table, $field_name, true);
        if ($this->_findSequence($sequence_name)) {
            $this->query('DROP SEQUENCE ' . $sequence_name);
        }
    }

    /**+
     * Returns true if the sequence name given is found
     *
     * @param string $name
     * @return bool   true if the sequence is found, false otherwise
     * TODO: check if some caching here makes sense, keeping in mind bug 43148
     */
    // @codingStandardsIgnoreLine PSR2.Methods.MethodDeclaration.Underscore
    protected function _findSequence($name)
    {
        $uname = strtoupper($name);
        $row = $this->fetchOne("SELECT SEQNAME FROM SYSCAT.SEQUENCES WHERE SEQNAME = '$uname'");
        return !empty($row);
    }

    /**
     * {@inheritDoc}
     *
     * NOTE normally the db2_statistics should produce the indices in an implementation independent manner.
     * However it wasn't producing any results for the LUW Express-C edition running on Vista.
     * Furthermore using a permanent connections resulted in unexplainable PHP errors.
     * Falling back to system views to retrieve this data:
     * http://publib.boulder.ibm.com/infocenter/db2luw/v9/topic/com.ibm.db2.udb.admin.doc/doc/r0001047.htm
     */
    protected function get_index_data($table_name = null, $index_name = null)
    {
        $data = [];
        $this->populate_index_data($table_name, $index_name, $data);

        return $data;
    }

    /**
     * Populates array with index data
     *
     * @param string $table_name Table name
     * @param string $index_name Index name
     * @param array $data Array to be populated
     */
    protected function populate_index_data($table_name, $index_name, &$data)
    {
        $filterByTable = $table_name !== null;
        $filterByIndex = $index_name !== null;

        $columns = [];
        if (!$filterByTable) {
            $columns[] = 'i.TABNAME AS table_name';
        }

        if (!$filterByIndex) {
            $columns[] = 'i.INDNAME AS index_name';
        }

        $columns[] = 'i.UNIQUERULE';
        $columns[] = 'c.COLNAME AS column_name';

        $query = 'SELECT ' . implode(', ', $columns) . '
FROM SYSCAT."INDEXES" i
INNER JOIN SYSCAT."INDEXCOLUSE" c
    ON i.INDNAME = c.INDNAME';

        $where = ['TABSCHEMA = ?'];
        $params = [$this->schema];

        if ($filterByTable) {
            $where[] = 'i.TABNAME = ?';
            $params[] = strtoupper($table_name);
        }

        if ($filterByIndex) {
            $where[] = 'i.INDNAME = ?';
            $params[] = strtoupper($this->getValidDBName($index_name, true, 'index'));
        }

        $query .= ' WHERE ' . implode(' AND ', $where);

        $order = [];
        if (!$filterByTable) {
            $order[] = 'i.TABNAME';
        }

        if (!$filterByIndex) {
            $order[] = 'i.INDNAME';
        }

        $order[] = 'c.COLSEQ';
        $query .= ' ORDER BY ' . implode(', ', $order);

        $stmt = $this
            ->getConnection()
            ->executeQuery($query, $params);

        while (($row = $stmt->fetchAssociative())) {
            if (!$filterByTable) {
                $table_name = strtolower($row['table_name']);
            }

            if (!$filterByIndex) {
                $index_name = strtolower($row['index_name']);
            }

            if ($row['uniquerule'] == 'P') {
                $type = 'primary';
            } elseif ($row['uniquerule'] == 'U') {
                $type = 'unique';
            } else {
                $type = 'index';
            }

            $data[$table_name][$index_name]['name'] = $index_name;
            $data[$table_name][$index_name]['type'] = $type;
            $data[$table_name][$index_name]['fields'][] = strtolower($row['column_name']);
        }
    }

    /**~
     * @see DBManager::add_drop_constraint()
     * @inheritDoc
     * Note: Tested all constructs pending feedback from IBM on text search index creation from code
     */
    public function add_drop_constraint(string $table, array $definition, bool $drop = false): string
    {
        $type = $definition['type'];
        $fieldsListSQL = implode(',', $definition['fields']);
        $name = $definition['name'];
        $sql = '';

        switch ($type) {
            // generic indices
            case 'index':
            case 'alternate_key':
            case 'clustered':
                if ($drop) {
                    $sql = "DROP INDEX {$name}";
                } else {
                    $sql = "CREATE INDEX {$name} ON {$table} ({$fieldsListSQL})";
                }
                break;
                // constraints as indices
            case 'unique':
                if ($drop) {
                    $sql = "DROP INDEX {$name}";
                } else {
                    $sql = "CREATE UNIQUE INDEX {$name} ON {$table} ({$fieldsListSQL}) EXCLUDE NULL KEYS";
                }
                break;
            case 'primary':
                if ($drop) {
                    $sql = "ALTER TABLE {$table} DROP PRIMARY KEY";
                } else {
                    $sql = "ALTER TABLE {$table} ADD CONSTRAINT {$name} PRIMARY KEY ({$fieldsListSQL})";
                }
                break;
            case 'foreign':
                if ($drop) {
                    $sql = "ALTER TABLE {$table} DROP FOREIGN KEY ({$fieldsListSQL})";
                } else {
                    $sql = "ALTER TABLE {$table} ADD CONSTRAINT {$name} FOREIGN KEY ({$fieldsListSQL}) REFERENCES {$definition['foreignTable']}({$definition['foreignField']})";
                }
                break;
        }

        $this->logger->info('IBMDB2Manager.add_drop_constraint: ' . $sql);
        return $sql;
    }

    /**
     * {@inheritDoc}
     */
    public function massageFieldDef(array &$fieldDef): void
    {
        parent::massageFieldDef($fieldDef);

        switch ($fieldDef['type']) {
            case 'integer':
                $fieldDef['len'] = '4';
                break;
            case 'smallint':
                $fieldDef['len'] = '2';
                break;
            case 'bigint':
                $fieldDef['len'] = '8';
                break;
            case 'double':
                $fieldDef['len'] = '8';
                break;
            case 'time':
                $fieldDef['len'] = '3';
                break;
            case 'varchar':
                if (empty($fieldDef['len'])) {
                    $fieldDef['len'] = '255';
                }
                break;
            case 'decimal':
                if (empty($fieldDef['precision'])
                    && !strpos($fieldDef['len'], ',')) {
                    $fieldDef['len'] .= ',0'; // Adding 0 precision if it is not specified
                }
                break;
        }

        // IBM DB2 requires default value for NOT NULL fields
        if (!empty($fieldDef['required']) && empty($fieldDef['default'])) {
            switch ($fieldDef['type']) {
                case 'integer':
                case 'smallint':
                case 'bigint':
                case 'float':
                case 'double':
                case 'decimal':
                    $fieldDef['default'] = 0;
                    break;
                default:
                    $fieldDef['default'] = '';
            }
        }

        if (empty($fieldDef['isnull'])) {
            $fieldDef['isnull'] = 'false';
        }
    }


    /**
     * Can this field be null?
     *
     * Fields that are part of indexes cannot be null in DB2 and this are marked as 'required' and not 'isnull'
     * @param array $vardef
     * @see parent::isNullable($vardef)
     */
    protected function isNullable($vardef)
    {
        if (!empty($vardef['required']) && ($vardef['required'] || $vardef['required'] == 'true')
            && !empty($vardef['isnull']) && (!$vardef['isnull'] || $vardef['isnull'] == 'false')) {
            return false;
        }
        return parent::isNullable($vardef);
    }

    /**+
     * Generates SQL for dropping a table.
     *
     * @param string $name table name
     * @return string SQL statement
     */
    public function dropTableNameSQL($name)
    {

        $return = parent::dropTableNameSQL(strtoupper($name));
        $this->reorgQueueRemoveTable($name);
        return $return;
    }

    /**+
     * Truncate table
     * @param  $name
     * @return string
     */
    public function truncateTableSQL($name)
    {
        return 'TRUNCATE TABLE ' . strtoupper($name) . ' IMMEDIATE';
    }

    /**
     * List of available collation settings
     * @return string
     */
    public function getDefaultCollation()
    {
        return 'utf8_general_ci';
    }

    /**
     * Does this type represent text (i.e., non-varchar) value?
     * @param string $type
     */
    public function isTextType($type)
    {
        $type = strtolower($type);
        if (strncmp($type, 'clob', 4) === 0 || strncmp($type, 'blob', 4) === 0) {
            return true;
        }
        $type = $this->getColumnType($type);
        if (strncmp($type, 'clob', 4) === 0 || strncmp($type, 'blob', 4) === 0) {
            return true;
        }
        return false;
    }

    /**
     * Does this type represent blob value?
     *
     * @param string $type
     * @return bool
     */
    public function isBlobType($type)
    {
        $type = strtolower($type ?? '');
        return strncmp($type, 'blob', 4) === 0
            || strncmp($this->getColumnType($type), 'blob', 4) === 0;
    }

    /**+
     * @see DBManager::renameColumnSQL()
     * Only supported
     */
    public function renameColumnSQL($tablename, $column, $newname)
    {
        return
            'ALTER TABLE ' . $this->getValidDBName($tablename, false, 'table') . ' ' .
            'RENAME COLUMN ' . $this->getValidDBName($column) . ' ' .
            'TO ' . $this->getValidDBName($newname);
    }

    /**
     * {@inheritDoc}
     */
    public function emptyValue($type, $forPrepared = false)
    {
        // http://www.devx.com/dbzone/Article/28713
        // http://publib.boulder.ibm.com/infocenter/db2luw/v9r7/index.jsp?topic=/com.ibm.db2.luw.sql.ref.doc/doc/r0008474.html

        if ($type == 'datetime' || $type == 'datetimecombo') {
            return $forPrepared
                ? '0001-01-01 00:00:00'
                : $this->convert($this->quoted('0001-01-01 00:00:00'), 'datetime');
        }

        if ($type == 'date') {
            return $forPrepared
                ? '0001-01-01'
                : $this->convert($this->quoted('0001-01-01'), 'date');
        }

        if ($type == 'time') {
            return $forPrepared
                ? '00:00:00'
                : $this->convert($this->quoted('00:00:00'), 'time');
        }

        return parent::emptyValue($type, $forPrepared);
    }

    /**
     * (non-PHPdoc)
     * @see DBManager::lastDbError()
     */
    public function lastDbError()
    {
        if (db2_conn_error()) {
            return 'IBM_DB2 connection error ' . db2_conn_error() . ': ' . db2_conn_errormsg();
        }
        /* FIXME:
        * Added $connOnly parameter to skip the statement error check
        * as there is a statics bug in the DB2 driver which persists failures
        * http://pecl.php.net/bugs/bug.php?id=22854
        */
        if (!$this->ignoreErrors) {
            $error = db2_stmt_error();
            if ($error) {
                return 'IBM_DB2 statement error ' . $error . ': ' . db2_stmt_errormsg();
            }
        }
        return false;
    }

    /**+
     * Quote DB2 search term
     * @param string $term
     * @return string
     */
    protected function quoteTerm($term)
    {
        if (strpos($term, ' ') !== false) {
            return '"' . $term . '"';
        }
        return $term;
    }

    /**+
     * @return array
     */
    public function getDbInfo()
    {
        $this->getDatabase();
        $server = @db2_server_info($this->database);
        if (is_object($server)) {
            $server = get_object_vars($server);
        } else {
            $server = null;
        }
        $client = @db2_client_info($this->database);
        if (is_object($client)) {
            $client = get_object_vars($client);
        } else {
            $client = null;
        }
        return [
            'IBM DB2 Client Info' => $client,
            'IBM DB2 Server Info' => $server,
        ];
    }

    public function validateQuery($query)
    {
        $this->checkConnection();

        $valid = (@db2_prepare($this->getDatabase(), $query, ['deferred_prepare' => DB2_DEFERRED_PREPARE_OFF]) != false); // Force boolean result
        $this->logger->debug('IBMDB2Manager.validateQuery  -> ' . $query . ' result: ' . $valid);
        return $valid;
    }

    protected function makeTempTableCopy($table)
    {
        $this->logger->debug("creating temp table for [$table]...");
        $create = $this->getOne("SHOW CREATE TABLE {$table}");
        if (empty($create)) {
            return false;
        }
        // rewrite DDL with _temp name
        $tempTableQuery = str_replace("CREATE TABLE `{$table}`", "CREATE TABLE `{$table}__uw_temp`", $create);
        $r2 = $this->query($tempTableQuery);
        if (empty($r2)) {
            return false;
        }

        // get sample data into the temp table to test for data/constraint conflicts
        $this->logger->debug('inserting temp dataset...');
        $q3 = "INSERT INTO `{$table}__uw_temp` SELECT * FROM `{$table}` LIMIT 10";
        $this->query($q3, false, "Preflight Failed for: {$q3}");
        return true;
    }

    /**
     * Tests an ALTER TABLE query
     * @param string table The table name to get DDL
     * @param string query The query to test.
     * @return string Non-empty if error found
     */
    protected function verifyAlterTable($table, $query)
    {
        $this->logger->debug('verifying ALTER TABLE');
        // Skipping ALTER TABLE [table] DROP PRIMARY KEY because primary keys are not being copied
        // over to the temp tables
        if (strpos(strtoupper($query), 'DROP PRIMARY KEY') !== false) {
            $this->logger->debug('Skipping DROP PRIMARY KEY');
            return '';
        }
        if (!$this->makeTempTableCopy($table)) {
            return 'Could not create temp table copy';
        }

        // test the query on the test table
        $this->logger->debug('testing query: [' . $query . ']');
        $tempTableTestQuery = str_replace("ALTER TABLE `{$table}`", "ALTER TABLE `{$table}__uw_temp`", $query);
        if (strpos($tempTableTestQuery, 'idx') === false) {
            if (strpos($tempTableTestQuery, '__uw_temp') === false) {
                return 'Could not use a temp table to test query!';
            }

            $this->logger->debug('testing query on temp table: [' . $tempTableTestQuery . ']');
            $this->query($tempTableTestQuery, false, "Preflight Failed for: {$query}");
        } else {
            // test insertion of an index on a table
            $tempTableTestQuery_idx = str_replace('ADD INDEX `idx_', 'ADD INDEX `temp_idx_', $tempTableTestQuery);
            $this->logger->debug('testing query on temp table: [' . $tempTableTestQuery_idx . ']');
            $this->query($tempTableTestQuery_idx, false, "Preflight Failed for: {$query}");
        }
        $mysqlError = $this->lastError();
        if (!empty($mysqlError)) {
            return $mysqlError;
        }
        $this->dropTableName("{$table}__uw_temp");

        return '';
    }

    protected function verifyGenericReplaceQuery($querytype, $table, $query)
    {
        $this->logger->debug("verifying $querytype statement");

        if (!$this->makeTempTableCopy($table)) {
            return 'Could not create temp table copy';
        }
        // test the query on the test table
        $this->logger->debug('testing query: [' . $query . ']');
        $tempTableTestQuery = str_replace("$querytype `{$table}`", "$querytype `{$table}__uw_temp`", $query);
        if (strpos($tempTableTestQuery, '__uw_temp') === false) {
            return 'Could not use a temp table to test query!';
        }

        $this->query($tempTableTestQuery, false, "Preflight Failed for: {$query}");
        $error = $this->lastError(); // empty on no-errors
        $this->dropTableName("{$table}__uw_temp"); // just in case
        return $error;
    }

    /**
     * Tests a DROP TABLE query
     * @param string table The table name to get DDL
     * @param string query The query to test.
     * @return string Non-empty if error found
     */
    public function verifyDropTable($table, $query)
    {
        return $this->verifyGenericReplaceQuery('DROP TABLE', $table, $query);
    }

    /**
     * Execute data manipulation statement, then roll it back
     * @param  $type
     * @param  $table
     * @param  $query
     * @return string
     */
    protected function verifyGenericQueryRollback($type, $table, $query)
    {
        $db = $this->database;
        $this->logger->debug("verifying $type statement");
        $stmt = db2_prepare($db, $query);
        if (!$stmt) {
            return 'Cannot prepare statement';
        }
        $ac = db2_autocommit($db);
        db2_autocommit($db, DB2_AUTOCOMMIT_OFF);
        // try query, but don't generate result set and do not commit
        $res = db2_execute($stmt, OCI_DESCRIBE_ONLY | OCI_NO_AUTO_COMMIT);
        // just in case, rollback all changes
        $error = $this->lastError();
        db2_rollback($db);
        db2_free_stmt($stmt); // It would be a good idea to keep this and reuse it.
        db2_autocommit($db, $ac);

        if (!$res) {
            return 'Query failed to execute';
        }
        return $error;
    }

    /**
     * Tests an INSERT INTO query
     * @param string table The table name to get DDL
     * @param string query The query to test.
     * @return string Non-empty if error found
     */
    public function verifyInsertInto($table, $query)
    {
        return $this->verifyGenericQueryRollback('INSERT', $table, $query);
    }

    /**
     * Tests an UPDATE query
     * @param string table The table name to get DDL
     * @param string query The query to test.
     * @return string Non-empty if error found
     */
    public function verifyUpdate($table, $query)
    {
        return $this->verifyGenericQueryRollback('UPDATE', $table, $query);
    }

    /**
     * Tests an DELETE FROM query
     * @param string table The table name to get DDL
     * @param string query The query to test.
     * @return string Non-empty if error found
     */
    public function verifyDeleteFrom($table, $query)
    {
        return $this->verifyGenericQueryRollback('DELETE', $table, $query);
    }

    /**+
     * Check if certain database exists
     * @param string $dbname
     * With DB2 the admin creates the database and we cannot connect without full credentials and the database name.
     */
    public function dbExists($dbname)
    {
        return true;
    }


    /**~
     * Check if certain DB user exists
     * @param string $username
     * DB2 has no concept of a 'database' user. It uses Operating System users that may
     * have or not have access GRANTED to certain aspects of the database. I.e. it will
     * delegate user authentication to the OS.
     */
    public function userExists($username)
    {
        //TODO Should we implement an OS verification if a user exists???
        return true;
    }

    /**+
     * Create DB user
     * @param string $database_name
     * @param string $host_name
     * @param string $user
     * @param string $password
     * DB2 has no concept of a 'database' user. It uses Operating System users that may
     * have or not have access GRANTED to certain aspects of the database. I.e. it will
     * delegate user authentication to the OS.
     */
    public function createDbUser($database_name, $host_name, $user, $password)
    {
        return true;
    }

    /**+
     * Create a database
     * @param string $dbname
     * DB2 does not support the programmatic creation of databases. The admin
     * will have the create the database manually.
     */
    public function createDatabase($dbname)
    {
        return true;
    }

    /**+
     * Drop a database
     * @param string $dbname
     * DB2 does not support the programmatic creation of databases.
     */
    public function dropDatabase($dbname)
    {
        return true;
    }

    /**+
     * Check if this driver can be used
     * @return bool
     */
    public function valid()
    {
        return function_exists('db2_connect');
    }

    /**
     * Commits pending changes to the database when the driver is setup to support transactions.
     *
     * @return bool true if commit succeeded, false if it failed
     */
    public function commit()
    {
        if ($this->database) {
            $success = db2_commit($this->database);
            $this->logger->info("IBMDB2Manager.commit(): $success");
            $this->executeReorgs();
            return $success;
        }
        return true;
    }

    /**
     * Rollsback pending changes to the database when the driver is setup to support transactions.
     *
     * @return bool true if rollback succeeded, false if it failed
     */
    public function rollback()
    {
        if ($this->database) {
            $success = db2_rollback($this->database);
            $this->logger->info("IBMDB2Manager.rollback(): $success");
            return $success;
        }
        return false;
    }


    /// START REORG QUEUE FUNCTIONALITY

    /**
     * Protected variable that keeps lists of database objects that require reorganization
     * @var array
     */
    protected $reorgQueues = [
        'table' => [],
        //'index' => array(), // We currently don't need to reorg indexes, this is for future changes
    ];

    /**
     * Adds the specified table to the queue for reorganization
     * @param $name
     * @return void
     */
    protected function reorgQueueAddTable($name)
    {
        $this->reorgQueues['table'] [] = strtoupper($name);
    }

    /**
     * Removes the specified table from the reorganization queue if it was already added.
     * @param $name
     * @return void
     */
    protected function reorgQueueRemoveTable($name)
    {
        $name = strtoupper($name);
        $this->reorgQueues['table'] = array_diff($this->reorgQueues['table'], [$name]);
    }

    /**
     * Performs the REORG for any database objects (pending reorganization) in the reorg queue
     * @return void
     */
    protected function executeReorgs()
    {
        $tables = array_unique($this->reorgQueues['table']);
        foreach ($tables as $table) {
            $this->reorgTable($table);
        }
        if (safeCount($tables) > 0) {
            $this->logger->info('Table REORG completed on: ' . implode(', ', $tables));
            $this->reorgQueues['table'] = []; // Clearing out queue
        }
    }

    /**
     * Perform REORG query for a table.
     * @param string $table
     */
    protected function reorgTable(string $table): void
    {
        $validTableName = $this->getValidDBName($table, false, 'table');
        $sql = "CALL ADMIN_CMD('REORG TABLE {$validTableName} ALLOW READ ACCESS')";
        $this->query($sql, false, 'REORG problem');
    }

    /// END REORG QUEUE FUNCTIONALITY

    protected function runstatsTable(string $table): void
    {
        $sql = "CALL ADMIN_CMD('RUNSTATS ON TABLE {$table} ON ALL COLUMNS and INDEXES ALL ALLOW READ ACCESS')";
        $this->query($sql, false, 'RUNSTATS problem');
    }

    public function optimizeTable(string $table): void
    {
        if (!SugarConfig::getInstance()->get('disable_optimize_table', false)) {
            $this->reorgTable($table);
            $this->runstatsTable($table);
        }
    }

    /**
     * Check if this DB name is valid
     *
     * @param string $name
     * @return bool
     */
    public function isDatabaseNameValid($name)
    {
        // No funny chars
        return preg_match('/[\#\"\'\*\/\\?\:\\<\>\-\ \&\!\(\)\[\]\{\}\;\,\.\`\~\|\\\\]+/', $name) == 0;
    }

    public function installConfig()
    {
        return [
            'LBL_DBCONFIG_MSG3' => [
                'setup_db_database_name' => ['label' => 'LBL_DBCONF_DB_NAME', 'required' => true],
            ],
            'LBL_DBCONFIG_MSG2' => [
                'setup_db_host_name' => ['label' => 'LBL_DBCONF_HOST_NAME', 'required' => true],
                'setup_db_port_num' => ['label' => 'LBL_DBCONF_HOST_PORT'],
                'setup_db_create_sugarsales_user' => false,

            ],
            'LBL_DBCONF_TITLE_USER_INFO' => [],
            'LBL_DBCONFIG_B_MSG1' => [
                'setup_db_admin_user_name' => ['label' => 'LBL_DBCONF_DB_ADMIN_USER', 'required' => true],
                'setup_db_admin_password' => ['label' => 'LBL_DBCONF_DB_ADMIN_PASSWORD', 'type' => 'password'],
            ],
        ];
    }

    /**
     * @see DBManager::massageValue()
     */
    public function massageValue($val, $fieldDef, $forPrepared = false)
    {
        $type = $this->getFieldType($fieldDef);
        $ctype = $this->getColumnType($type);

        // Deal with values that would exceed the 32k constant limit of DB2
        //Note we assume DB2 counts bytes and not characters
        if (strpos((string)$ctype, 'clob') !== false && strlen((string)$val) > 32000 && !$forPrepared) {
            $chunk = '';
            // Incrementing with number of bytes of chunk to not loose any characters
            for ($pos = 0, $i = 0; $pos < strlen($val) && $i < 5; $pos += strlen($chunk), $i++) {
                //mb_strcut uses bytes and shifts to left character boundary for both start and stop if necessary
                $chunk = mb_strcut($val, $pos, 32000);
                if (!isset($massagedValue)) {
                    $massagedValue = "TO_CLOB('$chunk')";
                } else {
                    $massagedValue = "CONCAT($massagedValue, '$chunk')";
                }
            }

            return $massagedValue;
        }

        $val = parent::massageValue($val, $fieldDef, $forPrepared);

        if (!$forPrepared) {
            switch ($type) {
                case 'blob':
                case 'longblob':
                    $val = 'SYSIBM.BLOB(' . $val . ')';
                    break;
            }
        }
        return $val;
    }


    /**
     * Generates the a recursive SQL query or equivalent stored procedure implementation.
     * The DBManager's default implementation is based on SQL-99's recursive common table expressions.
     * Databases supporting recursive CTEs only need to set the recursive_query capability to true
     * @param string $tablename table name
     * @param string $key primary key field name
     * @param string $parent_key foreign key field name self referencing the table
     * @param string $fields list of fields that should be returned
     * @param bool $lineage find the lineage, if false, find the children
     * @param string $startWith identifies starting element(s) as in a where clause
     * @param string $level when not null returns a field named as level which indicates the level/dept from the starting point
     * @return string               Recursive SQL query or equivalent representation.
     */
    public function getRecursiveSelectSQL($tablename, $key, $parent_key, $fields, $lineage = false, $startWith = null, $level = null, $whereClause = null)
    {
        if ($lineage) {
            $connectBy = "CONNECT BY $key = PRIOR $parent_key";  // Search up the tree to get lineage
        } else {
            $connectBy = "CONNECT BY $parent_key = PRIOR $key";  // Search down the tree to find children
        }

        if (!empty($startWith)) {
            $startWith = 'START WITH ' . $startWith;
        } else {
            $startWith = '';
        }

        if (!empty($level)) {
            $fields = "$fields, LEVEL as $level";
        }

        // cleanup WHERE clause
        if (empty($whereClause)) {
            $whereClause = '';
        } else {
            $whereClause = ltrim($whereClause);
            preg_replace('/^WHERE\s/i', '', $whereClause);  //remove WHERE if it exists

            if (!preg_match('/^\s*?AND\s/i', $whereClause)) {  // Add AND
                $whereClause = "AND {$whereClause}";
            }

            $whereClause .= ' ';  // make sure there is a trailing blank
        }

        return "SELECT $fields FROM $tablename $startWith $whereClause $connectBy $whereClause";
    }


    /**
     * Returns a DB specific FROM clause which can be used to select against functions.
     * Note that depending on the database that this may also be an empty string.
     * @return string
     */
    public function getFromDummyTable()
    {
        return 'from sysibm.sysdummy1';
    }

    /**
     * Returns a DB specific piece of SQL which will generate GUID (UUID)
     * This string can be used in dynamic SQL to do multiple inserts with a single query.
     * I.e. generate a unique Sugar id in a sub select of an insert statement.
     * @return string
     */
    public function getGuidSQL()
    {
        $guidStart = create_guid_section(9);
        return "'$guidStart-' || HEX(generate_unique())";
    }
}
