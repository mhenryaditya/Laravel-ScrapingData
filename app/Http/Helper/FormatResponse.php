<?php

namespace App\Http\Helper;

class FormatResponse
{
    public static function formatResponse($query, $resource, $request, $pageLength = 10)
    {
        $pageLength = $request->input('pageLength', 10);
        $data = $query->paginate($pageLength);

        if ($data->isEmpty()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Nothing data found',
                'data' => [],
                'pagination' => [
                    'current_page' => $data->currentPage(),
                    'last_page' => $data->lastPage(),
                    'per_page' => $data->perPage(),
                    'total' => $data->total(),
                ]
            ], 200);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Lists of data have been retrieved successfully',
            'data' => $resource::collection($data),
            'pagination' => [
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
            ]
        ], 200);
    }
}