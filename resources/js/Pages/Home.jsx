import React from 'react';
import LoginIcon from '@mui/icons-material/Login';
import { Stack, Button, Container } from '@mui/material';

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
                direction={{ xs: 'column', sm: 'row' }}
                spacing={{ xs: 3 }}
                sx={{
                    alignItems: "center",
                    justifyContent: "space-between",
                    width: '90%',
                    height: { xs: 'auto', sm: 160 },
                    paddingTop: 3
                }}
            >
                <Container
                    sx={{
                        height: 'auto',
                        width: { xs: 320, sm: 460 }
                    }}
                >
                    <img
                        style={{
                            width: '100%'
                        }}
                        src="https://requerimentos.ime.usp.br/img/home/ime-logo-title.svg"
                    />
                </Container>
                <Button
                    variant="contained"
                    size="large"
                    color="primary"
                    href="login"
                    style={{ textAlign: 'center' }}
                    startIcon={<LoginIcon />}
                >
                    Acessar
                </Button>
            </Stack>

            <Stack
                direction='row'
                sx={{
                    alignItems: "center",
                    justifyContent: "center",
                    width: '100%',
                    height: { xs: 100, sm: 140 },
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
                    <Container
                        disableGutters
                        sx={{
                            alignContent: 'center',
                            height: 'auto',
                            width: { xs: 200, sm: 460 },
                            margin: 0
                        }}
                    >
                        <img
                            style={{
                                width: '100%'
                            }}
                            src="img/footer/ime-logo-footer.svg"
                        />
                    </Container>
                    <Container
                        disableGutters
                        sx={{
                            alignContent: 'center',
                            height: 'auto',
                            width: { xs: 110, sm: 170 },
                            margin: 0
                        }}
                    >
                        <img
                            style={{
                                width: '100%'
                            }}
                            src="img/footer/usp-logo.svg"
                        />
                    </Container>
                </Stack>
            </Stack>
        </Stack>
    );
};