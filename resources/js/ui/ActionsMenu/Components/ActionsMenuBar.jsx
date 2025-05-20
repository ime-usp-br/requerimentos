import React from 'react'
import { styled } from '@mui/material/styles';
import { Stack, Divider, Paper } from '@mui/material';
import ActionsMenu from '../ActionsMenu';

const ActionsMenuContainer = styled(Stack)(({ theme }) => ({
    width: '100%',
    flexDirection: 'row',
    gap: theme.spacing(2),

    [theme.breakpoints.down('md')]: {
        flexDirection: 'column',
        gap: theme.spacing(1),
    },
}));

const actionsMenubarButtonStyle = {
    variant: 'contained'
};

export default function ActionsMenuBar({ builder, selectedActions }) {
    return (
        <ActionsMenuContainer>
            {
                selectedActions.map((grouping, groupIndex) =>
                    builder.build(grouping).map((itemBuilder, itemIndex) =>
                        <Paper elevation={0} key={`paper-${groupIndex}-${itemIndex}`}>
                            {itemBuilder({ styles: actionsMenubarButtonStyle })}
                        </Paper>
                    ).concat(
                        (selectedActions.length - 1 !== groupIndex)
                            ? [<Divider key={`divider-${groupIndex}`} />]
                            : []
                    )
                ).flat()
            }
        </ActionsMenuContainer>
    );
};