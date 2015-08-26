<?php
Class Notes extends Database {
	
	public $noteID;
	public $noteTitle;
	public $noteContent;

	/*
	 * Load database connection for notes system.
	 */
	public function __construct($db_host, $db_name, $db_user, $db_pass) { 
		parent::__construct($db_host, $db_name, $db_user, $db_pass);

	}

	public function newNote() {
		$this->insertRow('Notes', 
			array( 
			'note_title' 	=> $this->noteTitle,
			'note_content' 	=> $this->noteContent
			) 
		);
	}

	public function editNote() {
		$this->updateRow($table, $where = array('id' => $this->noteID));
	}

	public function deleteNote() {
		$this->deleteRow('Notes', array('id' => $this->noteID) );
	}

	/*
	 * this application installation query
	 */
	public function installation() {
		$sql = '
		CREATE TABLE IF NOT EXISTS `Notes` (
			`id` INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
			`note_title` VARCHAR(75) NOT NULL,
			`noteContent` VARCHAR(75) NOT NULL
		)';
		
		$this->sql = $sql;
		$this->executeSql();
	}
}

Class Database {
	/*
	 * Create new Database Connection 
	 */
	public $sql;
	
	public $db_host;
	public $db_name;
	public $db_user;
	public $db_pass;

	private $connType = 'mysqli';
	private $conn;
	/*
	 * $connection_type - 0 - mysqli, 1 - mysql
	 */
	public function __construct($db_host, $db_name, $db_user, $db_pass, $connection_type=0) {
		$this->db_host = $db_host;
		$this->db_name = $db_name;
		$this->db_user = $db_user;
		$this->db_pass = $db_pass;

		if($connection_type == 0){
			$this->connType = 'mysqli';
		}
		else {
			$this->connType = 'mysql';
		}

		$this->connect();//connect to db...
	}

	/*
	 * Set mysql connection
	 */
	private function connect() {
		if( $this->connType=='mysqli' && function_exists('mysqli_connect') ) {
			$this->conn = mysqli_connect( $this->db_host , $this->db_user, $this->db_pass, $this->db_name ) 
				or trigger_error('mysqli connection faild');
		}
		else {
			$this->conn = mysql_connect( $this->db_host , $this->db_user, $this->db_pass, $this->db_name )
			or trigger_error('mysql connection faild');
		}
	}

	private function secureQuery($value) {
		if( $this->connType == 'mysqli' ) {
			mysqli_real_escape_string($this->conn, $value);
		}
		elseif( $this->connType == 'mysql' ) {
			mysql_real_escape_string($value, $this->conn);	
		}
	}

	/*
	 * Execute simple php sql query
	 */
	public function executeSql() {
		if($this->connType=='mysqli') {
			mysqli_query($this->conn ,$this->sql) 
				or trigger_error('mysqli query: '. $this->sql . ' Has been faild... error: '.$this->conn->error);
		}
		elseif($this->connType == 'mysql') {
			mysql_query($this->sql, $this->conn)
				or trigger_error('mysql query: '. $this->sql . ' Has been faild... error: '.$this->conn->error);
		}		
	}

	/*
	 * Fetch results to array
	 */
	public function fetchResults() {
		if($this->connType=='mysqli') {
			return mysqli_fetch_array($this->sql)
				or trigger_error('mysqli fetch query: '. $this->sql . ' Has been faild... error: '.$this->conn->error);
		}
		elseif($this->connType == 'mysql') {
			return mysql_fetch_array($this->sql, $this->conn)
				or trigger_error('mysql fetch query: '. $this->sql . ' Has been faild... error: '.$this->conn->error);
		}
	}

	/*
	 * Delete from db easly...
	 */
	public function deleteRow($table, $where) {
		// $where can be an array with some of fields ('field_name' => 'field_value')
		if(is_array($where))
		{
			foreach($where as $field => $value)
			{
				$where = $field.'='.$this->secureQuery($this->conn, $value);
			}
		}

		$this->sql = 'DELETE FROM'. $table .' WHERE '. $where;
		$this->executeSql();
	}

	/*
	 * this function is to update data where i wanna do it!
	 */
	public function updateRow($table, $fields, $where) {
		// $where can be an array with some of fields ('field_name' => 'field_value')
		if( is_array($where) )
		{
			foreach($where as $field => $value)
			{
				$where = $field.'='.$this->secureQuery($this->conn, $value);
			}
		}

		$field_names = '';
		$field_values = '';

		$array_len = count($fields);
		$i = 0;

		foreach($fields as $field => $value) {
			$seperator = ( $i == $array_len-1 ) ? '' : ',';
			$field_names  = $field_names  . $field  . $seperator;
			$field_values = $field_values . "'" . $value . "'" . $seperator;

			$i++;
		}

		$this->sql = 'UPDATE '.$field_names.' FROM '.$table.' WHERE '.$where;
		$this->executeSql();
	}

	/*
	 * insert new rows to my database
	 */
	public function insertRow($table, $fields=[]) {
		$field_names = '';
		$field_values = '';

		$array_len = count($fields);
		$i = 0;
		//print_r()( $fields );
		foreach($fields as $field => $value) {
			$seperator = ( $i == $array_len-1 ) ? '' : ',';
			$field_names  = $field_names  . $field  . $seperator;
			$field_values = $field_values . "'" . $value . "'" . $seperator;

			$i++;
		}

		$this->sql = 'INSERT INTO '.$table.' ('.$field_names.') VALUES('.$field_values.') ';
		$this->executeSql();
	}
}