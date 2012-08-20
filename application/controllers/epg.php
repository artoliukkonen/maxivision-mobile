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

?>
<!DOCTYPE html>
<html>
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link rel="stylesheet" href="http://code.jquery.com/mobile/1.2.0-alpha.1/jquery.mobile-1.2.0-alpha.1.min.css" />
  <script src="http://code.jquery.com/jquery-1.7.1.min.js"></script>
  <script src="http://code.jquery.com/mobile/1.2.0-alpha.1/jquery.mobile-1.2.0-alpha.1.min.js"></script>
  <script src="<?=base_url()?>assets/js/app.js"></script>
  <meta charset=utf-8 />
  <title>Maxivision Mobile</title>
  
<script>
var BASE_URL = '<?=base_url()?>'; 
</script>

<style>
.ui-header .ui-title, .ui-footer .ui-title { margin: 0.6em 15% 0.8em; }
</style>
</head>
<body>
<div data-role="page">
  <div data-theme="e" data-role="header" data-position="fixed">
    <h1>
      Maxivision Mobile
    </h1>
  </div>
  <div data-role="content">
    <div class="ui-grid-a">
      <div class="ui-block-a">
        <select onchange="window.location = '<?=base_url()?>epg/index/'+$(this).val()+'/<?=$date?>'">
          <option value="0" <?=($currentChannel==0?'selected="selected"':'')?>>TV1</option> 
          <option value="1" <?=($currentChannel==1?'selected="selected"':'')?>>TV2</option> 
          <option value="2" <?=($currentChannel==2?'selected="selected"':'')?>>MTV3</option> 
          <option value="3" <?=($currentChannel==3?'selected="selected"':'')?>>Nelonen</option> 
          <option value="4" <?=($currentChannel==4?'selected="selected"':'')?>>YLE FEM</option> 
          <option value="5" <?=($currentChannel==5?'selected="selected"':'')?>>Sub</option> 
          <option value="6" <?=($currentChannel==6?'selected="selected"':'')?>>Yle Teema</option> 
          <option value="7" <?=($currentChannel==7?'selected="selected"':'')?>>Liv</option> 
          <option value="8" <?=($currentChannel==8?'selected="selected"':'')?>>TV Viisi</option> 
          <option value="9" <?=($currentChannel==9?'selected="selected"':'')?>>The Voice</option> 
          <option value="10" <?=($currentChannel==10?'selected="selected"':'')?>>JIM</option> 
          <option value="11" <?=($currentChannel==11?'selected="selected"':'')?>>FOX</option> 
          <option value="12" <?=($currentChannel==12?'selected="selected"':'')?>>Taivas TV7</option> 
          <option value="13" <?=($currentChannel==13?'selected="selected"':'')?>>AVA</option> 
        </select>
      </div>
      <div class="ui-block-b">
        <input name="mydate" value="<?=($date?$date:date('d.m.Y'))?>" id="mydate" type="date" data-role="datebox" data-options='{"mode": "calbox"}' onchange="window.location = '<?=base_url()?>epg/index/<?=$currentChannel?>/'+$(this).val()">
      </div>
    </div>

  <div data-role="collapsible-set">
<?
    foreach($ar['tr'] AS $el) {
      if(!is_array($el)) continue;
      
      if(isset($el['td']) && is_array($el['td'])) $programs = $el['td'][$channel];
      else { 
        if(!isset($el[$channel])) continue; 
        $programs = $el[$channel]; 
      }
      
      if(!isset($programs['ul']['li'][0])) {
        $tmp = $programs['ul']['li'];
        $programs['ul']['li'] = array();
        $programs['ul']['li'][] = $tmp;
      }
      
      foreach($programs['ul']['li'] AS $prog) {
        if(!is_array($prog)) continue; 
        
        $record_url = $prog['p']['a'][0]['href'];
        $program_url = $prog['p']['a'][count($prog['p']['a'])-1]['href'];
        if(strstr($record_url, 'remove')) $record_string = 'Poista ajastus';
        else $record_string = 'Ajasta';
        $starttime = $prog['span'];
        
        ?>
          <div data-role="collapsible" data-collapsed="true" data-path="<?=$program_url?>" class="program">
            <h3>
              <?=$starttime?>: <?=$prog['p']['a'][count($prog['p']['a'])-1]['_value']?>
            </h3>
            <p>
              <a href="#" onclick="addRecording('<?=$record_url?>', $(this))"><?=$record_string?></a>
            </p>
            <div class="details"></div>
          </div>
        <?
      }
    }
   
    ?>
    </div>
  </div>
  
      
  <div data-role="popup" class="recordpopup" data-shadow="true" data-corners="true" data-overlay-theme="a">
    <a href="#" data-rel="back" data-role="button" data-theme="a" data-icon="delete" data-iconpos="notext" class="ui-btn-right">Sulje</a>
    <div class="description"></div>
  </div>
</div>
     

</body></html>
    <?
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