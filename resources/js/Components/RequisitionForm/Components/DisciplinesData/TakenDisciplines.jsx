import React from "react";
import {
    Stack,
    TextField,
    Typography,
    Autocomplete,
    Divider,
    Button,
} from "@mui/material";

const TakenDisciplines = ({ data, setData}) => {
    const handleRemoveDiscipline = () => {
        if (data.takenDiscCount > 1) {
            setData("takenDiscCount", data.takenDiscCount - 1);
            setData("takenDiscNames", data.takenDiscNames.slice(0, -1));
            setData("takenDiscInstitutions", data.takenDiscInstitutions.slice(0, -1));
            setData("takenDiscCodes", data.takenDiscCodes.slice(0, -1));
            setData("takenDiscGrades", data.takenDiscGrades.slice(0, -1));
            setData("takenDiscYears", data.takenDiscYears.slice(0, -1));
            setData("takenDiscSemesters", data.takenDiscSemesters.slice(0, -1));
        }
    };

    const handleAddDiscipline = () => {
        setData("takenDiscCount", data.takenDiscCount + 1);
        setData("takenDiscNames", [...data.takenDiscNames, ""]);
        setData("takenDiscInstitutions", [...data.takenDiscInstitutions, ""]);
        setData("takenDiscCodes", [...data.takenDiscCodes, ""]);
        setData("takenDiscGrades", [...data.takenDiscGrades, ""]);
        setData("takenDiscYears", [...data.takenDiscYears, ""]);
        setData("takenDiscSemesters", [...data.takenDiscSemesters, ""]);
    };

    const semesters = ["Primeiro", "Segundo", "Anual"];

    return (
        <Stack spacing={1.5}>
            <Typography variant={"subtitle1"}>
                Disciplinas a serem aproveitadas
            </Typography>
            <Stack 
                spacing={1.5} 
                divider={<Divider orientation="horizontal" flexItem />}
            >
                {(() => {
                    const disciplineFields = [];
                    for (let index = 0; index < data.takenDiscCount; index++) {
                        disciplineFields.push(
                            <Stack spacing={1.5} key={`discipline-${index}`}>
                                <TextField
                                    size="small"
                                    label={"Nome da " + (data.takenDiscCount > 1 ? (index + 1) + "ª " : "") + "disciplina cursada"}
                                    required
                                    value={data.takenDiscNames[index]}
                                    onChange={(e) =>
                                        setData("takenDiscNames", [
                                            ...data.takenDiscNames.slice(0, index),
                                            e.target.value,
                                            ...data.takenDiscNames.slice(index + 1)
                                        ])
                                    }
                                    key={`discipline-name-${index}`}
                                />
                                <TextField
                                    size="small"
                                    label="Instituição em que foi cursada"
                                    required
                                    value={data.takenDiscInstitutions[index]}
                                    onChange={(e) =>
                                        setData("takenDiscInstitutions", [
                                            ...data.takenDiscInstitutions.slice(0, index),
                                            e.target.value,
                                            ...data.takenDiscInstitutions.slice(index + 1)
                                        ])
                                    }
                                    key={`discipline-institution-${index}`}
                                />
                                <Stack direction="row" spacing={1.5} key={`discipline-codes-grades-${index}`}>
                                    <TextField
                                        fullWidth
                                        size="small"
                                        label="Sigla da disciplina cursada"
                                        required
                                        value={data.takenDiscCodes[index]}
                                        onChange={(e) =>
                                            setData("takenDiscCodes", [
                                                ...data.takenDiscCodes.slice(0, index),
                                                e.target.value,
                                                ...data.takenDiscCodes.slice(index + 1)
                                            ])
                                        }
                                        key={`discipline-code-${index}`}
                                    />
                                    <TextField
                                        fullWidth
                                        size="small"
                                        label="Nota obtida"
                                        required
                                        value={data.takenDiscGrades[index]}
                                        onChange={(e) =>
                                            setData("takenDiscGrades", [
                                                ...data.takenDiscGrades.slice(0, index),
                                                e.target.value,
                                                ...data.takenDiscGrades.slice(index + 1)
                                            ])
                                        }
                                        key={`discipline-grade-${index}`}
                                    />
                                </Stack>
                                <Stack direction="row" spacing={1.5} key={`discipline-year-semester-${index}`}>
                                    <TextField
                                        fullWidth
                                        size="small"
                                        label="Ano em que foi cursada"
                                        required
                                        type="number"
                                        value={data.takenDiscYears[index]}
                                        onChange={(e) =>
                                            setData("takenDiscYears", [
                                                ...data.takenDiscYears.slice(0, index),
                                                e.target.value,
                                                ...data.takenDiscYears.slice(index + 1)
                                            ])
                                        }
                                        key={`discipline-year-${index}`}
                                    />
                                    <Autocomplete
                                        size="small"
                                        options={semesters}
                                        value={data.takenDiscSemesters[index]}
                                        fullWidth
                                        onChange={(event, newValue) =>
                                            setData("takenDiscSemesters", [
                                                ...data.takenDiscSemesters.slice(0, index),
                                                newValue,
                                                ...data.takenDiscSemesters.slice(index + 1)
                                            ])
                                        }
                                        renderInput={(params) => (
                                            <TextField
                                                {...params}
                                                label="Semestre em que foi cursada"
                                                variant="outlined"
                                                required
                                            />
                                        )}
                                        key={`discipline-semester-${index}`}
                                    />
                                </Stack>
                            </Stack>
                        );
                    }
                    return disciplineFields;
                })()}
            </Stack>
            <Stack direction="row" spacing={1.5} justifyContent="flex-start">
                {data.takenDiscCount > 1 && (
                    <Button
                        size="small"
                        color="error"
                        variant="contained"
                        onClick={handleRemoveDiscipline}
                        sx={{ maxWidth: 200 }}
                    >
                        Remover disciplina
                    </Button>
                )}
                <Button
                    size="small"
                    variant="contained"
                    onClick={handleAddDiscipline}
                    sx={{ maxWidth: 200 }}
                >
                    Adicionar disciplina
                </Button>
            </Stack>
        </Stack>
    );
};

export default TakenDisciplines;
