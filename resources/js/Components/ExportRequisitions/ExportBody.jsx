import React, { useRef } from "react";
import { Stack, Button } from "@mui/material";

import Filters from "./Filters/Filters";

export default function ExportBody({ options }) {
    const filterRef = useRef({
        internal_status: options.internal_statusOptions[0],
        department: options.departments[0],
        requested_disc_type: options.discTypes[0],
        start_date: null,
        end_date: null
    });

    return (
        <Stack
            direction='column'
            spacing={5}
            sx={{
                alignItems: { xs: 'left', sm: 'baseline' },
                width: '90%'
            }}
        >
            <Filters options={options} filterRef={filterRef} />
            <Button
                variant="contained"c
                size="large"
                color="primary"
                href={route('pages.requisitions.filterAndExport', filterRef.current)}
            >
                Exportar
            </Button>
        </Stack>
    );
};