<?php

namespace App\Http\Controllers;

use App\Http\Helper\FormatResponse;
use App\Http\Resources\SintaMPeringkatResource;
use App\Models\SintaMPeringkat;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SintaMPeringkatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = SintaMPeringkat::query();

        // search by name
        if ($request->has('nama_universitas')) {
            $query->where('nama_universitas', 'like', '%' . $request->input('nama_universitas') . '%');
        }

        // Filtering by minimum column value
        if ($request->has('score_min_name')) {
            $query->where($request->input('score_min_name'), '>=', $request->input('sinta_score'));
        }

        // Filtering by maximum column value
        if ($request->has('score_max_name')) {
            $query->where($request->input('score_max_name'), '<=', $request->input('sinta_score'));
        }

        // Search across multiple fields
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%$search%")
                    ->orWhere('nama_universitas', 'like', "%$search%")
                    ->orWhere('sinta_score_3_yr', 'like', "%$search%")
                    ->orWhere('sinta_score_overall', 'like', "%$search%");
            });
        }

        return FormatResponse::formatResponse($query, SintaMPeringkatResource::class, $request);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_universitas' => ['required', 'string'],
            'sinta_score_3_yr' => ['required', 'numeric', 'min:0'],
            'sinta_score_overall' => ['required', 'numeric', 'min:0'],
        ]);

        $sinta = SintaMPeringkat::create([
            'nama_universitas' => $validated['nama_universitas'],
            'sinta_score_3_yr' => $validated['sinta_score_3_yr'],
            'sinta_score_overall' => $validated['sinta_score_overall'],
        ]);

        return response()->json([
            'message' => 'Data has been created successfully',
            'data' => new SintaMPeringkatResource($sinta)
        ], 201);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required', 'mimes:xlsx']
        ]);

        $file = $request->file('file');
        $reader = ReaderEntityFactory::createXLSXReader();
        $reader->open($file->getPathname());

        $header = ['nama_universitas', 'sinta_score_3_yr', 'sinta_score_overall'];
        $isFirstRow = true;

        set_time_limit(0);

        foreach ($reader->getSheetIterator() as $sheet) {
            foreach ($sheet->getRowIterator() as $row) {
                $cells = $row->toArray();

                if ($isFirstRow) {
                    $header = $cells;
                    $isFirstRow = false;
                    continue;
                }

                $data = array_combine($header, $cells);

                // Additional validation before creating data
                $validator = Validator::make($data, [
                    'nama_universitas' => ['required', 'string'],
                    'sinta_score_3_yr' => ['required', 'numeric', 'min:0'],
                    'sinta_score_overall' => ['required', 'numeric', 'min:0'],
                ]);

                if ($validator->fails()) {
                    return response()->json([
                        'status' => 'failed',
                        'message' => 'Data failed to insert, check data input or try again.'
                    ], 400);
                }

                SintaMPeringkat::create([
                    'nama_universitas' => $data['nama_universitas'],
                    'sinta_score_3_yr' => $data['sinta_score_3_yr'],
                    'sinta_score_overall' => $data['sinta_score_overall'],
                ]);
            }
        }

        $reader->close();

        return response()->json([
            'status' => 'success',
            'message' => 'Data has been imported successfully'
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $sinta = SintaMPeringkat::find($id);
        if (!$sinta) {
            return response()->json([
                'message' => 'Data not found'
            ], 404);
        }
        return response()->json([
            'status' => 'success',
            'message' => 'Data has been retrieved successfully',
            'data' => new SintaMPeringkatResource($sinta)
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $sinta = SintaMPeringkat::find($id);
        if (!$sinta) {
            return response()->json([
                'message' => 'Data not found'
            ], 404);
        }
        $validated = $request->validate([
            'nama_universitas' => ['required', 'string'],
            'sinta_score_3_yr' => ['required', 'numeric', 'min:0'],
            'sinta_score_overall' => ['required', 'numeric', 'min:0'],
        ]);

        $sinta->update([
            'nama_universitas' => $validated['nama_universitas'],
            'sinta_score_3_yr' => $validated['sinta_score_3_yr'],
            'sinta_score_overall' => $validated['sinta_score_overall'],
        ]);

        return response()->json([
            'message' => 'Data has been updated successfully',
            'data' => new SintaMPeringkatResource($sinta)
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $sinta = SintaMPeringkat::find($id);
        if (!$sinta) {
            return response()->json([
                'message' => 'Data not found'
            ], 404);
        }
        $sinta->delete();
        return response()->json([
            'message' => 'Data has been deleted successfully'
        ], 200);
    }

    /**
     * Show removed the specified resource from storage (Soft delete).
     */
    public function showSoftDelete()
    {
        $sinta = SintaMPeringkat::onlyTrashed()->get();
        if (!$sinta) {
            return response()->json([
                'message' => 'Data not found'
            ], 404);
        }
        return response()->json([
            'status' => 'success',
            'message' => 'Data has been retrieved successfully',
            'data' => $sinta
            // 'data' => new SintaMPeringkatResource($sinta)
        ], 200);
    }

    /**
     * Restore removed the specified resource from storage (Soft delete).
     */
    public function restoreSoftDelete($id)
    {
        $sinta = SintaMPeringkat::onlyTrashed()->where('id', $id)->first();
        if (!$sinta) {
            return response()->json([
                'message' => 'Data not found'
            ], 404);
        }
        $sinta->restore();
        return response()->json([
            'status' => 'success',
            'message' => 'Data has been restored successfully',
        ], 200);
    }

    /**
     * Permanently removed the specified resource from storage (Soft delete).
     */
    public function permanetDelSoftDelete(string $id)
    {
        $sinta = SintaMPeringkat::onlyTrashed()->where('id', $id)->first();
        if (!$sinta) {
            return response()->json([
                'message' => 'Data not found'
            ], 404);
        }
        $sinta->forceDelete();
        return response()->json([
            'message' => 'Data has been deleted from database successfully'
        ], 200);
    }

}
