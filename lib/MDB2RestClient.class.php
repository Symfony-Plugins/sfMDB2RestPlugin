<?php
/**
 * MDB2RestClient
 *
 * @package     MDB2Rest
 * @subpackage  Client
 * @uses        MDB2Rest
 * @author Jonathan H. Wage
 */
class MDB2RestClient
{
  protected $serverUrl  = null,
            $username   = null,
            $password   = null;
  
  /**
   * __construct
   *
   * @param string $serverUrl 
   * @return void
   */
  public function __construct($serverUrl)
  {
    $this->serverUrl = $serverUrl;
  }
  
  /**
   * setUsername
   *
   * @param string $username 
   * @return void
   */
  public function setUsername($username)
  {
    $this->username = $username;
  }
  
  /**
   * setPassword
   *
   * @param string $password 
   * @return void
   */
  public function setPassword($password)
  {
    $this->password = $password;
  }
  
  /**
   * request
   *
   * @param string $request 
   * @return void
   */
  protected function request($request)
  {
    $url  = strstr($this->serverUrl, '?') ? $this->serverUrl . '&':$this->serverUrl . '?';
    $url .= 'username=' . $this->username . '&password=' . $this->password;
    
    ob_start();
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, array('data' => serialize($request)));
    curl_exec($ch);
    curl_close($ch);
    $results = ob_get_contents();
    ob_end_clean();
    
    return unserialize($results);
  }
  
  /**
   * exec
   *
   * @param string $query 
   * @return void
   */
  public function exec($query)
  {
    return $this->execute(__FUNCTION__, get_defined_vars());
  }
   
  /**
   * query
   *
   * @param string $query
   * @param $types
   * @param $result_class
   * @param $result_wrap_class
   * @return void
   */
  public function query($query, $types = null, $result_class = true, $result_wrap_class = false)
  {
    return $this->execute(__FUNCTION__, get_defined_vars());
  }
  
  /**
   * queryAll
   *
   * @param string $query 
   * @param string $types 
   * @param string $fetchmode 
   * @param string $rekey 
   * @param string $force_array 
   * @param string $group 
   * @return void
   */
  public function queryAll($query, $types = null, $fetchmode = 2, $rekey = false, $force_array = false, $group = false)
  {
    return $this->execute(__FUNCTION__, get_defined_vars());
  }
  
  /**
   * queryCol
   *
   * @param string $query 
   * @param string $type 
   * @param string $colnum 
   * @return void
   */
  public function queryCol($query, $type = null, $colnum = 0)
  {
    return $this->execute(__FUNCTION__, get_defined_vars());
  }
  
  /**
   * queryOne
   *
   * @param string $query 
   * @param string $type 
   * @param string $colnum 
   * @return void
   */
  public function queryOne($query, $type = null, $colnum = 0)
  {
    return $this->execute(__FUNCTION__, get_defined_vars());
  }
  
  /**
   * queryRow
   *
   * @param string $query 
   * @param string $types 
   * @param string $fetchmode 
   * @return void
   */
  public function queryRow($query, $types = null, $fetchmode = 4)
  {
    return $this->execute(__FUNCTION__, get_defined_vars());
  }
  
  /**
   * execute
   *
   * @param string $method 
   * @param string $arguments 
   * @return void
   */
  protected function execute($method, $arguments)
  {
    $request = array('method' => $method, 'arguments' => $arguments);
    
    $results = $this->request($request);
    
    if (isset($results['error'])) {
      throw new Exception($results['error']);
    } else {
      return $results;
    }
  }
}