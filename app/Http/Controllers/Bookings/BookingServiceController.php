<?php

namespace App\Http\Controllers\Bookings;

use App\BookingService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BookingServiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $services = BookingService::all();

        return view('bookings.services.index', compact('services'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('bookings.services.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'service_name' => 'required|unique:booking_services|max:25',
        ]);

        BookingService::create($validatedData);

        return redirect(route('bookservices.index'))->withMessage('Service created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(BookingService $bookservice)
    {
        return view('bookings.services.edit', compact('bookservice'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, BookingService $bookservice)
    {
        $validatedData = $request->validate([
            'service_name' => 'required|max:25|unique:booking_services,service_name,'.$bookservice->id,
        ]);

        $bookservice->update($validatedData);

        return redirect(route('bookservices.index'))->withMessage('Service updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(BookingService $bookservice)
    {
        $bookservice->delete();

        return redirect(route('bookservices.index'))->withMessage('Service deleted successfully');

    }
}
