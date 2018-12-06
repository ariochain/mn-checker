<?php

require_once("include/init.inc.php");

$hostname = 'https://www.ariochain.info';   // report hostname
$reportUri = '/api/v1/public/peers.json';   // report uri
$notifyEmail = 'dev@ariochain.info';        // send mail to
$show = true;                               // show results in terminal


// your ip addresses you want to check

$checknodes = array(
    "5.5.95.239",
);


// load report

$apiUrl = $hostname.$reportUri;
$ariochainreport = file_get_contents($apiUrl);
$json=json_decode($ariochainreport,true); // formated result

// our nodes
foreach ($checknodes as $node) {
   
    if ($show) echo $node." ";
    
    // get node's current block
    $url = "http://".$node."/peer.php?q=";
    $data = peer_post($url."currentBlock", [], 5);

    // report data
    foreach ($json['peers'] as $peer) {

        if ($peer['ip'] == $node) { // our node matching report entry

            if ($data['height'] !== $peer['lastblock']) { // if reporting has wrong number
                $peer['lastblock'] == $data['height']; // display current block
            } else
            if ($peer['status'] == 'behind') { // if reporting has last block, check if status is behind

                // send notification
                $to = $notifyEmail;
                $subject = "Node ".$node." behind";
                $txt = "Your node ".$node." is behind.";
                $headers = "From: ".$notifyEmail;

                mail($to,$subject,$txt,$headers);
            }

            if ($show) echo $peer['lastblock']." ";
            if ($show) echo $peer['status']." ";

        }
    }

    if ($show) echo "\n";
}

?>
