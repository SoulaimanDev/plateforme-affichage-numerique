<?php

class Request
{
    private $get;
    private $post;
    private $server;

    public function __construct()
    {
        $this->get = $_GET;
        $this->post = $_POST;
        $this->server = $_SERVER;
    }

    /**
     * Get a value from GET parameters
     */
    public function query($key, $default = null)
    {
        return $this->get[$key] ?? $default;
    }

    /**
     * Get a value from POST parameters
     */
    public function input($key, $default = null)
    {
        return $this->post[$key] ?? $default;
    }

    /**
     * Get the request method
     */
    public function getMethod()
    {
        return $this->server['REQUEST_METHOD'] ?? 'GET';
    }

    /**
     * Check if the request method matches
     */
    public function isMethod($method)
    {
        return strtoupper($this->getMethod()) === strtoupper($method);
    }

    /**
     * Get the request URI
     */
    public function getUri()
    {
        return $this->server['REQUEST_URI'] ?? '/';
    }

    /**
     * Get all input data (POST + GET)
     */
    public function all()
    {
        return array_merge($this->get, $this->post);
    }
}
