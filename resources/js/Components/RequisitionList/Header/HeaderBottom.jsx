import React from 'react'
import { Stack, Button, Tooltip } from '@mui/material';

export default function HeaderBottom({ roleId, requisitionPeriodStatus }) {
    const buttonStyle = {
        height: 56,
        px: 4
    }

    return (
        <Stack 
            direction={{ sm: 'column', md: 'row' }}
            spacing={2}
            >
            { (roleId != 1) && (roleId != 3) &&
                <Button 
                    variant="contained" 
                    size="medium" 
                    color="primary" 
                    href={'/'}
                    sx={buttonStyle}
                >
                    Administrar Sistema
                </Button>
            }
            
            { roleId < 3 &&
                <Tooltip 
                    title="Disponível durante o período de requerimentos"
                    disableHoverListener={requisitionPeriodStatus || roleId == 2}
                >
                    <span>
                        <Button 
                            variant="contained"
                            disabled={!requisitionPeriodStatus && roleId != 2}
                            size="medium" 
                            color="primary" 
                            href={'/'}
                            sx={buttonStyle}
                        >
                            Criar Requerimento
                        </Button>
                    </span>
                </Tooltip>
            }   

            { roleId == 2 &&
                <Button 
                    variant="contained" 
                    size="medium" 
                    color="primary" 
                    href={'/'}
                    sx={buttonStyle}
                >
                    Filtros
                </Button>
            }
        </Stack>
    );
};