import React from "react";
import {
    Button,
    DialogActions,
    DialogContent,
    DialogContentText,
} from "@mui/material";
import { useDialogContext } from '../Context/useDialogContext';

function RemoveRoleConfirmationDialog({ removeRole, data }) {
    const { closeDialog } = useDialogContext();

    function handleConfirm() {
        removeRole();
        closeDialog();
    };

    return (
        <>
            <DialogContent>
                <DialogContentText id="alert-dialog-description">
                    Você tem certeza que deseja retirar o papel {data.role} do
                    usuário {data.name} (nº usp {data.nusp})?
                </DialogContentText>
            </DialogContent>
            <DialogActions>
                <Button color="error" onClick={closeDialog}>
                    Cancelar
                </Button>
                <Button variant="contained" onClick={handleConfirm} autoFocus>
                    Confirmar
                </Button>
            </DialogActions>
        </>
    );
}

export default RemoveRoleConfirmationDialog;
