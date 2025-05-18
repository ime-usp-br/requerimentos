import React from 'react';
import { MenuList, MenuItem, Divider, Paper } from '@mui/material';

const actionsMenuBoxButtonStyles = {
    disableRipple: true,
    variant: 'text',
    color: 'black',
    sx: {
        padding: '12px 24px',
        width: '100%',
        height: '100%',
        textAlign: 'left',
        justifyContent: 'flex-start',
    }
};

export default function ActionsMenuBox({ builder, selectedActions, actionsParams }) {

    return (
        <Paper
            elevation={3}
            sx={{ position: 'sticky', top: 140 }}
        >
            <MenuList>
                {
                    selectedActions.map((grouping, groupIndex) =>
                        builder.build(grouping).map((itemBuilder, itemIndex) =>
                            <MenuItem key={`group-${groupIndex}-item-${itemIndex}`} sx={{ padding: 0 }}>
                                {itemBuilder({ actionsParams, styles: actionsMenuBoxButtonStyles })}
                            </MenuItem>
                        ).concat((selectedActions.length - 1 != groupIndex) ? [<Divider key={`divider-${groupIndex}`} />] : [])
                    ).flat()
                }
            </MenuList>
        </Paper>
    );
}