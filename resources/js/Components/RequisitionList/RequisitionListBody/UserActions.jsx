import React from 'react'
import { Stack, Button, Tooltip } from '@mui/material';

export default function UserActions({ roleId, requisitionPeriodStatus }) {
    return (
        <Stack 
            direction={{ xs: 'column', sm: 'row' }}
            spacing={{ xs: 1, sm: 2 }}
            sx={{ width: '100%' }}
        >
            { (roleId == 2 || roleId == 4) &&
                <Button 
                    variant="contained" 
                    size="large"
                    color="primary" 
                    href={'/'}
                >
                    Administrar Sistema
                </Button>
            }
            
            { (roleId == 1 || roleId == 2) &&
                <Tooltip 
                    title="Disponível durante o período de requerimentos"
                    disableHoverListener={requisitionPeriodStatus || roleId == 2}
                >
                    <span>
                        <Button 
                            variant="contained"
                            disabled={!requisitionPeriodStatus && roleId != 2}
                            size="large"
                            color="primary" 
                            href={route('newRequisition.get')}
                            sx={{ width: '100%' }}
                        >
                            Criar Requerimento
                        </Button>
                    </span>
                </Tooltip>
            }   

            { roleId == 2 &&
                <Button 
                    variant="contained" 
                    size="large"
                    color="primary" 
                    href="#" //{route('pages.requisitions.filters')}
                >
                    Exportar
                </Button>
            }
        </Stack>
    );
};