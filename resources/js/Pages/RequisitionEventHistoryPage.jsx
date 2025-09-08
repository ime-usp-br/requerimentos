import React from 'react';
import RequisitionEventHistoryTable from '../Features/RequisitionList/RequisitionEventHistoryTable';
import BasePage from './BasePage';

function RequisitionEventHistoryPage({ 
    label,
    events,
    selectedColumns,
    selectedActions,
    requisitionId
}) {

    return (
        <BasePage
            headerProps={{
                label: label,
                showRoleSelector: true,
                selectedActions: selectedActions,
                isExit: true
            }}
            actionsProps={{
                selectedActions: selectedActions,
                variant: 'bar'
            }}
        >
            <RequisitionEventHistoryTable
                events={events}
                selectedColumns={selectedColumns}
                requisitionId={requisitionId} />
        </BasePage>
    );
}

export default RequisitionEventHistoryPage;
