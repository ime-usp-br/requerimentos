import React from 'react';
import LoginIcon from '@mui/icons-material/Login';

export default function Home() {
    return (
        <div className='home-body'>
            <header className='home-header'>
                <img className='archimedes' src="https://requerimentos.ime.usp.br/img/home/ime-logo-title.svg" />
                <a href="login" class="home-button"><LoginIcon /> Acessar</a>
            </header>

            <footer className='home-footer'>
                <div class="home-footer-content">
                    <img class="ime-logo" src="img/footer/ime-logo-footer.svg" />
                    <img class="usp-logo" src="img/footer/usp-logo.svg" />
                </div>
            </footer>
        </div>
    );
};