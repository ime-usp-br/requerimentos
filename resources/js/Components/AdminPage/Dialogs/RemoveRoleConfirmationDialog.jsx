import React from "react";
import {
    Button,
    Dialog,
    DialogActions,
    DialogContent,
	DialogContentText,
    DialogTitle,
} from "@mui/material";
import { useForm } from "@inertiajs/react";

const AddRoleDialog = ({ open, handleClose, removeRole, data }) => {
    return (
        <Dialog
            open={open}
            onClose={handleClose}
            aria-labelledby="alert-dialog-title"
            aria-describedby="alert-dialog-description"
        >
            <DialogTitle id="alert-dialog-title">
                {"Confirmação de remoção de papel"}
            </DialogTitle>
            <DialogContent>
                <DialogContentText id="alert-dialog-description">
                    Você tem certeza que deseja retirar o papel {data.role} do
                    usuário {data.name}?
                </DialogContentText>
            </DialogContent>
            <DialogActions>
                <Button color="error" onClick={handleClose}>
                    Cancelar
                </Button>
                <Button variant="contained" onClick={removeRole} autoFocus>
                    Confirmar
                </Button>
            </DialogActions>
        </Dialog>
    );
};

export default AddRoleDialog;
