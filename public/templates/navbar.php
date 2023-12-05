<nav class="navbar">
    <ul class="nav-list">
        <li><a href="profile.php">Profil</a></li>
        <li><a href="booking.php">Booking</a></li>
        <li><a href="messages.php">Meldinger</a></li>
        <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
            <li><a href="<?php echo "__DIR__ . /../../logout.php" ?>" >Logg ut</a></li>
        <?php endif; ?>
    </ul>
</nav>