<?php
    if(!isset($phpbms))
        exit();

    if($phpbms->showFooter)
{?>
<div id="footer">
	<p id="footerAbout">activeWMS by <a href="http://www.hortonconsulting.co.uk" target="_blank">Mark Horton</a></p>
	<p id="footerTop"><a href="#toptop">top</a></p>
</div>
<?php }//end if ?>
<?php $phpbms->showExtraJs($phpbms->bottomJS) ?>
</body>
</html>
