<?php

function get_route()
{
    $str = '';
    /*
     * $_SERVER['SCRIPT_NAME'] will provide full sub-directory path after domain until "index.php" file.
     * Example: www.google.com/aa/bb/index.php => It will provide /aa/bb/index.php
     * 
     * Preg Replace: Only allow A-Z, a-z, 0,9 and special character "/" and "."
     */
    $script_name = explode('/', preg_replace("/[^a-zA-Z0-9\/\.]+/", "", $_SERVER['SCRIPT_NAME']));

    /*
     * Find array index until found the script name.
     * Save the array index number at variable $i
     */
    for ($i = 0; $i < sizeof($script_name); $i++) {
        if (strtolower($script_name[$i]) === 'index.php') {
            /*
             * Set the maximum $i value to strip string from Request URI
             */
            break;
        }
    }

    /*
     * $_SERVER['REQUEST_URI'] will provide full sub-directory path after domain.
     * Example: www.google.com/aa/bb/index.php/aa/cc => It will provide /aa/bb/index.php/aa/cc
     * ______________________
     * 
     * Assuming www.google.com has index.php and mode rewrite enabled
     * Example: www.google.com/aa => It will provide /aa
     * 
     * Preg Replace: Only allow A-Z, a-z, 0,9 and special character "/" and "."
     */
    $request_uri = explode('/', preg_replace("/[^a-zA-Z0-9\/\.]+/", "", $_SERVER['REQUEST_URI']));

    /*
     * Set the array index of $request_uri to match with the current directory name of "index.php"
     */
    for ($a = 0; $a < sizeof($request_uri); $a++) {
        if (strtolower($request_uri[$a]) === $script_name[($i - 1)]) {
            /*
             * If there is presence "index.php" name in request uri (in-case mod rewrite not enabled)
             * +1 the counter
             */
            if (strtolower($request_uri[($a + 1)]) === 'index.php') {
                $a++;
            }

            /*
             * +1 the counter to get directory or path after the current script directory folder or after "index.php"
             */
            $a++;

            /*
             * Iterate the loops until the end of the route
             */
            for (; $a < sizeof($request_uri); $a++) {
                if ($a === (sizeof($request_uri) - 1)) {
                    $str .= $request_uri[($a)];
                } else {
                    $str .= $request_uri[($a)] . '/';
                }
            }
            break;
        }
    }

    return $str;
}
