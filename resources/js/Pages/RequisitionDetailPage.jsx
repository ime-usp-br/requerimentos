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

    return (
        <RequisitionProvider requisitionData={{...requisition, takenDiscs: latestTakenDisciplines, documents: sortedDocuments}}>
            <BasePage
                headerProps={{
                    label: label,
                    isExit: false
                }}
                actionsProps={{
                    selectedActions: selectedActions,
                    variant: actionsVariant
                }}
            >
                <RequisitionDetail takenDiscs={latestTakenDisciplines} documents={sortedDocuments} />
            </BasePage>
        </RequisitionProvider>
    );
};

export default RequisitionDetailPage;