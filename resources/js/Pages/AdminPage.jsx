import React, { useState } from 'react';
import { Button, Typography, Container, Stack } from '@mui/material';
import ManageUsers from '../Components/AdminPage/ManageUsers';
import AddRoleDialog from "../Components/AdminPage/Dialogs/AddRoleDialog";
import RequisitionsPeriodDialog from "../Components/AdminPage/Dialogs/RequisitionsPeriodDialog";
import Header from '../Components/Atoms/Header';
import ActionsMenu1 from '../Components/Atoms/ActionsMenu1';
import { AccessTimeRounded } from '@mui/icons-material';

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
    console.log(users);
    let actionsParams = {};

    const [addRoleOpen, setAddRoleOpen] = useState(false);
    actionsParams.handleOpenAddRole = () => setAddRoleOpen(true);
    const handleCloseAddRole = () => setAddRoleOpen(false);

    const [requisitionPeriodOpen, setRequisitionPeriodOpen] = useState(false);
    actionsParams.handleOpenRequisitionPeriod = () => setRequisitionPeriodOpen(true);
    const handleCloseRequisitionPeriod = () => setRequisitionPeriodOpen(false);

    return (
        <Stack 
            direction='column'
            sx={{
                justifyContent: 'space-around',
                alignItems: 'center',
                width: '100%',
                paddingBottom: 20
            }}
        >
            <Header 
                actionsParams={actionsParams}
                showRoleSelector={false}
                label="Administração do sistema"
                isExit={false}
            />
            <Stack
                direction='column'
                spacing={4}
                sx={{
                    alignItems: 'top',
                    justifyContent: 'center',
                    width: '86%',
                    paddingTop: 4
                }} 
            >    
                <ActionsMenu1 
                    selectedActions={[['requisition_period', 'add_role']]}
                    params={actionsParams}
                />
                
                <AddRoleDialog open={addRoleOpen} handleClose={handleCloseAddRole}/>
                <RequisitionsPeriodDialog requisitionSubmissionIsOpen={false} requisitionEditionIsOpen={true} open={requisitionPeriodOpen} handleClose={handleCloseRequisitionPeriod}/>
                
                <ManageUsers users={users} />
            </Stack>
        </Stack>
    );
};

export default AdminPage;