<?php

use Katzgrau\KLogger\Logger;

class LoggerTest extends PHPUnit_Framework_TestCase
{
    private $logPath;
    private $logger;
    private $logFile;

    private $timeZone = "Europe/Berlin";

    public function setUp()
    {
        $date = new DateTime('now', new DateTimeZone($this->timeZone));
        $this->logFile = "log_".$date->format('Y-m-d').".txt";
        $this->logPath = __DIR__.'/logs/';

        for ($i = 1; $i <= 10; $i++) {
            $file = fopen($this->logPath."log_test".$i.".txt", 'w');
            fclose($file);
        }

        //TODO: this is neccessary to ensure that dummy files are older than real logfile :-(
        sleep(1);

        $this->logger = new Logger($this->logPath);
        $this->logger->setTimeZone($this->timeZone);
    }

    public function tearDown()
    {
        unlink($this->logPath.$this->logFile);
    }

    public function testImplementsPsr3LoggerInterface()
    {
        $this->assertInstanceOf('Psr\Log\LoggerInterface', $this->logger);
    }

    public function testFilename()
    {
        $this->assertEquals(1, file_exists($this->logPath.$this->logFile));
    }

    public function testContent()
    {
        $this->logger->debug("test");

        $logfile = fopen($this->logPath.$this->logFile, "r");
        $content = fgets($logfile);
        fclose($logfile);

        $this->assertNotEmpty($content);
        $this->assertTrue(strpos($content, "[DEBUG] test") != -1);
    }

    public function testRemoveOldLogfiles()
    {
        $count = count(glob($this->logPath."log_*.txt"));

        $this->assertEquals(5, $count);
    }
}
