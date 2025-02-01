import React from 'react';

import '../../../public/css/global.css';
import '../../../public/css/pages/home.css';
import '../../../public/css/components/footer.css';
import '../../../public/css/components/overlay.css';
import '../../../public/css/components/table.css';

export default function Home() {
    return (
        <>
            <header>
                <img src="https://requerimentos.ime.usp.br/img/home/ime-logo-title.svg" />
                <a href="login" class="button">⎆ Acessar</a>
            </header>

            <footer>
                <div class="footer-content">
                    <img class="ime-logo" src="img/footer/ime-logo-footer.svg" />
                    <img class="usp-logo" src="img/footer/usp-logo.svg" />
                </div>
            </footer>
        </>
    );
};