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

    <script src="{{ asset('js/sg/list.js')}}" defer></script>
    <link rel="stylesheet" type="text/css" href="{{ asset('css/pages/sg/list.css') }}">
    <title>Lista de requerimentos</title>
@endsection

@section('content')
    <header>
        <h1>Requerimentos</h1>
            @if (Auth::user()->roles()->count() > 1)
                <form action="{{ route('role.switch') }}" method="POST" class="role-switch">
                    @csrf
                    <label class="role">
                        Papel
                        <select name="role-switch">
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
            <nav>
                <a href="{{ route('sg.admin') }}" class="button">Administrar sistema</a>
                <a href="{{ route('sg.newRequisition') }}" class="button">Criar requerimento</a>

                <a href="{{ route('pages.requisitions.filters') }}" class="button">Filtros</a>
                <form action="{{ '/' . $logout_url }}" method="POST" id="form">
                    @csrf
                </form>
                <button type="submit" form="form" class="button">Sair</button>
                
            </nav>
    </header>
    
    <div class="content">
        <x-table :columns="['Data de criação', 'ID', 'Aluno', 'Número USP', 'Situação', 'Departamento']">
            @foreach ($reqs as $req)
                <tr>
                    <td>{{ \Illuminate\Support\Carbon::parse($req->created_at)->format('d/m/Y H:i:s') }}</td>
                    <td>{{ $req->id }}</td>
                    <td>{{ $req->student_name }}</td>
                    <td>{{ $req->student_nusp }}</td>
                    <td>{{ $req->internal_status }}</td>
                    <td>{{ $req->department }}</td>
                </tr>
            @endforeach
        </x-table>
    </div>
@endsection

<!--  -->
