@extends('_layout._layhome.home')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/property-detail.css') }}">
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
      crossorigin=""/>
<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
:root {
    --primary-gold: #D4A574;
    --secondary-gold: #B8956A;
    --dark-brown: #8B4513;
    --light-brown: #DEB887;
    --cream: #FFF8DC;
    --white: #FFFFFF;
    --text-dark: #2C1810;
    --text-light: #6B5B4F;
    --shadow-light: rgba(212, 165, 116, 0.1);
    --shadow-medium: rgba(212, 165, 116, 0.2);
    --shadow-heavy: rgba(139, 69, 19, 0.3);
}

* {
    box-sizing: border-box;
}

body {
    font-family: 'Inter', sans-serif;
    line-height: 1.6;
    color: var(--text-dark);
    background: linear-gradient(135deg, #FFF8DC 0%, #F5F0E8 100%);
}

.property-detail-page {
    background: transparent;
    border-radius: 0;
    overflow: visible;
    max-width: 1600px;
    margin: 0 auto;
    padding: 0 15px;
}

/* Hero Section */
.property-hero {
    background: linear-gradient(135deg, var(--primary-gold), var(--secondary-gold));
    border-radius: 20px;
    padding: 35px;
    margin-bottom: 40px;
    box-shadow: 0 15px 50px var(--shadow-medium);
    position: relative;
    overflow: hidden;
}

.property-hero::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 150px;
    height: 150px;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    border-radius: 50%;
}

.property-hero .row {
    align-items: center;
}

.property-hero h1 {
    font-family: 'Playfair Display', serif;
    font-size: 2rem;
    font-weight: 600;
    color: var(--white);
    margin-bottom: 12px;
    text-shadow: 0 2px 4px rgba(0,0,0,0.1);
    line-height: 1.3;
}

.property-hero .location {
    color: rgba(255,255,255,0.9);
    font-size: 1rem;
    margin-bottom: 0;
    display: flex;
    align-items: center;
    gap: 8px;
}

.property-hero .location i {
    font-size: 1.1rem;
}

.property-price {
    text-align: right;
}

.property-price h2 {
    font-family: 'Playfair Display', serif;
    font-size: 1.8rem;
    font-weight: 600;
    color: var(--white);
    margin: 0;
    text-shadow: 0 2px 4px rgba(0,0,0,0.1);
    line-height: 1.2;
}

/* Gallery Section */
.property-gallery {
    margin-bottom: 40px;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 15px 40px var(--shadow-light);
    /* Tăng kích thước gallery cho hiển thị tốt hơn */
    max-width: 100%;
    width: 100%;
}

.main-image {
    position: relative;
    height: 850px; /* Tăng từ 725px lên 850px (+17.2%) */
    overflow: hidden;
    background: #000;
}

.main-image img,
.main-image iframe,
.main-image video {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
    border: none;
}

.main-image:hover img {
    transform: scale(1.02);
}

/* Media Gallery Controls */
.media-gallery-controls {
    position: absolute;
    top: 20px;
    right: 20px;
    display: flex;
    gap: 10px;
    z-index: 10;
}

.media-btn {
    background: rgba(0, 0, 0, 0.7);
    color: white;
    border: none;
    padding: 8px 12px;
    border-radius: 8px;
    font-size: 0.9rem;
    display: flex;
    align-items: center;
    gap: 5px;
    transition: all 0.3s ease;
    backdrop-filter: blur(10px);
}

.media-btn:hover {
    background: rgba(0, 0, 0, 0.9);
    transform: translateY(-2px);
}

.thumbnail-list {
    display: flex;
    gap: 18px;
    padding: 30px;
    background: var(--white);
    overflow-x: auto;
    overflow-y: hidden;
    scrollbar-width: thin;
    scrollbar-color: var(--primary-gold) var(--cream);
    scroll-behavior: smooth;
    -webkit-overflow-scrolling: touch;
    flex-wrap: nowrap;
    /* Enhanced scrolling features for 10+ items */
    scroll-snap-type: x mandatory;
    scroll-padding: 30px;
    /* Improve scroll performance */
    transform: translateZ(0);
    will-change: scroll-position;
    /* Make sure content can scroll */
    min-width: 100%;
    width: 100%;
}

.thumbnail-list::-webkit-scrollbar {
    height: 12px; /* Increased for better usability */
}

.thumbnail-list::-webkit-scrollbar-track {
    background: var(--cream);
    border-radius: 8px;
    margin: 0 30px;
    box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);
}

.thumbnail-list::-webkit-scrollbar-thumb {
    background: linear-gradient(135deg, var(--primary-gold), var(--secondary-gold));
    border-radius: 8px;
    border: 2px solid var(--cream);
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    transition: all 0.3s ease;
}

.thumbnail-list::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(135deg, var(--secondary-gold), var(--dark-brown));
    transform: scaleY(1.2);
    box-shadow: 0 3px 6px rgba(0,0,0,0.3);
}

/* Enhanced scrolling features for large thumbnail collections */
.thumbnail-list::-webkit-scrollbar-corner {
    background: var(--cream);
}

/* Scroll indicator shadows for visual feedback */
.property-gallery {
    position: relative;
}

.property-gallery::before,
.property-gallery::after {
    content: '';
    position: absolute;
    top: 0;
    bottom: 0;
    width: 30px;
    pointer-events: none;
    z-index: 10;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.property-gallery::before {
    left: 0;
    background: linear-gradient(to right, rgba(255,255,255,0.9), transparent);
}

.property-gallery::after {
    right: 0;
    background: linear-gradient(to left, rgba(255,255,255,0.9), transparent);
}

.property-gallery.has-scroll-left::before {
    opacity: 1;
}

.property-gallery.has-scroll-right::after {
    opacity: 1;
}

/* Thumbnail hover effects for better interactivity */
.thumbnail:hover {
    transform: translateY(-3px) scale(1.02);
    box-shadow: 0 8px 20px var(--shadow-light);
    z-index: 5;
}

/* Thumbnail Navigation Buttons */
.thumbnail-container {
    position: relative;
    background: var(--white);
}

.thumbnail-nav-btn {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 45px;
    height: 45px;
    background: rgba(255, 255, 255, 0.95);
    border: 2px solid var(--primary-gold);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    z-index: 20;
    font-size: 18px;
    color: var(--primary-gold);
    backdrop-filter: blur(10px);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.thumbnail-nav-btn:hover {
    background: var(--primary-gold);
    color: white;
    transform: translateY(-50%) scale(1.1);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
}

.thumbnail-nav-btn.prev {
    left: 15px;
}

.thumbnail-nav-btn.next {
    right: 15px;
}

.thumbnail-nav-btn:disabled {
    opacity: 0.4;
    cursor: not-allowed;
    background: rgba(255, 255, 255, 0.5);
}

.thumbnail-nav-btn:disabled:hover {
    transform: translateY(-50%);
    background: rgba(255, 255, 255, 0.5);
    color: var(--primary-gold);
}

/* Hide navigation buttons when not needed */
.thumbnail-nav-btn.hidden {
    opacity: 0;
    pointer-events: none;
}

/* Card Styles */
.luxury-card {
    background: var(--white);
    border: none;
    border-radius: 20px;
    box-shadow: 0 10px 30px var(--shadow-light);
    margin-bottom: 30px;
    overflow: hidden;
    transition: all 0.3s ease;
}

.luxury-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 50px var(--shadow-medium);
}

.luxury-card-header {
    background: linear-gradient(135deg, var(--primary-gold), var(--secondary-gold));
    color: var(--white);
    padding: 20px 25px;
    border: none;
    margin: 0;
}

.luxury-card-header h3 {
    font-family: 'Playfair Display', serif;
    font-size: 1.3rem;
    font-weight: 600;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.luxury-card-header i {
    font-size: 1.2rem;
}

.luxury-card-body {
    padding: 25px;
}

/* Detail Items */
.detail-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 15px;
    margin-bottom: 20px;
}

.detail-item {
    background: var(--cream);
    padding: 15px;
    border-radius: 12px;
    border-left: 4px solid var(--primary-gold);
    transition: all 0.3s ease;
}

.detail-item:hover {
    background: var(--white);
    transform: translateX(5px);
    box-shadow: 0 5px 15px var(--shadow-light);
}

.detail-item p {
    margin: 0;
    font-weight: 500;
    color: var(--text-dark);
    font-size: 0.9rem;
    line-height: 1.4;
}

.detail-item strong {
    color: var(--dark-brown);
    display: flex;
    align-items: center;
    gap: 6px;
    margin-bottom: 4px;
    font-size: 0.85rem;
}

.detail-item i {
    color: var(--primary-gold);
    font-size: 1rem;
}

/* Highlights */
.highlights-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.highlights-list li {
    background: var(--cream);
    margin-bottom: 12px;
    padding: 15px 20px;
    border-radius: 12px;
    transition: all 0.3s ease;
    border-left: 4px solid var(--primary-gold);
}

.highlights-list li:hover {
    background: var(--white);
    transform: translateX(5px);
    box-shadow: 0 5px 15px var(--shadow-light);
}

.highlights-list li i {
    color: var(--primary-gold);
    margin-right: 10px;
}

/* Payment Table */
.table {
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 5px 15px var(--shadow-light);
    border: none;
}

.table thead th {
    background: linear-gradient(135deg, var(--primary-gold), var(--secondary-gold));
    color: var(--white);
    font-weight: 600;
    border: none;
    padding: 20px 15px;
    font-size: 0.95rem;
}

.table tbody td {
    padding: 18px 15px;
    border-color: var(--cream);
    color: var(--text-dark);
    font-weight: 500;
}

.table tbody tr:hover {
    background: var(--cream);
}

.table-warning {
    background: linear-gradient(135deg, var(--light-brown), var(--primary-gold)) !important;
    color: var(--white) !important;
}

/* Contact Sidebar */
.contact-sidebar {
    position: sticky;
    top: 20px;
}

.contact-card {
    background: var(--white);
    border-radius: 20px;
    padding: 20px;
    box-shadow: 0 15px 40px var(--shadow-light);
    border: 2px solid var(--cream);
}

.agent-profile {
    display: flex;
    align-items: center;
    margin-bottom: 15px;
    padding-bottom: 15px;
    border-bottom: 2px solid var(--cream);
}

.agent-avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    object-fit: cover;
    margin-right: 12px;
    border: 3px solid var(--primary-gold);
    box-shadow: 0 5px 15px var(--shadow-light);
}

.letter-avatar {
    background: linear-gradient(135deg, var(--primary-gold), var(--secondary-gold));
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--white);
    font-weight: 700;
    font-size: 1.1rem;
}

.agent-info h4 {
    font-family: 'Playfair Display', serif;
    font-size: 1rem;
    margin: 0 0 3px 0;
    color: var(--dark-brown);
}

.agent-info p {
    margin: 0;
    color: var(--text-light);
    font-weight: 500;
    font-size: 0.8rem;
}

.contact-details {
    margin-bottom: 15px;
}

.contact-details p {
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    gap: 8px;
    color: var(--text-dark);
    font-weight: 500;
    font-size: 0.85rem;
}

.contact-details i {
    color: var(--primary-gold);
    font-size: 0.9rem;
    width: 16px;
}

.contact-buttons {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.contact-buttons .btn {
    padding: 10px 12px;
    border-radius: 10px;
    font-weight: 600;
    text-decoration: none;
    text-align: center;
    transition: all 0.3s ease;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    font-size: 0.8rem;
}

.btn-primary {
    background: linear-gradient(135deg, var(--primary-gold), var(--secondary-gold));
    color: var(--white);
}

.btn-primary:hover {
    background: linear-gradient(135deg, var(--secondary-gold), var(--dark-brown));
    transform: translateY(-2px);
    box-shadow: 0 8px 20px var(--shadow-medium);
}

.btn-success {
    background: linear-gradient(135deg, #28a745, #20c997);
    color: var(--white);
}

.btn-success:hover {
    background: linear-gradient(135deg, #20c997, #17a2b8);
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(40, 167, 69, 0.3);
}

.btn-warning {
    background: linear-gradient(135deg, #ffc107, #fd7e14);
    color: var(--white);
}

.btn-warning:hover {
    background: linear-gradient(135deg, #fd7e14, #dc3545);
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(255, 193, 7, 0.3);
}

/* Related Properties */
.related-properties {
    margin-top: 50px;
}

.related-properties h3 {
    font-family: 'Playfair Display', serif;
    font-size: 2rem;
    color: var(--dark-brown);
    text-align: center;
    margin-bottom: 40px;
    position: relative;
}

.related-properties h3::after {
    content: '';
    position: absolute;
    bottom: -10px;
    left: 50%;
    transform: translateX(-50%);
    width: 80px;
    height: 3px;
    background: linear-gradient(135deg, var(--primary-gold), var(--secondary-gold));
    border-radius: 2px;
}

.property-card {
    background: var(--white);
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 10px 30px var(--shadow-light);
    transition: all 0.3s ease;
    height: 100%;
    border: 2px solid var(--cream);
}

.property-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 50px var(--shadow-medium);
    border-color: var(--primary-gold);
}

.property-image {
    position: relative;
    height: 220px;
    overflow: hidden;
}

.property-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.property-card:hover .property-image img {
    transform: scale(1.1);
}

.property-content {
    padding: 25px;
}

.property-title {
    font-family: 'Playfair Display', serif;
    font-size: 1.2rem;
    font-weight: 600;
    color: var(--dark-brown);
    margin-bottom: 10px;
    line-height: 1.3;
}

.property-location {
    color: var(--text-light);
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.property-location i {
    color: var(--primary-gold);
}

.property-details {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 10px;
    margin-bottom: 20px;
}

.property-details .detail {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.9rem;
    color: var(--text-dark);
    background: var(--cream);
    padding: 8px 12px;
    border-radius: 8px;
}

.property-details .detail i {
    color: var(--primary-gold);
    font-size: 1rem;
}

.property-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-top: 20px;
    border-top: 2px solid var(--cream);
}

.property-price-display {
    font-family: 'Playfair Display', serif;
    font-size: 1.3rem;
    font-weight: 700;
    color: var(--dark-brown);
}

.property-price-display small {
    font-family: 'Inter', sans-serif;
    font-size: 0.85rem;
    color: var(--text-light);
    font-weight: 500;
}

.btn-view {
    background: linear-gradient(135deg, var(--primary-gold), var(--secondary-gold));
    color: var(--white);
    padding: 10px 20px;
    border-radius: 10px;
    text-decoration: none;
    font-weight: 600;
    font-size: 0.9rem;
    transition: all 0.3s ease;
    border: none;
}

.btn-view:hover {
    background: linear-gradient(135deg, var(--secondary-gold), var(--dark-brown));
    transform: translateY(-2px);
    box-shadow: 0 5px 15px var(--shadow-medium);
    color: var(--white);
}

/* Map Styles */
#propertyMap {
    border-radius: 15px;
    box-shadow: 0 10px 30px var(--shadow-light);
    border: 3px solid var(--cream);
}

.full-address {
    background: var(--cream) !important;
    border: 2px solid var(--primary-gold) !important;
    border-radius: 15px !important;
}

/* Modal Styles */
.modal-content {
    border: none;
    border-radius: 20px;
    box-shadow: 0 20px 60px rgba(0,0,0,0.15);
}

.modal-header {
    background: linear-gradient(135deg, var(--primary-gold), var(--secondary-gold));
    border: none;
    border-radius: 20px 20px 0 0;
    padding: 25px 30px;
}

.modal-title {
    color: var(--white);
    font-family: 'Playfair Display', serif;
    font-weight: 600;
    font-size: 1.3rem;
}

.modal-body {
    padding: 30px;
}

.form-label {
    font-weight: 600;
    color: var(--dark-brown);
    margin-bottom: 8px;
}

.form-control {
    border: 2px solid var(--cream);
    border-radius: 12px;
    padding: 15px;
    font-size: 1rem;
    transition: all 0.3s ease;
}

.form-control:focus {
    border-color: var(--primary-gold);
    box-shadow: 0 0 0 0.2rem var(--shadow-light);
}

/* Responsive Design */
@media (min-width: 1200px) {
    .property-detail-page {
        max-width: 1400px;
        margin: 0 auto;
    }

    .property-hero {
        padding: 40px 50px;
    }

    .detail-grid {
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    }
}

@media (max-width: 768px) {
    .property-hero {
        padding: 25px;
        text-align: center;
    }

    .property-hero h1 {
        font-size: 1.7rem;
    }

    .property-hero .location {
        font-size: 0.9rem;
    }

    .property-price h2 {
        font-size: 1.5rem;
    }

    .property-price {
        text-align: center;
        margin-top: 20px;
    }

    .main-image {
        height: 500px; /* Tăng từ 422px lên 500px (+18.5%) */
    }

    .thumbnail-list {
        gap: 15px; /* Increased from 13px for better spacing */
        padding: 20px 18px; /* Better padding for mobile */
        scroll-padding: 18px;
        /* Enhanced mobile scrolling */
        overscroll-behavior-x: contain;
    }

    .thumbnail-list::-webkit-scrollbar {
        height: 8px; /* Smaller scrollbar for mobile */
    }

    .thumbnail {
        min-width: 110px; /* Increased from 97px for better touch targets */
        width: 110px;
        height: 82px; /* Increased from 72px for better aspect ratio */
        scroll-snap-align: center; /* Center alignment for mobile */
    }

    .media-gallery-controls {
        top: 10px;
        right: 10px;
        gap: 5px;
    }

    .media-btn {
        padding: 6px 8px;
        font-size: 0.8rem;
    }

    .video-play-overlay {
        font-size: 1.5rem;
    }

    .media-type-badge {
        padding: 2px 4px;
        font-size: 0.6rem;
    }

    /* Mobile responsive navigation buttons */
    .thumbnail-nav-btn {
        width: 35px;
        height: 35px;
        font-size: 14px;
    }

    .thumbnail-nav-btn.prev {
        left: 10px;
    }

    .thumbnail-nav-btn.next {
        right: 10px;
    }

    .detail-grid {
        grid-template-columns: 1fr;
        gap: 12px;
    }

    .detail-item {
        padding: 12px;
    }

    .detail-item p {
        font-size: 0.85rem;
    }

    .detail-item strong {
        font-size: 0.8rem;
    }

    .luxury-card-body {
        padding: 20px;
    }

    .luxury-card-header {
        padding: 15px 20px;
    }

    .luxury-card-header h3 {
        font-size: 1.2rem;
    }

    .property-details {
        grid-template-columns: 1fr;
    }

    .contact-buttons .btn {
        padding: 12px 16px;
        font-size: 0.9rem;
    }

    /* Stack layout on mobile */
    .col-lg-8, .col-lg-4 {
        margin-bottom: 20px;
    }

    .contact-sidebar {
        position: static;
        margin-top: 20px;
    }
}

/* Animation */
@keyframes slideInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.luxury-card {
    animation: slideInUp 0.6s ease forwards;
}

.luxury-card:nth-child(2) { animation-delay: 0.1s; }
.luxury-card:nth-child(3) { animation-delay: 0.2s; }
.luxury-card:nth-child(4) { animation-delay: 0.3s; }
</style>
@endsection

@section('home')
<div class="container-fluid px-3">
    <div class="property-detail-page">

            <!-- 1. Hero Section -->
            <div class="property-hero">
                <div class="row">
                    <div class="col-lg-8">
                        <h1>{{ $property->Title }}</h1>
                        <p class="location">
                            <i class="bi bi-geo-alt-fill"></i>
                            {{ $property->Ward }}, {{ $property->District }}, {{ $property->Province }}
                        </p>
                    </div>
                    <div class="col-lg-4">
                        <div class="property-price">
                            <h2>{{ number_format($property->Price, 0, ',', '.') }} VND</h2>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Hình ảnh & Thư viện ảnh -->
            <div class="property-gallery">
                <!-- Main gallery image -->
                <div class="main-image">
                    @if($property->images->count() > 0)
                        @php
                            // Get first image or primary image if exists
                            $mainImage = $property->images->first();
                            $imageUrl = \App\Helpers\ImageHelper::getImageUrl($mainImage->ImagePath);
                        @endphp
                        <img src="{{ $imageUrl }}" alt="{{ $mainImage->Caption ?? $property->Title }}" id="mainImage">
                    @else
                        <img src="{{ asset('storage/images/no-image.jpeg') }}" alt="{{ $property->Title }}" id="mainImage">
                    @endif

                    <!-- Media Gallery Controls -->
                    <div class="media-gallery-controls">
                        @if($property->images->count() > 1)
                            <button class="media-btn images-btn" onclick="openImageGallery('{{ $property->PropertyID }}')">
                                <i class="bi bi-images"></i>
                                <span>{{ $property->images->count() }}</span>
                            </button>
                        @endif

                        @if($property->videos->count() > 0)
                            <button class="media-btn videos-btn" onclick="openVideoGallery('{{ $property->PropertyID }}')">
                                <i class="bi bi-play-circle"></i>
                                <span>{{ $property->videos->count() }}</span>
                            </button>
                        @endif
                    </div>
                </div>

                <!-- Thumbnail gallery with videos -->
                <div class="thumbnail-container">
                    <!-- Navigation buttons -->
                    <button class="thumbnail-nav-btn prev" id="thumbnailPrev" onclick="scrollThumbnails('left')">
                        <i class="bi bi-chevron-left"></i>
                    </button>
                    <button class="thumbnail-nav-btn next" id="thumbnailNext" onclick="scrollThumbnails('right')">
                        <i class="bi bi-chevron-right"></i>
                    </button>

                    <div class="thumbnail-list" id="thumbnailList">
                    @php
                        $mediaItems = collect();

                        // Add images
                        foreach($property->images as $image) {
                            $mediaItems->push([
                                'type' => 'image',
                                'url' => \App\Helpers\ImageHelper::getImageUrl($image->ImagePath),
                                'caption' => $image->Caption ?? $property->Title,
                                'thumbnail' => \App\Helpers\ImageHelper::getImageUrl($image->ImagePath)
                            ]);
                        }

                        // Add videos
                        foreach($property->videos as $video) {
                            $videoPath = $video->VideoPath;
                            $isYoutube = strpos($videoPath, 'youtube.com') !== false || strpos($videoPath, 'youtu.be') !== false;

                            if ($isYoutube) {
                                // Extract YouTube video ID for thumbnail
                                $videoId = '';
                                if (strpos($videoPath, 'youtube.com/watch?v=') !== false) {
                                    $videoId = substr($videoPath, strpos($videoPath, 'v=') + 2);
                                    $videoId = explode('&', $videoId)[0];
                                } elseif (strpos($videoPath, 'youtu.be/') !== false) {
                                    $videoId = substr($videoPath, strpos($videoPath, 'youtu.be/') + 9);
                                }

                                $thumbnail = $videoId ? "https://img.youtube.com/vi/{$videoId}/mqdefault.jpg" : asset('storage/images/video-placeholder.jpg');
                            } else {
                                $thumbnail = asset('storage/images/video-placeholder.jpg');
                            }

                            $mediaItems->push([
                                'type' => 'video',
                                'url' => $videoPath,
                                'caption' => $video->Caption ?? 'Video',
                                'thumbnail' => $thumbnail,
                                'is_youtube' => $isYoutube
                            ]);
                        }
                    @endphp

                    @if($mediaItems->count() > 0)
                        @foreach($mediaItems as $index => $media)
                            <div class="thumbnail media-thumbnail {{ $loop->first ? 'active' : '' }}"
                                 data-type="{{ $media['type'] }}"
                                 data-url="{{ $media['url'] }}"
                                 data-youtube="{{ $media['is_youtube'] ?? false }}"
                                 onclick="changeMedia('{{ $media['url'] }}', '{{ $media['type'] }}', {{ $media['is_youtube'] ?? 'false' }})">

                                @if($media['type'] === 'video')
                                    <div class="video-thumbnail-wrapper">
                                        <img src="{{ $media['thumbnail'] }}" alt="{{ $media['caption'] }}" loading="lazy">
                                        <div class="video-play-overlay">
                                            <i class="bi bi-play-circle-fill"></i>
                                        </div>
                                        <div class="media-type-badge video">
                                            <i class="bi bi-play-fill"></i>
                                        </div>
                                    </div>
                                @else
                                    <img src="{{ $media['thumbnail'] }}" alt="{{ $media['caption'] }}" loading="lazy">
                                    <div class="media-type-badge image">
                                        <i class="bi bi-image-fill"></i>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    @else
                        <!-- No media found, display placeholder -->
                        <div class="thumbnail active">
                            <img src="{{ asset('storage/images/no-image.jpeg') }}" alt="{{ $property->Title }}">
                        </div>
                    @endif
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-8">
                    <!-- 3. Thông tin chi tiết về bất động sản -->
                    <div class="luxury-card">
                        <div class="luxury-card-header">
                            <h3><i class="bi bi-info-circle-fill"></i>Thông tin chi tiết</h3>
                        </div>
                        <div class="luxury-card-body">
                            <div class="detail-grid">
                                @if($property->danhMuc && $property->danhMuc->ten_pro)
                                    <div class="detail-item">
                                        <p><strong><i class="bi bi-house-door-fill"></i>Loại BĐS:</strong></p>
                                        <p>{{ $property->danhMuc->ten_pro }}</p>
                                    </div>
                                @endif

                                @if($property->chiTiet)
                                    <!-- Diện tích -->
                                    @if($property->chiTiet->TotalLength && $property->chiTiet->TotalWidth)
                                        <div class="detail-item">
                                            <p><strong><i class="bi bi-rulers"></i>Diện tích:</strong></p>
                                            <p>{{ $property->chiTiet->TotalLength * $property->chiTiet->TotalWidth }} m²</p>
                                        </div>
                                    @elseif($property->chiTiet->HouseLength && $property->chiTiet->HouseWidth)
                                        <div class="detail-item">
                                            <p><strong><i class="bi bi-rulers"></i>Diện tích:</strong></p>
                                            <p>{{ $property->chiTiet->HouseLength * $property->chiTiet->HouseWidth }} m²</p>
                                        </div>
                                    @elseif($property->chiTiet->Area && $property->chiTiet->Area > 0)
                                        <div class="detail-item">
                                            <p><strong><i class="bi bi-rulers"></i>Diện tích:</strong></p>
                                            <p>{{ $property->chiTiet->Area }} m²</p>
                                        </div>
                                    @endif

                                    <!-- Số phòng ngủ -->
                                    @if($property->chiTiet->Bedroom && $property->chiTiet->Bedroom > 0)
                                        <div class="detail-item">
                                            <p><strong><i class="bi bi-door-open-fill"></i>Số phòng ngủ:</strong></p>
                                            <p>{{ $property->chiTiet->Bedroom }} phòng</p>
                                        </div>
                                    @endif

                                    <!-- Số phòng tắm -->
                                    @if($property->chiTiet->Bath_WC && $property->chiTiet->Bath_WC > 0)
                                        <div class="detail-item">
                                            <p><strong><i class="bi bi-droplet-fill"></i>Số phòng tắm/WC:</strong></p>
                                            <p>{{ $property->chiTiet->Bath_WC }} phòng</p>
                                        </div>
                                    @endif

                                    <!-- Số tầng -->
                                    @if($property->chiTiet->Floor && $property->chiTiet->Floor > 0)
                                        <div class="detail-item">
                                            <p><strong><i class="bi bi-building"></i>Số tầng:</strong></p>
                                            <p>{{ $property->chiTiet->Floor }} tầng</p>
                                        </div>
                                    @endif

                                    <!-- Cấp nhà -->
                                    @if($property->chiTiet->Levelhouse && $property->chiTiet->Levelhouse != 'N/A' && trim($property->chiTiet->Levelhouse) != '')
                                        <div class="detail-item">
                                            <p><strong><i class="bi bi-house-fill"></i>Cấp nhà:</strong></p>
                                            <p>{{ $property->chiTiet->Levelhouse }}</p>
                                        </div>
                                    @endif

                                    <!-- Ban công -->
                                    @if($property->chiTiet->Balcony !== null)
                                        <div class="detail-item">
                                            <p><strong><i class="bi bi-door-open-fill"></i>Ban công:</strong></p>
                                            <p>{{ $property->chiTiet->Balcony == 1 ? 'Có' : 'Không' }}</p>
                                        </div>
                                    @endif

                                    <!-- Đường rộng -->
                                    @if($property->chiTiet->Road && $property->chiTiet->Road > 0)
                                        <div class="detail-item">
                                            <p><strong><i class="bi bi-signpost-fill"></i>Đường rộng:</strong></p>
                                            <p>{{ $property->chiTiet->Road }} m</p>
                                        </div>
                                    @endif

                                    <!-- Pháp lý -->
                                    @if($property->chiTiet->legal && $property->chiTiet->legal != 'N/A' && trim($property->chiTiet->legal) != '')
                                        <div class="detail-item">
                                            <p><strong><i class="bi bi-file-earmark-text-fill"></i>Pháp lý:</strong></p>
                                            <p>{{ $property->chiTiet->legal }}</p>
                                        </div>
                                    @endif

                                    <!-- Hướng view -->
                                    @if($property->chiTiet->view && $property->chiTiet->view != 'N/A' && trim($property->chiTiet->view) != '')
                                        <div class="detail-item">
                                            <p><strong><i class="bi bi-compass-fill"></i>Hướng:</strong></p>
                                            <p>{{ $property->chiTiet->view }}</p>
                                        </div>
                                    @endif

                                    <!-- Gần -->
                                    @if($property->chiTiet->near && $property->chiTiet->near != 'N/A' && trim($property->chiTiet->near) != '')
                                        <div class="detail-item">
                                            <p><strong><i class="bi bi-geo-alt-fill"></i>Gần:</strong></p>
                                            <p>{{ $property->chiTiet->near }}</p>
                                        </div>
                                    @endif

                                    <!-- Nội thất -->
                                    @if($property->chiTiet->Interior && $property->chiTiet->Interior != 'N/A' && trim($property->chiTiet->Interior) != '')
                                        <div class="detail-item">
                                            <p><strong><i class="bi bi-house-gear-fill"></i>Nội thất:</strong></p>
                                            <p>{{ $property->chiTiet->Interior }}</p>
                                        </div>
                                    @endif

                                    <!-- Giá điện -->
                                    @if($property->chiTiet->PowerPrice && $property->chiTiet->PowerPrice != 'N/A' && trim($property->chiTiet->PowerPrice) != '' && $property->chiTiet->PowerPrice != '0')
                                        <div class="detail-item">
                                            <p><strong><i class="bi bi-lightning-fill"></i>Giá điện:</strong></p>
                                            <p>{{ $property->chiTiet->PowerPrice }}</p>
                                        </div>
                                    @endif

                                    <!-- Giá nước -->
                                    @if($property->chiTiet->WaterPrice && $property->chiTiet->WaterPrice != 'N/A' && trim($property->chiTiet->WaterPrice) != '' && $property->chiTiet->WaterPrice != '0')
                                        <div class="detail-item">
                                            <p><strong><i class="bi bi-droplet-half"></i>Giá nước:</strong></p>
                                            <p>{{ $property->chiTiet->WaterPrice }}</p>
                                        </div>
                                    @endif

                                    <!-- Tiện ích -->
                                    @if($property->chiTiet->Utilities && $property->chiTiet->Utilities != 'N/A' && trim($property->chiTiet->Utilities) != '' && $property->chiTiet->Utilities != '0')
                                        <div class="detail-item">
                                            <p><strong><i class="bi bi-tools"></i>Tiện ích:</strong></p>
                                            <p>{{ $property->chiTiet->Utilities }}</p>
                                        </div>
                                    @endif
                                @endif
                            </div>

                            <!-- Thông tin kích thước chi tiết -->
                            @if($property->chiTiet && ($property->chiTiet->HouseLength || $property->chiTiet->HouseWidth || $property->chiTiet->TotalLength || $property->chiTiet->TotalWidth))
                                <div class="mt-4">
                                    <h5 style="color: var(--dark-brown); font-family: 'Playfair Display', serif; margin-bottom: 20px;">
                                        <i class="bi bi-rulers me-2" style="color: var(--primary-gold);"></i>Kích thước chi tiết
                                    </h5>
                                    <div class="detail-grid">
                                        @if($property->chiTiet->HouseLength && $property->chiTiet->HouseWidth)
                                            <div class="detail-item">
                                                <p><strong><i class="bi bi-house-fill"></i>Kích thước nhà:</strong></p>
                                                <p>{{ $property->chiTiet->HouseLength }}m x {{ $property->chiTiet->HouseWidth }}m</p>
                                            </div>
                                        @endif

                                        @if($property->chiTiet->TotalLength && $property->chiTiet->TotalWidth)
                                            <div class="detail-item">
                                                <p><strong><i class="bi bi-bounding-box"></i>Kích thước tổng:</strong></p>
                                                <p>{{ $property->chiTiet->TotalLength }}m x {{ $property->chiTiet->TotalWidth }}m</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                <!-- 4. Mô tả chi tiết & điểm nổi bật -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h3><i class="bi bi-info-circle me-2"></i>Mô tả chi tiết & Điểm nổi bật</h3>
                    </div>
                    <div class="card-body">
                        <div class="property-description">
                            <h4>Mô tả chi tiết</h4>
                            <div class="description-content">
                                {!! nl2br(e($property->Description)) !!}
                            </div>
                        </div>

                        <div class="property-highlights mt-4">
                            <h4>Điểm nổi bật</h4>
                            <ul class="highlights-list">
                                @if($property->TypePro == 'Sale' || $property->TypePro == 'Cho bán')
                                    <!-- Điểm nổi bật cho BĐS bán -->
                                    @if($property->danhMuc && str_contains(strtolower($property->danhMuc->ten_pro), 'nhà'))
                                        <li><i class="bi bi-check-circle-fill text-success me-2"></i>Sổ đỏ chính chủ, pháp lý hoàn chỉnh</li>
                                        <li><i class="bi bi-check-circle-fill text-success me-2"></i>Vị trí thuận lợi, gần trường học và chợ</li>
                                        <li><i class="bi bi-check-circle-fill text-success me-2"></i>Thiết kế hiện đại, tối ưu không gian</li>
                                        <li><i class="bi bi-check-circle-fill text-success me-2"></i>Hệ thống điện nước đầy đủ, ổn định</li>
                                        <li><i class="bi bi-check-circle-fill text-success me-2"></i>Giao thông thuận tiện, dễ dàng di chuyển</li>
                                    @elseif($property->danhMuc && str_contains(strtolower($property->danhMuc->ten_pro), 'đất'))
                                        <li><i class="bi bi-check-circle-fill text-success me-2"></i>Đất thổ cư 100%, sổ đỏ riêng</li>
                                        <li><i class="bi bi-check-circle-fill text-success me-2"></i>Mặt tiền rộng, thuận lợi kinh doanh</li>
                                        <li><i class="bi bi-check-circle-fill text-success me-2"></i>Hạ tầng hoàn thiện, điện nước đầy đủ</li>
                                        <li><i class="bi bi-check-circle-fill text-success me-2"></i>Khu vực phát triển mạnh, giá tăng ổn định</li>
                                        <li><i class="bi bi-check-circle-fill text-success me-2"></i>Gần các tiện ích công cộng</li>
                                    @elseif($property->danhMuc && str_contains(strtolower($property->danhMuc->ten_pro), 'chung cư'))
                                        <li><i class="bi bi-check-circle-fill text-success me-2"></i>Chung cư cao cấp, view đẹp thoáng mát</li>
                                        <li><i class="bi bi-check-circle-fill text-success me-2"></i>Tiện ích đầy đủ: hồ bơi, gym, sân chơi</li>
                                        <li><i class="bi bi-check-circle-fill text-success me-2"></i>Bảo vệ 24/7, camera an ninh</li>
                                        <li><i class="bi bi-check-circle-fill text-success me-2"></i>Gần trung tâm thương mại, bệnh viện</li>
                                        <li><i class="bi bi-check-circle-fill text-success me-2"></i>Thang máy hiện đại, hầm để xe rộng</li>
                                    @else
                                        <li><i class="bi bi-check-circle-fill text-success me-2"></i>Pháp lý rõ ràng, đầy đủ giấy tờ</li>
                                        <li><i class="bi bi-check-circle-fill text-success me-2"></i>Vị trí đắc địa, tiềm năng phát triển</li>
                                        <li><i class="bi bi-check-circle-fill text-success me-2"></i>Hạ tầng hoàn thiện, tiện ích đầy đủ</li>
                                        <li><i class="bi bi-check-circle-fill text-success me-2"></i>Cơ hội đầu tư sinh lời cao</li>
                                        <li><i class="bi bi-check-circle-fill text-success me-2"></i>Giao thông kết nối thuận lợi</li>
                                    @endif
                                @else
                                    <!-- Điểm nổi bật cho BĐS cho thuê -->
                                    @if($property->danhMuc && str_contains(strtolower($property->danhMuc->ten_pro), 'nhà'))
                                        <li><i class="bi bi-check-circle-fill text-success me-2"></i>Nội thất cơ bản, sẵn sàng ở ngay</li>
                                        <li><i class="bi bi-check-circle-fill text-success me-2"></i>Khu vực an ninh, dân trí cao</li>
                                        <li><i class="bi bi-check-circle-fill text-success me-2"></i>Gần trường học, bệnh viện, chợ</li>
                                        <li><i class="bi bi-check-circle-fill text-success me-2"></i>Giá thuê hợp lý, ổn định lâu dài</li>
                                        <li><i class="bi bi-check-circle-fill text-success me-2"></i>Chỗ để xe rộng rãi, thoáng mát</li>
                                    @elseif($property->danhMuc && str_contains(strtolower($property->danhMuc->ten_pro), 'chung cư'))
                                        <li><i class="bi bi-check-circle-fill text-success me-2"></i>Full nội thất cao cấp, đẹp như hình</li>
                                        <li><i class="bi bi-check-circle-fill text-success me-2"></i>View đẹp, thoáng mát, ánh sáng tự nhiên</li>
                                        <li><i class="bi bi-check-circle-fill text-success me-2"></i>Tiện ích chung: gym, hồ bơi miễn phí</li>
                                        <li><i class="bi bi-check-circle-fill text-success me-2"></i>Bảo vệ 24/7, thang máy hiện đại</li>
                                        <li><i class="bi bi-check-circle-fill text-success me-2"></i>Gần các trung tâm mua sắm lớn</li>
                                    @elseif($property->danhMuc && str_contains(strtolower($property->danhMuc->ten_pro), 'phòng'))
                                        <li><i class="bi bi-check-circle-fill text-success me-2"></i>Phòng riêng biệt, WC khép kín</li>
                                        <li><i class="bi bi-check-circle-fill text-success me-2"></i>Điện nước giá dân, wifi miễn phí</li>
                                        <li><i class="bi bi-check-circle-fill text-success me-2"></i>Khu vực yên tĩnh, thuận tiện học tập</li>
                                        <li><i class="bi bi-check-circle-fill text-success me-2"></i>Gần trường đại học, khu công nghiệp</li>
                                        <li><i class="bi bi-check-circle-fill text-success me-2"></i>Có chỗ nấu ăn, giặt phơi riêng</li>
                                    @else
                                        <li><i class="bi bi-check-circle-fill text-success me-2"></i>Sẵn sàng nhận khách, có thể vào ngay</li>
                                        <li><i class="bi bi-check-circle-fill text-success me-2"></i>Vị trí thuận lợi, giao thông dễ dàng</li>
                                        <li><i class="bi bi-check-circle-fill text-success me-2"></i>Giá cả hợp lý, có thể thương lượng</li>
                                        <li><i class="bi bi-check-circle-fill text-success me-2"></i>Chủ nhà thân thiện, hỗ trợ tận tình</li>
                                        <li><i class="bi bi-check-circle-fill text-success me-2"></i>Hợp đồng linh hoạt, gia hạn dễ dàng</li>
                                    @endif
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- 5. Tiến độ thanh toán -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h3><i class="bi bi-credit-card me-2"></i>
                            @if($property->TypePro == 'Sale' || $property->TypePro == 'Cho bán')
                                Tiến độ thanh toán
                            @else
                                Bảng giá thuê
                            @endif
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="payment-schedule">
                            <div class="table-responsive">
                                @if($property->TypePro == 'Sale' || $property->TypePro == 'Cho bán')
                                    <!-- Bảng thanh toán cho BĐS bán -->
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Đợt</th>
                                                <th>Mô tả</th>
                                                <th>Tỷ lệ (%)</th>
                                                <th>Thời gian</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><strong>1</strong></td>
                                                <td>Đặt cọc</td>
                                                <td>5%</td>
                                                <td>Khi ký hợp đồng đặt cọc</td>
                                            </tr>
                                            <tr>
                                                <td><strong>2</strong></td>
                                                <td>Thanh toán lần 1</td>
                                                <td>20%</td>
                                                <td>Khi ký hợp đồng mua bán</td>
                                            </tr>
                                            <tr>
                                                <td><strong>3</strong></td>
                                                <td>Thanh toán lần 2</td>
                                                <td>30%</td>
                                                <td>Khi hoàn thiện phần thô</td>
                                            </tr>
                                            <tr>
                                                <td><strong>4</strong></td>
                                                <td>Thanh toán lần 3</td>
                                                <td>40%</td>
                                                <td>Khi hoàn thiện và bàn giao</td>
                                            </tr>
                                            <tr>
                                                <td><strong>5</strong></td>
                                                <td>Bảo hành</td>
                                                <td>5%</td>
                                                <td>Sau 12 tháng bảo hành</td>
                                            </tr>
                                        </tbody>
                                        <tfoot>
                                            <tr class="table-warning">
                                                <th colspan="3">Tổng cộng</th>
                                                <th>100%</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                @else
                                    <!-- Bảng giá thuê cho BĐS cho thuê -->
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Thời hạn thuê</th>
                                                <th>Giá thuê/tháng</th>
                                                <th>Tổng tiền</th>
                                                <th>Ghi chú</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><strong>6 tháng</strong></td>
                                                <td>{{ number_format($property->Price, 0, ',', '.') }} VND</td>
                                                <td>{{ number_format($property->Price * 6, 0, ',', '.') }} VND</td>
                                                <td>Trả trước 3 tháng</td>
                                            </tr>
                                            <tr>
                                                <td><strong>12 tháng</strong></td>
                                                <td>{{ number_format($property->Price * 0.95, 0, ',', '.') }} VND</td>
                                                <td>{{ number_format($property->Price * 0.95 * 12, 0, ',', '.') }} VND</td>
                                                <td>Giảm 5%, trả trước 3 tháng</td>
                                            </tr>
                                            <tr>
                                                <td><strong>18 tháng</strong></td>
                                                <td>{{ number_format($property->Price * 0.92, 0, ',', '.') }} VND</td>
                                                <td>{{ number_format($property->Price * 0.92 * 18, 0, ',', '.') }} VND</td>
                                                <td>Giảm 8%, trả trước 6 tháng</td>
                                            </tr>
                                            <tr>
                                                <td><strong>24+ tháng</strong></td>
                                                <td>{{ number_format($property->Price * 0.90, 0, ',', '.') }} VND</td>
                                                <td>{{ number_format($property->Price * 0.90 * 24, 0, ',', '.') }} VND</td>
                                                <td>Giảm 10%, trả trước 6 tháng</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                @endif
                            </div>

                            <div class="payment-notes mt-3">
                                <h5><i class="bi bi-info-circle me-2"></i>
                                    @if($property->TypePro == 'Sale' || $property->TypePro == 'Cho bán')
                                        Lưu ý về thanh toán:
                                    @else
                                        Lưu ý về thuê:
                                    @endif
                                </h5>
                                <ul class="list-unstyled">
                                    @if($property->TypePro == 'Sale' || $property->TypePro == 'Cho bán')
                                        <li><i class="bi bi-check2 text-success me-2"></i>Các đợt thanh toán có thể được điều chỉnh theo thỏa thuận</li>
                                        <li><i class="bi bi-check2 text-success me-2"></i>Hỗ trợ vay ngân hàng lên đến 70% giá trị bất động sản</li>
                                        <li><i class="bi bi-check2 text-success me-2"></i>Miễn phí thủ tục pháp lý và chuyển nhượng</li>
                                        <li><i class="bi bi-check2 text-success me-2"></i>Bảo hành 24 tháng cho hạ tầng kỹ thuật</li>
                                    @else
                                        <li><i class="bi bi-check2 text-success me-2"></i>Giá thuê có thể thương lượng tùy theo thời hạn</li>
                                        <li><i class="bi bi-check2 text-success me-2"></i>Tiền cọc bằng 2 tháng tiền thuê</li>
                                        <li><i class="bi bi-check2 text-success me-2"></i>Miễn phí dịch vụ quản lý và bảo trì</li>
                                        <li><i class="bi bi-check2 text-success me-2"></i>Hỗ trợ gia hạn hợp đồng với ưu đãi đặc biệt</li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 6. Vị trí bất động sản -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h3><i class="bi bi-geo-alt me-2"></i>Vị trí bất động sản</h3>
                    </div>
                    <div class="card-body">
                        <div class="location-info">
                            <div class="address-details">
                                <p><strong>Khu vực:</strong></p>
                                <div class="address-breakdown">
                                    <p><i class="bi bi-building me-2"></i><strong>Quận/Huyện:</strong> {{ $property->District }}</p>
                                    <p><i class="bi bi-geo-alt me-2"></i><strong>Tỉnh/Thành phố:</strong> {{ $property->Province }}</p>
                                </div>
                                <div class="full-address mt-3 p-3 bg-light rounded">
                                    <strong>Khu vực:</strong><br>
                                    {{ $property->District }}, {{ $property->Province }}
                                </div>
                            </div>

                            <!-- Interactive Map with Leaflet -->
                            <div class="map-container mt-4">
                                <div id="propertyMap" style="width: 100%; height: 400px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); z-index: 1;"></div>
                                <p class="text-muted mt-2" style="font-size: 0.9rem;">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Bản đồ hiển thị khu vực {{ $property->District }}, {{ $property->Province }}. Liên hệ để biết địa chỉ chi tiết.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="col-lg-4">
                <!-- Thông tin liên hệ -->
                <div class="contact-sidebar">
                    <div class="contact-card">
                        @if($property->moigioi)
                            <!-- Thông tin môi giới -->
                            <div class="agent-profile">
                                @if($property->moigioi->Avatar)
                                    <img src="{{ asset('storage/' . $property->moigioi->Avatar) }}" alt="{{ $property->moigioi->Name }}" class="agent-avatar">
                                @else
                                    <div class="agent-avatar letter-avatar letter-avatar-agent">
                                        <span>{{ strtoupper(substr($property->moigioi->Name, 0, 1)) }}</span>
                                    </div>
                                @endif
                                <div class="agent-info">
                                    <h4>{{ $property->moigioi->Name }}</h4>
                                    <p>Môi giới bất động sản</p>
                                </div>
                            </div>

                            <div class="contact-details">
                                <p><i class="bi bi-telephone-fill"></i> {{ $property->moigioi->Phone }}</p>
                                <p><i class="bi bi-envelope-fill"></i> {{ $property->moigioi->Email }}</p>
                            </div>
                        @elseif($property->chusohuu)
                            <!-- Thông tin chủ sở hữu -->
                            <div class="agent-profile">
                                @if($property->chusohuu->Avatar)
                                    <img src="{{ asset('storage/' . $property->chusohuu->Avatar) }}" alt="{{ $property->chusohuu->Name }}" class="agent-avatar">
                                @else
                                    <div class="agent-avatar letter-avatar letter-avatar-owner">
                                        <span>{{ strtoupper(substr($property->chusohuu->Name, 0, 1)) }}</span>
                                    </div>
                                @endif
                                <div class="agent-info">
                                    <h4>{{ $property->chusohuu->Name }}</h4>
                                    <p>Chủ sở hữu</p>
                                </div>
                            </div>

                            <div class="contact-details">
                                <p><i class="bi bi-telephone-fill"></i> {{ $property->chusohuu->Phone }}</p>
                                <p><i class="bi bi-envelope-fill"></i> {{ $property->chusohuu->Email }}</p>
                            </div>
                        @else
                            <!-- Thông tin mặc định -->
                            <div class="agent-profile">
                                <div class="agent-avatar letter-avatar letter-avatar-default">
                                    <span>C</span>
                                </div>
                                <div class="agent-info">
                                    <h4>Chuyên viên tư vấn</h4>
                                    <p>Đội ngũ chuyên nghiệp</p>
                                </div>
                            </div>

                            <div class="contact-details">
                                <p><i class="bi bi-telephone-fill"></i> {{ $property->ContactPhone ?? '0901.234.567' }}</p>
                                <p><i class="bi bi-envelope-fill"></i> {{ $property->ContactEmail ?? 'info@batdongsan.vn' }}</p>
                            </div>
                        @endif

                        <!-- Buttons liên hệ -->
                        <div class="contact-buttons">
                            @php
                                $contactPhone = $property->moigioi->Phone ?? $property->chusohuu->Phone ?? $property->ContactPhone ?? '0901234567';
                                $contactName = $property->moigioi->Name ?? $property->chusohuu->Name ?? 'Chuyên viên tư vấn';
                                $propertyTitle = $property->Title;
                                $propertyPrice = number_format($property->Price, 0, ',', '.');
                                $zaloMessage = urlencode("Xin chào! Tôi quan tâm đến BĐS: {$propertyTitle} - Giá: {$propertyPrice} VND. Vui lòng tư vấn thêm.");
                            @endphp

                            <a href="https://zalo.me/{{ str_replace(['(', ')', ' ', '-'], '', $contactPhone) }}?text={{ $zaloMessage }}"
                               target="_blank" class="btn btn-success">
                                <i class="bi bi-chat-square-text-fill me-2"></i>Chat Zalo
                            </a>

                            <a href="tel:{{ $contactPhone }}" class="btn btn-warning">
                                <i class="bi bi-telephone-fill me-2"></i>Gọi ngay
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bất động sản liên quan -->
        <div class="related-properties">
            <h3><i class="bi bi-house-heart me-3"></i>Bất động sản liên quan</h3>

            <div class="row property-cards">
                @forelse($relatedProperties as $relatedProperty)
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="property-card">
                            <div class="property-image">
                                @if($relatedProperty->images->count() > 0)
                                    @php
                                        $mainImage = $relatedProperty->images->first();
                                        $imageUrl = \App\Helpers\ImageHelper::getImageUrl($mainImage->ImagePath);
                                    @endphp
                                    <img src="{{ $imageUrl }}" alt="{{ $relatedProperty->Title }}" loading="lazy">
                                @else
                                    <img src="{{ asset('storage/images/no-image.jpeg') }}" alt="{{ $relatedProperty->Title }}" loading="lazy">
                                @endif

                                <!-- Property Type Badge -->
                                <div style="position: absolute; top: 15px; left: 15px; background: {{ $relatedProperty->PropertyType == 'sale' ? 'linear-gradient(135deg, #ff6b6b, #ee5a52)' : 'linear-gradient(135deg, #4ecdc4, #44a08d)' }}; color: white; padding: 5px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">
                                    {{ $relatedProperty->PropertyType == 'sale' ? 'Bán' : 'Cho thuê' }}
                                </div>
                            </div>
                            <div class="property-content">
                                <h3 class="property-title">{{ \Illuminate\Support\Str::limit($relatedProperty->Title, 50) }}</h3>
                                <p class="property-location">
                                    <i class="bi bi-geo-alt"></i>
                                    {{ $relatedProperty->District }}, {{ $relatedProperty->Province }}
                                </p>



                                <div class="property-footer">
                                    <div class="property-price-display">
                                        {{ number_format($relatedProperty->Price, 0, ',', '.') }} đ
                                        @if($relatedProperty->chiTiet && $relatedProperty->chiTiet->Area > 0)
                                            <small style="color: #718096; font-weight: 500; display: block; font-size: 0.85rem;">
                                                {{ number_format(($relatedProperty->Price / $relatedProperty->chiTiet->Area), 0, ',', '.') }} đ/m²
                                            </small>
                                        @endif
                                    </div>
                                    @if($relatedProperty->PropertyType == 'sale')
                                        <a href="{{ route('properties.sale.detail', $relatedProperty->PropertyID) }}" class="btn btn-view">
                                            <i class="bi bi-eye me-1"></i>Xem chi tiết
                                        </a>
                                    @else
                                        <a href="{{ route('properties.rent.detail', $relatedProperty->PropertyID) }}" class="btn btn-view">
                                            <i class="bi bi-eye me-1"></i>Xem chi tiết
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div style="text-align: center; padding: 60px 20px; background: rgba(255,255,255,0.8); border-radius: 20px; backdrop-filter: blur(10px);">
                            <i class="bi bi-house-x" style="font-size: 4rem; color: #cbd5e0; margin-bottom: 20px;"></i>
                            <h4 style="color: #4a5568; margin-bottom: 10px;">Không có bất động sản liên quan</h4>
                            <p style="color: #718096;">Chúng tôi sẽ cập nhật thêm bất động sản tương tự trong thời gian sớm nhất.</p>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Message Modal -->
<div class="modal fade" id="messageModal" tabindex="-1" aria-labelledby="messageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 15px; border: none; box-shadow: 0 20px 60px rgba(0,0,0,0.15);">
            <div class="modal-header" style="background: linear-gradient(135deg, #ffd700, #ffb74d); border: none; border-radius: 15px 15px 0 0;">
                <h5 class="modal-title" id="messageModalLabel" style="color: #fff; font-weight: 600;">
                    <i class="bi bi-chat-dots-fill me-2"></i>Gửi tin nhắn
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="padding: 30px;">
                <form id="messageForm">
                    <div class="mb-3">
                        <label for="senderName" class="form-label" style="font-weight: 600; color: #4a5568;">Họ và tên</label>
                        <input type="text" class="form-control" id="senderName" placeholder="Nhập họ và tên của bạn" required
                               style="border-radius: 10px; border: 2px solid #e2e8f0; padding: 12px 15px;">
                    </div>
                    <div class="mb-3">
                        <label for="senderPhone" class="form-label" style="font-weight: 600; color: #4a5568;">Số điện thoại</label>
                        <input type="tel" class="form-control" id="senderPhone" placeholder="Nhập số điện thoại của bạn" required
                               style="border-radius: 10px; border: 2px solid #e2e8f0; padding: 12px 15px;">
                    </div>
                    <div class="mb-3">
                        <label for="messageContent" class="form-label" style="font-weight: 600; color: #4a5568;">Nội dung tin nhắn</label>
                        <textarea class="form-control" id="messageContent" rows="4" placeholder="Nhập nội dung tin nhắn..." required
                                  style="border-radius: 10px; border: 2px solid #e2e8f0; padding: 12px 15px; resize: vertical;"></textarea>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary" style="background: linear-gradient(135deg, #ffd700, #ffb74d); border: none; border-radius: 10px; padding: 12px; font-weight: 600; color: #fff;">
                            <i class="bi bi-send-fill me-2"></i>Gửi tin nhắn
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Message Modal functionality
    function openMessageModal(contactName, contactPhone) {
        const modal = new bootstrap.Modal(document.getElementById('messageModal'));

        // Pre-fill message content
        const propertyTitle = '{{ $property->Title }}';
        const propertyPrice = '{{ number_format($property->Price, 0, ",", ".") }}';
        const defaultMessage = `Xin chào ${contactName}!\n\nTôi quan tâm đến bất động sản: ${propertyTitle}\nGiá: ${propertyPrice} VND\n\nVui lòng tư vấn thêm thông tin chi tiết. Cảm ơn!`;

        document.getElementById('messageContent').value = defaultMessage;
        modal.show();
    }

    // Thumbnail Navigation Functions
    function scrollThumbnails(direction) {
        const thumbnailList = document.getElementById('thumbnailList');
        if (!thumbnailList) return;

        const scrollAmount = 200; // Pixels to scroll
        const currentScroll = thumbnailList.scrollLeft;

        if (direction === 'left') {
            thumbnailList.scrollTo({
                left: currentScroll - scrollAmount,
                behavior: 'smooth'
            });
        } else if (direction === 'right') {
            thumbnailList.scrollTo({
                left: currentScroll + scrollAmount,
                behavior: 'smooth'
            });
        }

        // Update button states after scrolling
        setTimeout(() => updateNavButtons(), 100);
    }

    function updateNavButtons() {
        const thumbnailList = document.getElementById('thumbnailList');
        const prevBtn = document.getElementById('thumbnailPrev');
        const nextBtn = document.getElementById('thumbnailNext');

        if (!thumbnailList || !prevBtn || !nextBtn) return;

        const { scrollLeft, scrollWidth, clientWidth } = thumbnailList;

        // Check if we can scroll left
        if (scrollLeft <= 0) {
            prevBtn.disabled = true;
            prevBtn.classList.add('hidden');
        } else {
            prevBtn.disabled = false;
            prevBtn.classList.remove('hidden');
        }

        // Check if we can scroll right
        if (scrollLeft >= scrollWidth - clientWidth - 1) {
            nextBtn.disabled = true;
            nextBtn.classList.add('hidden');
        } else {
            nextBtn.disabled = false;
            nextBtn.classList.remove('hidden');
        }
    }

    // Initialize navigation buttons on page load
    function initThumbnailNavigation() {
        const thumbnailList = document.getElementById('thumbnailList');
        if (!thumbnailList) return;

        // Update buttons on scroll
        thumbnailList.addEventListener('scroll', updateNavButtons);

        // Update buttons on window resize
        window.addEventListener('resize', updateNavButtons);

        // Check if navigation is needed
        const needsNavigation = thumbnailList.scrollWidth > thumbnailList.clientWidth;
        const navButtons = document.querySelectorAll('.thumbnail-nav-btn');

        if (needsNavigation) {
            navButtons.forEach(btn => btn.style.display = 'flex');
        } else {
            navButtons.forEach(btn => btn.style.display = 'none');
        }

        // Initial update
        updateNavButtons();
    }

    // Enhanced media gallery functionality with smooth transitions for images and videos
    function changeMedia(mediaSrc, mediaType, isYoutube = false) {
        const mainImageContainer = document.querySelector('.main-image');

        // Add smooth fade effect
        mainImageContainer.style.transition = 'opacity 0.3s ease';
        mainImageContainer.style.opacity = '0.7';

        setTimeout(() => {
            if (mediaType === 'video') {
                if (isYoutube) {
                    // Extract YouTube video ID for embedding
                    let videoId = '';
                    if (mediaSrc.includes('youtube.com/watch?v=')) {
                        videoId = mediaSrc.split('v=')[1]?.split('&')[0];
                    } else if (mediaSrc.includes('youtu.be/')) {
                        videoId = mediaSrc.split('youtu.be/')[1]?.split('?')[0];
                    }

                    if (videoId) {
                        mainImageContainer.innerHTML = `
                            <iframe
                                width="100%"
                                height="100%"
                                src="https://www.youtube.com/embed/${videoId}"
                                frameborder="0"
                                allowfullscreen
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                            ></iframe>
                        `;
                    }
                } else {
                    // Direct video file
                    mainImageContainer.innerHTML = `
                        <video
                            width="100%"
                            height="100%"
                            controls
                            style="object-fit: cover;"
                        >
                            <source src="${mediaSrc}" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                    `;
                }
            } else {
                // Display image
                mainImageContainer.innerHTML = `
                    <img src="${mediaSrc}" alt="Property Image" id="mainImage" style="width: 100%; height: 100%; object-fit: cover;">
                `;
            }

            mainImageContainer.style.opacity = '1';
        }, 150);

        // Update active thumbnail with enhanced styling and smooth scrolling
        const thumbnails = document.querySelectorAll('.thumbnail');
        const thumbnailList = document.querySelector('.thumbnail-list');

        thumbnails.forEach(thumbnail => {
            thumbnail.classList.remove('active');
            if (thumbnail.dataset.url === mediaSrc) {
                thumbnail.classList.add('active');

                // Smooth scroll to active thumbnail if it's not visible
                if (thumbnailList && thumbnail) {
                    const containerRect = thumbnailList.getBoundingClientRect();
                    const thumbnailRect = thumbnail.getBoundingClientRect();

                    // Check if thumbnail is outside visible area
                    if (thumbnailRect.left < containerRect.left || thumbnailRect.right > containerRect.right) {
                        thumbnail.scrollIntoView({
                            behavior: 'smooth',
                            block: 'nearest',
                            inline: 'center'
                        });
                    }
                }
            }
        });
    }

    // Enhanced thumbnail navigation with keyboard support
    function initThumbnailNavigation() {
        const thumbnailList = document.querySelector('.thumbnail-list');
        const thumbnails = document.querySelectorAll('.thumbnail');

        if (!thumbnailList || thumbnails.length === 0) return;

        // Add keyboard navigation
        thumbnailList.addEventListener('keydown', function(e) {
            const activeThumb = document.querySelector('.thumbnail.active');
            if (!activeThumb) return;

            let nextThumb = null;

            switch(e.key) {
                case 'ArrowLeft':
                    nextThumb = activeThumb.previousElementSibling;
                    break;
                case 'ArrowRight':
                    nextThumb = activeThumb.nextElementSibling;
                    break;
                case 'Home':
                    nextThumb = thumbnails[0];
                    break;
                case 'End':
                    nextThumb = thumbnails[thumbnails.length - 1];
                    break;
            }

            if (nextThumb && nextThumb.classList.contains('thumbnail')) {
                e.preventDefault();

                nextThumb.click();
                nextThumb.focus();
            }
        });

        // Make thumbnails focusable for accessibility
        thumbnails.forEach((thumb, index) => {
            thumb.setAttribute('tabindex', index === 0 ? '0' : '-1');
            thumb.setAttribute('role', 'button');
            thumb.setAttribute('aria-label', `View ${thumb.dataset.type || 'media'} ${index + 1}`);
        });
    }

    // Smooth scroll indicators for large thumbnail collections
    function initScrollIndicators() {
        const thumbnailList = document.querySelector('.thumbnail-list');
        if (!thumbnailList) return;

        function updateScrollIndicators() {
            const { scrollLeft, scrollWidth, clientWidth } = thumbnailList;
            const gallery = document.querySelector('.property-gallery');

            if (gallery) {
                // Show/hide left scroll indicator
                if (scrollLeft > 10) {
                    gallery.classList.add('has-scroll-left');
                } else {
                    gallery.classList.remove('has-scroll-left');
                }

                // Show/hide right scroll indicator
                if (scrollLeft < scrollWidth - clientWidth - 10) {
                    gallery.classList.add('has-scroll-right');
                } else {
                    gallery.classList.remove('has-scroll-right');
                }
            }
        }

        thumbnailList.addEventListener('scroll', updateScrollIndicators);
        window.addEventListener('resize', updateScrollIndicators);
        updateScrollIndicators(); // Initial call
    }

    // Legacy function for backward compatibility
    function changeImage(imgSrc) {
        changeMedia(imgSrc, 'image', false);
    }

    // Enhanced DOM loaded functionality
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Leaflet Map
        initPropertyMap();

        // Initialize enhanced thumbnail features
        initThumbnailNavigation();
        initScrollIndicators();

        // Smooth scroll reveal animation for cards
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -30px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        // Apply animation to cards
        const animatedElements = document.querySelectorAll('.info-card, .property-card, .contact-card');
        animatedElements.forEach((element, index) => {
            element.style.opacity = '0';
            element.style.transform = 'translateY(30px)';
            element.style.transition = `opacity 0.6s ease ${index * 0.1}s, transform 0.6s ease ${index * 0.1}s`;
            observer.observe(element);
        });

        // Message form submission
        const messageForm = document.getElementById('messageForm');
        if (messageForm) {
            messageForm.addEventListener('submit', function(e) {
                e.preventDefault();

                const submitBtn = this.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;

                // Get form data
                const senderName = document.getElementById('senderName').value;
                const senderPhone = document.getElementById('senderPhone').value;
                const messageContent = document.getElementById('messageContent').value;

                // Show loading state
                submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Đang gửi...';
                submitBtn.disabled = true;
                submitBtn.style.background = 'linear-gradient(135deg, #94a3b8, #64748b)';

                // Simulate message sending (replace with actual AJAX call)
                setTimeout(() => {
                    submitBtn.innerHTML = '<i class="bi bi-check-circle-fill me-2"></i>Đã gửi thành công!';
                    submitBtn.style.background = 'linear-gradient(135deg, #48bb78, #38a169)';

                    // Reset after success message
                    setTimeout(() => {
                        submitBtn.innerHTML = originalText;
                        submitBtn.disabled = false;
                        submitBtn.style.background = '';
                        this.reset();

                        // Close modal
                        const modal = bootstrap.Modal.getInstance(document.getElementById('messageModal'));
                        modal.hide();

                        // Show success notification
                        showNotification('Tin nhắn đã được gửi thành công! Chúng tôi sẽ liên hệ với bạn sớm nhất.', 'success');
                    }, 2000);
                }, 1500);
            });
        }

        // Enhanced thumbnail interactions
        const thumbnails = document.querySelectorAll('.thumbnail');
        thumbnails.forEach(thumbnail => {
            thumbnail.addEventListener('mouseenter', function() {
                if (!this.classList.contains('active')) {
                    this.style.transform = 'translateY(-3px) scale(1.02)';
                    this.style.boxShadow = '0 8px 25px rgba(102, 126, 234, 0.2)';
                }
            });

            thumbnail.addEventListener('mouseleave', function() {
                if (!this.classList.contains('active')) {
                    this.style.transform = 'translateY(0) scale(1)';
                    this.style.boxShadow = '';
                }
            });
        });

        // Phone call tracking
        const phoneLinks = document.querySelectorAll('a[href^="tel:"]');
        phoneLinks.forEach(link => {
            link.addEventListener('click', function() {
                // Add analytics tracking here if needed
                console.log('Phone call initiated to:', this.href);
            });
        });

        // Initialize thumbnail navigation and scrolling indicators
        initThumbnailNavigation();
        initScrollIndicators();
    });

    // Leaflet Map initialization
    function initPropertyMap() {
        // Location data from PHP
        const district = '{{ $property->District }}';
        const province = '{{ $property->Province }}';
        const propertyTitle = '{{ $property->Title }}';
        const propertyPrice = '{{ number_format($property->Price, 0, ",", ".") }}';

        // Default coordinates for major Vietnamese cities
        const locationCoords = getVietnamLocationCoords(district, province);

        // Initialize the map
        const map = L.map('propertyMap').setView([locationCoords.lat, locationCoords.lng], locationCoords.zoom);

        // Add OpenStreetMap tiles
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            maxZoom: 18
        }).addTo(map);

        // Create custom marker icon
        const customIcon = L.divIcon({
            className: 'custom-marker',
            html: `
                <div style="
                    background: linear-gradient(135deg, #ff6b6b, #ee5a52);
                    width: 40px;
                    height: 40px;
                    border-radius: 50% 50% 50% 0;
                    border: 3px solid white;
                    box-shadow: 0 4px 10px rgba(0,0,0,0.3);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    transform: rotate(-45deg);
                    position: relative;
                ">
                    <i class="bi bi-house-fill" style="
                        color: white;
                        font-size: 16px;
                        transform: rotate(45deg);
                    "></i>
                </div>
            `,
            iconSize: [40, 40],
            iconAnchor: [20, 35],
            popupAnchor: [0, -35]
        });

        // Add marker to map
        const marker = L.marker([locationCoords.lat, locationCoords.lng], {
            icon: customIcon
        }).addTo(map);

        // Add popup to marker
        marker.bindPopup(`
            <div style="padding: 10px; min-width: 200px;">
                <h6 style="margin: 0 0 8px 0; color: #2d3748; font-weight: 600; font-size: 14px;">
                    ${propertyTitle}
                </h6>
                <p style="margin: 0 0 5px 0; color: #4a5568; font-size: 12px;">
                    <i class="bi bi-geo-alt-fill" style="color: #ff6b6b;"></i>
                    ${district}, ${province}
                </p>
                <p style="margin: 0 0 8px 0; color: #38a169; font-weight: 600; font-size: 13px;">
                    ${propertyPrice} VND
                </p>
                <a href="https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(district + ', ' + province + ', Vietnam')}"
                   target="_blank"
                   style="
                       display: inline-block;
                       background: linear-gradient(135deg, #667eea, #764ba2);
                       color: white;
                       padding: 6px 12px;
                       border-radius: 6px;
                       text-decoration: none;
                       font-size: 11px;
                       font-weight: 600;
                   ">
                    <i class="bi bi-arrow-up-right-square me-1"></i>Xem trên Google Maps
                </a>
            </div>
        `).openPopup();

        // Add map controls
        const info = L.control({position: 'topright'});
        info.onAdd = function(map) {
            const div = L.DomUtil.create('div', 'map-info');
            div.innerHTML = `
                <div style="
                    background: rgba(255,255,255,0.95);
                    padding: 10px;
                    border-radius: 8px;
                    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                    font-size: 12px;
                    line-height: 1.4;
                ">
                    <strong style="color: #2d3748;">📍 Khu vực:</strong><br>
                    <span style="color: #4a5568;">${district}</span><br>
                    <span style="color: #4a5568;">${province}</span>
                </div>
            `;
            return div;
        };
        info.addTo(map);
    }

    // Get coordinates for Vietnamese locations
    function getVietnamLocationCoords(district, province) {
        // Coordinates for major Vietnamese cities and districts
        const locationMap = {
            // Ho Chi Minh City
            'TP. Hồ Chí Minh': { lat: 10.8231, lng: 106.6297, zoom: 11 },
            'Quận 1': { lat: 10.7769, lng: 106.7009, zoom: 14 },
            'Quận 2': { lat: 10.7947, lng: 106.7297, zoom: 13 },
            'Quận 3': { lat: 10.7865, lng: 106.6917, zoom: 14 },
            'Quận 4': { lat: 10.7574, lng: 106.7029, zoom: 14 },
            'Quận 5': { lat: 10.7594, lng: 106.6672, zoom: 14 },
            'Quận 6': { lat: 10.7387, lng: 106.6295, zoom: 14 },
            'Quận 7': { lat: 10.7379, lng: 106.7218, zoom: 13 },
            'Quận 8': { lat: 10.7387, lng: 106.6295, zoom: 14 },
            'Quận 9': { lat: 10.8411, lng: 106.8066, zoom: 13 },
            'Quận 10': { lat: 10.7759, lng: 106.6717, zoom: 14 },
            'Quận 11': { lat: 10.7649, lng: 106.6507, zoom: 14 },
            'Quận 12': { lat: 10.8537, lng: 106.6345, zoom: 13 },
            'Quận Gò Vấp': { lat: 10.8142, lng: 106.6438, zoom: 13 },
            'Quận Bình Thạnh': { lat: 10.8014, lng: 106.7109, zoom: 13 },
            'Quận Tân Bình': { lat: 10.8008, lng: 106.6527, zoom: 13 },
            'Quận Tân Phú': { lat: 10.7886, lng: 106.6253, zoom: 13 },
            'Quận Phú Nhuận': { lat: 10.7981, lng: 106.6831, zoom: 14 },
            'Quận Thủ Đức': { lat: 10.8537, lng: 106.7593, zoom: 12 },

            // Hanoi
            'Hà Nội': { lat: 21.0285, lng: 105.8542, zoom: 11 },
            'Quận Ba Đình': { lat: 21.0389, lng: 105.8372, zoom: 14 },
            'Quận Hoàn Kiếm': { lat: 21.0285, lng: 105.8542, zoom: 15 },
            'Quận Hai Bà Trưng': { lat: 21.0067, lng: 105.8611, zoom: 14 },
            'Quận Đống Đa': { lat: 21.0183, lng: 105.8342, zoom: 14 },
            'Quận Tây Hồ': { lat: 21.0583, lng: 105.8233, zoom: 13 },
            'Quận Cầu Giấy': { lat: 21.0333, lng: 105.7947, zoom: 13 },
            'Quận Thanh Xuân': { lat: 20.9972, lng: 105.8092, zoom: 13 },

            // Da Nang
            'Đà Nẵng': { lat: 16.0544, lng: 108.2022, zoom: 12 },
            'Quận Hải Châu': { lat: 16.0678, lng: 108.2208, zoom: 14 },
            'Quận Thanh Khê': { lat: 16.0678, lng: 108.1767, zoom: 14 },
            'Quận Sơn Trà': { lat: 16.0989, lng: 108.2525, zoom: 13 },
            'Quận Ngũ Hành Sơn': { lat: 15.9833, lng: 108.2525, zoom: 13 },

            // Other major cities
            'Cần Thơ': { lat: 10.0452, lng: 105.7469, zoom: 12 },
            'Nha Trang': { lat: 12.2388, lng: 109.1967, zoom: 12 },
            'Vũng Tàu': { lat: 10.4113, lng: 107.1366, zoom: 12 },
            'Hải Phòng': { lat: 20.8449, lng: 106.6881, zoom: 12 },
            'Huế': { lat: 16.4637, lng: 107.5909, zoom: 12 }
        };

        // Try to find exact match for district
        if (locationMap[district]) {
            return locationMap[district];
        }

        // Try to find match for province/city
        if (locationMap[province]) {
            return locationMap[province];
        }

        // Default to Ho Chi Minh City center if no match found
        return { lat: 10.8231, lng: 106.6297, zoom: 10 };
    }

    // Notification system
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: ${type === 'success' ? 'linear-gradient(135deg, #48bb78, #38a169)' : 'linear-gradient(135deg, #667eea, #764ba2)'};
            color: white;
            padding: 15px 20px;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            z-index: 10000;
            max-width: 300px;
            font-weight: 500;
            transform: translateX(100%);
            transition: transform 0.3s ease;
        `;

        notification.innerHTML = `
            <div style="display: flex; align-items: center;">
                <i class="bi bi-${type === 'success' ? 'check-circle-fill' : 'info-circle-fill'}" style="margin-right: 10px; font-size: 1.2rem;"></i>
                <span>${message}</span>
            </div>
        `;

        document.body.appendChild(notification);

        // Slide in
        setTimeout(() => {
            notification.style.transform = 'translateX(0)';
        }, 100);

        // Auto remove
        setTimeout(() => {
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        }, 4000);
    }
</script>

<!-- Image Gallery Modal -->
<div class="modal fade" id="imageGalleryModal" tabindex="-1" aria-labelledby="imageGalleryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageGalleryModalLabel">
                    <i class="bi bi-images"></i> Gallery Hình Ảnh
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="imageGalleryContent" class="property-gallery">
                    <!-- Images will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Video Gallery Modal -->
<div class="modal fade" id="videoGalleryModal" tabindex="-1" aria-labelledby="videoGalleryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="videoGalleryModalLabel">
                    <i class="bi bi-play-circle"></i> Gallery Video
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="videoGalleryContent" class="property-gallery">
                    <!-- Videos will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Property data for galleries
const propertyData = {
    '{{ $property->PropertyID }}': {
        images: [
            @foreach($property->images as $img)
            {
                url: '{{ \App\Helpers\ImageHelper::getImageUrl($img->ImagePath) }}',
                caption: '{{ $img->Caption ?? '' }}'
            },
            @endforeach
        ],
        videos: [
            @foreach($property->videos as $video)
            {
                path: '{{ $video->VideoPath }}',
                caption: '{{ $video->Caption ?? '' }}',
                isYoutube: {{ strpos($video->VideoPath, 'youtube.com') !== false || strpos($video->VideoPath, 'youtu.be') !== false ? 'true' : 'false' }}
            },
            @endforeach
        ]
    }
};

// Open Image Gallery
function openImageGallery(propertyId) {
    const property = propertyData[propertyId];
    if (!property || !property.images.length) return;

    const gallery = document.getElementById('imageGalleryContent');
    gallery.innerHTML = '';

    property.images.forEach((image, index) => {
        const item = document.createElement('div');
        item.className = 'gallery-item';
        item.innerHTML = `
            <img src="${image.url}" alt="Property Image ${index + 1}" class="gallery-image" onclick="openLightbox('${image.url}')">
            ${image.caption ? `<div class="gallery-item-caption">${image.caption}</div>` : ''}
        `;
        gallery.appendChild(item);
    });

    const modal = new bootstrap.Modal(document.getElementById('imageGalleryModal'));
    modal.show();
}

// Open Video Gallery
function openVideoGallery(propertyId) {
    const property = propertyData[propertyId];
    if (!property || !property.videos.length) return;

    const gallery = document.getElementById('videoGalleryContent');
    gallery.innerHTML = '';

    property.videos.forEach((video, index) => {
        const item = document.createElement('div');
        item.className = 'gallery-item';

        let videoContent = '';
        if (video.isYoutube) {
            const videoId = getYouTubeVideoId(video.path);
            videoContent = `<iframe src="https://www.youtube.com/embed/${videoId}" allowfullscreen></iframe>`;
        } else if (video.path.includes('tiktok.com')) {
            // TikTok video - show as link since embedding is complex
            videoContent = `<div class="tiktok-video-placeholder">
                <i class="bi bi-play-circle-fill" style="font-size: 3rem; color: #25f4ee;"></i>
                <p class="mt-2">Video TikTok</p>
                <a href="${video.path}" target="_blank" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-box-arrow-up-right"></i> Xem trên TikTok
                </a>
            </div>`;
        } else {
            // Other external video platforms or direct video URLs
            videoContent = `<div class="external-video-placeholder">
                <i class="bi bi-play-circle-fill" style="font-size: 3rem; color: #007bff;"></i>
                <p class="mt-2">Video bên ngoài</p>
                <a href="${video.path}" target="_blank" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-box-arrow-up-right"></i> Xem video
                </a>
            </div>`;
        }

        item.innerHTML = `
            <div class="gallery-video">${videoContent}</div>
            ${video.caption ? `<div class="gallery-item-caption">${video.caption}</div>` : ''}
        `;
        gallery.appendChild(item);
    });

    const modal = new bootstrap.Modal(document.getElementById('videoGalleryModal'));
    modal.show();
}

// Extract YouTube video ID
function getYouTubeVideoId(url) {
    const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|&v=)([^#&?]*).*/;
    const match = url.match(regExp);
    return (match && match[2].length === 11) ? match[2] : null;
}

// Lightbox functionality
function openLightbox(imageSrc) {
    const lightbox = document.getElementById('lightbox') || createLightbox();
    const img = lightbox.querySelector('img');
    img.src = imageSrc;
    lightbox.style.display = 'block';
}

function createLightbox() {
    const lightbox = document.createElement('div');
    lightbox.id = 'lightbox';
    lightbox.style.cssText = `
        display: none;
        position: fixed;
        z-index: 9999;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.9);
        cursor: pointer;
    `;

    lightbox.innerHTML = `
        <img style="
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            max-width: 90%;
            max-height: 90%;
            border-radius: 10px;
        ">
        <span style="
            position: absolute;
            top: 15px;
            right: 35px;
            color: #f1f1f1;
            font-size: 40px;
            font-weight: bold;
            cursor: pointer;
        ">&times;</span>
    `;

    lightbox.onclick = function() {
        lightbox.style.display = 'none';
    };

    document.body.appendChild(lightbox);
    return lightbox;
}
</script>

<!-- Leaflet JavaScript -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
        crossorigin=""></script>

@endsection
