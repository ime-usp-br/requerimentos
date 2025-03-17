import React from "react";
import RequiredDiscipline from "./RequiredDiscipline";
import TakenDisciplines from "./TakenDisciplines";

import {
    Stack,
    Typography,
} from "@mui/material";

const DisciplinesData = ({ data, setData, isUpdate }) => {
    return (
        <Stack spacing={1.5} component={"div"}>
            <Typography variant={"h6"} component={"legend"}>
                Disciplinas
            </Typography>
            <RequiredDiscipline data={data} setData={setData} isUpdate={isUpdate}/>
            <TakenDisciplines data={data} setData={setData} isUpdate={isUpdate}/>
        </Stack>
    );
};

export default DisciplinesData;
