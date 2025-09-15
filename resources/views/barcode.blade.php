@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2 class="mb-4 text-center">🔍 Barcode Scanner Pro</h2>
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form id="barcodeForm" method="POST" action="{{ route('barcode.store') }}" class="card p-4 shadow-sm mb-4">
        @csrf
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="barcode1" class="form-label">Barcode 1</label>
                <input type="text" class="form-control" id="barcode1" name="barcode1" required autofocus>
            </div>
            <div class="col-md-4">
                <label for="barcode2" class="form-label">Barcode 2</label>
                <input type="text" class="form-control" id="barcode2" name="barcode2" required>
            </div>
            <div class="col-md-4">
                <label for="quantity" class="form-label">Số lượng</label>
                <input type="number" class="form-control" id="quantity" name="quantity" required min="1">
            </div>
        </div>
        <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-primary" style="display:none">Quét & Lưu</button>
        </div>
    </form>

    <!-- Form xuất Excel -->
    <form method="GET" action="{{ route('barcode.export') }}" class="mb-4 card p-3 shadow-sm">
        <div class="row align-items-end">
            <div class="col-md-4">
                <label for="date" class="form-label">Chọn ngày</label>
                <input type="date" class="form-control" id="date" name="date">
            </div>
            <div class="col-md-4">
                <label for="month" class="form-label">Chọn tháng</label>
                <input type="month" class="form-control" id="month" name="month">
            </div>
            <div class="col-md-4 d-flex justify-content-end">
                <button type="submit" class="btn btn-success">Xuất Excel</button>
            </div>
        </div>
    </form>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const b1 = document.getElementById('barcode1');
        const b2 = document.getElementById('barcode2');
        const qty = document.getElementById('quantity');
        const form = document.getElementById('barcodeForm');

        setTimeout(() => b1.focus(), 100);

        b1.addEventListener('input', () => {
            if (b1.value.length > 8) setTimeout(() => b2.focus(), 300);
        });
        b2.addEventListener('input', () => {
            if (b2.value.length > 8) setTimeout(() => qty.focus(), 300);
        });
        qty.addEventListener('input', () => {
            // Tự động lấy số lượng đúng khi nhập
            const extracted = qty.value.match(/QTY[:\- ]?(\d+)/i)?.[1] || qty.value.match(/(\d+)(?!.*\d)/)?.[1] || '';
            if (extracted !== qty.value) qty.value = extracted;
            if (b1.value.length > 0 && b2.value.length > 0 && extracted.length > 0 && !isNaN(extracted)) {
                form.submit();
            }
        });
    });
    </script>

    <h3 class="mt-4">Lịch sử quét của bạn</h3>
    <div class="table-responsive">
        <table class="table table-bordered table-striped mt-2">
            <thead class="table-light">
                <tr>
                    <th>Thời gian</th>
                    <th>Barcode 1</th>
                    <th>Barcode 2</th>
                    <th>Số lượng</th>
                    <th>Kết quả</th>
                </tr>
            </thead>
            <tbody>
                @forelse($histories as $item)
                <tr>
                    <td>{{ $item->created_at->format('d/m/Y H:i:s') }}</td>
                    <td>{{ $item->barcode1 }}</td>
                    <td>{{ $item->barcode2 }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>
                        @if($item->result === 'PASS')
                            <span class="badge bg-success">PASS</span>
                        @else
                            <span class="badge bg-danger">FAIL</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted">Chưa có lịch sử quét.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
