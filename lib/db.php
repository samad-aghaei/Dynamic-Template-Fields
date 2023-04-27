<?php

$DB = (new class ([
	'dbHost' 	 => 'localhost',
	'dbUser' 	 => 'root',
	'dbPassword' => 'root',
	'dbName' 	 => 'users',
	'dbPost' 	 => '3306',
	'dbSocket' 	 => 'path/to/mysql/mysql.sock'
]) extends mysqli {
	
	private static $instance;

    public function __construct($dbConfig)
    {
		parent::__construct();
		parent::options( MYSQLI_INIT_COMMAND, 'SET AUTOCOMMIT = 1' );
		@parent::real_connect(
			$dbConfig['dbHost'], 
			$dbConfig['dbUser'], 
			$dbConfig['dbPassword'], 
			$dbConfig['dbName'], 
			$dbConfig['dbPort'], 
			$dbConfig['dbSocket'],
			MYSQLI_CLIENT_COMPRESS
		);
		
		if( \mysqli_connect_errno() ){
			die('Database Connection Error (' . \mysqli_connect_error() . ' Error No: ' . \mysqli_connect_errno() . ') ');
		}

		parent::set_charset('utf8');
    }

    final public static function getInstance($dbConfig)
    {	
		if (!static::$instance)
		{
            static::$instance = new static($dbConfig);
		}
        return static::$instance;
    }

    final public static function dbClose ()
    {
		if(static::$instance)
		{
			static::$instance->kill(static::$instance->thread_id);
			static::$instance->close();
		}
    }

	final public function __destruct()
	{
		static::dbClose();
	}

    final public function __clone(){}
    final public function __sleep(){}
    final public function __wakeup(){}
});

