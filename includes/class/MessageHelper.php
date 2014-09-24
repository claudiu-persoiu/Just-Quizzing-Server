<?php

class MessageHelper {

    static function set($message) {
        $_SESSION['message'] = $message;
    }

    static function get($remove = true) {
        $message = $_SESSION['message'];

        if($remove) {
            unset($_SESSION['message']);
        }

        return $message;
    }

    static function has() {
        return isset($_SESSION['message']) && $_SESSION['message'];
    }

}