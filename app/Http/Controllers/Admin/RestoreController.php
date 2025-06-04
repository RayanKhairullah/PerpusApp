<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Restore;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Carbon\Carbon; // Tambahkan ini

class RestoreController extends Controller
{
    public function index(Request $request)
    {
        $restores = Restore::with(['book', 'user']);

        $restores->when($request->search, function (Builder $query) use ($request) {
            $query->where(function (Builder $q) use ($request) {
                $q->whereHas('book', function (Builder $query) use ($request) {
                    $query->where('title', 'LIKE', "%{$request->search}%");
                })
                    ->orWhereHas('user', function (Builder $query) use ($request) {
                        $query->where('name', 'LIKE', "%{$request->search}%");
                    });
            });
        });

        // Logika filter tanggal
        $restores->when($request->filter_type, function (Builder $query) use ($request) {
            if ($request->filter_type === 'daily' && $request->daily_date) {
                $date = Carbon::parse($request->daily_date);
                $query->whereDate('returned_at', $date);
            } elseif ($request->filter_type === 'monthly' && $request->monthly_date) {
                $month = Carbon::parse($request->monthly_date)->month;
                $year = Carbon::parse($request->monthly_date)->year;
                $query->whereMonth('returned_at', $month)
                      ->whereYear('returned_at', $year);
            } elseif ($request->filter_type === 'yearly' && $request->yearly_date) {
                $query->whereYear('returned_at', $request->yearly_date);
            }
        });

        $restores = $restores->latest('id')->paginate(10);

        return view('admin.returns.index')->with([
            'restores' => $restores,
        ]);
    }

    public function edit($id)
    {
        $restore = Restore::query()->findOrFail($id);

        return view('admin.returns.edit')->with([
            'restore' => $restore,
        ]);
    }

    public function update(Request $request, $id)
    {
        $restore = Restore::query()->findOrFail($id);

        $data = $request->validate([
            'confirmation' => ['required', 'boolean'],
            'fine' => [Rule::when($restore->returned_at > $restore->borrow->borrowed_at->addDays($restore->borrow->duration) && is_null($restore->fine), ['required', 'numeric'])],
        ]);

        if ($data['confirmation']) {
            $restore->book()->increment('amount', $restore->borrow->amount);

            $data['status'] = Restore::STATUSES['Returned'];
        } else if (isset($data['fine'])) {
            $data['status'] = Restore::STATUSES['Fine not paid'];
        }

        $restore->update($data);

        return redirect()
            ->route('admin.returns.index')
            ->with('success', 'Berhasil mengubah status konfirmasi pengembalian.');
    }

    public function destroy($id)
    {
        $restore = Restore::query()->findOrFail($id);

        $restore->delete();

        return redirect()
            ->route('admin.returns.index')
            ->with('success', 'Berhasil menghapus pengembalian.');
    }
}