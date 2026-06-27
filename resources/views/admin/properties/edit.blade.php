@extends('layouts.admin')
@section('title','Edit Data Kos')
@section('subtitle',$property->name)
@section('content')
@include('admin.properties._form', ['property' => $property])
@endsection
