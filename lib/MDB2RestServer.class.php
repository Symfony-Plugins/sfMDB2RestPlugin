<?php
/**
 * MDB2RestServer
 *
 * @package     MDB2Rest
 * @subpackage  Server
 * @uses        MDB2Rest
 * @author Jonathan H. Wage
 */
class MDB2RestServer
{
  protected $htpasswdFile   =   null,
            $request        =   array(),
            $mdb2           =   null;
  
  /**
   * __construct
   *
   * @param string $htpasswdFile 
   * @param string $mdb2 
   * @return void
   */
  public function __construct($htpasswdFile, $mdb2)
  {
    $this->htpasswdFile = $htpasswdFile;
    $this->mdb2 = $mdb2;
  }
  
  /**
   * setRequest
   *
   * @param string $request 
   * @return void
   */
  public function setRequest($request)
  {
    $this->request = $request;
    
    // Set the PHP_AUTH information if it exists in the request
    if (isset($this->request['username'])) {
      $_SERVER['PHP_AUTH_USER'] = $this->request['username'];
    }

    if (isset($this->request['password'])) {
      $_SERVER['PHP_AUTH_PW'] = $this->request['password'];
    }
  }
  
  /**
   * auth
   *
   * @return void
   */
  public function auth()
  {
    $username = isset($_SERVER['PHP_AUTH_USER']) ? $_SERVER['PHP_AUTH_USER']:null;
    $password = isset($_SERVER['PHP_AUTH_PW']) ? $_SERVER['PHP_AUTH_PW']:null;
    
    $fileContents = file_get_contents($this->htpasswdFile);

    $lines = explode("\n", $fileContents);
    
    foreach ($lines as $line) {
      if (!$line) {
        continue;
      }
      
      list($u, $p) = explode(':', $line);
      
      if ($u == $username) {
        $salt = substr($p, 0 ,2);
        $encryptedPassword = crypt($password, $salt);
        
        if ($p == $encryptedPassword) {
          return true;
        }
      }
    }
    
    header('WWW-Authenticate: Basic realm="MDB2RestServer"');
    header('HTTP/1.0 401 Unauthorized');
    
    return false;
  }
  
  /**
   * run
   *
   * @return void
   */
  public function run()
  {
    $request = unserialize($this->request['data']);
    
    $method = $request['method'];
    $arguments = $request['arguments'];
    
    $results = $this->$method($arguments);
    
    if ($results instanceof MDB2_error) {
      $results = array('error' => $results->getMessage());
    }
    
    echo serialize($results);
  }
  
  /**
   * exec
   *
   * @param string $arguments 
   * @return void
   */
  protected function exec($arguments)
  {
    return call_user_func_array(array($this->mdb2, __FUNCTION__), $arguments);
  }
  
  /**
   * query
   *
   * @param string $arguments 
   * @return void
   */
  protected function query($arguments)
  {
    $results = call_user_func_array(array($this->mdb2, __FUNCTION__), $arguments);
    
    $rows = array();
    while (($row = $results->fetchRow())) {
        $rows[] = $row;
    }
    
    return $rows;
  }
  
  /**
   * queryAll
   *
   * @param string $arguments 
   * @return void
   */
  protected function queryAll($arguments)
  {
    return call_user_func_array(array($this->mdb2, __FUNCTION__), $arguments);
  }
  
  /**
   * queryCol
   *
   * @param string $arguments 
   * @return void
   */
  protected function queryCol($arguments)
  {
    return call_user_func_array(array($this->mdb2, __FUNCTION__), $arguments);
  }
  
  /**
   * queryOne
   *
   * @param string $arguments 
   * @return void
   */
  protected function queryOne($arguments)
  {
    return call_user_func_array(array($this->mdb2, __FUNCTION__), $arguments);
  }
  
  /**
   * queryRow
   *
   * @param string $arguments 
   * @return void
   */
  protected function queryRow($arguments)
  {
    return call_user_func_array(array($this->mdb2, __FUNCTION__), $arguments);
  }
  
  /**
   * __call
   *
   * @param string $method 
   * @param string $arguments 
   * @return void
   */
  public function __call($method, $arguments)
  {
    throw new Exception($method . ' does not exist');
  }
}