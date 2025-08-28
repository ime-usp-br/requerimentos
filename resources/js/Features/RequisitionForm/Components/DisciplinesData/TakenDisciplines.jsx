import React from "react";
import { IconButton } from "@mui/material";
import CloseIcon from "@mui/icons-material/Close";
import {
    Stack,
    TextField,
    Typography,
    Autocomplete,
    Divider,
    Button,
    FormControl,
    InputLabel,
    Select,
    MenuItem,
} from "@mui/material";
import AsyncSubjectAutocomplete from "./AsyncSubjectAutocomplete";

const TakenDisciplines = ({ data, setData, errors = {} }) => {
    const handleRemoveDiscipline = () => {
        if (data.takenDiscCount > 1) {
            setData("takenDiscCount", data.takenDiscCount - 1);
            setData("takenDiscNames", data.takenDiscNames.slice(0, -1));
            setData("takenDiscInstitutions", data.takenDiscInstitutions.slice(0, -1));
            setData("takenDiscCodes", data.takenDiscCodes.slice(0, -1));
            setData("takenDiscGrades", data.takenDiscGrades.slice(0, -1));
            setData("takenDiscYears", data.takenDiscYears.slice(0, -1));
            setData("takenDiscSemesters", data.takenDiscSemesters.slice(0, -1));
            setData("takenDiscIsUSP", data.takenDiscIsUSP.slice(0, -1));
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
        setData("takenDiscIsUSP", [...data.takenDiscIsUSP, false]);
    };

    const handleUSPToggle = (index, institutionType) => {
        const newIsUSP = [...data.takenDiscIsUSP];
        const isUSP = institutionType === 'USP';
        newIsUSP[index] = isUSP;
        setData("takenDiscIsUSP", newIsUSP);

        if (isUSP) {
            // Auto-fill institution field for USP disciplines
            const newInstitutions = [...data.takenDiscInstitutions];
            newInstitutions[index] = "USP";
            setData("takenDiscInstitutions", newInstitutions);
        } else {
            // Clear all fields when switching from USP to external institution
            const newInstitutions = [...data.takenDiscInstitutions];
            newInstitutions[index] = "";
            setData("takenDiscInstitutions", newInstitutions);

            const newCodes = [...data.takenDiscCodes];
            newCodes[index] = "";
            setData("takenDiscCodes", newCodes);

            const newNames = [...data.takenDiscNames];
            newNames[index] = "";
            setData("takenDiscNames", newNames);
        }
    };

    const handleUSPSubjectSelect = (index, selectedSubject) => {
        if (selectedSubject) {
            // Update code
            const newCodes = [...data.takenDiscCodes];
            newCodes[index] = selectedSubject.code;
            setData("takenDiscCodes", newCodes);

            // Update name
            const newNames = [...data.takenDiscNames];
            newNames[index] = selectedSubject.name;
            setData("takenDiscNames", newNames);
        } else {
            // Clear fields if no subject selected
            const newCodes = [...data.takenDiscCodes];
            newCodes[index] = "";
            setData("takenDiscCodes", newCodes);

            const newNames = [...data.takenDiscNames];
            newNames[index] = "";
            setData("takenDiscNames", newNames);
        }
    };

    const semesters = ["Primeiro", "Segundo", "Anual"];

    return (
        <Stack spacing={1.5}>
            <Typography variant={"h6"}>
                Disciplinas a serem aproveitadas
            </Typography>
            <Stack
                spacing={3}
            >
                {(() => {
                    const disciplineFields = [];
                    for (let index = 0; index < data.takenDiscCount; index++) {
                        const isUSP = data.takenDiscIsUSP[index];

                        disciplineFields.push(
                            <Stack spacing={2.5} key={`discipline-${index}`} sx={{ p: 2, border: '1px solid #e0e0e0', borderRadius: 2, bgcolor: '#fafafa', position: 'relative' }}>
                                <Stack direction="row" alignItems="center" justifyContent="space-between">
                                    <Typography variant="subtitle1" sx={{ color: 'primary.main' }}>
                                        {data.takenDiscCount > 1 ? `${index + 1}ª Disciplina Cursada` : 'Disciplina Cursada'}
                                    </Typography>
                                    {data.takenDiscCount > 1 && index > 0 && (
                                        <IconButton
                                            aria-label="Remover disciplina"
                                            size="small"
                                            onClick={handleRemoveDiscipline}
                                            sx={{ ml: 1, color: 'grey.500' }}
                                        >
                                            <CloseIcon fontSize="small" />
                                        </IconButton>
                                    )}
                                </Stack>

                                <FormControl size="small" required>
                                    <InputLabel>Tipo de instituição</InputLabel>
                                    <Select
                                        value={isUSP ? 'USP' : 'Externa'}
                                        onChange={(e) => handleUSPToggle(index, e.target.value)}
                                        label="Tipo de instituição"
                                    >
                                        <MenuItem value="USP">USP</MenuItem>
                                        <MenuItem value="Externa">Instituição Externa</MenuItem>
                                    </Select>
                                </FormControl>

                                {isUSP ? (
                                    // USP discipline with autocomplete
                                    <AsyncSubjectAutocomplete
                                        value={data.takenDiscCodes[index] && data.takenDiscNames[index] ? {
                                            code: data.takenDiscCodes[index],
                                            name: data.takenDiscNames[index]
                                        } : null}
                                        onChange={(selectedSubject) => handleUSPSubjectSelect(index, selectedSubject)}
                                        error={!!errors[`takenDiscCodes.${index}`] || !!errors[`takenDiscNames.${index}`]}
                                        helperText={errors[`takenDiscCodes.${index}`] || errors[`takenDiscNames.${index}`]}
                                        label="Código ou nome da disciplina cursada"
                                        required
                                    />
                                ) : (
                                    // Non-USP discipline with regular text fields
                                    <Stack spacing={2}>
                                        <TextField
                                            size="small"
                                            label="Nome da disciplina cursada"
                                            required
                                            value={data.takenDiscNames[index]}
                                            onChange={(e) =>
                                                setData("takenDiscNames", [
                                                    ...data.takenDiscNames.slice(0, index),
                                                    e.target.value,
                                                    ...data.takenDiscNames.slice(index + 1)
                                                ])
                                            }
                                            error={!!errors[`takenDiscNames.${index}`]}
                                            helperText={errors[`takenDiscNames.${index}`]}
                                        />
                                        <TextField
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
                                            error={!!errors[`takenDiscCodes.${index}`]}
                                            helperText={errors[`takenDiscCodes.${index}`]}
                                        />
                                    </Stack>
                                )}

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
                                    disabled={isUSP}
                                    error={!!errors[`takenDiscInstitutions.${index}`]}
                                    helperText={errors[`takenDiscInstitutions.${index}`]}
                                />

                                <Stack direction="row" spacing={2}>
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
                                        error={!!errors[`takenDiscGrades.${index}`]}
                                        helperText={errors[`takenDiscGrades.${index}`]}
                                    />
                                </Stack>

                                <Stack direction="row" spacing={2}>
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
                                        error={!!errors[`takenDiscYears.${index}`]}
                                        helperText={errors[`takenDiscYears.${index}`]}
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
                                                error={!!errors[`takenDiscSemesters.${index}`]}
                                                helperText={errors[`takenDiscSemesters.${index}`]}
                                            />
                                        )}
                                    />
                                </Stack>
                            </Stack>
                        );
                    }
                    return disciplineFields;
                })()}
            </Stack>
            <Stack direction="row" spacing={2} justifyContent="flex-start">
                <Button
                    size="small"
                    variant="contained"
                    onClick={handleAddDiscipline}
                >
                    Adicionar outra disciplina
                </Button>
            </Stack>
        </Stack>
    );
};

export default TakenDisciplines;
