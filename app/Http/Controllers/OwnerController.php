<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Property;
use App\Models\Appointment;
use App\Models\Transaction;

class OwnerController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function dashboard()
    {
        return view('owners.dashboard');
    }

    public function listProperty()
    {
        $properties = Property::where('OwnerID', auth()->id())->get();
        return view('owners.property.index', compact('properties'));
    }

    public function createProperty()
    {
        return view('owners.property.create');
    }

    public function storeProperty(Request $request)
    {
        $validated = $request->validate([
            'Title' => 'required|string|max:255',
            'Address' => 'required|string',
            'Description' => 'nullable|string',
            'Type' => 'required|in:rent,sale',
            'Price' => 'required|numeric',
            'LegalDocs.*' => 'nullable|file|mimes:pdf,jpg,png',
            'Images.*' => 'nullable|image',
        ]);

        $property = Property::create([
            'PropertyID' => uniqid('PROP_'),
            'OwnerID' => auth()->id(),
            'Title' => $validated['Title'],
            'Address' => $validated['Address'],
            'Description' => $validated['Description'] ?? '',
            'Type' => $validated['Type'],
            'Price' => $validated['Price'],
        ]);

        if ($request->hasFile('Images')) {
            foreach ($request->file('Images') as $img) {
                $img->store('properties/images', 'public');
            }
        }

        return redirect()->route('owner.property.index')->with('success', 'Đã thêm BĐS thành công');
    }

    public function appointments()
    {
        $appointments = Appointment::where('OwnerID', auth()->id())->with('user', 'agent')->get();
        return view('owners.appointments.index', compact('appointments'));
    }

    public function transactions()
    {
        $transactions = Transaction::where('OwnerID', auth()->id())->get();
        return view('owners.transactions.index', compact('transactions'));
    }
}
