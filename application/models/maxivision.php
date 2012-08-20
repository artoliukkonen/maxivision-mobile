<?

class Maxivision extends CI_Model {
  
  function doLogin() {
    $curl = $this->curl;
    $params = array();
    $params['_method'] = 'POST';
    $params['data[Customer][username]'] = MAXIVISION_USERNAME;
    $params['data[Customer][password]'] = MAXIVISION_PASSWORD;
    $params['data[Customer][login_referer]'] = '/epg';
    $r = $curl->post(BASEURL.'/login', $params);
  }
}