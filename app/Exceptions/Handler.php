public function render($request, Throwable $exception)
{
    // Kalau request API, balikin JSON
    if ($request->is('api/*')) {
        return response()->json([
            'success' => false,
            'error' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine()
        ], 500);
    }
    
    return parent::render($request, $exception);
}