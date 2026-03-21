<?php
$conn = new mysqli("localhost","root","Milan@1234","movie_booking_project_1");

$pid = $_GET['oid'];
$refId = $_GET['refId'];
$amt = $_GET['amt'];

// verify with esewa
$url ="https://uat.esewa.com.np/epay/transrec";

$data = [
    'amt' => $amt,
    'scd' => 'EPAYTEST',
    'rid' => $refId,
    'pid' => $pid
];

$curl = curl_init($url);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($curl);
curl_close($curl);

// check success
if (strpos($response, "Success") !== false) {

    // update DB
    $conn->query("UPDATE payments SET status='success' WHERE pid='$pid'");

    echo "<h2>✅ Payment Successful</h2>";
    echo "<a href='ticket.php'>View Ticket</a>";

} else {

    echo "<h2>❌ Payment Failed</h2>";
}
?>