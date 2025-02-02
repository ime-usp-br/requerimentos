import React from "react";
import RequiredDisciplines from "./RequiredDisciplines";
import TakenDisciplines from "./TakenDisciplines";

import {
    Stack,
    Typography,
} from "@mui/material";

const DisciplinesData = ({ data, setData, addDiscipline, removeDiscipline }) => {
    return (
        <Stack spacing={1.5} component={"div"}>
            <Typography variant={"h6"} component={"legend"}>
                Disciplinas
            </Typography>
            <RequiredDisciplines data={data} setData={setData} />
            <TakenDisciplines data={data} setData={setData} />
        </Stack>
    );
};

export default DisciplinesData;
