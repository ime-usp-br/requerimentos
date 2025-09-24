import React from 'react';
import {
	Paper, Typography, Stack, Grid2, Divider, TableContainer, Table, TableHead, TableRow, TableCell, TableBody,
	FormControlLabel, FormControl, RadioGroup, Radio, TextField, Button
} from '@mui/material';
import OpenInNewIcon from '@mui/icons-material/OpenInNew';
import Link from '@mui/material/Link';
import { useForm, router } from "@inertiajs/react";
import { useRequisitionContext } from './useRequisitionContext';
import { useDialogContext } from '../../Context/useDialogContext';
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
    const userRoles = user?.roles || [];
    const roleId = user?.currentRoleId;
    const departmentId = user?.currentDepartmentId;

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
		<Stack
			direction='column'
			sx={{
				justifyContent: 'center',
				alignItems: 'top',
				width: '1440px',
                height: 'calc(100vh - 130px)',
			}}>

			<Paper
				id="requisition-paper"
				elevation={2}
				sx={{
					width: '100%',
                    overflow: 'scroll',
				}}
			>
                <ActionsMenu selectedActions={selectedActions} variant={'bar'} />
				<Stack
                    spacing={3}
                    divider={<Divider orientation="horizontal" flexItem />}
                    sx={{
                        padding: 1
                    }}
                >
                    <RequisitionData requisitionData={requisitionData} />
                    <StudentData requisitionData={requisitionData} />
                    <RequestedDisciplineData requisitionData={requisitionData} takenDiscs={takenDiscs} documents={documents} />
                    <CompletedDisciplinesData requisitionData={requisitionData} takenDiscs={takenDiscs} documents={documents} />
                    { roleId != 1 && <Reviews requisitionData={requisitionData} /> }
                    { roleId != 1 && roleId != 4 && <ResultForm requisitionData={requisitionData} /> }
                </Stack>

			</Paper>
		</Stack>
	);
};

export default RequisitionDetail;
