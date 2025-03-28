import React from 'react'
import { Stack, Typography } from '@mui/material';

import HeaderActions from './HeaderActions';

export default function HeaderTop({ label, 
                                    roleId, 
                                    useRoles,
                                    userRoles, 
                                    isExit, 
                                    actionsParams }) {
    return (
        <Stack
            direction={{ sm: 'column', md: 'row' }}
            spacing={{ xs: 2, sm: 0 }}
            sx={{
                justifyContent: { xs: 'space-evenly', sm: 'space-between' },
                alignItems: "center",
                width: '86%',
                height: { xs: 'auto' },
                paddingY: { xs: 2, sm: 4 }
            }}
        >
            <Typography 
                variant="h4" 
                content="h2"
                sx={{
                    textAlign: { xs: 'center', sm: 'left' },
                    fontSize: 36,
                    color: 'white'
                }}
            >
                { label }
            </Typography>
            <HeaderActions
                roleId={roleId} 
                useRoles={useRoles}
                userRoles={userRoles} 
                isExit={isExit}
                actionsParams={actionsParams}
            />
        </Stack>
    );
};