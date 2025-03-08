import React from 'react';
import { Stack } from '@mui/material';

import Header from '../Components/Atoms/Header/Header';
import RequisitionListBody from '../Components/RequisitionList/RequisitionListBody';

export default function RequisitionList({ requisitions, 
                                          selectedColumns, 
                                          roleId, 
                                          userRoles, 
                                          requisitionPeriodStatus 
                                        }) {
    return (
        <Stack 
            direction='column'
            spacing={{ xs: 3, sm: 0 }}
            sx={{
                justifyContent: 'space-around',
                alignItems: 'center',
                width: '100%'
            }}
        >
            <Header roleId={roleId} userRoles={userRoles} />
            <RequisitionListBody
                roleId={roleId}
                requisitionPeriodStatus={requisitionPeriodStatus}
                requisitions={requisitions}
                selectedColumns={selectedColumns}
            />
        </Stack>
    );
};