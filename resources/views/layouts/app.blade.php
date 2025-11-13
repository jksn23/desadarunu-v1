@php
    $role = auth()->user()?->role;
@endphp

@if (in_array($role, ['admin_desa', 'admin_web']))
    @include('layouts.admin')
@else
    @include('layouts.operator')
@endif
