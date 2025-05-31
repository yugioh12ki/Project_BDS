<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Property;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AppointmentController extends Controller
{
    /**
     * Get user appointments
     */
    public function index(Request $request)
    {
        try {
            $user = $request->user();

            $query = Appointment::with(['property.danhMuc', 'property.images', 'customer', 'agent'])
                ->where(function ($q) use ($user) {
                    $q->where('CustomerID', $user->UserID)
                      ->orWhere('AgentID', $user->UserID);
                });

            // Filter by status if provided
            if ($request->has('status') && $request->status != '') {
                $query->where('Status', $request->status);
            }

            $appointments = $query->orderBy('AppointmentDate', 'desc')
                                 ->paginate($request->get('per_page', 20));

            $formattedAppointments = $appointments->getCollection()->map(function ($appointment) {
                return $this->formatAppointment($appointment);
            });

            return response()->json([
                'success' => true,
                'data' => $formattedAppointments,
                'pagination' => [
                    'current_page' => $appointments->currentPage(),
                    'last_page' => $appointments->lastPage(),
                    'per_page' => $appointments->perPage(),
                    'total' => $appointments->total(),
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching appointments: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Không thể tải danh sách cuộc hẹn',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create new appointment
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'property_id' => 'required|exists:property,PropertyID',
                'appointment_date' => 'required|date|after:now',
                'note' => 'nullable|string|max:500'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dữ liệu không hợp lệ',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = $request->user();
            $property = Property::find($request->property_id);

            // Check if property has an assigned agent
            $agentId = $property->AgentID;
            if (!$agentId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bất động sản này chưa có môi giới được phân công'
                ], 422);
            }

            $appointment = Appointment::create([
                'PropertyID' => $request->property_id,
                'CustomerID' => $user->UserID,
                'AgentID' => $agentId,
                'AppointmentDate' => $request->appointment_date,
                'Status' => 'Pending',
                'Note' => $request->note,
                'CreatedDate' => now()
            ]);

            // Load relationships for response
            $appointment->load(['property.danhMuc', 'property.images', 'customer', 'agent']);

            return response()->json([
                'success' => true,
                'message' => 'Đặt lịch hẹn thành công',
                'data' => $this->formatAppointment($appointment)
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error creating appointment: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Không thể tạo cuộc hẹn',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get appointment details
     */
    public function show($id, Request $request)
    {
        try {
            $user = $request->user();

            $appointment = Appointment::with(['property.danhMuc', 'property.images', 'customer', 'agent'])
                ->where('AppointmentID', $id)
                ->where(function ($q) use ($user) {
                    $q->where('CustomerID', $user->UserID)
                      ->orWhere('AgentID', $user->UserID);
                })
                ->first();

            if (!$appointment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy cuộc hẹn'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $this->formatAppointment($appointment)
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching appointment: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Không thể tải thông tin cuộc hẹn',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update appointment status (for agents)
     */
    public function updateStatus($id, Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'status' => 'required|in:Pending,Confirmed,Completed,Cancelled',
                'note' => 'nullable|string|max:500'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dữ liệu không hợp lệ',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = $request->user();

            $appointment = Appointment::where('AppointmentID', $id)
                ->where('AgentID', $user->UserID)
                ->first();

            if (!$appointment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy cuộc hẹn hoặc bạn không có quyền cập nhật'
                ], 404);
            }

            $appointment->Status = $request->status;
            if ($request->has('note')) {
                $appointment->Note = $request->note;
            }
            $appointment->save();

            // Load relationships for response
            $appointment->load(['property.danhMuc', 'property.images', 'customer', 'agent']);

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật trạng thái cuộc hẹn thành công',
                'data' => $this->formatAppointment($appointment)
            ]);

        } catch (\Exception $e) {
            Log::error('Error updating appointment: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Không thể cập nhật cuộc hẹn',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancel appointment (for customers)
     */
    public function cancel($id, Request $request)
    {
        try {
            $user = $request->user();

            $appointment = Appointment::where('AppointmentID', $id)
                ->where('CustomerID', $user->UserID)
                ->where('Status', '!=', 'Completed')
                ->first();

            if (!$appointment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy cuộc hẹn hoặc không thể hủy'
                ], 404);
            }

            $appointment->Status = 'Cancelled';
            $appointment->save();

            return response()->json([
                'success' => true,
                'message' => 'Hủy cuộc hẹn thành công'
            ]);

        } catch (\Exception $e) {
            Log::error('Error cancelling appointment: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Không thể hủy cuộc hẹn',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Format appointment data for API response
     */
    private function formatAppointment($appointment)
    {
        $property = $appointment->property;
        $mainImage = $property->images->first();
        $imagePath = $mainImage ? asset('storage/' . str_replace('storage/app/public/', '', $mainImage->ImagePath)) : null;

        return [
            'id' => $appointment->AppointmentID,
            'appointment_date' => $appointment->AppointmentDate,
            'status' => $appointment->Status,
            'note' => $appointment->Note,
            'created_date' => $appointment->CreatedDate,
            'property' => [
                'id' => $property->PropertyID,
                'title' => $property->Title,
                'address' => $property->Address . ', ' . $property->Ward . ', ' . $property->District . ', ' . $property->Province,
                'price' => $property->Price,
                'type' => $property->TypePro,
                'image_url' => $imagePath,
                'category' => [
                    'id' => $property->danhMuc->Protype_ID ?? null,
                    'name' => $property->danhMuc->ten_pro ?? 'N/A'
                ]
            ],
            'customer' => [
                'id' => $appointment->customer->UserID,
                'name' => $appointment->customer->Name,
                'phone' => $appointment->customer->PhoneNumber,
                'email' => $appointment->customer->Email
            ],
            'agent' => [
                'id' => $appointment->agent->UserID,
                'name' => $appointment->agent->Name,
                'phone' => $appointment->agent->PhoneNumber,
                'email' => $appointment->agent->Email
            ]
        ];
    }
}
