<?php
/*
 * Database read/write helpers for PDO
*/

require 'Debug2File.class.php';

class PDOMy {

	public $database;
	public $dbName;
	public $log;
	
	public function __construct() { 
		
		$this->log = new Debug2File();
		
		
	}
	function __destruct() {
		
		$this->database = null;
		$this->log = null;
	}

	public function connectPDO($host, $username, $password, $mydatabase) {
		try {
			$databaselink=new PDO("mysql:host=$host;dbname=$mydatabase;charset=utf8", $username, $password);
			$databaselink->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			return $databaselink;
		} catch (PDOException $ex) {
			$this->log->write(2,"DB Connection Error: ".$ex->getMessage());
            return 1;
		}

	}


	public function queryDB($query) {

		try {
			$stmt = $this->database->query($query);
			$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
			return $results;
		} catch (PDOException $e) {
			$this->log->write(3,"DB Query Error: ".$e->getMessage());
            return 1;
		}

	}

	public function addRow ($table, $data) {

		if (is_array($data)) {
			$fields = join(",",array_keys($data));
			$param_list = implode(',' , array_map("param_list" , array_keys($data)) );
			$sql = "INSERT INTO `$table` ($fields) VALUES ($param_list)";

			try {
				$stmt = $this->database->prepare($sql);
				$stmt->execute($data);
				$affected_rows = $stmt->rowCount();
				$this->log->write(7,"AddRow Query: |$sql| Rows Affected: $affected_rows\n" );
				return 0;
			} catch (PDOException $e) {
				$this->log->write(3,"DB Query Error: ".$e->getMessage());
				return 1;
			}

		} else {
			$this->log->write(3,"Method Error: Value passed to addRow() were not arrays as expected");
			return 1;
		}
	}

	public function updateRow ($table, $data, $id) {

		if (is_array($data) && $id != "") {

			foreach ($data as $key => $value) {
				$fields[] ="$key=:$key";
			}
			$fields=implode(',',$fields);
			$sql = "UPDATE `$table` SET $fields WHERE `id`=:id";
			//echo "$sql";

			try {
				$stmt = $this->database->prepare($sql);
				$stmt->bindValue(':id', $id, PDO::PARAM_STR);

				foreach ($data as $key => $value) {
					$stmt->bindValue($key, $value, PDO::PARAM_STR);
				}
				$stmt->execute();
				$affected_rows = $stmt->rowCount();
				$this->log->write(7,"Update Query: |$sql| Rows Affected: $affected_rows\n" );
				return 0;
			} catch (PDOException $e) {
				$this->log->write(3,"DB Query Error: ".$e->getMessage());
				return 1;
			}
		}
        return 1;
	}


	public function deleteRow ($table, $id) {
		try {
			$stmt = $this->database->prepare("DELETE FROM $table WHERE id=:id");
			$stmt->bindValue(':id', $id, PDO::PARAM_STR);
			$stmt->execute();
			$affected_rows = $stmt->rowCount();
			$this->log->write(7,"Delete Row: | Rows Affected: $affected_rows\n" );

		} catch (PDOException $e) {
			$this->log->write(3,"DB Query Error: ".$e->getMessage());
		}
	}

	public function columnNames ($table) {
		try {
			$stmt = $this->database->prepare("DESCRIBE $table");
			$stmt->execute();
			return $stmt->fetchAll(PDO::FETCH_COLUMN);
				
		} catch (PDOException $e) {
			$this->log->write(3,"DB Query Error: ".$e->getMessage());
            return 1;
		}

	}
	
	public function fullTextSearch($table, $term, $limit=250) {

		try {
			$stmt = $this->database->prepare("DESCRIBE $table");
			$stmt->execute();
			$columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
			$query = null;
			$query .= "SELECT * from `$table` WHERE ";
			
			$i = 0;
			foreach ($columns as $field) {
					if ($i != 0) { $query .= " OR "; }
					$i++;
					$query .= "`$field` LIKE '%$term%'"; 
			}
			$query .= " LIMIT $limit";
			$stmt = $this->database->query($query);
			$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
			return $results;
			
			} catch (PDOException $e) {
				$this->log->write(3,"DB Query Error: ".$e->getMessage());
            return 1;
			}
		
	}
	
	
	
	
}


