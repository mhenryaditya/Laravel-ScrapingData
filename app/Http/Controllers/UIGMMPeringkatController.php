<?php

namespace App\Http\Controllers;

use App\Http\Helper\FormatResponse;
use App\Http\Resources\UIGMMPeringkatResource;
use App\Models\UIGMMPeringkat;
use App\Models\UIGMRMetriks;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UIGMMPeringkatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = UIGMMPeringkat::query()->with('metriks');

        // search by name
        if ($request->has('nama_universitas')) {
            $query->where('nama_universitas', 'like', '%' . $request->input('nama_universitas') . '%');
        }

        // Filtering by minimum column value
        if ($request->has('skor_min_name')) {
            $query->where($request->input('skor_min_name'), '>=', $request->input('sinta_skor'));
        }

        // Filtering by maximum column value
        if ($request->has('skor_max_name')) {
            $query->where($request->input('skor_max_name'), '<=', $request->input('sinta_skor'));
        }

        // Search across multiple fields, including related metriks columns
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%$search%")
                    ->orWhere('nama_universitas', 'like', "%$search%")
                    ->orWhere('skor', 'like', "%$search%")
                    ->orWhere('peringkat_dunia', 'like', "%$search%");
            })
                ->orWhereHas('metriks', function ($q) use ($search) {
                    $q->where('nama_metriks_lengkap', 'like', "%$search%")
                        ->orWhere('nama_metriks_singkat', 'like', "%$search%");
                });
        }

        return FormatResponse::formatResponse($query, UIGMMPeringkatResource::class, $request);
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
            'type_metriks' => ['required', 'exists:uigm_r_metriks,nama_metriks_singkat'],
            'skor' => ['required', 'numeric', 'min:0'],
            'peringkat_dunia' => ['required', 'numeric', 'min:0'],
        ]);

        $uigm_r_metriks = UIGMRMetriks::get(['id', 'nama_metriks_singkat']);

        $metriks = $uigm_r_metriks->firstWhere('nama_metriks_singkat', $validated['type_metriks']);

        $uigmm = UIGMMPeringkat::create([
            'nama_universitas' => $validated['nama_universitas'],
            'id_metriks' => $metriks->id,
            'skor' => $validated['skor'],
            'peringkat_dunia' => $validated['peringkat_dunia'],
        ]);

        return response()->json([
            'message' => 'Data has been created successfully',
            'data' => new UIGMMPeringkatResource($uigmm)
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

        set_time_limit(0);

        // Get all metric short names from the database
        $uigm_r_metriks = UIGMRMetriks::get(['id', 'nama_metriks_singkat']);
        $metricShortNames = $uigm_r_metriks->pluck('nama_metriks_singkat')->toArray();

        $isFirstRow = true;
        $header = [];

        foreach ($reader->getSheetIterator() as $sheet) {
            foreach ($sheet->getRowIterator() as $row) {
                $cells = $row->toArray();

                if ($isFirstRow) {
                    $header = $cells;
                    $isFirstRow = false;
                    continue;
                }

                $data = array_combine($header, $cells);

                // Build validation rules dynamically
                $rules = [
                    'peringkat_dunia' => ['required', 'numeric', 'min:0'],
                    'nama_universitas' => ['required', 'string'],
                ];
                foreach ($metricShortNames as $metricShort) {
                    $rules[$metricShort] = ['required', 'numeric', 'min:0'];
                }

                $validator = Validator::make($data, $rules);

                if ($validator->fails()) {
                    return response()->json([
                        'status' => 'failed',
                        'message' => 'Data failed to insert, check data input or try again.'
                    ], 400);
                }

                // For each metric column, create a record
                foreach ($metricShortNames as $metricShort) {
                    $metriks = $uigm_r_metriks->firstWhere('nama_metriks_singkat', $metricShort);
                    if ($metriks && isset($data[$metricShort])) {
                        UIGMMPeringkat::create([
                            'nama_universitas' => $data['nama_universitas'],
                            'id_metriks' => $metriks->id,
                            'skor' => $data[$metricShort],
                            'peringkat_dunia' => $data['peringkat_dunia'],
                        ]);
                    }
                }
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
        $uigmm = UIGMMPeringkat::find($id);
        if (!$uigmm) {
            return response()->json([
                'message' => 'Data not found'
            ], 404);
        }
        return response()->json([
            'status' => 'success',
            'message' => 'Data has been retrieved successfully',
            'data' => new UIGMMPeringkatResource($uigmm)
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
        $uigmm = UIGMMPeringkat::find($id);
        if (!$uigmm) {
            return response()->json([
                'message' => 'Data not found'
            ], 404);
        }
        $validated = $request->validate([
            'nama_universitas' => ['required', 'string'],
            'type_metriks' => ['required', 'exists:uigm_r_metriks,nama_metriks_singkat'],
            'skor' => ['required', 'numeric', 'min:0'],
            'peringkat_dunia' => ['required', 'numeric', 'min:0'],
        ]);

        $uigm_r_metriks = UIGMRMetriks::get(['id', 'nama_metriks_singkat']);

        $metriks = $uigm_r_metriks->firstWhere('nama_metriks_singkat', $validated['type_metriks']);

        $uigmm->update([
            'nama_universitas' => $validated['nama_universitas'],
            'id_metriks' => $metriks->id,
            'skor' => $validated['skor'],
            'peringkat_dunia' => $validated['peringkat_dunia'],
        ]);

        return response()->json([
            'message' => 'Data has been updated successfully',
            'data' => new UIGMMPeringkatResource($uigmm)
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $uigmm = UIGMMPeringkat::find($id);
        if (!$uigmm) {
            return response()->json([
                'message' => 'Data not found'
            ], 404);
        }
        $uigmm->delete();
        return response()->json([
            'message' => 'Data has been deleted successfully'
        ], 200);
    }

    /**
     * Show removed the specified resource from storage (Soft delete).
     */
    public function showSoftDelete()
    {
        $uigmm = UIGMMPeringkat::onlyTrashed()->get();
        if (!$uigmm) {
            return response()->json([
                'message' => 'Data not found'
            ], 404);
        }
        return response()->json([
            'status' => 'success',
            'message' => 'Data has been retrieved successfully',
            'data' => $uigmm
        ], 200);
    }

    /**
     * Restore removed the specified resource from storage (Soft delete).
     */
    public function restoreSoftDelete($id)
    {
        $uigmm = UIGMMPeringkat::onlyTrashed()->where('id', $id)->first();
        if (!$uigmm) {
            return response()->json([
                'message' => 'Data not found'
            ], 404);
        }
        $uigmm->restore();
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
        $uigmm = UIGMMPeringkat::onlyTrashed()->where('id', $id)->first();
        if (!$uigmm) {
            return response()->json([
                'message' => 'Data not found'
            ], 404);
        }
        $uigmm->forceDelete();
        return response()->json([
            'message' => 'Data has been deleted from database successfully'
        ], 200);
    }

}