 <fieldset class="disciplines">
    <legend>Disciplinas</legend>
    
    <div class="taken">
        <div class="disc-title" >Disciplinas cursadas</div>
        <p class="instruction">Adicione aqui as disciplinas cursadas a serem aproveitadas</p>
        <div class="disc-list">
            <div class="disc">
                <label class="disc-name">
                    Nome: 
                    <input type="text" name="disc1-name" id="disc-name" required>
                </label>
                @error('disc1-name')
                    <style>
                        .disc-name input {
                            outline: 1.6px solid red;
                        }
                        .taken-disc-name {
                            color: red;
                        }
                    </style>
                @enderror
                <label class="disc-institution">
                    Instituição em que foi cursada:
                    <input type="text" name="disc1-institution" id="disc-institution" required>
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
                        <input type="text" name="disc1-code" id="disc-code">
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
                        Ano: 
                        <input type="text" name="disc1-year" id="disc-year" required>
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
                        Nota: 
                        <input type="text" name="disc1-grade" id="disc-grade" required>
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
                        Semestre:
                        <select name="disc1-semester" id="disc-semester" required>
                            <option value="">Selecione o semestre</option>
                            <option value="Primeiro">Primeiro</option>
                            <option value="Segundo">Segundo</option>
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
            <button type="button" class="button add-disc">Adicionar<br> disciplina</button>
            <button type="button" class="button remove-disc">Remover<br> disciplina</button>
        </div>
    </div>

    <div class="requested">
        <div class="disc-title">Disciplina requerida</div>
        <p class="instruction">Adicione aqui as informações da disciplina a ser adicionada ao seu histórico</p>
        <!-- <div class="disc-list">
            <p class="empty-list-message">Adicione aqui as disciplinas a serem adicionadas ao seu histórico.</p>
        </div>
        <div class="disc-management"> 
            <button type="button" class="button add-disc">Adicionar disciplina</button>
            <button type="button" class="button remove-disc">Remover disciplina</button>
        </div> -->
        <div class="disc">
            <label class="requested-disc-name">
                Nome: 
                <input type="text" id="disc-name" name="requested-disc-name" required>
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
                    Tipo:
                    <select name="requested-disc-type" id="disc-type" required>
                        <option value="">Selecione o tipo</option>
                        <option value="Extracurricular">Extracurricular</option>
                        <option value="Obrigatória">Obrigatória</option><option value="Optativa Eletiva">Optativa Eletiva</option>
                        <option value="Optativa Livre">Optativa Livre</option>
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
                    <input type="text" name="requested-disc-code" id="disc-code" required>
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
                Departamento:
                <select name="disc-department" required>
                    <option value="">Selecione o departamento</option>
                    <option value="MAC">MAC</option>
                    <option value="MAE">MAE</option>
                    <option value="MAP">MAP</option>
                    <option value="MAT">MAT</option>
                    <option value="Disciplina de fora do IME">Disciplina de fora do IME</option>
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

    <input type="hidden" name="takenDiscCount" id="taken-disc-count">
</fieldset>