<?php

namespace App\Http\Controllers;

use App\Http\Helper\FormatResponse;
use App\Http\Resources\UIGMRMetriksResource;
use App\Models\UIGMRMetriks;
use Illuminate\Http\Request;

class UIGMRMetriksController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = UIGMRMetriks::query()->with('metriks');

        // search by name
        if ($request->has('nama_metriks_lengkap')) {
            $query->where('nama_metriks_lengkap', 'like', '%' . $request->input('nama_metriks_lengkap') . '%');
        }

        // Search across multiple fields
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%$search%")
                    ->orWhere('nama_metriks_lengkap', 'like', "%$search%")
                    ->orWhere('nama_metriks_singkat', 'like', "%$search%");
            });
        }

        return FormatResponse::formatResponse($query, UIGMRMetriksResource::class, $request);
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
            'nama_metriks_lengkap' => ['required', 'string'],
            'nama_metriks_singkat' => ['required', 'string'],
        ]);

        $uigmr = UIGMRMetriks::create([
            'nama_metriks_lengkap' => $validated['nama_metriks_lengkap'],
            'nama_metriks_singkat' => $validated['nama_metriks_singkat'],
        ]);

        return response()->json([
            'message' => 'Data has been created successfully',
            'data' => new UIGMRMetriksResource($uigmr)
        ], 201);
    }

    // public function import(Request $request)
    // {
    //     $request->validate([
    //         'file' => ['required', 'mimes:xlsx']
    //     ]);

    //     $file = $request->file('file');
    //     $reader = ReaderEntityFactory::createXLSXReader();
    //     $reader->open($file->getPathname());

    //     $header = ['nama_universitas', 'id_metriks', 'score', 'peringkat_dunia'];
    //     $isFirstRow = true;

    //     set_time_limit(0);

    //     foreach ($reader->getSheetIterator() as $sheet) {
    //         foreach ($sheet->getRowIterator() as $row) {
    //             $cells = $row->toArray();

    //             if ($isFirstRow) {
    //                 $header = $cells;
    //                 $isFirstRow = false;
    //                 continue;
    //             }

    //             $data = array_combine($header, $cells);

    //             // Additional validation before creating data
    //             $validator = Validator::make($data, [
    //                 'nama_universitas' => ['required', 'string'],
    //                 'id_metriks' => ['required', 'exists:uigm_r_metriks,id'],
    //                 'score' => ['required', 'numeric', 'min:0'],
    //                 'peringkat_dunia' => ['required', 'numeric', 'min:0'],
    //             ]);

    //             if ($validator->fails()) {
    //                 return response()->json([
    //                     'status' => 'failed',
    //                     'message' => 'Data failed to insert, check data input or try again.'
    //                 ], 400);
    //             }

    //             UIGMRMetriks::create([
    //                 'nama_universitas' => $data['nama_universitas'],
    //                 'id_metriks' => $data['id_metriks'],
    //                 'score' => $data['score'],
    //                 'peringkat_dunia' => $data['peringkat_dunia'],
    //             ]);
    //         }
    //     }

    //     $reader->close();

    //     return response()->json([
    //         'status' => 'success',
    //         'message' => 'Data has been imported successfully'
    //     ], 201);
    // }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $uigmr = UIGMRMetriks::find($id);
        if (!$uigmr) {
            return response()->json([
                'message' => 'Data not found'
            ], 404);
        }
        return response()->json([
            'status' => 'success',
            'message' => 'Data has been retrieved successfully',
            'data' => new UIGMRMetriksResource($uigmr)
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
        $uigmr = UIGMRMetriks::find($id);
        if (!$uigmr) {
            return response()->json([
                'message' => 'Data not found'
            ], 404);
        }
        $validated = $request->validate([
            'nama_metriks_lengkap' => ['required', 'string'],
            'nama_metriks_singkat' => ['required', 'string'],
        ]);

        $uigmr->update([
            'nama_metriks_lengkap' => $validated['nama_metriks_lengkap'],
            'nama_metriks_singkat' => $validated['nama_metriks_singkat'],
        ]);

        return response()->json([
            'message' => 'Data has been updated successfully',
            'data' => new UIGMRMetriksResource($uigmr)
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $uigmr = UIGMRMetriks::find($id);
        if (!$uigmr) {
            return response()->json([
                'message' => 'Data not found'
            ], 404);
        }
        $uigmr->delete();
        return response()->json([
            'message' => 'Data has been deleted successfully'
        ], 200);
    }

    /**
     * Show removed the specified resource from storage (Soft delete).
     */
    public function showSoftDelete()
    {
        $uigmr = UIGMRMetriks::onlyTrashed()->get();
        if (!$uigmr) {
            return response()->json([
                'message' => 'Data not found'
            ], 404);
        }
        return response()->json([
            'status' => 'success',
            'message' => 'Data has been retrieved successfully',
            'data' => $uigmr
        ], 200);
    }

    /**
     * Restore removed the specified resource from storage (Soft delete).
     */
    public function restoreSoftDelete($id)
    {
        $uigmr = UIGMRMetriks::onlyTrashed()->where('id', $id)->first();
        if (!$uigmr) {
            return response()->json([
                'message' => 'Data not found'
            ], 404);
        }
        $uigmr->restore();
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
        $uigmr = UIGMRMetriks::onlyTrashed()->where('id', $id)->first();
        if (!$uigmr) {
            return response()->json([
                'message' => 'Data not found'
            ], 404);
        }
        $uigmr->forceDelete();
        return response()->json([
            'message' => 'Data has been deleted from database successfully'
        ], 200);
    }

}