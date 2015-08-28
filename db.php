<?php

use \PDO, PDOException;

class DB
{
    protected $pdo;
    protected $selected;

    /**
     * @param $host
     * @param $login
     * @param $pass
     * @param $dbName
     */
    public function __construct($host, $login, $pass, $dbName)
    {
        $this->pdo = new PDO("mysql:host=$host;dbname=$dbName;",$login,$pass );
    }

    public function __get($property)
    {
        if ($property == "pdo") {
            if (property_exists($this, $property)) {
                echo "CONNECTION STATUS :";
                return $this - $property;
            } else {
                echo "CONNECTION ERROR";
            }
        } else {
            echo "ACCESS DENIED";
        }
    }

    public function sendQuery($query)
    {

    }

    /**
     * @param $table table
     * @param $select selector
     * @param $row row
     * @param $order order
     * @param $rev DESC
     * @param $lim int
     */

    public function prepare($sql, $arr = array())
    {
        $query = $this->pdo->prepare($sql);
        $query->execute($arr);
        return $query;
    }

    public function select($table, $select = false, $row = false, $order = false, $rev = false, $lim = false)
    {

        $sql = "SELECT ";
        if(!$select){
            $sql .= "* ";
        }elseif(is_array($select)) {
            $comma = "";
            foreach($select as $key => $val){
                $sql .= $comma."`$val`";
                $comma = ", ";
            };
            $sql .= " ";
        }else {
            $sql .= "`$select` ";
        }
        $sql .="FROM `$table` ";
        if($row){
            $arr = [];
            $comma = "";
            $sql .="WHERE ";
            foreach($row as $key => $val) {
                $sql .= $comma . "`$key` = ? ";
                $comma = "AND ";
                $arr[] = $val;
            };
        }
        if($order) {
            $sql .= "ORDER BY `$order` ";
        }
        if($rev){
            $sql .= "DESC ";
        }
        if($lim) {
            $sql .= "LIMIT $lim";
        }


        $query = $this->prepare($sql, $arr);
        $arr = $query->fetchAll(PDO::FETCH_ASSOC);
        return $arr;

    }

    public function insert($table, $exp = array())
    {
        $arr = [];
        $sql = "INSERT INTO `$table` (";
        $comma = "";
        foreach($exp as $key => $val){
            $sql .=$comma."`$key`";
            $comma = ',';
        }
        $comma = "";
        $sql .=") VALUE (";
        foreach($exp as $key => $val){
            $sql .=$comma." ?";
            $comma = ',';
            $arr[] = $val;
        }
        $sql.=")";
        $this->prepare($sql, $arr);
        //$result = $this->pdo->query($sql);
        //if($result){
        //    echo "Job's done!";
        //}else {
        //    echo "Ups...something go wrong!";
        //}
    }

    public function update($table, $exp = array(),$where = false, $lim = false)
    {
        $sql = "UPDATE `$table` SET ";
        $arr = [];
        if($exp){
            $comma = '';
            foreach($exp as $key => $val){
                $sql .= $comma."`$key`= ?";
                $comma = ' , ';
                $arr[] = $val;
            }
            $comma = '';
            if(is_array($where)){
                $sql .= " WHERE ";
                foreach($where as $key => $val){
                    $sql .= $comma."`$key`= ?";
                    $comma = ' AND ';
                    $arr[] = $val;
                }
            }
        }
        if($lim){
            $sql .= " LIMIT $lim";
        }
        $this->prepare($sql, $arr);
    }
}

;
