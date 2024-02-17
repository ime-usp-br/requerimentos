<fieldset class="observations">
    <legend>Observações</legend>
    <label for="observations">
        Insira aqui informações adicionais pertinentes ao requerimento
        <textarea name="observations" id="observations">{{ isset($req) ? $req->observations : null }}</textarea>
    </label>
    
</fieldset>