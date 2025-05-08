import React from 'react';
import { Stack } from '@mui/material';

import Header from '../Components/Header/Header';
import RequisitionListBody from '../Components/RequisitionList/RequisitionListBody';

export default function RequisitionList({ label,
                                          requisitions, 
                                          selectedColumns,
                                          selectedActions,
                                          roleId,
                                          userRoles, 
                                          requisitionEditionStatus,
                                          requisitionCreationStatus
                                        }) {
    let actionsParams = {};
    actionsParams.roleId = roleId;
    actionsParams.requisitionEditionStatus = requisitionEditionStatus;
    actionsParams.requisitionCreationStatus = requisitionCreationStatus;

    return (
        <Stack 
            direction='column'
            sx={{
                justifyContent: 'space-around',
                alignItems: 'center',
                width: '100%'
            }}
        >
            <Header 
                label={label} 
                roleId={roleId} 
                useRoles={true}
                userRoles={userRoles}
                selectedActions={selectedActions}
                actionsParams={actionsParams}
                isExit={true}     
            />
                
            <RequisitionListBody
                requisitions={requisitions}
                selectedColumns={selectedColumns}
                selectedActions={selectedActions}
                actionsParams={actionsParams}
            />
        </Stack>
    );
};