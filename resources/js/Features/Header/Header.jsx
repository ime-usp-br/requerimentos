import React from 'react';
import Typography from '@mui/material/Typography';
import { Stack } from '@mui/material';
import HeaderActions from "./HeaderActions"

export default function Header({
    label,
    showRoleSelector,
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
                zIndex: 5,
            }}
        >
            <Stack
                direction={{ xs: 'column', sm: 'row' }}
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
                    {label}
                </Typography>
                <HeaderActions
                    showRoleSelector={showRoleSelector}
                    isExit={isExit}
                    actionsParams={actionsParams}
                />
            </Stack>
        </Stack>
    );
}