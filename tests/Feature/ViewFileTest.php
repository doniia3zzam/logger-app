<?php

namespace Tests\Feature;

use App\Services\FileService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ViewFileTest extends TestCase
{


    protected $fileService;

    private $nonexistent_path;
    private $existent_path;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fileService = new FileService();
        $this->nonexistent_path = '\logs\laravel.log';
        $this->existent_path = 'D:\xampp_new\htdocs\logger-app\storage\logs\laravel.log';
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        \Mockery::close(); // Clean up Mockery
    }


    public function testLoadFileContentFileNotFound()
    {
        $mockFileService = $this->getMockBuilder(FileService::class)
            ->onlyMethods(['fileExists'])
            ->getMock();

        $mockFileService->method('fileExists')->willReturn(false);

        $response = $mockFileService->loadfileContent( $this->nonexistent_path , 'operation', 1);
        $responseData = $response->getData();

        $this->assertSame('Error', $responseData->State);
        $this->assertSame(404, $responseData->status);
    }

    public function testCalculateTotalPages()
    {
        $fileService = new FileService();

        // Create a mock for getTotalLines method
        $mockFileService = $this->getMockBuilder(FileService::class)
            ->onlyMethods(['getTotalLines'])
            ->getMock();

        // Set up mock behavior for getTotalLines
        $mockFileService->method('getTotalLines')->willReturn(10); // Assuming 10 lines


        // Call the method to test
        $response = $fileService->loadfileContent($this->existent_path, 'operation', 1);
        $responseData = $response->getData();


        $this->assertSame(200, $responseData->status);
    }


    public function testHandelPagination()
    {
        // Create an instance of FileService (or your specific implementation)
        $fileService = new FileService();

        // Test 'first' operation
        $currentPage = $fileService->handelPagination('first', 10, 5);
        $this->assertSame(1, $currentPage);
        // Test 'last' operation
        $currentPage = $fileService->handelPagination('last', 10, 5);
        $this->assertSame(10, $currentPage);

        // Test 'next' operation
        $currentPage = $fileService->handelPagination('next', 10, 5);
        $this->assertSame(6, $currentPage);

        // Test 'prev' operation
        $currentPage = $fileService->handelPagination('prev', 10, 5);
        $this->assertSame(4, $currentPage);

        // Test 'next' operation at last page
        $currentPage = $fileService->handelPagination('next', 10, 10);
        $this->assertSame(10, $currentPage);

        // Test 'prev' operation at first page
        $currentPage = $fileService->handelPagination('prev', 10, 1);
        $this->assertSame(1, $currentPage);
    }


    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testLoadFile_SuccessResponse()
    {
        $path = $this->existent_path;
        $page = 2;
        $operation = 'next';

        $response = $this->get("/api/load-file?path=$path&page=$page&operation=$operation");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'State',
            'status',
            'data'
        ]);
    }

    public function testLoadFile_FileNotFoundResponse()
    {
        $path = $this->nonexistent_path;
        $page = 1;
        $operation = 'next';

        $response = $this->get("/api/load-file?path=$path&page=$page&operation=$operation");

        $response->assertStatus(404);

        $response->assertJsonStructure([
            'State',
            'message',
            'status',
        ]);
    }
}
