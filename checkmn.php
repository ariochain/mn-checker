<?php

require_once __DIR__.'/include/init.inc.php';

$hostname = 'https://www.ariochain.info';   // Report hostname
$reportUri = '/api/v1/public/peers.json';   // Report URI
$notifyEmail = 'dev@ariochain.info';        // Send mail to
$show = true;                               // Show results in terminal


// Your IP addresses you want to check

$checkNodes = [
    '5.5.95.239',
];


// Load report

$apiUrl = $hostname.$reportUri;
$ariochainReport = file_get_contents($apiUrl);
$json = json_decode($ariochainReport, true); // Formated result

// Our nodes
foreach ($checkNodes as $node) {
    if ($show) {
        echo $node.' ';
    }

    // Get node's current block
    $url = 'http://'.$node.'/peer.php?q=';
    $data = peer_post($url.'currentBlock', [], 5);

    // Report data
    foreach ($json['peers'] as $peer) {
        // Our node matching report entry
        if ($peer['ip'] == $node) {
            if ($data['height'] !== $peer['lastblock']) { // If reporting has wrong number
                $peer['lastblock'] == $data['height']; // Display current block
            } elseif ($peer['status'] == 'behind') { // If reporting has last block, check if status is behind
                // Send notification
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
