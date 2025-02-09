import React from 'react';
import { useMemo } from 'react';
import { MaterialReactTable, useMaterialReactTable } from 'material-react-table';
import VisibilityIcon from '@mui/icons-material/Visibility';
import { Link, Box } from '@mui/material';

import Builder from './columnBuilder';
import { route } from 'ziggy-js';

export default function List({ requisitions, selectedColumns }) {
    let builder = new Builder(selectedColumns);
    let columns = useMemo(
        () => builder.getStructure(),
        [],
    );
    let data = requisitions;
    const table = useMaterialReactTable({
        columns,
        data,
        rowCount: 20,
        enableSorting: false,
        enableDensityToggle: false,
        enableFullScreenToggle: false,
        enableHiding: false,
        enableColumnDragging: false,
        enableFilters: true,
        enableColumnFilters: true,
        enableTopToolbar: false,
        enableColumnOrdering: true,
        enableGlobalFilter: false,
        enableRowActions: true,
        displayColumnDefOptions: {
            'mrt-row-actions': {
                header: null, //change header text
                size: 0, //make actions column wider
            },
        },
        renderRowActions: ({ row }) => (
            <Link href={route('student.show', { requisitionId: row.original.id })} underline='never' color='textDisabled'>
                <VisibilityIcon />
            </Link>
        ),
        initialState: { density: 'compact' },
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