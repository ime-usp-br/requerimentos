import React from 'react'
import { Stack, Typography, Button, Autocomplete, TextField } from '@mui/material';

export default function HeaderTop({ roleId }) {
    const buttonStyle = {
        height: 56,
        px: 4
    }

    return (
        <Stack 
            direction={{ sm: 'column', md: 'row' }}
            sx={{
                justifyContent: "space-between",
                alignItems: "center",
                width: '100%'
            }}
            >
            <Typography variant="h4" content="h1">Requerimentos</Typography>
            <Stack 
                direction={{ sm: 'column', md: 'row' }}
                sx={{
                    justifyContent: "space-around",
                    alignItems: "center",
                }}
                spacing={2}
                >
                {/* <Button 
                    variant="contained" 
                    size="medium" 
                    color="primary" 
                    href={'/'}
                >
                    Criar Requerimento
                </Button> */}
                { roleId > 1 ? 
                    <Autocomplete 
                        disablePortal
                        options={['Serviço de Graduação',
                                'Parecerista',
                                'Secretaria do MAC',
                                'Secretaria do MAE',
                                'Secretaria do MAP',
                                'Secretaria do MAT',
                                'Secretaria Virtual']}
                        sx={{ 
                            width: 250
                        }}
                        renderInput={(params) => <TextField {...params} label='Papel' />}
                    /> : null 
                }
                
                <Button 
                    variant="contained" 
                    size="large"
                    color="primary" 
                    href={'/'}
                    sx={buttonStyle}
                >
                    Sair
                </Button>
            </Stack>
        </Stack>
    );
};