if (typeof RedactorPlugins === 'undefined') var RedactorPlugins = {};

RedactorPlugins.imgurl = {

	init:function(){
		//this.addBtnBefore('video','elfinderUrl','Insert Image From URL',this.imageFromUrl);
		this.buttonAddBefore('video','elfinderUrl','Insert Image From URL',this.imageFromUrl);
	},
	imageFromUrl:function(redactor_object, event, button_key) {
		$("#redactorImgUrlForm input").val('');
		$("#redactorImgUrl").modal('show');
		
		redactor_object.saveSelection();

		$("#redactorImgUrlInsert").unbind('click').click(function(){
			
			var uniqid = Math.random();

			//uniqid = uniqid + "";
			//uniqid = uniqid.replace(/0\./,"");
			
			//this.insertHtml("<img src='' id='id_"+uniqid+"'");
			
			//redactor_object.syncCode();
			//redactor_object.setBuffer();
			
			
			var w = $("#imgUrlWidth").val();
			var h = $("#imgUrlHeight").val();

			var ws = '';
			var hs = '';

			var html = '';

			var url = $('#imgUrl').val();

			w = parseInt(w);
			h = parseInt(h);



			if(w > 0) {
				wh = "width='"+w+"px'";
				ws = "width:"+w+"px;";
			} else {
				wh = '';

			}

			if(h > 0) {
				hh = "height='"+h+"px'";
				hs = "height:"+h+"px;";
			} else {
				hh = '';
			}

			var html = " <img  style='"+ws+" "+hs+"' src='"+url+"' "+wh+" "+hh+"> ";
			//var html = ' <img src="'+url+'" /> ';

			//console.log(html);
			
			redactor_object.restoreSelection();

			redactor_object.insertHtml(html);
			//redactor_object.insertNodeAtCaret(node);
			
			//redactor_object.syncCode();

			$("#redactorImgUrl").modal('hide');

			return false;
		});

		$("#redactorImgUrlCancel").click(function(){
			$("#redactorImgUrl").modal('hide');
		});
		
		return false;
	}
}