@extends('layouts.app')

@section('head')
    <!-- css e javascript usados no datatables (biblioteca da tabela) -->
    <link href="https://cdn.datatables.net/v/dt/jq-3.7.0/jszip-3.10.1/dt-1.13.8/b-2.4.2/b-html5-2.4.2/r-2.5.0/datatables.min.css" rel="stylesheet">
    <script src="https://cdn.datatables.net/v/dt/jq-3.7.0/jszip-3.10.1/dt-1.13.8/b-2.4.2/b-html5-2.4.2/r-2.5.0/datatables.min.js" defer></script>


    <!-- bibliotecas usadas para ordenar as linhas da tabela por data-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.4/moment.min.js" defer></script>
    <script src="https://cdn.datatables.net/plug-ins/1.13.7/sorting/datetime-moment.js" defer></script>

    <!-- nosso javascript -->
    <script src="{{ asset('js/records/list.js')}}" defer></script>

    <!-- nosso css -->
    <link rel="stylesheet" type="text/css" href="{{ asset('css/pages/student/list.css') }}"> 

    <title>Histórico do requerimento</title>
@endsection

@section('content')
    <header>
        <h1>Histórico do requerimento</h1>
        <nav>
            <a href="{{ $previousRoute }}" class="button">Voltar</a>
        </nav>
    </header>
    <div class="content">
        <x-table :columns="['Evento', 'Data de ocorrência', 'Horário de ocorrência', 'Usuário responsável pelo evento', 'Número USP do usuário responsável', 'Id']">
            @foreach ($events as $event)
                <tr>
                    {{--<td>{{ \Illuminate\Support\Carbon::parse($event->created_at->toDateString())->format('d/m/Y') }}</td>--}}
                    <td>{{ $event->type }}</td>
                    <td>{{ $event->created_at->format('d/m/Y') }}</td>
                    <td> {{ $event->created_at->format('H:i')}} </td>
                    <td>{{ $event->author_name }}</td>
                    <td>{{ $event->author_nusp }}</td>
                    <td>{{ $event->id }}</td>
                </tr>
            @endforeach
        </x-table>
    </div>
@endsection