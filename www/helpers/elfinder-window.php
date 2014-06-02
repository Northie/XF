<!DOCTYPE html>
<html>
        <head>
                <meta charset="utf-8">
                <title>File Manager - Upload, Organise and Choose Files and Images</title>

                <!-- jQuery and jQuery UI (REQUIRED) -->
                <link rel="stylesheet" type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.23/themes/smoothness/jquery-ui.css">
                <!-- script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script -->
		<script src="/libs/bootstrap/js/jquery.min.js"></script>
                <script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.23/jquery-ui.min.js"></script>

		
                <!-- elFinder CSS (REQUIRED) -->
                <link rel="stylesheet" type="text/css" href="/libs/elfinder/css/elfinder.min.css">
                <link rel="stylesheet" type="text/css" href="/libs/elfinder/css/theme.css">

                <!-- elFinder JS (REQUIRED) -->
                <script src="/libs/elfinder/js/elfinder.min.js"></script>
		<script type="text/javascript">
			var FileBrowserDialogue = {
				init: function() {
					// Here goes your code for setting your custom things onLoad.
				},
				mySubmit: function (URL) {
					// pass selected file path to TinyMCE
					top.tinymce.activeEditor.windowManager.getParams().setUrl(URL);

					// close popup window
					top.tinymce.activeEditor.windowManager.close();
				}
			}

			$(function(){
				var elf = $('#elfinder').elfinder({
					// set your elFinder options here
					url:"/connectors/elfinder.php",  // connector URL
					getFileCallback: function(file) { // editor callback
						// actually file.url - doesnt work for me, but file does. (elfinder 2.0-rc1)
						FileBrowserDialogue.mySubmit(file); // pass selected file path to TinyMCE 
					}
				}).elfinder('instance');      
			});
		</script>
        </head>
        <body>

                <!-- Element where elFinder will be created (REQUIRED) -->
                <div id="elfinder"></div>

        </body>
</html>