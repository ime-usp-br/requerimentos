import React from 'react'
import { Stack, Divider, Paper } from '@mui/material';

import Builder from './ComponentBuilder/Builder';
import buttonComponentList from './ComponentBuilder/buttonComponentList';

export default function ActionsMenuBar({ selectedActions, actionsParams }) {
    let builder = new Builder(buttonComponentList);
    const actionsMenubarButtonStyle = {
        variant: 'contained'
    };
    return (
        <Stack 
            direction={{ xs: 'column', sm: 'row' }}
            spacing={{ xs: 1, sm: 2 }}
        >
            { 
                selectedActions.map((grouping, groupIndex) =>
                    builder.build(grouping).map((itemBuilder) =>
                        <Paper elevation={0} >
                            { itemBuilder({ actionsParams, styles: actionsMenubarButtonStyle }) }
                        </Paper>
                    ).concat((selectedActions.length - 1 != groupIndex) ? [<Divider />] : [])
                ).flat() 
            }
        </Stack>
    );
};