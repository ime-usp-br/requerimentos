 <fieldset class="disciplines">
    <legend>Disciplinas</legend>
    

    <div class="requested">
        <div class="disc-title">Disciplina a ser dispensada</div>
        <p class="instruction">Adicione aqui as informações da disciplina a ser dispensada</p>
        <!-- <div class="disc-list">
            <p class="empty-list-message">Adicione aqui as disciplinas a serem adicionadas ao seu histórico.</p>
        </div>
        <div class="disc-management"> 
            <button type="button" class="button add-disc">Adicionar disciplina</button>
            <button type="button" class="button remove-disc">Remover disciplina</button>
        </div> -->
        <div class="disc">
            <label class="requested-disc-name">
                Nome
                <input type="text" id="disc-name" name="requested-disc-name" value="{{ old('requested-disc-name') }}" required>
            </label>
            @error('requested-disc-name')
                <style>
                    .requested-disc-name input {
                        outline: 1.6px solid red;
                    }
                    .requested-disc-name {
                        color: red;
                    }
                </style>
            @enderror

            <div class="disc-middle-row">
                <label class="requested-disc-type">
                    Tipo
                    <select name="requested-disc-type" id="disc-type" required>
                        <option value="">
                            Selecione o tipo
                        </option>
                        <option value="Obrigatória"
                            @if(old('requested-disc-type') === 'Obrigatória') 
                                selected 
                            @endif
                        >
                            Obrigatória
                        </option>
                        <option value="Optativa Eletiva"
                            @if(old('requested-disc-type') === 'Optativa Eletiva') 
                                selected 
                            @endif
                        >
                            Optativa Eletiva
                        </option>
                        <option value="Optativa Livre"
                            @if(old('requested-disc-type') === 'Optativa Livre') 
                                selected 
                            @endif
                        >
                            Optativa Livre
                        </option>
                        <option value="Extracurricular"
                            @if(old('requested-disc-type') === 'Extracurricular') 
                                selected 
                            @endif
                        >
                            Extracurricular
                        </option>
                    </select>
                </label>
                @error('requested-disc-type')
                    <style>
                        .requested-disc-type select {
                            outline: 1.6px solid red;
                        }
                        .requested-disc-type {
                            color: red;
                        }
                    </style>
                @enderror
                <label class="requested-disc-code">
                    Sigla
                    <input type="text" name="requested-disc-code" id="disc-code" value="{{ old('requested-disc-code') }}" required>
                </label>
                @error('requested-disc-code')
                    <style>
                        .requested-disc-code input {
                            outline: 1.6px solid red;
                        }
                        .requested-disc-code {
                            color: red;
                        }
                    </style>
                @enderror
            </div>

            <label class="disc-department">
                Departamento
                <select name="disc-department" required>
                    <option value="">Selecione o departamento</option>
                    <option value="MAC"
                        @if(old('disc-department') === 'MAC') 
                            selected 
                        @endif
                    >
                        MAC
                    </option>
                    <option value="MAE"
                        @if(old('disc-department') === 'MAE') 
                            selected 
                        @endif
                    >
                        MAE
                    </option>
                    <option value="MAP"
                        @if(old('disc-department') === 'MAP') 
                            selected 
                        @endif
                    >
                        MAP
                    </option>
                    <option value="MAT"
                        @if(old('disc-department') === 'MAC') 
                            selected 
                        @endif
                    >
                        MAT
                    </option>
                    <option value="Disciplina de fora do IME"
                        @if(old('disc-department') === 'Disciplina de fora do IME') 
                            selected 
                        @endif
                    >
                        Disciplina de fora do IME
                    </option>
                </select>
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
            
            <!-- <div class="disc-last-row">
                
            </div> -->
        </div>
    </div>


    <div class="taken">
        <div class="disc-title" >Disciplinas cursadas</div>
        <p class="instruction">Adicione aqui as disciplinas cursadas a serem utilizadas para a dispensa</p>
        <div class="disc-list">
            <div class="disc">
                <label class="disc-name">
                    Nome
                    <input type="text" name="disc1-name" id="disc-name" value="{{ old('disc1-name') }}" required>
                </label>
                @error('disc1-name')
                    <style>
                        .disc-name input {
                            outline: 1.6px solid red;
                        }
                        .disc-name {
                            color: red;
                        }
                    </style>
                @enderror
                <label class="disc-institution">
                    Instituição em que foi cursada
                    <input type="text" name="disc1-institution" id="disc-institution" value="{{ old('disc1-institution') }}" required>
                </label>
                @error('disc1-institution')
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
                        <input type="text" name="disc1-code" id="disc-code" value="{{ old('disc1-code') }}">
                    </label>
                    @error('disc1-code')
                        <style>
                            .disc-code input {
                                outline: 1.6px solid red;
                            }
                            .disc-code {
                                color: red;
                            }
                        </style>
                    @enderror
                    <label class="disc-year">
                        Ano
                        <input type="text" name="disc1-year" id="disc-year" value="{{ old('disc1-year') }}" required>
                    </label>
                    @error('disc1-year')
                        <style>
                            .disc-year input {
                                outline: 1.6px solid red;
                            }
                            .disc-year {
                                color: red;
                            }
                        </style>
                    @enderror
                    <label class="disc-grade">
                        Nota 
                        <input type="text" name="disc1-grade" id="disc-grade" value="{{ old('disc1-grade') }}" required>
                    </label>
                    @error('disc1-grade')
                        <style>
                            .disc-grade input {
                                outline: 1.6px solid red;
                            }
                            .disc-grade {
                                color: red;
                            }
                        </style>
                    @enderror
                </div>
                <div class="disc-last-row">
                    <label class="disc-semester">
                        Semestre
                        <select name="disc1-semester" id="disc-semester" required>
                            <option value="">Selecione o semestre</option>
                            <option value="Primeiro"
                                @if(old('disc1-semester') === 'Primeiro') 
                                    selected 
                                @endif
                            >
                                Primeiro
                            </option>
                            <option value="Segundo"
                                @if(old('disc1-semester') === 'Segundo') 
                                    selected 
                                @endif
                            >
                                Segundo
                            </option>
                            <option value="Anual"
                                @if(old('disc1-semester') === 'Anual') 
                                    selected 
                                @endif
                            >
                                Anual
                            </option>
                        </select>
                    </label>
                    @error('disc1-semester')
                        <style>
                            .disc-semester input {
                                outline: 1.6px solid red;
                            }
                            .disc-semester {
                                color: red;
                            }
                        </style>
                    @enderror
                </div>
            </div>
        </div>
        <div class="disc-management"> 
            <button type="button" class="button add-disc">Adicionar outra<br> disciplina</button>
            <button type="button" class="button remove-disc">Remover<br> disciplina</button>
        </div>
    </div>


    <input type="hidden" name="takenDiscCount" id="taken-disc-count">
</fieldset>