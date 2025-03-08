import React from "react";
import {
    Stack,
    TextField,
    Typography,
    Autocomplete,
} from "@mui/material";

const RequiredDisciplines = ({ data, setData }) => {
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
        <Stack spacing={1.5} component={"div"}>
            <Typography variant={"subtitle1"}>
                Disciplina a ser dispensada
            </Typography>
            <TextField
                size="small"
                label="Nome da disciplina requerida"
                required
                value={data.requestedDiscName}
                onChange={(e) =>
                    setData("requestedDiscName", e.target.value)
                }
            />
            <TextField
                    size="small"
                    label="Sigla da disciplina requerida"
                    required
                    value={data.requestedDiscCode}
                    onChange={(e) =>
                        setData("requestedDiscCode", e.target.value)
                    }
                />
            <Stack
                direction="row"
                spacing={1.5}
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
                        />
                    )}
                />
            </Stack>
        </Stack>
    );
};

export default RequiredDisciplines;
