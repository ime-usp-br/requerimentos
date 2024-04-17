@extends('layouts.app')

@section('head')
    <!-- não sei o que é isso, algum ícone talvez -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" /> 

    <!-- css e javascript usados no datatables (biblioteca da tabela) -->
    <link href="https://cdn.datatables.net/v/dt/jq-3.7.0/jszip-3.10.1/dt-1.13.8/b-2.4.2/b-html5-2.4.2/r-2.5.0/datatables.min.css" rel="stylesheet">
    <script src="https://cdn.datatables.net/v/dt/jq-3.7.0/jszip-3.10.1/dt-1.13.8/b-2.4.2/b-html5-2.4.2/r-2.5.0/datatables.min.js" defer></script>


    <!-- bibliotecas usadas para ordenar as linhas da tabela por data-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.4/moment.min.js" defer></script>
    <script src="https://cdn.datatables.net/plug-ins/1.13.7/sorting/datetime-moment.js" defer></script>

    <script src="{{ asset('js/reviewer/list.js')}}" defer></script>
    <link rel="stylesheet" type="text/css" href="{{ asset('css/pages/reviewer/list.css') }}">
@endsection

@section('content')
    <header>
        <h1>Requerimentos</h1>
        <div class="header-buttons">
            <nav>
                <a href="/" class="button">Sair</a>
            </nav>
            @if (Auth::user()->roles()->count() > 1)
                <form action="{{ route('role.switch') }}" method="POST" class="role-switch">
                    @csrf
                    <label class="role">
                        Papel
                        <select name="roleSwitch">
                            @foreach (Auth::user()->roles as $role)
                                <option value="{{ $role->id }}" 
                                    @if($role->id === Auth::user()->current_role_id) 
                                        selected 
                                    @endif
                                >{{ $role->name }}</option>  
                            @endforeach
                        </select>
                    </label>
                </form>                
            @endif
        </div>
    </header>
    
    <div class="content"> 
        <x-table :columns="['Data de criação', 'Aluno', 'Número USP', 'Disciplina Requirida', 'id']">
            @foreach ($reqs as $req)
                <tr>
                    <td>{{ \Illuminate\Support\Carbon::parse($req->created_at->toDateString())->format('d/m/Y') }}</td>
                    <td>{{ $req->student_name }}</td>
                    <td>{{ $req->nusp }}</td>
                    <td>{{ $req->requested_disc}}</td>
                    <td>{{ $req->id }}</td>
                    <td></td> 
                </tr>
            @endforeach
        </x-table>
    </div>
@endsection

<!--  -->