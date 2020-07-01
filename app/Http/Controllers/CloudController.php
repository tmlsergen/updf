<?php

namespace App\Http\Controllers;

use App\Models\Cloud;
use Illuminate\Http\Request;
use Storage;

class CloudController extends Controller
{
    public function pdfs(Request $request)
    {
        $pdfs = $request->user()->clouds()->get(['pdf','code']);

        $resp = array();

        foreach ($pdfs as $pdf) {
            if ($pdf->pdf)
                $resp[] = [$pdf->code ,Storage::disk('s3')->temporaryUrl($pdf->pdf, now()->addMinutes(5))];
        }

        return response()->json([
            'data' => $resp,
            'message' => __('Success')
        ], 200);
    }

    public function code($code)
    {
        $cloud = Cloud::where('code', $code)->first();

        $resp = [
            'pdf' => Storage::disk('s3')->temporaryUrl($cloud->pdf, now()->addMinutes(5)),
            'svg' => Storage::disk('s3')->temporaryUrl($cloud->svg, now()->addMinutes(5)),
            //'jpg' => Storage::disk('s3')->temporaryUrl($cloud->jpg, now()->addMinutes(5)),
            //'png' => Storage::disk('s3')->temporaryUrl($cloud->png, now()->addMinutes(5)),
            //'spng' => Storage::disk('s3')->temporaryUrl($cloud->spng, now()->addMinutes(5)),
            //'sjpg' => Storage::disk('s3')->temporaryUrl($cloud->sjpg, now()->addMinutes(5))
        ];

        return response()->json([
            'data' => $resp,
            'message' => __('Success')
        ], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'html' => 'required',
            'head' => 'nullable'
        ]);

        $cloud = Cloud::startProcess($request);

        return response()->json([
            'data' => [
                "code" => $cloud['code'],
            ],
            "message" => __('The word cloud is saved.')
        ]);
    }
}
