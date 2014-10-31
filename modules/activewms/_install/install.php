<?php
$theModule = new installModuleAjax($this->db, $this->phpbmsSession, "../modules/activewms/install/");
$theModule->tables = array(
			"choices",
			"locations",
			"menu",
			"modules",
			"relationships",
			"roles",
			"scheduler",
			"settings",
			"smartsearches",
			"tablecolumns",
			"tabledefs",
			"tablefindoptions",
			"tablegroupings",
			"tableoptions",
			"tablesearchablefields",
			"tabs",
			"usersearches",
			"widgets"
			);
