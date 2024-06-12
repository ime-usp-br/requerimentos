@extends('layouts.app')

@section('head')
    
    <link rel="stylesheet" type="text/css" href="{{ asset('css/pages/sg/detail.css') }}">
    <script src="{{ asset('js/department/detail.js')}}" defer></script>

    <!-- ionicons -->
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <title>Detalhes do requerimento</title>
@endsection

@section('content')
    <div class="content">
        <header>
            <h1>Detalhes do requerimento {{ $req->id }} </h1>
        </header>
        
        <nav class="nav">
            <a href="{{ route('record.versionReviews', ['eventId' =>  $event->id ]) }}" class="button">Pareceres</a>
            <a href="{{ route('record.requisition', ['requisitionId' => $event->requisition_id ]) }}" class="button">Voltar</a>
        </nav>

        <form method="GET" action="{{ route('reviewer.reviewerPick', ['requisitionId' => $req->id])}}" id="form" >
            @csrf

            <x-form.personal :withRecordButton="false" :req="$req"/>

            <hr>

            <x-form.course :req="$req"/>

            <hr>

            <x-form.disciplines.read :takenDiscs="$takenDiscs" :req="$req" :withRecordButton="false" />
            
            <hr>

            <x-form.documents.read :takenDiscsRecords="$takenDiscsRecords" :currentCourseRecords="$currentCourseRecords" :takenDiscSyllabi="$takenDiscSyllabi" :requestedDiscSyllabi="$requestedDiscSyllabi"/>

            <hr>

            <x-form.observations :req="$req" />
            
            <input type="hidden" name="button" id="btnType">
        </form>
        
    </div>
@endsection