import React from 'react';

const RequistionDetails = () => {
    const user = "Aluno";

    return (
        <div class="content">
            <header>
                <h1>Detalhes do requerimento {{ $req->id }} </h1>
                <select class="mode-select">
                    <option value="readonly">Modo de exibição</option>
                    <option value="edit">Modo de edição</option>
                </select>
            </header>
            
            <nav class="nav">
                <a href="{{ route('reviewer.reviews', ['requisitionId' => $req->id ]) }}" class="button">Pareceres</a>
                <a href="{{ route('record.requisition', ['requisitionId' => $req->id]) }}" class="button" >Histórico do requerimento</a>
                <a href="{{ route('sg.list') }}" class="button">Voltar</a>
            </nav>

            <form method="POST" action="{{ route('sg.update', ['requisitionId' => $req->id])}}" id="form" >
                <x-form.personal :withRecordButton="true" :req="$req"/>
                <x-form.course :req="$req" :readOnly="False"/>
                <x-form.disciplines.read :takenDiscs="$takenDiscs" :req="$req" :withRecordButton="false" :readOnly="False" />
                <x-form.documents.read :takenDiscsRecords="$takenDiscsRecords" :currentCourseRecords="$currentCourseRecords" :takenDiscSyllabi="$takenDiscSyllabi" :requestedDiscSyllabi="$requestedDiscSyllabi"/>
                <x-form.observations :req="$req" />
                <x-form.result :req="$req" />
                <input type="hidden" name="button" id="btnType">
            </form>

            <div class="nav"> 
                <a href="{{ route('sg.list') }}" class="button">Voltar</a>
                <button type="submit" form="form" class="button" id="save-btn">Salvar alterações</button>
                <button type="submit" form="form" class="button" id="department-btn">Enviar para o departamento</button>
                <button type="submit" form="form" class="button" id="reviewer-btn">Enviar para um parecerista</button>
            </div>
            
        </div>
    );
};

export default RequistionDetails;