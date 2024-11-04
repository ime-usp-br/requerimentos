@extends('layouts.app')

@section('head')
    <link rel="stylesheet" type="text/css" href="{{ asset('css/pages/student/newRequisition.css') }}">
    <script src="{{ asset('js/student/newRequisition.js')}}" defer></script>

    <!-- ionicons -->
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>    
    <title>Novo requerimento</title>
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
                <a href="{{ route('student.list') }}" class="button">Voltar para a página inicial</a>
                <a href="{{ route('student.newRequisition') }}" class="button">Criar outro requerimento</a>
            </div>
        @elseif($errors->any())
            <style>
                .overlay-container {
                    display: block;
                }
            </style>
            <p class="overlay-error-message">Os erros podem ter sido causados por problemas nos arquivos selecionados ou por inconsistência nos dados inseridos.</p>
        @endif
    </x-overlay>

    <div class="content">
        <header>
            <h1>Novo requerimento</h1>
            <a href="{{ route('student.list')}}" class="button">Voltar</a>
        </header>
        <p class="instruction">Preencha o seguinte formulário para criar o requerimento: <br> (Crie um formulário para cada matéria a ser dispensada) </p>

        <form method="POST" action="{{ route('student.create')}}" id="form" enctype="multipart/form-data">
            @csrf

            <x-form.course :readOnly="False"/>

            <hr>

            <x-form.disciplines.write />

            <hr>

            <x-form.documents.write />
            
            <hr>

            <x-form.observations :readOnly="False" />

        </form>

        <div class="bottom-nav"> 
            <a href="{{ route('student.list') }}" class="button">Voltar</a>
            <button type="submit" form="form" class="button">Encaminhar para análise</button>
        </div>
        
    </div>
@endsection