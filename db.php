<?php 
    require "libs/rb.php";
    require "config.php";
    R::setup( 'mysql:host='.DB_HOST.';dbname='.DB_NAME,
    DB_USER, DB_PASS );
    session_start();
?>