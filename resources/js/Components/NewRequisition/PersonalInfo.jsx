import React, { useEffect } from "react";
import { styled } from "styled-components";
import { Stack, TextField, Typography } from "@mui/material";

const PersonalData = ({ data, setData }) => {
    return (
        <Stack spacing={1.5}>
            <Typography variant={"h6"} component={"legend"}>Dados pessoais</Typography>
            <TextField
                size="small"
                label="Nome"
                required
                value={data.name}
                onChange={(e) => setData("name", e.target.value)}
            >
                Nome completo
            </TextField>
            
            <TextField
                size="small"
                label="Email USP"
                required
                value={data.email}
                onChange={(e) => setData("email", e.target.value)}
            >
                Email
            </TextField>

            <TextField
                size="small"
                label="Número USP"
                required
                value={data.nusp}
                onChange={(e) => setData("nusp", e.target.value)}
            >
                Número USP
            </TextField>
        </Stack>
    );
};

export default PersonalData;
