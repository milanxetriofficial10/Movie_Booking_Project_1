<?php
session_start();
$conn = new mysqli("localhost","root","Milan@1234","movie_booking_project_1");

// Example values (dynamic गर पछि)
$user_id = 1;
$movie_id = 2;
$amount = 100;

// unique order id
$pid = "ORD" . rand(1000,9999);

// DB मा insert (pending)
$conn->query("INSERT INTO payments (user_id, movie_id, amount, pid, status)
VALUES ('$user_id','$movie_id','$amount','$pid','pending')");
?>

<form id="esewaForm" action="https://uat.esewa.com.np/epay/main" method="POST">

    <input value="<?php echo $amount; ?>" name="tAmt" type="hidden">
    <input value="<?php echo $amount; ?>" name="amt" type="hidden">
    <input value="0" name="txAmt" type="hidden">
    <input value="0" name="psc" type="hidden">
    <input value="0" name="pdc" type="hidden">

    <input value="EPAYTEST" name="scd" type="hidden">
    <input value="<?php echo $pid; ?>" name="pid" type="hidden">

    <input value="http://localhost/success.php" name="su" type="hidden">
    <input value="http://localhost/failure.php" name="fu" type="hidden">

</form>

<script>
document.getElementById("esewaForm").submit();
</script>