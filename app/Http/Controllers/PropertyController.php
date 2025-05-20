<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PropertyController extends Controller
{
    /**
     * Update the status of a batch of properties
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateBatchStatus(Request $request)
    {
        // Validate request data
        $validator = Validator::make($request->all(), [
            'property_ids' => 'required',
            'status' => 'required|in:approved,rejected',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Decode property IDs
            $propertyIds = json_decode($request->property_ids, true);

            if (!is_array($propertyIds) || count($propertyIds) === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No property IDs provided'
                ], 400);
            }

            // Get current admin ID
            $adminId = Auth::id();

            // Update property status for all IDs
            $updateData = [
                'Status' => $request->status,
                'ApprovedBy' => $adminId,
                'ApprovedDate' => now()
            ];

            // Add reason if provided (for rejected properties)
            if ($request->status === 'rejected' && $request->has('reason')) {
                $updateData['RejectReason'] = $request->reason;
            }

            // Update all properties
            $updatedCount = Property::whereIn('PropertyID', $propertyIds)->update($updateData);

            // Return success response
            return response()->json([
                'success' => true,
                'message' => "Đã cập nhật trạng thái cho $updatedCount bất động sản thành công",
                'updated_count' => $updatedCount
            ]);

        } catch (\Exception $e) {
            // Log the error
            Log::error('Batch property status update error: ' . $e->getMessage());

            // Return error response
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi trong quá trình cập nhật trạng thái',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
