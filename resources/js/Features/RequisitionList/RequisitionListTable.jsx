import React from 'react';
import { useMemo, useState, useEffect } from 'react';
import { MaterialReactTable, useMaterialReactTable, MRT_GlobalFilterTextField, MRT_ToggleFiltersButton } from 'material-react-table';

// Make sure the imports are correct
import PageviewIcon from '@mui/icons-material/Pageview';
import { Link, Box, Button, TextField, InputAdornment } from '@mui/material';
import SearchIcon from '@mui/icons-material/Search';

import Builder from '../../ui/ComponentBuilder/Builder';
import columnTypes from "../../ui/ComponentBuilder/TableColumnTypes";

function List({ requisitions, selectedColumns }) {
    let textStyle = {
        //simple styling with the `sx` prop, works just like a style prop in this example
        sx: {
            fontSize: 18,
        },
    };
    let builder = new Builder(columnTypes);
    let columns = useMemo(
        () => builder.build(selectedColumns),
        [selectedColumns],
    );

    const [columnFilters, setColumnFilters] = useState(() => {
        return JSON.parse(sessionStorage.getItem('filters')) || [];
    });
    useEffect(() => {
        sessionStorage.setItem('filters', JSON.stringify(columnFilters));
    }, [columnFilters]);


    const [globalFilter, setGlobalFilter] = useState(() => {
        return sessionStorage.getItem('globalFilter') || '';
    });
    useEffect(() => {
        sessionStorage.setItem('globalFilter', globalFilter);
    }, [globalFilter]);


    let data = requisitions;
    const table = useMaterialReactTable({
        columns,
        data,
        enableSorting: true,
        enableDensityToggle: false,
        enableFullScreenToggle: false,
        enableHiding: false,          // This disables column hiding functionality
        enableColumnDragging: false,
        enableFilters: true,
        enableColumnFilters: true,
        enableTopToolbar: true,
        enableColumnOrdering: true,
        enableGlobalFilter: true,
        enableRowActions: true,
        muiTableHeadCellProps: textStyle,
        muiTableBodyCellProps: textStyle,
        displayColumnDefOptions: {
            'mrt-row-actions': {
                header: null,
                size: 80,
            },
        },
        renderRowActions: ({ row }) => (
            <Box display="flex" alignItems="center" justifyContent="center" height="100%">
                <Link href={route('showRequisition', { requisitionId: row.original.id })} underline='never' color='textDisabled' display="flex" alignItems="center" justifyContent="center">
                    <PageviewIcon fontSize="large" />
                </Link>
            </Box>
        ),
        state: { 
            columnFilters,
            globalFilter,
            density: 'compact',
        },
        onGlobalFilterChange: setGlobalFilter,
        onColumnFiltersChange: setColumnFilters,
        
        // Updated toolbar with functioning search box
        renderTopToolbar: ({ table }) => (
            <Box
                sx={{
                    display: 'flex',
                    gap: '0.5rem',
                    p: '8px',
                    justifyContent: 'flex-start',
                    alignItems: 'center',
                }}
            >
                {/* Global filter textbox */}
                <TextField
                    placeholder="Buscar por tudo..."
                    value={globalFilter ?? ''}
                    onChange={(e) => setGlobalFilter(e.target.value)}
                    size="small"
                    variant="outlined"
                    InputProps={{
                        startAdornment: (
                            <InputAdornment position="start">
                                <SearchIcon />
                            </InputAdornment>
                        ),
                    }}
                    sx={{ width: '250px' }}
                />
                
                <MRT_ToggleFiltersButton table={table} />
                <Button 
                    variant="outlined" 
                    size="large"
                    onClick={() => {
                        setColumnFilters([]);
                        setGlobalFilter('');
                    }}
                >
                    Limpar Filtros
                </Button>
            </Box>
        ),
        enableToolbarInternalActions: false,
    });

    return (
        <Box
            sx={{
                width: '100%'
            }}
        >
            <MaterialReactTable table={table} />
        </Box>
    );
};

export default List;