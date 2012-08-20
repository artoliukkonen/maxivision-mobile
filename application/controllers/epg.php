<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Epg extends CI_Controller {

	public function index()	{
    if(!$this->session->userdata('loggedin')) {
      $this->maxivision->doLogin();  // We could implement real login here, but now it's automatic...
      $this->session->set_userdata('loggedin', 1);
    }
    
    $channel =  $currentChannel = (int)$this->uri->segment(3);
    $date = $this->uri->segment(4);
    
    $date = $date?$date:date('Y-m-d');
    
    // Maxivision channels are in packs of 5
    $channelpath = 0;
    if($channel>9) { $channelpath = 2; $channel-=10; }
    elseif($channel>4) { $channelpath = 1; $channel-=5; }
    
    $fullpath = BASEURL.'/epg/index/'.str_replace('-','',$date).'/'.$channelpath.'/0/0';
    
    $txt = '';
    if(! $txt = $this->cache->get(md5($fullpath))) {
      $txt = $this->curl->get($fullpath);
      $txt = utf8_decode($txt['cr']);
      list($n, $txt) = explode('<tr class="channels">', $txt);
      list($txt) = explode('</td></tr>	</table>', $txt);
      $txt = '<tr>'.$txt; // Fix structure a bit...
      
      $this->cache->set(md5($fullpath), $txt, 300); 
    }
    
    $dom = new DOMDocument();
    $dom->loadHTML($txt);

    $ar = dom_to_array($dom);
    $ar = $ar['html'][1]['body'];


    $data->ar = $ar;
    $data->currentChannel = $currentChannel;
    $data->date = $date;
    $this->load->view('index', $data);
	}
	
	function programInfo() {
    $path = $this->input->get('program');
    $fullpath = BASEURL.$path;
    
    $data = new StdClass;
    
    if(!$data = $this->cache->get(md5($fullpath))) {
      $txt = $this->curl->get($fullpath);
      $txt = utf8_decode($txt['cr']);
      
      $dom = new DOMDocument();
      $dom->loadHTML($txt);

      $ar = dom_to_array($dom);
      $ar = $ar['html'][1]['body'];
      
      $data = new StdClass;
      $data->title = $ar['div'][0]['h2']; 
      $data->time = $ar['div'][1]['div'][0]['b']; 
      $data->image = $ar['div'][1]['div'][1]['img']['src']; 
      
      list($null, $description) = explode('<div class="info">', $txt);
      list($description) = explode('<br />', $description);
      $data->description = ($description?trim($description):'');
      
      $this->cache->set(md5($fullpath), $data); 
    }
    
    echo json_encode($data);
	}
	
	function recordProgram() {
    $path = $this->input->post('path');
    
    $txt = $this->curl->get(BASEURL.$path);
    echo $txt['cr'];
	}
}