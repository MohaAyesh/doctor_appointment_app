<?php

namespace App\Http\Controllers;

use App\Models\Appointments;
use App\Models\User;
use App\Models\Reviews;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class DocsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get doctor's appointment, patients, and display on the dashboard
        $doctor = Auth::user();
        $appointments = Appointments::where('doc_id', $doctor->id)->where('status', 'upcoming')->get();
        $reviews = Reviews::where('doc_id', $doctor->id)->where('status', 'active')->get();
        $user = User::all();
        $user2 = null; // Initialize $user2 before the loop
        foreach ($appointments as $appointment) {
            foreach ($user as $u) {
                if ($appointment['user_id'] == $u['id']) {
                    $user2 = $u;
                }
            }
        }
        // Return all data to the dashboard
        return view('dashboard')->with(['doctor' => $doctor, 'appointments' => $appointments, 'reviews' => $reviews, 'user' => $user2]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //this controller is to store booking details post from mobile app
        $reviews = new Reviews();
        //this is to update the appointment status from "upcoming" to "complete"
        $appointment = Appointments::where('id', $request->get('appointment_id'))->first();

        //save the ratings and reviews from user
        $reviews->user_id = Auth::user()->id;
        $reviews->doc_id = $request->get('doctor_id');
        $reviews->ratings = $request->get('ratings');
        $reviews->reviews = $request->get('reviews');
        $reviews->reviewed_by = Auth::user()->name;
        $reviews->status = 'active';
        $reviews->save();

        //change appointment status
        $appointment->status = 'complete';
        $appointment->save();

        return response()->json([
            'success'=>'The appointment has been completed and reviewed successfully!',
        ], 200);
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
