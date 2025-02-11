import React from 'react';
import { Stack, Button, Box } from '@mui/material';
import ComboBox from '../../ComboBox';

export default function HeaderActions({ roleId, userRoles }) {
    const handleComboBoxChange = (value) => {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        fetch(route('role.switch'), {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                'role-switch': value.id
            })
        }).then(response => {
            window.location.href = response.url;
        });
    };

    return (
        <Stack 
            direction='row'
            sx={{
                justifyContent: { sm: 'space-between', md: 'space-around' },
                alignItems: "center"
            }}
            spacing={2}
        >
            { roleId > 1 && userRoles.length > 1 && (
                <ComboBox
                    size='small'
                    options={userRoles}
                    optionGetter={(option) => option.name}
                    defaultValue={userRoles.find(val => val.id == roleId)}
                    sx={{ width: 230 }}
                    name='papel'
                    onChange={handleComboBoxChange}
                />
            )}
            <Button 
                variant="contained" 
                size="large"
                color="primary" 
                href={'/'}
            >
                Sair
            </Button>
        </Stack>
    );
};
