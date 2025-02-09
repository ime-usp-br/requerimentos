import React from "react";
import { Stack } from "@mui/material";

import UserActions from './RequisitionListBody/UserActions';
import List from './RequisitionListBody/List';

export default function RequisitionListBody({ roleId, requisitionPeriodStatus, requisitions, selectedColumns }) {
    return (
        <Stack
            direction='column'
            spacing={4}
            sx={{
                alignItems: 'center',
                width: '90%'
            }} 
        >
            <UserActions roleId={roleId} requisitionPeriodStatus={requisitionPeriodStatus} />
            <List requisitions={requisitions} selectedColumns={selectedColumns} />
        </Stack>
    );
};