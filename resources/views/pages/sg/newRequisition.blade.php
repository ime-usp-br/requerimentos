@extends('layouts.app')

@section('head')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" type="text/css" href="{{ asset('css/pages/sg/new-request.css') }}">
    <script src="{{ asset('js/sg/newRequest.js')}}" defer></script>

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
                <a href="{{ route('sg.list') }}" class="button">Voltar para a página inicial</a>
                <a href="{{ route('sg.newRequisition') }}" class="button">Criar outro requerimento</a>
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
            <h1>Novo requerimento</h1>
            <a href="{{ route('sg.list') }}" class="button">Voltar</a>
        </header>
        <p class="instruction">Preencha o seguinte formulário para criar o requerimento</p>

        <form method="POST" action="{{ route('sg.create')}}" id="form" enctype="multipart/form-data">
            @csrf

            <x-form.personal :withRecordButton="false"/> 

            <hr>

            <x-form.course />

            <hr>

            <x-form.disciplines.write />

            <hr>

            <x-form.documents.write />
            
            <hr>

            <x-form.observations />
            
            <!-- <input type="hidden" name="takenDiscCount" id="taken-disc-count"> -->
        </form>

        <div class="bottom-nav"> 
            <a href="{{ route('sg.list') }}" class="button">Voltar</a>
            <button type="submit" form="form" class="button">Encaminhar para análise</button>
        </div>
        
    </div>
@endsection