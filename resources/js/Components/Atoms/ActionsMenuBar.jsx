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
                        <Paper 
                            elevation={0}
                            sx={{
                                "& .MuiButton-root": {
                                    all: 'init',
                                    paddingY: 1,
                                    paddingX: 1.6,
                                    color: 'white',
                                    backgroundColor: 'primary.main'
                                }
                            }}
                        >
                            { itemBuilder(params) }
                        </Paper>
                    ).concat((selectedActions.length - 1 != groupIndex) ? [<Divider />] : [])
                ).flat() 
            }
        </Stack>
    );
};