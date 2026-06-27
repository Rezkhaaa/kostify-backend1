@extends('layouts.admin')
@section('title','Edit Admin Kos')
@section('subtitle',$adminUser->name)
@section('content')
@include('admin.property-admins._form', ['adminUser' => $adminUser, 'properties' => $properties])
@endsection
