@extends('layouts.app')

@section('head')
    <!-- css e javascript usados no datatables (biblioteca da tabela) -->
    <link href="https://cdn.datatables.net/v/dt/jq-3.7.0/jszip-3.10.1/dt-1.13.8/b-2.4.2/b-html5-2.4.2/r-2.5.0/datatables.min.css" rel="stylesheet">
    <script src="https://cdn.datatables.net/v/dt/jq-3.7.0/jszip-3.10.1/dt-1.13.8/b-2.4.2/b-html5-2.4.2/r-2.5.0/datatables.min.js" defer></script>


    <!-- bibliotecas usadas para ordenar as linhas da tabela por data-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.4/moment.min.js" defer></script>
    <script src="https://cdn.datatables.net/plug-ins/1.13.7/sorting/datetime-moment.js" defer></script>

    <!-- nosso javascript -->
    <script src="{{ asset('js/list.js')}}" defer></script>

    <!-- nosso css -->
    <link rel="stylesheet" type="text/css" href="{{ asset('css/pages/student/list.css') }}"> 
@endsection

@section('content')
    <header>
        <h1>Seus requerimentos</h1>
        <nav>
            <a href="/novo-requerimento" class="button">Criar requerimento</a>
            <!-- <form action="logout" id="form" method="POST"></form> -->
            <form action="{{ $logout_url }}" method="POST" id="form">
                @csrf
            </form>
            <!-- <a href="/logout" class="button">Sair</a> -->
            <button type="submit" form="form" class="button">Sair</button>
        </nav>
    </header>
    <div class="content">
        <table id="table" class="hover cell-border stripe" >
            <thead>
                <tr>
                    <th>Data de criação</th>
                    <th>Aluno</th>
                    <th>Número USP</th>
                    <th>Situação</th>
                    <th>Departamento</th>
                    <th>Id</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>18/11/2020</td>
                    <td>Roberto Oliveira Bolgheroni</td>
                    <td>11796430</td>
                    <td>Indeferido</td>
                    <td>MAT</td>
                    <td>12</td>
                </tr>
                <tr>
                    <td>12/05/2023</td>
                    <td>Ana Yoon Faria de Lima</td>
                    <td>11795273</td>
                    <td>Enviado para a secretaria</td>
                    <td>MAP</td>
                    <td>8</td>
                </tr>
                <tr>
                <td>05/05/2020</td>
                <td>Barbara Monteiro dos Santos Rosa</td>
                <td>74157318</td>
                <td>Indeferido</td>
                <td>MAE</td>
                <td>19</td>
                </tr>
                <tr>
                <td>21/06/2011</td>
                <td>Andreia Ribeiro Alves Motta</td>
                <td>59348713</td>
                <td>Recurso indeferido</td>
                <td>MAP</td>
                <td>35</td>
                </tr>
                <tr>
                <td>16/12/2005</td>
                <td>Lua Nowacki Scavacini Santilli</td>
                <td>11795492</td>
                <td>Aguardando parecer</td>
                <td>MAC</td>
                <td>59</td>
                </tr>
                <tr>
                <td>19/12/2021</td>
                <td>Debora Dangelo Reina de Araujo </td>
                <td>11221668</td>
                <td>Aguardando parecer</td>
                <td>MAT</td>
                <td>85</td>
                </tr>
                <tr>
                <td>10/02/2014</td>
                <td>Gabriel Fernandes Mota</td>
                <td>11796402</td>
                <td>Aguardando resultado</td>
                <td>MAE</td>
                <td>74</td>
                </tr>
                <tr>
                <td>13/08/2002</td>
                <td>Guilherme Simões Santos Marin</td>
                <td>10758748</td>
                <td>Deferido</td>
                <td>MAC</td>
                <td>1058</td>
                </tr>
                <tr>
                <td>28/01/2008</td>
                <td>Alexandro Medeiros Fernandez dos Santos</td>
                <td>63195965</td>
                <td>Enviado para a secretaria</td>
                <td>MAT</td>
                <td>2541</td>
                </tr>
                <tr>
                    <td>11/03/2018</td>
                    <td>Mario Barboza Telles Moura</td>
                    <td>01257315</td>
                    <td>Recurso em análise</td>
                    <td>MAE</td>
                    <td>224</td>
                </tr>
                <tr>
                    <td>13/09/2022</td>
                    <td>Julia Stankovich Pereira Lisboa Mattos</td>
                    <td>43765584</td>
                    <td>Indeferido</td>
                    <td>MAC</td>
                    <td>7785</td>
                </tr>
                <tr>
                    <td>06/07/2024</td>
                    <td>André Gustavo Nakagomi Lopez</td>
                    <td>50793821</td>
                    <td>Enviado para a secretaria</td>
                    <td>MAP</td>
                    <td>741</td>
                </tr>
                <tr>
                    <td>10/04/2010</td>
                    <td>Fernando Henrique Junqueira Muniz Barbi Silva</td>
                    <td>11795888</td>
                    <td>Indeferido</td>
                    <td>MAP</td>
                    <td>653</td>
                </tr>
                <tr>
                    <td>08/08/2013</td>
                    <td>Luis Davi Oliveira de Almeida Campos</td>
                    <td>11849460</td>
                    <td>Recurso deferido</td>
                    <td>MAE</td>
                    <td>123</td>
                </tr>
                <tr>
                    <td>02/03/2004</td>
                    <td>Antonio Marcos Shiro Arnauts Hachisuca</td>
                    <td>11796041</td>
                    <td>Aguardando Resultado</td>
                    <td>MAT</td>
                    <td>7769</td>
                </tr>
                <tr>
                    <td>01/01/2018</td>
                    <td>Jessica Yumi Nakano Sato</td>
                    <td>11795294</td>
                    <td>Deferido</td>
                    <td>MAC</td>
                    <td>438</td>
                </tr>
            </tbody>
        </table>
    </div>
@endsection