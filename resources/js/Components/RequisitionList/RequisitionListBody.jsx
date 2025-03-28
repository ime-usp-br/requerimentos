import React from "react";
import { Stack } from "@mui/material";

import ActionsMenu1 from "../Atoms/ActionsMenu1";

import List from './RequisitionListBody/List';

export default function RequisitionListBody({ requisitions, 
                                              selectedColumns,
                                              selectedActions,
                                              actionsParams }) {
    return (
        <Stack
            direction='column'
            spacing={4}
            sx={{
                alignItems: 'top',
                justifyContent: 'center',
                width: '86%',
                paddingTop: 4
            }} 
        >
            <ActionsMenu1 params={actionsParams} selectedActions={selectedActions} />
            <List 
                requisitions={requisitions} 
                selectedColumns={selectedColumns}
            />
        </Stack>
    );
};