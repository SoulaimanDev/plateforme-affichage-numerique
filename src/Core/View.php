<?php

class View {
    public static function render($template, $data = []) {
        extract($data);
        require_once __DIR__ . "/../View/{$template}.php";
    }
}
