<?php

class Response
{
    /**
     * Send a redirect response
     */
    public function redirect($url)
    {
        header("Location: {$url}");
        exit;
    }

    /**
     * Send a JSON response
     */
    public function json($data, $status = 200)
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    /**
     * Set HTTP status code
     */
    public function setStatus($code)
    {
        http_response_code($code);
        return $this; // Fluent interface
    }

    /**
     * Send HTML response (usually handled by View, but good for simple messages)
     */
    public function html($content, $status = 200)
    {
        http_response_code($status);
        echo $content;
    }
}
