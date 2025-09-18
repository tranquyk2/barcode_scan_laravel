@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Sửa thông tin User</h2>
    <form method="POST" action="{{ route('admin.update_user', $user->id) }}">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="name" class="form-label">Tên</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $user->name) }}" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $user->email) }}" required>
        </div>
        <div class="mb-3">
            <label for="role" class="form-label">Quyền</label>
            <select class="form-control" id="role" name="role" required>
                <option value="admin" @if($user->role=='admin') selected @endif>Admin</option>
                <option value="staff" @if($user->role=='staff') selected @endif>Nhân viên</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Mật khẩu mới (bỏ qua nếu không đổi)</label>
            <input type="password" class="form-control" id="password" name="password">
        </div>
        <button type="submit" class="btn btn-primary">Cập nhật</button>
        <a href="{{ route('admin.index') }}" class="btn btn-secondary">Quay lại</a>
    </form>
</div>
@endsection
