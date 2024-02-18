@extends('layouts.app')

@section('head')
    <link rel="stylesheet" type="text/css" href="{{ asset('css/pages/newRequisition.css') }}">
    <script src="{{ asset('js/newRequisition.js')}}" defer></script>

    <!-- ionicons -->
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>    
@endsection

@section('content')
    <x-form-overlay>
        <a href="{{ route('list') }}" class="button">Voltar para a página inicial</a>
        <a href="{{ route('newRequisition') }}" class="button">Criar outro requerimento</a>
    </x-form-overlay>

    <div class="content">
        <header>
            <h1>Novo requerimento</h1>
            <a href="{{ route('list')}}" class="button">Voltar</a>
        </header>
        <p class="instruction">Preencha o seguinte formulário para criar o requerimento</p>

        <form method="POST" action="{{ route('requisitions.create')}}" id="form" enctype="multipart/form-data">
            @csrf

            {{--<x-form.personal :withRecordButton="false"/> <hr> --}}
            
            <x-form.course />

            <hr>

            <x-form.disciplines.write />

            <hr>

            <x-form.documents.write />
            
            <hr>

            <x-form.observations />

        </form>

        <div class="bottom-nav"> 
            <a href="{{ route('list') }}" class="button">Voltar</a>
            <button type="submit" form="form" class="button">Encaminhar para análise</button>
        </div>
        
    </div>
@endsection