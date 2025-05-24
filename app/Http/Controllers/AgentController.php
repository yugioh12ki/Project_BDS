<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Property;
use App\Models\profile_agent;
use App\Models\Appointment;
use Illuminate\Support\Facades\Auth;

class AgentController extends Controller
{
    /**
     * Display agent dashboard
     */
    public function dashboard()
    {
        // Get current agent data
        $agent = Auth::user();
        
        // Count of properties managed by this agent
        $propertyCount = 0; // Replace with actual count from database
        
        // Recent listings or appointments
        $recentItems = [];
        
        return view('agents.dashboard', compact('agent', 'propertyCount', 'recentItems'));
    }
    
    /**
     * Display listings managed by this agent
     */
    public function brokers()
    {
        $properties = []; // Replace with actual properties query
        return view('agents.brokers', compact('properties'));
    }
    
    /**
     * Display appointments for this agent
     */
    public function appointments()
    {
        $agent = Auth::user();
        
        $appointments = Appointment::with(['cusUser', 'property'])
            ->where('AgentID', $agent->UserID)
            ->orderBy('AppointmentDateStart', 'desc')
            ->get();

        $properties = Property::select('PropertyID', 'Title', 'Address', 'Ward', 'District')
            ->where('Status', 'Active')
            ->get();

        return view('agents.appointments', compact('appointments', 'properties')); 
    }
    
    /**
     * Display agent profile
     */
    public function profile()
    {
        $agent = Auth::user();
        return view('agents.profile', compact('agent'));
    }
    
    /**
     * Update agent profile
     */
    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . Auth::id(),
            'phone' => 'nullable|string|max:20',
        ]);
        
        $user = Auth::user();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();
        
        // Update agent profile if exists
        if ($profile = profile_agent::where('user_id', $user->id)->first()) {
            $profile->phone = $request->phone;
            $profile->save();
        }
        
        return redirect()->back()->with('success', 'Profile updated successfully');
    }
}
