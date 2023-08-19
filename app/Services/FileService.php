<?php
namespace App\Services;


class FileService
{

    const LINES_PER_PAGE = 10;

    /**
     * Load and return file content based on the provided parameters.
     *
     * @param string $path The path to the log file.
     * @param string $operation The operation to perform ('first', 'last', 'next', 'prev').
     * @param int $currentPage The current page number.
     * @return \Illuminate\Http\JsonResponse The response containing content and pagination information.
     * @throws \Exception If there's an issue opening the file.
     */


    public function loadfileContent($path, $operation,$currentPage)
    {

        if (!$this->fileExists($path)) {
            return response()->json([
                'State'=>'Error',
                'message'=>'Log file not found.',
                'status'=>404
            ],404);
        }

        $totalPages = $this->calculateTotalPages($path);
        $currentPage = $this->handelPagination($operation,$totalPages,$currentPage);

        $logContent = $this->getFileContent($path, $currentPage);

        return response()->json([
            'State'=>'Success',
            'status'=>200,
            'data'=>[
                'content' => $logContent,
                'currentPage' => $currentPage,
                'totalPages' => $totalPages,
            ],
        ]);

    }

    /**
     * Check if the file exists.
     *
     * @param string $path The path to the file.
     * @return bool True if the file exists; false otherwise.
     */

    protected function fileExists($path)
    {
        return file_exists($path);
    }

    /**
     * Handle pagination operation and calculate the new current page.
     *
     * @param string $operation The pagination operation.
     * @param int $totalPages The total number of pages.
     * @param int $currentPage The current page number.
     * @return int The new current page number.
     */

    public function handelPagination($operation,$totalPages,$currentPage)
    {
        if($operation === 'first'){
            $currentPage = 1;
        } elseif ($operation === 'last') {
            $currentPage = $totalPages;
        } elseif ($operation === 'next') {
            $currentPage = min($currentPage + 1, $totalPages);
        } elseif ($operation === 'prev') {
            $currentPage = max($currentPage - 1, 1);
        }

        return $currentPage;
    }

    /**
     * Read content from the file and return it as a generator.
     *
     * @param string $path The path to the log file.
     * @param int $currentPage The current page number.
     * @return Generator A generator yielding each line of content.
     * @throws \Exception If there's an issue opening the file.
     */

    protected function readContentFile($path,$currentPage)
    {

        $file = fopen($path, 'r');
        if (!$file) {
            throw new \Exception('Unable to open log file.');
        }

        try {
            $startIndex = ($currentPage - 1) * self::LINES_PER_PAGE;
            $lineCount = 0;

            while ($line = fgets($file)) {
                if ($lineCount >= $startIndex) {
                    yield $line;
                }
                $lineCount++;
                if ($lineCount >= $startIndex + self::LINES_PER_PAGE) {
                    break;
                }
            }
        } finally {
            fclose($file);
        }
    }

    /**
     * Get the total number of lines in the file.
     *
     * @param string $path The path to the file.
     * @return int The total number of lines.
     * @throws \Exception If there's an issue opening the file.
     */

    public function getTotalLines($path)
    {
        $file = fopen($path, 'r');
        if (!$file) {
            throw new \Exception('Unable to open file: ' . $path);
        }

        try {
            $lineCount = 0;
            while (fgets($file)) {
                $lineCount++;
            }
            return $lineCount;
        } finally {
            fclose($file);
        }
    }


    /**
     * Calculate the total number of pages based on the total lines and lines per page.
     *
     * @param string $path The path to the file.
     * @return int The total number of pages.
     * @throws \Exception If there's an issue opening the file.
     */

    private function calculateTotalPages($path)
    {
        $totalLines = $this->getTotalLines($path);
        return max(1, ceil($totalLines / self::LINES_PER_PAGE));
    }


    /**
     * Get file content for the specified page.
     *
     * @param string $path The path to the file.
     * @param int $currentPage The current page number.
     * @return array The log content for the specified page.
     * @throws \Exception If there's an issue opening the file.
     */

    protected function getFileContent($path, $currentPage)
    {
        $content = [];

        foreach ($this->readContentFile($path,$currentPage) as $index => $line) {
                $content[] = $line;
        }

        return $content;
    }
}
