import React from 'react';
import { Typography, Grid2 } from '@mui/material';
import { useRequisitionContext } from '../useRequisitionContext';

const StudentData = () => {
    const { requisitionData } = useRequisitionContext();

    return (
        <Grid2 container rowSpacing={1} columnSpacing={1.5}>
            <Grid2 size={12}>
                <Typography variant='h6'><strong>Dados Estudantis</strong></Typography>
            </Grid2>
            <Grid2 size={1}>
                <Typography variant='body1'><strong>Nome:</strong></Typography>
            </Grid2>
            <Grid2 size={6}>
                <Typography variant='body1'>{requisitionData.student_name}</Typography>
            </Grid2>
            <Grid2 size={1}>
                <Typography variant='body1'><strong>NUSP:</strong></Typography>
            </Grid2>
            <Grid2 size={2}>
                <Typography variant='body1'>{requisitionData.student_nusp}</Typography>
            </Grid2>
            <Grid2 size={2} />

            <Grid2 size={1}>
                <Typography variant='body1'><strong>Curso:</strong></Typography>
            </Grid2>
            <Grid2 size={6}>
                <Typography variant='body1'>{requisitionData.course}</Typography>
            </Grid2>
            <Grid2 size={1}>
                <Typography variant='body1'><strong>email:</strong></Typography>
            </Grid2>
            <Grid2 size={2}>
                <Typography variant='body1'>{requisitionData.email}</Typography>
            </Grid2>
            <Grid2 size={2} />

            <Grid2
                container
                size={12}
                sx={(theme) => ({
                    backgroundColor: theme.palette.blue.light
                })
                }
            >
                <Grid2 size={12}>
                    <Typography variant='body1'><strong>Observações do pedido</strong></Typography>
                </Grid2>
                <Grid2 size={12}>
                    <Typography variant='body1'>{requisitionData.observations}</Typography>
                </Grid2>
            </Grid2>
        </Grid2>
    );
};

export default StudentData;
