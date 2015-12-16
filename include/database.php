<?php

require_once 'DatabaseException.php';

class Database
{
    private $con = null;
    private $options = array();

    function __construct(array $options = []){
        if (!isset($options['host'])) $options['host']='localhost';
        if (!isset($options['user'])) $options['user']='root';
        if (!isset($options['pass'])) $options['pass']='';
        if (!isset($options['charset'])) $options['charset']='utf8';
        if (!isset($options['throwable'])) $options['throwable'] = TRUE;

        $this->options = $options;
    }

    function __destruct(){
        $this->close();
    }

    function close(){
        if ($this->con!=NULL) mysqli_close($this->con);
        $this->con=NULL;
    }

    function connect(){
        if (!($this->con=@mysqli_connect($this->options['host'],$this->options['user'],$this->options['pass'])))
            return $this->handleError();
        if (isset($this->options['db']) && !$this->selectDB($this->options['db'])) {
            $this->close();
            return $this->handleError();
        }
        return true;
    }

    function selectDB($db) {
        if (!mysqli_select_db($this->con, $db)) return $this->handleError();
        mysqli_set_charset($this->con, $this->options['charset']);
        return true;
    }

    function query($sql){
        if (!$this->con) return $this->handleError();
        $q = mysqli_query($this->con, $sql);
        return $q === FALSE ? $this->handleError() : $q;
    }

    function lastInsertId(){
        return mysqli_insert_id($this->con);
    }

    function lastErrorCode(){
        return $this->con ? mysqli_errno($this->con) : mysqli_connect_errno();
    }

    function lastError(){
        return $this->con ? mysqli_error($this->con) : mysqli_connect_error();
    }

    function handleError(){
        if ($this->options['throwable']){
            throw new DatabaseException($this->lastError(), $this->lastErrorCode());
        }
        else return FALSE;
    }

    function fetch($sql, $count = -1) {
        $r = $this->query($sql);
        if ($r === FALSE) return $this->handleError();
        $f = array();
        while ($count-- && $ret = mysqli_fetch_assoc($r))
            $f[] = $ret;
        mysqli_free_result($r);
        return $f;
    }

    function insert($table, array $values){
        $fldList =''; $valList='';
        foreach ($values as $field => $value) {
            $fldList .= '`' . $field . '`,';
            $valList .=  $this->escapeString($value) . ',';
        }

        $fldList = rtrim($fldList, ',');
        $valList = rtrim($valList, ',');
        $sql = 'INSERT INTO `' . $table . '` (' . $fldList . ') VALUES (' . $valList . ')';
        return $this->query($sql);
    }

    function update($table, array $values, array $where){
        $list = '';
        foreach ($values as $field => $value) {
            $list .= '`' . $field . '`=' . $this->escapeString($value) . ',';
        }

        $list = rtrim($list, ',');

        $listW = '';
        foreach ($where as $field => $value) {
            $listW .= '`' . $field . '`=' . $this->escapeString($value) . ' AND ';
        }
        if (substr($listW, -4) == 'AND ') $listW = substr($listW, 0, -4);
        $sql = 'UPDATE `' . $table . '` SET ' . $list . ' WHERE ' . $listW;
        return $this->query($sql);
    }

    function delete($table, array $where){
        $listW = '';
        foreach ($where as $field => $value) {
            $listW .= '`' . $field . '`=' . $this->escapeString($value) . ' AND ';
        }
        if (substr($listW, -4) == 'AND ') $listW = substr($listW, 0, -4);
        $sql = 'DELETE FROM `' . $table . '` WHERE ' . $listW;
        return $this->query($sql);
    }

    function escapeString($str){
        return strcasecmp($str, 'NULL') == 0 || $str === NULL ? 'NULL' : '\'' . mysqli_real_escape_string($this->con, $str) . '\'';
    }

    function transactionStart(){
        $this->query('START TRANSACTION');
    }

    function transactionCommit(){
        $this->query('COMMIT');
    }

    function transactionRollback(){
        $this->query('ROLLBACK');
    }

    function isConnected(){ return $this->con ? TRUE: FALSE; }

}
?>