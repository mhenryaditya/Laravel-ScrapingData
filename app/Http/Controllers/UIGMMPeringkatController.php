<?php

namespace App\Http\Controllers;

use App\Http\Helper\FormatResponse;
use App\Http\Resources\UIGMMPeringkatResource;
use App\Models\UIGMMPeringkat;
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
                    ->orWhere('score', 'like', "%$search%")
                    ->orWhere('peringkat_dunia', 'like', "%$search%")
                    ->orWhere(function ($q) use ($search) {
                        $q->where('nama_metriks_lengkap', 'like', "%$search%")
                            ->orWhere('nama_metriks_singkat', 'like', "%$search%");
                    });
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
            'id_metriks' => ['required', 'exists:uigm_r_metriks,id'],
            'score' => ['required', 'numeric', 'min:0'],
            'peringkat_dunia' => ['required', 'numeric', 'min:0'],
        ]);

        $uigmm = UIGMMPeringkat::create([
            'nama_universitas' => $validated['nama_universitas'],
            'id_metriks' => $validated['id_metriks'],
            'score' => $validated['score'],
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

        $header = ['nama_universitas', 'id_metriks', 'score', 'peringkat_dunia'];
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
                    'id_metriks' => ['required', 'exists:uigm_r_metriks,id'],
                    'score' => ['required', 'numeric', 'min:0'],
                    'peringkat_dunia' => ['required', 'numeric', 'min:0'],
                ]);

                if ($validator->fails()) {
                    return response()->json([
                        'status' => 'failed',
                        'message' => 'Data failed to insert, check data input or try again.'
                    ], 400);
                }

                UIGMMPeringkat::create([
                    'nama_universitas' => $data['nama_universitas'],
                    'id_metriks' => $data['id_metriks'],
                    'score' => $data['score'],
                    'peringkat_dunia' => $data['peringkat_dunia'],
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
            'id_metriks' => ['required', 'exists:uigm_r_metriks,id'],
            'score' => ['required', 'numeric', 'min:0'],
            'peringkat_dunia' => ['required', 'numeric', 'min:0'],
        ]);

        $uigmm->update([
            'nama_universitas' => $validated['nama_universitas'],
            'id_metriks' => $validated['id_metriks'],
            'score' => $validated['score'],
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