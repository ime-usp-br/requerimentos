import React from "react";
import { useForm } from "@inertiajs/react";
import { Stack, Divider, Alert, Button, Container, Paper, useTheme, useMediaQuery } from "@mui/material";

import PersonalData from "./Components/PersonalInfo";
import CourseData from "./Components/CourseInfo";
import DisciplinesData from "./Components/DisciplinesData/DisciplinesData";
import DocumentsUpload from "./Components/DocumentsUpload";
import AdditionalInformation from "./Components/AdditionalInformation";

const RequisitionForm = ({ requisitionData, isStudent, isUpdate }) => {
	const { data, setData, post, processing, errors } = useForm({
		requisitionId: requisitionData?.requisitionId || "",
		student_name: requisitionData?.student_name || "",
		email: requisitionData?.email || "",
		student_nusp: requisitionData?.student_nusp || "",
		course: requisitionData?.course || "",
		requestedDiscName: requisitionData?.requestedDiscName || "",
		requestedDiscType: requisitionData?.requestedDiscType || "",
		requestedDiscCode: requisitionData?.requestedDiscCode || "",
		requestedDiscDepartment: requisitionData?.requestedDiscDepartment || "",
		takenDiscNames: requisitionData?.takenDiscNames || [""],
		takenDiscInstitutions: requisitionData?.takenDiscInstitutions || [""],
		takenDiscCodes: requisitionData?.takenDiscCodes || [""],
		takenDiscYears: requisitionData?.takenDiscYears || [""],
		takenDiscGrades: requisitionData?.takenDiscGrades || [""],
		takenDiscSemesters: requisitionData?.takenDiscSemesters || [""],
		takenDiscCount: requisitionData?.takenDiscCount || 1,
		takenDiscRecord: requisitionData?.takenDiscRecord || null,
		courseRecord: requisitionData?.courseRecord || null,
		takenDiscSyllabus: requisitionData?.takenDiscSyllabus || null,
		requestedDiscSyllabus: requisitionData?.requestedDiscSyllabus || null,
		observations: requisitionData?.observations || "",
	});

	function submit(e) {
		e.preventDefault();
		const routeName = isUpdate ? "updateRequisition.post" : "newRequisition.post";

		if (!data.takenDiscRecord || data.takenDiscRecord.type !== 'application/pdf') {
			data.takenDiscRecord = null;
		}
		if (!data.courseRecord || data.courseRecord.type !== 'application/pdf') {
			data.courseRecord = null;
		}
		if (!data.takenDiscSyllabus || data.takenDiscSyllabus.type !== 'application/pdf') {
			data.takenDiscSyllabus = null;
		}
		if (!data.requestedDiscSyllabus || data.requestedDiscSyllabus.type !== 'application/pdf') {
			data.requestedDiscSyllabus = null;
		}
		post(route(routeName));
	}

	const theme = useTheme();
	const isLg = useMediaQuery(theme.breakpoints.up('lg'));

	const getElevation = () => {
		if (isLg) return 3;
		return 0;
	};

	return (
		<Container spacing={2} maxWidth="lg">
			<Paper elevation={getElevation()} sx={{ margin: { lg: 2 }, padding: { lg: 2 } }}>

				<Alert severity="info">
					Crie um formulário para cada matéria a ser dispensada
				</Alert>

				{(errors.takenDiscCount || Object.keys(errors).length > 0) && (
					<Alert severity="error" sx={{ mt: 2 }}>
						{errors.takenDiscCount && (
							<div>{errors.takenDiscCount}</div>
						)}
						{Object.keys(errors).length > 0 && !errors.takenDiscCount && (
							<div>Por favor, corrija os erros nos campos destacados abaixo.</div>
						)}
					</Alert>
				)}

				<form onSubmit={submit}>
					<Stack
						spacing={2}
						divider={<Divider orientation="horizontal" flexItem />}
					>
						{(!isStudent && !isUpdate) && (<PersonalData data={data} setData={setData} isUpdate={isUpdate} errors={errors} />)}
						<CourseData data={data} setData={setData} isUpdate={isUpdate} errors={errors} />
						<DisciplinesData data={data} setData={setData} isUpdate={isUpdate} errors={errors} />
						<DocumentsUpload data={data} setData={setData} errors={errors} />
						<AdditionalInformation data={data} setData={setData} errors={errors} />
					</Stack>
				</form>

				<Stack direction="row" spacing={2} justifyContent="space-between" sx={{ marginTop: 2 }}>
					<Button variant="contained" color="primary" onClick={() => window.history.back()}>
						Voltar
					</Button>
					<Button 
						variant="contained" 
						color="primary" 
						onClick={submit}
						disabled={processing}
					>
						{isUpdate ? "Salvar edições" : "Encaminhar para análise"}
					</Button>
				</Stack>
			</Paper>
		</Container>
	);
};

export default RequisitionForm;
