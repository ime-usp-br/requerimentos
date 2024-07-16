
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
    <script src="{{ asset('js/geral/previousReviews.js')}}" defer></script>
    
    <!-- nosso css -->
    <link rel="stylesheet" type="text/css" href="{{ asset('css/pages/geral/previousReviews.css') }}">     
@endsection

@section('content')
    <header>
        <h1>Histórico da disciplina</h1>
        <nav>
            <a href="../../" class="button">Voltar</a>
        </nav>
    </header>
    
    <div class="content">
        <x-table :columns="['Cursada', 'Data cursada', 'Parecer', 'Data parecer', 'Justificativa', 'Copiar parecer', 'ID']">
            @foreach ($previousRequisitions as $req_groups)
                <tr>
                    <td class="{{Str::camel(Str::ascii($req_groups[0]->reviewer_decision)) . "Other"}}">
                        <ul>
                            @foreach ($req_groups as $req)
                            <li>{{$req->taken_codes}} </li>
                            @endforeach
                        </ul>    
                    </td>
                    <td class="{{Str::camel(Str::ascii($req_groups[0]->reviewer_decision)) . "Other"}}">
                        <ul>
                            @foreach ($req_groups as $req)
                            <li>{{$req->year_taken}}.{{ ($req->semester_taken == "Primeiro") ? "1" : "2"}}</li>
                            @endforeach
                        </ul>  
                    </td>
                    <td class="{{Str::camel(Str::ascii($req_groups[0]->reviewer_decision)) . "Main"}}">
                        {{ $req_groups[0]->reviewer_decision}}
                    </td>
                    <td class="{{Str::camel(Str::ascii($req_groups[0]->reviewer_decision)) . "Other"}}">
                        {{ $req_groups[0]->review_date}}
                    </td>
                    <td class="{{Str::camel(Str::ascii($req_groups[0]->reviewer_decision)) . "Other"}}">
                        {{ $req_groups[0]->reviewer_justification}}
                    </td>
                    <td class="{{Str::camel(Str::ascii($req_groups[0]->reviewer_decision)) . "Other"}}">
                        <form method="POST" action="{{ route('reviewer.copy', ['requisitionId' => $requisitionId])}}" >
                            @csrf
                            <input type="hidden" name="decision" value="{{$req_groups[0]->reviewer_decision}}">
                            <input type="hidden" name="justification" value="{{$req_groups[0]->reviewer_justification}}">
                            <button type="submit" class="button">Copiar parecer</button>
                        </form>
                    </td>
                    <td>
                        {{ $req_groups[0]->id }}
                    </td>
                </tr>
            @endforeach
        </x-table>
    </div>
@endsection