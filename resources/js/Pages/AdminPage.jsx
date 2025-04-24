import React from 'react';
import { Stack } from '@mui/material';
import ManageUsers from '../Components/AdminPage/ManageUsers';
import Header from '../Components/Header/Header';
import ActionsMenuBar from '../Components/Atoms/ActionsMenuBar';

function AdminPage({ users }){
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
                <ActionsMenuBar 
                    selectedActions={[['requisition_period', 'add_role']]}
                />
                
                <ManageUsers users={users} />
            </Stack>
        </Stack>
    );
};

export default AdminPage;