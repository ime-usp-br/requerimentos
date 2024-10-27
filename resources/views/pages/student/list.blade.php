@extends('layouts.app')

@section('head')
    <!-- css e javascript usados no datatables (biblioteca da tabela) -->
    <link href="https://cdn.datatables.net/v/dt/jq-3.7.0/jszip-3.10.1/dt-1.13.8/b-2.4.2/b-html5-2.4.2/r-2.5.0/datatables.min.css" rel="stylesheet">
    <script src="https://cdn.datatables.net/v/dt/jq-3.7.0/jszip-3.10.1/dt-1.13.8/b-2.4.2/b-html5-2.4.2/r-2.5.0/datatables.min.js" defer></script>


    <!-- bibliotecas usadas para ordenar as linhas da tabela por data-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.4/moment.min.js" defer></script>
    <script src="https://cdn.datatables.net/plug-ins/1.13.7/sorting/datetime-moment.js" defer></script>

    <!-- nosso javascript -->
    <script src="{{ asset('js/student/list.js')}}" defer></script>

    <!-- nosso css -->
    <link rel="stylesheet" type="text/css" href="{{ asset('css/pages/student/list.css') }}"> 

    <title>Lista de requerimentos</title>
@endsection

@section('content')
    <header>
        <h1>Requerimentos</h1>
        <nav>
            <a href="{{ route('student.newRequisition') }}" class="button">Criar requerimento</a>
            <form action="{{ '/' . $logout_url }}" method="POST" id="form">
                @csrf
            </form>
            <button type="submit" form="form" class="button">Sair</button>
        </nav>
    </header>
    <div class="content">
        <x-table :columns="['Data de criação', 'Disciplinas cursadas', 'Disciplina requerida', 'Situação', 'Id']">
            @foreach ($reqs as $req)
                <tr>
                    <td>{{ \Illuminate\Support\Carbon::parse($req->created_at->toDateString())->format('d/m/Y') }}</td>
                    <td>
                        <ul>
                            @foreach ($req->takenDisciplines as $disc)
                                <li>{{ $disc->name }} </li>
                            @endforeach
                        </ul>    
                    </td>
                    <td>{{ $req->requested_disc }}</td>
                    <td>{{ $req->situation }}</td>
                    <td>{{ $req->id }}</td>
                </tr>
            @endforeach
        </x-table>
    </div>
@endsection