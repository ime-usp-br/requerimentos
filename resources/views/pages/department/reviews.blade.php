@extends('layouts.app')

@section('head')
    <link rel="stylesheet" type="text/css" href="{{ asset('css/pages/sg/reviews.css') }}">

    <!-- ionicons -->
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <title>Pareceres do requerimento</title>
@endsection

@section('content')

    <div class="content">
        <header>
            <h1>Pareceres</h1>
            <a href="{{ route('sg.show', ['requisitionId' => $requisitionId ])}}" class="button">Voltar</a>
        </header>

        @forelse ($reviews as $review)
            <section class="result">
                <div class="title">Parecer de {{ $review->reviewer_name ?? 'Desconhecido' }}</div>

                {{--<div class="field">
                    <div class="label">Nome do parecerista: </div>
                    <div class="decision">{{ $review->reviewer_name }}</div>
                </div>--}}

                <div class="field">
                    <div class="label">Número USP do parecerista: </div>
                    <div class="decision">{{ $review->reviewer_nusp }}</div>
                </div>

                <div class="field">
                    <div class="label">Decisão: </div>
                    <div class="decision">{{ $review->reviewer_decision }}</div>
                </div>
                
                <div class="justification">
                    <div class="label">Justificativa</div>
                    <div class="textarea">{{ isset($review->justification) ? $review->justification : "O parecerista não escreveu uma justificativa." }}</div> 
                </div>
                        
            </section>            
        @empty
            <p>Não existem pareceres para esse requerimento.</p>
        @endforelse

    </div>
    
@endsection