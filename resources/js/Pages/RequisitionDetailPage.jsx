import React from 'react';
import RequisitionDetail from '../Features/RequisitionDetail/RequisitionDetail';
import BasePage from './BasePage';
import { RequisitionProvider } from '../Features/RequisitionDetail/useRequisitionContext';

const RequisitionDetailPage = ({ label,
    selectedActions,
    requisition,
    takenDiscs,
    documents }) => {

    return (
        <RequisitionProvider requisitionData={requisition}>
            <BasePage
                headerProps={{
                    label: label,
                    isExit: false
                }}
                actionsProps={{
                    selectedActions: selectedActions,
                    variant: 'box'
                }}
            >
                <RequisitionDetail requisition={requisition} takenDiscs={takenDiscs} documents={documents} />
            </BasePage>
        </RequisitionProvider>
    );
};

export default RequisitionDetailPage;