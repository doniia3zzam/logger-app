<?php

namespace Tests\Feature;

use App\Services\FileService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FileLoadMemoryTest extends TestCase
{
    const MAX_MEMORY_BYTES = 3 * 1024 * 1024; // 3MB in bytes

    private $testFilePath; // Path to the large test file

    protected function setUp(): void
    {
        parent::setUp();

        // Create a temporary large test file
        $this->testFilePath = tempnam(sys_get_temp_dir(), 'D:\xampp_new\htdocs\logger-app\storage\logs\laravel.log');
        $content = str_repeat("Line 1\n", 1024 * 1024); // Approximately 1MB
        file_put_contents($this->testFilePath, $content);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // Remove the temporary test file
        if (file_exists($this->testFilePath)) {
            unlink($this->testFilePath);
        }
    }

    public function testMemoryUsage()
    {

        $fileService = new FileService();

        // Define test parameters
        $operation = 'next';
        $currentPage = 1;

        // Measure memory usage before calling the function
        $startMemory = memory_get_usage();
        echo "Initial Memory Usage: " . $this->formatMemory($startMemory) . PHP_EOL;

        // Call the loadfileContent function
        $fileService->loadfileContent($this->testFilePath, $operation, $currentPage);

        // Measure memory usage after calling the function
        $endMemory = memory_get_usage();
        echo "Final Memory Usage: " . $this->formatMemory($endMemory) . PHP_EOL;

        // Calculate memory usage difference
        $memoryDiff = $endMemory - $startMemory;
        echo "Memory Increase: " . $this->formatMemory($memoryDiff) . PHP_EOL;

        // Assert that memory usage doesn't exceed 3MB
        $this->assertLessThanOrEqual(self::MAX_MEMORY_BYTES, $memoryDiff);
    }

    private function formatMemory($bytes)
    {
        return round($bytes / 1024 / 1024, 2) . " MB";
    }
}
