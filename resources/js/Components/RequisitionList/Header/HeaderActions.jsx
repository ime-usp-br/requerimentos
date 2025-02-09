import React, { useRef } from 'react'
import { Stack, Button, Box } from '@mui/material';

import ComboBox from '../../ComboBox';

export default function HeaderActions({ roleId, userRoles }) {
    const formRef = useRef(null);

    return (
        <Stack 
            direction='row'
            sx={{
                justifyContent: { sm: 'space-between', md: 'space-around' },
                alignItems: "center"
            }}
            spacing={2}
            >
            
            { roleId > 1 && userRoles.length > 1 ? 
                <Box
                    component="form"
                    ref={formRef}
                    onSubmit={(e) => {
                    e.preventDefault();
                    const formData = new FormData(e.target);
                    const data = Object.fromEntries(formData.entries());
                    console.log("Form submitted: ", data);
                    }}    
                >
                    <ComboBox
                        size='small'
                        options={userRoles}
                        optionGetter={(option) => option.name}
                        defaultValue={userRoles.find(val => val.id == roleId)}
                        sx={{ 
                            width: 230
                        }}
                        name='papel'
                    />
                </Box>
                : null 
            }
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
