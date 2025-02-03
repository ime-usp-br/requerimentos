import React from 'react';
import { Stack, Box } from '@mui/material';
import { route } from 'ziggy-js';

import List from '../Components/RequisitionList/List';
import Header from '../Components/RequisitionList/Header';

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

export default function StudentList({ requisitions, selectedColumns, roleId, requisitionPeriodStatus }) {
    console.log(requisitionPeriodStatus)
    return (
        <Stack 
            direction='column'
            spacing={2}
            sx={{
                justifyContent: 'space-around',
                alignItems: 'center',
                width: '100%'
            }}
        >
            <Header roleId={roleId} requisitionPeriodStatus={requisitionPeriodStatus} />
            <Box
                sx={{
                    width: '90%',
                    mx: 100
                }}
            >
                <List requisitions={requisitions} selectedColumns={selectedColumns} />
            </Box>
        </Stack>
    );
};