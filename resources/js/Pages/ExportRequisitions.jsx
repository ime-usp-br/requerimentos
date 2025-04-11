import React from 'react';
import { Stack, Button } from '@mui/material';

import Header from '../Components/Header/Header';
import Filters from "../Components/ExportRequisitions/Filters";
import { useForm } from '@inertiajs/react';
import axios from 'axios';


export default function ExportRequisitions({ label, roleId, userRoles, options }) {
    const { data, setData } = useForm({
        internal_status: options.internal_statusOptions[0],
        department: options.departments[0],
        requested_disc_type: options.discTypes[0],
        start_date: null,
        end_date: null
    });

    const handleExport = async () => {
        try {
            const response = await axios.post(route('exportRequisitionsPost'), data, {
                responseType: 'blob',
            });
    
            const url = window.URL.createObjectURL(new Blob([response.data]));
            const link = document.createElement('a');
            link.href = url;
            link.setAttribute('download', 'requisitions_export.csv');
            document.body.appendChild(link);
            link.click();
            link.remove();
        } catch (error) {
            console.error('Export failed', error);
        }
    };

    return (
        <Stack 
            spacing={3}
            sx={{
                alignItems: 'center',
                width: '100%',
            }} 
        >
            <Header label={label} roleId={roleId} userRoles={userRoles} />
            <Stack
                spacing={5}
                sx={{
                    alignItems: { xs: 'left', sm: 'baseline' },
                    width: '86%'
                }}
            >
                <Filters options={options} setData={setData} />
                <Button
                    variant="contained"
                    size="large"
                    color="primary"
                    onClick={handleExport}
                >
                    Exportar
                </Button>
            </Stack>        
        </Stack>
    );
};