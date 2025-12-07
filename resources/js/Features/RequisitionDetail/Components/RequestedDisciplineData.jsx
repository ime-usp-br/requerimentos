import React from 'react';
import { Typography, Grid2, Stack } from '@mui/material';
import { useRequisitionContext } from '../useRequisitionContext';
import DocumentLink from './DocumentLink';
import FullTextTooltip from '../../FullTextTooltip';

const RequestedDisciplineData = ({ documents }) => {
    const { requisitionData } = useRequisitionContext();

    return (
        <Grid2 container rowSpacing={.8} columnSpacing={1} columns={24}>
            <Grid2
                size={18}
            >
                <Typography variant='h6'><strong>Disciplina Requerida</strong></Typography>
            </Grid2>
            <Grid2
                container
                size={6}
                justifyContent='right'
                alignItems='end'
            >
                <Stack
                    direction='row'
                    justifyContent='right'
                    spacing={2}
                >
                    <DocumentLink title={'Ementa'} doc={documents[0]} />
                    <DocumentLink title={'Histórico'} doc={documents[2]} />
                </Stack>
            </Grid2>

            <Grid2
                container
                size={24}
                sx={(theme) => ({
                    backgroundColor: theme.palette.orange.main,
                    borderRadius: 1,
                })
                }
            >
                <Grid2 size={1} />
                <Grid2 size={3}>
                    <Typography variant='body2'><strong>Sigla</strong></Typography>
                </Grid2>
                <Grid2 size={6}>
                    <Typography variant='body2'><strong>Nome</strong></Typography>
                </Grid2>
                <Grid2 size={5}>
                    <Typography variant='body2'><strong>Departamento</strong></Typography>
                </Grid2>
                <Grid2 size={8}>
                    <Typography variant='body2'><strong>Tipo</strong></Typography>
                </Grid2>
            </Grid2>

            <Grid2 size={1} />
            <Grid2 size={3}><Typography variant='body2'>{requisitionData.requested_disc_code}</Typography></Grid2>
            <Grid2 size={6}>
                <FullTextTooltip value={requisitionData.requested_disc} />
            </Grid2>
            <Grid2 size={5}>
                <Typography variant='body2'>{requisitionData.department}</Typography>
            </Grid2>
            <Grid2 size={8}>
                <Typography variant='body2'>{requisitionData.requested_disc_type}</Typography>
            </Grid2>
        </Grid2>
    );
};

export default RequestedDisciplineData;
