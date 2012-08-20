<?

// Super simple key=value caching class

class Cache extends CI_Model {

  function __construct() {
    parent::__construct();
  }
  
  function get($key) {
    return apc_fetch($key);
  }
  
  function set($key, $val, $ttl = 3600) {
    return apc_store($key, $val, $ttl); 
  }
}