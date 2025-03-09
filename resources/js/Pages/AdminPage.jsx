import React, { useState } from 'react';
import { Button, Typography, Container, Stack } from '@mui/material';
import ManageUsers from '../Components/AdminPage/ManageUsers';
import AddRoleDialog from "../Components/AdminPage/Dialogs/AddRoleDialog";
import RequisitionsPeriodDialog from "../Components/AdminPage/Dialogs/RequisitionsPeriodDialog";


// Fake users object for testing
const fakeUsers = [
    {
        id: 1,
        name: 'John Doe',
        codpes: '123456',
        roles: [
            { name: 'Parecerista' },
            { name: 'Serviço de Graduação' }
        ]
    },
    {
        id: 2,
        name: 'Jane Smith',
        codpes: '654321',
        roles: [
            { name: 'Secretaria de Departamento' }
        ]
    }
];

const AdminPage = ({ users = fakeUsers, requisition_period_status = false }) => {
    const [addRoleOpen, setAddRoleOpen] = useState(false);
    const handleOpenAddRole = () => setAddRoleOpen(true);
    const handleCloseAddRole = () => setAddRoleOpen(false);

    const [requisitionPeriodOpen, setRequisitionPeriodOpen] = useState(false);
    const handleOpenRequisitionPeriod = () => setRequisitionPeriodOpen(true);
    const handleCloseRequisitionPeriod = () => setRequisitionPeriodOpen(false);

    return (
        <Stack spacing={2} sx={{alignItems: "center"}}>
            <header>
                <Typography component="h1" variant="h4">Administração do sistema</Typography>
                <Button variant="contained" href={route('list')}>Voltar</Button>
            </header>
            <AddRoleDialog open={addRoleOpen} handleClose={handleCloseAddRole}/>
            <RequisitionsPeriodDialog requisitionSubmissionIsOpen={false} requisitionEditionIsOpen={true} open={requisitionPeriodOpen} handleClose={handleCloseRequisitionPeriod}/>
            <Stack spacing={2} direction="row">
                <Button variant="contained" color="primary" onClick={handleOpenRequisitionPeriod}>Período de requerimentos</Button>
                <Button variant="contained" color="primary" onClick={handleOpenAddRole}>Adicionar um papel</Button>
            </Stack>
            <Container>
                <ManageUsers users={users} />
            </Container>
        </Stack>
    );
};

export default AdminPage;