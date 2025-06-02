import React from 'react';
import { useMemo, useState, useEffect } from 'react';
import { MaterialReactTable, useMaterialReactTable, MRT_GlobalFilterTextField, MRT_ToggleFiltersButton } from 'material-react-table';

import PageviewIcon from '@mui/icons-material/Pageview';
import { Link, Box, Button } from '@mui/material';

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

    let data = requisitions;
    const table = useMaterialReactTable({
        columns,
        data,
        enableSorting: true,
        enableDensityToggle: false,
        enableFullScreenToggle: false,
        enableHiding: false,
        enableColumnDragging: false,
        enableFilters: true,
        enableColumnFilters: true,
        enableTopToolbar: true,
        enableColumnOrdering: true,
        enableGlobalFilter: false,
        enableRowActions: true,
        muiTableHeadCellProps: textStyle,
        muiTableBodyCellProps: textStyle,
        displayColumnDefOptions: {
            'mrt-row-actions': {
                header: null, //change header text
                size: 80, //make actions column wider
            },
        },
        renderRowActions: ({ row }) => (
            <Box display="flex" alignItems="center" justifyContent="center" height="100%">
                <Link href={route('showRequisition', { requisitionId: row.original.id })} underline='never' color='textDisabled' display="flex" alignItems="center" justifyContent="center">
                    <PageviewIcon fontSize="large" />
                </Link>
            </Box>
        ),
        state: { columnFilters, density: 'compact', showGlobalFilter: true },
        renderTopToolbarCustomActions: ({ table }) => (
            <Box sx={{ display: 'flex', gap: '1rem', p: '4px' }}>
                <MRT_GlobalFilterTextField table={table} />
                <MRT_ToggleFiltersButton table={table} />
                <Button variant="outlined" onClick={() => setColumnFilters([])}>
                    Limpar Filtros
                </Button>
            </Box>
        ),
        renderToolbarInternalActions: ({ table }) => <></>,
        onColumnFiltersChange: setColumnFilters
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