import React from 'react'
import { Stack, Typography, Button, Autocomplete, TextField } from '@mui/material';

import HeaderTop from './Header/HeaderTop';
import HeaderBottom from './Header/HeaderBottom';

export default function Header({ roleId, requisitionPeriodStatus }) {
    return (
        <Stack 
            direction={{ sm: 'row', md: 'column' }}
            sx={{
                justifyContent: "space-around",
                alignItems: "left",
                height: 200,
                width: '90%'
            }}
            >
            <HeaderTop roleId={roleId} />
            <HeaderBottom roleId={roleId} requisitionPeriodStatus={requisitionPeriodStatus} />
        </Stack>
    );
};