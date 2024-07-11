<fieldset class="observations">
    <legend>Observações</legend>
    <label for="observations">
        @if ($shownInternally)
            Informações adicionais acrescentadas pelo aluno que fez o requerimento
        @else
            Insira aqui informações adicionais pertinentes ao requerimento
        @endif
        <textarea name="observations" id="observations" {{ $readOnly ? 'readonly' : ''}} >{{ isset($req) ? $req->observations : old('observations') }}</textarea>
    </label>
</fieldset>