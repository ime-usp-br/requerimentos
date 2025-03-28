import React from 'react';
import { Stack } from '@mui/material';
import HeaderTop from './Header/HeaderTop';

export default function Header({ label,
                                 roleId,
                                 showRoleSelector,
                                 userRoles,
                                 selectedActions,
                                 actionsParams,
                                 isExit }) {
    return (
        <Stack 
            direction='column'
            spacing={{ xs: 3, sm: 0 }}
            sx={{
                justifyContent: 'space-around',
                alignItems: 'center',
                width: '100%',
                backgroundColor: 'primary.main',
                position: "sticky",
                top: 0,
                zIndex: 5
            }}
        >
            <HeaderTop 
                label={label} 
                roleId={roleId} 
                showRoleSelector={showRoleSelector}
                userRoles={userRoles} 
                selectedActions={selectedActions}
                actionsParams={actionsParams}
                isExit={isExit}
            />
        </Stack>
    );
}