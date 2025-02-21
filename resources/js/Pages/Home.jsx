import React from 'react';
import LoginIcon from '@mui/icons-material/Login';
import { Stack, Button } from '@mui/material';

// import '../../../public/css/global.css';
// import '../../../public/css/pages/home.css';
// import '../../../public/css/components/footer.css';
// import '../../../public/css/components/overlay.css';
// import '../../../public/css/components/table.css';

export default function Home() {
    return (
        <Stack
            direction='column'
            sx={{
                alignItems: 'center',
                justifyContent: 'space-between',
                backgroundImage: "url('https://requerimentos.ime.usp.br/img/home/background.png')",  
                backgroundSize: 'cover',
                backgroundColor: 'black',
                width: '100%',
                height: '100vh',
            }}
        >
            <Stack 
                direction="row"
                sx={{ 
                    alignItems: "center",
                    justifyContent: "center",
                    width: '100%',
                    height: 180,
                }}
            >
                <Stack
                    direction='row'
                    sx={{ 
                        justifyContent: "space-between",
                        width: '85%',
                        height: 'auto'
                    }}
                >
                    <img style={{ transform: 'scale(1.3)' }} src="https://requerimentos.ime.usp.br/img/home/ime-logo-title.svg" />
                    <Button 
                        variant="contained" 
                        size="large"
                        color="primary" 
                        href="login"
                    >
                        <LoginIcon /> &nbsp; Acessar
                    </Button>
                </Stack>
            </Stack>

            <Stack
                direction='row'
                sx={{ 
                    alignItems: "center",
                    justifyContent: "center",
                    width: '100%',
                    height: 140,
                    backgroundColor: 'primary.main',
                }}
            >
                <Stack
                    direction='row'
                    sx={{ 
                        justifyContent: "space-between",
                        width: '90%',
                        height: 'auto'
                    }}
                >
                    <img class="ime-logo" src="img/footer/ime-logo-footer.svg" />
                    <img class="usp-logo" src="img/footer/usp-logo.svg" />
                </Stack>
            </Stack>
        </Stack>
    );
};