<?php

/**
 * Dump data from MySQL database
 *
 * @name    MySQLDump
 * @author  Sergey Shilko <imp_on@softhome.net>, 
 *          based on code by Marcus Vinícius
 * @version 1.1 2007-04-20
 * @example
 *
 * $dump = new MySQLDump();
 * print $dump->dumpDatabase("mydb",false,false);
 *
 */
set_time_limit(720); #720sec
session_cache_expire(720);   #720 min expire

class MySQLDump {

    /**
     * Dump data and structure from MySQL database
     *
     * @param string $database
     * @return string
     */
    function dumpDatabase($database, $nodata = false, $nostruct = false) {

        // Set content-type and charset
        header ('Content-Type: text/html; charset=utf-8');
        // Connect to database
        $db = @mysql_select_db($database);

        if (!empty($db)) {

            // Get all table names from database
            $c = 0;
            $result = mysql_list_tables($database);
            for ($x = 0; $x < mysql_num_rows($result); $x++) {
                $table = mysql_tablename($result, $x);
                if (!empty($table)) {
                    $arr_tables[$c] = mysql_tablename($result, $x);
                    $c++;
                }
            }
            // List tables
            $dump = '';

            $dump .= "-- ilchClan SQL Backup ".date('Y-m-d_h-m-s')."\n";
            $dump .= '-- MySQL DATABASE DUMPER. Copyright Sergey Shilko &reg;\n\n' . "\n";
            $dump .= "-- \n\n";

            for ($y = 0; $y < count($arr_tables); $y++) {

                // DB Table name
                $table = $arr_tables[$y];
                if ($nostruct == false) {

                    // Structure Header
                    $structure .= "-- ------------------------------------------------ \n";
                    $structure .= "-- Table structure for table `{$table}` started >>> \n";

                    // Dump Structure
                    $structure .= "DROP TABLE IF EXISTS `{$table}`; \n";
                    $structure .= "CREATE TABLE `{$table}` (\n";
                    $result = mysql_db_query($database, "SHOW FIELDS FROM `{$table}`");
                    while ($row = mysql_fetch_object($result)) {

                        $structure .= "  `{$row->Field}` {$row->Type}";
                        if ($row->Default != 'CURRENT_TIMESTAMP') {
                            $structure .= (!empty($row->Default)) ? " DEFAULT '{$row->Default}'" : false;
                        } else {
                            $structure .= (!empty($row->Default)) ? " DEFAULT {$row->Default}" : false;
                        }
                        $structure .= ($row->Null != "YES") ? " NOT NULL" : false;
                        $structure .= (!empty($row->Extra)) ? " {$row->Extra}" : false;
                        $structure .= ",\n";
                    }

                    $structure = ereg_replace(",\n$", "", $structure);

                    // Save all Column Indexes in array
                    unset($index);
                    $result = mysql_db_query($database, "SHOW KEYS FROM `{$table}`");
                    while ($row = mysql_fetch_object($result)) {

                        if (($row->Key_name == 'PRIMARY') AND ($row->Index_type == 'BTREE')) {
                            $index['PRIMARY'][$row->Key_name] = $row->Column_name;
                        }

                        if (($row->Key_name != 'PRIMARY') AND ($row->Non_unique == '0') AND ($row->Index_type == 'BTREE')) {
                            $index['UNIQUE'][$row->Key_name] = $row->Column_name;
                        }

                        if (($row->Key_name != 'PRIMARY') AND ($row->Non_unique == '1') AND ($row->Index_type == 'BTREE')) {
                            $index['INDEX'][$row->Key_name] = $row->Column_name;
                        }

                        if (($row->Key_name != 'PRIMARY') AND ($row->Non_unique == '1') AND ($row->Index_type == 'FULLTEXT')) {
                            $index['FULLTEXT'][$row->Key_name] = $row->Column_name;
                        }
                    }


                    // Return all Column Indexes of array
                    if (is_array($index)) {
                        foreach ($index as $xy => $columns) {

                            $structure .= ",\n";

                            $c = 0;
                            foreach ($columns as $column_key => $column_name) {

                                $c++;

                                $structure .= ($xy == "PRIMARY") ? "  PRIMARY KEY  (`{$column_name}`)" : false;
                                $structure .= ($xy == "UNIQUE") ? "  UNIQUE KEY `{$column_key}` (`{$column_name}`)" : false;
                                $structure .= ($xy == "INDEX") ? "  KEY `{$column_key}` (`{$column_name}`)" : false;
                                $structure .= ($xy == "FULLTEXT") ? "  FULLTEXT `{$column_key}` (`{$column_name}`)" : false;

                                $structure .= ($c < (count($index[$xy]))) ? ",\n" : false;
                            }
                        }
                    }

                    $structure .= "\n);\n\n";
                    $structure .= "-- Table structure for table `{$table}` finished <<< \n";
                    $structure .= "-- ------------------------------------------------- \n";
                }

                // Dump data
                if ($nodata == false) {

                    $structure .= " \n\n";

                    $result = mysql_query("SELECT * FROM `$table`");
                    $num_rows = mysql_num_rows($result);
                    $num_fields = mysql_num_fields($result);

                    $data .= "-- -------------------------------------------- \n";
                    $data .= "-- Dumping data for table `$table` started >>> \n";

                    for ($i = 0; $i < $num_rows; $i++) {

                        $row = mysql_fetch_object($result);
                        $data .= "INSERT INTO `$table` (";

                        // Field names
                        for ($x = 0; $x < $num_fields; $x++) {

                            $field_name = mysql_field_name($result, $x);

                            $data .= "`{$field_name}`";
                            $data .= ($x < ($num_fields - 1)) ? ", " : false;
                        }

                        $data .= ") VALUES (";

                        // Values
                        for ($x = 0; $x < $num_fields; $x++) {
                            $field_name = mysql_field_name($result, $x);

                            $data .= "'" . str_replace('\"', '"', mysql_real_escape_string($row->$field_name)) . "'";
                            $data .= ($x < ($num_fields - 1)) ? ", " : false;
                        }

                        $data.= ");\n";
                    }
                    $data .= "-- Dumping data for table `$table` finished <<< \n";
                    $data .= "-- -------------------------------------------- \n\n";

                    $data.= "\n";
                }
            }
            $dump .= $structure . $data;
        }
        return $dump;
    }

    function sendAttachFile($data, $contenttype = 'text/html', $filename = 'mysqldump.sql') {
        $path = getcwd();
        $handle = fopen($path . '/' . date('mdY') . "$filename", 'w');
        fwrite($handle, $data);
        fclose($handle);

        header("Content-type: $contenttype");
        header("Content-Disposition: attachment; filename=" . date('mdY') . $filename);
        echo ($data);
    }

    function sendAttachFileGzip($data, $filename = 'mysqldump.sql.gz') {
        $path = getcwd();
        $data = gzencode($data, 9);
        $handle = fopen($path . '/' . date('mdY') . "$filename", 'w');
        fwrite($handle, $data);
        fclose($handle);
        header("Content-type: application/x-gzip");
        header("Content-Disposition: attachment; filename=" . date('mdY') . $filename);
        echo($data);
    }

}

?> 