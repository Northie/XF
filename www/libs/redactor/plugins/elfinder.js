if (typeof RedactorPlugins === 'undefined') var RedactorPlugins = {};

RedactorPlugins.elfinder = {

	init:function(){
		//this.removeBtn('image');
		//this.addBtnBefore('video','elfinder','Choose Image',this.elFinder);
		
		this.buttonRemove('image');
		this.buttonAddBefore('video','elfinder','Choose Image',this.elFinder);
	},
	elFinder:function(redactor_object, event, button_key) {
		
		$("#elFinderWrapperOuter").slideDown();
		
		var elf = $('#elfinder').elfinder({
			 url:"/connectors/elfinder.php"
			,width:900
			,height:550
			,handlers:{
				dblclick:function(event, elfinderInstance){							
					
					$("body").mask("Adding Image...");
					
					$.ajax({
						url:"/connectors/elfinder.php?cmd=dim&target="+event.data.file,
						complete:function(a,b){
							var d = eval("("+a.responseText+")");

							var dc = Math.random();

							dc = dc + "";
							dc = dc.replace(/0\./,"");
							
							var html = "<img src='http://email.newsletterhub.co.uk"+d.path+"?_dc="+dc+"' />";
							
							redactor_object.insertHtml(html);
							
							$("#elFinderWrapperOuter").slideUp();
							
							$("body").unmask();

						}
					});

					return false;
				}

			}
		}).elfinder('instance');
		
		return false;
	}
}
