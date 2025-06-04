<x-admin-layout title="List Pengembalian">
    <div class="card shadow mb-4">
        <div class="card-body">
            @if ($success = session()->get('success'))
                <div class="card border-left-success mb-3">
                    <div class="card-body">{!! $success !!}</div>
                </div>
            @endif

            <x-admin.search url="{{ route('admin.returns.index') }}" placeholder="Cari pengembalian..." />

            {{-- Tambahkan filter tanggal di sini --}}
            <div class="mb-3">
                <form action="{{ route('admin.returns.index') }}" method="GET" class="form-inline">
                    <input type="hidden" name="search" value="{{ request('search') }}">
                    <label for="filter_type" class="my-1 mr-2">Filter Tanggal:</label>
                    <select name="filter_type" id="filter_type" class="form-control mr-2">
                        <option value="">Semua</option>
                        <option value="daily" {{ request('filter_type') == 'daily' ? 'selected' : '' }}>Harian</option>
                        <option value="monthly" {{ request('filter_type') == 'monthly' ? 'selected' : '' }}>Bulanan</option>
                        <option value="yearly" {{ request('filter_type') == 'yearly' ? 'selected' : '' }}>Tahunan</option>
                    </select>

                    <div id="daily-filter" style="{{ request('filter_type') == 'daily' ? '' : 'display:none;' }}">
                        <input type="date" name="daily_date" class="form-control mr-2" value="{{ request('daily_date') }}">
                    </div>

                    <div id="monthly-filter" style="{{ request('filter_type') == 'monthly' ? '' : 'display:none;' }}">
                        <input type="month" name="monthly_date" class="form-control mr-2" value="{{ request('monthly_date') }}">
                    </div>

                    <div id="yearly-filter" style="{{ request('filter_type') == 'yearly' ? '' : 'display:none;' }}">
                        <input type="number" name="yearly_date" class="form-control mr-2" placeholder="Tahun (YYYY)" min="1900" max="{{ date('Y') }}" value="{{ request('yearly_date', date('Y')) }}">
                    </div>

                    <button type="submit" class="btn btn-primary">Terapkan Filter</button>
                </form>
            </div>
            {{-- Akhir filter tanggal --}}

            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Buku</th>
                            <th>Peminjam</th>
                            <th>Tanggal Pengembalian</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($restores as $restore)
                            <tr>
                                <td>
                                    <img src="{{ isset($restore->book->cover) ? asset('storage/' . $restore->book->cover) : asset('storage/placeholder.png') }}"
                                        alt="{{ $restore->book->title }}" class="rounded" style="width: 100px;">
                                    <span class="ml-3">{{ $restore->book->title }}</span>
                                </td>
                                <td>{{ $restore->user->name }}</td>
                                <td>{{ $restore->returned_at->locale('id_ID')->isoFormat('LL') }}</td>
                                <td>
                                    @switch($restore->status)
                                        @case(\App\Models\Restore::STATUSES['Returned'])
                                            <span class="badge badge-success">{{ $restore->status }}</span>    
                                        @break
                                        
                                        @case(\App\Models\Restore::STATUSES['Not confirmed'])
                                            <span class="badge badge-warning">{{ $restore->status }}</span>
                                        @break

                                        @case(\App\Models\Restore::STATUSES['Past due'])
                                            <span class="badge badge-danger">{{ $restore->status }}</span>
                                        @break

                                        @case(\App\Models\Restore::STATUSES['Fine not paid'])
                                            <span class="badge badge-dark">{{ $restore->status }}</span>
                                        @break
                                    @endswitch
                                </td>
                                <td>
                                    <a href="{{ route('admin.returns.edit', $restore) }}" class="btn btn-link">Edit</a>

                                    <form action="{{ route('admin.returns.destroy', $restore) }}" method="POST"
                                        onsubmit="return confirm('Anda yakin ingin menghapus pengembalian ini?')">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit" class="btn btn-link text-danger">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Tidak ada data</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="mt-5">
                    {{ $restores->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>

    {{-- Tambahkan script JS untuk menampilkan/menyembunyikan input filter --}}
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const filterType = document.getElementById('filter_type');
                const dailyFilter = document.getElementById('daily-filter');
                const monthlyFilter = document.getElementById('monthly-filter');
                const yearlyFilter = document.getElementById('yearly-filter');

                function toggleFilters() {
                    dailyFilter.style.display = 'none';
                    monthlyFilter.style.display = 'none';
                    yearlyFilter.style.display = 'none';

                    if (filterType.value === 'daily') {
                        dailyFilter.style.display = 'block';
                    } else if (filterType.value === 'monthly') {
                        monthlyFilter.style.display = 'block';
                    } else if (filterType.value === 'yearly') {
                        yearlyFilter.style.display = 'block';
                    }
                }

                filterType.addEventListener('change', toggleFilters);

                // Initial call to set correct filter display on page load
                toggleFilters();
            });
        </script>
    @endpush
</x-admin-layout>