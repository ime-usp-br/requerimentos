import React from 'react';
import { Typography, Stack, Button, Alert} from '@mui/material';


const Header = () => {
    return (
        <Stack direction="row" spacing={2}
            sx={{
                justifyContent: "space-between",
                alignItems: "center",
            }}
            >
            <Typography variant="h4" content="h1">Novo requerimento</Typography>
            <Button variant="contained" size="medium" color="primary" href={route('sg.list')}>
                Voltar
            </Button>
        </Stack>
    )
}

export default Header;

