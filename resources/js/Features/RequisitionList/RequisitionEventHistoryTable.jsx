import React from 'react';
import { useMemo } from 'react';
import { MaterialReactTable, useMaterialReactTable } from 'material-react-table';

import PageviewIcon from '@mui/icons-material/Pageview';
import { Box, Button } from '@mui/material';
import { router } from '@inertiajs/react';

import Builder from '../../ui/ComponentBuilder/Builder';
import columnTypes from "../../ui/ComponentBuilder/TableColumnTypes";

function RequisitionEventHistoryTable({ events, selectedColumns }) {
    let textStyle = {
        sx: {
            fontSize: 18,
        },
    };
    let builder = new Builder(columnTypes);
    let columns = useMemo(
        () => builder.build(selectedColumns),
        [selectedColumns],
    );

    let data = events;
    const table = useMaterialReactTable({
        columns,
        data,
        rowCount: 20,
		enableSorting: true,
		initialState: { density: 'compact' },
        enableColumnDragging: false,
        enableColumnFilters: true,
        enableTopToolbar: false,
        enableColumnOrdering: true,
        enableRowActions: true,
        muiTableHeadCellProps: textStyle,
        muiTableBodyCellProps: textStyle,
        displayColumnDefOptions: {
            'mrt-row-actions': {
                header: 'Ver Estado',
                size: 120, 
            },
        },
        renderRowActions: ({ row }) => (
            <Box display="flex" alignItems="center" justifyContent="center" height="100%">
                <Button
                    variant="text"
                    onClick={() => router.get(route('record.event.version', { eventId: row.original.id }))}
                >
                    <PageviewIcon fontSize="large" />
                </Button>
            </Box>
        )
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

export default RequisitionEventHistoryTable;
