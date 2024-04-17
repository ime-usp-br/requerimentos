<fieldset class="result">
    <legend>Resultado</legend>
    <div class="result-radio" >

        <label class="radio-button" >
            <input 
                type="radio" 
                name="result" 
                value="Sem resultado" 
                @if(isset($req) && $req->result == 'Sem resultado') 
                    checked 
                @endif
            />
            <span class="label-visible">
                <span class="fake-radiobutton"></span>
                Sem resultado
            </span>
        </label>

        <label class="radio-button">
            <input 
                type="radio" 
                name="result" 
                value="Inconsistência nas informações"
                @if(isset($req) && $req->result == 'Inconsistência nas informações') 
                     checked
                @endif
            />
            <span class="label-visible">
                <span class="fake-radiobutton"></span>
                Inconsistência nas informações
            </span>
        </label>
        
        <label class="radio-button">
            <input 
                type="radio" 
                name="result" 
                value="Deferido" 
                @if(isset($req) && $req->result == 'Deferido') 
                     checked
                @endif
            />
            <span class="label-visible">
                <span class="fake-radiobutton"></span>
                Deferido
            </span>
        </label>
        <label class="radio-button">
            <input 
                type="radio" 
                name="result" 
                value="Indeferido" 
                @if(isset($req) && $req->result == 'Indeferido') 
                     checked
                @endif
            />
            <span class="label-visible">
                <span class="fake-radiobutton"></span>
                Indeferido
            </span>
        </label>
    </div>
    
    <label class="result-text">Insira aqui o texto que será retornado para o aluno<textarea name="result-text">{{ isset($req) ? $req->result_text : null }}</textarea></label>
</fieldset>