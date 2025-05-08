import React, { useEffect } from "react";
import { useForm } from "@inertiajs/react";
import { Stack, Divider, Alert, Button, Container, Paper, useTheme, useMediaQuery } from "@mui/material";

import PersonalData from "../Components/RequisitionForm/PersonalInfo";
import Header from "../Components/Header/Header";
import CourseData from "../Components/RequisitionForm/CourseInfo";
import DisciplinesData from "../Components/RequisitionForm/DisciplinesData/DisciplinesData";
import DocumentsUpload from "../Components/RequisitionForm/DocumentsUpload";
import AdditionalInformation from "../Components/RequisitionForm/AdditionalInformation";

const RequisitionForm = ({ requisitionData, label, roleId, userRoles, isStudent, isUpdate }) => {
    useEffect(() => {
        document.title = "Novo requerimento";
    }, []);

    const { data, setData, post } = useForm({
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
        takenDiscRecord: requisitionData?.takenDiscRecord || "",
        courseRecord: requisitionData?.courseRecord || "",
        takenDiscSyllabus: requisitionData?.takenDiscSyllabus || "",
        requestedDiscSyllabus: requisitionData?.requestedDiscSyllabus || "",
        observations: requisitionData?.observations || "",
    });

    const theme = useTheme();
    const isLg = useMediaQuery(theme.breakpoints.up('lg'));

    const getElevation = () => {
        if (isLg) return 3;
        return 0;
    };
    function submit(e) {
        e.preventDefault();
        const routeName = isUpdate ? "updateRequisition.post" : "newRequisition.post";
        post(route(routeName), {
            onSuccess: () => {
                console.log("Post was successful");
            },
            onError: (errors) => {
                console.log("Post failed", errors);
                console.log("data:\n", data);
            }
        });
    }

    return (
        <>
            <Header
                label={label}
                roleId={roleId}
                userRoles={userRoles}
                isExit={false} />
                
            <Container spacing={2} maxWidth="lg">
                <Paper elevation={getElevation()} sx={{ margin: { lg: 2 }, padding: { lg: 2 } }}>

                    <Alert severity="info">
                        Crie um formulário para cada matéria a ser dispensada
                    </Alert>

                    <form onSubmit={submit}>
                        <Stack
                            spacing={2}
                            divider={<Divider orientation="horizontal" flexItem />}
                        >
                            {(!isStudent && !isUpdate) && (<PersonalData data={data} setData={setData} isUpdate={isUpdate} />)}
                            <CourseData data={data} setData={setData} isUpdate={isUpdate} />
                            <DisciplinesData data={data} setData={setData} isUpdate={isUpdate} />
                            <DocumentsUpload data={data} setData={setData} />
                            <AdditionalInformation data={data} setData={setData} />
                        </Stack>
                    </form>

                    <Stack direction="row" spacing={2} justifyContent="space-between" sx={{ marginTop: 2 }}>
                        <Button variant="contained" color="primary" onClick={() => window.history.back()}>
                            Voltar
                        </Button>
                        <Button variant="contained" color="primary" onClick={submit}>
                            {isUpdate ? "Salvar edições" : "Encaminhar para análise"}
                        </Button>
                    </Stack>
                </Paper>
            </Container>
        </>
    );
};

export default RequisitionForm;
