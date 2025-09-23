import React from 'react';
import { Typography, Grid2 } from '@mui/material';
import { useRequisitionContext } from '../useRequisitionContext';
import { useUser } from '../../../Context/useUserContext';

const formatDate = (originalDate) => {
    const date = new Date(originalDate);
    const pad = (n) => n.toString().padStart(2, '0');
    return `${pad(date.getDate())}-${pad(date.getMonth() + 1)}-${date.getFullYear()} ${pad(date.getHours())}:${pad(date.getMinutes())}`;
};

const RequisitionData = () => {
    const { requisitionData } = useRequisitionContext();
    const { user } = useUser();
    const roleId = user.currentRoleId;

    const situation = roleId === 1 ? requisitionData.situation : requisitionData.internal_status;
    return (
        <Grid2 container rowSpacing={1} columnSpacing={1.5}>
            <Grid2 size={12}>
                <Typography variant='h6'><strong>Requerimento {requisitionData.id}</strong></Typography>
            </Grid2>
            <Grid2 size={1}>
                <Typography variant='body1'><strong>Situação:</strong></Typography>
            </Grid2>
            <Grid2 size={6}>
                <Typography variant='body1'>{situation}</Typography>
            </Grid2>
            <Grid2 size={1}>
                <Typography variant='body1'><strong>Abertura:</strong></Typography>
            </Grid2>
            <Grid2 size={4}>
                <Typography variant='body1'>{formatDate(requisitionData.created_at)}</Typography>
            </Grid2>


            <Grid2
                container
                size={12}
                sx={(theme) => ({
                    backgroundColor: theme.palette.blue.light
                })
                }
            >
                <Grid2 size={1}>
                    <Typography variant='body1'><strong>Resultado:</strong></Typography>
                </Grid2>
                <Grid2 size={6}>
                    <Typography variant='body1'>{requisitionData.result}</Typography>
                </Grid2>

                <Grid2 size={1}>
                    <Typography variant='body1'><strong>Últ. Mod.:</strong></Typography>
                </Grid2>
                <Grid2 size={4}>
                    <Typography variant='body1'>{formatDate(requisitionData.updated_at)}</Typography>
                </Grid2>
                <Grid2 size={12}>
                    <Typography variant='body1'><strong>Observação</strong></Typography>
                </Grid2>
                <Grid2 size={12}>
                    <Typography
                        variant='body1'
                        sx={{
                            wordBreak: 'break-word',
                            overflowWrap: 'break-word',
                            whiteSpace: 'pre-line',
                        }}
                    >
                        {requisitionData.result_text}
                    </Typography>
                </Grid2>
            </Grid2>
        </Grid2>
    );
};

export default RequisitionData;
