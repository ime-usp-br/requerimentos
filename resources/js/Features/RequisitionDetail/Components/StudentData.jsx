import React from 'react';
import { Typography, Grid2 } from '@mui/material';
import { useRequisitionContext } from '../useRequisitionContext';

const StudentData = () => {
    const { requisitionData } = useRequisitionContext();

    return (
        <Grid2 container rowSpacing={.8} columnSpacing={1} columns={24}>
            <Grid2 size={24}>
                <Typography variant='h6'><strong>Dados de {requisitionData.student_name}</strong></Typography>
            </Grid2>


            <Grid2
                container
                size={24}
                sx={(theme) => ({
                    backgroundColor: theme.palette.orange.main,
                    borderRadius: 1
                })
                }
            >
                <Grid2 size={1} />
                <Grid2 size={3}>
                    <Typography variant='body2'><strong>NUSP</strong></Typography>
                </Grid2>
                <Grid2 size={6}>
                    <Typography variant='body2'><strong>Email</strong></Typography>
                </Grid2>
                <Grid2 size={13}>
                    <Typography variant='body2'><strong>Curso</strong></Typography>
                </Grid2>
            </Grid2>

            <Grid2 size={1} />
            <Grid2 size={3}>
                <Typography variant='body2'>{requisitionData.student_nusp}</Typography>
            </Grid2>
            <Grid2 size={6}>
                <Typography variant='body2'>{requisitionData.email}</Typography>
            </Grid2>
            <Grid2 size={14}>
                <Typography variant='body2'>{requisitionData.course}</Typography>
            </Grid2>

        </Grid2>
    );
};

export default StudentData;
