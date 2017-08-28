<?php
/*
 * This file will define method that Pre-Defined by the Admin
 */
namespace M2U\Models\DB;

use M2U\Models\DB\Connect;
use mysqli;

class Main
{

    private $connect;

    /*
     * Define table name
     */

    const OPTIONS = 'options';
    const BILLS = 'bills';

    /*
     * Establish Connection to Database Server
     */

    public function __construct()
    {
        $this->connect = new Connect();
    }

    public function get_info($table, $what, $key = '', $value = '')
    {
        if (empty($key) && empty($value)) {
            $sql = "SELECT " . $what . " FROM " . $this->connect->dbprefix . $table . " WHERE 1";
        } else {
            $sql = "SELECT " . $what . " FROM " . $this->connect->dbprefix . $table . " WHERE " . $key . '="' . $value . '"';
        }
        $result = $this->connect->conn->query($sql);

        $id = $result->fetch_assoc();

        if ($what === '*' && !empty($id)) {
            return $id;
        } else if (empty($id[$what])) {
            return '';
        } else {
            return $id[$what];
        }
    }

    private function refValues($arr)
    {
        if (strnatcmp(phpversion(), '5.3') >= 0) { //Reference is required for PHP 5.3+
            $refs = array();
            foreach ($arr as $key => $value)
                $refs[$key] = &$arr[$key];
            return $refs;
        }
        return $arr;
    }

    public function set_info($table, $key = array(), $value = array())
    {
        $sql = "INSERT INTO " . $this->connect->dbprefix . $table;

        $sql .= ' (`' . implode('`,`', $key) . '`)';

        $sql .= ' VALUES';

        /*
         * Convert value array to "?" and
         * Determine how many bind variable
         */
        $valuearray = array();
        $bindvariable = '';
        foreach ($value as $data) {
            $valuearray[] = '?';
            $bindvariable .= 's';
        }
        $sql .= ' (' . implode(',', $valuearray) . ')';

        if ($stmt = $this->connect->conn->prepare($sql)) {
            /*
             *  Bind the variables to the parameter as strings.
             *  Put the 'sss' value to the beginning of array
             */

            array_unshift($value, $bindvariable);
            call_user_func_array(array($stmt, "bind_param"), $this->refValues($value));

            // Execute the statement.
            $stmt->execute();

            // Close the prepared statement.
            $stmt->close();
            return true;
        } else {
            return false;
        }
    }

    public function update_order($data)
    {
        $sql = "UPDATE " . $this->connect->dbprefix . self::BILLS;
        $sql .= ' SET ';
        $count = 0;
        $sizearray = sizeof($data);
        foreach ($data as $key => $value) {
            if (($sizearray - 1) === $count) {
                $sql .= '`' . $key . "` = '" . $value . "'";
            } else {
                $sql .= '`' . $key . "` = '" . $value . "', ";
            }
            $count++;
        }

        $sql .= " WHERE AcctId = '" . $data['AcctId'] . "'";
        $result = $this->connect->conn->query($sql);
        return $result;
    }

    public function check_urlid_for_duplicate($urlid)
    {
        $query = $this->get_info(self::BILLS, 'URLId', 'URLId', $urlid);
        if (empty($query)) {
            return true;
        } else {
            return false;
        }
    }

    public function set_bill($order_info, $amount, $name, $email, $phone, $urlid, $redirectUrl, $callbackUrl, $time)
    {
        $key = array('OrderInfo', 'Amt', 'URLId', 'Name', 'Email', 'Mobile', 'RedirectUrl', 'CallbackUrl', 'Timestamp');
        $value = array($order_info, $amount, $urlid, $name, $email, $phone, $redirectUrl, $callbackUrl, $time);
        return $this->set_info(self::BILLS, $key, $value);
    }

    public function get_bill($urlid)
    {
        $sql = $this->get_info(self::BILLS, '*', 'URLId', $urlid);
        if (!empty($sql)) {
            return $sql;
        }
        return false;
    }

    public function get_urlid($AcctId)
    {
        $sql = $this->get_info(self::BILLS, '*', 'AcctId', $AcctId);
        if (!empty($sql)) {
            return $sql;
        }
        return false;
    }
}

/*
 * One variable/instantiation for usage of entire website
 * Prevent the need of multiple db class instantiation
 */
$db = new Main();
