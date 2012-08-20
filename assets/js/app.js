function showDialog(message) {
  $('.description', '.recordpopup').html('<div style="padding:15px">'+message+'</div>');
  $('.recordpopup').popup().popup('open');
}

function addRecording(path, el) {
	$.ajax({
		url: BASE_URL+'epg/recordProgram',
		type: 'post',
		data: { path: path}, 
		dataType: 'html',
		success: function(data) {
      if(el.html()=='Ajasta') {
        showDialog('Ohjelma tallennetaan');
        el.html('Poista ajastus');
      } else {
        showDialog('Tallennus poistettu');
        el.html('Ajasta');
      }
		}
	});
}

$('div.program').live('expand', function(){
  var path = $(this).data("path");
  var that = this;
  if($(".details", this).html()=="") {
    $.ajax({
      url: BASE_URL+'epg/programInfo/?program='+escape(path), 
      dataType: 'json',
      type: 'get',
      success: function(data) {
        var html = '<h4>'+data.title+'</h4>';
        html+='<small>'+data.time+'</small>';
        if(data.description) html+='<p>'+data.description+'</p>';
        $(".details", that).html(html);
      }
    });
  }
});