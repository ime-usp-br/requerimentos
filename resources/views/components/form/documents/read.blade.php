<fieldset class="documents">
    <legend>Documentos</legend>
    <div class="doc-text" >Histórico com as disciplinas cursadas e aprovadas</div>
    <div class="document">
        <img src="{{ asset('img/requisitionDetails/PDF_file_icon.svg') }}" alt="PDF file icon" class="pdf-icon">
        <a href="{{ Storage::disk('public')->url($req->taken_discs_record)}}" id="taken" >Histórico escolar</a>
    </div>
    <div class="doc-text">Histórico do curso atual</div>
    <div class="document">
        <img src="{{ asset('img/requisitionDetails/PDF_file_icon.svg') }}" alt="PDF file icon" class="pdf-icon">
        <a href="{{ Storage::disk('public')->url($req->current_course_record)}}" id="current-course">Histórico escolar</a>
    </div>
    
    <div class="doc-text">Ementas das disciplinas cursadas</div>
    <div class="document">
        <img src="{{ asset('img/requisitionDetails/PDF_file_icon.svg') }}" alt="PDF file icon" class="pdf-icon">
        <a href="{{ Storage::disk('public')->url($req->taken_discs_syllabus)}}" id="taken-syllabus">Ementa</a>
    </div>
    <div class="doc-text">Ementa da disciplina a ser dispensada</div>
    <div class="document">
        <img src="{{ asset('img/requisitionDetails/PDF_file_icon.svg') }}" alt="PDF file icon" class="pdf-icon">
        <a href="{{ Storage::disk('public')->url($req->requested_disc_syllabus)}}">Ementa</a>
    </div>
</fieldset>