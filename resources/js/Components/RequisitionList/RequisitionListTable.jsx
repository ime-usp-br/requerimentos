import React from 'react';
import { useMemo } from 'react';
import { MaterialReactTable, useMaterialReactTable } from 'material-react-table';
import PageviewIcon from '@mui/icons-material/Pageview';
import { Link, Box } from '@mui/material';

import Builder from '../Atoms/ComponentBuilder/Builder';
import columnTypes from "../Atoms/ComponentBuilder/columnTypes";

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