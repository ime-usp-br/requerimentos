import React from 'react'
import { styled } from '@mui/material/styles';
import { Stack, Divider, ButtonGroup, MenuItem } from '@mui/material';
import ActionsMenu from '../ActionsMenu';

const actionsMenuBarButtonStyle = {
    disableRipple: true,
    variant: 'text',
    color: 'black',
    sx: {
        width: '100%',
        height: '100%',
        textAlign: 'left',
        justifyContent: 'flex-start',
    }

};

export default function ActionsMenuBar({ builder, selectedActions }) {
    return (
        <Stack
            direction="row"
            sx={{
                position: 'sticky',
                top: 0,
                padding: 0,
                bgcolor: 'white !important',
                justifyContent: 'space-between'
            }}
        >
            {
                selectedActions.map((grouping, groupIndex) =>
                (
                    <ButtonGroup
                        size="small"
                        sx={{
                            justifyContent: 'flex-end'
                        }}
                    >
                        {
                            builder.build(grouping).map((itemBuilder, itemIndex) =>
                                <MenuItem key={`group-${groupIndex}-item-${itemIndex}`} sx={{ padding: 0 }}>
                                    {itemBuilder({ styles: actionsMenuBarButtonStyle })}
                                </MenuItem>
                            ).concat((selectedActions.length - 1 != groupIndex) ? [<Divider key={`divider-${groupIndex}`} />] : [])
                        }
                    </ButtonGroup>
                )
                ).flat()
            }
        </Stack>
    );
};
