import React from 'react';
import Typography from '@mui/material/Typography';
import { Stack, Box, Grid2, Container } from '@mui/material';
import HeaderActions from "./HeaderActions"
import sgLogo from '../../../img/sg-logo.jpg';

export default function Header({
    label,
    showRoleSelector,
    isExit,
    selectedActions
}) {
    return (
        <Stack
            direction="row"
            spacing={2}
            sx={{
                width: '100%',
                height: '80px',
                paddingY: 1.5,
                position: "sticky",
                top: 0,
                zIndex: 5,
                backgroundColor: 'white',
            }}
        >
            <img
                height='90%'
                src={sgLogo}
                style={{
                    borderRadius: 65,
                    border: '3px solid #142C68',
                    margin: '2px 8px 0px 16px',
                }}
            />
            <Grid2
                justifyContent="center"
                container
                wrap="nowrap"
                sx={{
                    width: '100%'
                }}
            >
                <Stack
                    direction={{ xs: 'column', sm: 'row' }}
                    spacing={{ xs: 2, sm: 0 }}
                    sx={{
                        justifyContent: { xs: 'space-evenly', sm: 'space-between' },
                        alignItems: "center",
                        backgroundColor: '#D9D9D9',
                        width: '100%',
                        paddingX: 2,
                        paddingY: 1.2,
                        marginRight: 2,
                        marginY: 1.2
                    }}
                >
                    <Typography
                        variant="h6"
                        sx={{
                            textAlign: { xs: 'center', sm: 'left' },
                            fontSize: 28,
                        }}
                    >
                        Aproveitamento de Estudos
                    </Typography>
                    <HeaderActions
                        showRoleSelector={true}
                        selectedActions={selectedActions}
                        isExit={isExit}
                    />
                </Stack>
            </Grid2>
        </Stack>
    );
}
