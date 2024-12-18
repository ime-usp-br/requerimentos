<fieldset class="course">
    <legend>Curso</legend>
    <div class="course-wrapper">
        @if ($readOnly)
            <input type="text" class="large-field" value="{{ $req->course ?? '' }}" readonly>
            <!-- <input type="text" value="{{ $req->course ?? '' }}" readonly> -->
        @else
            <label class="select" >
                Nome
                <select name="course" required>
                    <option value="">Selecione o seu curso atual</option>
                    <option 
                        value="Bacharelado em Ciência da Computação"
                        @if(isset($req) && $req->course === 'Bacharelado em Ciência da Computação') 
                            selected 
                        @elseif (old('course') === 'Bacharelado em Ciência da Computação')
                            selected
                        @endif
                        
                        >
                        Bacharelado em Ciência da Computação
                    </option>
                    <option 
                        value="Bacharelado em Estatística"
                        @if(isset($req) && $req->course === 'Bacharelado em Estatística') 
                            selected
                        @elseif (old('course') === 'Bacharelado em Estatística')
                            selected 
                        @endif
                        >
                        Bacharelado em Estatística
                    </option>
                    <option 
                        value="Bacharelado em Matemática"
                        @if(isset($req) && $req->course === 'Bacharelado em Matemática') 
                            selected 
                        @elseif (old('course') === 'Bacharelado em Matemática')
                            selected 
                        @endif
                        >
                        Bacharelado em Matemática
                    </option>
                    <option 
                        value="Bacharelado em Matemática Aplicada"
                        @if(isset($req) && $req->course === 'Bacharelado em Matemática Aplicada') 
                            selected 
                        @elseif (old('course') === 'Bacharelado em Matemática Aplicada')
                            selected 
                        @endif
                        >
                        Bacharelado em Matemática Aplicada
                    </option>
                    <option 
                        value="Bacharelado em Matemática Aplicada e Computacional" class="bmac-option"
                        @if(isset($req) && $req->course === 'Bacharelado em Matemática Aplicada e Computacional') 
                            selected 
                        @elseif (old('course') === 'Bacharelado em Matemática Aplicada e Computacional')
                            selected 
                        @endif
                        >
                        Bacharelado em Matemática Aplicada e Computacional
                    </option>
                    <option 
                        value="Licenciatura em Matemática"
                        @if(isset($req) && $req->course === 'Licenciatura em Matemática') 
                            selected 
                        @elseif (old('course') === 'Licenciatura em Matemática')
                            selected 
                        @endif
                        >
                        Licenciatura em Matemática
                    </option>
                </select>
            </label>
        @endif
        
        @error('course')
            <style>
                .select select {
                    outline: 1.6px solid red;
                }
                .select {
                    color: red;
                }
            </style>
            <!-- <p class="error-message">{{ $message }}</p> -->
        @enderror
    </div>
</fieldset>