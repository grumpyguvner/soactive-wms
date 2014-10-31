<?php
    require_once ("PSWebServiceLibrary.php");

    $html = "<html><head><title>Prestashop WebService Test - Order Detail</title></head><body>";
    define('DEBUG', true);      					// Debug mode
    define('PS_SHOP_PATH', 'http://www.sheactive.co.uk/');		// Root path of your PrestaShop store
//    define('PS_SHOP_PATH', 'http://activeaddict.com/');		// Root path of your PrestaShop store
    define('PS_WS_AUTH_KEY', 'JWFI844NARM8VFPSIZP4FTBNDGBG5I2T');	// Auth key (Get it in your Back Office)

function retrieveData($resource,$id=NULL,$display=NULL){

    $resources = NULL;
    // Here we make the WebService Call
    try
    {
        $webService = new PrestaShopWebservice(PS_SHOP_PATH, PS_WS_AUTH_KEY, DEBUG);

        // Here we set the option array for the Webservice : we want customers resources
        $opt['resource'] = $resource;
        if(isset($id)) $opt['id'] = $id;
        if(isset($display)) $opt['display'] = $display;
//        $opt['filter[current_state]'] = '2';

        // Call
//        $xml = $webService->get($opt);
        $xml = $webService->getXML($opt);
print_r($xml);
return $xml[0][childs][0][childs];

        // Here we get the elements from children of customers markup "customer"
//        $resources = $xml->customers->children();
        $resources = $xml->children()->children();
    }
    catch (PrestaShopWebserviceException $e)
    {
        // Here we are dealing with errors
        $trace = $e->getTrace();
        if ($trace[0]['args'][0] == 404) echo 'Bad ID';
        else if ($trace[0]['args'][0] == 401) echo 'Bad auth key';
    //                else echo 'Other error';
    else print_r($e);
    }

    return $resources;
}

    $html .= "<h1>Order Detail</h1>";

    if(!isset($_GET['id'])){
        $html .= "<p>NO ORDER ID HAS BEEN SET.</p>";
        $html .= "</body></html>";
        die($html);
    }

    $order = retrieveData('orders', $_GET['id']);
    if(!isset($order)){
        $html .= "<p>UNABLE TO RETRIEVE ORDER ".$_GET['id'].".</p>";
        $html .= "</body></html>";
        die($html);
    }

    //Display returned order header
    $html .= "<table border='5'>";
    // if $resources is set we can lists element in it otherwise do nothing cause there's an error
    if (isset($order))
    {
        $html .= "<tr><th>Name</th><th>Value</th></tr>";
        foreach ($order as $key => $resource)
        {
                // Iterates on the found IDs
                $html .= "<tr><td>".$key."</td><td>".$resource."</td></tr>";
        }
    }
    $html .= "</table>";

    //If status isn't "Payment Accepted" then do not continue
    if($order->current_state!=2){
        $html .= "<p>NOTHING TO DO.</p>";
        $html .= "</body></html>";
        die($html);
    }

    $rows = $order->associations->order_rows;
    foreach ($rows as $row){
        $html .= "<table border='5'>";
        // if $resources is set we can lists element in it otherwise do nothing cause there's an error
        if (isset($row))
        {
            $html .= "<tr><th>Name</th><th>Value</th></tr>";
            $html .= "<tr><td>TEST</td><td>".$row->id."</td></tr>";
            foreach ($row as $key => $resource)
            {
                    // Iterates on the found IDs
                    $html .= "<tr><td>".$key."</td><td>".$resource."</td></tr>";
            }
        }
        $html .= "</table>";

//        $orderItem = retrieveData('order_details', $row->id);
//        $html .= "<table border='5'>";
        // if $resources is set we can lists element in it otherwise do nothing cause there's an error
//        if (isset($orderItem))
//        {
//            $html .= "<tr><th>Name</th><th>Value</th></tr>";
//            foreach ($orderItem as $key => $resource)
//            {
                    // Iterates on the found IDs
//                    $html .= "<tr><td>".$key."</td><td>".$resource."</td></tr>";
//            }
//        }
//        $html .= "</table>";
    }

    $html .= "</body></html>";
    die($html);
?>
