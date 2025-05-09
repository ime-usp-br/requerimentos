import React from 'react';
import { useMemo, useState } from 'react';
import { MaterialReactTable, useMaterialReactTable } from 'material-react-table';
import { Button, DialogActions, DialogContent } from '@mui/material';
import { router } from '@inertiajs/react';
import { useDialogContext } from '../Context/useDialogContext';
import ActionSuccessful from './ActionSuccessful';

export default function ReviewerPicker({ requisitionId, reviewers, closeDialog }) {
    const { setDialogTitle, setDialogBody, openDialog, _closeDialog } = useDialogContext();

    const [rowSelection, setRowSelection] = useState({});
    let textStyle = {
        //simple styling with the `sx` prop, works just like a style prop in this example
        sx: {
            fontSize: 18,
        },
    };
    
    let columns = useMemo(
        () => [
            {
                header: 'Nome',
                accessorKey: 'name',
                enableHiding: false,
                size: 120
            },
            {
                header: 'Número USP',
                accessorKey: 'codpes',
                enableHiding: false,
                size: 60
            }
        ],
        [],
    );

    const submit = () => {
        router.post(
            route('reviewer.sendToReviewer'), 
            { 
                'requisitionId': requisitionId,
                'reviewer_nusps': rowSelection
            },
            {
                onSuccess: () => {
                    closeDialog();
                    setDialogTitle('Requerimento enviado');
                    setDialogBody(<ActionSuccessful dialogText={'Enviado ao(s) parecerista(s) com sucesso.'} />);
                    openDialog();
                },
                onError: (errors) => console.log(errors)
            }
        );
    };

    const data = useMemo(() => 
        reviewers.map(item => ({
            ...item,
            name: item.name === null ? '*Desconhecido*' : item.name,
        })),
    [reviewers]);
    
    const table = useMaterialReactTable({
        columns,
        data,
        enableSorting: false,
        enableDensityToggle: false,
        enableFullScreenToggle: false,
        enableHiding: false,
        enableColumnDragging: false,
        enableFilters: true,
        enableColumnFilters: true,
        enableTopToolbar: false,
        enableBottomToolbar: false,
        getRowId: (row) => row.codpes,
        enableRowSelection: true,
        onRowSelectionChange: setRowSelection,
        state: { rowSelection },
        muiTableHeadCellProps: textStyle,
        muiTableBodyCellProps: textStyle,
        displayColumnDefOptions: {
            'mrt-row-actions': {
                header: null,
                size: 0
            },
        },
        initialState: { density: 'compact' },
    });

    return (
        <>
            <DialogContent>
                <MaterialReactTable table={table} />
            </DialogContent>
            <DialogActions>
                <Button onClick={closeDialog}>Cancelar</Button>
                <Button onClick={submit}>Enviar</Button>
            </DialogActions>
        </>
    );
};