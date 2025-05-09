import React from 'react';
import LoginIcon from '@mui/icons-material/Login';
import { Stack, Button, Container, Typography } from '@mui/material';
import home from '../../img/home.jpg';
import imeLogo from '../../img/ime-logo.svg';
import uspLogo from '../../img/usp-logo.svg';

export default function Home() {
    return (
        <Stack
            direction='column'
            sx={{
                alignItems: 'center',
                justifyContent: 'center',
                backgroundImage: `url(${home})`,
                backgroundSize: 'cover',
                backgroundPosition: 'center',
                backgroundRepeat: 'no-repeat',
                backgroundColor: 'black',
                width: '100%',
                height: '100vh',
            }}
        >
            <Stack
                direction='row'
                sx={{
                    alignItems: "center",
                    justifyContent: "space-between",
                    width: '100%',
                    height: { xs: 20, sm: 60 },
                    backgroundColor: 'primary.main',
                    position: 'absolute',
                    top: 0,
                }}
            >
                <img
                    style={{
                        height: '70%',
                        marginLeft: '16px',
                    }}
                    src={imeLogo}
                />
                <img
                    style={{
                        height: '70%',
                        marginRight: '16px',
                    }}
                    src={uspLogo}
                />
            </Stack>

            <Stack
                direction='column'
                spacing={3}
                sx={{
                    alignItems: "flex-start",
                    justifyContent: "center",
                    backgroundColor: 'rgb(255, 255, 255)',
                    padding: 4,
                    borderRadius: 2,
                    boxShadow: 3,
                    textAlign: 'left',
                }}
            >
                <Typography variant="h3">
                    Aproveitamento de Estudos
                </Typography>
                <Typography variant="subtitle1">
                    Entre para realizar seus requerimentos de aproveitamento de estudo.
                </Typography>

                <Button
                    variant="contained"
                    size="large"
                    color="primary"
                    href="login"
                    startIcon={<LoginIcon />}
                >
                    Acessar
                </Button>
            </Stack>
        </Stack>
    );
};