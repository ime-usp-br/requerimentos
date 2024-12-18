@extends('layouts.app')

@section('head')
    
    <link rel="stylesheet" type="text/css" href="{{ asset('css/pages/student/detail.css') }}">
    <script src="{{ asset('js/student/detail.js')}}" defer></script>

    <!-- ionicons -->
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <title>Detalhes do requerimento</title>
@endsection

@section('content')
    <div class="content">
        <header>
            <h1>Detalhes do requerimento</h1>
            <a href="{{ route('student.list')}}" class="button">Voltar</a>
        </header>

        @if ($req->result === 'Inconsistência nas informações')
            <section class="result">
                <h2>Inconsistência nos dados fornecidos</h2>
                @if ($req->result_text)
                    <div class="field-wrapper">
                        Por favor, atualize as seguintes informações do seu requerimento
                        <div class="field result-text large-field">{{ $req->result_text }}</div>
                    </div>
                @else
                    <div class="field-wrapper">
                        Por favor, atualize as informações do seu requerimento através do link abaixo
                    </div>                    
                @endif
                <a href="{{ route('student.edit', ['requisitionId' => $req->id]) }}" class="button" target="_blank">Editar o requerimento</a>            
            </section>
        @elseif ($req->result === 'Indeferido')
            <section class="result">
                <h2>Requerimento indeferido</h2>
                @if ($req->result_text)
                    <div class="field-wrapper">
                        Justificativa
                        <div class="field result-text large-field">{{ $req->result_text }}</div>
                    </div>
                @else
                    <div class="field-wrapper">
                        O requerimento passou por análise interna e foi indeferido. Para saber mais informações, entre em contato com o serviço de graduação.
                    </div> 
                @endif
            </section>
        @elseif ($req->result === 'Deferido')
            <section class="result">
                <h2>Requerimento deferido</h2>
                @if ($req->result_text)
                    <div class="field-wrapper">
                        Justificativa
                        <div class="field result-text large-field">{{ $req->result_text }}</div>
                    </div>
                @else
                    <div class="field-wrapper">
                        O requerimento passou por análise interna e foi deferido. Para saber mais informações, entre em contato com o serviço de graduação.
                    </div> 
                @endif
            </section>
        @endif

        <section>
            @if ($req->result)
                <h2>Informações do requerimento</h2>
            @endif
            <article class="disciplines">
            <div class="required">
                    <h3>Disciplina a ser dispensada</h3>
                    <div class="disc-list">
                        <div class="disc">
                            <div class="field-wrapper">
                                Nome 
                                <div class="field large-field">{{ $req->requested_disc }}</div>
                            </div>

                            <div class="disc-middle-row">
                                <div class="field-wrapper ">
                                    Departamento 
                                    <div class="field department">{{ $req->department }}</div>
                                </div>
                                <div class="field-wrapper ">
                                    Sigla 
                                    <div class="field" id="disc-code">{{ $req->requested_disc_code }}</div>
                                </div>                                
                            </div>
                            <div class="field-wrapper ">
                                Tipo
                                <div class="field" id="disc-type">{{ $req->requested_disc_type }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="taken">
                    @if ($takenDiscs->count() > 1)
                        <h3>Disciplinas cursadas</h3>
                    @else
                        <h3>Disciplina cursada</h3>
                    @endif
                    
                    <div class="disc-list">
                        @foreach($takenDiscs as $disc)
                            @if (!$loop->first)
                                <hr>
                            @endif
                            <div class="disc">
                                <div class="field-wrapper">
                                    Nome
                                    <div class="field large-field">{{ $disc->name }}</div>
                                </div>

                                <div class="field-wrapper">
                                    Instituição em que foi cursada 
                                    <div id="disc-institution" class="field">{{ $disc->institution }}</div>
                                </div>
                                
                                <div class="disc-middle-row">
                                    <div class="field-wrapper">
                                        Sigla 
                                        <div id="disc-code" class="field">{{ $disc->code }}</div>
                                    </div>

                                    <div class="field-wrapper">
                                        Ano 
                                        <div id="disc-year" class="field">{{ $disc->year }}</div>
                                    </div>

                                    <div class="field-wrapper">
                                        Nota 
                                        <div id="disc-grade" class="field">{{ $disc->grade }}</div>
                                    </div>
                                </div>

                                <div class="disc-last-row">
                                    <div class="field-wrapper">
                                        Semestre 
                                        <div id="disc-semester" class="field">{{ $disc->semester }}</div>
                                    </div>
                                </div>

                            </div>
                        @endforeach
                    </div>
                </div>

                
            </article>
            
            <article class="documents">
                <h3>Documentos</h3>
                <div class="doc-text" >Histórico escolar da instituição de origem</div>
                <div class="document">
                    <img src="{{ asset('img/student/detail/PDF_file_icon.svg') }}" alt="PDF file icon" class="pdf-icon">
                    @if (count($takenDiscsRecords) > 1)
                        <div class="files-wrapper">
                            <label class="files">
                                Arquivos
                                <select class="docs-select">
                                    @foreach ($takenDiscsRecords as $record)
                                        <option value="{{ $record->id }}" class="option">
                                            Arquivo inserido em {{ \Illuminate\Support\Carbon::parse($record->created_at->toDateTimeString())->format('d/m/Y \à\s H:i') }}
                                        </option>
                                    @endforeach
                                </select>
                            </label>
                            <a href="{{ '/documento/' . $takenDiscsRecords[0]->id }}" class="button file-link" target="_black">Link do arquivo</a>
                        </div>
                    @else
                        <a href="{{ '/documento/' . $takenDiscsRecords[0]->id }}" id="taken">Histórico escolar</a>
                    @endif

                    {{--<a href="{{ Storage::disk('public')->url($req->taken_discs_record)}}" id="taken" target="_blank" >Histórico escolar</a>--}}
                </div>
                <div class="doc-text">Histórico escolar do curso atual</div>
                <div class="document">
                    <img src="{{ asset('img/student/detail/PDF_file_icon.svg') }}" alt="PDF file icon" class="pdf-icon">
                    @if (count($currentCourseRecords) > 1)
                        <div class="files-wrapper">
                            <label class="files">
                                Arquivos
                                <select class="docs-select">
                                    @foreach ($currentCourseRecords as $record)
                                        <option value="{{ $record->id }}" class="option">
                                            Arquivo inserido em {{ \Illuminate\Support\Carbon::parse($record->created_at->toDateTimeString())->format('d/m/Y \à\s H:i') }}
                                        </option>
                                    @endforeach
                                </select> 
                            </label>
                            <a href="{{ '/documento/' . $currentCourseRecords[0]->id }}" class="button file-link" target="_black">Link do arquivo</a>
                        </div>

                    @else
                        <a href="{{ '/documento/' . $currentCourseRecords[0]->id }}" id="taken" >Histórico escolar</a>
                    @endif
                </div>
                
                <div class="doc-text">Ementas das disciplinas cursadas</div>
                <div class="document">
                    <img src="{{ asset('img/student/detail/PDF_file_icon.svg') }}" alt="PDF file icon" class="pdf-icon">
                    @if (count($takenDiscSyllabi) > 1)
                        <div class="files-wrapper">
                            <label class="files">
                                Arquivos
                                <select class="docs-select">
                                    @foreach ($takenDiscSyllabi as $syllabus)
                                        <option value="{{ $syllabus->id }}" class="option">
                                            Arquivo inserido em {{ \Illuminate\Support\Carbon::parse($syllabus->created_at->toDateTimeString())->format('d/m/Y \à\s H:i') }}
                                        </option>
                                    @endforeach
                                </select> 
                            </label>
                            <a href="{{ '/documento/' . $takenDiscSyllabi[0]->id }}" class="button file-link" target="_black">Link do arquivo</a>
                        </div>
                    @else
                        <a href="{{ '/documento/' . $takenDiscSyllabi[0]->id }}" id="taken">Ementa</a>
                    @endif
                </div>
                <div class="doc-text">Ementa da disciplina a ser dispensada</div>
                <div class="document">
                    <img src="{{ asset('img/student/detail/PDF_file_icon.svg') }}" alt="PDF file icon" class="pdf-icon">
                    @if (count($requestedDiscSyllabi) > 1)
                        <div class="files-wrapper">
                            <label class="files">
                                Arquivos
                                <select class="docs-select">
                                    @foreach ($requestedDiscSyllabi as $syllabus)
                                        <option value="{{ $syllabus->id }}" class="option" >
                                            Arquivo inserido em {{ \Illuminate\Support\Carbon::parse($syllabus->created_at->toDateTimeString())->format('d/m/Y \à\s H:i') }}
                                        </option>
                                    @endforeach
                                </select>                     
                            </label>
                            <a href="{{ '/documento/' . $requestedDiscSyllabi[0]->id }}" class="button file-link" target="_black">Link do arquivo</a>
                        </div>
                    @else
                        <a href="{{ '/documento/' . $requestedDiscSyllabi[0]->id }}" id="taken">Ementa</a>
                    @endif
                </div>
            </article>

            <article class="observations">
                <h2>Observações</h2>
                <div class="textarea">{{ isset($req) ? $req->observations : null }}</div>
            </article>
        </section>
    </div>
@endsection