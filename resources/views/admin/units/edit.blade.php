@extends('layouts.admin')
@section('title','Edit Kamar')
@section('subtitle',$unit->name)
@section('content')
@include('admin.units._form', ['unit' => $unit])
@endsection
