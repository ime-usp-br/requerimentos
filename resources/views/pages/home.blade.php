@extends('layouts.app')

@section('head')
    <title>Requerimentos</title>
    <link rel="stylesheet" type="text/css" href="{{ asset('css/pages/home.css') }}">
@endsection

@section('content')
    <header>
        <img src="{{ asset('img/home/ime-logo-title.svg')}}">
        <a href="login" class="button"><span class="material-symbols-outlined icon">login</span>Acessar</a>
    </header>
@endsection