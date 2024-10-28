@extends('layouts.app')

@section('head')
    <!-- ion-icons -->
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

    <!-- css e javascript usados no datatables (biblioteca da tabela) -->
    <link href="https://cdn.datatables.net/v/dt/jq-3.7.0/jszip-3.10.1/dt-1.13.8/b-2.4.2/b-html5-2.4.2/r-2.5.0/datatables.min.css" rel="stylesheet">
    <script src="https://cdn.datatables.net/v/dt/jq-3.7.0/jszip-3.10.1/dt-1.13.8/b-2.4.2/b-html5-2.4.2/r-2.5.0/datatables.min.js" defer></script>


    <!-- bibliotecas usadas para ordenar as linhas da tabela por data-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.4/moment.min.js" defer></script>
    <script src="https://cdn.datatables.net/plug-ins/1.13.7/sorting/datetime-moment.js" defer></script>

    <!-- nosso javascript -->
    <script src="{{ asset('js/sg/reviewerPick.js')}}" defer></script>
    <!-- <link rel="stylesheet" type="text/css" href="{{ asset('css/users.css') }}"> -->
    <link rel="stylesheet" type="text/css" href="{{ asset('css/pages/sg/reviewerPick.css') }}">  
    
    <title>Escolha do parecerista</title>
@endsection

@section('content')
    <header>
        <h1>Envio do requerimento</h1>
        <nav>
            <a href="{{ route('sg.show', ['requisitionId' => $requisitionId]) }}" class="button">Voltar</a>
        </nav>
    </header>

    <p>Escolha abaixo os pareceristas para os quais o requerimento será enviado</p>

    <div class="content">
        <x-table :columns="['Nome', 'Número USP', '', 'Id']">
            @foreach ($reviewers as $reviewer)
                <tr>
                    <td>{{ $reviewer->name ?? 'Desconhecido (usuário nunca logou no site)' }}</td>
                    <td>{{ $reviewer->codpes }}</td>
                    <td>
                        <form action="{{ route('reviewer.sendToReviewer', ['requisitionId' => $requisitionId ]) }}" method="POST" class="button-form">
                            @csrf
                            <input type="hidden" name="codpes" value="{{ $reviewer->codpes }}">
                            <input type="hidden" name="name" value="{{ $reviewer->name }}">
                            <button class="button" type="button">Enviar</button>
                        </form>
                    </td>
                    <td>{{ $reviewer->id }}</td>
                </tr>                
            @endforeach
        </x-table>
    </div>
@endsection