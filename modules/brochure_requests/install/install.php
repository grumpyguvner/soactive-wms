<?php
$theModule = new installModuleAjax($this->db, $this->phpbmsSession, "../modules/brochure_requests/install/");
$theModule->tables = array(
			"menu",
			"modules",
			"roles",
			"tablecolumns",
			"tabledefs",
			"tableoptions"
			);
