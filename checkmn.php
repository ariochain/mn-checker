<?php

require_once __DIR__.'/include/init.inc.php';

$hostname = 'https://www.ariochain.info';   // report hostname
$reportUri = '/api/v1/public/peers.json';   // report uri
$notifyEmail = 'dev@ariochain.info';        // send mail to
$show = true;                               // show results in terminal


// Your ip addresses you want to check

$checkNodes = [
    '5.5.95.239',
];


// Load report

$apiUrl = $hostname.$reportUri;
$ariochainReport = file_get_contents($apiUrl);
$json = json_decode($ariochainReport, true); // formated result

// Our nodes
foreach ($checkNodes as $node) {
    if ($show) {
        echo $node.' ';
    }

    // get node's current block
    $url = 'http://'.$node.'/peer.php?q=';
    $data = peer_post($url.'currentBlock', [], 5);

    // report data
    foreach ($json['peers'] as $peer) {
        if ($peer['ip'] == $node) { // our node matching report entry
            if ($data['height'] !== $peer['lastblock']) { // if reporting has wrong number
                $peer['lastblock'] == $data['height']; // display current block
            } elseif ($peer['status'] == 'behind') { // if reporting has last block, check if status is behind
                // send notification
                $to = $notifyEmail;
                $subject = 'Node '.$node.' behind';
                $txt = 'Your node '.$node.' is behind.';
                $headers = 'From: '.$notifyEmail;

                mail($to, $subject, $txt, $headers);
            }

            if ($show) {
                echo $peer['lastblock'].' ';
            }
            if ($show) {
                echo $peer['status'].' ';
            }
        }
    }

    if ($show) {
        echo "\n";
    }
}
