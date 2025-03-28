import React from 'react';
import { DialogContent, DialogContentText, DialogActions, Button } from '@mui/material';
import { router } from '@inertiajs/react';
import { useDialogContext } from '../../../Context/useDialogContext';

export default function ActionSuccessful({ dialogText }) {
    const { _setDialogTitle, _setDialogBody, _openDialog, closeDialog } = useDialogContext();

    const returnToList = () => {
        closeDialog();
        router.get(route('list'));
    };

    return (
        <>
            <DialogContent>
                <DialogContentText>
                    { dialogText }
                </DialogContentText>
            </DialogContent>
            <DialogActions>
                <Button onClick={returnToList}>Voltar à lista</Button>
            </DialogActions>
        </>
    );
}