@extends('layouts.student')
@section('title', 'Error')
@section('content')
<div class="text-center py-5">
    <h2>Profile Not Linked</h2>
    <p>{{ $message }}</p>
    <p>Please contact the administrator to link your account to a student profile.</p>
</div>
@endsection
