<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveTripRequest;
use App\Trip;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class TripController extends Controller
{
    public function index(Request $request): View
    {
        return $this->listing($request, false);
    }

    public function diary(Request $request): View
    {
        return $this->listing($request, true);
    }

    private function listing(Request $request, bool $mine): View
    {
        $trips = Trip::query()->with('user:id,name')
            ->when($mine, fn (Builder $query): Builder => $query->where('user_id', $request->user()->id))
            ->orderByDesc('created_at')->orderByDesc('id')->paginate(9);

        return view('trips.index', compact('trips', 'mine'));
    }

    public function show(Trip $trip): View
    {
        $trip->load('user:id,name');

        return view('trips.show', compact('trip'));
    }

    public function create(): View
    {
        return view('trips.form');
    }

    public function store(SaveTripRequest $request): RedirectResponse
    {
        $data = $this->tripData($request);
        $path = $request->file('photo')->store('trips', 'public');
        abort_if($path === false, 500, 'Не удалось сохранить фотографию.');
        try {
            $trip = $request->user()->trips()->create($data + ['photo_path' => $path]);
        } catch (\Throwable $exception) {
            Storage::disk('public')->delete($path);
            throw $exception;
        }

        return redirect()->route('trips.show', $trip)->with('success', 'Путешествие опубликовано! Теперь ваша история вдохновит других.');
    }

    /** @return array<string, mixed> */
    private function tripData(SaveTripRequest $request): array
    {
        $data = $request->safe()->except('photo');
        $data['places'] = array_values(array_filter(array_map('trim', preg_split('/\R/u', $data['places']))));

        return $data;
    }
}
