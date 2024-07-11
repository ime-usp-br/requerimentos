<fieldset class="decision">
    <legend>Parecer</legend>
    <div class="decision-radio" >
        <label class="radio-button" >
            <input 
                type="radio" 
                id="unfinished" 
                name="decision" 
                value="Sem decisão" 
                checked
                @if(isset($review) && $review->reviewer_decision == 'Sem decisão') 
                    checked
                @endif
            />
            <span class="label-visible">
                <span class="fake-radiobutton"></span>
                Sem decisão
            </span>
        </label>

        <label class="radio-button">
            <input 
                type="radio" 
                id="accepted" 
                name="decision" 
                value="Deferido"
                @if(isset($review) && $review->reviewer_decision === 'Deferido') 
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
                id="rejected" 
                name="decision" 
                value="Indeferido" 
                @if(isset($review) && $review->reviewer_decision === 'Indeferido') 
                     checked
                @endif
            />
            <span class="label-visible">
                <span class="fake-radiobutton"></span>
                Indeferido
            </span>
        </label>
    </div>

    <div class="justification-title">Justificativa</div>

    <label class="justification"><textarea name="justification">{{ isset($review) ? $review->justification : null }}</textarea></label>
</fieldset>