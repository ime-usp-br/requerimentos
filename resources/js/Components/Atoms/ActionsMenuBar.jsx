import React from 'react'
import { Stack, Divider, Paper } from '@mui/material';

import Builder from './ComponentBuilder/Builder';
import buttonComponentList from './ComponentBuilder/buttonComponentList';

export default function ActionsMenuBar({ selectedActions, params }) {
    let builder = new Builder(buttonComponentList);
    return (
        <Stack 
            direction={{ xs: 'column', sm: 'row' }}
            spacing={{ xs: 1, sm: 2 }}
        >
            { 
                selectedActions.map((grouping, groupIndex) =>
                    builder.build(grouping).map((itemBuilder) =>
                        <Paper elevation={0} >
                            { itemBuilder(params) }
                        </Paper>
                    ).concat((selectedActions.length - 1 != groupIndex) ? [<Divider />] : [])
                ).flat() 
            }
        </Stack>
    );
};