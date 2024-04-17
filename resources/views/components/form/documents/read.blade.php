<fieldset class="documents">
    <legend>Documentos</legend>
    <div class="doc-text" >Histórico com as disciplinas cursadas e aprovadas</div>
    <div class="document">
        <img src="{{ asset('img/student/detail/PDF_file_icon.svg') }}" alt="PDF file icon" class="pdf-icon">
        @if (count($takenDiscsRecords) > 1)
            <div class="files-wrapper">
                <label class="files">
                    Arquivos
                    <select class="docs-select">
                        @foreach ($takenDiscsRecords as $record)
                            <option value="{{ Storage::disk('public')->url($record->path) }}" class="option">
                                Arquivo inserido em {{ \Illuminate\Support\Carbon::parse($record->created_at->toDateTimeString())->format('d/m/Y \à\s H:i') }}
                            </option>
                        @endforeach
                    </select>
                </label>
                <a href="{{ Storage::disk('public')->url($takenDiscsRecords[0]->path)}}" class="button file-link" target="_black">Link do arquivo</a>
            </div>
        @else
            <a href="{{ Storage::disk('public')->url($takenDiscsRecords[0]->path)}}" id="taken" >Histórico escolar</a>
        @endif
    </div>
    <div class="doc-text">Histórico do curso atual</div>
    <div class="document">
        <img src="{{ asset('img/student/detail/PDF_file_icon.svg') }}" alt="PDF file icon" class="pdf-icon">
        @if (count($currentCourseRecords) > 1)
            <div class="files-wrapper">
                <label class="files">
                    Arquivos
                    <select class="docs-select">
                        @foreach ($currentCourseRecords as $record)
                            <option value="{{ Storage::disk('public')->url($record->path) }}" class="option">
                                Arquivo inserido em {{ \Illuminate\Support\Carbon::parse($record->created_at->toDateTimeString())->format('d/m/Y \à\s H:i') }}
                            </option>
                        @endforeach
                    </select> 
                </label>
                <a href="{{ Storage::disk('public')->url($currentCourseRecords[0]->path)}}" class="button file-link" target="_black">Link do arquivo</a>
            </div>

        @else
            <a href="{{ Storage::disk('public')->url($currentCourseRecords[0]->path)}}" id="taken" >Histórico escolar</a>
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
                            <option value="{{ Storage::disk('public')->url($syllabus->path) }}" class="option">
                                Arquivo inserido em {{ \Illuminate\Support\Carbon::parse($syllabus->created_at->toDateTimeString())->format('d/m/Y \à\s H:i') }}
                            </option>
                        @endforeach
                    </select> 
                </label>
                <a href="{{ Storage::disk('public')->url($takenDiscSyllabi[0]->path)}}" class="button file-link" target="_black">Link do arquivo</a>
            </div>
        @else
            <a href="{{ Storage::disk('public')->url($takenDiscSyllabi[0]->path)}}" id="taken">Ementa</a>
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
                            <option value="{{ Storage::disk('public')->url($syllabus->path) }}" class="option" >
                                Arquivo inserido em {{ \Illuminate\Support\Carbon::parse($syllabus->created_at->toDateTimeString())->format('d/m/Y \à\s H:i') }}
                            </option>
                        @endforeach
                    </select>                     
                </label>
                <a href="{{ Storage::disk('public')->url($requestedDiscSyllabi[0]->path)}}" class="button file-link" target="_black">Link do arquivo</a>
            </div>
        @else
            <a href="{{ Storage::disk('public')->url($requestedDiscSyllabi[0]->path)}}" id="taken">Ementa</a>
        @endif
    </div>
</fieldset>