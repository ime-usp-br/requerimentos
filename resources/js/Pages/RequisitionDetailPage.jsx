import React from 'react';
import RequisitionDetail from '../Features/RequisitionDetail/RequisitionDetail';
import BasePage from './BasePage';
import { RequisitionProvider } from '../Features/RequisitionDetail/useRequisitionContext';
import { useTheme } from '@mui/material/styles';
import useMediaQuery from '@mui/material/useMediaQuery';

const RequisitionDetailPage = ({ label,
    selectedActions,
    requisition,
    takenDiscs,
    documents }) => {

    // Use MUI theme breakpoints to determine variant

    const theme = useTheme();
    const isMediumOrLarger = useMediaQuery(theme.breakpoints.up('md'));
    const actionsVariant = isMediumOrLarger ? 'box' : 'bar';

    return (
        <RequisitionProvider requisitionData={{...requisition, 'takenDiscs': takenDiscs}}>
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
                <RequisitionDetail takenDiscs={takenDiscs} documents={documents} />
            </BasePage>
        </RequisitionProvider>
    );
};

export default RequisitionDetailPage;