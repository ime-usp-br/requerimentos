import React, { useState } from 'react';
import { Stack } from '@mui/material';
import { DateField, LocalizationProvider } from '@mui/x-date-pickers';
import { AdapterDayjs } from '@mui/x-date-pickers/AdapterDayjs';

import ComboBox from '../../Atoms/ComboBox';

export default function Filters({ options, filterRef }) {
    const comboStyle = {
        width: '100%'
    }

    return (
        <Stack
            direction='column'
            spacing={2}
            sx={{
                justifyContent: 'space-between',
                alignItems: 'center',
                width: '100%'
            }}
        >
            <ComboBox
                options={options.internal_statusOptions}
                size='small'
                defaultValue={options.internal_statusOptions[0]}
                sx={comboStyle}
                name='Situação'
                onChange={(value) => filterRef.current.internal_status = value}
            />
            <ComboBox
                options={options.departments}
                size='small'
                defaultValue={options.departments[0]}
                sx={comboStyle}
                name='Departamento'
                onChange={(value) => filterRef.current.department = value}
            />
            <ComboBox
                options={options.discTypes}
                size='small'
                defaultValue={options.discTypes[0]}
                sx={comboStyle}
                name='Tipo de Disciplina'
                onChange={(value) => filterRef.current.requested_disc_type = value}
            />
            <LocalizationProvider dateAdapter={AdapterDayjs}>
                <DateField 
                    label="Data Inicial" 
                    size='small' 
                    sx={comboStyle} 
                    onChange={(value) => filterRef.current.start_date = value['$d'].toISOString().slice(0, 10)}
                    format="DD/MM/YYYY"
                />
            </LocalizationProvider>
            <LocalizationProvider dateAdapter={AdapterDayjs}>
                <DateField 
                    label="Data Final" 
                    size='small'
                    sx={comboStyle} 
                    onChange={(value) => filterRef.current.end_date = value['$d'].toISOString().slice(0, 10)}
                    format="DD/MM/YYYY"
                />
            </LocalizationProvider>
        </Stack>
    );
};