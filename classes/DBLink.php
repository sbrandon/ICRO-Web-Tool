<?php

//-------------------------------------------------------------------------------------------------
// DBLink.php
//
// Utility class to connect to the DB and run queries, etc...
// Uses in-built php mysql classes
//
// Based on code by Peter Zeidman (http://www.intranetjournal.com/php-cms/)
//
// Dave Masterson, Sept 2009
// Updated: Stephen Brandon, July 2024
//-------------------------------------------------------------------------------------------------

class DBLink
{
    var $db_object;    // The connection to the DB
    var $last_error;   // Store the last error, for logging/debugging

    //-----------------------------------------------------------------------------------------
    // Creates a new link to the DB
    // Returns true or false, depending on sucess or failure
    //-----------------------------------------------------------------------------------------
    function __construct()
    {
        // Connection details for the DB
        require('../icro-config/config-live.php');

        // Initialise the error record
        $this->last_error = 'No Error';

        // Connect to DB  
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        $this->db_object = mysqli_connect($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

        if (mysqli_connect_errno())
        {
            // Connection Failure
            echo "Failed to connect to MySQL: " . mysqli_connect_error();
            $this->last_error = mysqli_error();
            die();
        }

        // callback to destructor
        register_shutdown_function(array(&$this, 'close'));
    }

    //-----------------------------------------------------------------------------------------
    // Execute a database query, that expects no response (insert, update, replace, etc...)
    // Returns true or false, depending on sucess or failure
    //-----------------------------------------------------------------------------------------
    function doQuery($query) 
    {
        $this->theQuery = $query;
        $result = mysqli_query($this->db_object, $query);
        
        if (!$result)
        {
            $this->last_error = mysqli_error();
            return false;
        }
        else
        {
            return true;
        }
    }

    //-----------------------------------------------------------------------------------------
    // Execute a database query, return the result set as an array
    // Returns an Array or false, depending on success or failure
    //-----------------------------------------------------------------------------------------
    function fetchQuery($query) 
    {
        $result = mysqli_query($this->db_object, $query);

        if (!$result)
        {
            $this->last_error = mysqli_error();
            return false; // Bad Query
        }
        else
        {
            $res_array = [];
            for ($i=0; $i < mysqli_num_rows($result); $i++)
            {
                $res_array[$i] = mysqli_fetch_array($result,MYSQLI_BOTH);
            }
            
            // Return a matrix containing all the rows
            return $res_array;
        }
    }

    //-----------------------------------------------------------------------------------------
    // Close the connection 
    //-----------------------------------------------------------------------------------------
    function close() 
    {
        mysqli_close($this->db_object);
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
