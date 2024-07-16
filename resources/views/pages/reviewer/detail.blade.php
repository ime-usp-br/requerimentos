@extends('layouts.app')

@section('head')
    
    <link rel="stylesheet" type="text/css" href="{{ asset('css/pages/reviewer/detail.css') }}">
    <script src="{{ asset('js/reviewer/detail.js')}}" defer></script>

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
            
            {{-- "{!! !!}" para rederizar as quebras de linha --}}
            <p> {!! session('success')['body message'] !!} </p> 
            @if (session('success')['return button'])
                <div class="overlay-nav">
                    <a href="{{ route('reviewer.list') }}" class="button">Voltar para a página inicial</a>
                </div>
            @endif
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
        <h1>Detalhes do requerimento {{ $req->id }} </h1>
        <nav class="nav">
            <a href="{{ route('reviewer.reviews', ['requisitionId' => $req->id ]) }}" class="button">Pareceres</a>
            <a href="{{ route('record.requisition', ['requisitionId' => $req->id]) }}" class="button">Histórico do requerimento</a>
            <a href="{{ route('reviewer.list') }}" class="button">Voltar</a>
        </nav>

        <form method="POST" action="{{ route('reviewer.saveOrSubmit', ['requisitionId' => $req->id])}}" id="form" >
            @csrf

            <x-form.personal :withRecordButton="true" :req="$req"/>

            <hr>

            <x-form.course :req="$req"/>

            <hr>

            <x-form.disciplines.read :takenDiscs="$takenDiscs" :req="$req" :withRecordButton="true" />
            
            <hr>

            <x-form.documents.read :takenDiscsRecords="$takenDiscsRecords" :currentCourseRecords="$currentCourseRecords" :takenDiscSyllabi="$takenDiscSyllabi" :requestedDiscSyllabi="$requestedDiscSyllabi"/>

            <hr>

            <x-form.observations :req="$req" />

            <hr>

            <x-form.review :req="$req" :review="$review" />

        </form>

        <div class="nav"> 
            <a href="{{ route('reviewer.list') }}" class="button">Voltar</a>
            <button type="submit" form="form" class="button" name="action" value="save">Salvar</button>
            <button type="submit" form="form" class="button" name="action" value="submit">Enviar parecer</button>
        </div>
        
    </div>
@endsection