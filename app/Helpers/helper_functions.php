<?php

$helper_files = [
    'app/Helpers/String/functions.php',
];

foreach ($helper_files as $file) {
    $file = dirname(dirname(__DIR__)).'/'.$file;
    if (file_exists($file)) {
        require $file;
    }
}


if (! function_exists('calculatePagination')) {
    /**
     * @param $request
     * @return array
     */
    function calculatePagination($request): array
    {
        $offset = $request->has('offset') ? $request->offset : 0;
        $limit = $request->has('limit') ? $request->limit : 50;

        return [$offset, $limit];
    }
}
