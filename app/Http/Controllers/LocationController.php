<?php

namespace App\Http\Controllers;

use App\Http\Requests\LocationRequest;
use App\Models\Location;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LocationController extends Controller
{
    public function index(Request $request)
    {
        $locations = Location::query()->search($request->search)->paginate();

        return Inertia::render('Location/Index', [
            'locations' => $locations,
            'filters' => $request->only('search'),
        ]);

    }

    public function store(LocationRequest $request)
    {
        $data = $request->validated();

        Location::create($data);

        return redirect()->back();
    }

    public function update(LocationRequest $request, Location $location)
    {
        $data = $request->validated();

        $location->update($data);

        return redirect()->back();
    }

    public function destroy(Location $location)
    {
        $location->delete();

        return redirect()->back();
    }
}
