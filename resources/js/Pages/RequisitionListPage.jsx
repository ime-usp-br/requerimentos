import React from 'react';
import RequisitionListTable from '../Features/RequisitionList/RequisitionListTable';
import BasePage from './BasePage';
import { useUser } from '../Context/useUserContext';


function RequisitionListPage({ label,
    requisitions,
    selectedColumns,
    selectedActions,
    requisitionEditionStatus,
    requisitionCreationStatus
}) {
    const { roleId } = useUser();
    
    let actionsParams = {};
    actionsParams.roleId = roleId;
    actionsParams.requisitionEditionStatus = requisitionEditionStatus;
    actionsParams.requisitionCreationStatus = requisitionCreationStatus;

    return (
        <BasePage
            headerProps={{
                label: label,
                showRoleSelector: true,
                selectedActions: selectedActions,
                actionsParams: actionsParams,
                isExit: true
            }}
            actionsProps={{
                selectedActions: selectedActions,
                actionsProps: actionsParams,
                variant: 'bar'
            }}
        >
            <RequisitionListTable
                requisitions={requisitions}
                selectedColumns={selectedColumns} />
        </BasePage>
    );
}

export default RequisitionListPage;