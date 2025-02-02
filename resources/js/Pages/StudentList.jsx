import React from 'react';
import { Stack, Typography, Button, Box } from '@mui/material';
import { route } from 'ziggy-js';

import List from '../Components/RequisitionList/List';

// import '../../../public/css/global.css';
// import '../../../public/css/pages/home.css';
// import '../../../public/css/components/footer.css';
// import '../../../public/css/components/overlay.css';
// import '../../../public/css/pages/student/list.css';

/*
    SG: criação, id, aluno, nusp, situação, departamento
    Dep: id, criação, ultima modificação, aluno, nusp, situação
    Aluno: id, criação, requerida, situação
*/

export default function StudentList({ requisitions, selectedColumns, requisition_period_status }) {
    return (
        <Stack 
            direction='column'
            sx={{
                justifyContent: 'space-around',
                alignItems: 'center',
                width: '100%'
            }}
        >
            <Stack 
                direction={{ sm: 'column', md: 'row' }}
                sx={{
                    justifyContent: "space-between",
                    alignItems: "center",
                    height: 200,
                    width: 1500
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
                    <Button 
                        variant="contained" 
                        size="medium" 
                        color="primary" 
                        href={'/'}
                        sx={{
                            textAlign: 'center',
                            width: 220,
                            height: 70
                        }}  
                    >
                        Criar Requerimento
                    </Button>
                    <Button 
                        variant="contained" 
                        size="medium" 
                        color="primary" 
                        href={'/'}
                        sx={{
                            width: 220,
                            height: 70
                        }}
                    >
                        Sair
                    </Button>
                </Stack>
            </Stack>
            <Box
                sx={{
                    width: 1500
                }}
            >
                <List requisitions={requisitions} selectedColumns={selectedColumns} />
            </Box>
        </Stack>
    );
};