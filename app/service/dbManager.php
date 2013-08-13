<?php

/**************************************************/
/*********php-mysql database connection class******/
/*************an easy-to-use class when************/
/***********developing php-mysql web apps**********/
/************author - Gradinariu Bogdan***********/
/**************http://ihatemondays.ro**************/
/**************************************************/

class EasyPhpMysqlDb{
    private $user = null;
    private $pass = null;
    private $host = null;
    private $db = null;
	private $conn = null;
	private $error = null;

	//encoding flags
	private $encode_json = false;
	private $encode_xml = false;
	private $encode_array = false;
	private $encode_obj = true;

	//the xml tags name (in case the result should be returned as xml)
	private $xmlParentNodeName = "result_set";
	private $xmlChildNodeName = "result";

	//constructer
	public function __construct($user='root', $pass='', $db_url='localhost', $db=''){
        $this->user = $user;
		$this->pass = $pass;
        $this->host = $db_url;
        $this->db = $db;
    }

	//seters
	public function setUser($x){
		$this->user = $x;
	}

	public function setPass($x){
		$this->pass = $x;
	}
	public function setHost($x){
		$this->host = $x;
	}
	public function setDb($x){
		$this->db = $x;
	}
	public function setError($x){
		$this->error = $x;
	}
	public function setXML($bool){
		if($bool == true)
			{
				$this->resetEncodingFlags();
				$this->encode_xml = true;
			}
		else
			$this->encode_xml = false;
	}
	public function setJson($bool){
		if($bool == true)
			{
				$this->resetEncodingFlags();
				$this->encode_json = true;
			}
		else
			$this->encode_json = false;
	}
	public function setArray($bool){
		if($bool == true)
			{
				$this->resetEncodingFlags();
				$this->encode_array = true;
			}
		else
			$this->encode_array = false;
	}
	public function setObj($bool){
		if($bool == true)
			{
				$this->resetEncodingFlags();
				$this->encode_obj = true;
			}
		else
			$this->encode_obj = false;
	}
	public function setResultEncoding($type){
		switch($type){
			case "json":
				$this->setJson(true);
				break;
			case "array":
				$this->setArray(true);
				break;
			case "xml":
				$this->setXml(true);
				break;
			case "obj":
			case "stdObj":
			default:
				$this->setObj(true);
		}
	}


	//geters
	public function getUser(){
		return $this->user;
	}
	public function getPass(){
		return $this->pass;
	}
	public function getHost(){
		return $this->host;
	}
	public function getDb(){
		return $this->db;
	}
	public function getError(){//returns&resets the error field
		$aux = $this->error;
		$this->error = null;
		return $aux;
	}
	public function isJsonEncoded(){
		return $this->encode_json;
	}
	public function isXmlEncoded(){
		return $this->encode_xml;
	}
	public function isObjEncoded(){
		return $this->encode_obj;
	}
	public function isArrayEncoded(){
		return $this->encode_array;
	}
	public function getResultEncoding(){
		if($this->encode_array)
			return "array";
		if($this->encode_obj)
			return "obj";
		if($this->encode_json)
			return "json";
		if($this->encode_xml)
			return "xml";
		return "none";
	}
	public function getXmlParentNodeName(){
		return $this->xmlParentNodeName;
	}
	public function getXmlChildNodeName(){
		return $this->xmlChildNodeName;
	}

	public function setCharset($charset){
		//set connection to UTF-8
        mysql_set_charset($charset, $this->conn) or die($this->getError());
	}

    public function toString() {
        echo "user:".$this->user;
		echo '<br />';
        echo "pass:".$this->pass;
		echo '<br />';
        echo "host:".$this->host;
		echo '<br />';
        echo "error:".$this->error;
		echo '<br />';
    }

	//function for internal usage only
	//if you must,use with caution, because if no flag is set, your result will be NULL
	private function resetEncodingFlags(){
		$this->encode_json=false;
		$this->encode_array=false;
		$this->encode_obj=false;
		$this->encode_xml=false;
	}

	public function setXmlParentNodeName($name){
		$this->xmlParentNodeName = $name;
	}
	public function setXmlChildNodeName($name){
		$this->xmlChildNodeName = $name;
	}

	//it returns true if the connection is established
	//and false otherwise(in this case also setting the "error" field with the corresponding error)
	public function connect(){
		$this->conn = mysql_connect($this->host,$this->user,$this->pass);
		if (!$this->conn)
			{
				$this->setError(mysql_error());
				return false;
			}

		//set connection to UTF-8
        $this->setCharset("utf8");
		return true;
	}

	public function disconnect(){
		if(!$this->conn)
			{
				$this->setError("there is no active connection");
				return false;
			}
		@mysql_close($this->conn);
		$this->conn = null;
		return true;
	}

	public function insert($sql){
		if(!$this->conn)
			{
				$this->setError("there is no active connection");
				return false;
			}
		if(!$this->db)
			{
				$this->setError("no database specified");
				return false;
			}

		mysql_select_db($this->db, $this->conn);
		if(!mysql_query($sql,$this->conn))
			{
				$e = mysql_error();
				if(!$e)
					$e = "no rows were inserted";
				$this->setError($e);
				return false;
			}
		return true;
	}

	public function update($sql){
		if(!$this->conn)
			{
				$this->setError("there is no active connection");
				return false;
			}
		if(!$this->db)
			{
				$this->setError("no database specified");
				return false;
			}
		mysql_select_db($this->db, $this->conn);
		if(!mysql_query($sql))
			{
				$this->setError('no rows were updated');
				return false;
			}
		return true;
	}

	public function createTable($sql){
		if(!$this->conn)
			{
				$this->setError("there is no active connection");
				return false;
			}
		if(!$this->db)
			{
				$this->setError("no database specified");
				return false;
			}
		mysql_select_db($this->db, $this->conn);
		mysql_query($sql);
		return true;
	}

	public function createDb($sql){
		if(!$this->conn)
			{
				$this->setError("there is no active connection");
				return false;
			}
		mysql_query($sql,$this->conn);
		return true;
	}

	//returns an array of objects representing the table row
	//eg: result[i]->id accesses the id of the i-nth matched row
	public function select($sql){
		if(!$this->conn)
			{
				$this->setError("there is no active connection");
				return false;
			}
		if(!$this->db)
			{
				$this->setError("no database specified");
				return false;
			}
		mysql_select_db($this->db, $this->conn);

		$result = mysql_query($sql);

		if(!$result)//if it is an empty result,return an empty array
			{
				$this->setError("no rows selected");
				return array();
			}
		$results = array();
		if($this->getResultEncoding() == "array")
			while ($row = mysql_fetch_array($result))
				{
					array_push($results,$row);
				}
		else
			while ($row = mysql_fetch_object($result))
				{
					array_push($results,$row);
				}
		mysql_free_result($result);
		switch($this->getResultEncoding()){
			case "json":
				return $this->toJson($results);
			case "xml":
				return $this->toXml($results);
			case "array":
			case "obj":
				return $results;
			case "none":
			default:
				$this->setError("no result encoding specified");
				return null;
		}
	}

	//the function that executes a query,depending on ($type) what kind of
	//query it is ($type = select,update,insert,create_table,create_db)
	public function executeQuery($sql,$type="select"){
		if(!$type)
			{
				$this->setError("no action was specified");
				return false;
			}
		switch($type)
			{
				case "select":
					return $this->select($sql);
				case "update":
				case "delete":
					return $this->update($sql);
				case "insert":
					return $this->insert($sql);
				case "create_table":
					return $this->createTable($sql);
				case "create_db":
					return $this->createDb($sql);
				default:
					$this->setError("invalid action specified");
					return false;
			}
	}

	public function getInsertedId(){
		return mysql_insert_id($this->conn);
	}

	private function toXml($result){
		$r = "<".$this->xmlParentNodeName.">";
		foreach($result as $entry)
			{
				$r .= "<".$this->xmlChildNodeName." ";
				foreach($entry as $key => $val)
					$r .= "\"".$this->parseToXml($key)."\"=\"".$this->parseToXml($val)."\" ";
				$r .= " />";
			}
		$r .= "</result_set>";
		return $r;
	}
	private function toJson($result){
		if($result == null || $result == '')
			return "[]";
		$r = "[";//starting the json array
		$n = count($result);
		$counter = 0;
		foreach($result as $row)
			{
				$r .= "{";
				foreach($row as $key => $val)
					{
						$r .= '"'.$key.'":"'.$val.'",';
					}
				$r = substr_replace($r ,"",-1);//removeing the last char( a ",") because it's the last element and we don't need another ","
				$r .= "},";
			}
		$r = substr_replace($r ,"",-1);//removeing the last char( a ",") because it's the last element and we don't need another ","
		$r .= "]";
		return $r;
	}
	private function parseToXML($htmlStr){
			$xmlStr=str_replace('<','&lt;',$htmlStr);
			$xmlStr=str_replace('>','&gt;',$xmlStr);
			$xmlStr=str_replace('"','&quot;',$xmlStr);
			$xmlStr=str_replace("'",'&#39;',$xmlStr);
			$xmlStr=str_replace("&",'&amp;',$xmlStr);
			return $xmlStr;
		}

	public function changeConnection($user='root', $pass='', $db_url='localhost',$db=''){
		$this->disconnect();

		$this->user = $user;
		$this->pass = $pass;
        $this->host = $db_url;
        $this->db = $db;

		return $this->connect();
	}
}
/*
$db = new EasyPhpMysqlDb();
echo $db->getResultEncoding();
echo "dbmanager works!";
*/
