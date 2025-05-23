&lt;?php

Route::get('/test-property-api', function() {
    try {
        // Mock authentication for testing
        if (!Auth::check()) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        $ownerId = Auth::user()->UserID;

        // Get properties for current owner
        $ownerProperties = App\Models\Property::with(['danhMuc', 'chiTiet', 'images'])
            ->where('OwnerID', $ownerId)
            ->get();

        return response()->json([
            'owner_id' => $ownerId,
            'properties_count' => $ownerProperties->count(),
            'properties' => $ownerProperties->map(function($property) {
                return [
                    'PropertyID' => $property->PropertyID,
                    'Title' => $property->Title,
                    'OwnerID' => $property->OwnerID,
                    'TypePro' => $property->TypePro,
                    'Price' => $property->Price
                ];
            })
        ]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
});
