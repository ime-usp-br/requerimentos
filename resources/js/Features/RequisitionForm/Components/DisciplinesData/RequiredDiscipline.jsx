import React from "react";
import {
    Stack,
    TextField,
    Typography,
    Autocomplete,
} from "@mui/material";
import AsyncSubjectAutocomplete from "./AsyncSubjectAutocomplete";

const RequiredDisciplines = ({ data, setData, isUpdate, errors = {} }) => {
    const discTypes = [
        "Obrigatória",
        "Optativa Eletiva",
        "Optativa Livre",
        "Extracurricular",
    ];

    const departments = [
        "MAC",
        "MAE",
        "MAP",
        "MAT",
        "Disciplina de fora do IME",
    ];

    return (
        <Stack spacing={2.5} component={"div"} sx={{ p: 2, border: '1px solid #e0e0e0', borderRadius: 2, bgcolor: '#f5f5f5' }}>
            <Typography variant={"subtitle1"} sx={{ fontWeight: 'bold', fontSize: '1.1rem', color: 'primary.main' }}>
                Disciplina a ser dispensada
            </Typography>
            <AsyncSubjectAutocomplete
                value={data.requestedDiscCode && data.requestedDiscName ? {
                    code: data.requestedDiscCode,
                    name: data.requestedDiscName
                } : null}
                onChange={(selectedSubject) => {
                    if (selectedSubject) {
                        setData("requestedDiscCode", selectedSubject.code);
                        // Always auto-fill the name field
                        setData("requestedDiscName", selectedSubject.name);
                    } else {
                        setData("requestedDiscCode", "");
                        setData("requestedDiscName", "");
                    }
                }}
                disabled={isUpdate}
                error={!!errors.requestedDiscCode}
                helperText={errors.requestedDiscCode}
                label="Código ou nome da disciplina requerida"
                required
            />
            <Stack
                direction="row"
                spacing={2}
            >
                <Autocomplete
                    size="small"
                    options={discTypes}
                    value={data.requestedDiscType}
                    fullWidth
                    onChange={(event, newValue) =>
                        setData("requestedDiscType", newValue)
                    }
                    renderInput={(params) => (
                        <TextField
                            {...params}
                            label="Tipo da disciplina requerida"
                            variant="outlined"
                            required
                            error={!!errors.requestedDiscType}
                            helperText={errors.requestedDiscType}
                        />
                    )}
                />
                <Autocomplete
                    size="small"
                    options={departments}
                    value={data.requestedDiscDepartment}
                    fullWidth
                    onChange={(event, newValue) =>
                        setData("requestedDiscDepartment", newValue)
                    }
                    renderInput={(params) => (
                        <TextField
                            {...params}
                            label="Departamento da disciplina requerida"
                            variant="outlined"
                            required
                            error={!!errors.requestedDiscDepartment}
                            helperText={errors.requestedDiscDepartment}
                        />
                    )}
                />
            </Stack>
        </Stack>
    );
};

export default RequiredDisciplines;
