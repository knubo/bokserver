<?php


/*
 * Created on Apr 5, 2007
 *
 */
class DB {

	private $link;

	function link() {
		return $this->link;
	}

	function begin() {
		$this->link->autocommit(FALSE);
	}

	function rollback() {
		$this->link->rollback();
	}

	function commit() {
		if(!$this->link->commit()) {
			$this->report_error();
		}
	}

	function __construct($keeplatin1 = 0) {
         $this->link = mysqli_connect(AppConfig::DB_HOST_NAME,
            AppConfig::DB_USER, AppConfig::DB_PASSWORD, AppConfig::DB_NAME);

        if(!$keeplatin1) {
            mysqli_query($this->link, "SET NAMES 'utf8'");
        }
		if (mysqli_connect_errno()) {
            header("HTTP/1.0 512 DB error");
			die("Connect failed: ".mysqli_connect_error());
		}
	}

    function insert_id() {
    	return mysqli_insert_id($this->link);
    }

	function table_exists($table) {
		$result = $this->link->query("show tables like '" . $table . "'");

        if(!$result) {
        	$this->report_error();
        }

		$match = $result->num_rows;

		$result->close();

		return $match > 0;
	}

    function report_error() {
        $error = $this->link->error;
        header("HTTP/1.0 512 DB error");
        $this->rollback();
        die("DB:".$error);
    }

	function prepare($query) {
		if(false && strstr($query, "bok_log") === FALSE) {
			include_once ("logger.php");
			$log = new Logger($this);
			$log->log("debug","db", $query);			
		}


		$mysqli = mysqli_prepare($this->link, $query);

		if (!$mysqli) {
            $this->report_error();
		}
		

		return new PrepWrapper($mysqli, $this);
	}

	function search($prequery, $orderby = "") {
		return new SearchWrapper($this, $prequery, $orderby);
	}

	function action($query) {
		if(!mysqli_query($this->link, $query)) {
			$this->report_error();
		}

	}

	function backtrace() {
		$output = "<div style='text-align: left; font-family: monospace;'>\n";
		$output .= "<b>Backtrace:</b><br />\n";
		$backtrace = debug_backtrace();

		foreach ($backtrace as $bt) {
			$args = '';
			foreach ($bt['args'] as $a) {
				if (!empty ($args)) {
					$args .= ', ';
				}
				switch (gettype($a)) {
					case 'integer' :
					case 'double' :
						$args .= $a;
						break;
					case 'string' :
						$a = htmlspecialchars(substr($a, 0, 64)) . ((strlen($a) > 64) ? '...' : '');
						$args .= "\"$a\"";
						break;
					case 'array' :
						$args .= 'Array(' . count($a) . ')';
						break;
					case 'object' :
						$args .= 'Object(' . get_class($a) . ')';
						break;
					case 'resource' :
						$args .= 'Resource(' . strstr($a, '#') . ')';
						break;
					case 'boolean' :
						$args .= $a ? 'True' : 'False';
						break;
					case 'NULL' :
						$args .= 'Null';
						break;
					default :
						$args .= 'Unknown';
				}
			}
			$output .= "<br />\n";
			$output .= "<b>file:</b> {$bt['line']} - {$bt['file']}<br />\n";
			$output .= "<b>call:</b> {$bt['class']}{$bt['type']}{$bt['function']}($args)<br />\n";
		}
		$output .= "</div>\n";
		return $output;
	}
	function affected_rows() {
		return $this->link->affected_rows;
	}
}

class PrepWrapper {
	private $Mysqli;

	function __construct($mysqli, $db) {
		$this->Mysqli = $mysqli;
        $this->db = $db;
	}

    function meta() {
        $handle = $this->Mysqli;

        $metadata = $handle->result_metadata();

        # No rows, no result, no action.
        if ($metadata == FALSE) {
            return;
        }

        $nof = $metadata->field_count;

        # The metadata of all fields
        return $metadata->fetch_fields();
    }

	function execute() {
		$handle = $this->Mysqli;

		if (!$handle->execute()) {
			$this->db->report_error();
		}

		$metadata = $handle->result_metadata();

		# No rows, no result, no action.
		if ($metadata == FALSE) {
			return;
		}

		$nof = $metadata->field_count;

		# The metadata of all fields
		$fieldMeta = $metadata->fetch_fields();

		# convert it to a normal array just containing the field names
		$fields = array ();
		for ($i = 0; $i < $nof; $i++) {
			$fields[$i] = $fieldMeta[$i]->name;
		}

		# The idea is to get an array with the result values just as in mysql_fetch_assoc();
		# But we have to use call_user_func_array to pass the right number of args ($nof+1)
		# So we create an array:
		# array( $stmt, &$result[0], &$result[1], ... )
		# So we get the right values in $result in the end!

		# Prepare $result and $arg (which will be passed to bind_result)
		$result = array ();
		$arg = array (
			$handle
		);
		for ($i = 0; $i < $nof; $i++) {
			$result[$i] = '';
			$arg[$i +1] = & $result[$i];
		}

		if(!call_user_func_array('mysqli_stmt_bind_result', $arg)) {
        	$this->db->report_error();
		}

		$myall = array ();

		# after mysqli_stmt_fetch(), our result array is filled just perfectly,
		# but it is numbered (like in mysql_fetch_array() ), not indexed by field name!
		# Make it ordered by field name.
		while ($handle->fetch()) {
			$row = array ();

			for ($i = 0; $i < $nof; $i++) {
				$row[$fields[$i]] = $result[$i];
			}
			$myall[] = $row;
		}
		return $myall;
	}

    function bind_array_params($types, $args) {

        $Args = array();
        foreach($args as $k => &$arg){
            $Args[$k] = &$arg;
        }

        $allArgs = array_merge(array (
        $this->Mysqli,
        $types
        ), $Args);

        if(!call_user_func_array('mysqli_stmt_bind_param', $allArgs)) {
            $this->db->report_error();
        }
    }

    function bind_params() {
        $args = func_get_args();

        $Args = array();
        foreach($args as $k => &$arg){
            $Args[$k] = &$arg;
        }

        if(!call_user_func_array('mysqli_stmt_bind_param',
        array_merge(array ($this->Mysqli), $Args))) {
            $this->db->report_error();
        }
    }
	
}

class SearchWrapper {
	private $Db;
	private $Prequery;
	private $Type;
	private $Params;
	private $Query;
	private $OuterJoin;
	private $SqlWhere;

	function SearchWrapper($db, $prequery, $orderby) {
		$this->Db = $db;
		$this->Prequery = $prequery;
		$this->Params = array();
		$this->Type = "";
		$this->OrderBy = $orderby;
		$this->OuterJoin = "";
		$this->Query = "";
		$this->SqlWhere = "";
	}

    function query() {
    	return $this->Query;
    }

	function addOuterJoin($table, $condA, $condB) {
		$this->OuterJoin .= " left join $table on $condA = $condB ";
	}

	function addAndSQL($type, $bind, $sql) {
		if(sizeof($this->Params) > 0) {
			$this->Query .=" and ";
		}

		$this->Type .=$type;
		$this->Params[] = $bind;
		$this->Query .= $sql;
	}

    function addOnlySql($sql) {
        if(sizeof($this->Params) > 0) {
            $this->Query .=" and ";
        }
    	$this->Query .= $sql;
    }

	function addAndParam0($type, $name, $param, $allowNull = 0) {
		if($param == 0) {
			return;
		}
		$this->addAndParam($type, $name, $param, $allowNull);
	}

	function addAndParam($type, $name, $param, $allowNull = 0) {
		if($param == "" && !$allowNull) {
			return;
		}

		$this->Type .=$type;

		if(sizeof($this->Params) > 0) {
			$this->Query .=" and ";
		}

		$this->Params[] = $param;
		if($type == "s") {
			$this->Query .="$name like ?";
		} else {
			$this->Query .="$name = ?";
		}
	}

	function addAndOrQuery0($type, $name1, $name2, $param, $allowNull = 0) {
		if($param == 0) {
			return;
		}
		
		$this->addAndOrQuery($type, $name1, $name2, $param, $allowNull);
	}

	function addAndOrQuery($type, $name1, $name2, $param, $allowNull = 0) {
		if($param == "" && !$allowNull) {
			return;
		}

		$this->Type .=$type.$type;

		if(sizeof($this->Params) > 0) {
			$this->Query .=" and ";
		}

		$this->Params[] = $param;
		$this->Params[] = $param;
		if($type == "s") {
			$this->Query .="($name1 like ? or name2 like ?)";
		} else {
			$this->Query .="($name1 = ? or $name2 = ?)";
		}
	}

    function addAndQuery($type, $param, $exists) {
        if($param == "") {
            return;
        }
        $this->Type .=$type;

        if(sizeof($this->Params) > 0) {
            $this->Query .=" and ";
        }
        $this->Params[] = $param;

        $this->Query .= $exists;

    }

	function execute() {
		if(sizeof($this->Params) == 0) {
			if($this->Query) {
				$sql = $this->Prequery. " ".$this->OuterJoin." where ".$this->Query." ".$this->SqlWhere." ".$this->OrderBy;							
			} else {
				$sql = $this->Prequery. " ".$this->OuterJoin." ".$this->SqlWhere." ".$this->OrderBy;			
			}
			
			if(false && strstr($sql, "bok_log") === FALSE) {
				include_once ("logger.php");
				$log = new Logger($this->Db);
				$log->log("debug","db", $sql);			
			}
			
			
			$prep = $this->Db->prepare($sql);
			return $prep->execute();
		}

		$sql = $this->Prequery. " ".$this->OuterJoin." where ".$this->Query. " ".$this->SqlWhere." ".$this->OrderBy;
		$prep = $this->Db->prepare($sql);
		$prep->bind_array_params($this->Type, $this->Params);
		return $prep->execute();
	}
}
?>
