import React from "react";
import RequiredDiscipline from "./RequiredDiscipline";
import TakenDisciplines from "./TakenDisciplines";

import {
    Stack,
    Typography,
} from "@mui/material";

const DisciplinesData = ({ data, setData, isUpdate, errors }) => {
    return (
        <Stack spacing={4} component={"div"}>
            <Typography variant={"h6"} component={"legend"} sx={{ fontWeight: 'bold', fontSize: '1.25rem' }}>
                Disciplinas
            </Typography>
            <RequiredDiscipline data={data} setData={setData} isUpdate={isUpdate} errors={errors} />
            <TakenDisciplines data={data} setData={setData} isUpdate={isUpdate} errors={errors} />
        </Stack>
    );
};

export default DisciplinesData;
