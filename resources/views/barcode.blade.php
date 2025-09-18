    @if (session('scan_result'))
        <div id="scanResultAlert" style="position:fixed; top:30px; right:30px; z-index:9999; width:300px; height:414px; display:flex; align-items:center; justify-content:center; font-size:7rem; font-weight:900; letter-spacing:6px; text-align:center; box-shadow:0 2px 16px #0003; border:4px solid #fff; color:#fff; background:@if(session('scan_result')==='PASS') #218838 @else #c82333 @endif; border-radius:32px;">
            {{ session('scan_result') }}
        </div>
        <script>
        setTimeout(function() {
            var el = document.getElementById('scanResultAlert');
            if (el) el.style.display = 'none';
        }, 5000);
        </script>
    @endif
@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2 class="mb-4 text-center">🔍 Barcode Scanner</h2>
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
            <button id="saveBtn" type="submit" class="btn btn-primary" style="display:none">Quét & Lưu</button>
        </div>
    </form>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const b1 = document.getElementById('barcode1');
        const b2 = document.getElementById('barcode2');
        const qty = document.getElementById('quantity');
        const form = document.getElementById('barcodeForm');
        const saveBtn = document.getElementById('saveBtn');

        setTimeout(() => b1.focus(), 100);

        b1.addEventListener('input', () => {
            if (b1.value.length > 8) setTimeout(() => b2.focus(), 300);
        });
        b2.addEventListener('input', () => {
            if (b2.value.length > 8) setTimeout(() => qty.focus(), 300);
        });

        let lastInputTime = 0;
        let inputTimer = null;


        qty.addEventListener('input', (e) => {
            const now = Date.now();
            const extracted = qty.value.match(/QTY[:\- ]?(\d+)/i)?.[1] || qty.value.match(/(\d+)(?!.*\d)/)?.[1] || '';
            if (extracted !== qty.value) qty.value = extracted;

            // Chỉ tự động submit khi số lượng có từ 2 ký tự trở lên và nhập nhanh
            if (extracted.length >= 10 && lastInputTime && (now - lastInputTime < 300)) {
                if (b1.value.length > 0 && b2.value.length > 0 && !isNaN(extracted)) {
                    saveBtn.style.display = 'none';
                    form.submit();
                }
            } else {
                // Nhập tay hoặc chỉ 1 ký tự: luôn hiện nút Lưu
                saveBtn.style.display = 'inline-block';
            }
            lastInputTime = now;

            // Nếu người dùng dừng nhập 1s, vẫn hiện nút Lưu
            clearTimeout(inputTimer);
            inputTimer = setTimeout(() => {
                saveBtn.style.display = 'inline-block';
            }, 1000);
        });

        // Cho phép nhấn Enter ở ô số lượng để submit khi nhập tay
        qty.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                if (b1.value.length > 0 && b2.value.length > 0 && qty.value.length > 0) {
                    form.submit();
                }
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
