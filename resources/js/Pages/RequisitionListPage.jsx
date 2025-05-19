import React from 'react';
import RequisitionListTable from '../Features/RequisitionList/RequisitionListTable';
import BasePage from './BasePage';

function RequisitionListPage({ label,
    requisitions,
    selectedColumns,
    selectedActions,
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
            <RequisitionListTable
                requisitions={requisitions}
                selectedColumns={selectedColumns} />
        </BasePage>
    );
}

export default RequisitionListPage;