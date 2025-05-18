import React, { createContext, useContext, useState } from 'react';
import { Dialog, DialogTitle, DialogContent } from '@mui/material';

const DialogContext = createContext();

const useDialogContext = () => {
    return useContext(DialogContext);
};

const DialogProvider = ({ children }) => {
    const [isOpen, setIsOpen] = useState(false);
    const [dialogTitle, setDialogTitle] = useState('');
    const [dialogBody, setDialogBody] = useState(<></>);

    const openDialog = () => setIsOpen(true);
    const closeDialog = () => setIsOpen(false);

    return (
        <DialogContext.Provider value={{ setDialogTitle, setDialogBody, openDialog, closeDialog }}>
            <Dialog open={isOpen} onClose={closeDialog}>
                <DialogTitle>{dialogTitle}</DialogTitle>
                <DialogContent>{dialogBody}</DialogContent>
            </Dialog>
            {children}
        </DialogContext.Provider>
    );
};

export { useDialogContext, DialogProvider };