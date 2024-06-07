@extends('layouts.app')

@section('head')
    
    <link rel="stylesheet" type="text/css" href="{{ asset('css/pages/sg/detail.css') }}">
    <script src="{{ asset('js/sg/detail.js')}}" defer></script>

    <!-- ionicons -->
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <title>Detalhes do requerimento</title>
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
            <h1>Detalhes do requerimento {{ $req->id }} </h1>
            <select class="mode-select">
                <option value="readonly">Modo de exibição</option>
                <option value="edit">Modo de edição</option>
            </select>
        </header>
        
        <nav class="nav">
            <a href="{{ route('reviewer.reviews', ['requisitionId' => $req->id ]) }}" class="button">Pareceres</a>
            <a href="{{ route('record.requisition', ['requisitionId' => $req->id]) }}" class="button" >Histórico do requerimento</a>
            <a href="{{ route('sg.list') }}" class="button">Voltar</a>
        </nav>

        <form method="POST" action="{{ route('sg.update', ['requisitionId' => $req->id])}}" id="form" >
            @csrf

            <x-form.personal :withRecordButton="true" :req="$req"/>

            <hr>

            <x-form.course :req="$req" :readOnly="False"/>

            <hr>

            <x-form.disciplines.read :takenDiscs="$takenDiscs" :req="$req" :withRecordButton="true" :readOnly="False" />
            
            <hr>

            <x-form.documents.read :takenDiscsRecords="$takenDiscsRecords" :currentCourseRecords="$currentCourseRecords" :takenDiscSyllabi="$takenDiscSyllabi" :requestedDiscSyllabi="$requestedDiscSyllabi"/>

            <hr>

            <x-form.observations :req="$req" />

            <hr>

            <x-form.result :req="$req" />
            
            <input type="hidden" name="button" id="btnType">
        </form>

        <div class="nav"> 
            <a href="{{ route('sg.list') }}" class="button">Voltar</a>
            <button type="submit" form="form" class="button" id="save-btn">Salvar alterações</button>
            <button type="submit" form="form" class="button" id="department-btn">Enviar para o departamento</button>
            <button type="submit" form="form" class="button" id="reviewer-btn">Enviar para um parecerista</button>
        </div>
        
    </div>
@endsection