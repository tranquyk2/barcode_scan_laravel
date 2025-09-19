<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BarcodeHistory;

class BarcodeApiController extends Controller
{
    // Lấy lịch sử quét của user đang đăng nhập
    public function myHistory(Request $request)
    {
        $user = $request->user();
        $histories = BarcodeHistory::where('user_id', $user->id)->orderByDesc('id')->get();
        return response()->json($histories);
    }

    // Quét barcode (API)
    public function scan(Request $request)
    {
        $validated = $request->validate([
            'barcode1' => 'required|string',
            'barcode2' => 'required|string',
            'quantity' => 'required|integer|max:99999',
        ], [
            'quantity.max' => 'Số lượng không được lớn hơn :max.',
        ]);
        $result = ($validated['barcode1'] === $validated['barcode2'] || strpos($validated['barcode1'], $validated['barcode2']) !== false || strpos($validated['barcode2'], $validated['barcode1']) !== false) ? 'PASS' : 'FAIL';
        $history = BarcodeHistory::create([
            'barcode1' => $validated['barcode1'],
            'barcode2' => $validated['barcode2'],
            'quantity' => $validated['quantity'],
            'result' => $result,
            'user_id' => $request->user()->id,
            'time' => now(),
        ]);
        return response()->json(['result' => $result, 'history' => $history]);
    }
}
