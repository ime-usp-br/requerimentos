import React from "react";
import { Stack } from "@mui/material";
import ActionsMenuBar from "../Atoms/ActionsMenuBar";
import RequisitionListTable from './RequisitionListTable';

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
            <ActionsMenuBar actionsParams={actionsParams} selectedActions={selectedActions} />
            <RequisitionListTable 
                requisitions={requisitions} 
                selectedColumns={selectedColumns}
            />
        </Stack>
    );
};