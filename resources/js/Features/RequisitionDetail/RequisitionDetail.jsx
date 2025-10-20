import React from 'react';
import { Paper, Stack, Divider } from '@mui/material';
import { useRequisitionContext } from './useRequisitionContext';
import { useUser } from '../../Context/useUserContext';

import ActionsMenu from '../../ui/ActionsMenu/ActionsMenu';
import RequisitionData from './Components/RequisitionData';
import StudentData from './Components/StudentData';
import RequestedDisciplineData from './Components/RequestedDisciplineData';
import CompletedDisciplinesData from './Components/CompletedDisciplinesData';
import Reviews from './Components/Reviews';
import ResultForm from './Components/ResultForm';

const RequisitionDetail = ({
    takenDiscs,
    documents,
    selectedActions
}) => {
    const { requisitionData } = useRequisitionContext();

    const { user } = useUser();
    const roleId = user?.currentRoleId;

    let resultColor;
    switch (requisitionData.result) {
        case "Deferido":
            resultColor = "green";
            break;
        case "Indeferido":
            resultColor = "red";
            break;
        case "Inconsistência nas informações":
            resultColor = "orange";
            break;
        case "Cancelado":
            resultColor = "grey";
            break;
        default:
            resultColor = "";
    }

    return (
        <Paper
            id="requisition-paper"
            elevation={2}
            sx={{
                marginTop: .5,
                height: 'calc(100vh - 80px)',
                width: '100%',
                maxWidth: '1440px',
                overflow: 'scroll',
            }}
        >
            <ActionsMenu selectedActions={selectedActions} variant={'bar'} />
            <Stack
                spacing={1.2}
                divider={<Divider />}
                sx={{
                    padding: 1
                }}
            >
                <RequisitionData requisitionData={requisitionData} />
                <StudentData requisitionData={requisitionData} />
                <RequestedDisciplineData requisitionData={requisitionData} takenDiscs={takenDiscs} documents={documents} />
                <CompletedDisciplinesData requisitionData={requisitionData} takenDiscs={takenDiscs} documents={documents} />
                {roleId != 1 && <Reviews requisitionData={requisitionData} />}
                {roleId != 1 && roleId != 4 && <ResultForm requisitionData={requisitionData} />}
            </Stack>

        </Paper>
    );
};

export default RequisitionDetail;
