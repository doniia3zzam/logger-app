<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\FileService;
use Illuminate\Http\Request;

class FileController extends Controller
{
    protected $fileService;

    /**
     * FileController constructor.
     *
     * @param FileService $fileService An instance of the FileService.
     */

    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }

    /**
     * Display the content of a file based on the provided parameters.
     *
     * @param Request $request The HTTP request.
     * @return \Illuminate\Http\JsonResponse The response containing log content and pagination information.
     * @throws \Exception If there's an issue during the file content loading process.
     */

    public function viewFile(Request $request)
    {
        // Get input parameters from the request
        $path = $request->input('path');
        $currentPage = $request->input('page',1); // Default to '1'
        $operation = $request->input('operation', 'first'); // Default to 'first'

        // Load file content using the FileService
        $data = $this->fileService->loadfileContent($path, $operation,$currentPage);

        return $data;
    }
}
