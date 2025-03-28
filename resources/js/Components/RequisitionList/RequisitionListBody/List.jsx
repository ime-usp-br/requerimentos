import React from 'react';
import { useMemo } from 'react';
import { MaterialReactTable, useMaterialReactTable } from 'material-react-table';
import VisibilityIcon from '@mui/icons-material/Visibility';
import { Link, Box } from '@mui/material';

import Builder from './builder';
import columnTypes from "./columnTypes";

export default function List({ requisitions, selectedColumns }) {
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
    let data = requisitions;
    const table = useMaterialReactTable({
        columns,
        data,
        rowCount: 20,
        enableSorting: true,
        enableDensityToggle: false,
        enableFullScreenToggle: false,
        enableHiding: false,
        enableColumnDragging: false,
        enableFilters: true,
        enableColumnFilters: true,
        enableTopToolbar: false,
        enableColumnOrdering: true,
        enableGlobalFilter: true,
        enableRowActions: true,
        muiTableHeadCellProps: textStyle,
        muiTableBodyCellProps: textStyle,
        displayColumnDefOptions: {
            'mrt-row-actions': {
                header: null, //change header text
                size: 0, //make actions column wider
            },
        },
        renderRowActions: ({ row }) => (
            <Link href={route('showRequisition', { requisitionId: row.original.id })} underline='never' color='textDisabled'>
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