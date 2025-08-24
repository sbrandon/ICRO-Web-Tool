<?php

//-------------------------------------------------------------------------------------------------
// Logger.php
//
// Utility class to connect to record site activity to a log table in the DB
// Depends on the DBLink class being initialised already....
//
// Dave Masterson, Sept 2009
// Updated: Stephen Brandon, July 2024
//-------------------------------------------------------------------------------------------------

class Logger
{
    var $db_object;    // The connection to the DB
    var $last_error;   // Store the last error, for logging/debugging

    //-----------------------------------------------------------------------------------------
    // Creates a new Logger
    // Returns true or false, depending on sucess or failure
    //-----------------------------------------------------------------------------------------
    function __construct($theDB)
    {
        // Initialise the error record
        $this->last_error = 'No Error';

        $this->db_object = $theDB;
    }

    //-----------------------------------------------------------------------------------------
    // Writes an entry to the log table in the DB
    // Returns true or false, depending on success or failure
    //-----------------------------------------------------------------------------------------
    function log($data) 
    {
        $data = addslashes($data);

        // for sms logging, where no user is logged in - default to davema
        $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 4 ;

        $res = $this->db_object->doQuery("insert into system_log (time, user_id, message) values (NOW(), '$user_id', '$data');");

        if (!$res)
        {
            $this->last_error = "Could not record log - ".$this->db_object->lasterror();
 
            return false; // Bad Query
        }
        else
        {
            return true;
        }
    }

    //-----------------------------------------------------------------------------------------
    // Return the last error (debugging)
    //-----------------------------------------------------------------------------------------
    function lasterror()
    {
        return $this->last_error;
    }
}

?>
