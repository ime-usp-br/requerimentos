import React from 'react';
import RequisitionDetail from '../Components/RequisitionDetail/RequisitionDetail';
import BasePage from './BasePage';

const RequisitionDetailPage = ({ label,
    roleId,
    userRoles,
    selectedActions,
    requisition,
    takenDiscs,
    documents }) => {

    let actionsParams = {};
    actionsParams.requisitionId = requisition.id;

    return (
        <BasePage
            headerProps={{
                label: label,
                roleId: roleId,
                userRoles: userRoles,
                isExit: false
            }}
            actionsProps={{
                actionsParams: actionsParams,
                selectedActions: selectedActions,
            }}
        >
            <RequisitionDetail requisition={requisition} takenDiscs={takenDiscs} documents={documents} />
        </BasePage>
    );
};

export default RequisitionDetailPage;