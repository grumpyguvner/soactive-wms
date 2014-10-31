<?php

if (class_exists("phpbmsTable")) {

    class brochure_requests extends phpbmsTable {

        function brochure_requests($db, $tabledefid = "tbld:8fd31ac0-9b43-11e1-879c-a76ad4230cdd", $backurl = NULL) {
            parent::phpbmsTable($db, $tabledefid, $backurl);
        }

//end function
    }

    //end class
}//end if

if (class_exists("searchFunctions")) {

    class brochure_requestsSearchFunctions extends searchFunctions {

        function delete_record($useUuid = false) {

            $message = "Deleting brochure request records not allowed, please use opt out function instead.";

            return $message;
        }

//end method

        function opt_out($useUuid = false) {

            $cnt = 0;
            //We need to iterate through each record so that OpenCart updates are run
            foreach ($this->idsArray as $theid) {
                include_once 'include/tables.php';
                $request = new phpbmsTable($this->db,'tbld:8fd31ac0-9b43-11e1-879c-a76ad4230cdd');
                $variables = $request->getRecord($theid, $useUuid);
                $variables["opted_out"] = date();
                $request->updateRecord($variables, NULL, true);
                $cnt++;
            }

            $message = $cnt . " records(s) opted out.";
            return $message;
        }

//end method
    }

//end class
}//end if
?>
