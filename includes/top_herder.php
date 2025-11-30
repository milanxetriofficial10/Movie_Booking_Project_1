<?php

?>
<div class="location-dropdown">
    <form method="get" action="/Movie_Booking_Project_1/index.php">
        <select name="location" onchange="this.form.submit()">
            <option value="">Select Location</option>
            <?php while($loc = $locations->fetch_assoc()): ?>
                <option value="<?= htmlspecialchars($loc['name']) ?>"><?= htmlspecialchars($loc['name']) ?></option>
            <?php endwhile; ?>
        </select>
    </form>
</div>
<style>
    .location-dropdown select {
    padding: 5px 10px;
    border-radius: 5px;
    border: 1px solid #ccc;
    background: #f7d154ff;
    font-weight: bold;
    cursor: pointer;
}
</style>
