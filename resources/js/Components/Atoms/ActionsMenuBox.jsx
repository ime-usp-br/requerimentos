import React from 'react';
import { MenuList, MenuItem, Divider, Paper } from '@mui/material';
import Builder from './ComponentBuilder/Builder';
import buttonComponentList from './ComponentBuilder/buttonComponentList';

export default function ActionsMenu2({ selectedActions, params }) {
    let builder = new Builder(buttonComponentList);
    return (
        <span>
            <Paper
                elevation={3}
                sx={{ position: 'sticky', top: 140 }}
            >
                <MenuList>
                    {
                        selectedActions.map((grouping, groupIndex) =>
                            (selectedActions.length - 1 != groupIndex) ?
                                builder.build(grouping).map((itemBuilder) =>
                                    <MenuItem>
                                        {itemBuilder(params)}
                                    </MenuItem>
                                ).concat([<Divider />])
                                :
                                builder.build(grouping).map((itemBuilder) =>
                                    <MenuItem>
                                        {itemBuilder(params)}
                                    </MenuItem>
                                )
                        ).flat()
                    }
                </MenuList>
            </Paper>
        </span>
    );
}