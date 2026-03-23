<?php
$pass = 'Admin@123';
$hash = '$2y$10$H8kN3F2lQk4ZpPfj6zXx2.OdljWwXoL5Q6gBqEo3Yz0F6jO5n/5eW';

if (password_verify($pass, $hash)) {
    echo "Password is correct!";
} else {
    echo "Password mismatch!";
}
?>
