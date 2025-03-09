// import '/public/css/pages/sg/newRequisition.css';
import React, { useEffect } from "react";
import { useForm } from "@inertiajs/react";
import { Stack, Divider, Alert, Button, Container, Paper, useTheme, useMediaQuery } from "@mui/material";

import PersonalData from "../Components/NewRequisition/PersonalInfo";
import Header from "../Components/NewRequisition/Header";
import CourseData from "../Components/NewRequisition/CourseInfo";
import DisciplinesData from "../Components/NewRequisition/DisciplinesData/DisciplinesData";
import DocumentsUpload from "../Components/NewRequisition/DocumentsUpload";
import AdditionalInformation from "../Components/NewRequisition/AdditionalInformation";

const NewRequisition = ({ isStudent }) => {
    useEffect(() => {
        document.title = "Novo requerimento";
    }, []);

    const { data, setData, post } = useForm({
        name: "",
        email: "",
        nusp: "",
        course: "",
        requestedDiscName: "",
        requestedDiscType: "",
        requestedDiscCode: "",
        requestedDiscDepartment: "",
        takenDiscNames: [""],
        takenDiscInstitutions: [""],
        takenDiscCodes: [""],
        takenDiscYears: [""],
        takenDiscGrades: [""],
        takenDiscSemesters: [""],
        takenDiscCount: 1,
        takenDiscRecord: "",
        courseRecord: "",
        takenDiscSyllabus: "",
        requestedDiscSyllabus: "",
        observations: "",
    });

    const theme = useTheme();
    const isLg = useMediaQuery(theme.breakpoints.up('lg'));

    const getElevation = () => {
        if (isLg) return 3;
        return 0;
    };

    function submit(e) {
        e.preventDefault();
        post(route("newRequisition.post"), {
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
        <Container spacing={2} maxWidth="lg">
            <Paper elevation={getElevation()} sx={{ margin: {lg: 2}, padding: { lg: 2 } }}>
                <Header />

                <Alert severity="info">
                    Crie um formulário para cada matéria a ser dispensada
                </Alert>

                <form onSubmit={submit} id="requisition-form">
                    <Stack
                        spacing={2}
                        divider={<Divider orientation="horizontal" flexItem />}
                    >
                        {(!isStudent) && (<PersonalData data={data} setData={setData} />)}
                        <CourseData data={data} setData={setData} />
                        <DisciplinesData data={data} setData={setData} />
                        <DocumentsUpload data={data} setData={setData} />
                        <AdditionalInformation data={data} setData={setData} />
                    </Stack>
                </form>

                <Stack direction="row" spacing={2} justifyContent="space-between" sx={{ marginTop: 2 }}>
                    <Button variant="contained" color="primary" onClick={() => window.history.back()}>
                        Voltar
                    </Button>
                    <Button variant="contained" color="primary" type="submit" form="requisition-form" onClick={submit}>
                        Encaminhar para análise
                    </Button>
                </Stack>
            </Paper>
        </Container>
    );
};

export default NewRequisition;
