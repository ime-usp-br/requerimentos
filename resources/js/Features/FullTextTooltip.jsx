import React from 'react';
import { Tooltip, Typography, Portal, Snackbar } from '@mui/material';

const FullTextTooltip = ({ value, fontSize }) => {
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
                        cursor: 'pointer'
                    }}
                >
                    <Typography variant='body2' fontSize={fontSize} noWrap>
                        {value}
                    </Typography>
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

export default FullTextTooltip;
