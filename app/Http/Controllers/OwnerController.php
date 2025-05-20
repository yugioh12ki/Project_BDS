<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Property;
use App\Models\User;
use App\Models\DanhMucBDS;
use App\Models\Appointment;
use App\Models\DetailProperty;
use App\Models\Transaction;
use Carbon\Carbon;

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
        // Lấy tất cả bất động sản từ database, không lọc theo OwnerID
        $properties = Property::with(['danhMuc', 'chiTiet', 'images'])->get();
        
        // Debug để kiểm tra dữ liệu
        // dd($properties);
        
        $categories = DanhMucBDS::all();
        $owners = User::all();

        return view('owners.property.index', compact('properties', 'categories', 'owners'));
    }


    // public function createProperty()
    // {
    //     $categories = DanhMucBDS::all();
    //     return view('property.create_proper', compact('categories'));
    // }

    public function storeProperty(Request $request)
    {
        $validated = $request->validate([
            'Title' => 'required|string|max:255',
            'Address' => 'required|string|max:255',
            'Price' => 'required|numeric|min:0',
            'Province' => 'required|string|max:255',
            'District' => 'required|string|max:255',
            'Ward' => 'required|string|max:255',
            'PropertyType' => 'required|integer',
            'TypePro' => 'required|string', 

            'Floor' => 'required|integer|min:0',
            'Area' => 'required|integer|min:1',
            'Bedroom' => 'required|integer|min:0',
            'Bath_WC' => 'required|integer|min:0',
            'Road' => 'required|integer|min:0',
 
            'legal' => 'nullable|string|max:255',
            'view' => 'nullable|string|max:255',
            'near' => 'nullable|string|max:255',
            'Interior' => 'nullable|string|max:255',
            'WaterPrice' => 'nullable|string|max:255',
            'PowerPrice' => 'nullable|string|max:255',
            'Utilities' => 'nullable|string|max:255',
            'Description' => 'nullable|string'
        ]);

        $detail = new DetailProperty();
        $detail->Floor = $validated['Floor'];
        $detail->Area = $validated['Area'];
        $detail->Bedroom = $validated['Bedroom'];
        $detail->Bath_WC = $validated['Bath_WC'];
        $detail->Road = $validated['Road'];
        $detail->legal = $validated['legal'] ?? null;
        $detail->view = $validated['view'] ?? null;
        $detail->near = $validated['near'] ?? null;
        $detail->Interior = $validated['Interior'] ?? null;
        $detail->WaterPrice = $validated['WaterPrice'] ?? null;
        $detail->PowerPrice = $validated['PowerPrice'] ?? null;
        $detail->Utilities = $validated['Utilities'] ?? null;
        $detail->save();

        $idDetail = $detail->IdDetail;
        $property = new Property();
        $property->Title = $validated['Title'];
        $property->Address = $validated['Address'];
        $property->Price = $validated['Price'];
        $property->Province = $validated['Province'];
        $property->District = $validated['District'];
        $property->Ward = $validated['Ward'];
        $property->PropertyType = $validated['PropertyType'];
        $property->TypePro = $validated['TypePro'];
        $property->Description = $validated['Description'] ?? null;
        $property->IdDetail = $idDetail;
        $property->OwnerID = Auth::user()->UserID;
        $property->PostedDate = now()->format('Y-m-d');
        $property->Status = 'inactive'; 
        $property->save();

        return redirect()->route('owner.property.index')->with('success', 'Thêm bất động sản thành công!');
    }


    public function appointments()
    {
        $ownerId = Auth::user()->UserID;
        
        // Lấy danh sách lịch hẹn liên quan đến chủ nhà
        $appointments = Appointment::where('OwnerID', $ownerId)
            ->with(['property', 'user_agent', 'user_customer'])
            ->orderBy('AppointmentDateStart', 'desc')
            ->get();
            
        // Phân loại các cuộc hẹn
        $upcomingAppointments = $appointments->filter(function($appointment) {
            return $appointment->AppointmentDateStart >= Carbon::today() && 
                  ($appointment->Status == 'pending' || 
                   $appointment->Status == 'confirmed');
        });
        
        $completedAppointments = $appointments->filter(function($appointment) {
            return $appointment->Status == 'completed' || $appointment->Status == 'Hoàn Thành';
        });
        
        $cancelledAppointments = $appointments->filter(function($appointment) {
            return $appointment->Status == 'cancelled' || $appointment->Status == 'Đã Hủy';
        });

        return view('owners.appointment.appointments', compact(
            'appointments', 
            'upcomingAppointments', 
            'completedAppointments', 
            'cancelledAppointments'
        ));
    }

    public function transactions()
    {
        $ownerId = Auth::user()->UserID;
        $transactions = Transaction::with('property')
            ->where('OwnerID', $ownerId)
            ->get();

        return view('owners.transactions', compact('transactions'));
    }
}
