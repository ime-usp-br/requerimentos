import React from 'react';
import { Stack, Typography, Button, Link } from '@mui/material';
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
        <div className='student-body'>
            <Stack direction="row"
                sx={{
                    justifyContent: "space-around",
                    alignItems: "center",
                }}
                >
                <Typography variant="h4" content="h1">Requerimentos</Typography>
                <Button variant="contained" size="medium" color="primary" href={'/'}>
                    Voltar
                </Button>
            </Stack>
            <div className='student-content'>
                <List requisitions={requisitions} selectedColumns={selectedColumns} />
            </div>
        </div>
    );
};