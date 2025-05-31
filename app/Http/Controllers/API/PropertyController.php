<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\DanhMucBDS;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PropertyController extends Controller
{
    /**
     * Get all approved properties
     */
    public function index(Request $request)
    {
        try {
            $query = Property::with(['danhMuc', 'chiTiet', 'images', 'chusohuu'])
                ->where('Status', 'active')
                ->orderBy('PostedDate', 'desc');

            // Apply filters
            if ($request->has('type') && $request->type != '') {
                $query->where('TypePro', $request->type);
            }

            if ($request->has('category') && $request->category != '') {
                $query->where('PropertyType', $request->category);
            }

            if ($request->has('district') && $request->district != '') {
                $query->where('District', 'LIKE', '%' . $request->district . '%');
            }

            if ($request->has('province') && $request->province != '') {
                $query->where('Province', 'LIKE', '%' . $request->province . '%');
            }

            // Price range filter
            if ($request->has('min_price') && $request->min_price != '') {
                $query->where('Price', '>=', $request->min_price);
            }

            if ($request->has('max_price') && $request->max_price != '') {
                $query->where('Price', '<=', $request->max_price);
            }

            $properties = $query->paginate($request->get('per_page', 20));

            $formattedProperties = $properties->getCollection()->map(function ($property) {
                return $this->formatProperty($property);
            });

            return response()->json([
                'success' => true,
                'data' => $formattedProperties,
                'pagination' => [
                    'current_page' => $properties->currentPage(),
                    'last_page' => $properties->lastPage(),
                    'per_page' => $properties->perPage(),
                    'total' => $properties->total(),
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching properties: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Không thể tải danh sách bất động sản',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get property details by ID
     */
    public function show($id)
    {
        try {
            $property = Property::with(['danhMuc', 'chiTiet', 'images', 'videos', 'chusohuu', 'moigioi'])
                ->where('PropertyID', $id)
                ->where('Status', 'active')
                ->first();

            if (!$property) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy bất động sản'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $this->formatProperty($property, true)
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching property details: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Không thể tải thông tin bất động sản',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search properties
     */
    public function search(Request $request)
    {
        try {
            $searchTerm = $request->get('q', '');

            $query = Property::with(['danhMuc', 'chiTiet', 'images', 'chusohuu'])
                ->where('Status', 'active');

            if (!empty($searchTerm)) {
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('Title', 'LIKE', '%' . $searchTerm . '%')
                      ->orWhere('Description', 'LIKE', '%' . $searchTerm . '%')
                      ->orWhere('Address', 'LIKE', '%' . $searchTerm . '%')
                      ->orWhere('District', 'LIKE', '%' . $searchTerm . '%')
                      ->orWhere('Province', 'LIKE', '%' . $searchTerm . '%');
                });
            }

            $properties = $query->orderBy('PostedDate', 'desc')
                               ->paginate($request->get('per_page', 20));

            $formattedProperties = $properties->getCollection()->map(function ($property) {
                return $this->formatProperty($property);
            });

            return response()->json([
                'success' => true,
                'data' => $formattedProperties,
                'pagination' => [
                    'current_page' => $properties->currentPage(),
                    'last_page' => $properties->lastPage(),
                    'per_page' => $properties->perPage(),
                    'total' => $properties->total(),
                ],
                'search_term' => $searchTerm
            ]);
        } catch (\Exception $e) {
            Log::error('Error searching properties: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Không thể tìm kiếm bất động sản',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get featured properties
     */
    public function featured()
    {
        try {
            $properties = Property::with(['danhMuc', 'chiTiet', 'images', 'chusohuu'])
                ->where('Status', 'active')
                ->orderBy('Price', 'desc')
                ->limit(10)
                ->get();

            $formattedProperties = $properties->map(function ($property) {
                return $this->formatProperty($property);
            });

            return response()->json([
                'success' => true,
                'data' => $formattedProperties
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching featured properties: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Không thể tải bất động sản nổi bật',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get property categories
     */
    public function categories()
    {
        try {
            $categories = DanhMucBDS::all();

            return response()->json([
                'success' => true,
                'data' => $categories->map(function ($category) {
                    return [
                        'id' => $category->Protype_ID,
                        'name' => $category->ten_pro,
                        'description' => $category->description ?? ''
                    ];
                })
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching categories: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Không thể tải danh mục',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Format property data for API response
     */
    private function formatProperty($property, $includeDetails = false)
    {
        $mainImage = $property->images->first();
        $imagePath = $mainImage ? asset('storage/' . str_replace('storage/app/public/', '', $mainImage->ImagePath)) : null;

        $data = [
            'id' => $property->PropertyID,
            'title' => $property->Title,
            'description' => $property->Description,
            'price' => $property->Price,
            'type' => $property->TypePro, // 'Sale' or 'Rent'
            'address' => $property->Address,
            'ward' => $property->Ward,
            'district' => $property->District,
            'province' => $property->Province,
            'full_address' => $property->Address . ', ' . $property->Ward . ', ' . $property->District . ', ' . $property->Province,
            'status' => $property->Status,
            'posted_date' => $property->PostedDate,
            'image_url' => $imagePath,
            'category' => [
                'id' => $property->danhMuc->Protype_ID ?? null,
                'name' => $property->danhMuc->ten_pro ?? 'N/A'
            ],
            'owner' => [
                'id' => $property->chusohuu->UserID ?? null,
                'name' => $property->chusohuu->Name ?? 'N/A',
                'phone' => $property->chusohuu->PhoneNumber ?? null,
                'email' => $property->chusohuu->Email ?? null
            ]
        ];

        // Add basic property details
        if ($property->chiTiet && $property->chiTiet->count() > 0) {
            $detail = $property->chiTiet->first();
            $data['details'] = [
                'area' => $detail->Area,
                'bedrooms' => $detail->Bedroom,
                'bathrooms' => $detail->Bath_WC,
                'floors' => $detail->Floor,
                'level_house' => $detail->LevelHouse,
                'house_length' => $detail->HouseLength,
                'house_width' => $detail->HouseWidth,
                'total_length' => $detail->TotalLength,
                'total_width' => $detail->TotalWidth,
                'road_width' => $detail->Road,
                'balcony' => $detail->Balcony,
                'legal_status' => $detail->legal,
                'view' => $detail->view,
                'nearby' => $detail->near,
                'interior' => $detail->Interior,
                'utilities' => $detail->Utilities,
                'water_price' => $detail->WaterPrice,
                'power_price' => $detail->PowerPrice
            ];
        }

        // Include additional details if requested
        if ($includeDetails) {
            // All images
            $data['images'] = $property->images->map(function ($image) {
                return [
                    'id' => $image->ImageID,
                    'url' => asset('storage/' . str_replace('storage/app/public/', '', $image->ImagePath)),
                    'caption' => $image->Caption ?? ''
                ];
            });

            // All videos
            $data['videos'] = $property->videos->map(function ($video) {
                $videoPath = $video->VideoPath;
                $isYoutube = strpos($videoPath, 'youtube.com') !== false || strpos($videoPath, 'youtu.be') !== false;

                return [
                    'id' => $video->VideoID,
                    'url' => $isYoutube ? $videoPath : asset('storage/' . str_replace('storage/app/public/', '', $videoPath)),
                    'is_youtube' => $isYoutube,
                    'caption' => $video->Caption ?? ''
                ];
            });

            // Agent info if available
            if ($property->moigioi) {
                $data['agent'] = [
                    'id' => $property->moigioi->UserID,
                    'name' => $property->moigioi->Name,
                    'phone' => $property->moigioi->PhoneNumber,
                    'email' => $property->moigioi->Email
                ];
            }
        }

        return $data;
    }
}
