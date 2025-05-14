@extends('_layout._layadmin.app')

@section('dashboard')

<div class="dashboard-container">
    <h1>
        Chào mừng <span class="highlight">{{ Auth::user()->name }}</span> tới hệ thống quản lý bất động sản ABC
    </h1>
    <p class="version-text">version 0.3</p>
</div>

@endsection
