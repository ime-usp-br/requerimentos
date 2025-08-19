import React from 'react';
import { useMemo, useState, useEffect } from 'react';
import { MaterialReactTable, useMaterialReactTable, MRT_ToggleFiltersButton } from 'material-react-table';

// Make sure the imports are correct
import { Link, Box, Button, TextField, InputAdornment, Divider, Stack, Grid2 } from '@mui/material';
import SearchIcon from '@mui/icons-material/Search';
import OpenInNewIcon from '@mui/icons-material/OpenInNew';

import Builder from '../../ui/ComponentBuilder/Builder';
import columnTypes from "../../ui/ComponentBuilder/TableColumnTypes";

function List({ requisitions, selectedColumns }) {
    let textStyle = {
        //simple styling with the `sx` prop, works just like a style prop in this example
        sx: {
            fontSize: 20,
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
        enableColumnOrdering: false,
        enableGlobalFilter: true,
        enableRowActions: true,
        positionActionsColumn: 'last',
        muiTableBodyCellProps: textStyle,
        displayColumnDefOptions: {
            'mrt-row-actions': {
                header: null,
                size: 30,
            },
        },
        defaultColumn: {
            minSize: 20
        },
        tableLayout: 'fixed',
        muiTableHeadRowProps: () => ({
            sx: {
                backgroundColor: '#7CB4FD',
                color: 'white'
            }
        }),
        muiTableHeadCellProps: () => ({
            sx: {
                fontSize: 20,
                color: 'white',

            }
        }),
        muiTableBodyRowProps: ({ row }) => ({
            sx: {
                backgroundColor: row.index % 2 != 0 ? '#E3FAFF' : '#ffffff', // alternate colors
            },
        }),
        muiTablePaperProps: {
            elevation: 0,
            sx: {
                borderRadius: 0
            }
        },
        renderRowActions: ({ row }) => (
            <Box display="flex" alignItems="center" justifyContent="center" height="100%">
                <Link href={route('showRequisition', { requisitionId: row.original.id })} underline='never' color='textDisabled' display="flex" alignItems="center" justifyContent="center">
                    <OpenInNewIcon fontSize="medium" />
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
            <Stack
                sx={{
                    display: 'flex',
                    gap: '0.5rem',
                    pb: '8px',
                    justifyContent: 'flex-start',
                    alignItems: 'center',
                }}
            >
                {/* Global filter textbox */}
                <Grid2
                    container
                    direction='row'
                    sx={{
                        width: '100%',
                        marginTop: -6,
                        marginRight: 4,
                        marginLeft: 46,
                        justifyContent: "flex-start",
                        alignItems: 'center',
                        position: 'fixed',
                        zIndex: 20
                    }}
                >
                    <TextField
                        placeholder="Buscar por tudo..."
                        value={globalFilter ?? ''}
                        onChange={(e) => setGlobalFilter(e.target.value)}
                        size="medium"
                        variant="standard"
                        InputProps={{
                            startAdornment: (
                                <InputAdornment position="start">
                                    <SearchIcon />
                                </InputAdornment>
                            ),
                        }}
                        sx={{ width: '250px' }}
                    />
                    
                    <MRT_ToggleFiltersButton 
                        table={table}
                    />
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
                </Grid2>
                
                <Divider 
                    orientation='horizontal' 
                    flexItem 
                    sx={{ 
                        borderWidth: 5.5,
                        bgcolor: '#FCA22D',
                    }} 
                />
            </Stack>
        ),
        enableToolbarInternalActions: false,
    });

    return (
        <Box
            sx={{
                width: '100%',
                paddingX: 2,
                boxSizing: 'border-box'
            }}
        >
            <MaterialReactTable table={table} />
        </Box>
    );
};

export default List;