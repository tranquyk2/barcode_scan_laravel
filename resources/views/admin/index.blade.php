@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Quản lý người dùng</h1>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Tên</th>
                <th>Email</th>
                <th>Role</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
            <tr>
                <td>{{ $user->id }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->role }}</td>
                <td>
                    <a href="{{ route('admin.edit_user', $user->id) }}" class="btn btn-sm btn-warning">Sửa</a>
                    <form action="{{ route('admin.delete_user', $user->id) }}" method="POST" style="display:inline-block">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Xóa user này?')">Xóa</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <hr>
    <h2>Lịch sử quét barcode</h2>
    <form method="GET" action="{{ route('admin.histories') }}" class="mb-3 row">
        <div class="col-md-3 mb-2 mb-md-0">
            <input type="text" name="barcode" class="form-control" placeholder="Tìm barcode..." value="{{ request('barcode') }}">
        </div>
        <div class="col-md-3 mb-2 mb-md-0">
            <select name="user_id" class="form-control">
                <option value="">-- Tất cả user --</option>
                @foreach($users as $u)
                    <option value="{{ $u->id }}" @if(request('user_id') == $u->id) selected @endif>{{ $u->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2 mb-2 mb-md-0">
            <input type="date" name="from_date" class="form-control mb-1" placeholder="Từ ngày" value="{{ request('from_date') }}">
            <input type="date" name="to_date" class="form-control" placeholder="Đến ngày" value="{{ request('to_date') }}">
        </div>
        <div class="col-md-2 mb-2 mb-md-0">
            <button type="submit" class="btn btn-info w-100">Tìm kiếm</button>
        </div>
        <div class="col-md-2">
            <a href="{{ route('admin.export_histories', request()->all()) }}" class="btn btn-success w-100">Xuất Excel</a>
        </div>
    </form>
    <div class="table-responsive">
    <table class="table table-striped table-bordered table-hover rounded shadow-sm align-middle">
        <thead class="table-primary text-center">
            <tr>
                <th>ID</th>
                <th>User</th>
                <th>Barcode 1</th>
                <th>Barcode 2</th>
                <th>Số lượng</th>
                <th>Thời gian</th>
                <th>Kết quả</th>

            </tr>
        </thead>
        <tbody>
            @foreach($histories as $h)
            <tr>
                <td class="text-center">{{ $h->id }}</td>
                <td>{{ $h->user->name ?? '' }}</td>
                <td class="text-break">{{ $h->barcode1 ?? '' }}</td>
                <td class="text-break">{{ $h->barcode2 ?? '' }}</td>
                <td class="text-center">{{ $h->quantity }}</td>
                <td class="text-center">{{ $h->created_at }}</td>
                <td class="text-center">
                    @if(strtoupper($h->result) === 'PASS')
                        <span class="badge bg-success fs-6">PASS</span>
                    @elseif(strtoupper($h->result) === 'FAIL')
                        <span class="badge bg-danger fs-6">FAIL</span>
                    @else
                        <span class="badge bg-secondary fs-6">{{ $h->result }}</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    </div>
    <div class="d-flex justify-content-center my-3">
        {!! $histories->withQueryString()->links('pagination::bootstrap-5') !!}
    </div>
</div>
@endsection
