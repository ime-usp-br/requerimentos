import React from 'react';
import { Stack, Typography } from '@mui/material';

import Header from '../Components/RequisitionList/Header';
import ExportBody from '../Components/ExportRequisitions/ExportBody';

export default function ExportRequisitions({ roleId, userRoles, options }) {
    return (
        <Stack 
            spacing={3}
            sx={{
                alignItems: 'center',
                width: '100%',
            }} 
        >
            <Header roleId={roleId} userRoles={userRoles} />
            <ExportBody options={options} />
        </Stack>
    );
};