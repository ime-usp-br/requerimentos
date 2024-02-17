<fieldset class="documents">
    <legend>Documentos</legend>
    <div class="doc-text" >Adicione o histórico escolar com as disciplinas cursadas</div>
    <div class="document">
        <img src="{{ asset('img/newRequisition/PDF_file_icon.svg') }}" alt="PDF file icon" class="pdf-icon">
        <div>
            <label class="button">
                Anexar arquivo
                <input type="file" name="taken-disc-record" id="taken-disc-record" accept=".pdf">
            </label>
            @error('taken-disc-record')
                <p class="error-message">Adicione um arquivo pdf com tamanho máximo de 5mb</p>
            @enderror
            <div class="taken-disc-record">Nenhum arquivo pdf anexado</div>
        </div>
    </div>
    <div class="doc-text">Adicione o histórico do curso atual</div>
    <div class="document">
        <img src="{{ asset('img/newRequisition/PDF_file_icon.svg') }}" alt="PDF file icon" class="pdf-icon">
        <div>
            <label class="button">
                Anexar arquivo
                <input type="file" name="course-record" id="course-record" accept=".pdf">
            </label>
            @error('course-record')
                <p class="error-message">Adicione um arquivo pdf com tamanho máximo de 5mb</p>
            @enderror
            <div class="course-record">Nenhum arquivo pdf anexado</div>
            
        </div>
    </div>
    <div class="doc-text">Adicione as ementas de todas as disciplinas cursadas</div>
    <div class="document">
        <img src="{{ asset('img/newRequisition/PDF_file_icon.svg') }}" alt="PDF file icon" class="pdf-icon">
        <div>
            <label class="button">
                Anexar arquivo
                <input type="file" name="taken-disc-syllabus" id="taken-disc-syllabus" accept=".pdf">
            </label>
            @error('course-record')
                <p class="error-message">Adicione um arquivo pdf com tamanho máximo de 5mb</p>
            @enderror
            <div class="taken-disc-syllabus">Nenhum arquivo pdf anexado</div>
        </div>
    </div>
    <div class="doc-text">Adicione as ementas de todas as disciplinas requeridas</div>
    <div class="document">
        <img src="{{ asset('img/newRequisition/PDF_file_icon.svg') }}" alt="PDF file icon" class="pdf-icon">
        <div>
            <label class="button">
                Anexar arquivo
                <input type="file" name="requested-disc-syllabus" id="requested-disc-syllabus" accept=".pdf">
            </label>
            @error('course-record')
                <p class="error-message">Adicione um arquivo pdf com tamanho máximo de 5mb</p>
            @enderror
            <div class="requested-disc-syllabus">Nenhum arquivo pdf anexado</div>
        </div>
    </div>
</fieldset>