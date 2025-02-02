import React from 'react';

const RequistionDetails = () => {

    return (
        <fieldset class="personal">
            <legend>Dados pessoais</legend>
            <div class="name-wrapper">
                <label class="name" >
                    Nome completo
                    <!-- <input type="text" name="name" value='{{ $req->student_name ?? null}}' required {{ $readOnly ? 'readonly' : '' }}> -->
                    <input type="text" name="name" class="large-field" value='{{ $req->student_name ?? null}}' required {{ $readOnly ? 'readonly' : '' }}>
                </label>
                <label class="email">
                    Email
                    <input type="email" name="email" class="large-field" value='{{ $req->email ?? null }}' required {{ $readOnly ? 'readonly' : '' }}>
                </label>
            </div>
            
            <div class="nusp-email-wrapper">
                <label class="nusp">
                    Número USP
                    <input type="text" name="nusp" value='{{ $req->student_nusp ?? null}}' required {{ $readOnly ? 'readonly' : '' }}>
                    
                </label>
            </div>
        </fieldset>
    );
};

export default RequistionDetails;