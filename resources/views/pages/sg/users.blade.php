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
    <script src="{{ asset('js/sg/users.js')}}" defer></script>
    <!-- <link rel="stylesheet" type="text/css" href="{{ asset('css/users.css') }}"> -->
    <link rel="stylesheet" type="text/css" href="{{ asset('css/pages/sg/users.css') }}">     
@endsection

@section('content')
    <header>
        <h1>Administração de usuários</h1>
        <nav>
            <button class="button">Adicionar usuário especial</button>
            <a href="{{ route('sg.list') }}" class="button">Voltar</a>
        </nav>
    </header>
    
    <div class="overlay-container">
        <div class="overlay-content">
            <div class="overlay-header">
                <div class="overlay-title">Adicione um usuário especial</div>
                <img class="close-button" src="{{ asset('img/overlay/close-button.svg') }}" alt="Close button" >
            </div>
            
            <form method="POST" action="{{ route('role.create')}}" class="overlay-form" autocomplete="off">
                @csrf
                <label class="nusp">
                    Número USP do usuário
                    <input type="text" name="nusp">
                </label>

                <div class="type-title">Tipo de usuário</div>
                <div class="type-radio" >

                    <label class="radio-button">
                        <input type="radio" id="reviewer" name="type" value="reviewer"/>
                        <span class="label-visible">
                            <span class="fake-radiobutton"></span>
                            Parecerista
                        </span>
                    </label>
                    
                    <label class="radio-button">
                        <input type="radio" id="grad-secretary" name="type" value="grad-secretary" />
                        <span class="label-visible">
                            <span class="fake-radiobutton"></span>
                            Secretaria de Graduação
                        </span>
                    </label>

                    <label class="radio-button" >
                        <input type="radio" id="coordination" name="type" value="coordination">
                        <span class="label-visible">
                            <span class="fake-radiobutton"></span>
                            Coordenação
                        </span>
                    </label>

                    <label class="radio-button">
                        <input type="radio" id="dept-secretary" name="type" value="dept-secretary" />
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
                            <input type="radio" id="MAC" name="department" value="MAC"/>
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
                    </div>
                </div>
                <button type="submit" class="button">Adicionar</button>
            </form>
        </div>
    </div>

    <div class="content">
        <table id="table" class="hover cell-border stripe" >
            <thead>
                <tr>
                <th>Nome</th>
                <th>Número USP</th>
                <th>Tipo</th>
                <th></th>
                <th>Id</th>
                </tr>
            </thead>
            <tbody>
                <tr id="row-1">
                    <td>Roberto Oliveira Bolgheroni</td>
                    <td>11796430</td>
                    <td>Coordenação</td>
                    <td><button class="button row-1">Remover permissão</button></td>
                    <td>12</td>
                </tr>
                <tr>
                    <td>Ana Yoon Faria de Lima</td>
                    <td>11795273</td>
                    <td>Secretaria de Graduação</td>
                    <td><button class="button">Remover permissão</button></td>
                    <td>8</td>
                </tr>
                <tr>
                    <td>Barbara Monteiro dos Santos Rosa</td>
                    <td>74157318</td>
                    <td>Secretaria de Departamento</td>
                    <td><button class="button">Remover permissão</button></td>
                    <td>19</td>
                </tr>
                <tr>
                    <td>Andreia Ribeiro Alves Motta</td>
                    <td>59348713</td>
                    <td>Parecerista</td>
                    <td><button class="button">Remover permissão</button></td>
                    <td>35</td>
                </tr>
                <tr>
                <td>Lua Nowacki Scavacini Santilli</td>
                <td>11795492</td>
                <td>Coordenação</td>
                <td><button class="button">Remover permissão</button></td>
                <td>59</td>
                </tr>
                <tr>
                <td>Debora Dangelo Reina de Araujo </td>
                <td>11221668</td>
                <td>Parecerista</td>
                <td><button class="button">Remover permissão</button></td>
                <td>85</td>
                </tr>
                <tr>
                <td>Gabriel Fernandes Mota</td>
                <td>11796402</td>
                <td>Secretaria de Departamento</td>
                <td><button class="button">Remover permissão</button></td>
                <td>74</td>
                </tr>
                <tr>
                <td>Guilherme Simões Santos Marin</td>
                <td>10758748</td>
                <td>Parecerista</td>
                <td><button class="button">Remover permissão</button></td>
                <td>1058</td>
                </tr>
                <tr>
                <td>Alexandro Medeiros Fernandez dos Santos</td>
                <td>63195965</td>
                <td>Secretaria de Graduação</td>
                <td><button class="button">Remover permissão</button></td>
                <td>2541</td>
                </tr>
                <tr>
                    <td>Mario Barboza Telles Moura</td>
                    <td>01257315</td>
                    <td>Secretaria de Graduação</td>
                    <td><button class="button">Remover permissão</button></td>
                    <td>224</td>
                </tr>
                <tr>
                    <td>Julia Stankovich Pereira Lisboa Mattos</td>
                    <td>43765584</td>
                    <td>Parecerista</td>
                    <td><button class="button">Remover permissão</button></td>
                    <td>7785</td>
                </tr>
                <tr>
                    <td>André Gustavo Nakagomi Lopez</td>
                    <td>50793821</td>
                    <td>Parecerista</td>
                    <td><button class="button">Remover permissão</button></td>
                    <td>741</td>
                </tr>
                <tr>
                    <td>Fernando Henrique Junqueira Muniz Barbi Silva</td>
                    <td>11795888</td>
                    <td>Secretaria de Graduação</td>
                    <td><button class="button">Remover permissão</button></td>
                    <td>653</td>
                </tr>
                <tr>
                    <td>Luis Davi Oliveira de Almeida Campos</td>
                    <td>11849460</td>
                    <td>Coordenação</td>
                    <td><button class="button">Remover permissão</button></td>
                    <td>123</td>
                </tr>
                <tr>
                    <td>Antonio Marcos Shiro Arnauts Hachisuca</td>
                    <td>11796041</td>
                    <td>Parecerista</td>
                    <td><button class="button">Remover permissão</button></td>
                    <td>7769</td>
                </tr>
                <tr>
                    <td>Jessica Yumi Nakano Sato</td>
                    <td>11795294</td>
                    <td>Secretaria de Departamento</td>
                    <td><button class="button">Remover permissão</button></td>
                    <td>438</td>
                </tr>
            </tbody>
        </table>
    </div>
@endsection