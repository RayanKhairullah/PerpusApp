<x-admin-layout title="List Member">
    <div class="card shadow mb-4">
        <div class="card-body">
            @if ($success = session()->get('success'))
                <div class="card border-left-success">
                    <div class="card-body">{!! $success !!}</div>
                </div>
            @endif

            <a href="{{ route('admin.members.create') }}"
                class="btn btn-primary d-block d-sm-inline-block my-3">Tambah</a>

            <x-admin.search url="{{ route('admin.members.index') }}" placeholder="Cari member..." />

            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Tipe Nomor</th>
                            <th>Nomor</th>
                            <th>Telepon</th>
                            <th>Total Denda</th> {{-- Tambahkan ini --}}
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($members as $member)
                            <tr>
                                <td>{{ $member->name }}</td>
                                <td>{{ $member->number_type }}</td>
                                <td>{{ $member->number }}</td>
                                <td>+{{ $member->telephone }}</td>
                                {{-- Tampilkan total denda yang diformat --}}
                                <td>Rp{{ number_format($member->total_fine ?? 0, 0, ',', '.') }}</td>
                                <td class="d-flex">
                                    <a href="{{ route('admin.members.edit', $member) }}"
                                        class="btn btn-link p-0 mx-1">Edit</a>

                                    <form action="{{ route('admin.members.destroy', $member) }}" method="POST"
                                        onsubmit="return confirm('Anda yakin ingin menghapus member ini?')">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit" class="btn btn-link text-danger p-0 mx-1">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Tidak ada data</td> {{-- Ubah colspan menjadi 6 --}}
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="mt-5">
                    {{ $members->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>