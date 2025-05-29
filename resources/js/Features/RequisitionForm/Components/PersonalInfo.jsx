import React, { useEffect } from "react";
import { Stack, TextField, Typography } from "@mui/material";

const PersonalData = ({ data, setData, isUpdate, errors = {} }) => {
    return (
        <Stack spacing={1.5}>
            <Typography variant={"h6"} component={"legend"}>Dados pessoais</Typography>
            <TextField
                size="small"
                label="Nome"
                required
                value={data.student_name}
                onChange={(e) => setData("student_name", e.target.value)}
                disabled={isUpdate}
                error={!!errors.student_name}
                helperText={errors.student_name}
            >
                Nome completo
            </TextField>
            
            <TextField
                size="small"
                label="Email USP"
                required
                value={data.email}
                onChange={(e) => setData("email", e.target.value)}
                disabled={isUpdate}
                error={!!errors.email}
                helperText={errors.email}
            >
                Email
            </TextField>

            <TextField
                size="small"
                label="Número USP"
                required
                value={data.student_nusp}
                onChange={(e) => setData("student_nusp", e.target.value)}
                disabled={isUpdate}
                error={!!errors.student_nusp}
                helperText={errors.student_nusp}
            >
                Número USP
            </TextField>
        </Stack>
    );
};

export default PersonalData;
