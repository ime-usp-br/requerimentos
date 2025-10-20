import React from 'react';
import { Typography, Grid2, Paper } from '@mui/material';
import { useRequisitionContext } from '../useRequisitionContext';
import { useUser } from '../../../Context/useUserContext';

const formatDate = (originalDate) => {
    const date = new Date(originalDate);
    const pad = (n) => n.toString().padStart(2, '0');
    return `${date.getFullYear()}/${pad(date.getMonth() + 1)}/${pad(date.getDate())}`;
};

const RequisitionData = () => {
    const { requisitionData } = useRequisitionContext();
    const { user } = useUser();
    const roleId = user.currentRoleId;

    const situation = roleId === 1 ? requisitionData.situation : requisitionData.internal_status;
    return (
        <Grid2 container columns={24} rowSpacing={.8} columnSpacing={1}>
            <Grid2 size={24}>
                <Typography variant='h6'><strong>Requerimento {requisitionData.id}</strong></Typography>
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
                <Grid2 size={8}>
                    <Typography variant='body2'><strong>Situação</strong></Typography>
                </Grid2>
                <Grid2 size={12}>
                    <Typography variant='body2'><strong>Observações do pedido</strong></Typography>
                </Grid2>
                <Grid2 size={3}>
                    <Typography variant='body2'><strong>Última mod.</strong></Typography>
                </Grid2>
            </Grid2>

            <Grid2 size={1} />
            <Grid2 size={8}>
                <Typography variant='body2' noWrap>{situation}</Typography>
            </Grid2>
            <Grid2 size={12}>
                <Typography variant='body2' noWrap>{requisitionData.observations}</Typography>
            </Grid2>

            <Grid2 size={3}>
                <Typography variant='body2'>{formatDate(requisitionData.updated_at)}</Typography>
            </Grid2>

        </Grid2>
    );
};

export default RequisitionData;
