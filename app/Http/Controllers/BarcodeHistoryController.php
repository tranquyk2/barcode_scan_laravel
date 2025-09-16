<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BarcodeHistory;
use Maatwebsite\Excel\Facades\Excel;

class BarcodeHistoryController extends Controller
{
    public function export(Request $request)
    {
        $userId = auth()->id();
        $query = BarcodeHistory::where('user_id', $userId);

        if ($request->filled('date')) {
            $date = $request->input('date');
            $query->whereDate('created_at', $date);
        } elseif ($request->filled('month')) {
            // month dạng yyyy-mm
            $month = $request->input('month');
            $parts = explode('-', $month);
            if (count($parts) === 2) {
                $query->whereYear('created_at', $parts[0])->whereMonth('created_at', $parts[1]);
            }
        }

        $data = $query->orderByDesc('id')->get();

        $exportData = $data->map(function($item) {
            return [
                'Thời gian' => $item->created_at->format('d/m/Y H:i:s'),
                'Barcode 1' => (string)$item->barcode1,
                'Barcode 2' => (string)$item->barcode2,
                'Số lượng' => (string)$item->quantity,
                'Kết quả' => $item->result,
            ];
        });

        return Excel::download(new \App\Exports\BarcodeHistoryExport($exportData), 'barcode_history.xlsx');
    }

    public function index(Request $request)
    {
        $this->checkApiKey($request);
        $user = $request->user();
        $histories = BarcodeHistory::where('user_id', $user->id)->orderByDesc('id')->get();
        return response()->json($histories);
    }

    public function store(Request $request)
    {
        // Nếu là API (request expects JSON hoặc có header X-API-KEY)
        if ($request->expectsJson() || $request->header('X-API-KEY')) {
            $this->checkApiKey($request);
            $validated = $request->validate([
                'barcode1' => 'required|string',
                'barcode2' => 'required|string',
                'quantity' => 'required|integer',
                'result' => 'required|string',
                'time' => 'nullable|date',
            ]);
            $validated['user_id'] = $request->user()->id;
            $history = BarcodeHistory::create($validated);
            return response()->json(['success' => true, 'data' => $history], 201);
        } else {
            // Xử lý cho web (form)
            $validated = $request->validate([
                'barcode1' => 'required|string',
                'barcode2' => 'required|string',
                'quantity' => 'required|integer',
            ]);
            $result = ($validated['barcode1'] === $validated['barcode2'] || strpos($validated['barcode1'], $validated['barcode2']) !== false || strpos($validated['barcode2'], $validated['barcode1']) !== false) ? 'PASS' : 'FAIL';
            $history = BarcodeHistory::create([
                'barcode1' => $validated['barcode1'],
                'barcode2' => $validated['barcode2'],
                'quantity' => $validated['quantity'],
                'result' => $result,
                'user_id' => auth()->id(),
                'time' => now(),
            ]);
            return redirect()->route('barcode.index')->with('scan_result', $result);
        }
    }
}