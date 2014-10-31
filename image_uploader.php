<?php
	require_once("include/session.php");
//	require_once("../../phpuploader/include_phpuploader.php");
	require_once("include/fields.php");

	$phpbms->jsIncludes[] = "uploadify/jquery-1.4.2.min.js";
	$phpbms->jsIncludes[] = "uploadify/swfobject.js";
	$phpbms->jsIncludes[] = "uploadify/jquery.uploadify.v2.1.4.min.js";


	$pagetitle="Image Uploader";


	include("header.php");
	?>
<div class="bodyline">
	<h1><?php echo $pagetitle?></h1>

        <input id="file_upload" type="file" name="file_upload" />
        <div id="image_container"></div>
</div>
<?php include("footer.php");?>
<script type="text/javascript">
$(document).ready(function() {
  $('#file_upload').uploadify({
    'uploader'  : 'uploadify/uploadify.swf',
    'script'    : 'uploadify/uploadify.php',
    'cancelImg' : 'uploadify/cancel.png',
    'folder'    : 'uploadify/uploads',
    'onAllComplete' : function(event,data) {
            $.get('uploadify/filelist.php', function(data) {
            $('#image_container').html(data);
            });
        },
    'multi'           : true,
    'auto'            : true
  });
});
</script>
