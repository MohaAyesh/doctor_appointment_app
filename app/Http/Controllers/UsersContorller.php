<?php

namespace App\Http\Controllers;

use App\Models\Appointments;
use App\Models\User;
use App\Models\Doctor;
use App\Models\UserDetails;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UsersContorller extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = array(); //this will return a set of user and doctor data
        $user = Auth::user();
        $doctor = User::where('type', 'doctor')->get();
        $details = $user->user_details;
        $doctorData = Doctor::all();
        $id=$details->user_id;
        //this is the date format without leading
        $date = now()->format('n/j/Y'); //change date format to suit the format in database

        //make this appointment filter only status is "upcoming"
        $appointment = Appointments::query()->where('status', 'upcoming')->where('date', $date)->where('user_id',$id)->first();

        //collect user data and all doctor details
        foreach($doctorData as $data){
            //sorting doctor name and doctor details
            foreach($doctor as $info){
                if($data['doc_id'] == $info['id']){
                    $data['doctor_name'] = $info['name'];
                    $data['doctor_profile'] = $info['profile_photo_url'];
                    if(isset($appointment) && $appointment['doc_id'] == $info['id']){
                        $data['appointments'] = $appointment;
                    }
                }
            }
        }

        $user['doctor'] = $doctorData;
        $user['details'] = $details; //return user details here together with doctor list

        return $user; //return all data
    }


  

  
    public function getCategory(Request $request)
    {
        $doctor = User::where('type', 'doctor')->get();
        $doctorData = Doctor::where('category', $request->get('category'))->get();
        
        // Collect user data and all doctor details
        foreach ($doctorData as $data) {
            // Sorting doctor name and doctor details
            foreach ($doctor as $info) {
                if ($data['doc_id'] == $info['id']) {
                    $data['doctor_name'] = $info['name'];
                    $data['doctor_profile'] = $info['profile_photo_url'];
                }
            }
        }
        
        return $doctorData; // Return all data
    }



    public function login(Request $reqeust)
    {
        //validate incoming inputs
        $reqeust->validate([
            'email'=>'required|email',
            'password'=>'required',
        ]);

        //check matching user
        $user = User::where('email', $reqeust->email)->first();

        //check password
        if(!$user || ! Hash::check($reqeust->password, $user->password)){
            throw ValidationException::withMessages([
                'email'=>['The provided credentials are incorrect'],
            ]);
        }

        //then return generated token
        return $user->createToken($reqeust->email)->plainTextToken;
    }

    public function register(Request $request)
    {
        //validate incoming inputs
        $request->validate([
            'name'=>'required|string',
            'email'=>'required|email',
            'password'=>'required',
        ]);

        $user = User::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'type'=>'user',
            'password'=>Hash::make($request->password),
        ]);

        $userInfo = UserDetails::create([
            'user_id'=>$user->id,
            'status'=>'active',
        ]);

        return $user;


    }
    /**
     * update favorite doctor list
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeFavDoc(Request $request)
    {

        $saveFav = UserDetails::where('user_id',Auth::user()->id)->first();

        $docList = json_encode($request->get('favList'));

        //update fav list into database
        $saveFav->fav = $docList;  //and remember update this as well
        $saveFav->save();

        return response()->json([
            'success'=>'The Favorite List is updated',
        ], 200);
    }


    public function updateUserProfile(Request $request){
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->storeAs('uploads', $imageName, 'public');

    
            // Get the authenticated user
            $user = User::find($request->get('user_id'));
    
            // Update the profile_photo_path column
            $user->profile_photo_path = 'uploads/' . $imageName;
            $user->save();
    
            return response()->json(['message' => 'Image uploaded successfully']);
        } else {
             dd($image = $request->get('image'));
            return response()->json(['error' => 'No image file provided'], 400);
        }
    }


    public function updateUserInfo(Request $request){
        $user = User::find($request->get('user_id'));
        $user->name=$request->get('name');
        $user->email=$request->get('email');
        $password=$request->get('password');
        if($password!=null && $password!=''){
           $user->password=Hash::make($password); 
        }
        $user->save();
    }


     /**
     * logout.
     *
     * @return \Illuminate\Http\Response
     */
    public function logout(){
        $user = Auth::user();
        $user->currentAccessToken()->delete();

        return response()->json([
            'success'=>'Logout successfully!',
        ], 200);
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
        //
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
