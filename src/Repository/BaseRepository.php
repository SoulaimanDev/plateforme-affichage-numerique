<?php

class BaseRepository
{
    protected $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }
    public function getLastInsertId()
    {
        return (int) $this->db->lastInsertId();
    }
}
