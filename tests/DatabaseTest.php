<?php

namespace Rudzz\Tests;

use PHPUnit\Framework\TestCase;

class DatabaseTest extends TestCase
{
    private $db;
    
    protected function setUp(): void
    {
        // Initialize database connection for testing
        $this->db = new \Rudzz\Database(
            getenv('DB_HOST'),
            getenv('DB_USER'),
            getenv('DB_PASS'),
            getenv('DB_NAME')
        );
    }
    
    public function testDatabaseConnection()
    {
        $this->assertTrue($this->db->isConnected());
    }
    
    public function testUserTableExists()
    {
        $result = $this->db->query("SHOW TABLES LIKE 'users'");
        $this->assertNotEmpty($result);
    }
}

