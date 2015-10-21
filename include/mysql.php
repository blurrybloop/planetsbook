<?php
class mysql
{
   private $con = null;
   private $options = array();

   function __construct(array $options = array()){
       if (!isset($options['host'])) $options['host']='localhost';
       if (!isset($options['user'])) $options['user']='root';
       if (!isset($options['pass'])) $options['pass']='';
       if (!isset($options['charset'])) $options['charset']='utf8';
       $this->options = $options;
   }

   function __destruct(){
      $this->close();
   }

   function close(){
      if ($this->con!=null) mysqli_close($this->con);
      $this->con=null;
   }

   function connect(){
       if (!($this->con=mysqli_connect($this->options['host'],$this->options['user'],$this->options['pass'])))
            return false;
       if (isset($this->options['db']) && !$this->select_db($this->options['db']))
        {
            $this->close();
            return false;
        }
        return true;
   }

   function select_db($db) {
        if (!mysqli_select_db($this->con, $db)) return false;
		mysqli_set_charset($this->con, $this->options['charset']);
        return true;
   }

   function query($sql){
        return mysqli_query($this->con, $sql);
   }

   function last_insert_id(){
       return mysqli_insert_id($this->con);
   }

   function last_error($includeCode = TRUE){
       return ($includeCode ? mysqli_errno($this->con) : '') . ': ' . mysqli_error( $this->con);
   }

   function fetch($sql, $count = -1) {
       $r = $this->query($sql);
       if ($r === FALSE) return false;
       $f = array();
       while ($count-- && $ret = mysqli_fetch_assoc($r))
           $f[] = $ret;
       mysqli_free_result($r);
       return $f;
   }

}
?>