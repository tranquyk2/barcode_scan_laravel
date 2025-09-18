<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\BarcodeHistory;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BarcodeHistoryExport;

class AdminController extends Controller
{
    
    public function index(Request $request)
    {
        $users = User::all();
        $query = BarcodeHistory::with('user')->orderByDesc('id');
        if ($request->barcode) {
            $barcode = $request->barcode;
            $query->where(function($q) use ($barcode) {
                $q->where('barcode1', 'like', "%$barcode%")
                  ->orWhere('barcode2', 'like', "%$barcode%") ;
            });
        }
        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->month) {
            // month dạng yyyy-mm
            $parts = explode('-', $request->month);
            if (count($parts) === 2) {
                $query->whereYear('created_at', $parts[0])->whereMonth('created_at', $parts[1]);
            }
        }
        $histories = $query->paginate(30);
        return view('admin.index', compact('users', 'histories'));
    }

    
    public function create()
    {
        return view('admin.create_user');
    }

    
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role' => 'required|in:admin,staff',
        ]);
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);
        return redirect()->route('admin.index')->with('success', 'Tạo user thành công');
    }

    
    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('admin.edit_user', compact('user'));
    }

    
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $id,
            'role' => 'required|in:admin,staff',
        ]);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        $user->save();
        return redirect()->route('admin.index')->with('success', 'Cập nhật user thành công');
    }

    
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return redirect()->route('admin.index')->with('success', 'Xóa user thành công');
    }

    
    public function showHistory(Request $request)
    {
        $query = BarcodeHistory::with('user')->orderByDesc('id');
        if ($request->barcode) {
            $query->where('barcode', 'like', '%' . $request->barcode . '%');
        }
        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }
        $histories = $query->paginate(30);
        $users = User::all();
        return view('admin.histories', compact('histories', 'users'));
    }

    
    public function exportHistory(Request $request)
    {
        $query = BarcodeHistory::query();
        if ($request->barcode) {
            $barcode = $request->barcode;
            $query->where(function($q) use ($barcode) {
                $q->where('barcode1', 'like', "%$barcode%")
                  ->orWhere('barcode2', 'like', "%$barcode%") ;
            });
        }
        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->date) {
            $query->whereDate('created_at', $request->date);
        }
        if ($request->month) {
            $parts = explode('-', $request->month);
            if (count($parts) === 2) {
                $query->whereYear('created_at', $parts[0])->whereMonth('created_at', $parts[1]);
            }
        }
        $histories = $query->get();
        return Excel::download(new BarcodeHistoryExport($histories), 'barcode_histories.xlsx');
    }
}
