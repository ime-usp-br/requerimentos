import React from 'react';
import { Stack } from '@mui/material';
import { DatePicker, LocalizationProvider } from '@mui/x-date-pickers';
import { AdapterDayjs } from '@mui/x-date-pickers/AdapterDayjs';

import ComboBox from '../../ui/ComboBox';

function ExportRequisitionFilters({ options, setData }) {
    const comboStyle = {
        width: '100%'
    };
    const [startDate, setStartDate] = React.useState(null);
    const [endDate, setEndDate] = React.useState(null);

    return (
        <Stack
            direction="row"
            spacing={2}
            sx={{
                width: '100%'
            }}
        >
            <ComboBox
                options={options.internal_statusOptions}
                defaultValue={options.internal_statusOptions[0]}
                sx={comboStyle}
                name="Situação"
                onChange={(value) => setData('internal_status', value)}
            />
            <ComboBox
                options={options.departments}
                defaultValue={options.departments[0]}
                sx={comboStyle}
                name="Departamento"
                onChange={(value) => setData('department', value)}
            />
            <ComboBox
                options={options.discTypes}
                defaultValue={options.discTypes[0]}
                sx={comboStyle}
                name="Tipo de Disciplina"
                onChange={(value) => setData('requested_disc_type', value)}
            />
            <LocalizationProvider dateAdapter={AdapterDayjs}>
                <DatePicker
                    label="Data Inicial"
                    value={startDate}
                    onChange={(newValue) => {
                        setStartDate(newValue);
                        setData('start_date', newValue?.$d ? newValue.$d.toISOString().slice(0, 10) : '');
                    }}
                    format="DD/MM/YYYY"
                    sx={comboStyle}
                />
            </LocalizationProvider>
            <LocalizationProvider dateAdapter={AdapterDayjs}>
                <DatePicker
                    label="Data Final"
                    value={endDate}
                    onChange={(newValue) => {
                        setEndDate(newValue);
                        setData('end_date', newValue?.$d ? newValue.$d.toISOString().slice(0, 10) : '');
                    }}
                    format="DD/MM/YYYY"
                    sx={comboStyle}
                />
            </LocalizationProvider>
        </Stack>
    );
}

export default ExportRequisitionFilters;