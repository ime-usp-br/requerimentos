import React from 'react';
import RequisitionDetail from '../Features/RequisitionDetail/RequisitionDetail';
import BasePage from './BasePage';
import { RequisitionProvider } from '../Features/RequisitionDetail/useRequisitionContext';
import { useTheme } from '@mui/material/styles';
import useMediaQuery from '@mui/material/useMediaQuery';

const RequisitionDetailPage = ({ label,
    selectedActions,
    requisition,
    latestDocuments,
    latestTakenDisciplines
}) => {
    const theme = useTheme();
    const isMediumOrLarger = useMediaQuery(theme.breakpoints.up('md'));
    const actionsVariant = isMediumOrLarger ? 'box' : 'bar';

    const sortedDocuments = latestDocuments.sort((a, b) => a.type.localeCompare(b.type));
    console.log(selectedActions);

    return (
        <RequisitionProvider requisitionData={{...requisition, takenDiscs: latestTakenDisciplines, documents: sortedDocuments}}>
            <BasePage
                headerProps={{
                    label: label,
                    selectedActions: selectedActions,
                    isExit: true
                }}
                actionsProps={{
                    selectedActions: selectedActions,
                    variant: actionsVariant
                }}
            >
                <RequisitionDetail
                    takenDiscs={latestTakenDisciplines}
                    documents={sortedDocuments}
                    selectedActions={selectedActions}
                />
            </BasePage>
        </RequisitionProvider>
    );
};

export default RequisitionDetailPage;
