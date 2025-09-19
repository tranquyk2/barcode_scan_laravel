@extends('layouts.app')
@section('title', 'Thống kê barcode')
@section('content')
<div class="container mt-4">
    
    <h2 class="mb-4">Thống kê barcode tháng {{ $month }}</h2>
    <form method="GET" class="mb-3">
        <label>Chọn tháng: <input type="month" name="month" value="{{ $month }}"></label>
        <button type="submit" class="btn btn-primary btn-sm">Xem thống kê</button>
    </form>
    <div class="row">
        <div class="col-md-6">
            <canvas id="byDayChart" height="220"></canvas>
        </div>
        <div class="col-md-6">
            <canvas id="passFailChart" height="220"></canvas>
        </div>
    </div>
    <div class="mt-4">
        <h4>Top 5 nhân viên quét nhiều nhất</h4>
        <table class="table table-bordered">
            <thead><tr><th>STT</th><th>Tên</th><th>Số lượt quét</th></tr></thead>
            <tbody>
            @foreach($topUsers as $i => $user)
                <tr>
                    <td>{{ $i+1 }}</td>
                    <td>{{ $user->user ? $user->user->name : 'N/A' }}</td>
                    <td>{{ $user->total }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Barcode by day
    const byDayLabels = {!! json_encode($byDay->pluck('day')->toArray()) !!};
    const byDayData = {!! json_encode($byDay->pluck('total')->toArray()) !!};
    if (byDayLabels.length > 0) {
        new Chart(document.getElementById('byDayChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: byDayLabels,
                datasets: [{
                    label: 'Số lượt quét theo ngày',
                    data: byDayData,
                    backgroundColor: '#007bff',
                }]
            },
            options: {responsive: true}
        });
    }
    // PASS/FAIL ratio
    const pfLabels = {!! json_encode(array_keys($passFail->toArray())) !!};
    const pfData = {!! json_encode(array_values($passFail->toArray())) !!};
    if (pfLabels.length > 0) {
        // Đảm bảo PASS là màu xanh, FAIL là màu đỏ
        const pfColors = pfLabels.map(label => label === 'PASS' ? '#28a745' : '#dc3545');
        new Chart(document.getElementById('passFailChart').getContext('2d'), {
            type: 'pie',
            data: {
                labels: pfLabels,
                datasets: [{
                    data: pfData,
                    backgroundColor: pfColors,
                }]
            },
            options: {responsive: true}
        });
    }
});
</script>
@endsection
