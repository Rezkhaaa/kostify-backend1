@extends('layouts.admin')
@section('title','Tambah Admin Kos')
@section('subtitle','Buat akun login web untuk pemilik/pengelola kos')
@section('content')
@include('admin.property-admins._form', ['adminUser' => null, 'properties' => $properties])
@endsection
