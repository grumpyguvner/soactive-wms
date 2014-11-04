<?php
	require_once("include/session.php");
//	require_once("../../phpuploader/include_phpuploader.php");
	require_once("include/fields.php");

	$phpbms->cssIncludes[] = "../../../uploadifive/uploadifive.css";
	$phpbms->jsIncludes[] = "uploadifive/jquery.min.js";
	$phpbms->jsIncludes[] = "uploadifive/jquery.uploadifive.min.js";


	$pagetitle="Image Uploader";


	include("header.php");
	?>
<div class="bodyline">
	<h1><?php echo $pagetitle?></h1>
	<form>
		<div id="queue"></div>
		<input id="file_upload" name="file_upload" type="file" class="Buttons" multiple="true">
		<!-- <a style="position: relative; top: 8px;" href="javascript:$('#file_upload').uploadifive('upload')">Upload Files</a> -->
	</form>

	<script type="text/javascript">
		<?php $timestamp = time();?>
		$(function() {
			$('#file_upload').uploadifive({
				'auto'             : true,
				'checkScript'      : 'uploadifive/check-exists.php',
				'formData'         : {
									   'timestamp' : '<?php echo $timestamp;?>',
									   'token'     : '<?php echo md5('unique_salt' . $timestamp);?>'
				                     },
				'queueID'          : 'queue',
				'uploadScript'     : 'uploadifive/uploadifive.php',
				'onUploadComplete' : function(file, data) { console.log(data); },
                                'onQueueComplete'  : function(queueData) { window.location.href = "/uploadifive/processfiles.php"; }
			});
		});
	</script>
<?php include("footer.php");?>