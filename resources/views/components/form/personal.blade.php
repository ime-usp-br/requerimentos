<fieldset class="personal">
    <legend>Dados pessoais</legend>
    <div class="name-wrapper">
        <label class="name" >
            Nome completo
            <!-- <input type="text" name="name" value='{{ $req->student_name ?? null}}' required {{ $readOnly ? 'readonly' : '' }}> -->
            <input type="text" name="name" class="large-field" value='{{ $req->student_name ?? null}}' required {{ $readOnly ? 'readonly' : '' }}>
        </label>
        @error('name')
            <style>
                .name input {
                    outline: 1.6px solid red;
                }
                .name {
                    color: red;
                }
                .name-wrapper {
                    gap: 0.3rem;
                }
            </style>
            <!-- <p class="error-message">{{ $message }}</p> -->
        @enderror
        <label class="email">
            Email
            <input type="email" name="email" class="large-field" value='{{ $req->email ?? null }}' required {{ $readOnly ? 'readonly' : '' }}>
        </label>
        @error('email')
            <style>
                .email input {
                    outline: 1.6px solid red;
                }
                .email {
                    color: red;
                }
            </style>
            <!-- <p class="error-message">{{ $message }}</p> -->
        @enderror
    </div>
    
    <div class="nusp-email-wrapper">
        <label class="nusp">
            Número USP
            <input type="text" name="nusp" value='{{ $req->student_nusp ?? null}}' required {{ $readOnly ? 'readonly' : '' }}>
            
        </label>
        @error('nusp')
            <style>
                .nusp input {
                    outline: 1.6px solid red;
                }
                .nusp {
                    color: red;
                }
            </style>
            <!-- <p class="error-message">{{ $message }}</p> -->
        @enderror
        {{-- @if ($withRecordButton)
          <a href="#" class="button">Histórico do aluno</a>  
        @endif --}}
    </div>
</fieldset>