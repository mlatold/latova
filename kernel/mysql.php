<?php if(!defined('LAT')) die("Access Denied.");

class kernel_mysql
{
    //   SQL Initalize
    // +-------------------------+
    // Connect the database and load up the cache

    function initialize()
    {
        $persistent = false;

        if(!function_exists("mysql_connect") && !function_exists("mysql_pconnect"))
        {
            $this->lat->core->panic("MySQL modules are disabled on this server.", "1. Check if they exist and are enabled\n2. Try restarting your server\n3. Reinstall PHP, Apache, and/or MySQL.");
        }

        // What kind of connection is wanted?
        if($persistent)
        {
            $this->connection = @mysql_pconnect($this->lat->config['SQL_HOST'], $this->lat->config['SQL_USER'], $this->lat->config['SQL_PASS']);
        }
        else
        {
            $this->connection = @mysql_connect($this->lat->config['SQL_HOST'], $this->lat->config['SQL_USER'], $this->lat->config['SQL_PASS']);
        }

        // No server connection
        if(!$this->connection)
        {
            $this->lat->core->panic("Could not connect to the SQL server.", "1. Verify your mysql host, username and password in config.php.\n3. It might just be starting up, wait a minute and then refresh.\n4. MySQL may not be installed or mis-configured.");
        }

        // No database connection
        if(!@mysql_select_db($this->lat->config['SQL_DATA']))
        {
            $this->lat->core->panic("Could not select a database.", "1. Verify the database name in config.php.\n2. You may be connecting to a server where your latova database does not exist.");
        }

        unset($this->lat->config['SQL_HOST']);
        unset($this->lat->config['SQL_USER']);
        unset($this->lat->config['SQL_PASS']);

        // Load everything from the cache
        $query_cache = array("select" => "label, autoload, data, cache",
                             "from"   => "kernel_cache");

        while($fetch_cache = $this->query($query_cache))
        {
            $this->raw_cache[$fetch_cache['label']] = $fetch_cache;

            if($fetch_cache['autoload'])
            {
                $this->lat->core->load_cache($fetch_cache['label']);
            }
        }
    }


    //   Version
    // +-------------------------+
    // Return sql version

    function version()
    {
        return mysql_get_server_info();
    }


    //   Kill Query
    // +-------------------------+
    // Get rid of a query record

    function kill($query="")
    {
        $serial_query = md5(serialize($query));
        unset($this->sql_hash[$serial_query]);
    }


    //   Run Query
    // +-------------------------+
    // Our super function for running queries. It takes its variables from an incoming array and makes it into a query.

    function query($query="", $no_fetch=false)
    {
        $serial_query = md5(serialize($query));

        if(is_resource($this->sql_hash[$serial_query]) && !$query['no_save'])
        {
            return mysql_fetch_assoc($this->sql_hash[$serial_query]);
        }

        if($this->debug && ($query['update'] || $query['delete'] || $query['insert'] || $query['minsert'] || $query['replace'] || $query['mreplace']))
        {
            return;
        }

        // No query
        if(empty($query))
        {
            $this->lat->core->panic("A database query was attempted but no data was sent.", "1. It is the result of a coding error. If you are in development, check your code and debug.\n2. If you are not in development, perhaps it is the result of a modification you installed and a mistake by that coder. Try disabling modifications to determine the source of the problem and report it to the creator.\n3. Maybe you dislike databases and take pleasure in sending them empty queries.");
        }

        $this->last_query = $query;

        // Manual query mode. Skips construction from array and just takes value from there.
        // Good for when the contructor doesn't work out for a weird query.
        if(!is_array($query))
        {
            $contruct = $query;
        }
        // Use the array query contructor instead
        else
        {
            // SELECT
            if($query['select'] || $query['user'])
            {
                if(!empty($query['user']))
                {
                    if(!is_array($query['user']))
                    {
                        $query['user'] = array($query['user']);
                    }

                    foreach($query['user'] as $userf)
                    {
                        $userf = explode(" ", $userf);
                        $user_fetch = explode(",", $this->lat->cache['storage']['user_fetch']);
                        $userq = array();

                        foreach($user_fetch as $uf)
                        {
                            $userq[] = "{$userf[0]}.{$uf} as {$userf[1]}{$uf}";
                        }

                        $userq = implode(", ", $userq);

                        if(!empty($query['select']))
                        {
                            $query['select'] = $query['select'].", ".$userq;
                        }
                        else
                        {
                            $query['select'] .= $userq;
                        }
                    }
                }

                $construct = "SELECT {$query['select']} FROM {$this->lat->config['SQL_PREF']}{$query['from']}";
            }
            // UPDATE
            elseif($query['update'])
            {
                if($query['low'])
                {
                    $construct = "UPDATE LOW_PRIORITY {$low}{$this->lat->config['SQL_PREF']}{$query['update']}";
                }
                else
                {
                    $construct = "UPDATE {$low}{$this->lat->config['SQL_PREF']}{$query['update']}";
                }
            }
            // DELETE
            elseif($query['delete'])
            {
                $construct = "DELETE FROM {$this->lat->config['SQL_PREF']}{$query['delete']}";
            }
            // INSERT
            elseif($query['insert'])
            {
                $insert = $this->parse_insert($query['data']);
                $construct = "INSERT INTO {$this->lat->config['SQL_PREF']}{$query['insert']} ({$insert['name']}) VALUES ({$insert['data']})";
            }
            // PARSE INSERT
            elseif($query['pinsert'])
            {
                $query['data'] = "(".implode("), (", $query['data']).")";
                $construct = "INSERT INTO {$this->lat->config['SQL_PREF']}{$query['pinsert']} ({$query['name']}) VALUES {$query['data']}";
            }
            // REPLACE
            elseif($query['replace'])
            {
                $query['data'] = $this->parse_insert($query['data']);
                $construct = "REPLACE INTO {$this->lat->config['SQL_PREF']}{$query['replace']} ({$query['data']['name']}) VALUES ({$query['data']['data']})";
            }
            // PARSE REPLACE
            elseif($query['preplace'])
            {
                $query['data'] = "(".implode("), (", $query['data']).")";
                $construct = "REPLACE INTO {$this->lat->config['SQL_PREF']}{$query['preplace']} ({$query['name']}) VALUES {$query['data']}";
            }

            // LEFT JOIN
            if($query['left'])
            {
                if(is_array($query['left']))
                {
                    foreach ($query['left'] as $left_join)
                        $construct .= " LEFT JOIN ".$this->lat->config['SQL_PREF'].$left_join;
                }
                else
                {
                    $construct .= " LEFT JOIN ".$this->lat->config['SQL_PREF'].$query['left'];
                }
            }

            // RIGHT JOIN
            if($query['right'])
            {
                if(is_array($query['right']))
                {
                    foreach ($query['right'] as $right_join)
                        $construct .= " RIGHT JOIN ".$this->lat->config['SQL_PREF'].$right_join;
                }
                else
                {
                    $construct .= " RIGHT JOIN ".$this->lat->config['SQL_PREF'].$query['right'];
                }
            }

            // INNER JOIN
            if($query['inner'])
            {
                if(is_array($query['inner']))
                {
                    foreach ($query['inner'] as $inner_join)
                        $construct .= " INNER JOIN ".$this->lat->config['SQL_PREF'].$inner_join;
                }
                else
                {
                    $construct .= " INNER JOIN ".$this->lat->config['SQL_PREF'].$query['inner'];
                }
            }

            // SET
            if($query['set'])
            {
                if(is_array($query['set']))
                {
                    foreach($query['set'] as $col => $val)
                    {
                        if(substr($col, -1) == "=")
                        {
                            $set[] = $col.$val;
                        }
                        else
                        {
                            $set[] = "{$col}='{$val}'";
                        }
                    }
                    $construct .= " SET ".implode(", ", $set);
                }
                else
                {
                    $construct .= " SET ".$query['set'];
                }
            }
            // WHERE
            if($query['where'])
            {
                $construct .= " WHERE ".$query['where'];
            }
            // GROUP BY
            if($query['group'])
            {
                $construct .= " GROUP BY ".$query['group'];
            }
            // HAVING
            if($query['having'])
            {
                $construct .= " HAVING ".$query['having'];
            }
            // ORDER BY
            if($query['order'])
            {
                $construct .= " ORDER BY ".$query['order'];
            }
            // LIMIT
            if($query['limit'])
            {
                $construct .= " LIMIT ".$query['limit'];
            }
        }

        // SHUTDOWN
        if($query['shutdown'])
        {
            $this->shutdown[] = $construct;
            $this->sql_out[]  = "<b><span style='color:gray'>[SHUTDOWN] ".htmlspecialchars($construct)."</span></b>";
            return;
        }

        // Start Timer
        $sql_start = $this->lat->core->timer();

        // Execute Query
        $this->last_sql = mysql_query($construct, $this->connection);

        // End timer
        $sql_end = $this->lat->core->timer();
        $sql_time = $sql_end - $sql_start;

        // Query failed!
        if (!$this->last_sql)
        {
            if(!$query['no_error'])
            {
                $sys_error = "A database query returned an error.";

                if(SECURITY_IP == $_SERVER['REMOTE_ADDR'] || SECURITY_IP == 1 || (SECURITY_IP == 0 && $this->lat->user['group']['superadmin']))
                {
                    $sys_error .= "\n\nQuery: ".htmlspecialchars($construct)."\n\nError Details: ".mysql_error();
                }

                $this->lat->core->panic($sys_error, "1. If you are coding a module for Latova, check your SQL syntax.\n2. If you have any new modifications installed, you may want to consider disabling them to determine if there is any incompatibility.\n3. If this query appears to be a glitch from a faulty data input that resides in a latova module or kernel, report it to https://github.com/mikelat/latova so it can be repaired in a future update.\n4. Try to 'repair' the tables in your database. Ask your web hosting provider if you do not understand the process of doing this.\n5. Contact your web hosting provider to see if there is a problem with the server.");
            }
            else
            {
                $this->sql_error = "Query: ".htmlspecialchars($construct)."<br />Error Details: ".mysql_error();
            }
        }

        // SQL Stats
        $this->sql_queries++;
        $this->sql_time += $sql_time;
        $this->sql_out[] = "<b>".htmlspecialchars($construct)."</b> (executed in <b>".$this->lat->parse->number($sql_time, 5)."</b> seconds).";
        $this->sql_hash[$serial_query] = $this->last_sql;

        if($query['select'] && !$no_fetch)
        {
            return mysql_fetch_assoc($this->last_sql);
        }
        elseif($query['insert'])
        {
            return $this->last();
        }
        else
        {
            return $this->last_sql;
        }
    }


    // +-------------------------+
    //   Update Cache
    // +-------------------------+
    // Cache some data in the database for quicker access for later

    function cache($name="", $no_error=false)
    {
        // We're updating everything :o
        if($name == "")
        {
            foreach($this->raw_cache as $c)
            {
                if($c['data'] != "")
                {
                    $this->cache($c['label'], $no_error);
                }
            }
            return;
        }
        elseif(empty($this->raw_cache[$name]))
        {
            $this->lat->core->panic("A cache update was attempted, but the cache item either doesn't exist or there are no instructions on how to update that cache item\n\nThe cache item's name is: ".$name, "1. This is a coding mistake. If you are developing something for latova, check your spelling and if you've properly assigned the cache update instructions.\n2. If you are not developing anything, it is likely the fault of a coding error on module you have installed. You may want to report the cache item that failed and/or look for updates to your modifications.\n3. Another common cause is that the query to recache is either non-existant or not serialized properly.");
        }

        $start = $this->lat->core->timer();
        $data = unserialize($this->raw_cache[$name]['cache']);

        if($data['query']['where'])
        {
            $data['query']['where'] .= " AND 1=1";
        }
        else
        {
            $data['query']['where'] = "1=1";
        }

        $this->kill($data['query']);

        // We're caching more than one value
        if(!$data['value'])
        {
            while ($result = $this->query($data['query']))
            {
                foreach ($result as $secondary_key => $value)
                {
                    $serial[$result[$data['key']]][$secondary_key] = $value;
                }
                $val++;
            }
        }
        // We're only caching one value
        else
        {
            while($result = $this->query($data['query']))
            {
                $serial[$result[$data['key']]] = $result[$data['value']];
                $val++;
            }
        }

        // Update into the cache
        $update = serialize($serial);
        $this->lat->cache[$name] = $serial;
        $this->raw_cache[$name]['data'] = $update;

        $this->query(array("update"   => "kernel_cache",
                           "set"      => array("data" => $this->lat->parse->sql_text($update)),
                           "where"    => "label='{$name}'",
                           "no_error" => $no_error,
                           "shutdown" => 1));

        // Usually invoked from CP
        if($no_error)
        {
            if($this->last_sql)
            {
                $end = $this->lat->core->timer();
                $time = $end - $start;
                $this->cache_debug_time += $time;
                $result = "<span class=\"pass\">Success ({$val} rows cached, completed in ".$this->lat->parse->number($time, 5)."s)</span>";
            }
            else
            {
                $result = "<span class=\"fail\">Failure<br />{$this->sql_error}</span>";
            }


            $this->cache_debug[] = "<b>{$this->raw_cache[$name]['label']}</b>: ".$result;
        }
    }


    // +-------------------------+
    //   Number of Rows
    // +-------------------------+
    // Count the number of rows returned by a query.

    function num($query="")
    {
        if(empty($query))
        {
            $query = $this->last_sql;
        }
        else
        {
            $serial_query = md5(serialize($query));
            if(!is_resource($this->sql_hash[$serial_query]))
            {
                $this->query($query, true);
            }
            $query = $this->sql_hash[$serial_query];
        }


        return mysql_num_rows($query);
    }


    // +-------------------------+
    //   Last ID
    // +-------------------------+
    // Get the ID from a inserted row.

    function last()
    {
        return mysql_insert_id($this->connection);
    }


    // +-------------------------+
    //   Free Memory
    // +-------------------------+
    // Frees up memory from a query,

    function free($query="")
    {
        if(!$query)
        {
            $query = $this->last_sql;
        }

        mysql_free_result($query);
    }


    // +-------------------------+
    //   Parse insert
    // +-------------------------+
    // Takes names and values and parses them into a insert query format.

    function parse_insert($insert)
    {
        foreach($insert as $name => $value)
        {
            $return['name'][] = "`{$name}`";
            $return['data'][] = "'{$value}'";
        }

        return array("name" => implode(", ", $return['name']),
                     "data" => implode(", ", $return['data']));
    }


    // +-------------------------+
    //   Execute Shutdown
    // +-------------------------+
    // Run shutdown queries to finish off execution of the entire script.

    function exec_shutdown()
    {
        // Do we have shutdown queries?
        if(!empty($this->shutdown))
        {
            foreach($this->shutdown as $query)
            {
                $check_query = mysql_query($query, $this->connection);

                // Query failed! This will only display in debug mode.
                if (!$check_query)
                {
                    $sys_error = "A shutdown database query returned an error.";

                    if(SECURITY_IP == $_SERVER['REMOTE_ADDR'] || SECURITY_IP == 1 || (SECURITY_IP == 0 && $this->lat->user['group']['superadmin']))
                    {
                        $sys_error .= "\n\nQuery: ".htmlspecialchars($query)."\n\nError Details: ".mysql_error();
                    }

                    $this->lat->core->panic($sys_error, "1. If you are coding a module for Latova, check your SQL syntax.\n2. If you have any new modifications installed, you may want to consider disabling them to determine if there is any incompatibility.\n3. If this query appears to be a glitch from a faulty data input that resides in a latova module or kernel, report it to https://github.com/mikelat/latova so it can be repaired in a future update.\n4. Try to 'repair' the tables in your database. Ask your web hosting provider if you do not understand the process of doing this.\n5. Contact your web hosting provider to see if there is a problem with the server.");
                }
            }
        }
    }
}
?>
