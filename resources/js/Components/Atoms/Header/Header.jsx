import React from 'react'
import { Stack,Typography } from '@mui/material';

import HeaderActions from './HeaderActions';

export default function Header({ roleId, useRoles, userRoles }) {
    return (
        <Stack
            direction='row'
            spacing={{ xs: 2, sm: 0 }}
            sx={{
                justifyContent: { xs: 'space-evenly', sm: 'space-between' },
                alignItems: "center",
                width: '90%',
                height: { xs: 'auto', sm: 140 }
            }}
        >
            <Typography variant="h4" content="h1">Requerimentos</Typography>
            <HeaderActions roleId={roleId} useRoles={useRoles} userRoles={userRoles} />
        </Stack>
    );
};