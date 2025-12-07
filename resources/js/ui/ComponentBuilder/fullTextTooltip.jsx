import React from 'react';
import { Tooltip, Snackbar, Portal } from '@mui/material';

const fullTextTooltip = ({ cell }) => {
    const value = cell.getValue();
    const [open, setOpen] = React.useState(false);

    const handleCopy = async () => {
        try {
            await navigator.clipboard.writeText(value);
            setOpen(true);
        } catch (err) {
            console.error('Failed to copy:', err);
        }
    };

    return (
        <>
            <Tooltip title={value} placement="bottom-start">
                <div
                    onClick={handleCopy}
                    style={{
                        overflow: 'hidden',
                        whiteSpace: 'nowrap',
                        textOverflow: 'ellipsis',
                        width: '100%',
                    }}
                >
                    {value}
                </div>
            </Tooltip>

            <Portal>
                <Snackbar
                    open={open}
                    onClose={() => setOpen(false)}
                    autoHideDuration={1500}
                    message="Copiado para a área de transferência"
                    anchorOrigin={{ vertical: 'bottom', horizontal: 'right' }}
                />
            </Portal>
        </>
    );
};

export default fullTextTooltip;
