@extends('layouts.app')

@section('head')
    <link rel="stylesheet" type="text/css" href="{{ asset('css/pages/student/editRequisition.css') }}">
    <script src="{{ asset('js/editRequisition.js')}}" defer></script>

    <!-- ionicons -->
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>    
@endsection

@section('content')
    <x-overlay>
        <x-slot name="title">
            @if (session()->has('success'))
                {{ session('success')['title message'] }}
            @elseif($errors->any())
                Erros no prenchimento dos dados
            @endif
        </x-slot>

        @if (session()->has('success'))
            <style>
                .overlay-container {
                    display: block;
                }
            </style>
            <p>{{ session('success')['body message'] }}</p>
            <div class="overlay-nav">
                <a href="{{ route('list') }}" class="button">Voltar para a página inicial</a>
            </div>
        @elseif($errors->any())
            <style>
                .overlay-container {
                    display: block;
                }
            </style>
            <p class="overlay-error-message">Os erros podem ter sido causados por campos obrigatórios não preenchidos ou por inconsistência nos dados inseridos.</p>
        @endif
    </x-overlay>

    <div class="content">
        <header>
            <h1>Edição do requerimento</h1>
            <!-- <a href="{{ route('list')}}" class="button">Voltar</a> -->
        </header>
        <p class="instruction">Atualize as informações do requerimento</p>
        
        <form method="POST" action="{{ route('requisitions.update', ['requisitionId' => $req->id])}}" id="form" enctype="multipart/form-data">
            @csrf

            <x-form.course :req="$req"/>

            <hr>

            <x-form.disciplines.read :takenDiscs="$takenDiscs" :req="$req" :withRecordButton="false" />

            <hr>

            <x-form.documents.write />
            
            <hr>

            <x-form.observations :req="$req"/>
            
            <input type="hidden" name="button" id="btnType">
        </form>

        <div class="bottom-nav"> 
            <a href="{{ route('list') }}" class="button">Voltar</a>
            
            <button type="submit" form="form" class="button">Salvar mudanças</button>
        </div>
        
    </div>
@endsection