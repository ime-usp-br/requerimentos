import React from "react";
import {
    FormControl,
    Divider,
    Typography,
    Autocomplete,
    TextField,
} from "@mui/material";
import { Stack } from "@mui/system";

const CourseData = ({ data, setData }) => {
    const courses = [
        "Bacharelado em Ciência da Computação",
        "Bacharelado em Estatística",
        "Bacharelado em Matemática",
        "Bacharelado em Matemática Aplicada",
        "Bacharelado em Matemática Aplicada e Computacional",
        "Licenciatura em Matemática",
    ];

    return (
        <Stack spacing={1.5} component={"div"} className="course">
            <Typography variant={"h6"} component={"legend"}>
                Curso
            </Typography>
            <FormControl fullWidth required>
                <Autocomplete
                    size="small"
                    id="course"
                    options={courses}
                    value={data.course}
                    required
                    onChange={(event, newValue) => setData("course", newValue)}
                    renderInput={(params) => (
                        <TextField
                            {...params}
                            label="Curso Atual"
                            variant="outlined"
                        />
                    )}
                />
            </FormControl>
        </Stack>
    );
};

export default CourseData;
