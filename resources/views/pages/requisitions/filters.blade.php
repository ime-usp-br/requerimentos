@extends('layouts.app')

@section('head')
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@400&display=swap">

    <link rel="stylesheet" href="{{ asset('css/pages/filters/filters.css') }}">
@endsection

@section('content')
    <header class="filters-header">
        <h1>Filtros de Requerimentos</h1>
        <div class="header-buttons">
        <a href="{{ route('sg.list') }}" class="button">Início</a>
            <a href="{{ route('sg.users') }}" class="button">Administrar usuários</a>
            <a href="{{ route('sg.newRequisition') }}" class="button">Criar requerimento</a>
        </div>
    </header>
    
    <div class="filters-content">
        <form action="{{ route('pages.requisitions.filterAndExport') }}" method="GET" class="filters-form">
            <div class="form-group">
                <label for="internal_status">Situação:</label>
                <select name="internal_status" id="internal_status" class="form-control">
                    @foreach ($internal_statusOptions as $status)
                        <option value="{{ $status }}">{{ $status }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="department">Departamento:</label>
                <select name="department" id="department" class="form-control">
                    @foreach ($departments as $department)
                        <option value="{{ $department }}">{{ $department }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="requested_disc_type">Tipo de Disciplina:</label>
                <select name="requested_disc_type" id="requested_disc_type" class="form-control">
                    @foreach($discTypes as $type)
                        <option value="{{ $type }}">{{ $type }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="start_date">Data Inicial:</label>
                <input type="date" name="start_date" id="start_date" class="form-control">
            </div>

            <div class="form-group">
                <label for="end_date">Data Final:</label>
                <input type="date" name="end_date" id="end_date" class="form-control">
            </div>

            <div class="export-buttons">
                <button type="submit" name="export_type" value="sg_meeting" class="button">Reunião da CG</button>
                <button type="submit" name="export_type" value="robosinho" class="button">Robozinho</button>
            </div>
        </form>
    </div>
@endsection

