<fieldset class="decision">
    <legend>Parecer</legend>

    {{--@if ($req->reviewer_decision !== 'Sem decisão')
        <div class="reviewer-id">
            <label class="large-field">
                Nome do parecerista
                <input type="text" name="reviewer_name" value='{{ $req->reviewer_name}}'>
            </label>

            <label class="reviewer-nusp">
                Número USP do parecerista
                <input type="text" name="reviewer_nusp"  value="{{ $req->reviewer_nusp }}">
            </label>
        </div>
    @endif--}}

    <!--<div class="appraisal-title">Decisão</div>-->
    <div class="decision-radio" >
        <label class="radio-button" >
            <input 
                type="radio" 
                id="unfinished" 
                name="decision" 
                value="Sem decisão" 
                checked
                {{--@if(isset($req) && $req->reviewer_decision == 'Sem decisão') 
                     
                @endif--}}
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
                {{--@if(isset($req) && $req->reviewer_decision == 'Deferido') 
                     checked
                @endif--}}
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
                {{--@if(isset($req) && $req->reviewer_decision == 'Indeferido') 
                     checked
                @endif--}}
            />
            <span class="label-visible">
                <span class="fake-radiobutton"></span>
                Indeferido
            </span>
        </label>
    </div>

    <!-- <div>

    </div> -->
    <div class="appraisal-title">Justificativa</div>

    {{--<label class="appraisal"><textarea name="appraisal">{{ isset($req) ? $req->appraisal : null }}</textarea></label>--}}
    <label class="appraisal"><textarea name="appraisal"></textarea></label>
</fieldset>