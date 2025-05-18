import React from 'react';
import RequisitionDetail from '../Features/RequisitionDetail/RequisitionDetail';
import BasePage from './BasePage';

const RequisitionDetailPage = ({ label,
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
                isExit: false
            }}
            actionsProps={{
                actionsParams: actionsParams,
                selectedActions: selectedActions,
                variant: 'box'
            }}
        >
            <RequisitionDetail requisition={requisition} takenDiscs={takenDiscs} documents={documents} />
        </BasePage>
    );
};

export default RequisitionDetailPage;