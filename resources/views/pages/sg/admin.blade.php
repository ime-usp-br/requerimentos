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
    <script src="{{ asset('js/sg/admin.js')}}" defer></script>
    <link rel="stylesheet" type="text/css" href="{{ asset('css/pages/sg/admin.css') }}">  
    <title>Administração do Sistema</title>    
@endsection

@section('content')
    <header>
        <h1>Administração do sistema</h1>
        <nav>
            <a href="{{ route('sg.list') }}" class="button">Voltar</a>
        </nav>
    </header>
    
    {{-- Overlay da adição de usuários --}}
    <div class="overlay-container">
        <div class="overlay-content">
            <div class="overlay-header">
                <div class="overlay-title">Adicione um papel</div>
                <img class="close-button" src="{{ asset('img/overlay/close-button.svg') }}" alt="Close button" >
            </div>
            
            <form method="POST" action="{{ route('role.add')}}" class="overlay-form" autocomplete="off">
                @csrf
                <label class="nusp">
                    Número USP do usuário
                    <input type="text" name="nusp">
                </label>

                <div class="type-title">Tipo de papel</div>
                <div class="type-radio" >

                    <label class="radio-button">
                        <input type="radio" id="reviewer" name="role" value="Parecerista"/>
                        <span class="label-visible">
                            <span class="fake-radiobutton"></span>
                            Parecerista
                        </span>
                    </label>
                    
                    <label class="radio-button">
                        <input type="radio" id="grad-secretary" name="role" value="Serviço de Graduação" />
                        <span class="label-visible">
                            <span class="fake-radiobutton"></span>
                            Serviço de Graduação
                        </span>
                    </label>

                    <label class="radio-button" >
                        <input type="radio" id="coordination" name="role" value="Department">
                        <span class="label-visible">
                            <span class="fake-radiobutton"></span>
                            Secretaria de Departamento
                        </span>
                    </label>
                </div>

                <div class="department-title">Departamento</div>
                <div class="department-radio">
                    <div class="type-radio" >
                        <label class="radio-button">
                            <input type="radio" id="MAC" name="department" value="MAC" checked/>
                            <span class="label-visible">
                                <span class="fake-radiobutton"></span>
                                MAC
                            </span>
                        </label>

                        <label class="radio-button">
                            <input type="radio" id="MAP" name="department" value="MAP"/>
                            <span class="label-visible">
                                <span class="fake-radiobutton"></span>
                                MAP
                            </span>
                        </label>

                        <label class="radio-button" >
                            <input type="radio" id="MAT" name="department" value="MAT">
                            <span class="label-visible">
                                <span class="fake-radiobutton"></span>
                                MAT
                            </span>
                        </label>

                        <label class="radio-button">
                            <input type="radio" id="MAE" name="department" value="MAE" />
                            <span class="label-visible">
                                <span class="fake-radiobutton"></span>
                                MAE
                            </span>
                        </label>

                        <label class="radio-button">
                            <input type="radio" id="VRT" name="department" value="VRT" />
                            <span class="label-visible">
                                <span class="fake-radiobutton"></span>
                                Virtual
                            </span>
                        </label>
                    </div>
                </div>
                <button type="submit" class="button">Adicionar</button>
            </form>
        </div>
    </div>


    <div class="content">
        @if(session()->has("success"))
            <p id="requisition-period-alert-message"> O período de requerimentos foi {{ session('success.info') ? "aberto" : "fechado"  }}.</p>
        @endif
        <div class="actions">
            <form action="{{ route('sg.requisition_period_toggle') }}" method="POST">
                @csrf
                @if($requisition_period_status)
                    <button type="submit" class="button" style="background-color:#8f3636">  
                        Desativar período de requerimentos
                    </button>
                @else
                    <button type="submit" class="button">  
                        Ativar período de requerimentos 
                    </button>
                @endif
            </form>
            <a class="button" id="addRoleButton">Adicionar um papel</a>
        </div>
        <div class="users-table">
            <h2> Usuários cadastrados </h2> 
            <x-table :columns="['Nome', 'Número USP', 'Papel', '', 'Id']">
                @foreach ($users as $user)
                    @foreach ($user->roles as $role)
                        <tr>
                            <td>{{ $user->name ?? 'Desconhecido (usuário nunca logou no site)' }}</td>
                            <td>{{ $user->codpes }}</td>
                            <td>{{ $role->name }}</td>
                            <td>
                                <form action="{{ route('role.remove') }}" method="POST" class="button-form">
                                    @csrf
                                    <input type="hidden" name="nusp" value="{{ $user->codpes }}">
                                    <input type="hidden" name="role" value="{{ $role->name }}">
                                    <button class="button" type="button" >Remover papel</button>
                                </form>
                            </td>
                            <td>{{ $user->id }}</td>
                        </tr>                
                    @endforeach
                @endforeach
            </x-table>
        </div>
    </div>
@endsection