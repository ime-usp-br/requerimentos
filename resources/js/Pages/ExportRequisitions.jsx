import React from 'react';
import { Stack } from '@mui/material';

import Header from '../Components/Atoms/Header';
import ExportBody from '../Components/ExportRequisitions/ExportBody';

export default function ExportRequisitions({ label, roleId, userRoles, options }) {
    return (
        <Stack 
            spacing={3}
            sx={{
                alignItems: 'center',
                width: '100%',
            }} 
        >
            <Header label={label} roleId={roleId} userRoles={userRoles} />
            <ExportBody options={options} />
        </Stack>
    );
};