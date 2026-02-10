<?php
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'administrateur') {
    header('Location: dashboard.php');
    exit();
}
