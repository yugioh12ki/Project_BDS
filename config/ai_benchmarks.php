<?php

return [
    /*
    |--------------------------------------------------------------------------
    | AI SYSTEM PERFORMANCE BENCHMARKS
    |--------------------------------------------------------------------------
    |
    | Cấu hình các thông số benchmark để đánh giá hiệu suất hệ thống AI
    | Model: Google Gemini 1.5 Flash
    |
    */

    'model_info' => [
        'name' => 'Google Gemini 1.5 Flash',
        'type' => 'Large Language Model (LLM)',
        'architecture' => 'Transformer-based Multimodal AI',
        'approach' => 'Retrieval Augmented Generation (RAG)',
        'context_window' => '1M tokens',
        'api_version' => 'v1beta',
        'release_date' => '2024',
        'capabilities' => [
            'Text Generation',
            'Natural Language Understanding',
            'Multimodal Processing',
            'Code Understanding',
            'Vietnamese Language Support',
            'Real-time Database Integration'
        ]
    ],

    'configuration_parameters' => [
        'chatbot' => [
            'maxOutputTokens' => 1000,
            'temperature' => 0.3,        // Low creativity, high consistency
            'topP' => 0.8,              // Nucleus sampling
            'topK' => 40,               // Top-K sampling
            'purpose' => 'Customer support với database integration',
            'optimization' => 'Balanced between creativity and accuracy'
        ],

        'contract_analysis' => [
            'maxOutputTokens' => 2500,
            'temperature' => 0.1,        // Very low creativity, maximum precision
            'topP' => 0.8,
            'topK' => 40,
            'purpose' => 'Legal document analysis',
            'optimization' => 'Maximum precision for legal compliance'
        ]
    ],

    'performance_benchmarks' => [
        'response_time' => [
            'excellent' => 1000,        // < 1 second
            'good' => 3000,             // < 3 seconds
            'acceptable' => 5000,       // < 5 seconds
            'poor' => 10000,            // > 10 seconds
            'unit' => 'milliseconds'
        ],

        'confidence_score' => [
            'excellent' => 0.9,         // 90%+
            'good' => 0.8,              // 80%+
            'acceptable' => 0.7,        // 70%+
            'poor' => 0.6,              // < 60%
            'unit' => 'percentage'
        ],

        'success_rate' => [
            'excellent' => 95,          // 95%+
            'good' => 90,               // 90%+
            'acceptable' => 85,         // 85%+
            'poor' => 80,               // < 80%
            'unit' => 'percentage'
        ],

        'database_integration_rate' => [
            'excellent' => 90,          // 90%+ queries use database
            'good' => 80,               // 80%+
            'acceptable' => 70,         // 70%+
            'poor' => 60,               // < 60%
            'unit' => 'percentage'
        ]
    ],

    'nlp_accuracy_benchmarks' => [
        'keyword_extraction' => [
            'excellent' => 0.95,        // 95%+ accuracy
            'good' => 0.90,             // 90%+
            'acceptable' => 0.85,       // 85%+
            'poor' => 0.80,             // < 80%
        ],

        'entity_recognition' => [
            'location' => 0.90,         // Địa điểm
            'property_type' => 0.85,    // Loại BĐS
            'price' => 0.88,            // Giá cả
            'id_extraction' => 0.95,    // Mã ID
        ],

        'intent_classification' => [
            'property_search' => 0.90,
            'user_search' => 0.88,
            'transaction_inquiry' => 0.85,
            'appointment_related' => 0.87,
            'commission_inquiry' => 0.83,
            'feedback_related' => 0.80
        ]
    ],

    'contract_analysis_benchmarks' => [
        'validation_accuracy' => [
            'excellent' => 0.95,        // 95%+ correct validation
            'good' => 0.90,             // 90%+
            'acceptable' => 0.85,       // 85%+
            'poor' => 0.80             // < 80%
        ],

        'analysis_completeness' => [
            'required_sections' => [
                'contract_analysis',
                'benefits_analysis',
                'risk_assessment',
                'detailed_ratings',
                'recommendations'
            ],
            'minimum_score' => 0.80     // 80% completeness
        ],

        'legal_compliance_scoring' => [
            'customer_protection' => [7, 10],      // Range 7-10
            'legal_compliance' => [8, 10],         // Range 8-10
            'terms_fairness' => [6, 9],            // Range 6-9
            'contract_clarity' => [7, 10],         // Range 7-10
            'overall_minimum' => 7.0               // Minimum overall score
        ]
    ],

    'stress_test_benchmarks' => [
        'concurrent_requests' => [
            'light_load' => 5,
            'medium_load' => 10,
            'heavy_load' => 20,
            'max_acceptable_time' => 15000  // 15 seconds for concurrent requests
        ],

        'throughput' => [
            'excellent' => 1.0,         // 1+ requests/second
            'good' => 0.5,              // 0.5+ requests/second
            'acceptable' => 0.2,        // 0.2+ requests/second
            'poor' => 0.1               // < 0.1 requests/second
        ],

        'memory_usage' => [
            'max_memory_mb' => 512,     // 512 MB max
            'warning_threshold' => 256   // Warning at 256 MB
        ]
    ],

    'database_integration_tests' => [
        'search_effectiveness' => [
            'properties' => [
                'by_location' => 0.90,
                'by_price_range' => 0.85,
                'by_property_type' => 0.88,
                'by_id' => 0.95
            ],
            'users' => [
                'by_role' => 0.85,
                'by_location' => 0.80,
                'by_id' => 0.95
            ],
            'transactions' => [
                'by_id' => 0.95,
                'by_status' => 0.85,
                'by_date_range' => 0.80
            ]
        ],

        'data_relevance_score' => [
            'excellent' => 0.90,
            'good' => 0.80,
            'acceptable' => 0.70,
            'poor' => 0.60
        ]
    ],

    'vietnamese_language_processing' => [
        'diacritics_handling' => 0.95,    // Xử lý dấu tiếng Việt
        'colloquial_understanding' => 0.85, // Hiểu ngôn ngữ thông thường
        'formal_language' => 0.90,         // Ngôn ngữ trang trọng
        'real_estate_terminology' => 0.92,  // Thuật ngữ BĐS
        'legal_terminology' => 0.88        // Thuật ngữ pháp lý
    ],

    'quality_metrics' => [
        'relevance' => [
            'excellent' => 0.90,
            'good' => 0.80,
            'acceptable' => 0.70,
            'weight' => 30
        ],

        'accuracy' => [
            'excellent' => 0.95,
            'good' => 0.90,
            'acceptable' => 0.85,
            'weight' => 25
        ],

        'completeness' => [
            'excellent' => 0.90,
            'good' => 0.85,
            'acceptable' => 0.80,
            'weight' => 20
        ],

        'clarity' => [
            'excellent' => 0.85,
            'good' => 0.80,
            'acceptable' => 0.75,
            'weight' => 15
        ],

        'usefulness' => [
            'excellent' => 0.85,
            'good' => 0.80,
            'acceptable' => 0.75,
            'weight' => 10
        ]
    ],

    'test_scenarios' => [
        'chatbot_queries' => [
            'simple_property_search' => [
                'query' => 'Tìm căn hộ ở Hà Nội',
                'expected_confidence' => 0.8,
                'expected_database_search' => true,
                'expected_response_time' => 3000
            ],

            'complex_property_search' => [
                'query' => 'Tìm biệt thự 3 tầng ở quận 1 TPHCM giá từ 15-20 tỷ có sổ hồng',
                'expected_confidence' => 0.85,
                'expected_database_search' => true,
                'expected_response_time' => 4000
            ],

            'user_inquiry' => [
                'query' => 'Thông tin agent có mã AG001',
                'expected_confidence' => 0.9,
                'expected_database_search' => true,
                'expected_response_time' => 2000
            ],

            'general_question' => [
                'query' => 'Quy trình mua nhà như thế nào?',
                'expected_confidence' => 0.7,
                'expected_database_search' => false,
                'expected_response_time' => 2500
            ],

            'company_info' => [
                'query' => 'Hotline công ty là gì?',
                'expected_confidence' => 0.95,
                'expected_database_search' => false,
                'expected_response_time' => 1500
            ]
        ],

        'contract_samples' => [
            'valid_sale_contract' => [
                'type' => 'sale',
                'expected_validation' => true,
                'expected_analysis_time' => 8000,
                'minimum_quality_score' => 0.8
            ],

            'valid_rental_contract' => [
                'type' => 'rental',
                'expected_validation' => true,
                'expected_analysis_time' => 7000,
                'minimum_quality_score' => 0.75
            ],

            'invalid_document' => [
                'type' => 'invalid',
                'expected_validation' => false,
                'expected_analysis_time' => 3000,
                'should_reject' => true
            ]
        ]
    ],

    'reporting' => [
        'output_formats' => ['json', 'html', 'txt'],
        'log_level' => 'detailed',
        'include_raw_responses' => true,
        'performance_charts' => true,
        'comparison_with_previous' => true
    ]
];
