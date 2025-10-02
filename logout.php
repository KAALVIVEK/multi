<?php
include_once 'auth.php';

// Call the function from auth.php to clear all session variables and destroy the session.
logout_user();

// Redirect the user back to the login page (or index page).
redirect('login.php'); 
?>
