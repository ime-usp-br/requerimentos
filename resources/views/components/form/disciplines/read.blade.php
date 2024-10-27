<fieldset class="disciplines">
    <legend>Disciplinas</legend>
    <div class="taken">
        <div class="disc-title" >Disciplinas cursadas</div>
        <div class="disc-list">
            @foreach($takenDiscs as $disc)
                <div class="disc">
                    <label>
                        <!-- Nome <input type="text" name="{{ 'disc' . $loop->iteration . '-name'}}" value="{{ $disc->name }}" id="disc-name" required {{ $readOnly ? 'readonly' : '' }}> -->
                        Nome <input type="text" name="{{ 'disc' . $loop->iteration . '-name'}}" value="{{ $disc->name }}" class="large-field" required {{ $readOnly ? 'readonly' : '' }}>
                    </label>
                    <label class="disc-institution">
                        Instituição em que foi cursada
                        <!-- <input type="text" name="{{ 'disc' . $loop->iteration . '-institution' }}" id="disc-institution" value="{{ $disc->institution }}" required {{ $readOnly ? 'readonly' : '' }}> -->
                        <input type="text" name="{{ 'disc' . $loop->iteration . '-institution' }}" class="large-field" value="{{ $disc->institution }}" required {{ $readOnly ? 'readonly' : '' }}>
                    </label>
                    @error('disc-institution')
                        <style>
                            .disc-institution input {
                                outline: 1.6px solid red;
                            }
                            .disc-institution {
                                color: red;
                            }
                        </style>
                    @enderror
                    <div class="disc-middle-row">
                        <label class="disc-code">
                            Sigla
                            <input type="text" name="{{ 'disc' . $loop->iteration . '-code' }}" value="{{ $disc->code }}" id="disc-code" {{ $readOnly ? 'readonly' : '' }}>
                        </label>
                        <label>
                            Ano 
                            <input type="text" name="{{ 'disc' . $loop->iteration . '-year'}}" value="{{ $disc->year }}" id="disc-year" required {{ $readOnly ? 'readonly' : '' }}>
                        </label>
                        <label>
                            Nota <input type="text" name="{{ 'disc' . $loop->iteration . '-grade'}}" value="{{ $disc->grade }}" id="disc-grade" required {{ $readOnly ? 'readonly' : '' }}>
                        </label>
                    </div>

                    <div class="disc-last-row">
                        @if ($readOnly)
                            <label>
                                Semestre
                                <input type="text" class="small-field" value="{{ $disc->semester ?? '' }}" readonly>
                            </label>
                        @else
                            <label>
                                Semestre 
                                <select name="{{ 'disc' . $loop->iteration .'-semester' }}" id="disc-semester">
                                    <option 
                                        value=""
                                        >
                                        Selecione o semestre
                                    </option>
                                    <option 
                                        value="Primeiro"
                                        @if($disc->semester == 'Primeiro') 
                                            selected 
                                        @endif
                                        >
                                        Primeiro
                                    </option>
                                    <option 
                                        value="Segundo"
                                        @if($disc->semester == 'Segundo') 
                                            selected 
                                        @endif
                                        >
                                        Segundo
                                    </option>
                                </select>
                            </label>
                        @endif
                        
                        {{-- @if ($withRecordButton)
                            <a href="#" class="button record-button">Requerimentos anteriores</a>
                        @endif --}}
                    </div>
                    <input type="hidden" name="{{ 'disc' . $loop->iteration . '-id'}}" value="{{$disc->id}}" >
                </div>
            @endforeach
        </div>
    </div>

    <div class="required">
        <div class="disc-title" >Disciplina requerida</div>
        <div class="disc-list">
            <div class="disc">
                <label>
                    <!-- Nome <input type="text" name="requested-disc-name" value="{{ $req->requested_disc }}" id="disc-name" required> -->
                    Nome 
                    <input type="text" name="requested-disc-name" value="{{ $req->requested_disc }}" class="large-field" {{ $readOnly ? 'readonly' : '' }} required>
                </label>
                <label class="disc-department">
                    @if ($readOnly)
                        <label>
                            Departamento
                            <input type="text" class="small-field" value="{{ $req->department ?? '' }}" readonly>
                        </label>
                    @else
                        Departamento
                        <select name="disc-department" required>
                            <option value="">
                                Selecione o departamento
                            </option>
                            <option 
                                value="MAC"
                                @if($req->department == 'MAC') 
                                    selected 
                                @endif
                                >
                                MAC
                            </option>
                            <option 
                                value="MAE"
                                @if($req->department == 'MAE') 
                                    selected 
                                @endif
                                >
                                MAE
                            </option>
                            <option 
                                value="MAP"
                                @if($req->department == 'MAP') 
                                    selected 
                                @endif
                                >MAP
                            </option>
                            <option 
                                value="MAT"
                                @if($req->department == 'MAT') 
                                    selected 
                                @endif
                                >
                                MAT
                            </option>
                            <option 
                                value="Disciplina de fora do IME"
                                @if($req->department == 'Disciplina de fora do IME') 
                                    selected 
                                @endif
                                >
                                Disciplina de fora do IME
                            </option>
                        </select>                        
                    @endif
                </label>
                @error('disc-department')
                    <style>
                        .disc-department select {
                            outline: 1.6px solid red;
                        }
                        .disc-department {
                            color: red;
                        }
                    </style>
                @enderror

                @if ($readOnly)
                    <label>
                        Tipo
                        <input type="text" class="type-input" value="{{ $req->requested_disc_type ?? '' }}" readonly>
                    </label>
                @else
                    <label>
                        Tipo 
                        <select id="disc-type" name="requested-disc-type">
                            <option value="">Selecione o tipo</option>
                            <option 
                            value="Obrigatória"
                            @if($req->requested_disc_type == 'Obrigatória') 
                            selected 
                            @endif
                            >
                            Obrigatória
                        </option>
                        <option 
                        value="Optativa Eletiva" 
                        @if($req->requested_disc_type == 'Optativa Eletiva') 
                        selected 
                                @endif
                                >
                                Optativa Eletiva
                            </option>
                            <option 
                            value="Optativa Livre" 
                            @if($req->requested_disc_type == 'Optativa Livre') 
                            selected 
                                @endif
                                >
                                Optativa Livre
                            </option>
                            <option 
                                value="Extracurricular" 
                                @if($req->requested_disc_type == 'Extracurricular') 
                                    selected 
                                @endif
                                >
                                Extracurricular
                            </option>
                        </select>
                    </label>                    
                @endif

                
                <div class="disc-last-row">
                    <label class="disc-code">
                        Sigla
                        <input type="text" name="requested-disc-code" value="{{ $req->requested_disc_code }}" id="disc-code" required>
                    </label>
                    
                    @if ($withRecordButton)
                        <a href={{url()->current() . '/pareceres-anteriores/' . $req->requested_disc_code . "?institution=". $req->takenDisciplines[0]->institution}} class="button record-button">Pareceres anteriores</a>
                    @endif
                    
                </div>
            </div>
            
        </div>
    </div>

    <input type="hidden" name="takenDiscCount" id="taken-disc-count" value="{{ $takenDiscs->count() }}" >
</fieldset>