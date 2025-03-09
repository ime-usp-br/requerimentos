import React from "react";
import { TextField, Typography, Stack } from "@mui/material";

const AdditionalInformation = ({ data, setData }) => {
    return (
        <Stack spacing={1.5} className="observations">
            <Typography variant="h6" component="legend">Observações</Typography>
            <TextField
                id="observations"
                name="observations"
                multiline
                rows={4}
                variant="outlined"
                fullWidth
                value={data.observations}
                onChange={(e) => setData("observations", e.target.value)}
                label="Adicione aqui informações adicionais necessárias."
            />
        </Stack>
    );
};

export default AdditionalInformation;