import React from 'react';
import RequisitionListTable from '../Features/RequisitionList/RequisitionListTable';
import BasePage from './BasePage';


function RequisitionList({ label,
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
        <BasePage
            headerProps={{
                label: label,
                roleId: roleId,
                showRoleSelector: true,
                userRoles: userRoles,
                selectedActions: selectedActions,
                actionsParams: actionsParams,
                isExit: true
            }}
            actionsProps={{
                selectedActions: selectedActions,
                actionsParams: actionsParams,
                variant: 'bar'
            }}
        >
            <RequisitionListTable
                requisitions={requisitions}
                selectedColumns={selectedColumns} />
        </BasePage>
    );
}

export default RequisitionList;