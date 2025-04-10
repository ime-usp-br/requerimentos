import React from 'react'
import { Stack, Divider, Paper } from '@mui/material';

import Builder from '../RequisitionList/RequisitionListBody/builder';
import buttonComponentList from '../RequisitionList/RequisitionListBody/UserActions/buttonComponentList';

export default function ActionsMenu1({ selectedActions, params }) {
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
                                },

                                // "& .MuiButton-root:hover": {
                                //     opacity: 0.8
                                // }
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