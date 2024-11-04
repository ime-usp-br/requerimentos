<fieldset class="documents">
    <legend>Documentos</legend>
    <div class="doc-text" >Adicione o histórico escolar da instituição de origem (máx. 150KB)</div>
    <div class="document">
        <img src="{{ asset('img/newRequisition/PDF_file_icon.svg') }}" alt="PDF file icon" class="pdf-icon">
        <div>
            <label class="button">
                Anexar arquivo
                <input type="file" class="file-input" name="taken-disc-record" id="taken-disc-record" accept=".pdf">
            </label>
            @error('taken-disc-record')
                <p class="error-message">Adicione um arquivo pdf com tamanho máximo de 150KB</p>
            @enderror
            <div class="taken-disc-record">Nenhum arquivo PDF anexado</div>
        </div>
    </div>
    <div class="doc-text">Adicione o histórico escolar do curso atual (máx. 150KB)</div>
    <div class="document">
        <img src="{{ asset('img/newRequisition/PDF_file_icon.svg') }}" alt="PDF file icon" class="pdf-icon">
        <div>
            <label class="button">
                Anexar arquivo
                <input type="file" class="file-input" name="course-record" id="course-record" accept=".pdf">
            </label>
            @error('course-record')
                <p class="error-message">Adicione um arquivo pdf com tamanho máximo de 150KB</p>
            @enderror
            <div class="course-record">Nenhum arquivo PDF anexado</div>
            
        </div>
    </div>
    <div class="doc-text">Adicione as ementas das disciplinas cursadas referentes a este pedido (máx. 150KB)</div>
    <div class="document">
        <img src="{{ asset('img/newRequisition/PDF_file_icon.svg') }}" alt="PDF file icon" class="pdf-icon">
        <div>
            <label class="button">
                Anexar arquivo
                <input type="file" class="file-input" name="taken-disc-syllabus" id="taken-disc-syllabus" accept=".pdf">
            </label>
            @error('taken-disc-syllabus')
                <p class="error-message">Adicione um arquivo pdf com tamanho máximo de 150KB</p>
            @enderror
            <div class="taken-disc-syllabus">Nenhum arquivo pdf anexado</div>
        </div>
    </div>
    <div class="doc-text">Adicione a ementa da disciplina a ser dispensada (máx. 150KB)</div>
    <div class="document">
        <img src="{{ asset('img/newRequisition/PDF_file_icon.svg') }}" alt="PDF file icon" class="pdf-icon">
        <div>
            <label class="button">
                Anexar arquivo
                <input type="file" class="file-input" name="requested-disc-syllabus" id="requested-disc-syllabus" accept=".pdf">
            </label>
            @error('requested-disc-syllabus')
                <p class="error-message">Adicione um arquivo pdf com tamanho máximo de 150KB</p>
            @enderror
            <div class="requested-disc-syllabus">Nenhum arquivo pdf anexado</div>
        </div>
    </div>
</fieldset>