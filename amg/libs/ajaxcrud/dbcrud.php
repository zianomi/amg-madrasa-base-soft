<?php
function q($q, $debug = 0){

    global $mysqliConn;

				if (!($r = $mysqliConn->query($q))) {
					$errorMsg = "Mysql Error: " . $mysqliConn->error;
					//logError($errorMsg);
					exit("<p>$errorMsg</p>");
				}



			if($debug == 1){
				echo "<br>$q<br>";
			}



			if(stristr(substr($q,0,8),"delete") ||	stristr(substr($q,0,8),"insert") || stristr(substr($q,0,8),"update")){

					$affectedRows = $mysqliConn->affected_rows;


				if ($affectedRows > 0){
					return true;
				}
				return false;
			}



				$numRows = $r->num_rows;


			if ($numRows > 1){

					while ($row = $r->fetch_array()){
						$results[] = $row;
					}

			}
			else if ($numRows == 1){
				$results = array();

					$results[] = $r->fetch_array();


			}
			else{
				$results = array();
			}

			return $results;
		}
function q1($q, $debug = 0){
    global $mysqliConn;

        if (!($r = $mysqliConn->query($q))) {
            $errorMsg = "Mysql Error: " . $mysqliConn->error;
            //logError($errorMsg);
            exit($errorMsg);
        }



    if($debug == 1){
        echo "<br>$q<br>";
    }

    if (isset($r)) {
        $row = $r->fetch_array();
    }

    if(count($row) == 2){
        return $row[0];
    }

    return $row;
}
function qr($q, $debug = 0){
    global $mysqliConn;


        if (!($r = $mysqliConn->query($q))) {
            $errorMsg = "<b>Mysql Error: " . $mysqliConn->error;
            exit("<p>$errorMsg</p>");
        }



    if($debug == 1){
        echo "<br>$q<br>";
    }

    if(stristr(substr($q,0,8),"delete") ||	stristr(substr($q,0,8),"insert") || stristr(substr($q,0,8),"update")){

            $numberOfAffectedRows = $mysqliConn->affected_rows;


        if ($numberOfAffectedRows > 0) {
            return true;
        }
        return false;
    }

    if(stristr(substr($q,0,8),"create") || stristr(substr($q,0,8),"drop")){
        //added for executing create table statements; e.g. the example install script /examples/install.php
        return true;
    }

    $results = array();


        $results[] = $r->fetch_array();


    $results = $results[0];

    return $results;
}
function amgQ($sql){
    global $mysqliConn;
    $res = $mysqliConn->query($sql);
    $data = array();
    while ($row = $res->fetch_assoc()){
        $data[] = $row;
    }

    return $data;
}