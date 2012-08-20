<?
class Curl extends CI_Model {

    var $opts = array(
            CURLOPT_HEADER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_COOKIEJAR => './cookie.txt',
            CURLOPT_COOKIEFILE => './cookie.txt' 
    );

    function __construct() {
      parent::__construct();
    }

    function r($ch,$opt){
        # assign global options array
        $opts = $this->opts;
        # assign user's options
        foreach($opt as $k=>$v){$opts[$k] = $v;}
        curl_setopt_array($ch,$opts);
        curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
        curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, FALSE); 
        $agent = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.0.3705; .NET CLR 1.1.4322)';
        curl_setopt($ch, CURLOPT_USERAGENT, $agent);

        $r['code'] = curl_getinfo($ch,CURLINFO_HTTP_CODE);
        $r['cr'] = curl_exec($ch);
        $r['ce'] = curl_errno($ch);
        curl_close($ch);
        return $r;
    }

    function get($url='',$opt=array()){
        # create cURL resource
        $ch = curl_init($url);
        return $this->r($ch,$opt);
    }

    function post($url='',$data=array(),$opt=array()){
        # set POST options
        $this->opts[CURLOPT_POST] = TRUE;
        $this->opts[CURLOPT_POSTFIELDS] = $data;

        # create cURL resource
        $ch = curl_init($url);
        return $this->r($ch,$opt);
    }
}