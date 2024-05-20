@extends('layouts.app')

@section('head')
    
    <link rel="stylesheet" type="text/css" href="{{ asset('css/pages/reviewer/detail.css') }}">
    <script src="{{ asset('js/reviewer/detail.js')}}" defer></script>

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
                <a href="{{ route('reviewer.list') }}" class="button">Voltar para a página inicial</a>
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
        <h1>Detalhes do requerimento {{ $req->id }} </h1>
        <nav class="nav">
            <!-- <div class="status">
                <span>Situação</span>
                <span><ion-icon name="ellipse-outline"></ion-icon>Aguardando parecer</span>
            </div> 
            <a href="{{ route('sg.reviews', ['requisitionId' => $req->id ]) }}" class="button">Pareceres</a>
            -->
            <a href="#" class="button" >Histórico do requerimento</a>
            <a href="{{ route('reviewer.list') }}" class="button">Voltar</a>
        </nav>

        <!--===================== ARRUMAR AQUI ================================= -->
        <form method="POST" action="{{ route('reviewer.update', ['requisitionId' => $req->id])}}" id="form" >
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

            <x-form.review :req="$reqs" />

            <hr>

            {{--<x-form.result :req="$reqs" />--}}
            
            <!-- <input type="hidden" name="req-id" value="{{ $req->id }}"> -->
            <input type="hidden" name="button" id="btnType">
        </form>

        <div class="nav"> 
            <a href="{{ route('reviewer.list') }}" class="button">Voltar</a>
           <!-- <button type="submit" form="form" class="button" id="save-btn">Salvar mudanças</button> -->
            <button type="submit" form="form" class="button" id="send-btn">Encaminhar para um Secretaria</button>
        </div>
        
    </div>
@endsection